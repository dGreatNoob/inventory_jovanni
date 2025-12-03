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
     * Initialize batch counts based on branch batch field
     */
    protected function initializeBatchCounts()
    {
        // Count branches by their batch field
        $this->batch1BranchesCount = Branch::where('batch', 'BATCH-01')->count();
        $this->batch2BranchesCount = Branch::where('batch', 'BATCH-02')->count(); 
        $this->batch3BranchesCount = Branch::where('batch', 'BATCH-03')->count();
    }

    /**
     * Select a batch to view its branches
     */
    public function selectBatch($batchNumber)
    {
        // Map batch number to batch name
        $batchMap = [
            '1' => 'BATCH-01',
            '2' => 'BATCH-02', 
            '3' => 'BATCH-03'
        ];
        
        $this->selectedBatch = $batchMap[$batchNumber] ?? $batchNumber;
        $this->loadBatchBranches();
    }

    /**
     * Load branches for the selected batch based on branch batch field
     */
    protected function loadBatchBranches()
    {
        $this->batchBranches = [];

        // Get branches by their batch field
        $branches = Branch::where('batch', $this->selectedBatch)->get();

        foreach ($branches as $branch) {
            $this->batchBranches[] = [
                'id' => $branch->id,
                'name' => $branch->name,
                'code' => $branch->code,
                'address' => $branch->address,
                'batch_number' => $branch->batch,
                'category' => $branch->category,
                'manager_name' => $branch->manager_name,
            ];
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