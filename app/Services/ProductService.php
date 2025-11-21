<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\ProductPriceHistory;
use App\Models\InventoryMovement;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\ProductImage;
use App\Models\ProductColor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Search products with advanced filtering
     */
    public function searchProducts(string $query = '', array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $products = Product::with(['category', 'supplier', 'images', 'inventory'])
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
            $data['product_number'] = $this->normalizeProductNumber($data['product_number'] ?? null);

            $color = null;
            if (!empty($data['product_color_id'])) {
                $color = ProductColor::find($data['product_color_id']);
            }

            $productType = $data['product_type'] ?? 'regular';
            $data['price_note'] = $this->generateInitialPriceNote($productType);

            if (empty($data['barcode']) && !empty($data['product_number']) && $color && isset($data['price']) && is_numeric($data['price']) && (float) $data['price'] >= 0) {
                $data['barcode'] = $this->composeBarcode($data['product_number'], $color->code, $data['price']);
            }

            // Create the product
            $product = Product::create([
                'entity_id' => $data['entity_id'] ?? 1,
                'product_number' => $data['product_number'] ?? null,
                'product_color_id' => $data['product_color_id'] ?? null,
                'product_type' => $productType,
                'sku' => $data['sku'],
                'barcode' => $data['barcode'],
                'name' => $data['name'],
                'specs' => $data['specs'] ?? null,
                'category_id' => $data['category_id'],
                'remarks' => $data['remarks'] ?? null,
                'uom' => $data['uom'] ?? 'pcs',
                'supplier_id' => $data['supplier_id'],
                'supplier_code' => $data['supplier_code'] ?? null,
                'soft_card' => $data['soft_card'] ?? null,
                'price' => $data['price'],
                'original_price' => $data['original_price'] ?? null,
                'price_note' => $data['price_note'],
                'cost' => $data['cost'],
                'shelf_life_days' => $data['shelf_life_days'] ?? null,
                'pict_name' => $data['pict_name'] ?? null,
                'disabled' => $data['disabled'] ?? false,
                'created_by' => $data['created_by'] ?? (auth()->id() ?? 1),
                'updated_by' => auth()->id() ?? 1,
            ]);

            // Create initial inventory
            if (isset($data['initial_quantity']) && $data['initial_quantity'] > 0) {
                $this->createInitialInventory($product, $data['initial_quantity']);
            }

            ProductPriceHistory::create([
                'product_id' => $product->id,
                'old_price' => null,
                'new_price' => $product->price,
                'pricing_note' => $product->price_note,
                'changed_by' => auth()->id() ?? 1,
                'changed_at' => now(),
            ]);

            foreach (\App\Models\Branch::all() as $branch) {
                $branch->products()->attach($product->id, ['stock' => 0]);
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
            $originalPrice = $product->price;
            $originalNote = $product->price_note;

            $data['product_number'] = $this->normalizeProductNumber($data['product_number'] ?? $product->product_number);

            $color = null;
            if (array_key_exists('product_color_id', $data)) {
                $color = $data['product_color_id'] ? ProductColor::find($data['product_color_id']) : null;
            } else {
                $color = $product->color;
            }

            $updatePayload = array_merge($data, [
                'updated_by' => auth()->id(),
            ]);

            if (array_key_exists('product_number', $data) || array_key_exists('product_color_id', $data) || array_key_exists('price', $data)) {
                $updatedProductNumber = $data['product_number'] ?? $product->product_number;
                $colorCode = $color?->code;
                $price = $data['price'] ?? $product->price;

                if ($updatedProductNumber && $colorCode && $price) {
                    $composedBarcode = $this->composeBarcode($updatedProductNumber, $colorCode, $price);
                    if ($composedBarcode) {
                        $updatePayload['barcode'] = $composedBarcode;
                    }
                }
            }

            $priceChanged = false;
            if (array_key_exists('price', $data)) {
                $newPrice = $data['price'];
                $priceChanged = $this->hasPriceChanged($originalPrice, $newPrice);

                if ($priceChanged) {
                    $productType = $data['product_type'] ?? null;
                    $nextNote = $this->generateNextPriceNote($product, $productType);
                    $updatePayload['price_note'] = $nextNote;
                    $data['price_note'] = $nextNote;
                }
            }

            $product->update($updatePayload);

            if ($priceChanged) {
                ProductPriceHistory::create([
                    'product_id' => $product->id,
                    'old_price' => $originalPrice,
                    'new_price' => $data['price'],
                    'pricing_note' => $data['price_note'] ?? $originalNote,
                    'changed_by' => auth()->id() ?? 1,
                    'changed_at' => now(),
                ]);
            }

            return $product->fresh(['category', 'supplier', 'images', 'inventory']);
        });
    }

    protected function hasPriceChanged($oldPrice, $newPrice): bool
    {
        if (is_null($oldPrice) && is_null($newPrice)) {
            return false;
        }

        if (is_null($oldPrice) xor is_null($newPrice)) {
            return true;
        }

        return round((float) $oldPrice, 2) !== round((float) $newPrice, 2);
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
            'inventory',
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
        return Product::with(['category', 'supplier', 'inventory'])
            ->whereHas('inventory', function($q) {
                $q->where('quantity', '<', 10)
                  ->where('quantity', '>', 0);
            })
            ->active()
            ->get();
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStockProducts(): Collection
    {
        return Product::with(['category', 'supplier', 'inventory'])
            ->whereHas('inventory', fn($q) => $q->where('quantity', '<=', 0))
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
    protected function createInitialInventory(Product $product, float $quantity): ProductInventory
    {
        $inventory = ProductInventory::create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'reserved_quantity' => 0,
            'available_quantity' => $quantity,
            'reorder_point' => $quantity * 0.2, // 20% of initial quantity
            'last_movement_at' => now(),
        ]);

        // Create initial movement record
        InventoryMovement::create([
            'product_id' => $product->id,
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

    public function getProductPriceHistory(int $productId, int $limit = 50): Collection
    {
            return ProductPriceHistory::with(['changedBy:id,name'])
            ->where('product_id', $productId)
            ->orderByDesc('changed_at')
            ->limit($limit)
            ->get();
    }

    protected function composeBarcode(?string $productNumber, ?string $colorCode, $price = null): ?string
    {
        if (!$productNumber || !$colorCode) {
            return null;
        }

        $digitsProduct = substr(preg_replace('/\D/', '', $productNumber), 0, 6);
        $digitsColor = substr(preg_replace('/\D/', '', $colorCode), 0, 4);

        if ($digitsProduct === '' || $digitsColor === '') {
            return null;
        }

        $normalizedProduct = str_pad($digitsProduct, 6, '0', STR_PAD_LEFT);
        $normalizedColor = str_pad($digitsColor, 4, '0', STR_PAD_LEFT);

        // Format price: remove decimal point and zero-pad to 6 digits
        $priceDigits = '';
        if ($price !== null && $price !== '' && is_numeric($price)) {
            // Convert price to string, remove decimal point, and pad to 6 digits
            $priceValue = (float) $price;
            $priceInCents = (int) ($priceValue * 100);
            // Ensure price doesn't exceed 999999.99 (99999999 cents = 8 digits, but we need 6)
            // Take only last 6 digits if price is too large
            $priceDigits = substr(str_pad((string) $priceInCents, 6, '0', STR_PAD_LEFT), -6);
        } else {
            // If price is missing, pad with zeros
            $priceDigits = '000000';
        }

        return $normalizedProduct . $normalizedColor . $priceDigits;
    }

    protected function normalizeProductNumber(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = substr(preg_replace('/\D/', '', $value), 0, 6);

        if ($digits === '') {
            return null;
        }

        return str_pad($digits, 6, '0', STR_PAD_LEFT);
    }

    protected function generateInitialPriceNote(?string $productType): string
    {
        $prefix = $this->determinePricePrefix($productType, null);
        return $prefix . '1';
    }

    protected function generateNextPriceNote(Product $product, ?string $productType): string
    {
        $prefix = $this->determinePricePrefix($productType, $product->price_note);

        $latestHistory = ProductPriceHistory::where('product_id', $product->id)
            ->where('pricing_note', 'like', $prefix . '%')
            ->orderByDesc('changed_at')
            ->orderByDesc('id')
            ->first();

        $referenceNote = $latestHistory?->pricing_note;

        if (!$referenceNote && str_starts_with(strtoupper((string) $product->price_note), $prefix)) {
            $referenceNote = $product->price_note;
        }

        $currentIndex = $this->extractTrailingIndex($referenceNote);

        return $prefix . ($currentIndex + 1);
    }

    protected function determinePricePrefix(?string $productType, ?string $fallbackNote): string
    {
        $type = strtolower((string) $productType);

        if ($type === 'sale') {
            return 'SAL';
        }

        if ($type === 'regular') {
            return 'REG';
        }

        $fallback = strtoupper((string) $fallbackNote);
        if (str_starts_with($fallback, 'SAL')) {
            return 'SAL';
        }

        if (str_starts_with($fallback, 'REG')) {
            return 'REG';
        }

        return 'REG';
    }

    protected function extractTrailingIndex(?string $note): int
    {
        if (empty($note)) {
            return 0;
        }

        if (preg_match('/(\d+)$/', $note, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }
}
