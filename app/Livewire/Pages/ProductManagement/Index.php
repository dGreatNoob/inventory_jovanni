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
use App\Models\ProductColor;
use App\Models\ProductPriceHistory;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.app')]
#[Title('Product Masterlist')]
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
    public $viewMode = 'table'; // grid or table

    // Data
    // public $products = []; // Removed - using computed property instead
    public $categories = [];
    public $suppliers = [];
    public $selectedProducts = [];
    public $showFilters = false;
    public $showProductPanel = false;

    // Modals
    public $editingProduct = null;
    public $isEditMode = false;
    public $priceHistories = [];
    // Viewer state for product details modal
    public $viewerProductId = null;
    public $viewerImages = [];
    public $viewerIndex = 0;
    public $viewingImage = null;

    // Form Data
    public $form = [
        'name' => '',
        'product_number' => '',
        'sku' => '',
        'barcode' => '',
        'remarks' => '',
        'category_id' => '',
        'supplier_id' => '',
        'supplier_code' => '',
        'soft_card' => '',
        'product_type' => 'regular',
        'price' => '',
        'original_price' => '',
        'price_note' => 'REG1',
        'cost' => '',
        'uom' => 'pcs',
        'shelf_life_days' => '',
        'disabled' => false,
        'initial_quantity' => '',
        'price_levels' => [],
        'discount_tiers' => [],
        'product_color_id' => '',
    ];
    
    public $colors = [];
    public $colorForm = [
        'code' => '',
        'name' => '',
        'shortcut' => '',
    ];
    public $showColorForm = false;
    public $latestColorCode = null;
    public $lastRegularPrice = null;
    public $lastSalePrice = null;
    public $regularPriceChangeCount = 0;
    public $salePriceChangeCount = 0;

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
        $this->loadColors();
    }

    public function loadFilters()
    {
        // Load all categories (flat structure)
        $this->categories = Category::active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'sort_order']);

        $this->suppliers = Supplier::active()
            ->orderBy('name')
            ->get(['id', 'name']);

    }
    
    protected function loadColors(): void
    {
        $colorsCollection = ProductColor::orderBy('code')
            ->get(['id', 'code', 'name', 'shortcut']);

        $this->colors = $colorsCollection
            ->map(fn ($color) => [
                'id' => $color->id,
                'code' => $color->code,
                'name' => $color->name,
                'shortcut' => $color->shortcut,
            ])
            ->toArray();

        $maxNumeric = null;
        foreach ($colorsCollection as $color) {
            $digits = preg_replace('/\D/', '', (string) $color->code);
            if ($digits === '') {
                continue;
            }
            $value = (int) $digits;
            if ($maxNumeric === null || $value > $maxNumeric) {
                $maxNumeric = $value;
            }
        }

        $this->latestColorCode = $maxNumeric !== null
            ? str_pad((string) $maxNumeric, 4, '0', STR_PAD_LEFT)
            : null;
    }

    protected function resetColorForm(): void
    {
        $this->colorForm = [
            'code' => '',
            'name' => '',
            'shortcut' => '',
        ];
    }

    public function startColorCreation(): void
    {
        $this->resetColorForm();
        $this->showColorForm = true;
    }

    public function cancelColorCreation(): void
    {
        $this->resetColorForm();
        $this->showColorForm = false;
    }

    public function saveNewColor(): void
    {
        $this->colorForm['code'] = $this->normalizeColorCodeInput($this->colorForm['code'] ?? '');
        $this->colorForm['name'] = trim((string) ($this->colorForm['name'] ?? ''));
        $this->colorForm['shortcut'] = trim((string) ($this->colorForm['shortcut'] ?? ''));

        $validated = $this->validate([
            'colorForm.code' => ['required', 'regex:/^\d{4}$/', Rule::unique('product_colors', 'code')],
            'colorForm.name' => ['required', 'string', 'max:255'],
            'colorForm.shortcut' => ['nullable', 'string', 'max:32'],
        ]);

        $color = ProductColor::create([
            'code' => $validated['colorForm']['code'],
            'name' => $validated['colorForm']['name'],
            'shortcut' => $validated['colorForm']['shortcut'] ?? null,
        ]);

        $this->loadColors();
        $this->form['product_color_id'] = (string) $color->id;
        $this->showColorForm = false;
        $this->resetColorForm();
        $this->refreshBarcode();
        $this->refreshDescription();
    }


    public function updatedFormSupplierId($supplierId)
    {
        if (!$supplierId) {
            $this->form['supplier_code'] = '';
            return;
        }

        $supplier = Supplier::find($supplierId);
        $this->form['supplier_code'] = $supplier?->code ?? '';

        // Auto-assign Red Tag rule if supplier indicates red tag capability
        if (data_get($supplier, 'is_red_tag', false)) {
            $this->form['product_type'] = 'sale';
            $this->form['price_note'] = 'SAL1';
        }
    }

    public function addPriceLevel(): void
    {
        $this->form['price_levels'][] = [
            'label' => '',
            'amount' => '',
        ];
    }

    public function removePriceLevel(int $index): void
    {
        if (isset($this->form['price_levels'][$index])) {
            unset($this->form['price_levels'][$index]);
            $this->form['price_levels'] = array_values($this->form['price_levels']);
        }
    }

    public function addDiscountTier(): void
    {
        $this->form['discount_tiers'][] = [
            'min_qty' => '',
            'max_qty' => '',
            'discount_percent' => '',
        ];
    }

    public function removeDiscountTier(int $index): void
    {
        if (isset($this->form['discount_tiers'][$index])) {
            unset($this->form['discount_tiers'][$index]);
            $this->form['discount_tiers'] = array_values($this->form['discount_tiers']);
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
        $this->priceHistories = [];
        $this->refreshBarcode();
        $this->lastRegularPrice = null;
        $this->lastSalePrice = null;
        $this->showProductPanel = true;
    }

    public function editProduct($productId)
    {
        $this->editingProduct = Product::findOrFail($productId);
        $this->loadProductData();
        $this->isEditMode = true;
        $this->loadPriceHistory($productId);
        $this->refreshBarcode();
        $this->showProductPanel = true;
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
            'product_number' => '',
            'sku' => '',
            'barcode' => '',
            'remarks' => '',
            'category_id' => '',
            'supplier_id' => '',
            'supplier_code' => '',
            'product_type' => 'regular',
            'price' => '',
            'original_price' => '',
            'price_note' => 'REG1',
            'cost' => '',
            'uom' => 'pcs',
            'shelf_life_days' => '',
            'disabled' => false,
            'initial_quantity' => '',
            'price_levels' => [],
            'discount_tiers' => [],
            'product_color_id' => '',
        ];
        $this->priceHistories = [];
        $this->refreshBarcode();
        $this->lastRegularPrice = null;
        $this->lastSalePrice = null;
        $this->regularPriceChangeCount = 0;
        $this->salePriceChangeCount = 0;
    }

    public function loadProductData()
    {
        if ($this->editingProduct) {
            $this->form = array_merge($this->form, [
                'name' => $this->editingProduct->name,
                'product_number' => $this->editingProduct->product_number,
                'sku' => $this->editingProduct->sku,
                'barcode' => $this->editingProduct->barcode,
                'remarks' => $this->editingProduct->remarks,
                'category_id' => $this->editingProduct->category_id,
                'supplier_id' => $this->editingProduct->supplier_id,
                'supplier_code' => $this->editingProduct->supplier_code,
                'soft_card' => $this->editingProduct->soft_card,
                'product_type' => $this->editingProduct->price_note && str_starts_with($this->editingProduct->price_note, 'SAL') ? 'sale' : 'regular',
                'price' => $this->editingProduct->price,
                'original_price' => $this->editingProduct->original_price,
                'price_note' => $this->editingProduct->price_note,
                'cost' => $this->editingProduct->cost,
                'uom' => $this->editingProduct->uom,
                'shelf_life_days' => $this->editingProduct->shelf_life_days,
                'disabled' => $this->editingProduct->disabled,
                'initial_quantity' => '',
                'product_color_id' => $this->editingProduct->product_color_id ? (string) $this->editingProduct->product_color_id : '',
            ]);
            $this->refreshBarcode();
            $this->refreshDescription();
        }
    }

    protected function loadPriceHistory(int $productId): void
    {
        if (empty($productId)) {
            $this->priceHistories = [];
            $this->lastRegularPrice = null;
            $this->lastSalePrice = null;
            $this->regularPriceChangeCount = 0;
            $this->salePriceChangeCount = 0;
            return;
        }

        $histories = $this->productService->getProductPriceHistory($productId);

        $this->priceHistories = $histories->map(function ($history) {
            return [
                'changed_at' => optional($history->changed_at)->toIso8601String(),
                'old_price' => $history->old_price,
                'new_price' => $history->new_price,
                'pricing_note' => $history->pricing_note,
                'changed_by' => [
                    'id' => $history->changedBy?->id,
                    'name' => $history->changedBy?->name,
                ],
            ];
        })->toArray();

        $this->updateLastPrices($histories, $productId);
    }

    public function saveProduct()
    {
        Log::info('=== saveProduct() called ===', [
            'is_edit_mode' => $this->isEditMode ?? false,
            'editing_product_id' => $this->editingProduct?->id ?? null,
        ]);

        try {
            // Log initial form data
            Log::info('Initial form data:', [
                'form' => $this->form,
                'form_keys' => array_keys($this->form ?? []),
            ]);

            // Ensure ProductService is available
            if (!$this->productService) {
                $this->productService = app(ProductService::class);
                Log::info('ProductService initialized');
            }

            // Normalize and prepare form data
            $this->form['product_number'] = $this->normalizeProductNumber($this->form['product_number']);
            Log::info('After normalizeProductNumber:', ['product_number' => $this->form['product_number']]);
            
            // Normalize product_color_id: convert empty string to null, ensure it's an integer if set
            if (isset($this->form['product_color_id'])) {
                $originalColorId = $this->form['product_color_id'];
                $this->form['product_color_id'] = trim((string) $this->form['product_color_id']);
                if ($this->form['product_color_id'] === '') {
                    $this->form['product_color_id'] = null;
                } else {
                    $this->form['product_color_id'] = (int) $this->form['product_color_id'];
                }
                Log::info('Normalized product_color_id:', [
                    'original' => $originalColorId,
                    'normalized' => $this->form['product_color_id'],
                ]);
            } else {
                Log::warning('product_color_id not set in form');
            }
            
            $this->refreshDescription();
            Log::info('After refreshDescription:', ['remarks' => $this->form['remarks'] ?? '']);
            
            // Generate barcode before validation - ensure all required fields are present
            $this->refreshBarcode();
            Log::info('After refreshBarcode:', [
                'barcode' => $this->form['barcode'] ?? '',
                'product_number' => $this->form['product_number'] ?? '',
                'product_color_id' => $this->form['product_color_id'] ?? '',
                'price' => $this->form['price'] ?? '',
            ]);
            
            // If barcode is still empty after refresh, it means required fields are missing
            // This will be caught by validation, but let's ensure we have the latest attempt
            if (empty($this->form['barcode']) && 
                !empty($this->form['product_number']) && 
                !empty($this->form['product_color_id']) && 
                !empty($this->form['price'])) {
                // Force regenerate barcode one more time
                $this->refreshBarcode();
                Log::info('Barcode regenerated after force refresh:', ['barcode' => $this->form['barcode'] ?? '']);
            }

            Log::info('Starting validation...');
            $this->validate([
                'form.name' => 'required|string|max:255',
                'form.product_number' => 'required|regex:/^\d{6}$/|unique:products,product_number' . ($this->editingProduct ? ',' . $this->editingProduct->id : ''),
                'form.sku' => 'required|string|max:255|unique:products,sku' . ($this->editingProduct ? ',' . $this->editingProduct->id : ''),
                'form.barcode' => ['required','regex:/^\d{16}$/','unique:products,barcode' . ($this->editingProduct ? ',' . $this->editingProduct->id : '')],
                'form.category_id' => 'required|exists:categories,id',
                'form.supplier_id' => 'required|exists:suppliers,id',
                'form.price' => 'required|numeric|min:0',
                'form.original_price' => 'nullable|numeric|min:0',
                'form.cost' => 'required|numeric|min:0',
                'form.uom' => 'required|string|max:255',
                'form.shelf_life_days' => 'nullable|integer|min:0',
                'form.initial_quantity' => 'nullable|numeric|min:0',
                'form.price_levels.*.label' => 'nullable|string|max:20',
                'form.price_levels.*.amount' => 'nullable|numeric|min:0',
                'form.discount_tiers.*.min_qty' => 'nullable|integer|min:1',
                'form.discount_tiers.*.max_qty' => 'nullable|integer|min:1',
                'form.discount_tiers.*.discount_percent' => 'nullable|numeric|min:0|max:100',
                'form.product_color_id' => [
                    'required',
                    'integer',
                    Rule::exists('product_colors', 'id'),
                ],
            ]);
            Log::info('Validation passed successfully');

            if ($this->editingProduct) {
                Log::info('Updating existing product', ['product_id' => $this->editingProduct->id]);
                // Update existing product
                $this->productService->updateProduct($this->editingProduct, $this->form);
                session()->flash('message', 'Product updated successfully.');
                Log::info('Product updated successfully');
            } else {
                Log::info('Creating new product');
                // Create new product
                $product = $this->productService->createProduct($this->form);
                session()->flash('message', 'Product created successfully.');
                Log::info('Product created successfully', ['product_id' => $product->id ?? null]);
            }

            $this->closeProductPanel();
            Log::info('Product panel closed');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', [
                'errors' => $e->errors(),
                'form_data' => $this->form,
            ]);
            // Re-throw validation exceptions to show field errors
            throw $e;
        } catch (\Exception $e) {
            Log::error('Exception in saveProduct:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'form_data' => $this->form,
            ]);
            session()->flash('error', 'Error saving product: ' . $e->getMessage());
        }
    }

    public function closeProductPanel(): void
    {
        $this->showProductPanel = false;
        $this->isEditMode = false;
        $this->editingProduct = null;
        $this->resetForm();
        $this->priceHistories = [];
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
        $this->viewingImage = $currentId ? ProductImage::with('product')->find($currentId) : null;
        
        Log::debug('openProductViewer', [
            'product_id' => $productId,
            'viewer_images_count' => count($this->viewerImages),
            'viewer_index' => $this->viewerIndex,
            'current_id' => $currentId,
            'viewing_image_id' => $this->viewingImage?->id,
            'viewing_image_filename' => $this->viewingImage?->filename,
            'viewing_image_url' => $this->viewingImage?->url,
        ]);
        
        $this->loadPriceHistory($this->viewerProductId);
        $this->refreshBarcode();
    }

    public function updatedFormProductNumber($value): void
    {
        $this->form['product_number'] = $this->sanitizeProductNumber($value);
        $this->refreshBarcode();
    }

    public function updatedFormProductColorId($value): void
    {
        $this->form['product_color_id'] = (string) $value;
        $this->refreshBarcode();
        $this->refreshDescription();
    }

    public function updatedFormPrice($value): void
    {
        $this->refreshBarcode();
    }

    public function updatedFormProductType($value): void
    {
        $value = (string) $value;

        if (!$this->isEditMode) {
            $this->form['price_note'] = $value === 'sale' ? 'SAL1' : 'REG1';
            return;
        }

        if (!$this->editingProduct) {
            $this->form['price_note'] = $value === 'sale' ? 'SAL1' : 'REG1';
            return;
        }

        $currentNote = strtoupper((string) ($this->form['price_note'] ?? ''));

        if ($value === 'sale' && !str_starts_with($currentNote, 'SAL')) {
            $this->form['price_note'] = 'SAL' . max($this->salePriceChangeCount + 1, 1);
        } elseif ($value !== 'sale' && !str_starts_with($currentNote, 'REG')) {
            $this->form['price_note'] = 'REG' . max($this->regularPriceChangeCount + 1, 1);
        }
    }

    public function updatedFormName($value): void
    {
        $this->form['name'] = (string) $value;
        $this->refreshDescription();
    }

    protected function sanitizeProductNumber($value): string
    {
        $digits = preg_replace('/\D/', '', (string) $value);
        return substr($digits, 0, 6);
    }

    protected function normalizeProductNumber($value): string
    {
        $digits = $this->sanitizeProductNumber($value);

        if ($digits === '') {
            return '';
        }

        return str_pad($digits, 6, '0', STR_PAD_LEFT);
    }

    protected function normalizeColorCodeInput(?string $code): string
    {
        $digits = preg_replace('/\D/', '', (string) $code);

        if ($digits === '') {
            return '';
        }

        return str_pad(substr($digits, 0, 4), 4, '0', STR_PAD_LEFT);
    }

    protected function getSelectedColorCode(): ?string
    {
        $colorId = $this->form['product_color_id'] ?? null;
        if (empty($colorId)) {
            return null;
        }

        $color = collect($this->colors)->firstWhere('id', (int) $colorId);

        if (!$color) {
            $colorModel = ProductColor::find($colorId);
            if (!$colorModel) {
                return null;
            }

            $this->colors[] = [
                'id' => $colorModel->id,
                'code' => $colorModel->code,
                'name' => $colorModel->name,
                'shortcut' => $colorModel->shortcut,
            ];

            return $colorModel->code;
        }

        return $color['code'];
    }

    protected function updateLastPrices($histories, int $productId): void
    {
        $regularHistory = $histories->filter(function ($history) {
            $note = strtoupper((string) ($history->pricing_note ?? ''));
            return str_starts_with($note, 'REG');
        });

        $saleHistory = $histories->filter(function ($history) {
            $note = strtoupper((string) ($history->pricing_note ?? ''));
            return str_starts_with($note, 'SAL');
        });

        $this->regularPriceChangeCount = ProductPriceHistory::where('product_id', $productId)
            ->where('pricing_note', 'like', 'REG%')
            ->count();

        $this->salePriceChangeCount = ProductPriceHistory::where('product_id', $productId)
            ->where('pricing_note', 'like', 'SAL%')
            ->count();

        $lastRegular = $regularHistory->first();
        $lastSale = $saleHistory->first();

        $this->lastRegularPrice = $lastRegular ? [
            'price' => $lastRegular->new_price,
            'note' => $lastRegular->pricing_note,
            'changed_at' => optional($lastRegular->changed_at)->toIso8601String(),
        ] : null;

        $this->lastSalePrice = $lastSale ? [
            'price' => $lastSale->new_price,
            'note' => $lastSale->pricing_note,
            'changed_at' => optional($lastSale->changed_at)->toIso8601String(),
        ] : null;
    }

    protected function getSelectedColorLabel(): string
    {
        $colorId = $this->form['product_color_id'] ?? null;
        if (empty($colorId)) {
            return '';
        }

        $color = collect($this->colors)->firstWhere('id', (int) $colorId);

        if (!$color) {
            $colorModel = ProductColor::find($colorId);
            if (!$colorModel) {
                return '';
            }

            $this->colors[] = [
                'id' => $colorModel->id,
                'code' => $colorModel->code,
                'name' => $colorModel->name,
                'shortcut' => $colorModel->shortcut,
            ];

            $color = [
                'code' => $colorModel->code,
                'name' => $colorModel->name,
                'shortcut' => $colorModel->shortcut,
            ];
        }

        $shortcut = trim((string) ($color['shortcut'] ?? ''));
        if ($shortcut !== '') {
            return $shortcut;
        }

        return trim((string) ($color['name'] ?? ''));
    }

    protected function refreshDescription(): void
    {
        $name = trim((string) ($this->form['name'] ?? ''));
        $colorLabel = $this->getSelectedColorLabel();

        $parts = array_filter([$name, $colorLabel], fn ($part) => $part !== '');

        $this->form['remarks'] = empty($parts) ? '' : implode(' ', $parts);
    }

    protected function refreshBarcode(): void
    {
        $productNumber = $this->normalizeProductNumber($this->form['product_number'] ?? '');
        $colorCode = $this->getSelectedColorCode();
        $colorDigits = $colorCode ? substr(preg_replace('/\D/', '', $colorCode), 0, 4) : '';
        $price = $this->form['price'] ?? '';

        // Check if price is numeric (including 0) and all required fields are present
        $hasValidPrice = $price !== '' && is_numeric($price) && (float) $price >= 0;

        if (strlen($productNumber) === 6 && strlen($colorDigits) === 4 && $hasValidPrice) {
            // Format price: remove decimal point and zero-pad to 6 digits
            $priceValue = (float) $price;
            $priceInCents = (int) ($priceValue * 100);
            // Ensure price doesn't exceed 999999.99 (99999999 cents = 8 digits, but we need 6)
            // Take only last 6 digits if price is too large
            $priceDigits = substr(str_pad((string) $priceInCents, 6, '0', STR_PAD_LEFT), -6);
            $this->form['barcode'] = $productNumber . str_pad($colorDigits, 4, '0', STR_PAD_LEFT) . $priceDigits;
        } else {
            $this->form['barcode'] = '';
        }
    }

    public function viewerPrev()
    {
        if (empty($this->viewerImages)) {
            return;
        }
        $count = count($this->viewerImages);
        $this->viewerIndex = ($this->viewerIndex - 1 + $count) % $count;
        $currentId = $this->viewerImages[$this->viewerIndex] ?? null;
        $this->viewingImage = $currentId ? ProductImage::with('product')->find($currentId) : null;
    }

    public function viewerNext()
    {
        if (empty($this->viewerImages)) {
            return;
        }
        $count = count($this->viewerImages);
        $this->viewerIndex = ($this->viewerIndex + 1) % $count;
        $currentId = $this->viewerImages[$this->viewerIndex] ?? null;
        $this->viewingImage = $currentId ? ProductImage::with('product')->find($currentId) : null;
    }

    public function setViewerImage(int $imageId): void
    {
        if (empty($this->viewerImages)) {
            return;
        }

        $index = array_search($imageId, $this->viewerImages, true);

        if ($index === false) {
            return;
        }

        $this->viewerIndex = $index;
        $currentId = $this->viewerImages[$this->viewerIndex] ?? null;
        $this->viewingImage = $currentId ? ProductImage::with('product')->find($currentId) : null;
    }

    public function getViewingImageUrlProperty(): ?string
    {
        if (!$this->viewingImage || empty($this->viewingImage->filename)) {
            return null;
        }
        
        return asset('storage/photos/' . $this->viewingImage->filename);
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
            'colors' => $this->colors,
            'latestColorCode' => $this->latestColorCode,
            'lastRegularPrice' => $this->lastRegularPrice,
            'lastSalePrice' => $this->lastSalePrice,
            'regularPriceChangeCount' => $this->regularPriceChangeCount,
            'salePriceChangeCount' => $this->salePriceChangeCount,
        ]);
    }
}
