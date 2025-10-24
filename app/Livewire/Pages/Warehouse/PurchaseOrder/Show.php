<?php

namespace App\Livewire\Pages\Warehouse\PurchaseOrder;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderApprovalLog;
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
        
        // Load purchase order with relationships including approval logs
        $this->purchaseOrder = PurchaseOrder::with([
            'supplier',
            'productOrders.product.category',
            'productOrders.product.supplier',
            'orderedByUser',
            'approverInfo',
            'department',
            'approvalLogs.user'  // ✅ Added approval logs
        ])->findOrFail($Id);
    }

    public function ApprovePurchaseOrder()
    {
        try {
            $this->purchaseOrder->update([
                'status' => 'approved',
                'approver' => Auth::id(),
            ]);
            
            // ✅ Log the approval action
            PurchaseOrderApprovalLog::create([
                'purchase_order_id' => $this->purchaseOrder->id,
                'user_id' => Auth::id(),
                'action' => 'approved',
                'remarks' => 'Purchase order approved by ' . Auth::user()->name,
                'ip_address' => request()->ip(),
            ]);
            
            session()->flash('message', 'Purchase order approved successfully.');
            return redirect()->route('warehouse.purchaseorder');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve purchase order: ' . $e->getMessage());
        }
    }

    public function RejectPurchaseOrder()
    {
        try {
            $this->purchaseOrder->update([
                'status' => 'rejected',
                'approver' => Auth::id(),
            ]);
            
            // ✅ Log the rejection action
            PurchaseOrderApprovalLog::create([
                'purchase_order_id' => $this->purchaseOrder->id,
                'user_id' => Auth::id(),
                'action' => 'rejected',
                'remarks' => 'Purchase order rejected by ' . Auth::user()->name,
                'ip_address' => request()->ip(),
            ]);
            
            session()->flash('message', 'Purchase order rejected.');
            return redirect()->route('warehouse.purchaseorder');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reject purchase order: ' . $e->getMessage());
        }
    }

    public function render()
    {
                // ✅ Fixed: Should render 'show' view, not 'index'
        return view('livewire.pages.warehouse.purchase-order.show');
    }
}