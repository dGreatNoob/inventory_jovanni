<?php

namespace App\Livewire\Pages\ProductManagement;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\InventoryLocation;
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
    public $locations = [];
    public $selectedProducts = [];
    public $showFilters = false;

    // Modals
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showBulkActionModal = false;
    public $editingProduct = null;

    // Form Data
    public $form = [
        'name' => '',
        'sku' => '',
        'barcode' => '',
        'remarks' => '',
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
        'location_id' => '',
    ];

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
        // Ensure ProductService is available
        if (!$this->productService) {
            $this->productService = app(ProductService::class);
        }
        $this->loadFilters();
    }

    public function loadFilters()
    {
        $this->categories = Category::active()
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        $this->suppliers = Supplier::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        $this->locations = InventoryLocation::active()
            ->orderBy('name')
            ->get(['id', 'name', 'type']);
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
        $this->showCreateModal = true;
    }

    public function editProduct($productId)
    {
        $this->editingProduct = Product::findOrFail($productId);
        $this->loadProductData();
        $this->showEditModal = true;
    }

    public function deleteProduct($productId)
    {
        $this->editingProduct = Product::findOrFail($productId);
        $this->showDeleteModal = true;
    }

    public function confirmDelete()
    {
        if ($this->editingProduct) {
            $this->productService->deleteProduct($this->editingProduct);
            $this->showDeleteModal = false;
            $this->editingProduct = null;
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
        $this->selectedProducts = collect($this->products)->pluck('id')->toArray();
    }

    public function clearSelection()
    {
        $this->selectedProducts = [];
    }

    public function openBulkActionModal()
    {
        if (empty($this->selectedProducts)) {
            session()->flash('error', 'Please select products first.');
            return;
        }
        $this->showBulkActionModal = true;
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
            $this->showBulkActionModal = false;
            $this->bulkAction = '';
            $this->bulkActionValue = '';

        } catch (\Exception $e) {
            session()->flash('error', 'Error performing bulk action: ' . $e->getMessage());
        }
    }

    public function exportProducts()
    {
        // TODO: Implement export functionality
        session()->flash('message', 'Export functionality coming soon.');
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
            'location_id' => '',
        ];
    }

    public function loadProductData()
    {
        if ($this->editingProduct) {
            $this->form = [
                'name' => $this->editingProduct->name,
                'sku' => $this->editingProduct->sku,
                'barcode' => $this->editingProduct->barcode,
                'remarks' => $this->editingProduct->remarks,
                'category_id' => $this->editingProduct->category_id,
                'supplier_id' => $this->editingProduct->supplier_id,
                'supplier_code' => $this->editingProduct->supplier_code,
                'price' => $this->editingProduct->price,
                'price_note' => $this->editingProduct->price_note,
                'cost' => $this->editingProduct->cost,
                'uom' => $this->editingProduct->uom,
                'shelf_life_days' => $this->editingProduct->shelf_life_days,
                'disabled' => $this->editingProduct->disabled,
                'initial_quantity' => '',
                'location_id' => '',
            ];
        }
    }

    public function saveProduct()
    {
        $this->validate([
            'form.name' => 'required|string|max:255',
            'form.sku' => 'required|string|max:255|unique:products,sku' . ($this->editingProduct ? ',' . $this->editingProduct->id : ''),
            'form.barcode' => 'nullable|string|max:255|unique:products,barcode' . ($this->editingProduct ? ',' . $this->editingProduct->id : ''),
            'form.category_id' => 'required|exists:categories,id',
            'form.supplier_id' => 'required|exists:suppliers,id',
            'form.price' => 'required|numeric|min:0',
            'form.cost' => 'required|numeric|min:0',
            'form.uom' => 'required|string|max:255',
            'form.shelf_life_days' => 'nullable|integer|min:0',
            'form.initial_quantity' => 'nullable|numeric|min:0',
            'form.location_id' => 'nullable|exists:inventory_locations,id',
        ]);

        try {
            if ($this->editingProduct) {
                // Update existing product
                $this->productService->updateProduct($this->editingProduct, $this->form);
                session()->flash('message', 'Product updated successfully.');
            } else {
                // Create new product
                $this->productService->createProduct($this->form);
                session()->flash('message', 'Product created successfully.');
            }

            $this->showCreateModal = false;
            $this->showEditModal = false;
            $this->resetForm();
            $this->editingProduct = null;

        } catch (\Exception $e) {
            session()->flash('error', 'Error saving product: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.product-management.index', [
            'products' => $this->products,
            'stats' => $this->stats,
        ]);
    }
}
