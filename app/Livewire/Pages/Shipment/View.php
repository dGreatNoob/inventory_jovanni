<?php

namespace App\Livewire\Pages\Shipment;

use Livewire\Component;
use App\Models\SalesOrder;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;

class View extends Component
{
    public $shipmentId;
    public $ShipmentResult;
    public $company_results;
    public $product_results;
    public $shippingMethodDropDown = [];
    public $editingItemId = null;
    public $editQuantities = [];
    
    public function mount($shipmentId)
    {
        $this->shipmentId = $shipmentId;
        $this->ShipmentResult = Shipment::findOrFail($shipmentId);
        $this->company_results  = \App\Models\Customer::all()->pluck('name', 'id');
        
        $this->shippingMethodDropDown = Shipment::deliveryMethodDropDown();

    }

    public function completeShipment()
    {
        // Only update if the current shipping status is 'pending' to prevent bypassing the intended flow
        if ($this->ShipmentResult->shipping_status === 'pending') {
            $this->ShipmentResult->shipping_status = 'completed';
            $this->ShipmentResult->approver_id = Auth::id(); // shorter version of Auth::user()->id
            $this->ShipmentResult->save();
        }

        session()->flash('message', 'Shipment has been completed successfully.');
        return redirect()->route('shipment.index');
    }

    public function rejectSalesOrder()
    {
        // if (!Auth::user()->can(PermissionEnum::APPROVE_REQUEST_SLIP->value)) {

        //     abort(403, 'You do not have permission to approve this request slip.');

        // } else {
        //     $this->request_slip->update([
        //         'status' => 'rejected',
        //         'approver' => Auth::user()->id,

        //     ]);
        //     session()->flash('message', 'Request Slip Rejected.');
        //     return redirect()->route('requisition.requestslip');
        // }

        $this->ShipmentResult->shipping_status = 'cancelled';
        $this->ShipmentResult->approver_id = Auth::user()->id;
        $this->ShipmentResult->save();

        session()->flash('message', 'Shipment has been cancelled successfully.');
        return redirect()->route('shipment.index');
    }

    public function startEdit($itemId)
    {
        $this->editingItemId = $itemId;
        $item = \App\Models\BranchAllocationItem::find($itemId);
        $this->editQuantities[$itemId] = $item->quantity;
    }

    public function saveEdit($itemId)
    {
        $this->validate([
            'editQuantities.' . $itemId => 'required|integer|min:0',
        ]);

        $item = \App\Models\BranchAllocationItem::find($itemId);
        $item->quantity = $this->editQuantities[$itemId];
        $item->save();

        $this->editingItemId = null;
        unset($this->editQuantities[$itemId]);

        session()->flash('message', 'Product quantity updated successfully.');
    }

    public function cancelEdit()
    {
        $this->editingItemId = null;
        $this->editQuantities = [];
    }

    public function render()
    {
        return view('livewire.pages.shipment.view', [
            'shipment_view' => Shipment::with(['salesOrder', 'branchAllocation.items.product'])->find($this->shipmentId),
        ]);
    }
}
