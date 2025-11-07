<?php

namespace App\Livewire\Pages\POManagement\PurchaseOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseOrder;

class PODeliveries extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

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

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $purchaseOrders = PurchaseOrder::query()
            ->with(['supplier', 'productOrders'])
            ->whereIn('status', ['to_receive', 'received'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('po_num', 'like', '%' . $this->search . '%')
                      ->orWhereHas('supplier', function ($query) {
                          $query->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'complete') {
                    $query->where('status', 'received');
                } elseif ($this->statusFilter === 'partial') {
                    $query->where('status', 'to_receive')
                          ->whereHas('productOrders', function ($q) {
                              $q->where('received_quantity', '>', 0);
                          });
                } elseif ($this->statusFilter === 'pending') {
                    $query->where('status', 'to_receive')
                          ->whereHas('productOrders', function ($q) {
                              $q->where('received_quantity', 0);
                          });
                }
            })
            ->orderBy('order_date', 'desc')
            ->paginate($this->perPage);

        return view('livewire.pages.POmanagement.purchase-order.PO-deliveries', [
            'purchaseOrders' => $purchaseOrders
        ]);
    }
}