<?php

namespace App\Livewire\Pages\Warehousestaff\StockIn;

use App\Models\PurchaseOrder;
use App\Models\ProductOrder;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
    public $scannedPONumber = '';

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

            // Accept only approved/to_receive/for_delivery status
            $validStatuses = ['approved', 'to_receive', 'for_delivery'];
            if (!in_array($purchaseOrder->status, $validStatuses)) {
                Log::info("PO {$purchaseOrder->po_num} has invalid status: {$purchaseOrder->status}");
                $this->foundPurchaseOrder = null;
                $this->showResult = true;
                $this->message = "Purchase Order found, but status is '{$purchaseOrder->status}'. Only APPROVED POs (or those marked 'To Receive' or 'For Delivery') can be processed for Stock In. Please request approval first.";
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

        foreach ($this->foundPurchaseOrder->productOrders as $productOrder) {
            $this->itemStatuses[$productOrder->id] = 'good';
            $this->itemRemarks[$productOrder->id] = '';
            $this->receivedQuantities[$productOrder->id] = $productOrder->getRemainingQuantityAttribute();
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

            foreach ($this->foundPurchaseOrder->productOrders as $productOrder) {
                $itemCondition = $this->itemStatuses[$productOrder->id];
                $remarks = $this->itemRemarks[$productOrder->id] ?? '';
                $receiveQty = (float) ($this->receivedQuantities[$productOrder->id] ?? 0);

                // Only process if receiveQty > 0
                if ($receiveQty > 0 && $productOrder->getRemainingQuantityAttribute() > 0) {
                    $actualReceive = min($receiveQty, $productOrder->getRemainingQuantityAttribute());
                    $productOrder->received_quantity += $actualReceive;
                    $productOrder->status = ($productOrder->isFullyReceived()) ? 'received' : 'partial';
                    $productOrder->notes = $remarks;
                    $productOrder->save();

                    // Update product inventory (do NOT update products.quantity, use ProductInventory)
                    $product = $productOrder->product;

                    // Find or create a ProductInventory record for this product
                    $inventory = \App\Models\ProductInventory::firstOrNew(['product_id' => $product->id]);
                    $oldQty = $inventory->quantity ?? 0;
                    $inventory->quantity = $oldQty + $actualReceive;
                    $inventory->save();

                    // Optional: Log activity for stock-in event
                    if ($actualReceive > 0) {
                        activity('Stock-in')
                            ->causedBy(auth()->user())
                            ->performedOn($product)
                            ->withProperties([
                                'sku' => $product->sku,
                                'qty_change' => $actualReceive,
                                'from' => $oldQty,
                                'to' => $oldQty + $actualReceive,
                            ])
                            ->log('Stock-in ' . $product->name . ': ' . $oldQty . ' → ' . ($oldQty + $actualReceive));
                    }
                }
            }

            // Update PO status based on overall status
            $hasDestroyed = false;
            $hasIncomplete = false;
            foreach ($this->itemStatuses as $status) {
                if ($status === 'destroyed') $hasDestroyed = true;
                if ($status === 'incomplete') $hasIncomplete = true;
            }

            $allReceived = $this->foundPurchaseOrder->productOrders->every(function ($order) {
                return $order->isFullyReceived();
            });

            $poStatus = $allReceived ? 'received' : 'partial';
            if ($hasDestroyed) $poStatus = 'damaged';
            elseif ($hasIncomplete) $poStatus = 'incomplete';

            $this->foundPurchaseOrder->status = $poStatus;
            // Set date received if fully received
            if ($poStatus === 'received') {
                $this->foundPurchaseOrder->del_on = now();

                // Log the receiving event in approval logs
                if (method_exists($this->foundPurchaseOrder, 'approvalLogs')) {
                    $this->foundPurchaseOrder->approvalLogs()->create([
                        'user_id' => auth()->id(),
                        'action' => 'received',
                        'remarks' => 'Stock-in completed via QR scan or manual PO input',
                    ]);
                }
            }
            $this->foundPurchaseOrder->save();

            $this->message = "Stock-in report submitted successfully for PO: {$this->foundPurchaseOrder->po_num}";
            $this->messageType = 'success';

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
            'scannedCode', 'foundPurchaseOrder', 'showResult',
            'message', 'messageType', 'itemStatuses', 'itemRemarks', 'generalRemarks',
            'scannedPONumber', 'receivedQuantities',
            'showManualInput', 'manualPONumber'
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
            'showManualInput' => $this->showManualInput
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