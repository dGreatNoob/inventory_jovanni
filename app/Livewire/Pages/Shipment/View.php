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
        $this->ShipmentResult = Shipment::with('deliveryReceipt')->findOrFail($shipmentId);
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
        $this->editQuantities[$itemId] = $item->scanned_quantity;
    }

    public function saveEdit($itemId)
    {
        $this->validate([
            'editQuantities.' . $itemId => 'required|integer|min:0',
        ]);

        $item = \App\Models\BranchAllocationItem::find($itemId);
        $oldQuantity = $item->scanned_quantity;
        $item->scanned_quantity = $this->editQuantities[$itemId];
        $item->save();

        // Update box current_count if it exists
        if ($item->box) {
            $box = $item->box;
            $totalScannedInBox = \App\Models\BranchAllocationItem::where('box_id', $box->id)->sum('scanned_quantity');
            $box->update(['current_count' => $totalScannedInBox]);
        }

        // Update DR scanned_items count
        if ($item->delivery_receipt_id) {
            $dr = \App\Models\DeliveryReceipt::find($item->delivery_receipt_id);
            if ($dr) {
                $totalScannedInDR = \App\Models\BranchAllocationItem::where('delivery_receipt_id', $dr->id)->sum('scanned_quantity');
                $dr->update(['scanned_items' => $totalScannedInDR]);
            }
        }

        // Log activity
        \Spatie\Activitylog\Models\Activity::create([
            'log_name' => 'branch_inventory',
            'description' => "Updated scanned quantity for product {$item->getDisplayBarcodeAttribute()} in shipment {$this->ShipmentResult->shipping_plan_num}",
            'subject_type' => \App\Models\BranchAllocationItem::class,
            'subject_id' => $item->id,
            'causer_type' => null,
            'causer_id' => null,
            'properties' => [
                'product_id' => $item->product_id,
                'product_name' => $item->getDisplayNameAttribute(),
                'barcode' => $item->getDisplayBarcodeAttribute(),
                'shipment_id' => $this->ShipmentResult->id,
                'old_scanned_quantity' => $oldQuantity,
                'new_scanned_quantity' => $this->editQuantities[$itemId],
                'branch_id' => $this->ShipmentResult->branchAllocation->branch_id ?? null,
                'box_number' => $item->box->box_number ?? null,
            ],
        ]);

        $this->editingItemId = null;
        unset($this->editQuantities[$itemId]);

        session()->flash('message', 'Scanned quantity updated successfully.');
    }

    public function cancelEdit()
    {
        $this->editingItemId = null;
        $this->editQuantities = [];
    }

    public function render()
    {
        return view('livewire.pages.shipment.view', [
            'shipment_view' => Shipment::with(['salesOrder', 'branchAllocation', 'deliveryReceipt', 'vehicles.deliveryReceipt.box'])->find($this->shipmentId),
        ]);
    }
}
