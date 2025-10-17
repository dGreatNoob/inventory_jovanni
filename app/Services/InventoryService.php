<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\InventoryMovement;
use App\Models\InventoryLocation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Update inventory quantity
     */
    public function updateInventory(int $productId, int $locationId, float $quantity, string $movementType, array $options = []): InventoryMovement
    {
        return DB::transaction(function () use ($productId, $locationId, $quantity, $movementType, $options) {
            // Get or create inventory record
            $inventory = ProductInventory::firstOrCreate(
                [
                    'product_id' => $productId,
                    'location_id' => $locationId,
                ],
                [
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                    'available_quantity' => 0,
                    'reorder_point' => 0,
                ]
            );

            // Update quantity
            $inventory->quantity += $quantity;
            $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
            $inventory->last_movement_at = now();
            $inventory->save();

            // Create movement record
            $movement = InventoryMovement::create([
                'product_id' => $productId,
                'location_id' => $locationId,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'unit_cost' => $options['unit_cost'] ?? 0,
                'total_cost' => $quantity * ($options['unit_cost'] ?? 0),
                'reference_type' => $options['reference_type'] ?? 'manual',
                'reference_id' => $options['reference_id'] ?? null,
                'notes' => $options['notes'] ?? null,
                'metadata' => $options['metadata'] ?? null,
                'created_by' => $options['created_by'] ?? auth()->id(),
            ]);

            // Check for low stock alerts
            $this->checkLowStockAlert($inventory);

            return $movement;
        });
    }

    /**
     * Reserve inventory quantity
     */
    public function reserveInventory(int $productId, int $locationId, float $quantity): bool
    {
        return DB::transaction(function () use ($productId, $locationId, $quantity) {
            $inventory = ProductInventory::where('product_id', $productId)
                ->where('location_id', $locationId)
                ->first();

            if (!$inventory || $inventory->available_quantity < $quantity) {
                return false;
            }

            $inventory->reserved_quantity += $quantity;
            $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
            $inventory->save();

            return true;
        });
    }

    /**
     * Unreserve inventory quantity
     */
    public function unreserveInventory(int $productId, int $locationId, float $quantity): void
    {
        DB::transaction(function () use ($productId, $locationId, $quantity) {
            $inventory = ProductInventory::where('product_id', $productId)
                ->where('location_id', $locationId)
                ->first();

            if ($inventory) {
                $inventory->reserved_quantity = max(0, $inventory->reserved_quantity - $quantity);
                $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
                $inventory->save();
            }
        });
    }

    /**
     * Transfer inventory between locations
     */
    public function transferInventory(int $productId, int $fromLocationId, int $toLocationId, float $quantity, array $options = []): array
    {
        return DB::transaction(function () use ($productId, $fromLocationId, $toLocationId, $quantity, $options) {
            // Stock out from source location
            $stockOutMovement = $this->updateInventory(
                $productId,
                $fromLocationId,
                -$quantity,
                'transfer_out',
                array_merge($options, [
                    'reference_type' => 'transfer',
                    'reference_id' => $options['transfer_id'] ?? null,
                    'notes' => "Transfer to location ID: {$toLocationId}",
                ])
            );

            // Stock in to destination location
            $stockInMovement = $this->updateInventory(
                $productId,
                $toLocationId,
                $quantity,
                'transfer_in',
                array_merge($options, [
                    'reference_type' => 'transfer',
                    'reference_id' => $options['transfer_id'] ?? null,
                    'notes' => "Transfer from location ID: {$fromLocationId}",
                ])
            );

            return [
                'stock_out' => $stockOutMovement,
                'stock_in' => $stockInMovement,
            ];
        });
    }

    /**
     * Get inventory summary by location
     */
    public function getInventoryByLocation(int $locationId = null): Collection
    {
        $query = ProductInventory::with(['product.category', 'product.supplier', 'location']);

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        return $query->get();
    }

    /**
     * Get low stock alerts
     */
    public function getLowStockAlerts(): Collection
    {
        return ProductInventory::with(['product.category', 'product.supplier', 'location'])
            ->whereColumn('available_quantity', '<=', 'reorder_point')
            ->where('available_quantity', '>', 0)
            ->get();
    }

    /**
     * Get out of stock alerts
     */
    public function getOutOfStockAlerts(): Collection
    {
        return ProductInventory::with(['product.category', 'product.supplier', 'location'])
            ->where('available_quantity', '<=', 0)
            ->get();
    }

    /**
     * Get inventory movement history
     */
    public function getMovementHistory(int $productId = null, int $locationId = null, int $days = 30): Collection
    {
        $query = InventoryMovement::with(['product', 'location', 'creator'])
            ->where('created_at', '>=', now()->subDays($days));

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get inventory valuation
     */
    public function getInventoryValuation(int $locationId = null): array
    {
        $query = ProductInventory::with(['product', 'location']);

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        $inventory = $query->get();

        $totalCost = $inventory->sum(function ($item) {
            return $item->available_quantity * $item->product->cost;
        });

        $totalRetail = $inventory->sum(function ($item) {
            return $item->available_quantity * $item->product->price;
        });

        return [
            'total_items' => $inventory->count(),
            'total_quantity' => $inventory->sum('available_quantity'),
            'total_cost_value' => $totalCost,
            'total_retail_value' => $totalRetail,
            'total_profit_potential' => $totalRetail - $totalCost,
            'inventory' => $inventory,
        ];
    }

    /**
     * Get monthly inventory summary
     */
    public function getMonthlyInventorySummary(int $year = null, int $month = null): array
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = now()->setYear($year)->setMonth($month)->endOfMonth();

        $movements = InventoryMovement::with(['product', 'location'])
            ->whereBetween('created_at', [$startDate, $endDate])
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
        ];

        return $summary;
    }

    /**
     * Check for low stock alerts
     */
    protected function checkLowStockAlert(ProductInventory $inventory): void
    {
        if ($inventory->available_quantity <= $inventory->reorder_point) {
            // You can implement notification logic here
            // For example, send email, create notification, etc.
            logger("Low stock alert for product {$inventory->product_id} at location {$inventory->location_id}");
        }
    }

    /**
     * Set reorder point for a product at a location
     */
    public function setReorderPoint(int $productId, int $locationId, float $reorderPoint): ProductInventory
    {
        $inventory = ProductInventory::where('product_id', $productId)
            ->where('location_id', $locationId)
            ->first();

        if (!$inventory) {
            $inventory = ProductInventory::create([
                'product_id' => $productId,
                'location_id' => $locationId,
                'quantity' => 0,
                'reserved_quantity' => 0,
                'available_quantity' => 0,
                'reorder_point' => $reorderPoint,
            ]);
        } else {
            $inventory->reorder_point = $reorderPoint;
            $inventory->save();
        }

        return $inventory;
    }

    /**
     * Get inventory statistics
     */
    public function getInventoryStats(): array
    {
        $totalLocations = InventoryLocation::active()->count();
        $totalInventoryRecords = ProductInventory::count();
        $lowStockAlerts = $this->getLowStockAlerts()->count();
        $outOfStockAlerts = $this->getOutOfStockAlerts()->count();
        $totalValuation = $this->getInventoryValuation();

        return [
            'total_locations' => $totalLocations,
            'total_inventory_records' => $totalInventoryRecords,
            'low_stock_alerts' => $lowStockAlerts,
            'out_of_stock_alerts' => $outOfStockAlerts,
            'total_quantity' => $totalValuation['total_quantity'],
            'total_cost_value' => $totalValuation['total_cost_value'],
            'total_retail_value' => $totalValuation['total_retail_value'],
            'profit_potential' => $totalValuation['total_profit_potential'],
        ];
    }
}
