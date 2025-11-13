<?php

namespace App\Livewire\Pages\ProductManagement;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\ProductImage;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
#[Title('Product Management')]
class Index extends Component
{
    use WithPagination;

    // Search and Filters
    public $search = '';
    public $categoryFilter = '';
    public $supplierFilter = '';
    public $stockLevelFilter = '';
    public $priceMin = '';
    public $priceMax = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 20;
    public $viewMode = 'grid'; // grid or table

    // Data
    // public $products = []; // Removed - using computed property instead
    public $categories = [];
    public $suppliers = [];
    public $selectedProducts = [];
    public $showFilters = false;

    // Modals
    public $editingProduct = null;
    public $isEditMode = false;
    // Viewer state for product details modal
    public $viewerProductId = null;
    public $viewerImages = [];
    public $viewerIndex = 0;
    public $viewingImage = null;

    // Form Data
    public $form = [
        'name' => '',
        'sku' => '',
        'barcode' => '',
        'remarks' => '',
        'root_category_id' => '',  // For cascading dropdown
        'category_id' => '',
        'supplier_id' => '',
        'supplier_code' => '',
        'price' => '',
        'price_note' => '',
        'cost' => '',
        'uom' => 'pcs',
        'shelf_life_days' => '',
        'disabled' => false,
        'initial_quantity' => '',
    ];
    
    // Filtered subcategories based on root selection
    public $filteredSubcategories = [];

    // Bulk Actions
    public $bulkAction = '';
    public $bulkActionValue = '';

    protected $productService;

    public function boot(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function mount()
    {
        $this->loadFilters();
    }

    public function loadFilters()
    {
        // Load hierarchical categories with parent information
        $this->categories = Category::with('parent')
            ->active()
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id', 'sort_order']);

        $this->suppliers = Supplier::active()
            ->orderBy('name')
            ->get(['id', 'name']);

    }
    
    // Get root categories only (for first dropdown)
    public function getRootCategoriesProperty()
    {
        return Category::whereNull('parent_id')
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);
    }
    
    // Update subcategories when root category changes
    public function updatedFormRootCategoryId($value)
    {
        if ($value) {
            $this->filteredSubcategories = Category::where('parent_id', $value)
                ->active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name'])
                ->toArray();
        } else {
            $this->filteredSubcategories = [];
            $this->form['category_id'] = '';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedSupplierFilter()
    {
        $this->resetPage();
    }

    public function updatedStockLevelFilter()
    {
        $this->resetPage();
    }

    public function updatedPriceMin()
    {
        $this->resetPage();
    }

    public function updatedPriceMax()
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

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'table' : 'grid';
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->supplierFilter = '';
        $this->stockLevelFilter = '';
        $this->priceMin = '';
        $this->priceMax = '';
        $this->resetPage();
    }

    public function getProductsProperty()
    {
        try {
            $filters = [
                'category' => $this->categoryFilter,
                'supplier' => $this->supplierFilter,
                'stock_level' => $this->stockLevelFilter,
                'price_min' => $this->priceMin,
                'price_max' => $this->priceMax,
            ];

            return $this->productService->searchProducts(
                $this->search,
                array_filter($filters),
                $this->perPage
            );
        } catch (\Exception $e) {
            // Return empty paginated result if there's an error
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                $this->perPage,
                1,
                ['path' => request()->url()]
            );
        }
    }

    public function getStatsProperty()
    {
        try {
            return $this->productService->getProductStats();
        } catch (\Exception $e) {
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

    public function createProduct()
    {
        $this->resetForm();
        $this->editingProduct = null;
        $this->isEditMode = false;
    }

    public function editProduct($productId)
    {
        $this->editingProduct = Product::findOrFail($productId);
        $this->loadProductData();
        $this->isEditMode = true;
    }

    public function deleteProduct($productId)
    {
        $this->editingProduct = Product::findOrFail($productId);
    }

    public function confirmDelete()
    {
        if ($this->editingProduct) {
            $this->productService->deleteProduct($this->editingProduct);
            $this->editingProduct = null;
            $this->dispatch('close-modal', name: 'delete-product');
            session()->flash('message', 'Product deleted successfully.');
        }
    }

    public function toggleProductSelection($productId)
    {
        if (in_array($productId, $this->selectedProducts)) {
            $this->selectedProducts = array_diff($this->selectedProducts, [$productId]);
        } else {
            $this->selectedProducts[] = $productId;
        }
    }

    public function selectAllProducts()
    {
        $this->selectedProducts = $this->products->pluck('id')->toArray();
    }

    public function clearSelection()
    {
        $this->selectedProducts = [];
    }


    public function performBulkAction()
    {
        if (empty($this->selectedProducts) || empty($this->bulkAction)) {
            return;
        }

        try {
            switch ($this->bulkAction) {
                case 'delete':
                    $this->productService->bulkDeleteProducts($this->selectedProducts);
                    session()->flash('message', 'Selected products deleted successfully.');
                    break;
                case 'update_category':
                    if ($this->bulkActionValue) {
                        $this->productService->bulkUpdateProducts(
                            $this->selectedProducts,
                            ['category_id' => $this->bulkActionValue]
                        );
                        session()->flash('message', 'Selected products updated successfully.');
                    }
                    break;
                case 'update_supplier':
                    if ($this->bulkActionValue) {
                        $this->productService->bulkUpdateProducts(
                            $this->selectedProducts,
                            ['supplier_id' => $this->bulkActionValue]
                        );
                        session()->flash('message', 'Selected products updated successfully.');
                    }
                    break;
                case 'disable':
                    $this->productService->bulkUpdateProducts(
                        $this->selectedProducts,
                        ['disabled' => true]
                    );
                    session()->flash('message', 'Selected products disabled successfully.');
                    break;
                case 'enable':
                    $this->productService->bulkUpdateProducts(
                        $this->selectedProducts,
                        ['disabled' => false]
                    );
                    session()->flash('message', 'Selected products enabled successfully.');
                    break;
            }

            $this->clearSelection();
            $this->bulkAction = '';
            $this->bulkActionValue = '';
            $this->dispatch('close-modal', name: 'bulk-actions');

        } catch (\Exception $e) {
            session()->flash('error', 'Error performing bulk action: ' . $e->getMessage());
        }
    }

    public function exportProducts()
    {
        // Open the print view in a new tab to immediately trigger print, while keeping the page state
        return redirect()->away(route('product-management.print'));
    }

    public function refreshProducts()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->form = [
            'name' => '',
            'sku' => '',
            'barcode' => '',
            'remarks' => '',
            'root_category_id' => '',
            'category_id' => '',
            'supplier_id' => '',
            'supplier_code' => '',
            'price' => '',
            'price_note' => '',
            'cost' => '',
            'uom' => 'pcs',
            'shelf_life_days' => '',
            'disabled' => false,
            'initial_quantity' => '',
        ];
        $this->filteredSubcategories = [];
    }

    public function loadProductData()
    {
        if ($this->editingProduct) {
            // Load the product's category to determine root and subcategory
            $category = Category::find($this->editingProduct->category_id);
            
            // Determine root category and actual category
            if ($category) {
                if ($category->parent_id) {
                    // Product has a subcategory, so set both root and sub
                    $this->form['root_category_id'] = $category->parent_id;
                    $this->form['category_id'] = $category->id;
                    
                    // Load subcategories for the selected root
                    $this->filteredSubcategories = Category::where('parent_id', $category->parent_id)
                        ->active()
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get(['id', 'name'])
                        ->toArray();
                } else {
                    // Product has a root category only
                    $this->form['root_category_id'] = $category->id;
                    $this->form['category_id'] = '';
                    $this->filteredSubcategories = [];
                }
            }
            
            $this->form = array_merge($this->form, [
                'name' => $this->editingProduct->name,
                'sku' => $this->editingProduct->sku,
                'barcode' => $this->editingProduct->barcode,
                'remarks' => $this->editingProduct->remarks,
                'supplier_id' => $this->editingProduct->supplier_id,
                'supplier_code' => $this->editingProduct->supplier_code,
                'price' => $this->editingProduct->price,
                'price_note' => $this->editingProduct->price_note,
                'cost' => $this->editingProduct->cost,
                'uom' => $this->editingProduct->uom,
                'shelf_life_days' => $this->editingProduct->shelf_life_days,
                'disabled' => $this->editingProduct->disabled,
                'initial_quantity' => '',
            ]);
        }
    }

    public function saveProduct()
    {
        try {
            // Ensure ProductService is available
            if (!$this->productService) {
                $this->productService = app(ProductService::class);
            }

            // Determine final category_id: use subcategory if selected, otherwise use root
            $finalCategoryId = !empty($this->form['category_id']) 
                ? $this->form['category_id'] 
                : $this->form['root_category_id'];

            $this->validate([
                'form.name' => 'required|string|max:255',
                'form.sku' => 'required|string|max:255|unique:products,sku' . ($this->editingProduct ? ',' . $this->editingProduct->id : ''),
                'form.barcode' => 'nullable|string|max:255|unique:products,barcode' . ($this->editingProduct ? ',' . $this->editingProduct->id : ''),
                'form.root_category_id' => 'required|exists:categories,id',
                'form.category_id' => 'nullable|exists:categories,id',
                'form.supplier_id' => 'required|exists:suppliers,id',
                'form.price' => 'required|numeric|min:0',
                'form.cost' => 'required|numeric|min:0',
                'form.uom' => 'required|string|max:255',
                'form.shelf_life_days' => 'nullable|integer|min:0',
                'form.initial_quantity' => 'nullable|numeric|min:0',
            ]);
            
            // Update form with final category_id for saving
            $this->form['category_id'] = $finalCategoryId;

            if ($this->editingProduct) {
                // Update existing product
                $this->productService->updateProduct($this->editingProduct, $this->form);
                session()->flash('message', 'Product updated successfully.');
                // Close modal without flipping UI to create-state before close
                $this->dispatch('close-modal', name: 'create-edit-product');
                // Keep isEditMode true for this response to avoid flicker
            } else {
                // Create new product
                $this->productService->createProduct($this->form);
                session()->flash('message', 'Product created successfully.');
                // Close modal and reset form for next open
                $this->dispatch('close-modal', name: 'create-edit-product');
                $this->resetForm();
                $this->isEditMode = false;
                $this->editingProduct = null;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to show field errors
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving product: ' . $e->getMessage());
        }
    }

    public function openProductViewer($productId, $startImageId = null)
    {
        // Load product details and images
        $this->editingProduct = Product::with(['category', 'supplier'])->findOrFail($productId);
        $this->viewerProductId = (int) $productId;

        $images = ProductImage::where('product_id', $this->viewerProductId)
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->viewerImages = $images->pluck('id')->values()->all();
        $this->viewerIndex = 0;

        if ($startImageId) {
            $idx = array_search((int) $startImageId, $this->viewerImages, true);
            if ($idx !== false) {
                $this->viewerIndex = $idx;
            }
        } else {
            $primary = $images->firstWhere('is_primary', true);
            if ($primary) {
                $idx = array_search($primary->id, $this->viewerImages, true);
                if ($idx !== false) {
                    $this->viewerIndex = $idx;
                }
            }
        }

        $currentId = $this->viewerImages[$this->viewerIndex] ?? null;
        $this->viewingImage = $currentId ? ProductImage::find($currentId) : null;
    }

    public function viewerPrev()
    {
        if (empty($this->viewerImages)) {
            return;
        }
        $count = count($this->viewerImages);
        $this->viewerIndex = ($this->viewerIndex - 1 + $count) % $count;
        $this->viewingImage = ProductImage::find($this->viewerImages[$this->viewerIndex]);
    }

    public function viewerNext()
    {
        if (empty($this->viewerImages)) {
            return;
        }
        $count = count($this->viewerImages);
        $this->viewerIndex = ($this->viewerIndex + 1) % $count;
        $this->viewingImage = ProductImage::find($this->viewerImages[$this->viewerIndex]);
    }

    public function render()
    {
        if (!auth()->user()->hasAnyPermission(['product view'])) {
            return view('livewire.pages.errors.403');
        }

        return view('livewire.pages.product-management.index', [
            'products' => $this->products,
            'stats' => $this->stats,
            'selectedProduct' => $this->editingProduct,
        ]);
    }
}
