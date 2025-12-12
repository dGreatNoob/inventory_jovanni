<?php

namespace App\Livewire\Pages\Shipment;

use App\Models\Shipment;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class QrScannder extends Component
{
    public $foundShipment = null;
    public $showResult = false;
    public $stockInQuantity = '';
    public $showStockOutModal = false;
    public $message = '';
    public $messageType = '';
    public $receivingStatus = 'good';
    public $receivingRemarks = '';
    public $currentStep = 0;

    // Step 2 properties
    public $itemStatuses = [];
    public $itemRemarks = [];
    public $generalRemarks = '';

    // Shipment selection
    public $availableShipments = [];
    public $selectedShipmentId = null;
    public $manualShipmentRef = '';

    public function mount()
    {
        $this->loadAvailableShipments();
    }

    public function loadAvailableShipments()
    {
        $this->availableShipments = Shipment::with('branchAllocation.branch')
            ->whereIn('shipping_status', ['approved', 'in_transit'])
            ->whereHas('branchAllocation.items')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function selectShipment()
    {
        if (!$this->selectedShipmentId && empty($this->manualShipmentRef)) {
            $this->message = "Please select a shipment or enter a reference number manually";
            $this->messageType = 'error';
            return;
        }

        $shipmentId = $this->selectedShipmentId ?: null;
        $reference = $this->manualShipmentRef ?: null;

        $query = Shipment::with('branchAllocation.items.product');

        if ($shipmentId) {
            $query->where('id', $shipmentId);
        } elseif ($reference) {
            $query->where('shipping_plan_num', $reference);
        }

        $Shipment = $query->first();

        if (!$Shipment) {
            $this->message = "Shipment not found";
            $this->messageType = 'error';
            return;
        }

        //Check for valid status - allow multiple valid statuses
        $validStatuses = ['approved', 'in_transit'];
        if (!in_array($Shipment->shipping_status, $validStatuses)) {
            $this->foundShipment = null;
            $this->showResult = true;
            $this->message = "Shipment found, but status is '{$Shipment->shipping_status}'. Only Shipments with status 'Approved' or 'In Transit' can be processed.";
            $this->messageType = 'error';
            return;
        }

        // Check if shipment has associated branch allocation with items
        if (!$Shipment->branchAllocation || $Shipment->branchAllocation->items->isEmpty()) {
            $this->foundShipment = null;
            $this->showResult = true;
            $this->message = "Shipment found, but it has no associated allocated items to review.";
            $this->messageType = 'error';
            return;
        }

        // Set the found shipment
        $this->foundShipment = $Shipment;
        $this->showResult = true;
        $this->message = "Shipment selected: {$Shipment->shipping_plan_num}";
        $this->messageType = 'success';

        // Initialize step 2 data
        $this->initializeStep2Data();

        // Automatically advance to step 2
        $this->currentStep = 1;
    }

    public function initializeStep2Data()
    {
        if (!$this->foundShipment || !$this->foundShipment->branchAllocation) return;
        // Initialize status and remarks for each branch allocation item (only original items, not scanned duplicates)
        foreach ($this->foundShipment->branchAllocation->items->where('box_id', null) as $item) {
            $this->itemStatuses[$item->id] = 'good';
            $this->itemRemarks[$item->id] = '';
        }
    }

    public function setItemStatus($supplyOrderId, $status)
    {
        $this->itemStatuses[$supplyOrderId] = $status;
    }

    public function setItemRemarks($supplyOrderId, $remarks)
    {
        $this->itemRemarks[$supplyOrderId] = $remarks;
    }

    public function goToStep3()
    {
        // Set shipment status to 'in_transit' after completing review (step 2)
        if ($this->foundShipment) {
            $this->foundShipment->update(['shipping_status' => 'in_transit']);
        }
        $this->currentStep = 2;
    }

    public function goBackToStep2()
    {
        $this->currentStep = 1;
    }

    public function submitStockInReport()
    {
        try {
            if (!$this->foundShipment->branchAllocation) {
                $this->message = "Cannot submit report: Shipment has no associated branch allocation.";
                $this->messageType = 'error';
                return;
            }

            // Validate that all items have statuses (only for items without box_id)
            $missingStatuses = [];
            foreach ($this->foundShipment->branchAllocation->items->where('box_id', null) as $item) {
                if (!isset($this->itemStatuses[$item->id]) || empty($this->itemStatuses[$item->id])) {
                    $missingStatuses[] = $item->product->supply_description;
                }
            }

            if (!empty($missingStatuses)) {
                $this->message = "Please set status for all items: " . implode(', ', $missingStatuses);
                $this->messageType = 'error';
                return;
            }

            // Process each branch allocation item based on its status (only original items, not scanned duplicates)
            foreach ($this->foundShipment->branchAllocation->items->where('box_id', null) as $item) {
                $itemCondition = $this->itemStatuses[$item->id];
                $remarks = $this->itemRemarks[$item->id] ?? '';

                // Update supply order receiving_status and remarks
                $item->update([
                    'receiving_status' => $itemCondition,
                    'receiving_remarks' => $remarks,
                ]);

                // Update product inventory for good items
                if ($itemCondition === 'good') {
                    $product = $item->product;
                    $qtyChange = $item->quantity;

                    // Custom Spatie activity log
                    if ($qtyChange > 0) {
                        activity('Shipment Review')
                            ->causedBy(Auth::user())
                            ->performedOn($product)
                            ->withProperties([
                                'sku' => $product->sku,
                                'Shipping Plan #' => $this->foundShipment->shipping_plan_num,
                                'Allocated Quantity' => $qtyChange,
                            ])
                            ->log('Shipment Review' . $this->foundShipment->shipping_plan_num);
                    }
                    // Update branch inventory matrix in the branch_product pivot
                    $branchId = $this->foundShipment->branchAllocation->branch_id;
                    $productId = $item->product_id;

                    $pivot = DB::table('branch_product')->where([
                        ['branch_id', '=', $branchId],
                        ['product_id', '=', $productId],
                    ])->first();

                    $newStock = ($pivot ? $pivot->stock : 0) + $qtyChange;

                    DB::table('branch_product')->updateOrInsert(
                        ['branch_id' => $branchId, 'product_id' => $productId],
                        ['stock' => $newStock]
                    );

                    // Now deduct from initial_quantity via the oldest ProductInventory record:
                    $initialInventory = $product->inventory()->orderBy('created_at', 'asc')->first();
                    if ($initialInventory && $initialInventory->available_quantity >= $qtyChange) {
                        $initialInventory->available_quantity -= $qtyChange;
                        $initialInventory->save();
                    }
                }
            }

            // Update Shipment status based on overall status (only consider original items)
            $allGood = true;
            $hasDestroyed = false;
            $hasIncomplete = false;

            foreach ($this->foundShipment->branchAllocation->items->where('box_id', null) as $item) {
                $status = $this->itemStatuses[$item->id] ?? 'good';
                if ($status === 'destroyed') $hasDestroyed = true;
                if ($status === 'incomplete') $hasIncomplete = true;
                if ($status !== 'good') $allGood = false;
            }

            $poStatus = 'completed';
            if ($hasDestroyed) $poStatus = 'damaged';
            elseif ($hasIncomplete) $poStatus = 'incomplete';

            $this->foundShipment->update([
                'shipping_status' => $poStatus,
                'review_remarks' => $this->generalRemarks,
            ]);

            $this->message = "Shipment report submitted successfully for Shipping Plan Number: {$this->foundShipment->shipping_plan_num}";
            $this->messageType = 'success';

            // Advance to step 4 (completion)
            $this->currentStep = 3;

        } catch (\Exception $e) {
            $this->message = "Error submitting report: " . $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function goBackToStep1()
    {
        $this->currentStep = 0;
        $this->reset(
            [
                'foundShipment',
                'showResult',
                'message',
                'messageType',
                'itemStatuses',
                'itemRemarks',
                'generalRemarks',
                'selectedShipmentId',
                'manualShipmentRef'
            ]
        );
    }


    public function setReceivingStatus($status)
    {
        $this->receivingStatus = $status;
    }

    public function closeStockInModal()
    {
        $this->reset(
            [
                'stockInQuantity', 
                'selectedSupplyOrder', 
                'showStockOutModal'
            ]
        );
    }

    public function clearResult()
    {
        $this->reset(
            [
                'foundShipment',
                'showResult',
                'message',
                'messageType'
            ]
        );
    }

    public function setStep($step)
    {
        $this->currentStep = $step;
    }

    public function render()
    {       
        return view('livewire.pages.shipment.scan-item');
    }
}
