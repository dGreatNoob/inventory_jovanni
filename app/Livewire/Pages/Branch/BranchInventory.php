<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use App\Models\Branch;
use App\Models\BatchAllocation;
use App\Models\BranchAllocation;

class BranchInventory extends Component
{
    // Batch selection properties - using the original 3-batch approach
    public $selectedBatch = null;
    public $batchBranches = [];

    // Batch counts for UI
    public $batch1BranchesCount = 0;
    public $batch2BranchesCount = 0;
    public $batch3BranchesCount = 0;

    // Branch details modal
    public $showBranchDetailsModal = false;
    public $selectedBranchDetails = null;

    public function mount()
    {
        $this->initializeBatchCounts();
    }

    /**
     * Initialize batch counts for the 3 batch buttons
     */
    protected function initializeBatchCounts()
    {
        // Get batch allocations grouped by batch number
        $batchAllocations = BatchAllocation::with(['branchAllocations'])->get();

        // Count branches for each batch
        $this->batch1BranchesCount = $batchAllocations->filter(function($batch) {
            return str_contains($batch->batch_number ?? '', '1') && $batch->branchAllocations->count() > 0;
        })->flatMap->branchAllocations->count();

        $this->batch2BranchesCount = $batchAllocations->filter(function($batch) {
            return str_contains($batch->batch_number ?? '', '2') && $batch->branchAllocations->count() > 0;
        })->flatMap->branchAllocations->count();

        $this->batch3BranchesCount = $batchAllocations->filter(function($batch) {
            return str_contains($batch->batch_number ?? '', '3') && $batch->branchAllocations->count() > 0;
        })->flatMap->branchAllocations->count();
    }

    /**
     * Select a batch to view its branches
     */
    public function selectBatch($batchNumber)
    {
        $this->selectedBatch = $batchNumber;
        $this->loadBatchBranches();
    }

    /**
     * Load branches for the selected batch
     */
    protected function loadBatchBranches()
    {
        $this->batchBranches = [];

        // Get batch allocations for the selected batch
        $batchAllocations = BatchAllocation::with(['branchAllocations.branch'])
            ->where(function($query) {
                // Filter by batch number containing the selected batch digit
                $query->where('batch_number', 'LIKE', "%{$this->selectedBatch}%")
                      ->orWhere('id', $this->selectedBatch); // Fallback
            })
            ->get();

        foreach ($batchAllocations as $batchAllocation) {
            foreach ($batchAllocation->branchAllocations as $branchAllocation) {
                $branch = $branchAllocation->branch;

                $this->batchBranches[] = [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'code' => $branch->code,
                    'address' => $branch->address,
                    'batch_number' => $batchAllocation->batch_number,
                    'allocation_id' => $branchAllocation->id,
                    'reference' => $batchAllocation->ref_no,
                ];
            }
        }
    }

    /**
     * View details of a specific branch
     */
    public function viewBranchDetails($branchId)
    {
        $branchDetails = collect($this->batchBranches)->firstWhere('id', $branchId);

        if ($branchDetails) {
            $this->selectedBranchDetails = $branchDetails;
            $this->showBranchDetailsModal = true;
        }
    }

    /**
     * Close the branch details modal
     */
    public function closeBranchDetailsModal()
    {
        $this->showBranchDetailsModal = false;
        $this->selectedBranchDetails = null;
    }

    /**
     * Refresh batch data
     */
    public function refreshBatchData()
    {
        $this->initializeBatchCounts();
        if ($this->selectedBatch) {
            $this->loadBatchBranches();
        }
    }

    /**
     * Clear batch selection
     */
    public function clearSelection()
    {
        $this->selectedBatch = null;
        $this->batchBranches = [];
    }

    public function render()
    {
        return view('livewire.pages.branch.branch-inventory');
    }
}