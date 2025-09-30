<?php

namespace App\Livewire\Pages\PaperRollWarehouse\PurchaseOrder;

use App\Models\PurchaseOrder;
use App\Models\RawMatInv;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $statusFilter = '';
    public $deletingPurchaseOrderId = null;
    public $showDeleteModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
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

            $purchaseOrder = PurchaseOrder::with('rawMatOrders')->findOrFail($this->deletingPurchaseOrderId);
            
            // Delete all associated supply orders first
            $purchaseOrder->rawMatOrders()->delete();

            // Delete the purchase order
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

    public function render()
    {
        $purchaseOrders = PurchaseOrder::query()
            ->where('po_type', 'raw_mats')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('del_to', 'like', '%' . $this->search . '%')
                        ->orWhere('payment_terms', 'like', '%' . $this->search . '%')
                        ->orWhere('quotation', 'like', '%' . $this->search . '%')
                        ->orWhereHas('supplier', function ($query) {
                            $query->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->with(['supplier', 'department'])
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.paper-roll-warehouse.purchase-order.index', [
            'purchaseOrders' => $purchaseOrders
        ]);
    }
}
