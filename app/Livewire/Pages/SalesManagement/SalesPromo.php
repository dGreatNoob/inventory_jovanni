<?php

namespace App\Livewire\Pages\SalesManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Promo;
use App\Models\Branch;
use App\Models\Product;
use Carbon\Carbon;

class SalesPromo extends Component
{
    use WithPagination;

    // Form fields
    public $promo_name, $promo_code, $promo_description, $startDate, $endDate;
    public $selected_batches = [], $selected_products = [], $selected_second_products = [];

    // Promo type
    public $promo_type = '';
    public $promo_type_options = ['Buy one Take one', '70% Discount', '60% Discount', '50% Discount'];

    // Lists
    public $branches = [], $products = [];

    // Dropdowns
    public $batchDropdown = false, $productDropdown = false, $secondProductDropdown = false, $showSecondProductDropdown = false;

    // Delete modal
    public $showDeleteModal = false;
    public $deleteId = null;

    // Edit modal fields
    public $showEditModal = false;
    public $edit_id, $edit_name, $edit_code, $edit_description, $edit_startDate, $edit_endDate, $edit_type;
    public $edit_selected_batches = [], $edit_selected_products = [], $edit_selected_second_products = [];
    public $editBatchDropdown = false, $editProductDropdown = false, $editSecondProductDropdown = false;

    // View modal fields
    public $showViewModal = false;
    public $view_name, $view_code, $view_type, $view_startDate, $view_endDate, $view_description;
    public $view_selected_batches = [], $view_selected_products = [], $view_selected_second_products = [];
    public $showCreatePanel = false;

    // Search & pagination
    public $search = '';
    public $perPage = 10;

    // Load branches and products
    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
        $this->products = Product::orderBy('name')->get();
    }

    public function showCreatePanel()
    {
        $this->showCreatePanel = true;
    }

    public function closeCreatePanel()
    {
        $this->showCreatePanel = false;
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
            'selected_batches' => 'required|array|min:1',
            'selected_batches.*' => 'exists:branches,id',
            'selected_products' => 'required|array|min:1',
            'selected_products.*' => 'exists:products,id',
            'promo_type' => 'required|string|in:' . implode(',', $this->promo_type_options),
        ];

        if ($this->promo_type === 'Buy one Take one') {
            $rules['selected_second_products'] = 'required|array|min:1|max:1';
            $rules['selected_second_products.*'] = 'exists:products,id';
        }

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
            'edit_selected_batches.*' => 'exists:branches,id',
            'edit_selected_products' => 'required|array|min:1',
            'edit_selected_products.*' => 'exists:products,id',
            'edit_type' => 'required|string|in:' . implode(',', $this->promo_type_options),
        ];

        if ($this->edit_type === 'Buy one Take one') {
            $rules['edit_selected_second_products'] = 'required|array|min:1|max:1';
            $rules['edit_selected_second_products.*'] = 'exists:products,id';
        }

        return $rules;
    }

    // Validation messages
    protected $messages = [
        'selected_batches.required' => 'Please select at least one batch.',
        'selected_products.required' => 'Please select at least one product.',
        'selected_second_products.required' => 'For Buy One Take One promotions, you must select a second product.',
        'selected_second_products.min' => 'Please select one second product.',
        'selected_second_products.max' => 'You can only select one second product for Buy One Take One.',
        'edit_selected_batches.required' => 'Please select at least one batch.',
        'edit_selected_products.required' => 'Please select at least one product.',
        'edit_selected_second_products.required' => 'For Buy One Take One promotions, you must select a second product.',
        'edit_selected_second_products.min' => 'Please select one second product.',
        'edit_selected_second_products.max' => 'You can only select one second product for Buy One Take One.',
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
            'branch' => json_encode($this->selected_batches), // Still storing branch IDs but calling it batches in UI
            'product' => json_encode($this->selected_products),
            'second_product' => $this->promo_type === 'Buy one Take one' ? json_encode($this->selected_second_products) : null,
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
            'selected_batches',
            'selected_products',
            'selected_second_products',
            'promo_type',
            'showSecondProductDropdown',
        ]);
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
        $this->view_selected_second_products = json_decode($promo->second_product ?? '[]', true);
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
        $this->edit_selected_second_products = json_decode($promo->second_product ?? '[]', true);

        $this->showEditModal = true;
    }

    // Update Promo with overlap validation
    public function update()
    {
        // Validate the edit form
        $this->validate($this->editRules());

        $promo = Promo::findOrFail($this->edit_id);

        // Additional validation for Buy One Take One
        if ($this->edit_type === 'Buy one Take one' && empty($this->edit_selected_second_products)) {
            $this->addError('edit_selected_second_products', 'For Buy One Take One promotions, you must select a second product.');
            return;
        }

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
            'second_product' => $this->edit_type === 'Buy one Take one' ? json_encode($this->edit_selected_second_products) : null,
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'Promo updated successfully!');
    }

    /**
     * Check for overlapping promos
     */
    private function checkForOverlappingPromos($excludeId = null)
    {
        // Determine if we're in create or edit mode
        $isEdit = $this->showEditModal;
        
        $startDate = $isEdit ? $this->edit_startDate : $this->startDate;
        $endDate = $isEdit ? $this->edit_endDate : $this->endDate;
        $batches = $isEdit ? $this->edit_selected_batches : $this->selected_batches;
        $products = $isEdit ? $this->edit_selected_products : $this->selected_products;
        $secondProducts = $isEdit ? $this->edit_selected_second_products : $this->selected_second_products;

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
                // Get batches and products from existing promo
                $existingBatches = $this->safeJsonDecode($existingPromo->branch);
                $existingMainProducts = $this->safeJsonDecode($existingPromo->product);
                $existingSecondProducts = $this->safeJsonDecode($existingPromo->second_product);

                // Check if any of our selected batches overlap with existing promo batches
                $batchOverlap = array_intersect($batches, $existingBatches);
                
                if (!empty($batchOverlap)) {
                    // Check if any of our selected products exist in the existing promo
                    foreach ($products as $productId) {
                        if (in_array($productId, $existingMainProducts) || in_array($productId, $existingSecondProducts)) {
                            $conflictingPromos->push([
                                'promo' => $existingPromo,
                                'product_id' => $productId,
                                'batch_ids' => $batchOverlap,
                                'type' => 'main'
                            ]);
                        }
                    }

                    // Check second products
                    foreach ($secondProducts as $productId) {
                        if (in_array($productId, $existingMainProducts) || in_array($productId, $existingSecondProducts)) {
                            $conflictingPromos->push([
                                'promo' => $existingPromo,
                                'product_id' => $productId,
                                'batch_ids' => $batchOverlap,
                                'type' => 'second'
                            ]);
                        }
                    }
                }
            }
        }

        // If we found conflicts, show errors
        if ($conflictingPromos->isNotEmpty()) {
            // Group by product to show consolidated errors
            $productConflicts = $conflictingPromos->groupBy('product_id');
            
            foreach ($productConflicts as $productId => $conflicts) {
                $productName = $this->products->firstWhere('id', $productId)->name ?? 'Unknown Product';
                $promoNames = $conflicts->pluck('promo.name')->unique()->implode(', ');
                
                // Get batch names for the conflict
                $batchIds = $conflicts->flatMap->batch_ids->unique();
                $batchNames = $this->branches->whereIn('id', $batchIds)->pluck('name')->implode(', ');
                
                $errorMessage = "Product '{$productName}' is already in promotion(s): {$promoNames} for batch(es): {$batchNames} during the selected dates.";
                
                // Determine which field to show error on
                $isSecondProduct = $conflicts->first()['type'] === 'second';
                $field = $isEdit 
                    ? ($isSecondProduct ? 'edit_selected_second_products' : 'edit_selected_products')
                    : ($isSecondProduct ? 'selected_second_products' : 'selected_products');
                
                session()->flash('error', $errorMessage);
                $this->addError($field, $errorMessage);
            }
            
            return true;
        }

        return false;
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

    // Handle first product selection for "Buy one Take one"
    public function updatedSelectedProducts()
    {
        if ($this->promo_type === 'Buy one Take one') {
            // If no products selected, clear second products and return
            if (empty($this->selected_products)) {
                $this->selected_second_products = [];
                return;
            }
            
            // Limit to only one product for Buy one Take one
            if (count($this->selected_products) > 1) {
                $last = end($this->selected_products);
                $this->selected_products = [$last];
            }

            // Filter second products based on first product's price
            $firstPrice = $this->products->firstWhere('id', $this->selected_products[0])->price ?? 0;

            $this->selected_second_products = array_filter($this->selected_second_products, function ($id) use ($firstPrice) {
                $product = $this->products->firstWhere('id', $id);
                return $product && $product->price <= $firstPrice;
            });
        }
    }

    public function updatedEditSelectedProducts($value)
    {
        if ($this->edit_type === 'Buy one Take one') {
            // If no products selected, clear second products and return
            if (empty($this->edit_selected_products)) {
                $this->edit_selected_second_products = [];
                return;
            }
            
            // Limit to only one product for Buy one Take one
            if (count($this->edit_selected_products) > 1) {
                $last = end($this->edit_selected_products);
                $this->edit_selected_products = [$last];
            }

            $this->edit_selected_second_products = [];
        }
        
        $this->validateOnly('edit_selected_products', $this->editRules());
    }

    public function updatedEditSelectedSecondProducts($value)
    {
        $this->validateOnly('edit_selected_second_products', $this->editRules());
    }

    public function updatedEditType($value)
    {
        // Reset second products when type changes
        if ($value !== 'Buy one Take one') {
            $this->edit_selected_second_products = [];
        }
        
        // Validate immediately when type changes
        $this->validateOnly('edit_selected_second_products', $this->editRules());
    }

    // Close modals
    public function cancelEdit()
    {
        $this->showEditModal = false;
        $this->resetValidation();
        $this->reset([
            'edit_id', 'edit_name', 'edit_code', 'edit_description', 
            'edit_startDate', 'edit_endDate', 'edit_type',
            'edit_selected_batches', 'edit_selected_products', 'edit_selected_second_products'
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
        if ($value === 'Buy one Take one') {
            $this->showSecondProductDropdown = true;
        } else {
            $this->showSecondProductDropdown = false;
            $this->selected_second_products = [];
            $this->secondProductDropdown = false;
        }
    }

    // Computed property for edit second products
    public function getEditSecondProductsProperty()
    {
        if (empty($this->edit_selected_products)) {
            return collect();
        }

        $firstProductId = $this->edit_selected_products[0];
        $firstProduct = $this->products->firstWhere('id', $firstProductId);
        
        if (!$firstProduct) {
            return collect();
        }

        $firstPrice = $firstProduct->price;

        return $this->products->filter(function ($product) use ($firstProductId, $firstPrice) {
            return $product->id != $firstProductId && $product->price <= $firstPrice;
        });
    }

    // Render
    public function render()
    {
        $items = Promo::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('code', 'like', '%' . $this->search . '%');
        })
        ->orderBy('created_at', 'desc')
        ->paginate($this->perPage);

        return view('livewire.pages.sales-management.promo', [
            'items' => $items,
            'branches' => $this->branches,
            'products' => $this->products,
            'totalPromos' => Promo::count(),
            'activePromos' => Promo::where('endDate', '>=', now())->count(),
        ]);
    }
}