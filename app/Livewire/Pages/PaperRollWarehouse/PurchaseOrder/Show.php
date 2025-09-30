<?php

namespace App\Livewire\Pages\PaperRollWarehouse\PurchaseOrder;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\RawMatInv;
use Illuminate\Support\Facades\Auth;
use PDO;

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
        $this->purchaseOrder = PurchaseOrder::with(['supplier', 'rawMatOrders.rawMatProfile', 'orderedBy', 'approverInfo', 'department'])
            ->findOrFail($Id);
    }

    public function viewPurchaseOrder($poId)
    {
        $this->selectedPOId = $poId;
        $this->showViewModal = true;
    }

    public function ApprovePurchaseOrder()
    {
        // dd(auth()->user());
        $this->purchaseOrder->update([
            'status' => 'approved',
            'approver' => Auth::user()->id,
           
        ]);
        session()->flash('message', 'Purchase order approved successfully.');
        return redirect()->route('prw.purchaseorder');
        
    }

    public function RejectPurchaseOrder()
    {
        // dd(auth()->user());
        $this->purchaseOrder->update([
            'status' => 'rejected',
            'approver' => Auth::user()->id,
           
        ]);
        session()->flash('message', 'Purchase order Rejected.');
        return redirect()->route('prw.purchaseorder');
        
    }

    public function render()
    {
        return view('livewire.pages.paper-roll-warehouse.purchase-order.show');
    }
}
