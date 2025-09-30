<?php

namespace App\Livewire\Pages\Finance;

use Livewire\Component;
use App\Models\Finance;

class Expenses extends Component
{
    public $reference_id;
    public $party;
    public $date;
    public $category;
    public $amount;
    public $payment_method;
    public $status = 'pending';
    public $remarks;
    public $search = '';
    public $perPage = 10;
    public $editingExpenseId = null;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $expenseToDelete = null;

    protected $rules = [
        'reference_id' => 'nullable|string|max:255',
        'party' => 'nullable|string|max:255',
        'date' => 'required|date',
        'category' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'payment_method' => 'required|string|max:255',
        'status' => 'required|string|max:255',
        'remarks' => 'nullable|string',
    ];

    public function mount()
    {
        $this->generateReferenceId();
    }

    private function generateReferenceId()
    {
        $date = now()->format('ymd'); // e.g. 250721
        $prefix = 'EXP' . $date;

        // Find the latest reference ID for expenses for today only
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
        \App\Models\Finance::create([
            'type' => 'expense',
            'reference_id' => $this->reference_id,
            'party' => $this->party,
            'date' => $this->date,
            'category' => $this->category,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'status' => 'pending',
            'remarks' => $this->remarks,
        ]);
        session()->flash('success', 'Expense saved successfully!');
        $this->reset(['party', 'date', 'category', 'amount', 'payment_method', 'remarks']);
        $this->status = 'pending';
        $this->generateReferenceId();
    }

    public function edit($id)
    {
        $expense = \App\Models\Finance::findOrFail($id);
        $this->editingExpenseId = $expense->id;
        $this->type = 'expense';
        $this->reference_id = $expense->reference_id;
        $this->party = $expense->party;
        $this->date = $expense->date;
        $this->category = $expense->category;
        $this->amount = $expense->amount;
        $this->payment_method = $expense->payment_method;
        $this->status = $expense->status;
        $this->remarks = $expense->remarks;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate();
        $expense = \App\Models\Finance::findOrFail($this->editingExpenseId);
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
        ]);
        session()->flash('success', 'Expense updated successfully!');
        $this->resetEditState();
        $this->generateReferenceId();
    }

    public function cancel()
    {
        $this->resetEditState();
        $this->closeDeleteModal();
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->expenseToDelete = null;
    }

    private function resetEditState()
    {
        $this->editingExpenseId = null;
        $this->showEditModal = false;
        $this->reset(['party', 'date', 'category', 'amount', 'payment_method', 'remarks']);
        $this->status = 'pending';
        $this->generateReferenceId();
    }

    public function confirmDelete($id)
    {
        $this->expenseToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $expense = \App\Models\Finance::findOrFail($this->expenseToDelete);
        $expense->delete();
        session()->flash('success', 'Expense deleted successfully!');
        $this->closeDeleteModal();
    }

    public function getDashboardStatsProperty()
    {
        $baseQuery = Finance::where('type', 'expense');
        
        $totalExpenses = $baseQuery->count();
        $pendingExpenses = $baseQuery->where('status', 'pending')->count();
        $paidExpenses = $baseQuery->where('status', 'paid')->count();
        $totalAmount = $baseQuery->sum('amount');
        $thisMonthAmount = $baseQuery->whereMonth('date', now()->month)
                                   ->whereYear('date', now()->year)
                                   ->sum('amount');
        $lastMonthAmount = $baseQuery->whereMonth('date', now()->subMonth()->month)
                                   ->whereYear('date', now()->subMonth()->year)
                                   ->sum('amount');
                                   
        $changePercent = $lastMonthAmount > 0 ? 
            round((($thisMonthAmount - $lastMonthAmount) / $lastMonthAmount) * 100, 1) : 0;

        return [
            [
                'label' => 'Total Expenses',
                'value' => number_format($totalExpenses),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
                'gradient' => 'from-blue-500 to-blue-600'
            ],
            [
                'label' => 'Pending',
                'value' => number_format($pendingExpenses),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                'gradient' => 'from-yellow-500 to-yellow-600'
            ],
            [
                'label' => 'Paid',
                'value' => number_format($paidExpenses),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                'gradient' => 'from-green-500 to-green-600'
            ],
            [
                'label' => 'This Month',
                'value' => '₱' . number_format($thisMonthAmount, 2),
                'change' => $changePercent,
                'period' => 'last month',
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>',
                'gradient' => 'from-red-500 to-red-600'
            ]
        ];
    }

    public function render()
    {
        $expenses = \App\Models\Finance::query()
            ->where('type', 'expense')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('type', 'like', "%{$this->search}%")
                        ->orWhere('reference_id', 'like', "%{$this->search}%")
                        ->orWhere('party', 'like', "%{$this->search}%")
                        ->orWhere('payment_method', 'like', "%{$this->search}%")
                        ->orWhere('status', 'like', "%{$this->search}%")
                        ->orWhere('remarks', 'like', "%{$this->search}%");
                });
            })
            ->orderByDesc('date')
            ->paginate($this->perPage);

        return view('livewire.pages.finance.expenses', [
            'expenses' => $expenses,
            'dashboardStats' => $this->getDashboardStatsProperty()
        ]);
    }
} 