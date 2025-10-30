<?php

namespace App\Livewire\Pages\SalesManagement;

use App\Enums\Enum\PermissionEnum;
use App\Enums\RolesEnum;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class View extends Component
{

    public $salesOrderId;
    public $salesOrderResult;
    public $company_results;
    public $product_results;
    public $shippingMethodDropDown = [];
    public $paymentMethodDropdown = [];
    public $paymentTermsDropdown = [];
    public $editingItems = []; // Track which items are being edited
    public $editablePrices = []; // Store editable prices for each item
    
    public function mount($salesOrderId)
    {
        $this->salesOrderId = $salesOrderId;
        $this->salesOrderResult = SalesOrder::findOrFail($salesOrderId);
        $this->company_results  = \App\Models\Customer::all()->pluck('name', 'id');
        $this->product_results  = \App\Models\SupplyProfile::all()->pluck('supply_description', 'id');
        $this->shippingMethodDropDown = SalesOrder::shippingMethodDropDown();   
        $this->paymentMethodDropdown  = SalesOrder::paymentMethodDropdown();   
        $this->paymentTermsDropdown   = SalesOrder::paymentTermsDropdown(); 
    }

    public function approveSalesOrder()
    {
        // if (!Auth::user()->can(PermissionEnum::APPROVE_REQUEST_SLIP->value)) {

        //     abort(403, 'You do not have permission to approve this request slip.');

        // } else {
        //     $this->request_slip->update([
        //         'status' => 'approved',
        //         'approver' => Auth::user()->id,

        //     ]);
        //     session()->flash('message', 'Request Slip approved successfully.');
        //     return redirect()->route('requisition.requestslip');
        // }

        $this->salesOrderResult->status = 'approved'; 
        $this->salesOrderResult->approver = Auth::user()->id; 
        $this->salesOrderResult->save();

        session()->flash('message', 'Sales order approved successfully.');
        return redirect()->route('salesorder.index');
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
               
        $this->salesOrderResult->status = 'rejected'; 
        $this->salesOrderResult->approver = Auth::user()->id; 
        $this->salesOrderResult->save();
       
        session()->flash('message', 'Sales Order rejected successfully.');
        return redirect()->route('salesorder.index');
    }



    public function startEditing($itemId)
    {
        Log::info('Starting edit for item', ['itemId' => $itemId]);
        $this->editingItems[$itemId] = true;
        $item = \App\Models\SalesOrderBranchItem::find($itemId);
        if ($item) {
            $this->editablePrices[$itemId] = (float) $item->unit_price;
            Log::info('Item found and price set', ['itemId' => $itemId, 'price' => $item->unit_price]);
        } else {
            Log::warning('Item not found', ['itemId' => $itemId]);
        }
        $this->dispatch('$refresh');
    }

    public function savePrice($itemId)
    {
        Log::info('Saving price for item', ['itemId' => $itemId, 'price' => $this->editablePrices[$itemId] ?? 'not set']);

        if (isset($this->editablePrices[$itemId])) {
            $item = \App\Models\SalesOrderBranchItem::find($itemId);
            if ($item) {
                $newPrice = (float) $this->editablePrices[$itemId];
                $item->update([
                    'unit_price' => $newPrice,
                    'subtotal' => $item->quantity * $newPrice,
                ]);
                Log::info('Price updated successfully', ['itemId' => $itemId, 'newPrice' => $newPrice]);
                session()->flash('message', 'Price updated successfully.');
            } else {
                Log::warning('Item not found during save', ['itemId' => $itemId]);
            }
        } else {
            Log::warning('No editable price found', ['itemId' => $itemId]);
        }

        // Clean up
        unset($this->editingItems[$itemId]);
        unset($this->editablePrices[$itemId]);

        $this->dispatch('$refresh');
    }

    public function cancelEditing($itemId)
    {
        Log::info('Cancelling edit for item', ['itemId' => $itemId]);
        unset($this->editingItems[$itemId]);
        unset($this->editablePrices[$itemId]);
        $this->dispatch('$refresh');
    }

    public function render()
    {
        $salesOrder = SalesOrder::with(['customers', 'branchItems.product'])->find($this->salesOrderId);

        return view('livewire.pages.sales-management.view', [
            'sales_order_view' => $salesOrder,
        ]);
    }
}
