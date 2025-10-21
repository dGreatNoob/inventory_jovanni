<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of categories
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            $filters = $request->only(['parent_id', 'is_active']);
            $perPage = $request->get('per_page', 20);

            $categories = $this->categoryService->searchCategories($query, $filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'entity_id' => 'nullable|integer',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id',
                'sort_order' => 'nullable|integer|min:0',
                'slug' => 'nullable|string|max:255|unique:categories,slug',
                'is_active' => 'boolean',
            ]);

            $category = $this->categoryService->createCategory($validated);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category created successfully'
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
                'message' => 'Error creating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified category
     */
    public function show(int $id): JsonResponse
    {
        try {
            $category = $this->categoryService->getCategoryDetails($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id',
                'sort_order' => 'nullable|integer|min:0',
                'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
                'is_active' => 'boolean',
            ]);

            $updatedCategory = $this->categoryService->updateCategory($category, $validated);

            return response()->json([
                'success' => true,
                'data' => $updatedCategory,
                'message' => 'Category updated successfully'
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
                'message' => 'Error updating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $this->categoryService->deleteCategory($category);

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category tree structure
     */
    public function tree(): JsonResponse
    {
        try {
            $tree = $this->categoryService->getCategoryTree();

            return response()->json([
                'success' => true,
                'data' => $tree,
                'message' => 'Category tree retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving category tree: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->categoryService->getCategoryStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Category statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving category statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
