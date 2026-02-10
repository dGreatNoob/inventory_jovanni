<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Branch;
use App\Models\Shipment;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class BranchInventory extends Component
{
    use WithPagination;

    public $batches = [];
    public $batchesWithoutCompletedShipments = [];

    public $search = '';
    public $batchFilter = '';
    /** @var bool When true, only show branches with completed shipments */
    public $completedShipmentOnly = true;
    public $perPage = 10;
    public $showFilters = false;

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedBatchFilter()
    {
        $this->resetPage();
    }

    public function updatedCompletedShipmentOnly()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->loadBatches();
    }

    protected function loadBatches()
    {
        $batchesWithShipments = Branch::whereHas('branchAllocations.shipments', function ($q) {
            $q->where('shipping_status', 'completed');
        })
        ->distinct()
        ->pluck('batch')
        ->filter()
        ->sort()
        ->values();

        $this->batches = $batchesWithShipments->map(function ($batchName) {
            $branchCount = Branch::where('batch', $batchName)
                ->whereHas('branchAllocations.shipments', function ($q) {
                    $q->where('shipping_status', 'completed');
                })
                ->count();

            $totalShipments = Shipment::whereHas('branchAllocation.branch', function ($q) use ($batchName) {
                $q->where('batch', $batchName);
            })
            ->where('shipping_status', 'completed')
            ->count();

            return [
                'name' => $batchName,
                'branch_count' => $branchCount,
                'total_shipments' => $totalShipments,
                'last_shipment_date' => $this->getLastShipmentDateForBatch($batchName),
            ];
        });

        $this->loadBatchesWithoutCompletedShipments($batchesWithShipments->toArray());
    }

    protected function loadBatchesWithoutCompletedShipments(array $excludeBatchNames)
    {
        $allBatchNames = Branch::whereNotNull('batch')
            ->where('batch', '!=', '')
            ->distinct()
            ->pluck('batch')
            ->filter()
            ->sort()
            ->values()
            ->diff($excludeBatchNames)
            ->values();

        $this->batchesWithoutCompletedShipments = $allBatchNames->map(function ($batchName) {
            $branchCount = Branch::where('batch', $batchName)->count();

            $branchesWithAllocations = Branch::where('batch', $batchName)
                ->whereHas('branchAllocations')
                ->count();

            $branchesWithShipments = Branch::where('batch', $batchName)
                ->whereHas('branchAllocations.shipments')
                ->count();

            if ($branchesWithAllocations === 0) {
                $status = 'no_allocations';
                $status_label = 'No allocations';
            } elseif ($branchesWithShipments === 0) {
                $status = 'no_shipments';
                $status_label = 'No shipments';
            } else {
                $status = 'pending_shipments';
                $status_label = 'Pending shipments';
            }

            return [
                'name' => $batchName,
                'branch_count' => $branchCount,
                'status' => $status,
                'status_label' => $status_label,
            ];
        })->values()->all();
    }

    protected function getLastShipmentDateForBatch($batchName)
    {
        $lastShipment = Shipment::whereHas('branchAllocation.branch', function ($q) use ($batchName) {
            $q->where('batch', $batchName);
        })
        ->where('shipping_status', 'completed')
        ->latest('updated_at')
        ->first();

        return $lastShipment ? $lastShipment->updated_at->format('M d, Y') : null;
    }

    protected function getLastShipmentDate($branchId)
    {
        $lastShipment = Shipment::whereHas('branchAllocation', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })
        ->where('shipping_status', 'completed')
        ->latest('updated_at')
        ->first();

        return $lastShipment ? $lastShipment->updated_at->format('M d, Y') : null;
    }

    /**
     * All batches for the filter dropdown (with and without completed shipments)
     */
    public function getAllBatchesForFilterProperty()
    {
        $with = collect($this->batches)->map(fn ($b) => array_merge($b, ['has_completed_shipments' => true]));
        $without = collect($this->batchesWithoutCompletedShipments)->map(fn ($b) => array_merge($b, ['has_completed_shipments' => false]));
        return $with->concat($without)->sortBy('name')->values();
    }

    public function getFilteredBranchesProperty()
    {
        $hasBatchFilter = trim((string) $this->batchFilter) !== '';

        $query = Branch::query();

        if ($hasBatchFilter) {
            $query->where('batch', $this->batchFilter);
        }

        if ($this->completedShipmentOnly) {
            $query->whereHas('branchAllocations.shipments', function ($q) {
                $q->where('shipping_status', 'completed');
            });
        }

        $query->withCount([
            'branchAllocations as completed_shipments_count' => function ($q) {
                $q->whereHas('shipments', fn ($sq) => $sq->where('shipping_status', 'completed'));
            },
        ]);

        $branches = $query->get();

        $search = trim($this->search);
        if ($search !== '') {
            $lower = strtolower($search);
            $branches = $branches->filter(fn ($b) =>
                str_contains(strtolower($b->name ?? ''), $lower)
                || str_contains(strtolower($b->code ?? ''), $lower)
                || str_contains(strtolower($b->address ?? ''), $lower)
            )->values();
        }

        return $branches->map(function ($branch) {
            return [
                'id' => $branch->id,
                'name' => $branch->name,
                'code' => $branch->code,
                'batch' => $branch->batch,
                'address' => $branch->address,
                'completed_shipments_count' => $branch->completed_shipments_count,
                'last_shipment_date' => $this->getLastShipmentDate($branch->id),
            ];
        });
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->batchFilter = '';
        $this->completedShipmentOnly = true;
        $this->resetPage();
    }

    public function render()
    {
        $filtered = $this->filteredBranches;
        $items = new LengthAwarePaginator(
            $filtered->forPage($this->getPage(), $this->perPage)->values(),
            $filtered->count(),
            $this->perPage,
            $this->getPage(),
            ['path' => Paginator::resolveCurrentPath(), 'pageName' => 'page']
        );

        return view('livewire.pages.branch.branch-inventory', [
            'items' => $items,
        ]);
    }
}
