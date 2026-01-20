<?php

namespace App\Livewire\Pages\Finance;

use Livewire\Component;
use App\Models\Finance;
use App\Models\PurchaseOrder; // <-- Add model
use App\Models\Supplier;      // <-- Optional: for supplier name

class Payables extends Component
{
    public $type = 'payable';
    public $reference_id;
    public $supplier;
    public $purchase_order;
    public $purchase_order_id;    // <-- Add PO id property
    public $party;
    public $date;
    public $due_date;
    public $amount;
    public $payment_method;
    public $status;
    public $remarks;
    public $balance;

    public $search = '';
    public $perPage = 10;
    public $editingPayableId = null;
    public $showCreatePanel = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $payableToDelete = null;

    public $purchaseOrders = [];  // <-- Add dropdown PO list

    protected $rules = [
        'type' => 'required|string|max:255',
        'reference_id' => 'nullable|string|max:255',
        'supplier' => 'nullable|string|max:255',
        'purchase_order' => 'nullable|string|max:255',
        'party' => 'nullable|string|max:255',
        'date' => 'required|date',
        'due_date' => 'nullable|date',
        'amount' => 'required|numeric|min:0',
        'balance' => 'required|numeric|min:0',
        'payment_method' => 'required|string|max:255',
        'status' => 'required|string|max:255',
        'remarks' => 'nullable|string',
    ];

    public function mount()
    {
        $this->generateReferenceId();
        $this->purchaseOrders = PurchaseOrder::orderBy('po_num')->get(); // <-- Populate POs for dropdown
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
        $this->editingPayableId = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['supplier', 'purchase_order', 'purchase_order_id', 'party', 'date', 'due_date', 'amount', 'balance', 'payment_method', 'status', 'remarks']);
        $this->status = 'pending';
        $this->date = now()->format('Y-m-d');
        $this->balance = '';
    }

    private function generateReferenceId()
    {
        $date = now()->format('ymd'); // e.g. 250721
        $prefix = 'PAY' . $date;

        // Find the latest reference ID for payables for today only
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

    // <-- Hook: When PO selection changes, autofill PO number/cost/supplier
    public function updatedPurchaseOrderId($value)
    {
        if ($value) {
            $po = PurchaseOrder::with('supplier')->find($value);
            
            if ($po) {
                // Populate the fields
                $this->purchase_order = $po->po_num;
                $this->supplier = $po->supplier->name ?? '';
                $this->amount = $po->total_price;
                $this->balance = $po->total_price; // Set balance equal to amount initially
                
                // Use your existing reference ID generation method
                $this->generateReferenceId();
            }
        } else {
            // Reset fields when no PO is selected
            $this->purchase_order = '';
            $this->supplier = '';
            $this->amount = '';
            $this->balance = '';
            $this->generateReferenceId();
        }
    }

    public function save()
    {
        $this->balance = $this->amount; // On create, balance = amount
        $this->status = $this->calculateStatus($this->amount, $this->balance);
        $this->validate();
        Finance::create([
            'type' => $this->type,
            'reference_id' => $this->reference_id,
            'supplier' => $this->supplier,
            'purchase_order' => $this->purchase_order,
            'party' => $this->party,
            'date' => $this->date,
            'due_date' => $this->due_date,
            'amount' => $this->amount,
            'balance' => $this->balance,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'remarks' => $this->remarks,
        ]);
        session()->flash('success', 'Payable saved successfully!');
        $this->closeCreatePanel();
        $this->reset(['reference_id', 'supplier', 'purchase_order', 'purchase_order_id', 'party', 'date', 'due_date', 'amount', 'balance', 'payment_method', 'status', 'remarks']);
        $this->generateReferenceId();
    }

    public function edit($id)
    {
        $payable = Finance::findOrFail($id);
        $this->editingPayableId = $payable->id;
        $this->type = $payable->type;
        $this->reference_id = $payable->reference_id;
        $this->supplier = $payable->supplier;
        $this->purchase_order = $payable->purchase_order;
        $this->party = $payable->party;
        $this->date = $payable->date;
        $this->due_date = $payable->due_date;
        $this->amount = $payable->amount;
        $this->balance = $payable->balance;
        $this->payment_method = $payable->payment_method;
        $this->status = $payable->status;
        $this->remarks = $payable->remarks;

        // Try to set PO select when editing by matching PO num
        $po = PurchaseOrder::where('po_num', $payable->purchase_order)->first();
        $this->purchase_order_id = $po ? $po->id : null;

        $this->showCreatePanel = true;
    }

    public function update()
    {
        $this->validate();
        $payable = Finance::findOrFail($this->editingPayableId);
        $this->status = $this->calculateStatus($this->amount, $this->balance, $this->status);
        $payable->update([
            'type' => $this->type,
            'reference_id' => $this->reference_id,
            'supplier' => $this->supplier,
            'purchase_order' => $this->purchase_order,
            'party' => $this->party,
            'date' => $this->date,
            'due_date' => $this->due_date,
            'amount' => $this->amount,
            'balance' => $this->balance,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'remarks' => $this->remarks,
        ]);
        session()->flash('success', 'Payable updated successfully!');
        $this->closeCreatePanel();
        $this->generateReferenceId();
    }

    public function cancel()
    {
        $this->closeCreatePanel();
    }

    private function resetEditState()
    {
        $this->editingPayableId = null;
        $this->showCreatePanel = false;
        $this->reset(['type', 'reference_id', 'supplier', 'purchase_order', 'purchase_order_id', 'party', 'date', 'due_date', 'amount', 'balance', 'payment_method', 'status', 'remarks']);
    }

    public function confirmDelete($id)
    {
        $this->payableToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $payable = Finance::findOrFail($this->payableToDelete);
        $payable->delete();
        session()->flash('success', 'Payable deleted successfully!');
        $this->showDeleteModal = false;
        $this->payableToDelete = null;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->payableToDelete = null;
    }

    private function calculateStatus($amount, $balance, $currentStatus = null)
    {
        if ($balance == 0) {
            return 'paid';
        } elseif ($balance < $amount) {
            return $currentStatus === 'cancelled' ? 'cancelled' : 'partial';
        } elseif ($balance == $amount) {
            return 'pending';
        }
        return $currentStatus ?? 'pending';
    }

    public function render()
    {
        $payables = Finance::query()
            ->where('type', 'payable')
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

        return view('livewire.pages.finance.payables', [
            'payables' => $payables,
            'purchaseOrders' => $this->purchaseOrders // <-- Pass PO list to view
        ]);
    }
}