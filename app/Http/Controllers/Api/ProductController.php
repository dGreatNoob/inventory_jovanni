<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of products
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            $filters = $request->only(['category', 'supplier', 'stock_level', 'price_min', 'price_max']);
            $perPage = $request->get('per_page', 20);

            $products = $this->productService->searchProducts($query, $filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'sku' => 'required|string|max:255|unique:products,sku',
                'barcode' => 'nullable|string|max:255|unique:products,barcode',
                'name' => 'required|string|max:255',
                'specs' => 'nullable|array',
                'category_id' => 'required|exists:categories,id',
                'remarks' => 'nullable|string',
                'uom' => 'required|string|max:255',
                'supplier_id' => 'required|exists:suppliers,id',
                'supplier_code' => 'nullable|string|max:255',
                'price' => 'required|numeric|min:0',
                'price_note' => 'nullable|string',
                'cost' => 'required|numeric|min:0',
                'shelf_life_days' => 'nullable|integer|min:0',
                'pict_name' => 'nullable|string|max:255',
                'disabled' => 'boolean',
                'initial_quantity' => 'nullable|numeric|min:0',
                'location_id' => 'nullable|exists:inventory_locations,id',
            ]);

            $product = $this->productService->createProduct($validated);

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product created successfully'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductDetails($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $validated = $request->validate([
                'sku' => 'sometimes|required|string|max:255|unique:products,sku,' . $id,
                'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $id,
                'name' => 'sometimes|required|string|max:255',
                'specs' => 'nullable|array',
                'category_id' => 'sometimes|required|exists:categories,id',
                'remarks' => 'nullable|string',
                'uom' => 'sometimes|required|string|max:255',
                'supplier_id' => 'sometimes|required|exists:suppliers,id',
                'supplier_code' => 'nullable|string|max:255',
                'price' => 'sometimes|required|numeric|min:0',
                'price_note' => 'nullable|string',
                'cost' => 'sometimes|required|numeric|min:0',
                'shelf_life_days' => 'nullable|integer|min:0',
                'pict_name' => 'nullable|string|max:255',
                'disabled' => 'boolean',
            ]);

            $updatedProduct = $this->productService->updateProduct($product, $validated);

            return response()->json([
                'success' => true,
                'data' => $updatedProduct,
                'message' => 'Product updated successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $this->productService->deleteProduct($product);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product analytics
     */
    public function analytics(int $id, Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 30);
            $analytics = $this->productService->getProductAnalytics($id, $days);

            if (empty($analytics)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'message' => 'Product analytics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get low stock products
     */
    public function lowStock(): JsonResponse
    {
        try {
            $products = $this->productService->getLowStockProducts();

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Low stock products retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving low stock products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get out of stock products
     */
    public function outOfStock(): JsonResponse
    {
        try {
            $products = $this->productService->getOutOfStockProducts();

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Out of stock products retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving out of stock products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top selling products
     */
    public function topSelling(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $days = $request->get('days', 30);
            
            $products = $this->productService->getTopSellingProducts($limit, $days);

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Top selling products retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving top selling products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->productService->getProductStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Product statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update products
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_ids' => 'required|array|min:1',
                'product_ids.*' => 'integer|exists:products,id',
                'update_data' => 'required|array',
            ]);

            $updated = $this->productService->bulkUpdateProducts(
                $validated['product_ids'],
                $validated['update_data']
            );

            return response()->json([
                'success' => true,
                'data' => ['updated_count' => $updated],
                'message' => "Successfully updated {$updated} products"
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error bulk updating products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete products
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_ids' => 'required|array|min:1',
                'product_ids.*' => 'integer|exists:products,id',
            ]);

            $deleted = $this->productService->bulkDeleteProducts($validated['product_ids']);

            return response()->json([
                'success' => true,
                'data' => ['deleted_count' => $deleted],
                'message' => "Successfully deleted {$deleted} products"
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error bulk deleting products: ' . $e->getMessage()
            ], 500);
        }
    }
}