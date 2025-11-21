<?php

namespace App\Livewire\Pages\Warehousestaff\StockIn;

use App\Models\PurchaseOrder;
use App\Models\ProductOrder;
use App\Models\Product;
use App\Models\ProductInventory;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Enums\PurchaseOrderStatus;

class Index extends Component
{
    public $scannedCode = '';
    public $foundPurchaseOrder = null;
    public $showResult = false;
    public $message = '';
    public $messageType = '';
    public $currentStep = 0;

    // Step 2 properties
    public $itemStatuses = [];
    public $itemRemarks = [];
    public $generalRemarks = '';
    public $receivedQuantities = [];
    public $destroyedQuantities = []; // Added for destroyed items
    public $batch_numbers = []; // Added for batch numbers
    public $scannedPONumber = '';

    // Delivery Information
    public $drNumber = ''; // Added DR Number

    // Manual PO input properties
    public $showManualInput = false;
    public $manualPONumber = '';
    public $poPrefix = 'PO-2025-'; // Update as appropriate for your system

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
        $this->scannedPONumber = '';

        // Find purchase order with this PO number
        $purchaseOrder = PurchaseOrder::with(['productOrders.product', 'supplier', 'department'])
            ->where('po_num', $code)
            ->first();

        if ($purchaseOrder) {
            Log::info("Found PO: {$purchaseOrder->po_num} for scanned code: {$code}");

            // ✅ CORRECT - Accept only approved/to_receive status using enum objects
            $validStatuses = [
                \App\Enums\PurchaseOrderStatus::APPROVED,
                \App\Enums\PurchaseOrderStatus::TO_RECEIVE,
            ];
            
            if (!in_array($purchaseOrder->status, $validStatuses)) {
                Log::info("PO {$purchaseOrder->po_num} has invalid status: {$purchaseOrder->status->value}");
                $this->foundPurchaseOrder = null;
                $this->showResult = true;
                $this->message = "Purchase Order found, but status is '{$purchaseOrder->status->label()}'. Only APPROVED POs (or those marked 'To Receive') can be processed for Stock In. Please request approval first.";
                $this->messageType = 'error';
                return;
            }

            $this->scannedPONumber = $code;
            $this->foundPurchaseOrder = $purchaseOrder;
            $this->showResult = true;
            $this->message = "Purchase Order found: {$purchaseOrder->po_num}";
            $this->messageType = 'success';

            $this->initializeStep2Data();
            $this->currentStep = 1;
            return;
        }

        // If not found, show error
        $this->foundPurchaseOrder = null;
        $this->showResult = true;
        $this->message = "No purchase order found for code: {$code}";
        $this->messageType = 'error';
    }

    public function initializeStep2Data()
    {
        if (!$this->foundPurchaseOrder) return;

        // ✅ Generate unique DR number
        if (empty($this->foundPurchaseOrder->dr_number)) {
            // Get the next DR number
            $lastDelivery = \App\Models\PurchaseOrderDelivery::orderBy('id', 'desc')->first();
            $nextNumber = $lastDelivery ? ($lastDelivery->id + 1) : 1;
            $this->drNumber = 'DR-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT); // e.g., DR-00001
        } else {
            $this->drNumber = $this->foundPurchaseOrder->dr_number;
        }

        foreach ($this->foundPurchaseOrder->productOrders as $productOrder) {
            $this->itemStatuses[$productOrder->id] = 'good';
            $this->itemRemarks[$productOrder->id] = '';
            $this->receivedQuantities[$productOrder->id] = $productOrder->getRemainingQuantityAttribute();
            $this->destroyedQuantities[$productOrder->id] = 0;
            $this->batch_numbers[$productOrder->id] = $productOrder->batch_number ?? '';
        }
    }

    public function setItemStatus($productOrderId, $status)
    {
        $this->itemStatuses[$productOrderId] = $status;
    }

    public function setItemRemarks($productOrderId, $remarks)
    {
        $this->itemRemarks[$productOrderId] = $remarks;
    }

    public function goToStep3()
    {
        // Validate DR Number before proceeding
        if (empty(trim($this->drNumber))) {
            $this->message = 'Delivery Receipt (DR) Number is required';
            $this->messageType = 'error';
            return;
        }

        // Validate that all items have statuses
        $missingStatuses = [];
        foreach ($this->foundPurchaseOrder->productOrders as $productOrder) {
            if (!isset($this->itemStatuses[$productOrder->id]) || empty($this->itemStatuses[$productOrder->id])) {
                $missingStatuses[] = $productOrder->product->name;
            }
        }
        
        if (!empty($missingStatuses)) {
            $this->message = "Please set status for all items: " . implode(', ', $missingStatuses);
            $this->messageType = 'error';
            return;
        }

        $this->currentStep = 2;
        $this->message = '';
        $this->messageType = '';
    }

    public function goBackToStep2()
    {
        $this->currentStep = 1;
    }

    public function submitStockInReport()
    {
        try {
            DB::beginTransaction();

            Log::info('Starting stock-in submission', [
                'po_num' => $this->foundPurchaseOrder->po_num,
                'dr_number' => $this->drNumber,
                'user' => 'Wts135',
                'timestamp' => '2025-11-11 08:31:49',
            ]);

            $allItemsFullyReceived = true;
            $anyItemsReceived = false;

            // Validate DR number before processing
            if (empty(trim($this->drNumber))) {
                DB::rollBack();
                $this->message = "Delivery Receipt (DR #) number is required.";
                $this->messageType = 'error';
                return;
            }

            // Check if DR number is already used (prevent duplicate DR numbers)
            $drExists = \App\Models\PurchaseOrderDelivery::where('dr_number', strtoupper(trim($this->drNumber)))->exists();
            
            if ($drExists) {
                DB::rollBack();
                $this->message = "Delivery Receipt number '{$this->drNumber}' is already used. Please enter a unique DR number.";
                $this->messageType = 'error';
                return;
            }

            // Process each product order
            foreach ($this->foundPurchaseOrder->productOrders as $productOrder) {
                $itemCondition = $this->itemStatuses[$productOrder->id] ?? 'good';
                $remarks = $this->itemRemarks[$productOrder->id] ?? '';
                $batchNumber = $this->batch_numbers[$productOrder->id] ?? '';
                $receiveQty = (float) ($this->receivedQuantities[$productOrder->id] ?? 0);
                $destroyedQty = (float) ($this->destroyedQuantities[$productOrder->id] ?? 0);

                // ✅ receiveQty = GOOD items only (go to inventory)
                // ✅ destroyedQty = DAMAGED items (tracked but NOT in inventory)
                // ✅ totalDelivered = GOOD + DAMAGED (for PO completion tracking)
                $actualReceiveQty = $receiveQty; // Good items only
                $totalDelivered = $receiveQty + $destroyedQty;
                
                // Track if this product order needs saving
                $needsSaving = false;
                
                // ✅ SET FLAG FIRST - If ANYTHING was delivered, mark as received
                if ($totalDelivered > 0) {
                    $anyItemsReceived = true;
                    
                    Log::info('Items being processed for delivery', [
                        'product_sku' => $productOrder->product->sku,
                        'good_qty' => $receiveQty,
                        'destroyed_qty' => $destroyedQty,
                        'total_delivered' => $totalDelivered,
                        'batch_number' => $batchNumber,
                        'remaining_qty' => $productOrder->getRemainingQuantityAttribute(),
                        'user' => 'Wts135',
                        'timestamp' => '2025-11-11 08:42:44',
                    ]);
                }
                
                // ✅ SAVE DESTROYED QUANTITY (if any)
                if ($destroyedQty > 0) {
                    $productOrder->destroyed_qty = ($productOrder->destroyed_qty ?? 0) + $destroyedQty;
                    $needsSaving = true;
                    
                    Log::info('Destroyed items recorded', [
                        'product_sku' => $productOrder->product->sku,
                        'destroyed_qty' => $destroyedQty,
                        'total_destroyed' => $productOrder->destroyed_qty,
                        'user' => 'Wts135',
                        'timestamp' => '2025-11-11 08:42:44',
                    ]);
                }
                
                // ✅ PROCESS ITEMS (create batch even if only damaged items)
                if ($totalDelivered > 0) {
                    // Calculate actual receive based on remaining capacity
                    $remainingQty = $productOrder->getRemainingQuantityAttribute();
                    $actualReceive = 0;
                    
                    // Only add to inventory if there's remaining capacity AND good items exist
                    if ($receiveQty > 0 && $remainingQty > 0) {
                        $actualReceive = min($receiveQty, $remainingQty);
                        
                        $productOrder->received_quantity += $actualReceive;
                        $needsSaving = true;
                        
                        // ✅ UPDATE INVENTORY with GOOD items only
                        $product = $productOrder->product;
                        $inventory = ProductInventory::firstOrNew(['product_id' => $product->id]);
                        $oldQty = $inventory->quantity ?? 0;
                        $inventory->quantity = $oldQty + $actualReceive;
                        $inventory->save();

                        Log::info('Inventory updated', [
                            'product_sku' => $product->sku,
                            'old_qty' => $oldQty,
                            'new_qty' => $inventory->quantity,
                            'added_qty' => $actualReceive,
                            'user' => 'Wts135',
                            'timestamp' => '2025-11-11 08:42:44',
                        ]);
                    }
                    
                    // ✅ Check status based on TOTAL delivered (good + destroyed)
                    $totalReceivedSoFar = $productOrder->received_quantity + ($productOrder->destroyed_qty ?? 0);
                    
                    if ($totalReceivedSoFar >= $productOrder->quantity) {
                        $productOrder->status = 'received';
                    } else {
                        $productOrder->status = 'partially_received';
                    }
                    
                    $productOrder->notes = $remarks;
                    
                    if (!empty($batchNumber)) {
                        $productOrder->batch_number = $batchNumber;
                        $needsSaving = true;
                    }

                    // ✅ CREATE BATCH (even if only damaged items or remaining = 0)
                    if (!empty($batchNumber)) {
                        try {
                            $batchNotes = "Total Delivered: {$totalDelivered} units";
                            if ($actualReceive > 0) {
                                $batchNotes .= "\n✅ Good Items: {$actualReceive} units (Available)";
                            }
                            if ($destroyedQty > 0) {
                                $batchNotes .= "\n💥 Destroyed: {$destroyedQty} units (Damaged on arrival)";
                            }
                            $batchNotes .= "\nDR: {$this->drNumber}";
                            $batchNotes .= "\nReceived by: " . auth()->user()->name;
                            $batchNotes .= "\nPO: {$this->foundPurchaseOrder->po_num}";
                            $batchNotes .= "\nDate: " . now()->format('Y-m-d H:i:s');
                            
                            $newBatch = \App\Models\ProductBatch::create([
                                'product_id' => $productOrder->product->id,
                                'purchase_order_id' => $this->foundPurchaseOrder->id,
                                'batch_number' => $batchNumber,
                                'initial_qty' => $totalDelivered,
                                'current_qty' => $actualReceive,
                                'received_date' => now()->toDateString(),
                                'received_by' => auth()->id(),
                                'location' => 'Warehouse',
                                'notes' => $batchNotes,
                            ]);
                            
                            Log::info('Batch created with PO tracking', [
                                'batch_id' => $newBatch->id,
                                'batch_number' => $batchNumber,
                                'product_id' => $productOrder->product->id,
                                'product_sku' => $productOrder->product->sku,
                                'purchase_order_id' => $this->foundPurchaseOrder->id,
                                'po_num' => $this->foundPurchaseOrder->po_num,
                                'initial_qty' => $totalDelivered,
                                'current_qty' => $actualReceive,
                                'destroyed_qty' => $destroyedQty,
                                'user' => 'Wts135',
                                'timestamp' => '2025-11-11 08:42:44',
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Failed to create batch', [
                                'product_sku' => $productOrder->product->sku,
                                'batch_number' => $batchNumber,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                                'user' => 'Wts135',
                                'timestamp' => '2025-11-11 08:42:44',
                            ]);
                        }
                    }

                    // ✅ LOG STOCK-IN ACTIVITY (only if good items added to inventory)
                    if ($actualReceive > 0) {
                        $logMessage = "Stock-in {$productOrder->product->name}: {$oldQty} → " . ($oldQty + $actualReceive);
                        if ($destroyedQty > 0) {
                            $logMessage .= " (Good: {$actualReceive}, Destroyed: {$destroyedQty})";
                        }
                        
                        activity('Stock-in')
                            ->causedBy(auth()->user())
                            ->performedOn($productOrder->product)
                            ->withProperties([
                                'sku' => $productOrder->product->sku,
                                'good_qty' => $actualReceive,
                                'destroyed_qty' => $destroyedQty,
                                'total_delivered' => $totalDelivered,
                                'inventory_from' => $oldQty,
                                'inventory_to' => $oldQty + $actualReceive,
                                'dr_number' => $this->drNumber,
                                'batch_number' => $batchNumber,
                            ])
                            ->log($logMessage);
                    }
                }

                // ✅ SAVE PRODUCT ORDER
                if ($needsSaving) {
                    $productOrder->save();
                    
                    Log::info('Product order updated', [
                        'product_order_id' => $productOrder->id,
                        'received_quantity' => $productOrder->received_quantity,
                        'destroyed_qty' => $productOrder->destroyed_qty,
                        'batch_number' => $productOrder->batch_number,
                        'total' => $productOrder->received_quantity + ($productOrder->destroyed_qty ?? 0),
                        'user' => 'Wts135',
                        'timestamp' => '2025-11-11 08:42:44',
                    ]);
                }

                // ✅ CHECK IF FULLY RECEIVED (good + destroyed)
                $totalReceivedSoFar = $productOrder->received_quantity + ($productOrder->destroyed_qty ?? 0);
                if ($totalReceivedSoFar < $productOrder->quantity) {
                    $allItemsFullyReceived = false;
                }

                // ✅ LOG DESTROYED ITEMS
                if ($destroyedQty > 0) {
                    activity('Stock-in Destroyed')
                        ->causedBy(auth()->user())
                        ->performedOn($productOrder->product)
                        ->withProperties([
                            'sku' => $productOrder->product->sku,
                            'destroyed_qty' => $destroyedQty,
                            'dr_number' => $this->drNumber,
                            'batch_number' => $batchNumber,
                            'remarks' => $remarks,
                            'product_order_id' => $productOrder->id,
                        ])
                        ->log("Destroyed items for {$productOrder->product->name}: {$destroyedQty} units (NOT added to inventory)");
                }
            }

            // ✅ UPDATE PO STATUS
            if ($allItemsFullyReceived) {
                $this->foundPurchaseOrder->status = \App\Enums\PurchaseOrderStatus::RECEIVED;
                $this->foundPurchaseOrder->del_on = now();
            } else {
                $this->foundPurchaseOrder->status = \App\Enums\PurchaseOrderStatus::TO_RECEIVE;
            }
            
            // Save DR number to PO
            $this->foundPurchaseOrder->dr_number = $this->drNumber;
            $this->foundPurchaseOrder->save();

            // ✅ CREATE DELIVERY RECORD
            if ($anyItemsReceived) {
                try {
                    $receivedItems = [];
                    $receivedTotal = 0;
                    $destroyedTotal = 0;
                    
                    foreach ($this->foundPurchaseOrder->productOrders as $productOrder) {
                        $receiveQty = (float) ($this->receivedQuantities[$productOrder->id] ?? 0);
                        $destroyedQty = (float) ($this->destroyedQuantities[$productOrder->id] ?? 0);
                        
                        if ($receiveQty > 0) {
                            $batchInfo = !empty($this->batch_numbers[$productOrder->id]) ? " (Batch: {$this->batch_numbers[$productOrder->id]})" : "";
                            $receivedItems[] = "✅ " . $productOrder->product->name . ': ' . $receiveQty . ' units (Good)' . $batchInfo;
                            $receivedTotal += $receiveQty;
                        }
                        
                        if ($destroyedQty > 0) {
                            $receivedItems[] = "💥 " . $productOrder->product->name . ': ' . $destroyedQty . ' units (DESTROYED)';
                            $destroyedTotal += $destroyedQty;
                        }
                    }
                    
                    $deliveryNotes = $this->generalRemarks ?: 'Received via stock-in process';
                    if (!empty($receivedItems)) {
                        $deliveryNotes .= "\n\n📦 Delivery Details:\n" . implode("\n", $receivedItems);
                    }
                    $deliveryNotes .= "\n\n✅ Total Good (Added to Inventory): {$receivedTotal} units";
                    if ($destroyedTotal > 0) {
                        $deliveryNotes .= "\n💥 Total Destroyed (NOT in Inventory): {$destroyedTotal} units";
                    }
                    $deliveryNotes .= "\n📊 Total Delivered: " . ($receivedTotal + $destroyedTotal) . " units";
                    $deliveryNotes .= "\n👤 Received by: " . auth()->user()->name;
                    
                    // Create delivery record
                    DB::table('purchase_order_deliveries')->insert([
                        'purchase_order_id' => $this->foundPurchaseOrder->id,
                        'dr_number' => strtoupper(trim($this->drNumber)),
                        'delivery_date' => now()->toDateString(),
                        'notes' => $deliveryNotes,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    Log::info('Delivery record created', [
                        'po_num' => $this->foundPurchaseOrder->po_num,
                        'dr_number' => $this->drNumber,
                        'good_qty' => $receivedTotal,
                        'destroyed_qty' => $destroyedTotal,
                        'total_qty' => $receivedTotal + $destroyedTotal,
                        'user' => 'Wts135',
                        'timestamp' => '2025-11-11 08:31:49',
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    
                    Log::error('Failed to create delivery record', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'user' => 'Wts135',
                        'timestamp' => '2025-11-11 08:31:49',
                    ]);
                    
                    $this->message = "Failed to create delivery record: " . $e->getMessage();
                    $this->messageType = 'error';
                    return;
                }
            }

            // Log approval if fully received
            if ($allItemsFullyReceived && method_exists($this->foundPurchaseOrder, 'approvalLogs')) {
                $this->foundPurchaseOrder->approvalLogs()->create([
                    'user_id' => auth()->id(),
                    'action' => 'received',
                    'remarks' => 'Stock-in completed. DR: ' . $this->drNumber,
                ]);
            }

            DB::commit();

            Log::info('Stock-in submission completed successfully', [
                'po_num' => $this->foundPurchaseOrder->po_num,
                'dr_number' => $this->drNumber,
                'status' => $this->foundPurchaseOrder->status->value,
                'user' => 'Wts135',
                'timestamp' => '2025-11-11 08:31:49',
            ]);

            $this->message = "Stock-in report submitted successfully for PO: {$this->foundPurchaseOrder->po_num}";
            $this->messageType = 'success';
            $this->currentStep = 3;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Stock-in submission error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => 'Wts135',
                'timestamp' => '2025-11-11 08:31:49',
            ]);
            
            $this->message = "Error submitting report: " . $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function goBackToStep1()
    {
        Log::info("goBackToStep1 called - resetting all state");
        $this->currentStep = 0;
        $this->reset([
            'scannedCode', 'foundPurchaseOrder', 'showResult',
            'message', 'messageType', 'itemStatuses', 'itemRemarks', 'generalRemarks',
            'scannedPONumber', 'receivedQuantities', 'destroyedQuantities', 'batch_numbers',
            'showManualInput', 'manualPONumber', 'drNumber'
        ]);
        Log::info("State reset complete");
    }

    // Manual PO input logic
    public function toggleManualInput()
    {
        $this->showManualInput = !$this->showManualInput;
        if ($this->showManualInput && empty($this->manualPONumber)) {
            $this->manualPONumber = $this->poPrefix;
        }
    }

    public function resetPOInput()
    {
        $this->manualPONumber = $this->poPrefix;
    }

    public function processManualPO()
    {
        $poNumber = trim($this->manualPONumber);
        if ($poNumber) {
            $this->handleQrScanned($poNumber);
        } else {
            $this->message = "Please enter a PO number.";
            $this->messageType = 'error';
        }
    }

    public function testStep2()
    {
        Log::info("testStep2() called. Scanned PO Number: " . ($this->scannedPONumber ?? 'null'));

        if ($this->scannedPONumber) {
            $purchaseOrder = PurchaseOrder::with(['productOrders.product', 'supplier', 'department'])
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

        $testPO = PurchaseOrder::with(['productOrders.product', 'supplier', 'department'])->first();
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
            'scannedCode' => $this->scannedCode,
            'manualPONumber' => $this->manualPONumber,
            'showManualInput' => $this->showManualInput,
            'drNumber' => $this->drNumber
        ];

        $this->message = "Debug Info: " . json_encode($debugInfo, JSON_PRETTY_PRINT);
        $this->messageType = 'info';
    }

    public function ensureCorrectPO()
    {
        Log::info("ensureCorrectPO called. Scanned code: {$this->scannedCode}, scannedPONumber: {$this->scannedPONumber}");

        if ($this->scannedCode && $this->foundPurchaseOrder && $this->foundPurchaseOrder->po_num !== $this->scannedCode) {
            $purchaseOrder = PurchaseOrder::with(['productOrders.product', 'supplier', 'department'])
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

    public function setStep($step)
    {
        $this->currentStep = $step;
    }

    public function render()
    {
        return view('livewire.pages.warehousestaff.stock-in.index');
    }
}