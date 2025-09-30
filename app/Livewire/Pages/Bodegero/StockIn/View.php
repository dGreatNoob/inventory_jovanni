<?php

namespace App\Livewire\Pages\Bodegero\StockIn;

use App\Models\SupplyOrder;
use App\Models\PurchaseOrder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.phone'), Title('Bodegero - Stock In')]
class View extends Component
{
    public $purchaseOrder;


    public function mount($purchaseOrder)
    {
        // Load purchase order with supplier
        $this->purchaseOrder = PurchaseOrder::with('supplier')->findOrFail($purchaseOrder);
       
    }
    // public function updateStatusByQrCode($qrCode)//THIS FUNCTION IS TEMPORARY LANG , WALA PA NI PULOS
    // {
    //     $supplyOrder = SupplyOrder::find($qrCode);

    //     if ($supplyOrder) {
    //         $supplyOrder->status = 'delivered';
    //         $supplyOrder->save();
            
            
    //         $this->dispatch('statusUpdated', message: "Status updated for QR: $qrCode");
    //         $this->dispatch('$refresh');
    //     } else {
    //         $this->dispatch('statusUpdated', message: "QR code not found: $qrCode", error: true);
    //     }
    // }
    public function render()
    {
         // Fetch supply orders related to this purchase order
        $supplyOrders = SupplyOrder::with('supplyProfile')
            ->where('purchase_order_id', $this->purchaseOrder->id)
            ->get();
        return view('livewire.pages.bodegero.stock-in.view', [
            'purchaseOrder' => $this->purchaseOrder,
            'supplyOrders' => $supplyOrders,
        ]);
    }
}
