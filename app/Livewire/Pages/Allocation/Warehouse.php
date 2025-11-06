<?php

namespace App\Livewire\Pages\Allocation;

use App\Models\BatchAllocation;
use App\Models\Branch;
use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
#[Title('Allocation - Warehouse')]
class Warehouse extends Component
{
    public $batchAllocations = [];
    public $selectedBatch = null;
    public $showCreateBatchModal = false;
    public $showAddBranchesModal = false;
    public $showAddItemsModal = false;
    public $selectedBranchAllocation = null;

    // Create batch fields
    public $transaction_date;
    public $remarks;
    public $ref_no;
    public $status = 'draft';

    // Add branches fields
    public $availableBranches = [];
    public $selectedBranchIds = [];
    public $branchRemarks = [];

    // Add items fields
    public $availableProducts = [];
    public $selectedProductId = null;
    public $productQuantity = 1;
    public $productUnitPrice = null;

    public function mount()
    {
        $this->loadBatchAllocations();
    }

    public function loadBatchAllocations()
    {
        $this->batchAllocations = BatchAllocation::with([
            'branchAllocations.branch',
            'branchAllocations.items.product'
        ])->orderBy('created_at', 'desc')->get();
    }

    public function generateRefNo()
    {
        $lastBatch = BatchAllocation::orderBy('id', 'desc')->first();
        $nextNumber = $lastBatch ? $lastBatch->id + 1 : 1;
        return 'WT-' . now()->format('Ymd') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function openCreateBatchModal()
    {
        $this->showCreateBatchModal = true;
        $this->ref_no = $this->generateRefNo();
    }

    public function closeCreateBatchModal()
    {
        $this->showCreateBatchModal = false;
        $this->reset(['transaction_date', 'remarks', 'ref_no']);
    }

    public function createBatch()
    {
        $this->validate([
            'transaction_date' => 'required|date',
            'remarks' => 'nullable|string|max:1000',
            'ref_no' => 'required|string|unique:batch_allocations,ref_no',
        ]);

        BatchAllocation::create([
            'transaction_date' => $this->transaction_date,
            'remarks' => $this->remarks,
            'ref_no' => $this->ref_no,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Batch allocation created successfully.');
        $this->closeCreateBatchModal();
        $this->loadBatchAllocations();
    }

    public function openAddBranchesModal(BatchAllocation $batch)
    {
        $this->selectedBatch = $batch;
        $this->showAddBranchesModal = true;
        
        // Get branches not already in this batch
        $existingBranchIds = $batch->branchAllocations->pluck('branch_id')->toArray();
        $this->availableBranches = Branch::whereNotIn('id', $existingBranchIds)->orderBy('name')->get();
    }

    public function closeAddBranchesModal()
    {
        $this->showAddBranchesModal = false;
        $this->selectedBatch = null;
        $this->selectedBranchIds = [];
        $this->branchRemarks = [];
    }

    public function addBranchesToBatch()
    {
        if (empty($this->selectedBranchIds)) {
            session()->flash('error', 'Please select at least one branch.');
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($this->selectedBranchIds as $branchId) {
                BranchAllocation::create([
                    'batch_allocation_id' => $this->selectedBatch->id,
                    'branch_id' => $branchId,
                    'remarks' => $this->branchRemarks[$branchId] ?? null,
                    'status' => 'pending',
                ]);
            }

            DB::commit();
            session()->flash('message', 'Branches added to batch successfully.');
            $this->closeAddBranchesModal();
            $this->loadBatchAllocations();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Failed to add branches: ' . $e->getMessage());
        }
    }

    public function openAddItemsModal(BranchAllocation $branchAllocation)
    {
        $this->selectedBranchAllocation = $branchAllocation;
        $this->showAddItemsModal = true;
        $this->availableProducts = Product::orderBy('name')->get();
    }

    public function closeAddItemsModal()
    {
        $this->showAddItemsModal = false;
        $this->selectedBranchAllocation = null;
        $this->selectedProductId = null;
        $this->productQuantity = 1;
        $this->productUnitPrice = null;
    }

    public function addItemToBranch()
    {
        $this->validate([
            'selectedProductId' => 'required|exists:products,id',
            'productQuantity' => 'required|integer|min:1',
            'productUnitPrice' => 'nullable|numeric|min:0',
        ]);

        // Check if product already exists in this branch allocation
        $existingItem = BranchAllocationItem::where('branch_allocation_id', $this->selectedBranchAllocation->id)
            ->where('product_id', $this->selectedProductId)
            ->first();

        if ($existingItem) {
            session()->flash('error', 'Product already exists in this branch allocation.');
            return;
        }

        BranchAllocationItem::create([
            'branch_allocation_id' => $this->selectedBranchAllocation->id,
            'product_id' => $this->selectedProductId,
            'quantity' => $this->productQuantity,
            'unit_price' => $this->productUnitPrice,
        ]);

        session()->flash('message', 'Item added to branch successfully.');
        $this->closeAddItemsModal();
        $this->loadBatchAllocations();
    }

    public function removeBranch(BranchAllocation $branchAllocation)
    {
        if ($branchAllocation->batchAllocation->status !== 'draft') {
            session()->flash('error', 'Cannot remove branch from non-draft batch.');
            return;
        }

        $branchAllocation->delete();
        session()->flash('message', 'Branch removed from batch successfully.');
        $this->loadBatchAllocations();
    }

    public function removeItem(BranchAllocationItem $item)
    {
        if ($item->branchAllocation->batchAllocation->status !== 'draft') {
            session()->flash('error', 'Cannot remove item from non-draft batch.');
            return;
        }

        $item->delete();
        session()->flash('message', 'Item removed successfully.');
        $this->loadBatchAllocations();
    }

    public function dispatchBatch(BatchAllocation $batch)
    {
        if ($batch->status !== 'draft') {
            session()->flash('error', 'Only draft batches can be dispatched.');
            return;
        }

        if ($batch->branchAllocations->isEmpty()) {
            session()->flash('error', 'Cannot dispatch batch without branches.');
            return;
        }

        foreach ($batch->branchAllocations as $branchAllocation) {
            if ($branchAllocation->items->isEmpty()) {
                session()->flash('error', 'Cannot dispatch batch. Branch "' . $branchAllocation->branch->name . '" has no items.');
                return;
            }
        }

        // Update batch status
        $batch->update(['status' => 'dispatched']);

        session()->flash('message', 'Batch dispatched successfully and sales allocations have been generated.');
        $this->loadBatchAllocations();
    }

    public function render()
    {
        return view('livewire.pages.allocation.warehouse');
    }
}
