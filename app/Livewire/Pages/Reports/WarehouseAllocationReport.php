<?php

namespace App\Livewire\Pages\Reports;

use App\Models\BatchAllocation;
use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
#[Title('Warehouse Allocation Report')]
class WarehouseAllocationReport extends Component
{
    public $dateFrom = '';
    public $dateTo = '';
    public $statusFilter = '';

    // Computed properties for the view
    public $totalBatches = 0;
    public $totalAllocations = 0;
    public $totalProducts = 0;
    public $totalValue = 0;
    public $pendingBatches = 0;
    public $dispatchedBatches = 0;
    public $draftBatches = 0;

    // Chart data
    public $statusData = [];
    public $monthlyData = [];
    public $months = [];
    public $values = [];

    // Recent batches
    public $recentBatches = [];

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->loadData();
    }

    public function updatedDateFrom()
    {
        $this->loadData();
    }

    public function updatedDateTo()
    {
        $this->loadData();
    }

    public function updatedStatusFilter()
    {
        $this->loadData();
    }

    private function loadData()
    {
        $query = BatchAllocation::query();

        // Apply date filters
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Apply status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $batches = $query->with(['branchAllocations.items'])->get();

        // Calculate KPIs
        $this->totalBatches = $batches->count();
        $this->totalAllocations = $batches->sum(function ($batch) {
            return $batch->branchAllocations->count();
        });

        $this->totalProducts = 0;
        $this->totalValue = 0;

        foreach ($batches as $batch) {
            foreach ($batch->branchAllocations as $allocation) {
                foreach ($allocation->items as $item) {
                    $this->totalProducts += $item->quantity;
                    $this->totalValue += ($item->quantity * ($item->unit_price ?? 0));
                }
            }
        }

        // Status counts
        $this->pendingBatches = $batches->where('status', 'pending')->count();
        $this->dispatchedBatches = $batches->where('status', 'dispatched')->count();
        $this->draftBatches = $batches->where('status', 'draft')->count();

        // Status distribution for chart
        $this->statusData = [
            'draft' => $this->draftBatches,
            'pending' => $this->pendingBatches,
            'dispatched' => $this->dispatchedBatches,
        ];

        // Monthly trend data (last 6 months)
        $this->loadMonthlyData();

        // Recent batches
        $this->recentBatches = BatchAllocation::with(['branchAllocations.branch'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function loadMonthlyData()
    {
        $this->months = [];
        $this->values = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $this->months[] = $date->format('M');

            $count = BatchAllocation::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $this->values[] = $count;
        }
    }

    public function render()
    {
        return view('livewire.pages.reports.warehouse-allocation');
    }
}