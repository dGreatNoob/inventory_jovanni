<?php

namespace App\Livewire\Pages\Bodegero\StockIn;

use Livewire\Component;
use App\Models\SupplyOrder;
use App\Models\PurchaseOrder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.phone'), Title('Bodegero - Stock In')]
class Receive extends Component
{
    public $purchaseOrder;
    public $remarks = [];
    public $received_qty = [];
    public $reasons = [];
    public $supplyOrders;

    public function mount($purchaseOrder)
    {
        $this->purchaseOrder = PurchaseOrder::findOrFail($purchaseOrder);
        $this->supplyOrders = SupplyOrder::with('supplyProfile')
            ->where('purchase_order_id', $purchaseOrder)
            ->get();

        // Initialize fields
        foreach ($this->supplyOrders as $order) {
            $this->remarks[$order->id] = 'Accepted';
            $this->received_qty[$order->id] = $order->order_qty;
            $this->reasons[$order->id] = '';
        }
    }
    public function render()
    {
        return view('livewire.pages.bodegero.stock-in.receive', ['supplyOrders' => $this->supplyOrders, 'purchaseOrder' => $this->purchaseOrder]);
    }
}
