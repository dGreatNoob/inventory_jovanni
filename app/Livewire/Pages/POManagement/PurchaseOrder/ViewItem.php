<?php

namespace App\Livewire\Pages\POManagement\PurchaseOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseOrderItem;

class ViewItem extends Component
{
    use WithPagination;

    public $poId;
    public $perPage = 10;

    public function mount($poId = null)
    {
        $this->poId = $poId ?? request()->get('poId');
    }

    public function render()
    {
        $purchaseOrderItems = PurchaseOrderItem::query()
            ->where('purchase_order_id', $this->poId)
            ->with(['purchaseOrder.supplier', 'purchaseOrder.department', 'product'])
            ->latest()
            ->paginate($this->perPage);


        return view('livewire.pages.POmanagement.purchase-order.index', [
            'purchaseOrderItems' => $purchaseOrderItems
        ]);
    }
}
