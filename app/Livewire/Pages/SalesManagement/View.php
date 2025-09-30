<?php

namespace App\Livewire\Pages\SalesManagement;

use App\Enums\Enum\PermissionEnum;
use App\Enums\RolesEnum;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\Auth;

class View extends Component
{

    public $salesOrderId;
    public $salesOrderResult;
    public $company_results;
    public $product_results;
    public $shippingMethodDropDown = [];
    public $paymentMethodDropdown = []; 
    public $paymentTermsDropdown = [];
    
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

    public function render()
    {
        $results =  SalesOrder::with('items.product')->find($this->salesOrderId);
        
        

        return view('livewire.pages.sales-management.view', [
            'sales_order_view' => SalesOrder::with('items.product')->find($this->salesOrderId),
        ]);
    }
}
