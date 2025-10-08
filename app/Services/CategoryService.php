<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * Search categories with filtering
     */
    public function searchCategories(string $query = '', array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $categories = Category::with(['parent', 'children', 'products'])
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->when(($filters['root_only'] ?? false) === true, fn($q) => $q->whereNull('parent_id'))
            ->when($filters['parent_id'] ?? null, fn($q) => $q->where('parent_id', $filters['parent_id']))
            ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', $filters['is_active']))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($perPage);

        return $categories;
    }

    /**
     * Create a new category
     */
    public function createCategory(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Set default values
            $data['entity_id'] = $data['entity_id'] ?? 1;
            $data['is_active'] = $data['is_active'] ?? true;
            $data['sort_order'] = $data['sort_order'] ?? 0;

            $category = Category::create($data);

            return $category->load(['parent', 'children']);
        });
    }

    /**
     * Update category information
     */
    public function updateCategory(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            // Only generate slug if name changed AND slug is not already provided
            if (isset($data['name']) && $data['name'] !== $category->name && empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            $category->update($data);

            return $category->fresh(['parent', 'children']);
        });
    }

    /**
     * Delete category (soft delete)
     */
    public function deleteCategory(Category $category): bool
    {
        return DB::transaction(function () use ($category) {
            // Check if category has products
            if ($category->products()->count() > 0) {
                throw new \Exception('Cannot delete category with existing products');
            }

            // Check if category has children
            if ($category->children()->count() > 0) {
                throw new \Exception('Cannot delete category with subcategories');
            }

            // Soft delete the category
            $category->delete();

            return true;
        });
    }

    /**
     * Get category with full details
     */
    public function getCategoryDetails(int $categoryId): ?Category
    {
        return Category::with([
            'parent',
            'children',
            'products' => function($query) {
                $query->with(['supplier', 'inventory.location'])->limit(10);
            }
        ])->find($categoryId);
    }

    /**
     * Get category tree structure
     */
    public function getCategoryTree(): Collection
    {
        $categories = Category::with(['children' => function($query) {
            $query->orderBy('sort_order')->orderBy('name');
        }])
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        return $this->buildTree($categories);
    }

    /**
     * Build hierarchical tree structure
     */
    protected function buildTree(Collection $categories): Collection
    {
        return $categories->map(function ($category) {
            $category->children = $this->buildTree($category->children);
            return $category;
        });
    }

    /**
     * Get categories for dropdown/select
     */
    public function getCategoriesForSelect(): Collection
    {
        return Category::active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);
    }

    /**
     * Get category breadcrumb
     */
    public function getCategoryBreadcrumb(int $categoryId): array
    {
        $category = Category::with('parent')->find($categoryId);
        
        if (!$category) {
            return [];
        }

        $breadcrumb = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumb, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
            ]);
            $current = $current->parent;
        }

        return $breadcrumb;
    }

    /**
     * Get category statistics
     */
    public function getCategoryStats(): array
    {
        $totalCategories = Category::count();
        $activeCategories = Category::active()->count();
        $inactiveCategories = $totalCategories - $activeCategories;
        $rootCategories = Category::whereNull('parent_id')->count();
        $subCategories = Category::whereNotNull('parent_id')->count();

        // Get categories with most products
        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(5)
            ->get();

        return [
            'total_categories' => $totalCategories,
            'active_categories' => $activeCategories,
            'inactive_categories' => $inactiveCategories,
            'root_categories' => $rootCategories,
            'sub_categories' => $subCategories,
            'top_categories' => $topCategories,
        ];
    }

    /**
     * Reorder categories
     */
    public function reorderCategories(array $categoryIds): bool
    {
        return DB::transaction(function () use ($categoryIds) {
            foreach ($categoryIds as $index => $categoryId) {
                Category::where('id', $categoryId)->update(['sort_order' => $index + 1]);
            }
            return true;
        });
    }

    /**
     * Move category to different parent
     */
    public function moveCategory(int $categoryId, int $newParentId = null): Category
    {
        return DB::transaction(function () use ($categoryId, $newParentId) {
            $category = Category::findOrFail($categoryId);
            
            // Prevent moving category to its own descendant
            if ($newParentId && $this->isDescendant($categoryId, $newParentId)) {
                throw new \Exception('Cannot move category to its own descendant');
            }

            $category->update(['parent_id' => $newParentId]);

            return $category->fresh(['parent', 'children']);
        });
    }

    /**
     * Check if category is descendant of another category
     */
    protected function isDescendant(int $categoryId, int $parentId): bool
    {
        $category = Category::find($categoryId);
        
        while ($category && $category->parent_id) {
            if ($category->parent_id === $parentId) {
                return true;
            }
            $category = $category->parent;
        }

        return false;
    }
}
