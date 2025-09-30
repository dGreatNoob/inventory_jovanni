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

    #[Validate('required|in:Pet Food,Pet Toys,Pet Care,Pet Health,Pet Grooming,Pet Bedding,Pet Training,Pet Safety,Office Supplies,Packaging,Equipment,Other')]
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

    public $showCreateModal = false;

    public array $purposes = [
        'Pet Food' => 'Request Pet Food Supplies',
        'Pet Toys' => 'Request Pet Toys & Accessories',
        'Pet Care' => 'Request Pet Care Products',
        'Pet Health' => 'Request Pet Health & Medical Supplies',
        'Pet Grooming' => 'Request Pet Grooming Supplies',
        'Pet Bedding' => 'Request Pet Bedding & Comfort Items',
        'Pet Training' => 'Request Pet Training Supplies',
        'Pet Safety' => 'Request Pet Safety & Security Items',
        'Office Supplies' => 'Request Office Supplies',
        'Packaging' => 'Request Packaging Materials',
        'Equipment' => 'Request Equipment & Tools',
        'Other' => 'Request Other Items',
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
        $this->showCreateModal = false;
        $this->purpose = array_key_first($this->purposes);
        $this->description = '';
        $this->sent_to = Department::orderBy('name')->first()?->id ?? '';
        $this->reset('request_date');
        $this->resetPage();
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
