<?php

namespace App\Livewire\Pages\Reports;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Enums\PurchaseOrderStatus;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[
    Layout('components.layouts.app'),
    Title('Purchase Orders Report')
]
class PurchaseOrders extends Component
{
    public string $dateFrom = '';
    public string $dateTo = '';
    public ?int $selectedSupplier = null;
    public string $statusFilter = '';

    protected function baseQuery()
    {
        $query = PurchaseOrder::query();

        if ($this->dateFrom) {
            $query->whereDate('order_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('order_date', '<=', $this->dateTo);
        }
        if ($this->selectedSupplier) {
            $query->where('supplier_id', $this->selectedSupplier);
        }
        if ($this->statusFilter) {
            // Only apply if it matches a defined status
            $valid = collect(PurchaseOrderStatus::cases())->pluck('value')->all();
            if (in_array($this->statusFilter, $valid, true)) {
                $query->where('status', $this->statusFilter);
            }
        }

        return $query;
    }

    public function render()
    {
        $base = $this->baseQuery();

        $totalPOs = (clone $base)->count();
        $pendingPOs = (clone $base)->where('status', PurchaseOrderStatus::PENDING)->count();
        $toReceivePOs = (clone $base)->where('status', PurchaseOrderStatus::TO_RECEIVE)->count();
        $receivedPOs = (clone $base)->where('status', PurchaseOrderStatus::RECEIVED)->count();
        $totalValue = (clone $base)->sum('total_price');

        // Monthly trend for last 6 months using order_date
        $months = [];
        $values = [];
        $now = Carbon::now();
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $label = $month->format('M');
            $count = (clone $base)
                ->whereMonth('order_date', $month->month)
                ->whereYear('order_date', $month->year)
                ->count();
            $months[] = $label;
            $values[] = $count;
        }

        // Top suppliers by order count and total value
        $topSuppliers = (clone $base)
            ->select('supplier_id')
            ->selectRaw('COUNT(*) as order_count')
            ->selectRaw('SUM(total_price) as total_value')
            ->with('supplier')
            ->groupBy('supplier_id')
            ->orderByDesc('order_count')
            ->limit(5)
            ->get();

        // Recent orders
        $recentOrders = (clone $base)
            ->with(['supplier', 'productOrders'])
            ->orderByDesc('order_date')
            ->limit(5)
            ->get();

        $suppliers = Supplier::orderBy('name')->get();

        return view('livewire.pages.reports.purchase-orders', compact(
            'totalPOs', 'pendingPOs', 'toReceivePOs', 'receivedPOs', 'totalValue',
            'months', 'values', 'topSuppliers', 'recentOrders', 'suppliers'
        ));
    }
}