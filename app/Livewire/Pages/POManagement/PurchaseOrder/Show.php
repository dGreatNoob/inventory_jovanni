<?php

namespace App\Livewire\Pages\POManagement\PurchaseOrder;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderApprovalLog;
use App\Models\ProductOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function mount($Id)
    {
        $this->Id = $Id;
        
        // Load purchase order with relationships
        $this->purchaseOrder = PurchaseOrder::with([
            'supplier',
            'productOrders.product.category',
            'productOrders.product.supplier',
            'orderedByUser',
            'approverInfo',
            'department',
            'approvalLogs.user',
            'deliveries',  // ✅ ADD THIS - for delivery information cards
        ])->findOrFail($Id);

        // Initialize computed properties
        $this->updateComputedProperties();

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
            
            // ✅ CORRECT - Compare enum to enum
            if ($this->purchaseOrder->status === PurchaseOrderStatus::PENDING) {
                // ✅ First approval: PENDING → APPROVED
                $autoExpectedDate = now()->addDays(7)->format('Y-m-d');
                
                $this->purchaseOrder->update([
                    'status' => PurchaseOrderStatus::APPROVED,  // ✅ Use enum, not string
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
                session()->flash('message', 'Purchase order #' . $this->purchaseOrder->po_num . ' approved successfully! Expected delivery: ' . now()->addDays(7)->format('M d, Y'));
                
            } elseif ($this->purchaseOrder->status === PurchaseOrderStatus::APPROVED) {  // ✅ Compare enum to enum
                // ✅ Second approval: APPROVED → TO_RECEIVE (with batch numbers)
                
                \Log::info('Attempting to save batch numbers', [
                    'batch_numbers' => $this->batch_numbers,
                    'po_id' => $this->purchaseOrder->id,
                ]);
                
                // Check if batch numbers are provided
                $batchNumbersProvided = array_filter($this->batch_numbers, function($value) {
                    return !empty(trim($value));
                });
                
                if (empty($batchNumbersProvided)) {
                    DB::rollBack();
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
                            
                            \Log::info("Saved batch number", [
                                'order_id' => $orderId,
                                'batch_number' => trim($batchNumber),
                                'saved' => $productOrder->batch_number,
                            ]);
                        } else {
                            \Log::warning("Product order not found", ['order_id' => $orderId]);
                        }
                    }
                }

                if ($savedCount === 0) {
                    DB::rollBack();
                    session()->flash('error', 'Failed to save batch numbers. Please try again.');
                    return;
                }

                // Update status to TO_RECEIVE
                $this->purchaseOrder->status = PurchaseOrderStatus::TO_RECEIVE;  // ✅ Use enum
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
                
                session()->flash('message', "Purchase order is now ready for receiving. {$savedCount} batch number(s) have been saved.");
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Approval error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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

                $this->purchaseOrder->update([
                    'status' => PurchaseOrderStatus::CANCELLED,  // ✅ Use enum
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
                
                session()->flash('message', 'Purchase order rejected.');
            });
            
        } catch (\Exception $e) {
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

                $this->purchaseOrder->update([
                    'status' => PurchaseOrderStatus::APPROVED,  // ✅ Use enum
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
                
                session()->flash('message', 'Purchase order returned to approved status. Batch numbers cleared.');
            });
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to return purchase order: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.POmanagement.purchase-order.show');
    }
}