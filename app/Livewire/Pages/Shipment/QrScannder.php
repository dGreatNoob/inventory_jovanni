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
    public $scannedCode = '';
    public $foundShipment = null;

    public $foundProduct = null;
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

    // Store the scanned PO number
    public $scannedShipmentNumber = '';

    // Manual input
    public $manualShipmentRef = '';

    #[On('qrScanned')]
    public function handleQrScanned($code)
    {
        $this->message = "QR Scanned: {$code}";
        $this->messageType = 'info';

        $this->scannedCode = $code;

        $this->processScannedCode($code);
    }

    public function processManualInput()
    {
        if (empty($this->manualShipmentRef)) {
            $this->message = "Please enter a shipment reference number";
            $this->messageType = 'error';
            return;
        }

        $this->message = "Processing manual input: {$this->manualShipmentRef}";
        $this->messageType = 'info';

        $this->scannedCode = $this->manualShipmentRef;
        $this->processScannedCode($this->manualShipmentRef);
    }

    public function processScannedCode($code)
    { 
        // Clear any previous state
        $this->foundShipment = null;
        $this->foundProduct = null;
        $this->scannedShipmentNumber = '';
        
        // First, try to find a shipment with this reference number
        $Shipment = Shipment::with('branchAllocation.items.product')
            ->where('shipping_plan_num', $code)
            ->first();

        if ($Shipment) {

            //Check for valid status - allow multiple valid statuses
            $validStatuses = ['approved', 'in_transit'];
            if (!in_array($Shipment->shipping_status, $validStatuses)) {
                $this->foundShipment = null;
                $this->foundProduct = null;
                $this->showResult = true;
                $this->message = "Shipment found, but status is '{$Shipment->shipping_status}'. Only Shipments with status 'Approved' or 'In Transit' can be processed.";
                $this->messageType = 'error';
                return;
            }

            // Check if shipment has associated branch allocation with items
            if (!$Shipment->branchAllocation || $Shipment->branchAllocation->items->isEmpty()) {
                $this->foundShipment = null;
                $this->foundProduct = null;
                $this->showResult = true;
                $this->message = "Shipment found, but it has no associated allocated items to review.";
                $this->messageType = 'error';
                return;
            }

            // Store the scanned Shipping Plan number FIRST
            $this->scannedShipmentNumber = $code;

            // Then set the found shipment
            $this->foundShipment = $Shipment;

            $this->foundProduct = null;
            $this->showResult = true;
            $this->message = "Shipment found: {$Shipment->shipping_plan_num}";
            $this->messageType = 'success';

            // Initialize step 2 data
            $this->initializeStep2Data();

            // Automatically advance to step 2
            $this->currentStep = 1;

            Log::info("Advanced to step 1. Final state - scannedShipmentNumber: {$this->scannedShipmentNumber}");

            return;
        }
    }

    public function initializeStep2Data()
    {
        if (!$this->foundShipment || !$this->foundShipment->branchAllocation) return;
        // Initialize status and remarks for each branch allocation item
        foreach ($this->foundShipment->branchAllocation->items as $item) {
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

            // Validate that all items have statuses
            $missingStatuses = [];
            foreach ($this->foundShipment->branchAllocation->items as $item) {
                if (!isset($this->itemStatuses[$item->id]) || empty($this->itemStatuses[$item->id])) {
                    $missingStatuses[] = $item->product->supply_description;
                }
            }

            if (!empty($missingStatuses)) {
                $this->message = "Please set status for all items: " . implode(', ', $missingStatuses);
                $this->messageType = 'error';
                return;
            }

            // Process each branch allocation item based on its status
            foreach ($this->foundShipment->branchAllocation->items as $item) {
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

            // Update Shipment status based on overall status
            $allGood = true;
            $hasDestroyed = false;
            $hasIncomplete = false;

            foreach ($this->itemStatuses as $status) {
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
                'scannedCode',
                'foundShipment',
                'foundProduct',
                'showResult',
                'message',
                'messageType',
                'itemStatuses',
                'itemRemarks',
                'generalRemarks',
                'scannedShipmentNumber',
                'manualShipmentRef'
            ]
        );
    }

    public function testStep2()
    {
        if ($this->scannedShipmentNumber) {
            $shipmentResults = Shipment::with(['branchAllocation.items.product'])
                ->where('shipping_plan_num', $this->scannedShipmentNumber)
                ->first();

            if ($shipmentResults) {
                $this->foundShipment = $shipmentResults;
                $this->currentStep = 1;
                $this->initializeStep2Data();
                return;
            }
        }

        // Fallback: if no scanned shipment number, get the first shipment for testing
        $testShipment = Shipment::with(['branchAllocation.items.product'])->first();
        if ($testShipment) {
            $this->foundShipment = $testShipment;
            $this->currentStep = 1;
            $this->initializeStep2Data();
        }
    }

    public function fixScannedShipment()
    {
        if ($this->scannedCode) {
            // Force reload the shipment based on scanned code
            $shipmentResults = Shipment::with(['branchAllocation.items.product'])
                ->where('shipping_plan_num', $this->scannedCode)
                ->first();

            if ($shipmentResults) {
                $this->scannedShipmentNumber = $this->scannedCode;
                $this->foundShipment = $shipmentResults;
                $this->initializeStep2Data();
            } else {
                Log::error("No Shipment found for scanned code: {$this->scannedCode}");
            }
        }
    }

    public function ensureCorrectPO()
    {          
        // If we have a scanned code but the displayed PO doesn't match, fix it
        if ($this->scannedCode && $this->foundShipment && $this->foundShipment->shipping_plan_num !== $this->scannedCode) {
            Log::info("Shipment mismatch detected. Displayed: {$this->foundShipment->shipping_plan_num}, Scanned: {$this->scannedCode}");
            $this->fixScannedShipment();
        }
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
                'scannedCode', 
                'foundShipment', 
                'foundProduct', 
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
