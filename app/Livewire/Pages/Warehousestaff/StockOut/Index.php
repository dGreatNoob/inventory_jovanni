<?php

namespace App\Livewire\Pages\Bodegero\StockOut;

use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\SupplyOrder;
use App\Models\SupplyProfile;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Attributes\On;

class Index extends Component
{
    public $scannedCode = '';
    public $foundSalesOrder = null;

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
    public $scannedSONumber = '';

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
        $this->foundSalesOrder = null;
        $this->foundSupplyProfile = null;
        $this->scannedSONumber = '';
        
        // First, try to find a purchase order with this PO number
        $salesOrder = SalesOrder::with(['items.product'])
            ->where('sales_order_number', $code)
            ->first();
        

        if ($salesOrder) {          
            
            //Check for valid status - allow multiple valid statuses
            $validStatuses = ['approved'];
            if (!in_array($salesOrder->status, $validStatuses)) {               
                $this->foundSalesOrder = null;
                $this->foundSupplyProfile = null;
                $this->showResult = true;
                $this->message = "Sales Order found, but status is '{$salesOrder->status}'. Only SOs with status 'Approved' can be processed for Stock Out.";
                $this->messageType = 'error';
                return;
            }
            
            // Store the scanned PO number FIRST
            $this->scannedSONumber = $code;
                       
            // Then set the found purchase order
            $this->foundSalesOrder = $salesOrder;
                     
            $this->foundSupplyProfile = null;
            $this->showResult = true;
            $this->message = "Sales Order found: {$salesOrder->sales_order_number}";
            $this->messageType = 'success';
            
            // Initialize step 2 data
            $this->initializeStep2Data();
            
            // Automatically advance to step 2
            $this->currentStep = 1;
            
            \Log::info("Advanced to step 1. Final state - scannedSONumber: {$this->scannedSONumber}, foundSalesOrder: " . ($this->foundSalesOrder ? $this->foundSalesOrder->sales_order_number : 'null'));
            
            return;
        }       
    }

    public function initializeStep2Data()
    {
        if (!$this->foundSalesOrder) return;
        
        // Initialize status and remarks for each supply order
        foreach ($this->foundSalesOrder->items as $item) {
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
            foreach ($this->foundSalesOrder->items as $item) {
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
            foreach ($this->foundSalesOrder->items as $item) {
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

                    // Suppress default Spatie log
                    \Illuminate\Support\Facades\Event::fakeFor(function () use ($supplyProfile, $qtyChange) {
                        $supplyProfile->update([
                            'supply_qty' => $supplyProfile->supply_qty - $qtyChange
                        ]);
                    });

                    // Custom Spatie activity log
                    if ($qtyChange > 0) {
                        activity('Stock-out')
                            ->causedBy(auth()->user())
                            ->performedOn($supplyProfile)
                            ->withProperties([
                                'sku' => $supplyProfile->supply_sku,
                                'qty_change' => $qtyChange,
                                'from' => $oldQty,
                                'to' => $oldQty - $qtyChange,
                            ])
                            ->log('Stock-out ' . $supplyProfile->supply_description . ': ' . $oldQty . ' → ' . ($oldQty - $qtyChange));
                    }
                }
            }

            // Update PO status based on overall status
            $allGood = true;
            $hasDestroyed = false;
            $hasIncomplete = false;

            foreach ($this->itemStatuses as $status) {
                if ($status === 'destroyed') $hasDestroyed = true;
                if ($status === 'incomplete') $hasIncomplete = true;
                if ($status !== 'good') $allGood = false;
            }

            $poStatus = 'released';
            if ($hasDestroyed) $poStatus = 'damaged';
            elseif ($hasIncomplete) $poStatus = 'incomplete';

            $this->foundSalesOrder->update([
                'status' => $poStatus,
                'receiving_remarks' => $this->generalRemarks,
            ]);

            // // Log the activity
            // \App\Models\ActivityLog::create([
            //     'user_id' => auth()->id(),
            //     'action' => 'stock_in_report',
            //     'description' => "Stock In Report submitted for PO: {$this->foundPurchaseOrder->po_num}. Status: {$poStatus}. General Remarks: {$this->generalRemarks}",
            //     'subject_type' => PurchaseOrder::class,
            //     'subject_id' => $this->foundPurchaseOrder->id,
            // ]);

            $this->message = "Stock-out report submitted successfully for Sales Order: {$this->foundSalesOrder->sales_order_number}";
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
                'foundSalesOrder', 
                'foundSupplyProfile', 
                'showResult', 
                'message', 
                'messageType', 
                'itemStatuses', 
                'itemRemarks', 
                'generalRemarks', 
                'scannedSONumber'
            ]
        );
    }

    public function testStep2()
    {  
        if ($this->scannedSONumber) {
            $salesOrder = SalesOrder::with(['items.product'])
                ->where('sales_order_number', $this->scannedSONumber)
                ->first();
            
            if ($salesOrder) {               
                $this->foundSalesOrder = $salesOrder;
                $this->currentStep = 1;
                $this->initializeStep2Data();
                return;
            }
        }
        
        // Fallback: if no scanned PO number, get the first PO for testing
        $testPO = SalesOrder::with(['items.product'])->first();
        if ($testPO) {           
            $this->foundSalesOrder = $testPO;
            $this->currentStep = 1;
            $this->initializeStep2Data();
        }
    }  

    public function fixScannedSO()
    {          
        if ($this->scannedCode) {
            // Force reload the PO based on scanned code
            $salesOrder = SalesOrder::with(['items.product'])
                ->where('sales_order_number', $this->scannedCode)
                ->first();
            
            if ($salesOrder) {               
                $this->scannedSONumber = $this->scannedCode;
                $this->foundSalesOrder = $salesOrder;
                $this->initializeStep2Data();
            } else {
                \Log::error("No Sales Order found for scanned code: {$this->scannedCode}");
            }
        }
    }

    public function ensureCorrectPO()
    {          
        // If we have a scanned code but the displayed PO doesn't match, fix it
        if ($this->scannedCode && $this->foundSalesOrder && $this->foundSalesOrder->sales_order_number !== $this->scannedCode) {
            \Log::info("Sales Order mismatch detected. Displayed: {$this->foundSalesOrder->sales_order_number}, Scanned: {$this->scannedCode}");
            $this->fixScannedSO();
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
                'foundSalesOrder', 
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
        return view('livewire.pages.bodegero.stock-out.index');
    }
}
