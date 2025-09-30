<?php

namespace App\Livewire\Pages\Finance;

use Livewire\Component;
use App\Models\Finance;

class Receivables extends Component
{
    public $reference_id;
    public $customer; // <-- ADDED FOR RECEIVABLES
    public $sales_order; // <-- ADDED FOR RECEIVABLES
    public $party;
    public $date;
    public $due_date;
    public $amount;
    public $payment_method;
    public $status;
    public $remarks;

    public $search = '';
    public $perPage = 10;
    public $editingReceivableId = null;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $receivableToDelete = null;
    public $deleteReferenceId; // <-- For showing invoice number in delete modal

    protected $rules = [
        'reference_id' => 'nullable|string|max:255',
        'customer' => 'nullable|string|max:255', // <-- ADDED FOR RECEIVABLES
        'sales_order' => 'nullable|string|max:255', // <-- ADDED FOR RECEIVABLES
        'party' => 'nullable|string|max:255',
        'date' => 'required|date',
        'due_date' => 'nullable|date',
        'amount' => 'required|numeric|min:0',
        'payment_method' => 'required|string|max:255',
        'status' => 'nullable|string|max:255', // <-- Make status optional
        'remarks' => 'nullable|string',
    ];

    public function mount()
    {
        $this->generateReferenceId();
    }

    private function generateReferenceId()
    {
        $date = now()->format('ymd'); // e.g. 250721
        $prefix = 'REC' . $date;

        // Find the latest reference ID for receivables for today only
        $latest = Finance::where('reference_id', 'like', $prefix . '%')
                        ->orderByDesc('reference_id')
                        ->first();

        if ($latest) {
            // Extract the last 3-digit sequence
            $lastNumber = (int) substr($latest->reference_id, -3);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        $this->reference_id = $prefix . $nextNumber;
    }

    public function save()
    {
        $this->validate();
        Finance::create([
            'type' => 'receivable',
            'reference_id' => $this->reference_id,
            'customer' => $this->customer, // <-- ADDED FOR RECEIVABLES
            'sales_order' => $this->sales_order, // <-- ADDED FOR RECEIVABLES
            'party' => $this->party,
            'date' => $this->date,
            'due_date' => $this->due_date,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'status' => 'pending', // <-- Always set to pending on create
            'remarks' => $this->remarks,
        ]);
        session()->flash('success', 'Receivable saved successfully!');
        $this->reset(['reference_id', 'customer', 'sales_order', 'party', 'date', 'due_date', 'amount', 'payment_method', 'status', 'remarks']);
        $this->generateReferenceId(); // <-- Only generate after saving a new record
    }

    public function edit($id)
    {
        $receivable = Finance::findOrFail($id);
        $this->editingReceivableId = $receivable->id;
        $this->type = 'receivable';
        $this->reference_id = $receivable->reference_id;
        $this->customer = $receivable->customer; // <-- ADDED FOR RECEIVABLES
        $this->sales_order = $receivable->sales_order; // <-- ADDED FOR RECEIVABLES
        $this->party = $receivable->party;
        $this->date = $receivable->date;
        $this->due_date = $receivable->due_date;
        $this->amount = $receivable->amount;
        $this->payment_method = $receivable->payment_method;
        $this->status = $receivable->status;
        $this->remarks = $receivable->remarks;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate();
        $receivable = Finance::findOrFail($this->editingReceivableId);
        $receivable->update([
            'type' => 'receivable',
            'reference_id' => $this->reference_id,
            'customer' => $this->customer, // <-- ADDED FOR RECEIVABLES
            'sales_order' => $this->sales_order, // <-- ADDED FOR RECEIVABLES
            'party' => $this->party,
            'date' => $this->date,
            'due_date' => $this->due_date,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'remarks' => $this->remarks,
        ]);
        session()->flash('success', 'Receivable updated successfully!');
        $this->resetEditState();
        $this->generateReferenceId(); // <-- Generate new invoice number after updating
    }

    public function cancel()
    {
        $this->resetEditState();
        $this->generateReferenceId(); // <-- Generate new invoice number after closing edit modal
    }

    private function resetEditState()
    {
        $this->editingReceivableId = null;
        $this->showEditModal = false;
        $this->reset(['customer', 'sales_order', 'party', 'date', 'due_date', 'amount', 'payment_method', 'status', 'remarks']); // <-- Do NOT reset reference_id here
        // Do not generateReferenceId here, only after cancel or update
    }

    public function confirmDelete($id)
    {
        $receivable = Finance::findOrFail($id);
        $this->receivableToDelete = $id;
        $this->deleteReferenceId = $receivable->reference_id; // <-- Store invoice number for delete modal
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $receivable = Finance::findOrFail($this->receivableToDelete);
        $receivable->delete();
        session()->flash('success', 'Receivable deleted successfully!');
        $this->showDeleteModal = false;
        $this->receivableToDelete = null;
        $this->deleteReferenceId = null; // <-- Reset after delete
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->receivableToDelete = null;
        $this->deleteReferenceId = null; // <-- Reset after close
    }

    public function render()
    {
        $receivables = Finance::query()
            ->where('type', 'receivable')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('reference_id', 'like', "%{$this->search}%")
                        ->orWhere('party', 'like', "%{$this->search}%")
                        ->orWhere('payment_method', 'like', "%{$this->search}%")
                        ->orWhere('status', 'like', "%{$this->search}%")
                        ->orWhere('remarks', 'like', "%{$this->search}%");
                });
            })
            ->orderByDesc('date')
            ->paginate($this->perPage);

        return view('livewire.pages.finance.receivables', [
            'receivables' => $receivables,
        ]);
    }
} 