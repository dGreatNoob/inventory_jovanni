<?php

namespace App\Livewire\Pages\Warehousestaff\StockIn;

use App\Models\ProductOrder;
use App\Models\PurchaseOrder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.phone'), Title('Warehousestaff - Stock In')]
class View extends Component
{
    public $purchaseOrder;

    public function mount($purchaseOrder)
    {
        // Load purchase order with supplier
        $this->purchaseOrder = PurchaseOrder::with('supplier')->findOrFail($purchaseOrder);
    }

    // Example function for future QR code status updates
    // public function updateStatusByQrCode($qrCode)
    // {
    //     $productOrder = ProductOrder::find($qrCode);
    //     if ($productOrder) {
    //         $productOrder->status = 'delivered';
    //         $productOrder->save();
    //         $this->dispatch('statusUpdated', message: "Status updated for QR: $qrCode");
    //         $this->dispatch('$refresh');
    //     } else {
    //         $this->dispatch('statusUpdated', message: "QR code not found: $qrCode", error: true);
    //     }
    // }

    public function render()
    {
        // Fetch product orders related to this purchase order
        $productOrders = ProductOrder::with('product')
            ->where('purchase_order_id', $this->purchaseOrder->id)
            ->get();

        return view('livewire.pages.warehousestaff.stock-in.view', [
            'purchaseOrder' => $this->purchaseOrder,
            'productOrders' => $productOrders,
        ]);
    }
}