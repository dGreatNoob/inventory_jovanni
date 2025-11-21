<?php

namespace App\Livewire\Pages\Requisition\RequestSlip;


use App\Models\Department;
use App\Enums\Enum\PermissionEnum;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\RequestSlip;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use App\Events\RequestSlipCreated;
#[
    Layout('components.layouts.app'),
    Title('Request Slip')
]
class Index extends Component
{

    use WithPagination;

    // #[Validate('required|in:pending,approved,rejected')]    
    public $status = 'pending';

    #[Validate('required|in:Stock Replenishment,New Product Launch,Store Transfer,Return/Exchange,Quality Control,Display Materials,Consignment Adjustment,Inventory Audit,Store Setup,Seasonal Collection,Other')]
    public $purpose = '';

    #[Validate('required|string|max:255')]
    public $description = '';


    public $sent_from;

    #[Validate('required|exists:departments,id')]
    public $sent_to = '';

    // #[Validate('required|string|max:255')]
    public $requested_by = '';

    public $approver = '';
    public $request_date = '';

    #[Url(as: 'q')]
    public $search = '';
    public int $perPage = 10;

    public $showRequestSlipPanel = false;

    public array $purposes = [
        'Stock Replenishment' => 'Request stock replenishment for SM stores',
        'New Product Launch' => 'Request new bag collection for store launch',
        'Store Transfer' => 'Transfer bags between SM store locations',
        'Return/Exchange' => 'Return or exchange defective/damaged items',
        'Quality Control' => 'Request items for quality inspection',
        'Display Materials' => 'Request display fixtures and marketing materials',
        'Consignment Adjustment' => 'Adjust consignment inventory levels',
        'Inventory Audit' => 'Request for inventory counting and audit',
        'Store Setup' => 'Request bags for new store opening',
        'Seasonal Collection' => 'Request seasonal bag collection',
        'Other' => 'Other request purposes',
    ];

    public $purposeFilter = '';

    public function mount()
    {
        // Get user's department name safely
        $user = Auth::user();
        $department = $user->department;
        
        if ($department) {
            $this->sent_from = $department->name;
        } else {
            // Fallback if user has no department
            $this->sent_from = 'Unknown Department';
        }
    }


    #[Computed()]
    public function departments()
    {
        
        return Department::orderBy('name')->get();
    }

    public function create()
    {
        $this->validate();

        // Get user and check department
        $user = Auth::user();
        $department = $user->department;
        
        if (!$department) {
            session()->flash('error', 'User must be assigned to a department to create request slips.');
            return;
        }

        RequestSlip::create([
            'status' => 'pending',
            'purpose' => $this->purpose,
            'description' => $this->description,
            'sent_from' => $department->id,
            'sent_to' => $this->sent_to,
            'request_date' => now(),
            'requested_by' => $user->id,
        ]);
        
        // Temporarily disable broadcasting to fix 500 error
        // broadcast(new RequestSlipCreated(RequestSlip::latest()->first()->id))->toOthers();

        session()->flash('message', 'Request created successfully.');
        $this->closeRequestSlipPanel();
        $this->resetPage();
    }

    public function closeRequestSlipPanel()
    {
        $this->showRequestSlipPanel = false;
        $this->purpose = array_key_first($this->purposes);
        $this->description = '';
        $this->sent_to = Department::orderBy('name')->first()?->id ?? '';
        $this->reset('request_date');
    }

    public function delete($id)
    {
        if (!Auth::user()->can(PermissionEnum::DELETE_REQUEST_SLIP->value)) {
            abort(403, 'You do not have permission to delete this request slip.');
        }

        $requestSlip = RequestSlip::find($id);
        if ($requestSlip) {
            $requestSlip->delete();
            session()->flash('message', 'Request Slip deleted successfully.');
        } else {
            session()->flash('error', 'Request Slip not found.');
        }
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingPurposeFilter()
    {
        $this->resetPage();
    }
    // public function updatingPerPage()
    // {
    //     $this->resetPage();
    // }
    #[On('echo:request-slip,RequestSlipCreated')]
    public function onRequestSlipCreated($event)
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = RequestSlip::search($this->search);
        
        // Apply purpose filter if selected
        if (!empty($this->purposeFilter)) {
            $query->where('purpose', $this->purposeFilter);
        }
        
        return view('livewire.pages.requisition.request-slip.index', [
            'request_slips' => $query->latest()->paginate($this->perPage),
        ]);
    }
}
