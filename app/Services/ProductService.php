<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\InventoryMovement;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\InventoryLocation;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    /**
     * Search products with advanced filtering
     */
    public function searchProducts(string $query = '', array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $products = Product::with(['category', 'supplier', 'images', 'inventory.location'])
            ->active()
            ->when($query, fn($q) => $q->search($query))
            ->when($filters['category'] ?? null, fn($q) => $q->byCategory($filters['category']))
            ->when($filters['supplier'] ?? null, fn($q) => $q->bySupplier($filters['supplier']))
            ->when($filters['stock_level'] ?? null, function($q) use ($filters) {
                switch ($filters['stock_level']) {
                    case 'in_stock':
                        $q->whereHas('inventory', fn($inv) => $inv->inStock());
                        break;
                    case 'low_stock':
                        $q->whereHas('inventory', fn($inv) => $inv->lowStock());
                        break;
                    case 'out_of_stock':
                        $q->whereHas('inventory', fn($inv) => $inv->outOfStock());
                        break;
                }
            })
            ->when($filters['price_min'] ?? null, fn($q) => $q->where('price', '>=', $filters['price_min']))
            ->when($filters['price_max'] ?? null, fn($q) => $q->where('price', '<=', $filters['price_max']))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $products;
    }

    /**
     * Create a new product with inventory
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // Create the product
            $product = Product::create([
                'entity_id' => $data['entity_id'] ?? 1,
                'sku' => $data['sku'],
                'barcode' => $data['barcode'] ?? null,
                'name' => $data['name'],
                'specs' => $data['specs'] ?? null,
                'category_id' => $data['category_id'],
                'remarks' => $data['remarks'] ?? null,
                'uom' => $data['uom'] ?? 'pcs',
                'supplier_id' => $data['supplier_id'],
                'supplier_code' => $data['supplier_code'] ?? null,
                'price' => $data['price'],
                'price_note' => $data['price_note'] ?? null,
                'cost' => $data['cost'],
                'shelf_life_days' => $data['shelf_life_days'] ?? null,
                'pict_name' => $data['pict_name'] ?? null,
                'disabled' => $data['disabled'] ?? false,
                'created_by' => $data['created_by'] ?? (auth()->id() ?? 1),
                'updated_by' => auth()->id() ?? 1,
            ]);

            // Create initial inventory for default location
            if (isset($data['initial_quantity']) && $data['initial_quantity'] > 0) {
                $this->createInitialInventory($product, $data['initial_quantity'], $data['location_id'] ?? 1);
            }

            return $product->load(['category', 'supplier', 'images', 'inventory']);
        });
    }

    /**
     * Update product information
     */
    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update(array_merge($data, [
                'updated_by' => auth()->id(),
            ]));

            return $product->fresh(['category', 'supplier', 'images', 'inventory']);
        });
    }

    /**
     * Delete product (soft delete)
     */
    public function deleteProduct(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            // Soft delete the product
            $product->delete();
            
            // Optionally, you might want to handle inventory cleanup
            // For now, we'll keep inventory records for audit purposes
            
            return true;
        });
    }

    /**
     * Get product with full details
     */
    public function getProductDetails(int $productId): ?Product
    {
        return Product::with([
            'category',
            'supplier',
            'images',
            'inventory.location',
            'movements' => function($query) {
                $query->with(['location', 'creator'])->latest()->limit(10);
            }
        ])->find($productId);
    }

    /**
     * Get low stock products
     */
    public function getLowStockProducts(): Collection
    {
        return Product::with(['category', 'supplier', 'inventory.location'])
            ->whereHas('inventory', fn($q) => $q->lowStock())
            ->active()
            ->get();
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStockProducts(): Collection
    {
        return Product::with(['category', 'supplier', 'inventory.location'])
            ->whereHas('inventory', fn($q) => $q->outOfStock())
            ->active()
            ->get();
    }

    /**
     * Get top selling products
     */
    public function getTopSellingProducts(int $limit = 10, int $days = 30): Collection
    {
        return Product::with(['category', 'supplier'])
            ->whereHas('movements', function($query) use ($days) {
                $query->where('movement_type', 'sale')
                      ->where('created_at', '>=', now()->subDays($days));
            })
            ->withCount(['movements as sales_count' => function($query) use ($days) {
                $query->where('movement_type', 'sale')
                      ->where('created_at', '>=', now()->subDays($days));
            }])
            ->orderBy('sales_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get product analytics
     */
    public function getProductAnalytics(int $productId, int $days = 30): array
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return [];
        }

        $movements = $product->movements()
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        $totalSold = $movements->where('movement_type', 'sale')->sum('quantity');
        $totalPurchased = $movements->where('movement_type', 'purchase')->sum('quantity');
        $totalRevenue = $movements->where('movement_type', 'sale')->sum('total_cost');
        $totalCost = $movements->where('movement_type', 'purchase')->sum('total_cost');

        return [
            'product' => $product,
            'period_days' => $days,
            'total_sold' => abs($totalSold),
            'total_purchased' => $totalPurchased,
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'gross_profit' => $totalRevenue - $totalCost,
            'profit_margin' => $totalRevenue > 0 ? (($totalRevenue - $totalCost) / $totalRevenue) * 100 : 0,
            'movements' => $movements->groupBy('movement_type'),
        ];
    }

    /**
     * Create initial inventory for a product
     */
    protected function createInitialInventory(Product $product, float $quantity, int $locationId): ProductInventory
    {
        $inventory = ProductInventory::create([
            'product_id' => $product->id,
            'location_id' => $locationId,
            'quantity' => $quantity,
            'reserved_quantity' => 0,
            'available_quantity' => $quantity,
            'reorder_point' => $quantity * 0.2, // 20% of initial quantity
            'last_movement_at' => now(),
        ]);

        // Create initial movement record
        InventoryMovement::create([
            'product_id' => $product->id,
            'location_id' => $locationId,
            'movement_type' => 'adjustment',
            'quantity' => $quantity,
            'unit_cost' => $product->cost,
            'total_cost' => $quantity * $product->cost,
            'reference_type' => 'manual',
            'notes' => 'Initial inventory setup',
            'created_by' => auth()->id() ?? 1,
        ]);

        return $inventory;
    }

    /**
     * Get product statistics
     */
    public function getProductStats(): array
    {
        try {
            $totalProducts = Product::active()->count();
            $totalCategories = Category::active()->count();
            $totalSuppliers = Supplier::active()->count();
            $lowStockProducts = $this->getLowStockProducts()->count();
            $outOfStockProducts = $this->getOutOfStockProducts()->count();

            return [
                'total_products' => $totalProducts,
                'total_categories' => $totalCategories,
                'total_suppliers' => $totalSuppliers,
                'low_stock_products' => $lowStockProducts,
                'out_of_stock_products' => $outOfStockProducts,
                'in_stock_products' => $totalProducts - $lowStockProducts - $outOfStockProducts,
            ];
        } catch (\Exception $e) {
            // Return default values if there's an error
            return [
                'total_products' => 0,
                'total_categories' => 0,
                'total_suppliers' => 0,
                'low_stock_products' => 0,
                'out_of_stock_products' => 0,
                'in_stock_products' => 0,
            ];
        }
    }

    /**
     * Bulk update products
     */
    public function bulkUpdateProducts(array $productIds, array $updateData): int
    {
        return DB::transaction(function () use ($productIds, $updateData) {
            $updateData['updated_by'] = auth()->id();
            
            return Product::whereIn('id', $productIds)->update($updateData);
        });
    }

    /**
     * Bulk delete products
     */
    public function bulkDeleteProducts(array $productIds): int
    {
        return DB::transaction(function () use ($productIds) {
            return Product::whereIn('id', $productIds)->delete();
        });
    }
}
