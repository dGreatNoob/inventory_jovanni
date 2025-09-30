<?php

namespace App\Livewire\Pages\Supplies\PurchaseOrder;

use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ShowForApproval extends Component
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

    public function ApprovePurchaseOrder()
    {
        $this->purchaseOrder->update([
            'status' => 'to_receive',
            'approver' => Auth::user()->id,
        ]);

        // Activity logging
        $itemSummary = $this->purchaseOrder->supplyOrders->map(function($order) {
            return $order->supplyProfile->supply_description . ' (Qty: ' . $order->order_qty . ')';
        })->implode(', ');

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->purchaseOrder)
            ->withProperties([
                'po_num' => $this->purchaseOrder->po_num,
                'supplier' => $this->purchaseOrder->supplier ? $this->purchaseOrder->supplier->name : '-',
                'total_price' => $this->totalPrice,
                'items' => $itemSummary,
            ])
            ->log('Purchase order approved');

        session()->flash('message', 'Purchase order approved and marked as To Receive.');
        return redirect()->route('supplies.PurchaseOrder');
    }

    public function RejectPurchaseOrder()
    {
        $this->purchaseOrder->update([
            'status' => 'rejected',
            'approver' => Auth::user()->id,
        ]);

        // Activity logging
        $itemSummary = $this->purchaseOrder->supplyOrders->map(function($order) {
            return $order->supplyProfile->supply_description . ' (Qty: ' . $order->order_qty . ')';
        })->implode(', ');

        activity()
            ->causedBy(Auth::user())
            ->performedOn($this->purchaseOrder)
            ->withProperties([
                'po_num' => $this->purchaseOrder->po_num,
                'supplier' => $this->purchaseOrder->supplier ? $this->purchaseOrder->supplier->name : '-',
                'total_price' => $this->totalPrice,
                'items' => $itemSummary,
            ])
            ->log('Purchase order rejected');

        session()->flash('message', 'Purchase order Rejected.');
        return redirect()->route('supplies.PurchaseOrder');
    }

    public function render()
    {
        return view('livewire.pages.supplies.purchase-order.show-for-approval');
    }
}
