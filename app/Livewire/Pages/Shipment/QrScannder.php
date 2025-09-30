<?php

namespace App\Livewire\Pages\Shipment;

use App\Models\Shipment;
use Livewire\Component;
use Livewire\Attributes\On;

class QrScannder extends Component
{
    public $scannedCode = '';
    public $foundShipment = null;

    public $foundSupplyProfile = null;
    public $showResult = false;

    public $stockInQuantity = '';

    public $selectedSupplyOrder = null;

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

    #[On('qrScanned')]
    public function handleQrScanned($code)
    {    
        $this->message = "QR Scanned: {$code}";
        $this->messageType = 'info';
        
        $this->scannedCode = $code;
                
        $this->processScannedCode($code);
    }

    public function processScannedCode($code)
    { 
        // Clear any previous state
        $this->foundShipment = null;
        $this->foundSupplyProfile = null;
        $this->scannedShipmentNumber = '';
        
        // First, try to find a purchase order with this PO number
        $Shipment = Shipment::with('salesOrder.items.product')
            ->where('shipping_plan_num', $code)
            ->first();        

        if ($Shipment) {          
            
            //Check for valid status - allow multiple valid statuses
            $validStatuses = ['approved'];
            if (!in_array($Shipment->shipping_status, $validStatuses)) {               
                $this->foundShipment = null;
                $this->foundSupplyProfile = null;
                $this->showResult = true;
                $this->message = "Shipment found, but status is '{$Shipment->shipping_status}'. Only Shipments with status 'Approved' can be processed for processing.";
                $this->messageType = 'error';
                return;
            }
            
            // Store the scanned Shipping Plan number FIRST
            $this->scannedShipmentNumber = $code;
                       
            // Then set the found purchase order
            $this->foundShipment = $Shipment;
                     
            $this->foundSupplyProfile = null;
            $this->showResult = true;
            $this->message = "Shipment found: {$Shipment->shipping_plan_num}";
            $this->messageType = 'success';
            
            // Initialize step 2 data
            $this->initializeStep2Data();
            
            // Automatically advance to step 2
            $this->currentStep = 1;
            
            \Log::info("Advanced to step 1. Final state - scannedShipmentNumber: {$this->scannedShipmentNumber}");
            
            return;
        }       
    }

    public function initializeStep2Data()
    {
        if (!$this->foundShipment) return;     
        // Initialize status and remarks for each supply order
        foreach ($this->foundShipment->salesOrder->items as $item) {
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
        $this->currentStep = 2;
    }

    public function goBackToStep2()
    {
        $this->currentStep = 1;
    }

    public function submitStockInReport()
    {
        try {
            // Validate that all items have statuses
            $missingStatuses = [];
            foreach ($this->foundShipment->salesOrder->items as $item) {
                if (!isset($this->itemStatuses[$item->id]) || empty($this->itemStatuses[$item->id])) {
                    $missingStatuses[] = $item->product->supply_description;
                }
            }

            if (!empty($missingStatuses)) {
                $this->message = "Please set status for all items: " . implode(', ', $missingStatuses);
                $this->messageType = 'error';
                return;
            }

            // Process each supply order based on its status
            foreach ($this->foundShipment->salesOrder->items as $item) {
                $itemCondition = $this->itemStatuses[$item->id];
                $remarks = $this->itemRemarks[$item->id] ?? '';

                // Update supply order receiving_status and remarks
                $item->update([
                    'receiving_status' => $itemCondition,
                    'receiving_remarks' => $remarks,
                ]);

                // Update supply profile quantity for good items
                if ($itemCondition === 'good') {
                    $supplyProfile = $item->product;
                    $oldQty = $supplyProfile->supply_qty;
                    $qtyChange = $item->order_qty;                  

                    // Custom Spatie activity log
                    if ($qtyChange > 0) {
                        activity('Shipment Review')
                            ->causedBy(auth()->user())
                            ->performedOn($supplyProfile)
                            ->withProperties([
                                'sku' => $supplyProfile->supply_sku,
                                'Shipping Plan #' => $this->foundShipment->shipping_plan_num,
                                'Order Quantity' => $qtyChange, 
                            ])
                            ->log('Shipment Review' . $this->foundShipment->shipping_plan_num);
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

            $poStatus = 'processing';
            if ($hasDestroyed) $poStatus = 'damaged';
            elseif ($hasIncomplete) $poStatus = 'incomplete';

            $this->foundShipment->update([
                'shipping_status' => $poStatus,
                'review_remarks' => $this->generalRemarks,
            ]);          

            $this->message = "Shipment report submitted successfully for Shippping Plan Number: {$this->foundShipment->shipping_plan_num}";
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
                'foundSupplyProfile', 
                'showResult', 
                'message', 
                'messageType', 
                'itemStatuses', 
                'itemRemarks', 
                'generalRemarks', 
                'scannedShipmentNumber'
            ]
        );
    }

    public function testStep2()
    {  
        if ($this->scannedShipmentNumber) {
            $shipmentResults = Shipment::with(['salesOrder.items.product'])
                ->where('shipping_plan_num', $this->scannedShipmentNumber)
                ->first();
            
            if ($shipmentResults) {               
                $this->foundShipment = $shipmentResults;
                $this->currentStep = 1;
                $this->initializeStep2Data();
                return;
            }
        }
        
        // Fallback: if no scanned PO number, get the first PO for testing
        $testPO = Shipment::with(['salesOrder.items.product'])->first();
        if ($testPO) {           
            $this->foundShipment = $testPO;
            $this->currentStep = 1;
            $this->initializeStep2Data();
        }
    }  

    public function fixScannedShipment()
    {          
        if ($this->scannedCode) {
            // Force reload the PO based on scanned code
            $shipmentResults = Shipment::with(['salesOrder.items.product'])
                ->where('shipping_plan_num', $this->scannedCode)
                ->first();
            
            if ($shipmentResults) {               
                $this->scannedShipmentNumber = $this->scannedCode;
                $this->foundShipment = $shipmentResults;
                $this->initializeStep2Data();
            } else {
                \Log::error("No Shipment found for scanned code: {$this->scannedCode}");
            }
        }
    }

    public function ensureCorrectPO()
    {          
        // If we have a scanned code but the displayed PO doesn't match, fix it
        if ($this->scannedCode && $this->foundShipment && $this->foundShipment->shipping_plan_num !== $this->scannedCode) {
            \Log::info("Shipment mismatch detected. Displayed: {$this->foundShipment->shipping_plan_num}, Scanned: {$this->scannedCode}");
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
                'foundSupplyProfile', 
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
