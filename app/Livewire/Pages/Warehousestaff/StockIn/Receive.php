<?php

namespace App\Livewire\Pages\Warehousestaff\StockIn;

use Livewire\Component;
use App\Models\ProductOrder;
use App\Models\PurchaseOrder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.phone'), Title('Warehousestaff - Stock In')]
class Receive extends Component
{
    public $purchaseOrder;
    public $remarks = [];
    public $received_qty = [];
    public $reasons = [];
    public $productOrders;

    public function mount($purchaseOrder)
    {
        $this->purchaseOrder = PurchaseOrder::findOrFail($purchaseOrder);
        $this->productOrders = ProductOrder::with('product')
            ->where('purchase_order_id', $purchaseOrder)
            ->get();

        // Initialize fields
        foreach ($this->productOrders as $order) {
            $this->remarks[$order->id] = 'Accepted';
            $this->received_qty[$order->id] = $order->quantity;
            $this->reasons[$order->id] = '';
        }
    }

    public function render()
    {
        return view('livewire.pages.warehousestaff.stock-in.receive', [
            'productOrders' => $this->productOrders,
            'purchaseOrder' => $this->purchaseOrder
        ]);
    }
}