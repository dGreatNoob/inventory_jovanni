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
    public $statusFilter = '';
    public $sortBy = 'sort_order';
    public $sortDirection = 'asc';
    public $perPage = 20;

    // Data
    // public $categories = []; // Removed - using computed property instead
    public $selectedCategories = [];
    public $showFilters = false;

    // Modals
    public $editingCategory = null;

    // Form Data
    public $form = [
        'entity_id' => 1, // Default entity for multi-tenant support
        'name' => '',
        'description' => '',
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
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function getCategoriesProperty()
    {
        $filters = [
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

    public function editCategory($categoryId)
    {
        $this->editingCategory = Category::findOrFail($categoryId);
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
            'form.sort_order' => 'nullable|integer|min:0',
            'form.slug' => 'required|string|max:255|unique:categories,slug' . ($this->editingCategory ? ',' . $this->editingCategory->id : ''),
            'form.is_active' => 'boolean',
        ], [
            'form.entity_id.required' => 'Entity ID is required.',
            'form.entity_id.integer' => 'Entity ID must be a number.',
            'form.entity_id.min' => 'Entity ID must be at least 1.',
            'form.name.required' => 'Category name is required.',
            'form.name.max' => 'Category name cannot exceed 255 characters.',
            'form.sort_order.integer' => 'Sort order must be a number.',
            'form.sort_order.min' => 'Sort order must be greater than or equal to 0.',
            'form.slug.required' => 'Slug is required.',
            'form.slug.unique' => 'This slug is already in use.',
        ]);

        try {
            if ($this->editingCategory) {
                // Update existing category
                $this->categoryService->updateCategory($this->editingCategory, $this->form);
                session()->flash('message', 'Category updated successfully.');
                // Reset form and editing state after update
                $this->resetForm();
                $this->editingCategory = null;
            } else {
                // Create new category - ensure parent_id is null for flat structure
                $formData = array_merge($this->form, ['parent_id' => null]);
                $this->categoryService->createCategory($formData);
                session()->flash('message', 'Category created successfully.');
                // Reset form fields for next creation
                $this->resetForm();
                $this->editingCategory = null;
            }
            $this->dispatch('close-modal', name: 'create-edit-category');

        } catch (\Exception $e) {
            session()->flash('error', 'Error saving category: ' . $e->getMessage());
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
        if (!auth()->user()->hasAnyPermission(['category view'])) {
            return view('livewire.pages.errors.403');
        }
        
        return view('livewire.pages.product-management.category-management', [
            'categories' => $this->categories,
            'stats' => $this->stats,
            'categoryTree' => $this->categoryTree,
        ]);
    }
}
