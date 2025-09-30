<?php

namespace App\Livewire\Pages\SalesManagement;

use App\Enums\Enum\PermissionEnum;
use App\Enums\RolesEnum;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\SalesOrder;
use App\Models\SalesReturn;
use Illuminate\Support\Facades\Auth;

class ViewSalesReturn extends Component
{

    public $salesReturnId;
    public $salesReturnResult;
    public $company_results;
    public $product_results;
    
    public function mount($salesreturnId)
    {
        $this->salesReturnId = $salesreturnId;
        $this->salesReturnResult = SalesReturn::with('salesorder')->find($salesreturnId);

        $this->company_results  = \App\Models\Customer::all()->pluck('name', 'id');
        $this->product_results  = \App\Models\SupplyProfile::all()->pluck('supply_description', 'id');
       
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

        $this->salesReturnResult->status = 'approved'; 
        $this->salesReturnResult->processed_by = Auth::user()->id; 
        $this->salesReturnResult->save();

        session()->flash('message', 'Sales order approved successfully.');
        return redirect()->route('salesorder.return');
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
               
        $this->salesReturnResult->status = 'rejected'; 
        $this->salesReturnResult->processed_by = Auth::user()->id; 
        $this->salesReturnResult->save();
       
        session()->flash('message', 'Sales Order rejected successfully.');
        return redirect()->route('salesorder.return');
    }

    public function render()
    {
        $result = \App\Models\User::find($this->salesReturnResult->processed_by);

        if($result){
            $username = $result->email;
        }else{
            $username = '';
        }
       
        return view('livewire.pages.sales-management.sales-return-view', [
            'sales_return_view' => SalesReturn::find($this->salesReturnId),
            'userName' => $username
        ]);
    }
}
