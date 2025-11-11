<?php

namespace App\Livewire\Pages\POManagement\PurchaseOrder;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderApprovalLog;
use App\Models\ProductOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\PurchaseOrderStatus;

class Show extends Component
{
    public $purchaseOrder;
    public $Id;
    
    // For APPROVED status form
    public $expected_delivery_date;
    public $expected_quantities = [];
    public $manufactured_dates = [];
    public $cancellation_reason;

    // Public properties for blade access
    public $totalQuantity;
    public $totalPrice;
    public $totalExpected;
    public $totalDelivered;
    public $totalReceived;
    public $totalDestroyed;
    public $batch_numbers = [];
    public $batches; // ✅ ADDED - Store batches for this PO

    public function mount($Id)
    {
        $this->Id = $Id;
        
        Log::info('Loading Purchase Order details', [
            'po_id' => $Id,
            'user' => 'Wts135',
            'timestamp' => '2025-11-11 08:01:50',
        ]);
        
        // Load purchase order with relationships
        $this->purchaseOrder = PurchaseOrder::with([
            'supplier',
            'productOrders.product.category',
            'productOrders.product.supplier',
            'orderedByUser',
            'approverInfo',
            'department',
            'approvalLogs.user',
            'deliveries',
        ])->findOrFail($Id);

        // Initialize computed properties
        $this->updateComputedProperties();
        
        // ✅ ADDED - Load batches for this PO
        $this->loadBatches();

        // ✅ CORRECT - Compare enum to enum
        if ($this->purchaseOrder->status === PurchaseOrderStatus::APPROVED) {
            if ($this->purchaseOrder->expected_delivery_date) {
                $this->expected_delivery_date = $this->purchaseOrder->expected_delivery_date->format('Y-m-d');
            }
            
            // Initialize arrays
            $this->expected_quantities = [];
            $this->batch_numbers = [];
            
            foreach ($this->purchaseOrder->productOrders as $order) {
                $this->expected_quantities[$order->id] = (int) ($order->expected_qty ?? $order->quantity);
                // Load existing batch numbers
                $this->batch_numbers[$order->id] = $order->batch_number ?? '';
            }
        }
        
        // ✅ CORRECT - Compare enum values
        if (in_array($this->purchaseOrder->status, [
            PurchaseOrderStatus::TO_RECEIVE,
            PurchaseOrderStatus::RECEIVED
        ])) {
            foreach ($this->purchaseOrder->productOrders as $order) {
                $this->batch_numbers[$order->id] = $order->batch_number ?? '';
            }
        }
        
        Log::info('Purchase Order loaded successfully', [
            'po_id' => $Id,
            'po_num' => $this->purchaseOrder->po_num,
            'status' => $this->purchaseOrder->status->value,
            'batch_count' => $this->batches->count(),
            'user' => 'Wts135',
            'timestamp' => '2025-11-11 08:01:50',
        ]);
    }

    /**
     * ✅ ADDED - Load batches for THIS purchase order ONLY
     */
    protected function loadBatches()
    {
        Log::info('Loading batches for Purchase Order', [
            'po_id' => $this->purchaseOrder->id,
            'po_num' => $this->purchaseOrder->po_num,
            'user' => 'Wts135',
            'timestamp' => '2025-11-11 08:01:50',
        ]);
        
        // ✅ Filter by purchase_order_id (not product_id)
        $this->batches = \App\Models\ProductBatch::with(['product', 'receivedByUser'])
            ->where('purchase_order_id', $this->purchaseOrder->id)
            ->orderBy('received_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        Log::info('Batches loaded successfully', [
            'po_id' => $this->purchaseOrder->id,
            'po_num' => $this->purchaseOrder->po_num,
            'batch_count' => $this->batches->count(),
            'batch_numbers' => $this->batches->pluck('batch_number')->unique()->toArray(),
            'product_ids' => $this->batches->pluck('product_id')->unique()->toArray(),
            'user' => 'Wts135',
            'timestamp' => '2025-11-11 08:01:50',
        ]);
    }

    // Update all computed properties
    protected function updateComputedProperties()
    {
        $this->totalQuantity = (int) $this->purchaseOrder->productOrders->sum('quantity');
        $this->totalPrice = $this->purchaseOrder->productOrders->sum('total_price');
        $this->totalExpected = (int) $this->purchaseOrder->productOrders->sum(function($order) {
            return $order->expected_qty ?? $order->quantity;
        });
        $this->totalDelivered = (int) $this->purchaseOrder->productOrders->sum('received_quantity');
        $this->totalReceived = (int) $this->purchaseOrder->productOrders->sum('received_quantity');
        $this->totalDestroyed = (int) $this->purchaseOrder->productOrders->sum('destroyed_quantity') ?? 0;
    }

    // Update computed properties when expected quantities change
    public function updatedExpectedQuantities()
    {
        $this->totalExpected = $this->purchaseOrder->productOrders->sum(function($order) {
            return $this->expected_quantities[$order->id] ?? ($order->expected_qty ?? $order->quantity);
        });
    }

    public function ApprovePurchaseOrder()
    {
        try {
            DB::beginTransaction();
            
            Log::info('Starting approval process', [
                'po_id' => $this->purchaseOrder->id,
                'po_num' => $this->purchaseOrder->po_num,
                'current_status' => $this->purchaseOrder->status->value,
                'user' => 'Wts135',
                'timestamp' => '2025-11-11 08:01:50',
            ]);
            
            // ✅ CORRECT - Compare enum to enum
            if ($this->purchaseOrder->status === PurchaseOrderStatus::PENDING) {
                // ✅ First approval: PENDING → APPROVED
                $autoExpectedDate = now()->addDays(7)->format('Y-m-d');
                
                $this->purchaseOrder->update([
                    'status' => PurchaseOrderStatus::APPROVED,
                    'approver' => Auth::id(),
                    'approved_at' => now(),
                    'approved_by' => Auth::id(),
                    'expected_delivery_date' => $autoExpectedDate,
                ]);

                PurchaseOrderApprovalLog::create([
                    'purchase_order_id' => $this->purchaseOrder->id,
                    'user_id' => Auth::id(),
                    'action' => 'approved',
                    'remarks' => 'Purchase order approved by ' . Auth::user()->name . '. Auto-expected delivery: ' . $autoExpectedDate,
                    'ip_address' => request()->ip(),
                ]);

                DB::commit();
                
                Log::info('Purchase order approved (PENDING → APPROVED)', [
                    'po_id' => $this->purchaseOrder->id,
                    'po_num' => $this->purchaseOrder->po_num,
                    'expected_delivery_date' => $autoExpectedDate,
                    'user' => 'Wts135',
                    'timestamp' => '2025-11-11 08:01:50',
                ]);
                
                session()->flash('message', 'Purchase order #' . $this->purchaseOrder->po_num . ' approved successfully! Expected delivery: ' . now()->addDays(7)->format('M d, Y'));
                
            } elseif ($this->purchaseOrder->status === PurchaseOrderStatus::APPROVED) {
                // ✅ Second approval: APPROVED → TO_RECEIVE (with batch numbers)
                
                Log::info('Attempting to save batch numbers', [
                    'batch_numbers' => $this->batch_numbers,
                    'po_id' => $this->purchaseOrder->id,
                    'user' => 'Wts135',
                    'timestamp' => '2025-11-11 08:01:50',
                ]);
                
                // Check if batch numbers are provided
                $batchNumbersProvided = array_filter($this->batch_numbers, function($value) {
                    return !empty(trim($value));
                });
                
                if (empty($batchNumbersProvided)) {
                    DB::rollBack();
                    Log::warning('No batch numbers provided', [
                        'po_id' => $this->purchaseOrder->id,
                        'user' => 'Wts135',
                        'timestamp' => '2025-11-11 08:01:50',
                    ]);
                    session()->flash('error', 'Please enter at least one batch number before completing approval.');
                    return;
                }

                // Save batch numbers to product orders
                $savedCount = 0;
                foreach ($this->batch_numbers as $orderId => $batchNumber) {
                    if (!empty(trim($batchNumber))) {
                        $productOrder = ProductOrder::where('id', $orderId)
                            ->where('purchase_order_id', $this->purchaseOrder->id)
                            ->first();
                        
                        if ($productOrder) {
                            $productOrder->batch_number = trim($batchNumber);
                            $productOrder->save();
                            $savedCount++;
                            
                            Log::info('Batch number saved to product order', [
                                'order_id' => $orderId,
                                'product_id' => $productOrder->product_id,
                                'batch_number' => trim($batchNumber),
                                'user' => 'Wts135',
                                'timestamp' => '2025-11-11 08:01:50',
                            ]);
                        } else {
                            Log::warning('Product order not found', [
                                'order_id' => $orderId,
                                'user' => 'Wts135',
                                'timestamp' => '2025-11-11 08:01:50',
                            ]);
                        }
                    }
                }

                if ($savedCount === 0) {
                    DB::rollBack();
                    Log::error('Failed to save batch numbers', [
                        'po_id' => $this->purchaseOrder->id,
                        'user' => 'Wts135',
                        'timestamp' => '2025-11-11 08:01:50',
                    ]);
                    session()->flash('error', 'Failed to save batch numbers. Please try again.');
                    return;
                }

                // Update status to TO_RECEIVE
                $this->purchaseOrder->status = PurchaseOrderStatus::TO_RECEIVE;
                $this->purchaseOrder->save();

                PurchaseOrderApprovalLog::create([
                    'purchase_order_id' => $this->purchaseOrder->id,
                    'user_id' => Auth::id(),
                    'action' => 'approved',
                    'remarks' => "Purchase order marked as ready for receiving. {$savedCount} batch number(s) saved.",
                    'ip_address' => request()->ip(),
                ]);

                DB::commit();
                
                // Refresh data
                $this->purchaseOrder->refresh();
                $this->updateComputedProperties();
                $this->loadBatches(); // ✅ ADDED - Refresh batches
                
                Log::info('Purchase order marked as ready to receive (APPROVED → TO_RECEIVE)', [
                    'po_id' => $this->purchaseOrder->id,
                    'po_num' => $this->purchaseOrder->po_num,
                    'batch_count' => $savedCount,
                    'user' => 'Wts135',
                    'timestamp' => '2025-11-11 08:01:50',
                ]);
                
                session()->flash('message', "Purchase order is now ready for receiving. {$savedCount} batch number(s) have been saved.");
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval error', [
                'po_id' => $this->purchaseOrder->id ?? null,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => 'Wts135',
                'timestamp' => '2025-11-11 08:01:50',
            ]);
            session()->flash('error', 'Failed to approve purchase order: ' . $e->getMessage());
        }
    }
    
    public function RejectPurchaseOrder()
    {
        try {
            DB::transaction(function () {
                $this->validate([
                    'cancellation_reason' => 'required|string|min:5|max:500',
                ]);

                Log::info('Rejecting purchase order', [
                    'po_id' => $this->purchaseOrder->id,
                    'po_num' => $this->purchaseOrder->po_num,
                    'reason' => $this->cancellation_reason,
                    'user' => 'Wts135',
                    'timestamp' => '2025-11-11 08:01:50',
                ]);

                $this->purchaseOrder->update([
                    'status' => PurchaseOrderStatus::CANCELLED,
                    'cancellation_reason' => $this->cancellation_reason,
                    'cancelled_at' => now(),
                    'cancelled_by' => Auth::id(),
                ]);
                
                // Log the rejection action
                PurchaseOrderApprovalLog::create([
                    'purchase_order_id' => $this->purchaseOrder->id,
                    'user_id' => Auth::id(),
                    'action' => 'rejected',
                    'remarks' => $this->cancellation_reason,
                    'ip_address' => request()->ip(),
                ]);
                
                // Refresh the purchase order and computed properties
                $this->purchaseOrder->refresh();
                $this->updateComputedProperties();
                
                Log::info('Purchase order rejected successfully', [
                    'po_id' => $this->purchaseOrder->id,
                    'po_num' => $this->purchaseOrder->po_num,
                    'user' => 'Wts135',
                    'timestamp' => '2025-11-11 08:01:50',
                ]);
                
                session()->flash('message', 'Purchase order rejected.');
            });
            
        } catch (\Exception $e) {
            Log::error('Rejection error', [
                'po_id' => $this->purchaseOrder->id ?? null,
                'message' => $e->getMessage(),
                'user' => 'Wts135',
                'timestamp' => '2025-11-11 08:01:50',
            ]);
            session()->flash('error', 'Failed to reject purchase order: ' . $e->getMessage());
        }
    }

    public function ReturnToApproved()
    {
        try {
            DB::transaction(function () {
                $this->validate([
                    'cancellation_reason' => 'required|string|min:5|max:500',
                ]);

                Log::info('Returning purchase order to approved status', [
                    'po_id' => $this->purchaseOrder->id,
                    'po_num' => $this->purchaseOrder->po_num,
                    'reason' => $this->cancellation_reason,
                    'user' => 'Wts135',
                    'timestamp' => '2025-11-11 08:01:50',
                ]);

                $this->purchaseOrder->update([
                    'status' => PurchaseOrderStatus::APPROVED,
                    'loaded_date' => null,
                    'return_reason' => $this->cancellation_reason,
                ]);

                // Reset batch numbers in database
                foreach ($this->purchaseOrder->productOrders as $order) {
                    $order->update([
                        'batch_number' => null,
                    ]);
                }

                // Clear the batch_numbers array in component
                $this->batch_numbers = [];

                // Log the return action
                PurchaseOrderApprovalLog::create([
                    'purchase_order_id' => $this->purchaseOrder->id,
                    'user_id' => Auth::id(),
                    'action' => 'returned_to_approved',
                    'remarks' => 'Returned to approved: ' . $this->cancellation_reason,
                    'ip_address' => request()->ip(),
                ]);

                // Refresh the purchase order and computed properties
                $this->purchaseOrder->refresh();
                $this->updateComputedProperties();
                $this->loadBatches(); // ✅ ADDED - Refresh batches
                
                Log::info('Purchase order returned to approved status successfully', [
                    'po_id' => $this->purchaseOrder->id,
                    'po_num' => $this->purchaseOrder->po_num,
                    'user' => 'Wts135',
                    'timestamp' => '2025-11-11 08:01:50',
                ]);
                
                session()->flash('message', 'Purchase order returned to approved status. Batch numbers cleared.');
            });
            
        } catch (\Exception $e) {
            Log::error('Return to approved error', [
                'po_id' => $this->purchaseOrder->id ?? null,
                'message' => $e->getMessage(),
                'user' => 'Wts135',
                'timestamp' => '2025-11-11 08:01:50',
            ]);
            session()->flash('error', 'Failed to return purchase order: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.POmanagement.purchase-order.show');
    }
}