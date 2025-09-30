<?php

namespace App\Livewire\Pages\Requisition\RequestSlip;

use App\Enums\Enum\PermissionEnum;
use App\Enums\RolesEnum;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\RequestSlip;
use Illuminate\Support\Facades\Auth;



#[
    Layout('components.layouts.app'),
    Title('View Request Slip')
]
class View extends Component
{

    public $request_slip_id;

    public $request_slip;

    public function mount($request_slip_id)
    {
        $this->request_slip_id = $request_slip_id;
        $this->request_slip = RequestSlip::findOrFail($request_slip_id);
    }


    public function ApproveRequestSlip()
    {
        if (!Auth::user()->can(PermissionEnum::APPROVE_REQUEST_SLIP->value)) {

            abort(403, 'You do not have permission to approve this request slip.');

        } else {
            $this->request_slip->update([
                'status' => 'approved',
                'approver' => Auth::user()->id,

            ]);
            session()->flash('message', 'Request Slip approved successfully.');
            return redirect()->route('requisition.requestslip');
        }

    }


    public function RejectRequestSlip()
    {
        if (!Auth::user()->can(PermissionEnum::APPROVE_REQUEST_SLIP->value)) {

            abort(403, 'You do not have permission to approve this request slip.');

        } else {
            $this->request_slip->update([
                'status' => 'rejected',
                'approver' => Auth::user()->id,

            ]);
            session()->flash('message', 'Request Slip Rejected.');
            return redirect()->route('requisition.requestslip');
        }



    }

    public function render()
    {
        return view('livewire.pages.requisition.request-slip.view', [
            'request_slip' => RequestSlip::find($this->request_slip_id),
        ]);
    }
}
