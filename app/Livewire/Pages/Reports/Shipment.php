<?php

namespace App\Livewire\Pages\Reports;

use App\Models\Shipment as ShipmentModel;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Shipment extends Component
{
    public $dateFrom;
    public $dateTo;
    public $statusFilter = '';

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = ShipmentModel::query();

        // Apply filters
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }
        if ($this->statusFilter) {
            $query->where('shipping_status', $this->statusFilter);
        }

        // Basic stats
        $totalShipments = $query->count();
        $activeShipments = (clone $query)->whereIn('shipping_status', ['pending', 'approved', 'in_transit'])->count();
        $completedShipments = (clone $query)->where('shipping_status', 'completed')->count();

        // Status counts
        $statusCounts = (clone $query)->select('shipping_status', DB::raw('count(*) as count'))
            ->groupBy('shipping_status')
            ->pluck('count', 'shipping_status')
            ->toArray();

        // Monthly shipments for last 6 months
        $monthlyShipments = [];
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $monthlyShipments[] = ShipmentModel::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->when($this->statusFilter, fn($q) => $q->where('shipping_status', $this->statusFilter))
                ->count();
        }

        // Top branches by shipment count
        $topBranches = (clone $query)->with('branchAllocation.branch')
            ->select('branch_allocation_id', DB::raw('count(*) as shipment_count'))
            ->groupBy('branch_allocation_id')
            ->orderBy('shipment_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'name' => $item->branchAllocation->branch->name ?? 'Unknown',
                    'shipment_count' => $item->shipment_count
                ];
            });

        // Delivery methods
        $deliveryMethods = (clone $query)->select('delivery_method', DB::raw('count(*) as count'))
            ->groupBy('delivery_method')
            ->orderBy('count', 'desc')
            ->get();

        // Pending shipments
        $pendingShipments = (clone $query)->with('branchAllocation.branch')
            ->whereIn('shipping_status', ['pending', 'approved'])
            ->orderBy('scheduled_ship_date')
            ->limit(10)
            ->get();

        return view('livewire.pages.reports.shipment', [
            'totalShipments' => $totalShipments,
            'activeShipments' => $activeShipments,
            'completedShipments' => $completedShipments,
            'statusCounts' => $statusCounts,
            'monthlyShipments' => $monthlyShipments,
            'months' => $months,
            'topBranches' => $topBranches,
            'deliveryMethods' => $deliveryMethods,
            'pendingShipments' => $pendingShipments,
        ]);
    }
}