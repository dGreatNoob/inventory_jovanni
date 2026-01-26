<?php

namespace App\Livewire\Pages\Reports;

use Livewire\Component;
use App\Models\Branch;
use App\Models\BranchAllocationItem;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[
    Layout('components.layouts.app'),
    Title('Branch Inventory Report')
]
class BranchInventoryReport extends Component
{
    public string $dateFrom = '';
    public string $dateTo = '';
    public ?int $selectedBranch = null;

    public function mount()
    {
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
    }

    protected function baseQuery()
    {
        $query = BranchAllocationItem::with([
            'branchAllocation.branch',
            'branchAllocation.shipments',
            'product'
        ])
        ->whereHas('branchAllocation.shipments', function ($q) {
            $q->where('shipping_status', 'completed');
        })
        ->where('box_id', null); // Only unpacked items

        if ($this->dateFrom) {
            $query->whereHas('branchAllocation.shipments', function ($q) {
                $q->whereDate('created_at', '>=', $this->dateFrom);
            });
        }
        if ($this->dateTo) {
            $query->whereHas('branchAllocation.shipments', function ($q) {
                $q->whereDate('created_at', '<=', $this->dateTo);
            });
        }
        if ($this->selectedBranch) {
            $query->whereHas('branchAllocation', function ($q) {
                $q->where('branch_id', $this->selectedBranch);
            });
        }

        return $query;
    }

    public function render()
    {
        $base = $this->baseQuery();

        // Overview stats
        $totalBranches = Branch::whereHas('branchAllocations.shipments', function ($q) {
            $q->where('shipping_status', 'completed');
        })->count();

        $totalProducts = (clone $base)->distinct('product_id')->count('product_id');

        $totalQuantity = (clone $base)->sum('quantity');
        $totalSold = (clone $base)->sum('sold_quantity');
        $totalValue = (clone $base)->sum(DB::raw('quantity * unit_price'));

        $lowStockItems = (clone $base)
            ->selectRaw('product_id, SUM(quantity - sold_quantity) as remaining')
            ->groupBy('product_id')
            ->having('remaining', '<', 10)
            ->having('remaining', '>', 0)
            ->get()
            ->count();

        $outOfStockItems = (clone $base)
            ->selectRaw('product_id, SUM(quantity - sold_quantity) as remaining')
            ->groupBy('product_id')
            ->having('remaining', '<=', 0)
            ->get()
            ->count();

        // Branch performance - calculate manually
        $branchPerformance = Branch::whereHas('branchAllocations.shipments', function ($q) {
            $q->where('shipping_status', 'completed');
            if ($this->dateFrom) $q->whereDate('created_at', '>=', $this->dateFrom);
            if ($this->dateTo) $q->whereDate('created_at', '<=', $this->dateTo);
        })
        ->with(['branchAllocations.shipments' => function ($q) {
            $q->where('shipping_status', 'completed');
            if ($this->dateFrom) $q->whereDate('created_at', '>=', $this->dateFrom);
            if ($this->dateTo) $q->whereDate('created_at', '<=', $this->dateTo);
        }])
        ->get()
        ->map(function ($branch) {
            $totalShipments = $branch->branchAllocations->sum(function ($allocation) {
                return $allocation->shipments->count();
            });

            $allocationItems = BranchAllocationItem::whereHas('branchAllocation', function ($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            })
            ->whereHas('branchAllocation.shipments', function ($q) {
                $q->where('shipping_status', 'completed');
                if ($this->dateFrom) $q->whereDate('created_at', '>=', $this->dateFrom);
                if ($this->dateTo) $q->whereDate('created_at', '<=', $this->dateTo);
            })
            ->where('box_id', null)
            ->get();

            $totalAllocated = $allocationItems->sum('quantity');
            $totalSold = $allocationItems->sum('sold_quantity');

            return [
                'id' => $branch->id,
                'name' => $branch->name,
                'total_shipments' => $totalShipments,
                'total_allocated' => $totalAllocated,
                'total_sold' => $totalSold,
            ];
        })
        ->sortByDesc('total_shipments')
        ->take(10);

        // Top products by quantity
        $topProducts = (clone $base)
            ->select('product_id', 'product_snapshot_name', 'product_snapshot_barcode', 'product_snapshot_sku')
            ->selectRaw('SUM(quantity) as total_quantity, SUM(sold_quantity) as total_sold, SUM(quantity * unit_price) as total_value')
            ->groupBy('product_id', 'product_snapshot_name', 'product_snapshot_barcode', 'product_snapshot_sku')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // Low stock products
        $lowStockProducts = (clone $base)
            ->select('product_id', 'product_snapshot_name', 'product_snapshot_barcode', 'product_snapshot_sku')
            ->selectRaw('SUM(quantity - sold_quantity) as remaining, SUM(quantity) as total_quantity')
            ->groupBy('product_id', 'product_snapshot_name', 'product_snapshot_barcode', 'product_snapshot_sku')
            ->having('remaining', '<', 10)
            ->having('remaining', '>', 0)
            ->orderBy('remaining')
            ->limit(10)
            ->get();

        // Out of stock products
        $outOfStockProducts = (clone $base)
            ->select('product_id', 'product_snapshot_name', 'product_snapshot_barcode', 'product_snapshot_sku')
            ->selectRaw('SUM(quantity - sold_quantity) as remaining, SUM(quantity) as total_quantity')
            ->groupBy('product_id', 'product_snapshot_name', 'product_snapshot_barcode', 'product_snapshot_sku')
            ->having('remaining', '<=', 0)
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // Monthly trend for last 6 months
        $months = [];
        $values = [];
        $now = now();
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $label = $month->format('M');
            $count = Shipment::where('shipping_status', 'completed')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->when($this->selectedBranch, function ($q) {
                    $q->whereHas('branchAllocation', function ($sub) {
                        $sub->where('branch_id', $this->selectedBranch);
                    });
                })
                ->count();
            $months[] = $label;
            $values[] = $count;
        }

        $branches = Branch::orderBy('name')->get();

        return view('livewire.pages.reports.branch-inventory-report', compact(
            'totalBranches', 'totalProducts', 'totalQuantity', 'totalSold', 'totalValue',
            'lowStockItems', 'outOfStockItems', 'branchPerformance', 'topProducts',
            'lowStockProducts', 'outOfStockProducts', 'months', 'values', 'branches'
        ));
    }
}