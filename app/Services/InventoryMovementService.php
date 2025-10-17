<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\InventoryLocation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryMovementService
{
    /**
     * Get inventory movements with filtering
     */
    public function getMovements(array $filters = [], int $perPage = 20, int $days = 30): LengthAwarePaginator
    {
        $query = InventoryMovement::with(['product.category', 'product.supplier', 'location', 'creator'])
            ->where('created_at', '>=', now()->subDays($days));

        if ($filters['product_id'] ?? null) {
            $query->where('product_id', $filters['product_id']);
        }

        if ($filters['location_id'] ?? null) {
            $query->where('location_id', $filters['location_id']);
        }

        if ($filters['movement_type'] ?? null) {
            $query->where('movement_type', $filters['movement_type']);
        }

        if ($filters['reference_type'] ?? null) {
            $query->where('reference_type', $filters['reference_type']);
        }

        if ($filters['created_by'] ?? null) {
            $query->where('created_by', $filters['created_by']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create a new inventory movement
     */
    public function createMovement(array $data): InventoryMovement
    {
        return DB::transaction(function () use ($data) {
            // Set default values
            $data['created_by'] = $data['created_by'] ?? auth()->id();
            
            // Calculate total cost if not provided
            if (!isset($data['total_cost']) && isset($data['unit_cost']) && isset($data['quantity'])) {
                $data['total_cost'] = $data['unit_cost'] * abs($data['quantity']);
            }

            $movement = InventoryMovement::create($data);

            return $movement->load(['product.category', 'product.supplier', 'location', 'creator']);
        });
    }

    /**
     * Get movement with full details
     */
    public function getMovementDetails(int $movementId): ?InventoryMovement
    {
        return InventoryMovement::with([
            'product.category',
            'product.supplier',
            'location',
            'creator'
        ])->find($movementId);
    }

    /**
     * Get movements by product
     */
    public function getMovementsByProduct(int $productId, int $perPage = 20, int $days = 30): LengthAwarePaginator
    {
        return InventoryMovement::with(['product.category', 'product.supplier', 'location', 'creator'])
            ->where('product_id', $productId)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get movements by location
     */
    public function getMovementsByLocation(int $locationId, int $perPage = 20, int $days = 30): LengthAwarePaginator
    {
        return InventoryMovement::with(['product.category', 'product.supplier', 'location', 'creator'])
            ->where('location_id', $locationId)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get movement statistics
     */
    public function getMovementStats(int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $totalMovements = InventoryMovement::where('created_at', '>=', $startDate)->count();
        
        $movementsByType = InventoryMovement::where('created_at', '>=', $startDate)
            ->selectRaw('movement_type, COUNT(*) as count')
            ->groupBy('movement_type')
            ->get()
            ->pluck('count', 'movement_type');

        $totalQuantityIn = InventoryMovement::where('created_at', '>=', $startDate)
            ->where('quantity', '>', 0)
            ->sum('quantity');

        $totalQuantityOut = abs(InventoryMovement::where('created_at', '>=', $startDate)
            ->where('quantity', '<', 0)
            ->sum('quantity'));

        $totalCostIn = InventoryMovement::where('created_at', '>=', $startDate)
            ->where('quantity', '>', 0)
            ->sum('total_cost');

        $totalCostOut = abs(InventoryMovement::where('created_at', '>=', $startDate)
            ->where('quantity', '<', 0)
            ->sum('total_cost'));

        $topProducts = InventoryMovement::where('created_at', '>=', $startDate)
            ->with('product')
            ->selectRaw('product_id, SUM(ABS(quantity)) as total_quantity')
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        $topLocations = InventoryMovement::where('created_at', '>=', $startDate)
            ->with('location')
            ->selectRaw('location_id, COUNT(*) as movement_count')
            ->groupBy('location_id')
            ->orderBy('movement_count', 'desc')
            ->limit(10)
            ->get();

        return [
            'period_days' => $days,
            'total_movements' => $totalMovements,
            'movements_by_type' => $movementsByType,
            'total_quantity_in' => $totalQuantityIn,
            'total_quantity_out' => $totalQuantityOut,
            'total_cost_in' => $totalCostIn,
            'total_cost_out' => $totalCostOut,
            'net_quantity' => $totalQuantityIn - $totalQuantityOut,
            'net_cost' => $totalCostIn - $totalCostOut,
            'top_products' => $topProducts,
            'top_locations' => $topLocations,
        ];
    }

    /**
     * Get movement types
     */
    public function getMovementTypes(): array
    {
        return [
            'purchase' => 'Purchase',
            'sale' => 'Sale',
            'adjustment' => 'Adjustment',
            'transfer_in' => 'Transfer In',
            'transfer_out' => 'Transfer Out',
            'return' => 'Return',
            'damage' => 'Damage',
            'expired' => 'Expired',
        ];
    }

    /**
     * Get monthly movement summary
     */
    public function getMonthlySummary(int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $movements = InventoryMovement::whereBetween('created_at', [$startDate, $endDate])
            ->with(['product', 'location'])
            ->get();

        $summary = [
            'period' => $startDate->format('Y-m'),
            'total_movements' => $movements->count(),
            'stock_ins' => $movements->where('quantity', '>', 0)->count(),
            'stock_outs' => $movements->where('quantity', '<', 0)->count(),
            'total_quantity_in' => $movements->where('quantity', '>', 0)->sum('quantity'),
            'total_quantity_out' => abs($movements->where('quantity', '<', 0)->sum('quantity')),
            'total_cost_in' => $movements->where('quantity', '>', 0)->sum('total_cost'),
            'total_cost_out' => abs($movements->where('quantity', '<', 0)->sum('total_cost')),
            'movements_by_type' => $movements->groupBy('movement_type')->map->count(),
            'movements_by_day' => $movements->groupBy(function ($movement) {
                return $movement->created_at->format('Y-m-d');
            })->map->count(),
        ];

        return $summary;
    }

    /**
     * Get movement trends
     */
    public function getMovementTrends(int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        // Get daily trends
        $dailyTrends = InventoryMovement::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, 
                        SUM(CASE WHEN quantity > 0 THEN quantity ELSE 0 END) as quantity_in,
                        SUM(CASE WHEN quantity < 0 THEN ABS(quantity) ELSE 0 END) as quantity_out,
                        COUNT(*) as movement_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get weekly trends
        $weeklyTrends = InventoryMovement::where('created_at', '>=', $startDate)
            ->selectRaw('YEARWEEK(created_at) as week,
                        SUM(CASE WHEN quantity > 0 THEN quantity ELSE 0 END) as quantity_in,
                        SUM(CASE WHEN quantity < 0 THEN ABS(quantity) ELSE 0 END) as quantity_out,
                        COUNT(*) as movement_count')
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        // Get movement type trends
        $typeTrends = InventoryMovement::where('created_at', '>=', $startDate)
            ->selectRaw('movement_type, 
                        SUM(CASE WHEN quantity > 0 THEN quantity ELSE 0 END) as quantity_in,
                        SUM(CASE WHEN quantity < 0 THEN ABS(quantity) ELSE 0 END) as quantity_out,
                        COUNT(*) as count')
            ->groupBy('movement_type')
            ->get();

        return [
            'period_days' => $days,
            'daily_trends' => $dailyTrends,
            'weekly_trends' => $weeklyTrends,
            'type_trends' => $typeTrends,
        ];
    }

    /**
     * Get recent movements
     */
    public function getRecentMovements(int $limit = 10): Collection
    {
        return InventoryMovement::with(['product.category', 'product.supplier', 'location', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get movements by user
     */
    public function getMovementsByUser(int $userId, int $perPage = 20, int $days = 30): LengthAwarePaginator
    {
        return InventoryMovement::with(['product.category', 'product.supplier', 'location', 'creator'])
            ->where('created_by', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get movements by reference
     */
    public function getMovementsByReference(string $referenceType, int $referenceId): Collection
    {
        return InventoryMovement::with(['product.category', 'product.supplier', 'location', 'creator'])
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get movement summary by product
     */
    public function getProductMovementSummary(int $productId, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $movements = InventoryMovement::where('product_id', $productId)
            ->where('created_at', '>=', $startDate)
            ->get();

        $totalIn = $movements->where('quantity', '>', 0)->sum('quantity');
        $totalOut = abs($movements->where('quantity', '<', 0)->sum('quantity'));
        $totalCostIn = $movements->where('quantity', '>', 0)->sum('total_cost');
        $totalCostOut = abs($movements->where('quantity', '<', 0)->sum('total_cost'));

        return [
            'product_id' => $productId,
            'period_days' => $days,
            'total_movements' => $movements->count(),
            'total_quantity_in' => $totalIn,
            'total_quantity_out' => $totalOut,
            'net_quantity' => $totalIn - $totalOut,
            'total_cost_in' => $totalCostIn,
            'total_cost_out' => $totalCostOut,
            'net_cost' => $totalCostIn - $totalCostOut,
            'movements_by_type' => $movements->groupBy('movement_type')->map->count(),
        ];
    }

    /**
     * Get movement summary by location
     */
    public function getLocationMovementSummary(int $locationId, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $movements = InventoryMovement::where('location_id', $locationId)
            ->where('created_at', '>=', $startDate)
            ->get();

        $totalIn = $movements->where('quantity', '>', 0)->sum('quantity');
        $totalOut = abs($movements->where('quantity', '<', 0)->sum('quantity'));
        $totalCostIn = $movements->where('quantity', '>', 0)->sum('total_cost');
        $totalCostOut = abs($movements->where('quantity', '<', 0)->sum('total_cost'));

        return [
            'location_id' => $locationId,
            'period_days' => $days,
            'total_movements' => $movements->count(),
            'total_quantity_in' => $totalIn,
            'total_quantity_out' => $totalOut,
            'net_quantity' => $totalIn - $totalOut,
            'total_cost_in' => $totalCostIn,
            'total_cost_out' => $totalCostOut,
            'net_cost' => $totalCostIn - $totalCostOut,
            'movements_by_type' => $movements->groupBy('movement_type')->map->count(),
        ];
    }
}
