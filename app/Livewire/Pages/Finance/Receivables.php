<?php

namespace App\Livewire\Pages\Finance;

use Livewire\Component;
use App\Models\Finance;
use App\Models\Branch;
use App\Models\Agent;
use App\Models\SalesReceipt;

class Receivables extends Component
{
    public $reference_id;
    public $branch_id;
    public $amount_due;
    public $due_date;
    public $payment_status;
    public $remarks;

    // Agent data (for display only, not for user selection)
    public $availableAgents = [];

    public $search = '';
    public $perPage = 10;
    public $editingReceivableId = null;
    public $showCreatePanel = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $receivableToDelete = null;
    public $deleteReferenceId; // <-- For showing invoice number in delete modal

    // For inline status updates
    public $statusUpdates = [];

    // Filters
    public $filter_status = '';
    public $filter_due_date_from = '';
    public $filter_due_date_to = '';
    public $filter_branch = '';
    public $filter_agent = '';

    protected $rules = [
        'reference_id' => 'nullable|string|max:255',
        'branch_id' => 'nullable|exists:branches,id',
        'amount_due' => 'required|numeric|min:0.01',
        'due_date' => 'nullable|date',
        'payment_status' => 'nullable|string|in:pending,paid,overdue,cancelled',
        'remarks' => 'nullable|string',
    ];

    public function mount()
    {
        $this->generateReferenceId();
        // Initialize available agents as empty collection
        $this->availableAgents = collect();
    }

    public function openCreatePanel()
    {
        $this->showCreatePanel = true;
        $this->resetForm();
        $this->generateReferenceId();
    }

    public function closeCreatePanel()
    {
        $this->showCreatePanel = false;
        $this->editingReceivableId = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['reference_id', 'branch_id', 'amount_due', 'due_date', 'payment_status', 'remarks']);
        $this->availableAgents = collect();
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

    private function loadAvailableAgents()
    {
        if ($this->branch_id) {
            $branch = Branch::find($this->branch_id);
            $this->availableAgents = $branch ? $branch->currentAgents : collect();
        } else {
            $this->availableAgents = collect();
        }
    }

    public function updatedBranchId()
    {
        // Reset amount due when branch changes
        $this->amount_due = null;

        // Load available agents for the selected branch - this will trigger reactivity
        $this->loadAvailableAgents();
    }

    private function autoPopulateAmountDue()
    {
        if (!$this->branch_id || $this->amount_due) {
            // Don't auto-populate if amount is already set or no branch selected
            return;
        }

        // Find the latest sales receipt for this branch with 'received' status
        $salesReceipt = SalesReceipt::where('branch_id', $this->branch_id)
            ->where('status', 'received')
            ->with('items.product')
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($salesReceipt) {
            $totalAmount = 0;
            foreach ($salesReceipt->items as $item) {
                $quantity = $item->received_qty ?? 0;
                $unitPrice = $item->product->price ?? 0;
                $totalAmount += $quantity * $unitPrice;
            }
            $this->amount_due = $totalAmount;
        }
    }

    public function save()
    {
        $this->payment_status = 'pending';
        $this->validate();

        // Get the assigned agent for the selected branch
        $assignedAgentId = null;
        if ($this->branch_id) {
            $branch = Branch::find($this->branch_id);
            $currentAgents = $branch ? $branch->currentAgents : collect();
            $assignedAgentId = $currentAgents->first()?->id;
        }

        Finance::create([
            'type' => 'receivable',
            'reference_id' => $this->reference_id,
            'branch_id' => $this->branch_id,
            'agent_id' => $assignedAgentId,
            'date' => now()->toDateString(),
            'amount' => $this->amount_due,
            'balance' => $this->amount_due,
            'due_date' => $this->due_date,
            'payment_method' => 'N/A', // Not used in new functionality
            'status' => $this->payment_status,
            'remarks' => $this->remarks,
        ]);
        session()->flash('success', 'Receivable saved successfully!');
        $this->closeCreatePanel();
        $this->generateReferenceId(); // <-- Only generate after saving a new record
    }

    public function edit($id)
    {
        $receivable = Finance::findOrFail($id);
        $this->editingReceivableId = $receivable->id;
        $this->reference_id = $receivable->reference_id;
        $this->branch_id = $receivable->branch_id;
        $this->amount_due = $receivable->amount;
        $this->due_date = $receivable->due_date;
        $this->payment_status = $receivable->status;
        $this->remarks = $receivable->remarks;

        // Load available agents for the selected branch
        if ($this->branch_id) {
            $this->loadAvailableAgents();
        }

        $this->showCreatePanel = true;
    }

    public function update()
    {
        $this->validate();
        
        // Get the assigned agent for the selected branch
        $assignedAgentId = null;
        if ($this->branch_id) {
            $branch = Branch::find($this->branch_id);
            $currentAgents = $branch ? $branch->currentAgents : collect();
            $assignedAgentId = $currentAgents->first()?->id;
        }
        
        $receivable = Finance::findOrFail($this->editingReceivableId);
        $receivable->update([
            'reference_id' => $this->reference_id,
            'branch_id' => $this->branch_id,
            'agent_id' => $assignedAgentId,
            'amount' => $this->amount_due,
            'balance' => $this->amount_due,
            'due_date' => $this->due_date,
            'payment_method' => 'N/A', // Not used in new functionality
            'status' => $this->payment_status,
            'remarks' => $this->remarks,
        ]);
        session()->flash('success', 'Receivable updated successfully!');
        $this->closeCreatePanel();
        $this->generateReferenceId(); // <-- Generate new invoice number after updating
    }

    public function cancel()
    {
        $this->closeCreatePanel();
        $this->generateReferenceId(); // <-- Generate new invoice number after closing edit modal
    }

    private function resetEditState()
    {
        $this->editingReceivableId = null;
        $this->showCreatePanel = false;
        $this->availableAgents = collect();
        $this->reset(['branch_id', 'amount_due', 'due_date', 'payment_status', 'remarks']); // <-- Do NOT reset reference_id here
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

    public function updateReceivableStatus($id)
    {
        $receivable = Finance::findOrFail($id);
        $newStatus = $this->statusUpdates[$id];
        $receivable->status = $newStatus;

        // Adjust balance based on status
        if ($newStatus === 'paid') {
            $receivable->balance = 0;
        } elseif ($newStatus === 'pending') {
            $receivable->balance = $receivable->amount;
        } elseif ($newStatus === 'cancelled') {
            $receivable->balance = 0;
        }
        // For overdue, keep current balance

        $receivable->save();
        session()->flash('success', 'Status updated successfully!');
    }

    public function render()
    {
        // Auto-update overdue statuses
        Finance::where('type', 'receivable')
            ->where('due_date', '<', now()->toDateString())
            ->whereNotIn('status', ['paid', 'cancelled', 'overdue'])
            ->update(['status' => 'overdue']);

        // Load available agents dynamically based on selected branch
        if ($this->branch_id) {
            $branch = Branch::find($this->branch_id);
            $this->availableAgents = $branch ? $branch->currentAgents : collect();
        } else {
            $this->availableAgents = collect();
        }

        $receivables = Finance::query()
            ->with(['branch', 'agent'])
            ->where('type', 'receivable')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('reference_id', 'like', "%{$this->search}%")
                        ->orWhere('remarks', 'like', "%{$this->search}%")
                        ->orWhereHas('branch', function ($branchQuery) {
                            $branchQuery->where('name', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('agent', function ($agentQuery) {
                            $agentQuery->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->filter_status, function ($query) {
                $query->where('status', $this->filter_status);
            })
            ->when($this->filter_due_date_from, function ($query) {
                $query->where('due_date', '>=', $this->filter_due_date_from);
            })
            ->when($this->filter_due_date_to, function ($query) {
                $query->where('due_date', '<=', $this->filter_due_date_to);
            })
            ->when($this->filter_branch, function ($query) {
                $query->where('branch_id', $this->filter_branch);
            })
            ->when($this->filter_agent, function ($query) {
                $query->where('agent_id', $this->filter_agent);
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        // Initialize status updates for inline editing
        foreach ($receivables as $receivable) {
            $this->statusUpdates[$receivable->id] = $receivable->status;
        }

        return view('livewire.pages.finance.receivables', [
            'receivables' => $receivables,
        ]);
    }
} 