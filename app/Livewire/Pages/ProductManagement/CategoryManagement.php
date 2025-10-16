<?php

namespace App\Livewire\Pages\ProductManagement;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Support\Str;

#[Layout('components.layouts.app')]
#[Title('Category Management')]
class CategoryManagement extends Component
{
    use WithPagination;

    // Search and Filters
    public $search = '';
    public $parentFilter = '';
    public $statusFilter = '';
    public $sortBy = 'sort_order';
    public $sortDirection = 'asc';
    public $perPage = 20;

    // Data
    // public $categories = []; // Removed - using computed property instead
    public $parentCategories = [];
    public $selectedCategories = [];
    public $showFilters = false;

    // Modals
    public $editingCategory = null;
    public $creationMode = null; // 'root' or 'subcategory'

    // Form Data
    public $form = [
        'entity_id' => 1, // Default entity for multi-tenant support
        'name' => '',
        'description' => '',
        'parent_id' => '',
        'sort_order' => 0,
        'slug' => '',
        'is_active' => true,
    ];

    // Bulk Actions
    public $bulkAction = '';
    public $bulkActionValue = '';

    protected $categoryService;

    public function boot(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function mount()
    {
        // Ensure CategoryService is available
        if (!$this->categoryService) {
            $this->categoryService = app(CategoryService::class);
        }
        $this->loadFilters();
    }

    public function loadFilters()
    {
        $this->parentCategories = Category::whereNull('parent_id')
            ->where('is_active', true)  // Only show active categories
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFormName()
    {
        // Auto-generate slug from name if slug is empty
        if (empty($this->form['slug']) && !empty($this->form['name'])) {
            $this->form['slug'] = Str::slug($this->form['name']);
        }
    }

    public function updatedParentFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->parentFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function getCategoriesProperty()
    {
        $filters = [
            'root_only' => $this->parentFilter === 'root',
            'parent_id' => is_numeric($this->parentFilter) ? (int) $this->parentFilter : null,
            'is_active' => $this->statusFilter === 'active' ? true : ($this->statusFilter === 'inactive' ? false : null),
        ];

        return $this->categoryService->searchCategories(
            $this->search,
            array_filter($filters),
            $this->perPage
        );
    }

    public function getStatsProperty()
    {
        return $this->categoryService->getCategoryStats();
    }

    public function getCategoryTreeProperty()
    {
        return $this->categoryService->getCategoryTree();
    }

    public function createCategory()
    {
        $this->resetForm();
        $this->editingCategory = null;
    }

    public function createRootCategory()
    {
        $this->resetForm();
        $this->editingCategory = null;
        $this->creationMode = 'root';
        $this->form['parent_id'] = null; // Ensure it's a root category
        $this->form['sort_order'] = 0; // Default sort order for root categories
    }

    public function createSubcategory()
    {
        $this->resetForm();
        $this->editingCategory = null;
        $this->creationMode = 'subcategory';
        $this->form['parent_id'] = ''; // Ensure it's empty for subcategory creation
    }

    public function editCategory($categoryId)
    {
        $this->editingCategory = Category::findOrFail($categoryId);
        $this->creationMode = null; // Clear creation mode when editing
        $this->loadCategoryData();
    }

    public function deleteCategory($categoryId)
    {
        $this->editingCategory = Category::findOrFail($categoryId);
    }

    public function confirmDelete()
    {
        if ($this->editingCategory) {
            try {
                $this->categoryService->deleteCategory($this->editingCategory);
                $this->editingCategory = null;
                $this->dispatch('close-modal', name: 'delete-category');
                session()->flash('message', 'Category deleted successfully.');
            } catch (\Exception $e) {
                session()->flash('error', 'Error deleting category: ' . $e->getMessage());
            }
        }
    }

    public function toggleCategorySelection($categoryId)
    {
        if (in_array($categoryId, $this->selectedCategories)) {
            $this->selectedCategories = array_diff($this->selectedCategories, [$categoryId]);
        } else {
            $this->selectedCategories[] = $categoryId;
        }
    }

    public function selectAllCategories()
    {
        $this->selectedCategories = $this->categories->pluck('id')->toArray();
    }

    public function clearSelection()
    {
        $this->selectedCategories = [];
    }


    public function performBulkAction()
    {
        if (empty($this->selectedCategories) || empty($this->bulkAction)) {
            return;
        }

        try {
            switch ($this->bulkAction) {
                case 'delete':
                    // Check if any root categories with subcategories are selected
                    $rootCategoriesWithChildren = Category::whereIn('id', $this->selectedCategories)
                        ->whereNull('parent_id')
                        ->whereHas('children')
                        ->count();
                    
                    if ($rootCategoriesWithChildren > 0) {
                        session()->flash('error', 'Cannot delete root categories that have subcategories. Please delete all subcategories first, or deselect root categories with subcategories and try again.');
                        return;
                    }
                    
                    foreach ($this->selectedCategories as $categoryId) {
                        $category = Category::findOrFail($categoryId);
                        $this->categoryService->deleteCategory($category);
                    }
                    session()->flash('message', 'Selected categories deleted successfully.');
                    break;
                case 'activate':
                    Category::whereIn('id', $this->selectedCategories)->update(['is_active' => true]);
                    session()->flash('message', 'Selected categories activated successfully.');
                    break;
                case 'deactivate':
                    Category::whereIn('id', $this->selectedCategories)->update(['is_active' => false]);
                    session()->flash('message', 'Selected categories deactivated successfully.');
                    break;
                case 'update_parent':
                    if ($this->bulkActionValue) {
                        Category::whereIn('id', $this->selectedCategories)->update(['parent_id' => $this->bulkActionValue]);
                        session()->flash('message', 'Selected categories moved successfully.');
                    }
                    break;
            }

            $this->clearSelection();
            $this->bulkAction = '';
            $this->bulkActionValue = '';
            $this->dispatch('close-modal', name: 'bulk-actions-category');

        } catch (\Exception $e) {
            session()->flash('error', 'Error performing bulk action: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->form = [
            'entity_id' => 1, // Default entity for multi-tenant support
            'name' => '',
            'description' => '',
            'parent_id' => '',
            'sort_order' => 0,
            'slug' => '',
            'is_active' => true,
        ];
    }

    public function loadCategoryData()
    {
        if ($this->editingCategory) {
            $this->form = [
                'entity_id' => $this->editingCategory->entity_id,
                'name' => $this->editingCategory->name,
                'description' => $this->editingCategory->description,
                'parent_id' => $this->editingCategory->parent_id,
                'sort_order' => $this->editingCategory->sort_order,
                'slug' => $this->editingCategory->slug,
                'is_active' => $this->editingCategory->is_active,
            ];
        }
    }

    public function saveCategory()
    {
        // Generate slug if not provided before validation
        if (empty($this->form['slug'])) {
            $this->form['slug'] = Str::slug($this->form['name']);
        }

        $this->validate([
            'form.entity_id' => 'required|integer|min:1',
            'form.name' => 'required|string|max:255',
            'form.description' => 'nullable|string',
            'form.parent_id' => 'nullable|exists:categories,id',
            'form.sort_order' => 'nullable|integer|min:0',
            'form.slug' => 'required|string|max:255|unique:categories,slug' . ($this->editingCategory ? ',' . $this->editingCategory->id : ''),
            'form.is_active' => 'boolean',
        ], [
            'form.entity_id.required' => 'Entity ID is required.',
            'form.entity_id.integer' => 'Entity ID must be a number.',
            'form.entity_id.min' => 'Entity ID must be at least 1.',
            'form.name.required' => 'Category name is required.',
            'form.name.max' => 'Category name cannot exceed 255 characters.',
            'form.parent_id.exists' => 'Selected parent category does not exist.',
            'form.sort_order.integer' => 'Sort order must be a number.',
            'form.sort_order.min' => 'Sort order must be greater than or equal to 0.',
            'form.slug.required' => 'Slug is required.',
            'form.slug.unique' => 'This slug is already in use.',
        ]);

        try {
            // Handle parent_id properly - convert empty string to null for root categories
            if ($this->form['parent_id'] === '') {
                $this->form['parent_id'] = null;
            }

            if ($this->editingCategory) {
                // Update existing category
                $this->categoryService->updateCategory($this->editingCategory, $this->form);
                session()->flash('message', 'Category updated successfully.');
                // Reset form and editing state after update
                $this->resetForm();
                $this->editingCategory = null;
                $this->creationMode = null;
            } else {
                // Create new category
                $this->categoryService->createCategory($this->form);
                session()->flash('message', 'Category created successfully.');
                // Reset form fields for next creation but keep the creation mode
                $this->resetForm();
                $this->editingCategory = null;
            }
            $this->dispatch('close-modal', name: 'create-edit-category');
            $this->loadFilters();

        } catch (\Exception $e) {
            session()->flash('error', 'Error saving category: ' . $e->getMessage());
        }
    }

    public function moveCategory($categoryId, $newParentId = null)
    {
        try {
            $category = Category::findOrFail($categoryId);
            $this->categoryService->moveCategory($categoryId, $newParentId);
            session()->flash('message', 'Category moved successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error moving category: ' . $e->getMessage());
        }
    }

    public function reorderCategories($categoryIds)
    {
        try {
            $this->categoryService->reorderCategories($categoryIds);
            session()->flash('message', 'Categories reordered successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error reordering categories: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.product-management.category-management', [
            'categories' => $this->categories,
            'stats' => $this->stats,
            'categoryTree' => $this->categoryTree,
        ]);
    }
}
