<?php

namespace App\Livewire\Pages\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;
use App\Models\Finance;
use App\Enums\PaymentStatus;

class Payments extends Component
{
    use WithPagination;

    public $payment_ref;
    public $amount;
    public $payment_date;
    public $payment_method;
    public $finance_id;
    public $status;
    public $remarks;

    public $search = '';
    public $perPage = 10;
    public $filterPaymentMethod = '';
    public $filterStatus = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    
    public $editingPaymentId = null;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $paymentToDelete = null;

    // Available payables and receivables
    public $availableFinances = [];
    public $selectedFinanceBalance = 0;

    protected $rules = [
        'payment_ref' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0.01',
        'payment_date' => 'required|date',
        'payment_method' => 'nullable|string|max:255',
        'finance_id' => 'required|exists:finances,id',
        'remarks' => 'nullable|string',
    ];

    protected $messages = [
        'finance_id.required' => 'Please select a payable or receivable record.',
    ];

    public function mount()
    {
        $this->generatePaymentRef();
        $this->loadAvailableFinances();
        $this->payment_date = now()->format('Y-m-d');
        $this->payment_method = 'Cash';
    }

    private function generatePaymentRef()
    {
        $date = now()->format('ymd');
        $prefix = 'PAY' . $date;

        $latest = Payment::where('payment_ref', 'like', $prefix . '%')
                        ->orderByDesc('payment_ref')
                        ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->payment_ref, -3);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        $this->payment_ref = $prefix . $nextNumber;
    }

    private function loadAvailableFinances()
    {
        // Load all finances (payables and receivables) that have a balance > 0
        $this->availableFinances = Finance::where('balance', '>', 0)
            ->orderByDesc('date')
            ->get()
            ->map(function ($finance) {
                return [
                    'id' => $finance->id,
                    'label' => "{$finance->reference_id} - {$finance->type} - Balance: " . number_format($finance->balance, 2),
                    'balance' => $finance->balance,
                    'type' => $finance->type,
                ];
            });
    }

    public function updatedFinanceId($value)
    {
        if ($value) {
            $finance = Finance::find($value);
            $this->selectedFinanceBalance = $finance ? $finance->balance : 0;
        } else {
            $this->selectedFinanceBalance = 0;
        }
    }

    public function save()
    {
        $this->validate();

        // Determine status based on amount vs balance
        $finance = Finance::find($this->finance_id);
        $this->status = $this->calculatePaymentStatus($finance, $this->amount);

        Payment::create([
            'payment_ref' => $this->payment_ref,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'payment_method' => $this->payment_method,
            'finance_id' => $this->finance_id,
            'status' => $this->status,
            'remarks' => $this->remarks,
        ]);

        // Update finance balance
        $finance->balance -= $this->amount;
        $finance->save();

        session()->flash('success', 'Payment recorded successfully!');
        $this->resetForm();
    }

    public function edit($id)
    {
        $payment = Payment::with('finance')->findOrFail($id);
        $this->editingPaymentId = $payment->id;
        $this->payment_ref = $payment->payment_ref;
        $this->amount = $payment->amount;
        $this->payment_date = \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d');
        $this->payment_method = $payment->payment_method;
        $this->finance_id = $payment->finance_id;
        $this->status = $payment->status;
        $this->remarks = $payment->remarks;
        $this->selectedFinanceBalance = $payment->finance->balance + $payment->amount; // Add back original payment
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate();

        $payment = Payment::findOrFail($this->editingPaymentId);
        $finance = Finance::find($this->finance_id);

        // Restore original balance
        $finance->balance += $payment->amount;

        // Determine status
        $this->status = $this->calculatePaymentStatus($finance, $this->amount);

        $payment->update([
            'payment_ref' => $this->payment_ref,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'payment_method' => $this->payment_method,
            'finance_id' => $this->finance_id,
            'status' => $this->status,
            'remarks' => $this->remarks,
        ]);

        // Update finance balance
        $finance->balance -= $this->amount;
        $finance->save();

        session()->flash('success', 'Payment updated successfully!');
        $this->resetEditState();
    }

    public function confirmDelete($id)
    {
        $this->paymentToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $payment = Payment::findOrFail($this->paymentToDelete);
        
        // Restore balance to finance record
        $finance = Finance::find($payment->finance_id);
        if ($finance) {
            $finance->balance += $payment->amount;
            $finance->save();
        }
        
        $payment->delete();
        
        session()->flash('success', 'Payment deleted successfully!');
        $this->showDeleteModal = false;
        $this->paymentToDelete = null;
        $this->loadAvailableFinances();
    }

    public function cancel()
    {
        $this->resetEditState();
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->paymentToDelete = null;
    }

    private function resetForm()
    {
        $this->reset([
            'amount', 'payment_date', 'payment_method', 
            'finance_id', 'status', 'remarks'
        ]);
        $this->selectedFinanceBalance = 0;
        $this->generatePaymentRef();
        $this->loadAvailableFinances();
        $this->payment_date = now()->format('Y-m-d');
    }

    private function resetEditState()
    {
        $this->editingPaymentId = null;
        $this->showEditModal = false;
        $this->resetForm();
    }

    private function calculatePaymentStatus($finance, $paymentAmount)
    {
        $remainingBalance = $finance->balance - $paymentAmount;
        
        if ($remainingBalance == 0) {
            return PaymentStatus::FULLY_PAID->value;
        } elseif ($remainingBalance > 0 && $remainingBalance < $finance->amount) {
            return PaymentStatus::PARTIALLY_PAID->value;
        } elseif ($finance->due_date && now()->isAfter($finance->due_date)) {
            return PaymentStatus::OVERDUE->value;
        } else {
            return PaymentStatus::NOT_PAID->value;
        }
    }

    public function render()
    {
        $payments = Payment::with('finance')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('payment_ref', 'like', "%{$this->search}%")
                        ->orWhere('payment_method', 'like', "%{$this->search}%")
                        ->orWhere('remarks', 'like', "%{$this->search}%")
                        ->orWhereHas('finance', function ($fq) {
                            $fq->where('reference_id', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->filterPaymentMethod, function ($query) {
                $query->where('payment_method', $this->filterPaymentMethod);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterDateFrom && $this->filterDateTo, function ($query) {
                $query->whereBetween('payment_date', [$this->filterDateFrom, $this->filterDateTo]);
            })
            ->orderByDesc('payment_date')
            ->paginate($this->perPage);

        return view('livewire.pages.finance.payments', [
            'payments' => $payments,
            'paymentStatuses' => PaymentStatus::cases(),
        ]);
    }
}
