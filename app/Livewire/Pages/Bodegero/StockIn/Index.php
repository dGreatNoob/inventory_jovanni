<?php

namespace App\Livewire\Pages\Bodegero\StockIn;

use App\Models\PurchaseOrder;
use App\Models\SupplyOrder;
use App\Models\SupplyProfile;
use App\Models\SupplyBatch;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    public $scannedCode = '';
    public $foundPurchaseOrder = null;
    public $foundSupplyProfile = null;
    public $showResult = false;
    public $stockInQuantity = '';
    public $selectedSupplyOrder = null;
    public $showStockInModal = false;
    public $message = '';
    public $messageType = '';
    public $receivingStatus = 'good';
    public $receivingRemarks = '';
    public $currentStep = 0;
    
    // Step 2 properties
    public $itemStatuses = [];
    public $itemRemarks = [];
    public $generalRemarks = '';
    
    // Batch tracking properties for consumables
    public $itemBatchNumbers = [];
    public $itemExpirationDates = [];
    public $itemManufacturedDates = [];
    public $itemLocations = [];
    public $itemBatchNotes = [];
    
    // Store the scanned PO number
    public $scannedPONumber = '';

    #[On('qrScanned')]
    public function handleQrScanned($code)
    {
        Log::info("handleQrScanned called with code: {$code}");
        
        $this->message = "QR Scanned: {$code}";
        $this->messageType = 'info';
        
        $this->scannedCode = $code;
        $this->processScannedCode($code);
        
        Log::info("handleQrScanned completed. Current step: {$this->currentStep}, PO: " . ($this->foundPurchaseOrder ? $this->foundPurchaseOrder->po_num : 'null'));
    }

    public function processScannedCode($code)
    {
        Log::info("processScannedCode called with code: {$code}");
        
        // Clear any previous state
        $this->foundPurchaseOrder = null;
        $this->foundSupplyProfile = null;
        $this->scannedPONumber = '';
        
        // First, try to find a purchase order with this PO number
        $purchaseOrder = PurchaseOrder::with(['supplyOrders.supplyProfile'])
            ->where('po_num', $code)
            ->first();

        if ($purchaseOrder) {
            Log::info("Found PO: {$purchaseOrder->po_num} for scanned code: {$code}");
            
            // Check for valid status - allow multiple valid statuses
            $validStatuses = ['to_receive', 'for_delivery', 'pending'];
            if (!in_array($purchaseOrder->status, $validStatuses)) {
                Log::info("PO {$purchaseOrder->po_num} has invalid status: {$purchaseOrder->status}");
                $this->foundPurchaseOrder = null;
                $this->foundSupplyProfile = null;
                $this->showResult = true;
                $this->message = "Purchase Order found, but status is '{$purchaseOrder->status}'. Only POs with status 'To Receive', 'For Delivery', or 'Pending' can be processed for Stock In.";
                $this->messageType = 'error';
                return;
            }
            
            Log::info("PO {$purchaseOrder->po_num} has valid status: {$purchaseOrder->status}");
            
            // Store the scanned PO number FIRST
            $this->scannedPONumber = $code;
            Log::info("Set scannedPONumber to: {$this->scannedPONumber}");
            
            // Then set the found purchase order
            $this->foundPurchaseOrder = $purchaseOrder;
            Log::info("Set foundPurchaseOrder to: {$this->foundPurchaseOrder->po_num}");
            
            $this->foundSupplyProfile = null;
            $this->showResult = true;
            $this->message = "Purchase Order found: {$purchaseOrder->po_num}";
            $this->messageType = 'success';
            
            // Initialize step 2 data
            $this->initializeStep2Data();
            
            // Automatically advance to step 2
            $this->currentStep = 1;
            
            Log::info("Advanced to step 1. Final state - scannedPONumber: {$this->scannedPONumber}, foundPurchaseOrder: " . ($this->foundPurchaseOrder ? $this->foundPurchaseOrder->po_num : 'null'));
            
            return;
        }

        // If not a PO, try to find a supply profile with this SKU
        $supplyProfile = SupplyProfile::where('supply_sku', $code)->first();

        if ($supplyProfile) {
            $this->foundSupplyProfile = $supplyProfile;
            $this->foundPurchaseOrder = null;
            $this->showResult = true;
            $this->message = "Supply Profile found: {$supplyProfile->supply_description}";
            $this->messageType = 'success';
            return;
        }

        // If neither found, show error
        $this->foundPurchaseOrder = null;
        $this->foundSupplyProfile = null;
        $this->showResult = true;
        $this->message = "No purchase order or supply profile found for code: {$code}";
        $this->messageType = 'error';
    }

    public function initializeStep2Data()
    {
        if (!$this->foundPurchaseOrder) return;
        
        // Initialize status and remarks for each supply order
        foreach ($this->foundPurchaseOrder->supplyOrders as $supplyOrder) {
            $this->itemStatuses[$supplyOrder->id] = 'good';
            $this->itemRemarks[$supplyOrder->id] = '';
            
            // Initialize batch tracking for consumable items
            if ($supplyOrder->supplyProfile->isConsumable()) {
                $this->itemBatchNumbers[$supplyOrder->id] = SupplyBatch::generateBatchNumber($supplyOrder->supply_profile_id);
                $this->itemExpirationDates[$supplyOrder->id] = '';
                $this->itemManufacturedDates[$supplyOrder->id] = date('Y-m-d');
                $this->itemLocations[$supplyOrder->id] = 'Warehouse A';
                $this->itemBatchNotes[$supplyOrder->id] = '';
            }
        }
    }

    public function setBatchInfo($supplyOrderId, $field, $value)
    {
        switch ($field) {
            case 'batch_number':
                $this->itemBatchNumbers[$supplyOrderId] = $value;
                break;
            case 'expiration_date':
                $this->itemExpirationDates[$supplyOrderId] = $value;
                break;
            case 'manufactured_date':
                $this->itemManufacturedDates[$supplyOrderId] = $value;
                break;
            case 'location':
                $this->itemLocations[$supplyOrderId] = $value;
                break;
            case 'notes':
                $this->itemBatchNotes[$supplyOrderId] = $value;
                break;
        }
    }

    public function generateNewBatchNumber($supplyOrderId)
    {
        $supplyOrder = $this->foundPurchaseOrder->supplyOrders->firstWhere('id', $supplyOrderId);
        if ($supplyOrder) {
            $this->itemBatchNumbers[$supplyOrder->id] = SupplyBatch::generateBatchNumber($supplyOrder->supply_profile_id);
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
            foreach ($this->foundPurchaseOrder->supplyOrders as $supplyOrder) {
                if (!isset($this->itemStatuses[$supplyOrder->id]) || empty($this->itemStatuses[$supplyOrder->id])) {
                    $missingStatuses[] = $supplyOrder->supplyProfile->supply_description;
                }
            }

            if (!empty($missingStatuses)) {
                $this->message = "Please set status for all items: " . implode(', ', $missingStatuses);
                $this->messageType = 'error';
                return;
            }

            // Process each supply order based on its status
            foreach ($this->foundPurchaseOrder->supplyOrders as $supplyOrder) {
                $itemCondition = $this->itemStatuses[$supplyOrder->id];
                $remarks = $this->itemRemarks[$supplyOrder->id] ?? '';

                // Update supply order receiving_status and remarks
                $supplyOrder->update([
                    'receiving_status' => $itemCondition,
                    'receiving_remarks' => $remarks,
                ]);

                // Update supply profile quantity for good items
                if ($itemCondition === 'good') {
                    $supplyProfile = $supplyOrder->supplyProfile;
                    $oldQty = $supplyProfile->supply_qty;
                    $qtyChange = $supplyOrder->order_qty;
                    
                    // For consumable items, create batch records
                    if ($supplyProfile->isConsumable()) {
                        // Validate batch information
                        $batchNumber = $this->itemBatchNumbers[$supplyOrder->id] ?? null;
                        $expirationDate = $this->itemExpirationDates[$supplyOrder->id] ?? null;
                        $manufacturedDate = $this->itemManufacturedDates[$supplyOrder->id] ?? null;
                        $location = $this->itemLocations[$supplyOrder->id] ?? 'Warehouse A';
                        $notes = $this->itemBatchNotes[$supplyOrder->id] ?? '';

                        if (empty($batchNumber)) {
                            $this->message = "Batch number is required for consumable item: {$supplyProfile->supply_description}";
                            $this->messageType = 'error';
                            return;
                        }

                        // Check if batch number already exists for this product
                        $existingBatch = SupplyBatch::where('supply_profile_id', $supplyProfile->id)
                                                ->where('batch_number', $batchNumber)
                                                ->first();

                        if ($existingBatch) {
                            // Add to existing batch
                            $existingBatch->addQuantity($qtyChange);
                        } else {
                            // Create new batch
                            SupplyBatch::create([
                                'supply_profile_id' => $supplyProfile->id,
                                'supply_order_id' => $supplyOrder->id,
                                'batch_number' => $batchNumber,
                                'expiration_date' => $expirationDate ?: null,
                                'manufactured_date' => $manufacturedDate ?: null,
                                'initial_qty' => $qtyChange,
                                'current_qty' => $qtyChange,
                                'location' => $location,
                                'notes' => $notes,
                                'status' => 'active',
                                'received_date' => now()->toDateString(),
                                'received_by' => Auth::id(),
                            ]);
                        }
                    }
                    
                    // Suppress default Spatie log
                    \Illuminate\Support\Facades\Event::fakeFor(function () use ($supplyProfile, $qtyChange) {
                        $supplyProfile->update([
                            'supply_qty' => $supplyProfile->supply_qty + $qtyChange
                        ]);
                    });
                    // Custom Spatie activity log
                    if ($qtyChange > 0) {
                        activity('Stock-in')
                            ->causedBy(Auth::user())
                            ->performedOn($supplyProfile)
                            ->withProperties([
                                'sku' => $supplyProfile->supply_sku,
                                'qty_change' => $qtyChange,
                                'from' => $oldQty,
                                'to' => $oldQty + $qtyChange,
                                'batch_number' => $supplyProfile->isConsumable() ? ($this->itemBatchNumbers[$supplyOrder->id] ?? null) : null,
                                'expiration_date' => $supplyProfile->isConsumable() ? ($this->itemExpirationDates[$supplyOrder->id] ?? null) : null,
                            ])
                            ->log('Stock-in ' . $supplyProfile->supply_description . ': ' . $oldQty . ' → ' . ($oldQty + $qtyChange));
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

            $poStatus = 'received';
            if ($hasDestroyed) $poStatus = 'damaged';
            elseif ($hasIncomplete) $poStatus = 'incomplete';

            $this->foundPurchaseOrder->update([
                'status' => $poStatus,
                'receiving_remarks' => $this->generalRemarks,
            ]);

            // ✅ AUTO-CREATE DELIVERY RECORD when PO is received/damaged/incomplete
            if (in_array($poStatus, ['received', 'damaged', 'incomplete'])) {
                // Check if delivery record already exists
                $deliveryExists = \App\Models\PurchaseOrderDelivery::where('purchase_order_id', $this->foundPurchaseOrder->id)->exists();
                
                if (!$deliveryExists) {
                    try {
                        \Illuminate\Support\Facades\DB::table('purchase_order_deliveries')->insert([
                            'purchase_order_id' => $this->foundPurchaseOrder->id,
                            'dr_number' => $this->foundPurchaseOrder->dr_number ?: ('DR-' . $this->foundPurchaseOrder->po_num),
                            'delivery_date' => now()->toDateString(),
                            'notes' => $this->generalRemarks ?: 'Received via stock-in process',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        Log::info("✅ Auto-created delivery record for PO: {$this->foundPurchaseOrder->po_num}");
                    } catch (\Exception $e) {
                        Log::error("❌ Failed to create delivery record for PO {$this->foundPurchaseOrder->po_num}: " . $e->getMessage());
                    }
                }
            }

            $this->message = "Stock-in report submitted successfully for PO: {$this->foundPurchaseOrder->po_num}";
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
        Log::info("goBackToStep1 called - resetting all state");
        $this->currentStep = 0;
        $this->reset([
            'scannedCode', 'foundPurchaseOrder', 'foundSupplyProfile', 'showResult', 
            'message', 'messageType', 'itemStatuses', 'itemRemarks', 'generalRemarks', 
            'scannedPONumber', 'itemBatchNumbers', 'itemExpirationDates', 
            'itemManufacturedDates', 'itemLocations', 'itemBatchNotes'
        ]);
        Log::info("State reset complete");
    }

    public function testStep2()
    {
        Log::info("testStep2() called. Scanned PO Number: " . ($this->scannedPONumber ?? 'null'));
        
        // If we have a scanned PO number, query for that specific PO
        if ($this->scannedPONumber) {
            $purchaseOrder = PurchaseOrder::with(['supplyOrders.supplyProfile'])
                ->where('po_num', $this->scannedPONumber)
                ->first();
            
            if ($purchaseOrder) {
                Log::info("Loading scanned PO: {$purchaseOrder->po_num}");
                $this->foundPurchaseOrder = $purchaseOrder;
                $this->currentStep = 1;
                $this->initializeStep2Data();
                return;
            }
        }
        
        // Fallback: if no scanned PO number, get the first PO for testing
        $testPO = PurchaseOrder::with(['supplyOrders.supplyProfile'])->first();
        if ($testPO) {
            Log::info("Loading fallback PO: {$testPO->po_num}");
            $this->foundPurchaseOrder = $testPO;
            $this->currentStep = 1;
            $this->initializeStep2Data();
        }
    }

    public function debugState()
    {
        $debugInfo = [
            'currentStep' => $this->currentStep,
            'scannedPONumber' => $this->scannedPONumber ?? 'null',
            'foundPurchaseOrder' => $this->foundPurchaseOrder ? $this->foundPurchaseOrder->po_num : 'null',
            'scannedCode' => $this->scannedCode
        ];
        
        $this->message = "Debug Info: " . json_encode($debugInfo, JSON_PRETTY_PRINT);
        $this->messageType = 'info';
    }

    public function fixScannedPO()
    {
        Log::info("fixScannedPO called. Scanned code: {$this->scannedCode}");
        
        if ($this->scannedCode) {
            // Force reload the PO based on scanned code
            $purchaseOrder = PurchaseOrder::with(['supplyOrders.supplyProfile'])
                ->where('po_num', $this->scannedCode)
                ->first();
            
            if ($purchaseOrder) {
                Log::info("Fixed: Loading PO {$purchaseOrder->po_num} for scanned code {$this->scannedCode}");
                $this->scannedPONumber = $this->scannedCode;
                $this->foundPurchaseOrder = $purchaseOrder;
                $this->initializeStep2Data();
            } else {
                Log::error("No PO found for scanned code: {$this->scannedCode}");
            }
        }
    }

    public function ensureCorrectPO()
    {
        Log::info("ensureCorrectPO called. Scanned code: {$this->scannedCode}, scannedPONumber: {$this->scannedPONumber}");
        
        // If we have a scanned code but the displayed PO doesn't match, fix it
        if ($this->scannedCode && $this->foundPurchaseOrder && $this->foundPurchaseOrder->po_num !== $this->scannedCode) {
            Log::info("PO mismatch detected. Displayed: {$this->foundPurchaseOrder->po_num}, Scanned: {$this->scannedCode}");
            $this->fixScannedPO();
        }
    }

    public function selectSupplyOrder($supplyOrderId)
    {
        $this->selectedSupplyOrder = SupplyOrder::with(['supplyProfile'])
            ->findOrFail($supplyOrderId);
        $this->stockInQuantity = $this->selectedSupplyOrder->order_qty;
        $this->showStockInModal = true;
    }

    public function setReceivingStatus($status)
    {
        $this->receivingStatus = $status;
    }

    public function processStockInWithStatus()
    {
        if (!$this->selectedSupplyOrder) {
            $this->message = "No supply order selected";
            $this->messageType = 'error';
            return;
        }

        if (empty($this->stockInQuantity) || $this->stockInQuantity <= 0) {
            $this->message = "Please enter a valid quantity";
            $this->messageType = 'error';
            return;
        }

        if ($this->stockInQuantity > $this->selectedSupplyOrder->order_qty) {
            $this->message = "Stock-in quantity cannot exceed order quantity";
            $this->messageType = 'error';
            return;
        }

        try {
            // Update the supply order received quantity, status, and remarks
            $newReceivedQty = $this->selectedSupplyOrder->received_qty + $this->stockInQuantity;
            $orderStatus = 'partial';
            if ($this->receivingStatus === 'good' && $newReceivedQty >= $this->selectedSupplyOrder->order_qty) {
                $orderStatus = 'received';
            } elseif ($this->receivingStatus === 'damaged') {
                $orderStatus = 'damaged';
            } elseif ($this->receivingStatus === 'incomplete') {
                $orderStatus = 'incomplete';
            }
            $this->selectedSupplyOrder->update([
                'received_qty' => $newReceivedQty,
                'status' => $orderStatus,
                'receiving_status' => $this->receivingStatus,
                'receiving_remarks' => $this->receivingRemarks,
            ]);

            // Update the supply profile quantity only if status is good or incomplete
            $supplyProfile = $this->selectedSupplyOrder->supplyProfile;
            if (in_array($this->receivingStatus, ['good', 'incomplete'])) {
                $supplyProfile->update([
                    'supply_qty' => $supplyProfile->supply_qty + $this->stockInQuantity
                ]);
            }

            // Check if all items in the PO are received
            $purchaseOrder = $this->selectedSupplyOrder->purchaseOrder;
            $allReceived = $purchaseOrder->supplyOrders()
                ->where('received_qty', '<', 'order_qty')
                ->count() === 0;

            if ($allReceived) {
                $purchaseOrder->update(['status' => 'received']);
            } elseif ($this->receivingStatus === 'damaged') {
                $purchaseOrder->update(['status' => 'damaged']);
            } elseif ($this->receivingStatus === 'incomplete') {
                $purchaseOrder->update(['status' => 'incomplete']);
            }

            // Log the action in the activity logs
            activity()
                ->causedBy(Auth::user())
                ->performedOn($this->selectedSupplyOrder)
                ->withProperties([
                    'status' => $this->receivingStatus,
                    'remarks' => $this->receivingRemarks,
                    'quantity' => $this->stockInQuantity,
                    'sku' => $supplyProfile->supply_sku,
                ])
                ->log('Stock-in: ' . $supplyProfile->supply_description . ' (SKU: ' . $supplyProfile->supply_sku . ') - Status: ' . ucfirst($this->receivingStatus) . '. Remarks: ' . $this->receivingRemarks);

            $this->message = "Receiving report submitted: {$this->stockInQuantity} units of {$supplyProfile->supply_description} as {$this->receivingStatus}.";
            $this->messageType = 'success';

            // Reset form
            $this->reset(['stockInQuantity', 'selectedSupplyOrder', 'showStockInModal', 'receivingStatus', 'receivingRemarks']);
            // Refresh the found purchase order data - use scanned PO number to ensure correct PO
            if ($this->scannedPONumber) {
                $this->foundPurchaseOrder = PurchaseOrder::with(['supplyOrders.supplyProfile'])
                    ->where('po_num', $this->scannedPONumber)
                    ->first();
            } elseif ($this->foundPurchaseOrder) {
                $this->foundPurchaseOrder = PurchaseOrder::with(['supplyOrders.supplyProfile'])
                    ->find($this->foundPurchaseOrder->id);
            }
        } catch (\Exception $e) {
            $this->message = "Error processing receiving report: " . $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function closeStockInModal()
    {
        $this->reset(['stockInQuantity', 'selectedSupplyOrder', 'showStockInModal']);
    }

    public function clearResult()
    {
        $this->reset(['scannedCode', 'foundPurchaseOrder', 'foundSupplyProfile', 'showResult', 'message', 'messageType']);
    }

    public function setStep($step)
    {
        $this->currentStep = $step;
    }

    public function render()
    {
        return view('livewire.pages.bodegero.stock-in.index');
    }
}
