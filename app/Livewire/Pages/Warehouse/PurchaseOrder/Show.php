<?php

namespace App\Livewire\Pages\Warehouse\PurchaseOrder;

use Livewire\Component;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    public $purchaseOrder;
    public $Id;
    public $perPage = 10;
    public $showViewModal = false;
    public $selectedPOId = null;

    public function mount($Id)
    {
        $this->Id = $Id;
        
        // ✅ Changed from rawMatOrders to productOrders
        $this->purchaseOrder = PurchaseOrder::with([
            'supplier',
            'productOrders.product.category',  // ✅ Changed relationship
            'productOrders.product.supplier',  // ✅ Added for product supplier info
            'orderedByUser',                   // ✅ Changed from orderedBy
            'approverInfo',
            'department'
        ])->findOrFail($Id);
    }

    public function viewPurchaseOrder($poId)
    {
        $this->selectedPOId = $poId;
        $this->showViewModal = true;
    }

    public function ApprovePurchaseOrder()
    {
        $this->purchaseOrder->update([
            'status' => 'approved',
            'approver' => Auth::id(),
        ]);
        
        session()->flash('message', 'Purchase order approved successfully.');
        return redirect()->route('warehouse.purchaseorder');
    }

    public function RejectPurchaseOrder()
    {
        $this->purchaseOrder->update([
            'status' => 'rejected',
            'approver' => Auth::id(),
        ]);
        
        session()->flash('message', 'Purchase order rejected.');
        return redirect()->route('warehouse.purchaseorder');
    }

    public function render()
    {
        // ✅ Fixed: Should render 'show' view, not 'index'
        return view('livewire.pages.warehouse.purchase-order.show');
    }
}