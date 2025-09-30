<?php

namespace App\Livewire\Pages\Supplies\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SupplyProfile;
use App\Models\SupplyBatch;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StockBatches extends Component
{
    use WithPagination;

    public $supply;
    public $search = '';
    public $statusFilter = '';
    public $expiryFilter = '';
    public $showBatchModal = false;
    public $editingBatch = null;
    
    // Form properties
    public $batch_number = '';
    public $expiration_date = '';
    public $manufactured_date = '';
    public $initial_qty = '';
    public $current_qty = '';
    public $location = '';
    public $notes = '';
    public $status = 'active';

    protected $rules = [
        'batch_number' => 'required|string|max:100',
        'expiration_date' => 'nullable|date|after:today',
        'manufactured_date' => 'nullable|date|before_or_equal:today',
        'initial_qty' => 'required|numeric|min:0',
        'current_qty' => 'required|numeric|min:0',
        'location' => 'nullable|string|max:100',
        'notes' => 'nullable|string|max:500',
        'status' => 'required|in:active,expired,depleted,quarantined',
    ];

    public function mount($supply)
    {
        $this->supply = SupplyProfile::with(['itemType', 'allocation'])->findOrFail($supply);
        
        // Check if not a consumable item and redirect
        if (!$this->supply->isConsumable()) {
            session()->flash('error', 'Batch tracking is only available for consumable products.');
            return $this->redirect(route('supplies.inventory'));
        }
        
        // Set default values for form
        $this->manufactured_date = Carbon::now()->toDateString();
        $this->location = 'Warehouse A';
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->batch_number = SupplyBatch::generateBatchNumber($this->supply->id);
        $this->showBatchModal = true;
    }

    public function editBatch($id)
    {
        $batch = SupplyBatch::findOrFail($id);
        $this->editingBatch = $batch;
        
        $this->batch_number = $batch->batch_number;
        $this->expiration_date = $batch->expiration_date ? $batch->expiration_date->toDateString() : '';
        $this->manufactured_date = $batch->manufactured_date ? $batch->manufactured_date->toDateString() : '';
        $this->initial_qty = $batch->initial_qty;
        $this->current_qty = $batch->current_qty;
        $this->location = $batch->location;
        $this->notes = $batch->notes;
        $this->status = $batch->status;
        
        $this->showBatchModal = true;
    }

    public function saveBatch()
    {
        $this->validate();

        if ($this->editingBatch) {
            // Update existing batch
            $this->editingBatch->update([
                'batch_number' => $this->batch_number,
                'expiration_date' => $this->expiration_date ?: null,
                'manufactured_date' => $this->manufactured_date ?: null,
                'initial_qty' => $this->initial_qty,
                'current_qty' => $this->current_qty,
                'location' => $this->location,
                'notes' => $this->notes,
                'status' => $this->status,
            ]);

            session()->flash('message', 'Batch updated successfully.');
        } else {
            // Create new batch
            SupplyBatch::create([
                'supply_profile_id' => $this->supply->id,
                'batch_number' => $this->batch_number,
                'expiration_date' => $this->expiration_date ?: null,
                'manufactured_date' => $this->manufactured_date ?: null,
                'initial_qty' => $this->initial_qty,
                'current_qty' => $this->current_qty,
                'location' => $this->location,
                'notes' => $this->notes,
                'status' => $this->status,
                'received_date' => Carbon::now()->toDateString(),
                'received_by' => Auth::id(),
            ]);

            session()->flash('message', 'Batch created successfully.');
        }

        $this->closeModal();
    }

    public function deleteBatch($id)
    {
        $batch = SupplyBatch::findOrFail($id);
        $batch->delete();
        
        session()->flash('message', 'Batch deleted successfully.');
    }

    public function closeModal()
    {
        $this->showBatchModal = false;
        $this->editingBatch = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->batch_number = '';
        $this->expiration_date = '';
        $this->manufactured_date = Carbon::now()->toDateString();
        $this->initial_qty = '';
        $this->current_qty = '';
        $this->location = 'Warehouse A';
        $this->notes = '';
        $this->status = 'active';
    }

    public function generateNewBatchNumber()
    {
        $this->batch_number = SupplyBatch::generateBatchNumber($this->supply->id);
    }

    public function render()
    {
        $query = SupplyBatch::query()
            ->where('supply_profile_id', $this->supply->id)
            ->with(['receivedBy']);

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('batch_number', 'like', '%' . $this->search . '%')
                  ->orWhere('notes', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Apply expiry filter
        if ($this->expiryFilter === 'expiring_soon') {
            $query->expiringSoon(30);
        } elseif ($this->expiryFilter === 'expired') {
            $query->expired();
        }

        $batches = $query->orderBy('expiration_date', 'asc')
                        ->orderBy('received_date', 'desc')
                        ->paginate(10);

        return view('livewire.pages.supplies.inventory.stock-batches', [
            'supply' => $this->supply,
            'batches' => $batches,
        ]);
    }
}
