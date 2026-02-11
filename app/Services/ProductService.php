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
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Build the product search query with filters and sort (no pagination).
     * Used by searchProducts (paginated) and searchProductsForExport (collection).
     */
    private function buildProductSearchQuery(string $query = '', array $filters = []): \Illuminate\Database\Eloquent\Builder
    {
        $statusFilter = $filters['status'] ?? 'active';
        $products = Product::with([
            'category',
            'supplier',
            'inventory',
            'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')->orderBy('created_at', 'desc'),
        ])
            ->when($statusFilter === 'active', fn ($q) => $q->active())
            ->when($statusFilter === 'disabled', fn ($q) => $q->where('disabled', true))
            // 'all' = no status filter
            // Advanced search: product masterlist search with special handling
            // for product-number style queries (e.g. "LD-127").
            ->when($query, function ($q) use ($query) {
                $rawQuery = trim($query);
                if ($rawQuery === '') {
                    return;
                }

                // Detect a structured product-number style query:
                // - has both letters and digits
                // - and includes a separator (space, dash, underscore)
                $hasLetters = (bool) preg_match('/[A-Za-z]/', $rawQuery);
                $hasDigits  = (bool) preg_match('/\d/', $rawQuery);
                $hasSep     = (bool) preg_match('/[\s\-_]/', $rawQuery);
                $isProductNumberQuery = $hasLetters && $hasDigits && $hasSep;

                // Normalize query for prefix matching (remove spaces, lowercase)
                $normalizedQuery = strtolower(preg_replace('/\s+/', '', $rawQuery));

                // Build prefix segments for product_number matching
                $prefixes = [];
                $len = strlen($normalizedQuery);
                for ($i = 1; $i <= $len; $i++) {
                    $prefixes[] = substr($normalizedQuery, 0, $i);
                }

                // Tokenize original query (by spaces, hyphens, underscores) and normalize each token
                $queryTokens = preg_split('/[\s\-_]+/', $rawQuery);
                $queryTokens = array_values(array_filter(array_map(function ($t) {
                    $normalized = strtolower(trim($t));
                    return $normalized !== '' ? $normalized : null;
                }, $queryTokens)));

                $q->where(function ($base) use ($rawQuery, $normalizedQuery, $prefixes, $queryTokens, $isProductNumberQuery) {
                    // If this looks like a product-number query (e.g. "LD-127"),
                    // restrict results to product_number matches only:
                    // - first token is treated as a prefix
                    // - all tokens must appear (AND) in product_number.
                    if ($isProductNumberQuery && !empty($queryTokens)) {
                        $base->where(function ($qp) use ($queryTokens) {
                            foreach ($queryTokens as $index => $token) {
                                if (strlen($token) < 1) {
                                    continue;
                                }

                                if ($index === 0) {
                                    // First token: strict prefix on product_number
                                    $qp->where('product_number', 'like', $token . '%');
                                } else {
                                    // Subsequent tokens: must appear somewhere after
                                    $qp->where('product_number', 'like', '%' . $token . '%');
                                }
                            }
                        });

                        return;
                    }

                    // Generic / fallback behaviour (non-product-number queries)
                    $like = '%' . $rawQuery . '%';

                    // 1) Base "contains" search on primary fields (backwards compatible)
                    $base->where(function ($q1) use ($like) {
                        $q1->where('name', 'like', $like)
                            ->orWhere('sku', 'like', $like)
                            ->orWhere('barcode', 'like', $like)
                            ->orWhere('remarks', 'like', $like)
                            ->orWhere('supplier_code', 'like', $like)
                            ->orWhere('product_number', 'like', $like)
                            ->orWhereHas('supplier', function ($supplierQuery) use ($like) {
                                $supplierQuery->where('name', 'like', $like);
                            })
                            ->orWhereHas('category', function ($categoryQuery) use ($like) {
                                $categoryQuery->where('name', 'like', $like);
                            });
                    });

                    // 2) Prefix segmentation matching for product_number
                    if (!empty($normalizedQuery) && !empty($prefixes)) {
                        $base->orWhere(function ($q2) use ($prefixes) {
                            foreach ($prefixes as $prefix) {
                                if (strlen($prefix) < 2) {
                                    continue;
                                }
                                $q2->orWhere('product_number', 'like', $prefix . '%');
                            }
                        });
                    }

                    // 3) Token-based matching (all tokens must be present across fields)
                    if (!empty($queryTokens)) {
                        $base->orWhere(function ($q3) use ($queryTokens) {
                            foreach ($queryTokens as $token) {
                                if (strlen($token) < 1) {
                                    continue;
                                }
                                $likeToken = '%' . $token . '%';
                                // Each token must match at least one of the fields
                                $q3->where(function ($qt) use ($likeToken) {
                                    $qt->where('name', 'like', $likeToken)
                                        ->orWhere('sku', 'like', $likeToken)
                                        ->orWhere('barcode', 'like', $likeToken)
                                        ->orWhere('remarks', 'like', $likeToken)
                                        ->orWhere('supplier_code', 'like', $likeToken)
                                        ->orWhere('product_number', 'like', $likeToken)
                                        ->orWhereHas('supplier', function ($supplierQuery) use ($likeToken) {
                                            $supplierQuery->where('name', 'like', $likeToken);
                                        })
                                        ->orWhereHas('category', function ($categoryQuery) use ($likeToken) {
                                            $categoryQuery->where('name', 'like', $likeToken);
                                        });
                                });
                            }
                        });
                    }
                });
            })
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
            ->when(($filters['product_type'] ?? null) === 'placeholder', fn($q) => $q->placeholder());

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = in_array($filters['sort_direction'] ?? 'desc', ['asc', 'desc'], true)
            ? $filters['sort_direction']
            : 'desc';

        if ($sortBy === 'latest_stock_movement') {
            $lastMovementSub = InventoryMovement::query()
                ->selectRaw('MAX(created_at)')
                ->whereColumn('product_id', 'products.id');
            $products = $products
                ->select('products.*')
                ->selectSub($lastMovementSub, 'last_movement_at')
                ->orderByRaw('last_movement_at IS NULL')
                ->orderBy('last_movement_at', $sortDirection);
        } else {
            $allowedSortColumns = ['created_at', 'name', 'price'];
            if (in_array($sortBy, $allowedSortColumns, true)) {
                $products = $products->orderBy('products.' . $sortBy, $sortDirection);
            } else {
                $products = $products->orderBy('products.created_at', 'desc');
            }
        }

        return $products;
    }

    /**
     * Search products with advanced filtering (paginated).
     */
    public function searchProducts(string $query = '', array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->buildProductSearchQuery($query, $filters)->paginate($perPage);
    }

    /**
     * Get all products matching the same filters as the masterlist (for export).
     * Respects a max count to avoid huge exports.
     */
    public function searchProductsForExport(string $query = '', array $filters = [], int $max = 2000): Collection
    {
        return $this->buildProductSearchQuery($query, $filters)->limit($max)->get();
    }

    /**
     * Create a new product with inventory
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $data['product_number'] = isset($data['product_number']) ? substr(trim((string) $data['product_number']), 0, 6) : null;

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
                'price_effective_date' => !empty($data['price_effective_date']) ? $data['price_effective_date'] : null,
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
     * Create a minimal placeholder product for PO lines when only supplier code is known.
     * Used when adding items by supplier code and no matching product exists.
     */
    public function createPlaceholderProduct(array $data): Product
    {
        $supplierCode = trim($data['supplier_code'] ?? '');
        $supplierId = (int) ($data['supplier_id'] ?? 0);
        $unitPrice = (float) ($data['unit_price'] ?? 0);

        if (!$supplierCode || !$supplierId) {
            throw new \InvalidArgumentException('supplier_id and supplier_code are required for placeholder product.');
        }

        $defaultCategoryId = config('products.default_placeholder_category_id')
            ?? Category::whereNotNull('parent_id')->orderBy('id')->value('id');

        if (!$defaultCategoryId) {
            throw new \RuntimeException('No default category available for placeholder products. Configure default_placeholder_category_id or add leaf categories.');
        }

        $sanitized = preg_replace('/[^A-Za-z0-9_-]/', '', $supplierCode) ?: 'SC';
        $sku = 'PND-' . $supplierId . '-' . substr($sanitized, 0, 20) . '-' . strtolower(\Illuminate\Support\Str::random(6));

        $productColorId = array_key_exists('product_color_id', $data) && $data['product_color_id']
            ? (int) $data['product_color_id']
            : null;
        $sellingPrice = array_key_exists('price', $data)
            ? (float) $data['price']
            : $unitPrice;

        $payload = [
            'sku' => $sku,
            'barcode' => null,
            'name' => 'Pending: ' . $supplierCode,
            'category_id' => $defaultCategoryId,
            'supplier_id' => $supplierId,
            'supplier_code' => $supplierCode,
            'price' => $sellingPrice,
            'cost' => $unitPrice,
            'product_number' => null,
            'product_color_id' => $productColorId,
            'product_type' => 'placeholder',
            'remarks' => 'Auto-created from PO; complete details in Product Management',
            'uom' => 'pcs',
        ];

        return $this->createProduct(array_merge($payload, [
            'product_number' => null,
            'product_color_id' => $productColorId,
            'barcode' => null,
        ]));
    }

    /**
     * Update product information
     */
    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $originalPrice = $product->price;
            $originalNote = $product->price_note;

            $pn = $data['product_number'] ?? $product->product_number;
            $data['product_number'] = $pn !== null ? substr(trim((string) $pn), 0, 6) : null;

            $color = null;
            if (array_key_exists('product_color_id', $data)) {
                $color = $data['product_color_id'] ? ProductColor::find($data['product_color_id']) : null;
            } else {
                $color = $product->color;
            }

            $effectiveDate = !empty($data['price_effective_date'])
                ? Carbon::parse($data['price_effective_date'])->startOfDay()
                : null;
            $isFutureEffective = $effectiveDate && $effectiveDate->gt(Carbon::today());

            $updatePayload = array_merge($data, [
                'updated_by' => auth()->id(),
            ]);

            // Barcode: when effective date is future we keep current price for barcode; otherwise use form price
            $priceForBarcode = $isFutureEffective ? $product->price : ($data['price'] ?? $product->price);
            if (array_key_exists('product_number', $data) || array_key_exists('product_color_id', $data) || array_key_exists('price', $data)) {
                $updatedProductNumber = $data['product_number'] ?? $product->product_number;
                $colorCode = $color?->code;

                if ($updatedProductNumber && $colorCode && $priceForBarcode) {
                    $composedBarcode = $this->composeBarcode($updatedProductNumber, $colorCode, $priceForBarcode);
                    if ($composedBarcode) {
                        $updatePayload['barcode'] = $composedBarcode;
                    }
                }
            }

            $priceChanged = false;
            if (array_key_exists('price', $data)) {
                $newPrice = $data['price'];
                $priceChanged = $this->hasPriceChanged($originalPrice, $newPrice);
            }

            if ($isFutureEffective) {
                // Store new price/note as pending; do not change active price/price_note/product_type yet
                $productType = $data['product_type'] ?? null;
                $pendingNote = $this->generateNextPriceNote($product, $productType);
                $updatePayload['pending_price'] = $data['price'] ?? null;
                $updatePayload['pending_price_note'] = $pendingNote;
                $updatePayload['price_effective_date'] = $effectiveDate;
                unset($updatePayload['price'], $updatePayload['price_note'], $updatePayload['product_type']);
                $product->update($updatePayload);
                // No ProductPriceHistory until the scheduled command applies the pending price
            } else {
                // Apply price immediately and clear any pending
                $updatePayload['pending_price'] = null;
                $updatePayload['pending_price_note'] = null;
                $updatePayload['price_effective_date'] = null;

                if ($priceChanged) {
                    $productType = $data['product_type'] ?? null;
                    $nextNote = $this->generateNextPriceNote($product, $productType);
                    $updatePayload['price_note'] = $nextNote;
                    $data['price_note'] = $nextNote;
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
     * Apply pending price/price_note for a single product if its price_effective_date is today or in the past.
     * Call this when loading a product for display or edit so the current price is up to date.
     * Returns true if the product was updated, false otherwise.
     */
    public function applyDuePendingPriceForProduct(Product $product): bool
    {
        if ($product->price_effective_date === null) {
            return false;
        }
        if ($product->price_effective_date->isFuture()) {
            return false;
        }
        if ($product->pending_price === null && $product->pending_price_note === null) {
            return false;
        }

        $oldPrice = $product->price;
        $newPrice = $product->pending_price ?? $product->price;
        $newNote = $product->pending_price_note ?? $product->price_note;
        // Update product_type based on the new price_note (SAL = sale, REG = regular)
        $newProductType = str_starts_with(strtoupper((string) $newNote), 'SAL') ? 'sale' : 'regular';

        DB::transaction(function () use ($product, $oldPrice, $newPrice, $newNote, $newProductType) {
            $product->update([
                'price' => $newPrice,
                'price_note' => $newNote,
                'product_type' => $newProductType,
                'pending_price' => null,
                'pending_price_note' => null,
                'price_effective_date' => null,
                'updated_by' => auth()->id(),
            ]);

            ProductPriceHistory::create([
                'product_id' => $product->id,
                'old_price' => $oldPrice,
                'new_price' => $newPrice,
                'pricing_note' => $newNote,
                'changed_by' => auth()->id() ?? 1,
                'changed_at' => now(),
            ]);
        });

        return true;
    }

    /**
     * Apply pending price/price_note for products whose price_effective_date is today or in the past.
     * Returns the number of products updated. Use for manual bulk apply (e.g. artisan command).
     */
    public function applyDuePendingPrices(): int
    {
        $today = Carbon::today();
        $products = Product::whereNotNull('price_effective_date')
            ->where('price_effective_date', '<=', $today)
            ->where(function ($q) {
                $q->whereNotNull('pending_price')->orWhereNotNull('pending_price_note');
            })
            ->get();

        $count = 0;
        foreach ($products as $product) {
            if ($this->applyDuePendingPriceForProduct($product->fresh())) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get product with full details.
     * Applies any due pending price (effective date <= today) so the returned product has current price.
     */
    public function getProductDetails(int $productId): ?Product
    {
        $product = Product::with([
            'category',
            'supplier',
            'images',
            'inventory',
            'movements' => function($query) {
                $query->with(['location', 'creator'])->latest()->limit(10);
            }
        ])->find($productId);

        if ($product) {
            $this->applyDuePendingPriceForProduct($product);
            $product->refresh();
        }

        return $product;
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
