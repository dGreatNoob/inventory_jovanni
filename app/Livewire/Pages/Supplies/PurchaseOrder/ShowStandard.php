<?php

namespace App\Livewire\Pages\Supplies\PurchaseOrder;

use App\Models\PurchaseOrder;
use Livewire\Component;

class ShowStandard extends Component
{
    public $Id;
    public $purchaseOrder;
    public $totalPrice = 0;
    public $totalQuantity = 0;

    public function mount($Id)
    {
        $this->Id = $Id;
        $this->purchaseOrder = PurchaseOrder::with([
            'supplier',
            'supplyOrders.supplyProfile.itemType',
            'orderedBy',
            'approverInfo',
            'department'
        ])->findOrFail($Id);

        // Calculate totals
        $this->totalPrice = $this->purchaseOrder->supplyOrders->sum('order_total_price');
        $this->totalQuantity = $this->purchaseOrder->supplyOrders->sum('order_qty');
    }

    public function render()
    {
        return view('livewire.pages.supplies.purchase-order.show-standard');
    }
}
