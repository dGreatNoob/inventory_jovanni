<?php

namespace App\Livewire\Pages\POManagement\PurchaseOrder;

use App\Models\PurchaseOrder;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrderApprovalLog;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $statusFilter = '';
    public $deletingPurchaseOrderId = null;
    public $showDeleteModal = false;
    public $receivingPurchaseOrderId = null;
    public $showReceiveModal = false;
    public $deliveringPurchaseOrderId = null;
    public $showDeliverModal = false;
    public $activeTab = 'list';
    public $viewingLogsForPO = null;
    public $viewingQRForPO = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];
    

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deletingPurchaseOrderId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::with('productOrders')->findOrFail($this->deletingPurchaseOrderId);
            
            $purchaseOrder->productOrders()->delete();
            $purchaseOrder->delete();

            DB::commit();

            $this->reset(['deletingPurchaseOrderId', 'showDeleteModal']);
            session()->flash('message', 'Purchase order and its associated items have been deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to delete purchase order: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->reset(['deletingPurchaseOrderId', 'showDeleteModal']);
    }

    // ✅ UPDATED: Mark as Delivered with Logging
    public function confirmDeliver($id)
    {
        $this->deliveringPurchaseOrderId = $id;
        $this->showDeliverModal = true;
    }

    public function markAsDelivered()
    {
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($this->deliveringPurchaseOrderId);
            
            if ($purchaseOrder->status === 'delivered' || $purchaseOrder->status === 'received') {
                session()->flash('error', 'This purchase order has already been delivered.');
                $this->cancelDeliver();
                return;
            }

            if ($purchaseOrder->status !== 'approved') {
                session()->flash('error', 'Only approved purchase orders can be marked as delivered.');
                $this->cancelDeliver();
                return;
            }

            // Update status
            $purchaseOrder->update([
                'status' => 'delivered',
            ]);

            // ✅ Log the action
            PurchaseOrderApprovalLog::create([
                'purchase_order_id' => $purchaseOrder->id,
                'user_id' => auth()->id(),
                'action' => 'delivered',
                'remarks' => 'Purchase order marked as delivered by ' . auth()->user()->name,
                'ip_address' => request()->ip(),
            ]);

            session()->flash('message', 'Purchase order #' . $purchaseOrder->po_num . ' marked as delivered successfully.');
            $this->cancelDeliver();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to mark as delivered: ' . $e->getMessage());
        }
    }

    public function cancelDeliver()
    {
        $this->reset(['deliveringPurchaseOrderId', 'showDeliverModal']);
    }

    // ✅ UPDATED: Mark as Received with Logging
    public function confirmReceive($id)
    {
        $this->receivingPurchaseOrderId = $id;
        $this->showReceiveModal = true;
    }

    public function markAsReceived()
    {
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($this->receivingPurchaseOrderId);
            
            if ($purchaseOrder->status === 'received') {
                session()->flash('error', 'This purchase order has already been marked as received.');
                $this->cancelReceive();
                return;
            }

            if ($purchaseOrder->status !== 'delivered') {
                session()->flash('error', 'Only delivered purchase orders can be marked as received.');
                $this->cancelReceive();
                return;
            }

            // Update status and delivery date with current timestamp
            $purchaseOrder->update([
                'status' => 'received',
                'del_on' => now(),
            ]);

            // ✅ Log the action
            PurchaseOrderApprovalLog::create([
                'purchase_order_id' => $purchaseOrder->id,
                'user_id' => auth()->id(),
                'action' => 'received',
                'remarks' => 'Purchase order marked as received by ' . auth()->user()->name,
                'ip_address' => request()->ip(),
            ]);

            session()->flash('message', 'Purchase order #' . $purchaseOrder->po_num . ' marked as received successfully.');
            $this->cancelReceive();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to mark as received: ' . $e->getMessage());
        }
    }

    public function cancelReceive()
    {
        $this->reset(['receivingPurchaseOrderId', 'showReceiveModal']);
    }

    public function render()
    {
        $purchaseOrders = PurchaseOrder::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('po_num', 'like', '%' . $this->search . '%')
                        ->orWhereHas('supplier', function ($query) {
                            $query->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('department', function ($query) {
                            $query->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter, function ($query) {
                // ✅ UPDATED: Added 'partially_received' to status filter
                $query->where('status', $this->statusFilter);
            })
            ->with(['supplier', 'department', 'orderedByUser', 'approvalLogs'])
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.POmanagement.purchase-order.index', [
            'purchaseOrders' => $purchaseOrders
        ]);
    }
}