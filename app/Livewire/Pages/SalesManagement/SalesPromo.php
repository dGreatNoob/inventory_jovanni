<?php

namespace App\Livewire\Pages\SalesManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Promo;
use App\Models\Branch;
use App\Models\Product;
use App\Models\BatchAllocation;
use App\Models\BranchAllocationItem;
use Carbon\Carbon;

class SalesPromo extends Component
{
    use WithPagination;

    // Form fields
    public $promo_name, $promo_code, $promo_description, $startDate, $endDate;
    public $selected_batches = [], $selected_products = [];

    // Promo type
    public $promo_type = '';
    public $promo_type_options = ['Buy one Take one', '70% Discount', '60% Discount', '50% Discount'];

    // Lists
    public $branches = [], $products = [];
    public $batchAllocations = [];

    // Dropdowns
    public $batchDropdown = false, $productDropdown = false;

    // Product search (for searchable dropdown)
    public $productSearch = '';
    public $editProductSearch = '';

    // Delete modal
    public $showDeleteModal = false;
    public $deleteId = null;

    // Edit modal fields
    public $showEditModal = false;
    public $edit_id, $edit_name, $edit_code, $edit_description, $edit_startDate, $edit_endDate, $edit_type;
    public $edit_selected_batches = [], $edit_selected_products = [];
    public $editBatchDropdown = false, $editProductDropdown = false;

    // View modal fields
    public $showViewModal = false;
    public $view_name, $view_code, $view_type, $view_startDate, $view_endDate, $view_description;
    public $view_selected_batches = [], $view_selected_products = [];
    public $showCreatePanel = false;

    // Search & pagination
    public $search = '';
    public $perPage = 10;
    
    // Filters
    public $typeFilter = '';
    public $statusFilter = '';
    public $filterStartDate = '';
    public $filterEndDate = '';

    // Load batch allocations and branches (products loaded on-demand to avoid memory issues)
    public function mount()
    {
        $this->batchAllocations = BatchAllocation::with(['branchAllocations.items.product'])
            ->orderBy('ref_no', 'desc')
            ->get();
        $this->branches = Branch::orderBy('name')->get();
        $this->products = collect();
        
        // Reset form fields when component mounts
        $this->resetForm();
    }

    public function showCreatePanel()
    {
        // Reset form before opening panel
        $this->resetForm();
        $this->showCreatePanel = true;
    }

    public function closeCreatePanel()
    {
        $this->showCreatePanel = false;
        // Reset form when panel closes
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'promo_name',
            'promo_code',
            'promo_description',
            'startDate',
            'endDate',
            'selected_products',
            'promo_type',
            'productDropdown',
            'productSearch',
        ]);
        $this->resetValidation();
    }

    // Validation rules for create form
    protected function rules()
    {
        $rules = [
            'promo_name' => 'required|string|max:255',
            'promo_code' => 'nullable|string|max:100|unique:promos,code',
            'promo_description' => 'nullable|string|max:500',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'selected_products' => 'required|array|min:1',
            'selected_products.*' => 'exists:products,id',
            'promo_type' => 'required|string|in:' . implode(',', $this->promo_type_options),
        ];

        return $rules;
    }

    // Validation rules for edit form
    protected function editRules()
    {
        $rules = [
            'edit_name' => 'required|string|max:255',
            'edit_code' => 'nullable|string|max:100|unique:promos,code,' . $this->edit_id,
            'edit_description' => 'nullable|string|max:500',
            'edit_startDate' => 'required|date',
            'edit_endDate' => 'required|date|after_or_equal:edit_startDate',
            'edit_selected_batches' => 'required|array|min:1',
            'edit_selected_batches.*' => 'exists:batch_allocations,id',
            'edit_selected_products' => 'required|array|min:1',
            'edit_selected_products.*' => 'exists:products,id',
            'edit_type' => 'required|string|in:' . implode(',', $this->promo_type_options),
        ];

        return $rules;
    }

    // Validation messages
    protected $messages = [
        'selected_products.required' => 'Please select at least one product.',
        'edit_selected_products.required' => 'Please select at least one product.',
    ];

    // Create Promo with overlap validation
    public function submit()
    {
        $this->validate();

        // Check for overlapping promos
        $overlapError = $this->checkForOverlappingPromos();
        if ($overlapError) {
            return;
        }

        Promo::create([
            'name' => $this->promo_name,
            'code' => $this->promo_code,
            'description' => $this->promo_description,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'branch' => json_encode([]), // No batch selection required
            'product' => json_encode($this->selected_products),
            'second_product' => null,
            'type' => $this->promo_type,
        ]);

        session()->flash('message', 'Promo created successfully!');

        // Reset form fields
        $this->reset([
            'promo_name',
            'promo_code',
            'promo_description',
            'startDate',
            'endDate',
            'selected_products',
            'promo_type',
        ]);

        // Close the create panel
        $this->closeCreatePanel();
    }

    // View Promo
    public function view($id)
    {
        $promo = Promo::findOrFail($id);

        $this->view_name = $promo->name;
        $this->view_code = $promo->code;
        $this->view_type = $promo->type;
        $this->view_startDate = $promo->startDate ? $promo->startDate->format('M d, Y') : '-';
        $this->view_endDate = $promo->endDate ? $promo->endDate->format('M d, Y') : '-';
        $this->view_selected_batches = json_decode($promo->branch, true) ?? [];
        $this->view_selected_products = json_decode($promo->product, true) ?? [];
        $this->view_description = $promo->description;

        $this->showViewModal = true;
    }

    // Edit Promo
    public function edit($id)
    {
        $promo = Promo::findOrFail($id);

        $this->edit_id = $promo->id;
        $this->edit_name = $promo->name;
        $this->edit_code = $promo->code;
        $this->edit_description = $promo->description;
        $this->edit_startDate = $promo->startDate->format('Y-m-d');
        $this->edit_endDate = $promo->endDate->format('Y-m-d');
        $this->edit_type = $promo->type;
        $this->edit_selected_batches = json_decode($promo->branch, true) ?? [];
        $this->edit_selected_products = json_decode($promo->product, true) ?? [];

        $this->showEditModal = true;
    }

    // Update Promo with overlap validation
    public function update()
    {
        // Validate the edit form
        $this->validate($this->editRules());

        $promo = Promo::findOrFail($this->edit_id);

        // Check for overlapping promos (excluding current promo)
        $overlapError = $this->checkForOverlappingPromos($this->edit_id);
        if ($overlapError) {
            return;
        }

        $promo->update([
            'name' => $this->edit_name,
            'code' => $this->edit_code,
            'description' => $this->edit_description,
            'startDate' => $this->edit_startDate,
            'endDate' => $this->edit_endDate,
            'type' => $this->edit_type,
            'branch' => json_encode($this->edit_selected_batches),
            'product' => json_encode($this->edit_selected_products),
            'second_product' => null,
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'Promo updated successfully!');
    }

    /**
     * Check for overlapping promos (simplified - checks product overlaps only, no batch requirement)
     */
    private function checkForOverlappingPromos($excludeId = null)
    {
        // Determine if we're in create or edit mode
        $isEdit = $this->showEditModal;
        
        $startDate = $isEdit ? $this->edit_startDate : $this->startDate;
        $endDate = $isEdit ? $this->edit_endDate : $this->endDate;
        $products = $isEdit ? $this->edit_selected_products : $this->selected_products;

        // Convert dates to Carbon for proper comparison
        $newStart = Carbon::parse($startDate);
        $newEnd = Carbon::parse($endDate);

        // Get ALL promos first (we'll filter manually for better control)
        $allPromos = Promo::when($excludeId, function($query) use ($excludeId) {
            $query->where('id', '!=', $excludeId);
        })->get();

        $conflictingPromos = collect();

        foreach ($allPromos as $existingPromo) {
            $existingStart = Carbon::parse($existingPromo->startDate);
            $existingEnd = Carbon::parse($existingPromo->endDate);

            // Check if dates overlap
            $datesOverlap = ($newStart <= $existingEnd) && ($newEnd >= $existingStart);
            
            if ($datesOverlap) {
                // Get products from existing promo
                $existingMainProducts = $this->safeJsonDecode($existingPromo->product);
                $existingSecondProducts = $this->safeJsonDecode($existingPromo->second_product);

                // Check if any of our selected products exist in the existing promo
                foreach ($products as $productId) {
                    if (in_array($productId, $existingMainProducts) || in_array($productId, $existingSecondProducts)) {
                        $conflictingPromos->push([
                            'promo' => $existingPromo,
                            'product_id' => $productId,
                        ]);
                    }
                }
            }
        }

        // If we found conflicts, show errors
        if ($conflictingPromos->isNotEmpty()) {
            // Group by product to show consolidated errors
            $productConflicts = $conflictingPromos->groupBy('product_id');
            
            foreach ($productConflicts as $productId => $conflicts) {
                $productName = Product::find($productId)?->name ?? 'Unknown Product';
                $promoNames = $conflicts->pluck('promo.name')->unique()->implode(', ');
                
                $errorMessage = "Product '{$productName}' is already in promotion(s): {$promoNames} during the selected dates.";
                
                // Determine which field to show error on
                $field = $isEdit ? 'edit_selected_products' : 'selected_products';
                
                session()->flash('error', $errorMessage);
                $this->addError($field, $errorMessage);
            }
            
            return true;
        }

        return false;
    }

    /**
     * Check if a product is disabled due to overlap (simplified - no batch requirement)
     * @param iterable|null $promosCollection Pre-loaded promos to avoid repeated DB queries
     */
    private function isProductDisabled($productId, $startDate, $endDate, $excludePromoId = null, $promosCollection = null)
    {
        if (empty($startDate) || empty($endDate)) {
            return false;
        }

        $newStart = Carbon::parse($startDate);
        $newEnd = Carbon::parse($endDate);

        $allPromos = $promosCollection ?? Promo::when($excludePromoId, function($query) use ($excludePromoId) {
            $query->where('id', '!=', $excludePromoId);
        })->get();

        foreach ($allPromos as $existingPromo) {
            if ($excludePromoId && (int) $existingPromo->id === (int) $excludePromoId) {
                continue;
            }
            $existingStart = Carbon::parse($existingPromo->startDate);
            $existingEnd = Carbon::parse($existingPromo->endDate);

            // Check if dates overlap
            $datesOverlap = ($newStart <= $existingEnd) && ($newEnd >= $existingStart);
            
            if ($datesOverlap) {
                // Get products from existing promo
                $existingProducts = $this->safeJsonDecode($existingPromo->product);
                $existingSecondProducts = $this->safeJsonDecode($existingPromo->second_product);

                // Check if product is already in an overlapping promo
                if (in_array($productId, $existingProducts) || in_array($productId, $existingSecondProducts)) {
                    return true; // Product is disabled due to conflict
                }
            }
        }

        return false;
    }

    /**
     * Check if a product is disabled for edit (batch context); delegates to isProductDisabled.
     */
    private function isProductDisabledForBatches($productId, $batchIds, $startDate, $endDate, $excludePromoId = null)
    {
        return $this->isProductDisabled($productId, $startDate, $endDate, $excludePromoId);
    }

    /**
     * Safe JSON decode helper
     */
    private function safeJsonDecode($jsonString)
    {
        if (empty($jsonString)) {
            return [];
        }

        try {
            $decoded = json_decode($jsonString, true);
            return is_array($decoded) ? $decoded : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    // Handle product selection - validate for overlaps
    public function updatedSelectedProducts()
    {
        // Remove any disabled products that were selected
        if (!empty($this->selected_products) && $this->startDate && $this->endDate) {
            $validProducts = [];
            foreach ($this->selected_products as $productId) {
                if (!$this->isProductDisabled($productId, $this->startDate, $this->endDate)) {
                    $validProducts[] = $productId;
                }
            }
            $this->selected_products = $validProducts;
        }
    }

    public function updatedEditSelectedProducts($value)
    {
        // Remove any disabled products that were selected
        if (!empty($this->edit_selected_products) && $this->edit_startDate && $this->edit_endDate) {
            $validProducts = [];
            foreach ($this->edit_selected_products as $productId) {
                if (!$this->isProductDisabled($productId, $this->edit_startDate, $this->edit_endDate, $this->edit_id)) {
                    $validProducts[] = $productId;
                }
            }
            $this->edit_selected_products = $validProducts;
        }
        
        $this->validateOnly('edit_selected_products', $this->editRules());
    }

    // Close modals
    public function cancelEdit()
    {
        $this->showEditModal = false;
        $this->resetValidation();
        $this->reset([
            'edit_id', 'edit_name', 'edit_code', 'edit_description', 
            'edit_startDate', 'edit_endDate', 'edit_type',
            'edit_selected_batches', 'edit_selected_products',
            'editProductDropdown', 'editProductSearch',
        ]);
    }

    public function cancelView()
    {
        $this->showViewModal = false;
    }

    // Delete Promo
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $promo = Promo::find($this->deleteId);

        if ($promo) {
            $promo->delete();
            session()->flash('message', 'Promo deleted successfully!');
        } else {
            session()->flash('error', 'Promo not found.');
        }

        $this->reset('deleteId', 'showDeleteModal');
        $this->resetPage();
    }

    public function cancelDelete()
    {
        $this->reset('deleteId', 'showDeleteModal');
    }

    // Update promo type
    public function updatedPromoType($value)
    {
        // No special handling needed
    }

    /** Products for selected chips (create form) - only loads selected IDs */
    public function getSelectedProductModelsProperty()
    {
        if (empty($this->selected_products)) {
            return collect();
        }
        return Product::whereIn('id', $this->selected_products)->orderBy('name')->get();
    }

    /** Search-driven product dropdown (create form) - max 100 results to avoid freeze */
    public function getFilteredAvailableProductsProperty()
    {
        $query = trim($this->productSearch ?? '');
        $like = $query !== '' ? '%' . $query . '%' : null;

        $q = Product::query()
            ->active()
            ->orderBy('name')
            ->limit(100);

        if ($like !== null) {
            $q->where(function ($qb) use ($like) {
                $qb->where('name', 'like', $like)
                    ->orWhere('remarks', 'like', $like)
                    ->orWhere('sku', 'like', $like)
                    ->orWhere('supplier_code', 'like', $like)
                    ->orWhere('product_number', 'like', $like);
            });
        }

        $products = $q->get();
        $promos = Promo::get();

        return $products->map(function ($product) use ($promos) {
            $product->isDisabled = $this->isProductDisabled($product->id, $this->startDate, $this->endDate, null, $promos);
            return $product;
        });
    }

    // Computed property for edit products based on selected batch allocations
    public function getAvailableProductsForEditBatchesProperty()
    {
        if (empty($this->edit_selected_batches)) {
            return collect();
        }

        // Get all product IDs from branch allocation items in selected batch allocations
        $productIds = BranchAllocationItem::whereHas('branchAllocation', function ($query) {
            $query->whereIn('batch_allocation_id', $this->edit_selected_batches);
        })->pluck('product_id')->unique();

        $products = Product::whereIn('id', $productIds)->orderBy('name')->get();
        
        // Filter out products that would cause overlap conflicts
        return $products->map(function ($product) {
            $product->isDisabled = $this->isProductDisabledForBatches($product->id, $this->edit_selected_batches, $this->edit_startDate, $this->edit_endDate, $this->edit_id);
            return $product;
        });
    }

    // Filtered edit products for searchable dropdown
    public function getFilteredEditProductsProperty()
    {
        $products = $this->availableProductsForEditBatches;
        $query = trim($this->editProductSearch);
        if ($query === '') {
            return $products;
        }
        $lower = strtolower($query);
        return $products->filter(function ($product) use ($lower) {
            return str_contains(strtolower($product->name ?? ''), $lower)
                || str_contains(strtolower((string) ($product->remarks ?? '')), $lower)
                || str_contains(strtolower((string) ($product->sku ?? '')), $lower)
                || str_contains(strtolower((string) ($product->supplier_code ?? '')), $lower)
                || str_contains(strtolower((string) ($product->product_number ?? '')), $lower);
        })->values();
    }


    // When dates change, validate selected products
    public function updatedStartDate()
    {
        $this->updatedSelectedProducts();
    }

    public function updatedEndDate()
    {
        $this->updatedSelectedProducts();
    }

    // When edit batch allocations change, reset products
    public function updatedEditSelectedBatches()
    {
        // Clear selected products when batch allocations change
        $this->edit_selected_products = [];
    }

    // When edit dates change, validate selected products
    public function updatedEditStartDate()
    {
        $this->updatedEditSelectedProducts(null);
    }

    public function updatedEditEndDate()
    {
        $this->updatedEditSelectedProducts(null);
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->statusFilter = '';
        $this->filterStartDate = '';
        $this->filterEndDate = '';
    }

    // Render
    public function render()
    {
        $items = Promo::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('code', 'like', '%' . $this->search . '%');
        })
        ->when($this->typeFilter, function ($query) {
            $query->where('type', $this->typeFilter);
        })
        ->when($this->filterStartDate, function ($query) {
            $query->where('startDate', '>=', $this->filterStartDate);
        })
        ->when($this->filterEndDate, function ($query) {
            $query->where('endDate', '<=', $this->filterEndDate);
        })
        ->when($this->statusFilter, function ($query) {
            $now = now();
            if ($this->statusFilter === 'active') {
                $query->where('startDate', '<=', $now)
                      ->where('endDate', '>=', $now);
            } elseif ($this->statusFilter === 'upcoming') {
                $query->where('startDate', '>', $now);
            } elseif ($this->statusFilter === 'expired') {
                $query->where('endDate', '<', $now);
            }
        })
        ->orderBy('created_at', 'desc')
        ->paginate($this->perPage);

        $viewModalProducts = !empty($this->view_selected_products)
            ? Product::whereIn('id', $this->view_selected_products)->orderBy('name')->get()
            : collect();

        return view('livewire.pages.sales-management.promo', [
            'items' => $items,
            'branches' => $this->branches,
            'products' => $this->products,
            'viewModalProducts' => $viewModalProducts,
            'batchAllocations' => $this->batchAllocations,
            'totalPromos' => Promo::count(),
            'activePromos' => Promo::where('startDate', '<=', now())
                ->where('endDate', '>=', now())
                ->count(),
            'upcomingPromos' => Promo::where('startDate', '>', now())->count(),
        ]);
    }
}