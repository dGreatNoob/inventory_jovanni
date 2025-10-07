<?php

namespace App\Services;

use App\Models\InventoryLocation;
use App\Models\ProductInventory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InventoryLocationService
{
    /**
     * Search inventory locations with filtering
     */
    public function searchLocations(string $query = '', array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $locations = InventoryLocation::with(['inventory.product.category', 'inventory.product.supplier'])
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('type', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->when($filters['type'] ?? null, fn($q) => $q->where('type', $filters['type']))
            ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', $filters['is_active']))
            ->orderBy('name')
            ->paginate($perPage);

        return $locations;
    }

    /**
     * Create a new inventory location
     */
    public function createLocation(array $data): InventoryLocation
    {
        return DB::transaction(function () use ($data) {
            // Set default values
            $data['entity_id'] = $data['entity_id'] ?? 1;
            $data['is_active'] = $data['is_active'] ?? true;

            $location = InventoryLocation::create($data);

            return $location->load(['inventory.product']);
        });
    }

    /**
     * Update inventory location information
     */
    public function updateLocation(InventoryLocation $location, array $data): InventoryLocation
    {
        return DB::transaction(function () use ($location, $data) {
            $location->update($data);

            return $location->fresh(['inventory.product']);
        });
    }

    /**
     * Delete inventory location (soft delete)
     */
    public function deleteLocation(InventoryLocation $location): bool
    {
        return DB::transaction(function () use ($location) {
            // Check if location has inventory
            if ($location->inventory()->count() > 0) {
                throw new \Exception('Cannot delete location with existing inventory');
            }

            // Soft delete the location
            $location->delete();

            return true;
        });
    }

    /**
     * Get location with full details
     */
    public function getLocationDetails(int $locationId): ?InventoryLocation
    {
        return InventoryLocation::with([
            'inventory' => function($query) {
                $query->with(['product.category', 'product.supplier', 'product.images'])
                      ->where('quantity', '>', 0)
                      ->orderBy('quantity', 'desc');
            }
        ])->find($locationId);
    }

    /**
     * Get locations for dropdown/select
     */
    public function getLocationsForSelect(): Collection
    {
        return InventoryLocation::active()
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'address']);
    }

    /**
     * Get location inventory
     */
    public function getLocationInventory(InventoryLocation $location, int $perPage = 20): LengthAwarePaginator
    {
        return $location->inventory()
            ->with(['product.category', 'product.supplier', 'product.images'])
            ->where('quantity', '>', 0)
            ->orderBy('quantity', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get location statistics
     */
    public function getLocationStats(): array
    {
        $totalLocations = InventoryLocation::count();
        $activeLocations = InventoryLocation::active()->count();
        $inactiveLocations = $totalLocations - $activeLocations;

        // Get locations by type
        $locationsByType = InventoryLocation::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get();

        // Get locations with most inventory
        $topLocations = InventoryLocation::withCount(['inventory as inventory_count' => function($query) {
            $query->where('quantity', '>', 0);
        }])
        ->orderBy('inventory_count', 'desc')
        ->limit(5)
        ->get();

        // Get total inventory value by location
        $inventoryValue = InventoryLocation::with(['inventory.product'])
            ->get()
            ->map(function ($location) {
                $totalValue = $location->inventory->sum(function ($item) {
                    return $item->quantity * $item->product->cost;
                });
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'total_value' => $totalValue,
                ];
            })
            ->sortByDesc('total_value')
            ->take(5);

        return [
            'total_locations' => $totalLocations,
            'active_locations' => $activeLocations,
            'inactive_locations' => $inactiveLocations,
            'locations_by_type' => $locationsByType,
            'top_locations' => $topLocations,
            'inventory_value_by_location' => $inventoryValue,
        ];
    }

    /**
     * Get location types
     */
    public function getLocationTypes(): array
    {
        return [
            'warehouse' => 'Warehouse',
            'store' => 'Store',
            'showroom' => 'Showroom',
            'office' => 'Office',
            'factory' => 'Factory',
            'distribution_center' => 'Distribution Center',
            'retail_outlet' => 'Retail Outlet',
            'online' => 'Online',
            'other' => 'Other',
        ];
    }

    /**
     * Get location inventory summary
     */
    public function getLocationInventorySummary(int $locationId): array
    {
        $location = InventoryLocation::findOrFail($locationId);
        
        $inventory = $location->inventory()
            ->with('product')
            ->where('quantity', '>', 0)
            ->get();

        $totalProducts = $inventory->count();
        $totalQuantity = $inventory->sum('quantity');
        $totalValue = $inventory->sum(function ($item) {
            return $item->quantity * $item->product->cost;
        });

        $lowStockItems = $inventory->filter(function ($item) {
            return $item->quantity <= $item->reorder_point;
        });

        return [
            'location' => $location,
            'total_products' => $totalProducts,
            'total_quantity' => $totalQuantity,
            'total_value' => $totalValue,
            'low_stock_items' => $lowStockItems->count(),
            'inventory' => $inventory,
        ];
    }

    /**
     * Get locations with low stock
     */
    public function getLocationsWithLowStock(): Collection
    {
        return InventoryLocation::whereHas('inventory', function($query) {
            $query->whereColumn('quantity', '<=', 'reorder_point')
                  ->where('quantity', '>', 0);
        })
        ->with(['inventory' => function($query) {
            $query->whereColumn('quantity', '<=', 'reorder_point')
                  ->where('quantity', '>', 0)
                  ->with('product');
        }])
        ->get();
    }

    /**
     * Get locations with no inventory
     */
    public function getEmptyLocations(): Collection
    {
        return InventoryLocation::whereDoesntHave('inventory', function($query) {
            $query->where('quantity', '>', 0);
        })
        ->orWhereHas('inventory', function($query) {
            $query->where('quantity', '<=', 0);
        })
        ->get();
    }

    /**
     * Transfer inventory between locations
     */
    public function transferInventory(int $fromLocationId, int $toLocationId, array $transfers): array
    {
        return DB::transaction(function () use ($fromLocationId, $toLocationId, $transfers) {
            $results = [];

            foreach ($transfers as $transfer) {
                $productId = $transfer['product_id'];
                $quantity = $transfer['quantity'];

                // Check if source location has enough inventory
                $sourceInventory = ProductInventory::where('product_id', $productId)
                    ->where('location_id', $fromLocationId)
                    ->first();

                if (!$sourceInventory || $sourceInventory->quantity < $quantity) {
                    throw new \Exception("Insufficient inventory for product {$productId} at source location");
                }

                // Perform transfer
                $sourceInventory->quantity -= $quantity;
                $sourceInventory->available_quantity = $sourceInventory->quantity - $sourceInventory->reserved_quantity;
                $sourceInventory->save();

                // Add to destination location
                $destInventory = ProductInventory::firstOrCreate(
                    [
                        'product_id' => $productId,
                        'location_id' => $toLocationId,
                    ],
                    [
                        'quantity' => 0,
                        'reserved_quantity' => 0,
                        'available_quantity' => 0,
                        'reorder_point' => 0,
                    ]
                );

                $destInventory->quantity += $quantity;
                $destInventory->available_quantity = $destInventory->quantity - $destInventory->reserved_quantity;
                $destInventory->save();

                $results[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'from_location' => $fromLocationId,
                    'to_location' => $toLocationId,
                ];
            }

            return $results;
        });
    }
}
