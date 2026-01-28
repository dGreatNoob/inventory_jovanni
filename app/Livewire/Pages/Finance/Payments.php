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
    public $type;
    public $status;
    public $remarks;

    public $search = '';
    public $perPage = 10;
    public $filterPaymentMethod = '';
    public $filterStatus = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';

    public $editingPaymentId = null;
    public $showCreatePanel = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $paymentToDelete = null;

    protected $rules = [
        'payment_ref' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0.01',
        'payment_date' => 'required|date',
        'payment_method' => 'nullable|string|max:255',
        'type' => 'nullable|in:Payable,Receivable',
        'remarks' => 'nullable|string',
    ];

    protected $messages = [
        'type.required' => 'Please select a type.',
    ];

    public function mount()
    {
        $this->generatePaymentRef();
        $this->payment_date = now()->format('Y-m-d');
        $this->payment_method = 'Cash';
    }

    public function openCreatePanel()
    {
        $this->showCreatePanel = true;
        $this->resetForm();
        $this->generatePaymentRef();
    }

    public function closeCreatePanel()
    {
        $this->showCreatePanel = false;
        $this->editingPaymentId = null;
        $this->resetForm();
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


    public function save()
    {
        $this->validate();

        $this->status = 'fully_paid'; // Default status

        Payment::create([
            'payment_ref' => $this->payment_ref,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'payment_method' => $this->payment_method,
            'type' => $this->type,
            'status' => $this->status,
            'remarks' => $this->remarks,
        ]);

        session()->flash('success', 'Payment recorded successfully!');
        $this->closeCreatePanel();
    }

    public function edit($id)
    {
        $payment = Payment::findOrFail($id);
        $this->editingPaymentId = $payment->id;
        $this->payment_ref = $payment->payment_ref;
        $this->amount = $payment->amount;
        $this->payment_date = \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d');
        $this->payment_method = $payment->payment_method;
        $this->type = $payment->type;
        $this->status = $payment->status;
        $this->remarks = $payment->remarks;
        $this->showCreatePanel = true;
    }

    public function update()
    {
        $this->validate();

        $payment = Payment::findOrFail($this->editingPaymentId);

        $this->status = 'fully_paid';

        $payment->update([
            'payment_ref' => $this->payment_ref,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'payment_method' => $this->payment_method,
            'type' => $this->type,
            'status' => $this->status,
            'remarks' => $this->remarks,
        ]);

        session()->flash('success', 'Payment updated successfully!');
        $this->closeCreatePanel();
    }

    public function confirmDelete($id)
    {
        $this->paymentToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $payment = Payment::findOrFail($this->paymentToDelete);

        $payment->delete();

        session()->flash('success', 'Payment deleted successfully!');
        $this->showDeleteModal = false;
        $this->paymentToDelete = null;
    }

    public function cancel()
    {
        $this->closeCreatePanel();
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
            'type', 'status', 'remarks'
        ]);
        $this->generatePaymentRef();
        $this->payment_date = now()->format('Y-m-d');
    }

    private function resetEditState()
    {
        $this->editingPaymentId = null;
        $this->showCreatePanel = false;
        $this->resetForm();
    }


    public function render()
    {
        $payments = Payment::
            when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('payment_ref', 'like', "%{$this->search}%")
                        ->orWhere('payment_method', 'like', "%{$this->search}%")
                        ->orWhere('remarks', 'like', "%{$this->search}%")
                        ->orWhere('type', 'like', "%{$this->search}%");
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
