<?php

namespace App\Livewire\Pages\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Finance;
use Illuminate\Support\Facades\Storage;

class Expenses extends Component
{
    use WithPagination, WithFileUploads;

    public $reference_id;
    public $party;
    public $date;
    public $category;
    public $amount;
    public $payment_method;
    public $status = 'pending';
    public $remarks;
    public $file;
    public $search = '';
    public $perPage = 10;
    public $editingExpenseId = null;
    public $showDeleteModal = false;
    public $expenseToDelete = null;
    public $showCreatePanel = false;
    public $showReceiptModal = false;
    public $currentReceipt = null;
    public $categoryFilter = '';
    public $startDate = '';
    public $endDate = '';

    public function openCreatePanel()
    {
        $this->showCreatePanel = true;
        $this->resetForm();
        $this->generateReferenceId();
    }

    public function closeCreatePanel()
    {
        $this->showCreatePanel = false;
        $this->editingExpenseId = null;
        $this->resetForm();
    }

    protected $rules = [
        'reference_id' => 'nullable|string|max:255',
        'party' => 'required|string|max:255',
        'date' => 'required|date',
        'category' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'payment_method' => 'required|string|max:255',
        'status' => 'required|string|max:255',
        'remarks' => 'nullable|string',
        'file' => 'nullable|file|max:10240', // 10MB max
    ];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $this->generateReferenceId();
    }

    private function generateReferenceId()
    {
        $date = now()->format('ymd');
        $prefix = 'EXP' . $date;

        $latest = Finance::where('reference_id', 'like', $prefix . '%')
                        ->orderByDesc('reference_id')
                        ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->reference_id, -3);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        $this->reference_id = $prefix . $nextNumber;
    }

    public function resetForm()
    {
        $this->reset(['party', 'date', 'category', 'amount', 'payment_method', 'remarks', 'file']);
        $this->status = 'pending';
        $this->date = now()->format('Y-m-d');
        $this->generateReferenceId();
    }

    public function save()
    {
        $this->validate();

        $filePath = null;
        if ($this->file) {
            $filePath = $this->file->store('expense-receipts', 'public');
        }

        Finance::create([
            'type' => 'expense',
            'reference_id' => $this->reference_id,
            'party' => $this->party,
            'date' => $this->date,
            'category' => $this->category,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'file_path' => $filePath,
        ]);

        session()->flash('success', 'Expense saved successfully!');
        $this->closeCreatePanel();
    }

    public function edit($id)
    {
        $expense = Finance::findOrFail($id);
        $this->editingExpenseId = $expense->id;
        $this->reference_id = $expense->reference_id;
        $this->party = $expense->party;
        $this->date = $expense->date;
        $this->category = $expense->category;
        $this->amount = $expense->amount;
        $this->payment_method = $expense->payment_method;
        $this->status = $expense->status;
        $this->remarks = $expense->remarks;
        $this->showCreatePanel = true;
    }

    public function update()
    {
        $this->validate();
        
        $expense = Finance::findOrFail($this->editingExpenseId);
        
        $filePath = $expense->file_path;
        if ($this->file) {
            // Delete old file if exists
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $this->file->store('expense-receipts', 'public');
        }

        $expense->update([
            'type' => 'expense',
            'reference_id' => $this->reference_id,
            'party' => $this->party,
            'date' => $this->date,
            'category' => $this->category,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'file_path' => $filePath,
        ]);

        session()->flash('success', 'Expense updated successfully!');
        $this->closeCreatePanel();
    }

    public function confirmDelete($id)
    {
        $this->expenseToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $expense = Finance::findOrFail($this->expenseToDelete);
        
        // Delete associated file if exists
        if ($expense->file_path && Storage::disk('public')->exists($expense->file_path)) {
            Storage::disk('public')->delete($expense->file_path);
        }
        
        $expense->delete();
        
        session()->flash('success', 'Expense deleted successfully!');
        $this->closeDeleteModal();
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->expenseToDelete = null;
    }

    // Receipt viewing methods
    public function viewReceipt($expenseId)
    {
        $expense = Finance::findOrFail($expenseId);
        
        if (!$expense->file_path || !Storage::disk('public')->exists($expense->file_path)) {
            session()->flash('error', 'Receipt file not found.');
            return;
        }
        
        $this->currentReceipt = [
            'expense' => $expense,
            'file_url' => Storage::disk('public')->url($expense->file_path),
            'file_name' => basename($expense->file_path),
            'file_extension' => pathinfo($expense->file_path, PATHINFO_EXTENSION),
        ];
        
        $this->showReceiptModal = true;
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->currentReceipt = null;
    }

    public function downloadReceipt($expenseId)
    {
        $expense = Finance::findOrFail($expenseId);
        
        if (!$expense->file_path || !Storage::disk('public')->exists($expense->file_path)) {
            session()->flash('error', 'Receipt file not found.');
            return;
        }
        
        return Storage::disk('public')->download($expense->file_path);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'categoryFilter', 'startDate', 'endDate']);
        $this->resetPage();
    }

    public function getStatsProperty()
    {
        $baseQuery = Finance::where('type', 'expense');

        return [
            'total' => $baseQuery->sum('amount'),
            'this_month' => $baseQuery->whereYear('date', now()->year)
                            ->whereMonth('date', now()->month)
                            ->sum('amount'),
            'pending' => $baseQuery->where('status', 'pending')->sum('amount'),
            'categories' => $baseQuery->distinct('category')->count('category'),
        ];
    }

    public function render()
    {
        $expenses = Finance::query()
            ->where('type', 'expense')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('reference_id', 'like', "%{$this->search}%")
                      ->orWhere('party', 'like', "%{$this->search}%")
                      ->orWhere('category', 'like', "%{$this->search}%")
                      ->orWhere('payment_method', 'like', "%{$this->search}%")
                      ->orWhere('status', 'like', "%{$this->search}%")
                      ->orWhere('remarks', 'like', "%{$this->search}%");
                });
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category', $this->categoryFilter);
            })
            ->when($this->startDate, function ($query) {
                $query->where('date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->where('date', '<=', $this->endDate);
            })
            ->orderByDesc('date')
            ->paginate($this->perPage);

        return view('livewire.pages.finance.expenses', [
            'expenses' => $expenses,
            'stats' => $this->stats,
        ]);
    }
}