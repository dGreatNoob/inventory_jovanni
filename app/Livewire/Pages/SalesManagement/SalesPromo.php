<?php

namespace App\Livewire\Pages\SalesManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Promo;
use App\Models\Branch;
use App\Models\Product;

class SalesPromo extends Component
{
    use WithPagination;

    // Form fields
    public $promo_name;
    public $promo_code;
    public $promo_description;
    public $startDate;
    public $endDate;
    public $selected_branches = [];
    public $selected_products = [];
    public $selected_second_products = [];

    // Promo type
    public $promo_type_options = ['Buy one Take one', '70% Discount', '60% Discount'];
    public $promo_type = '';

    public $branches = [];
    public $products = [];

    public $branchDropdown = false;
    public $productDropdown = false;
    public $secondProductDropdown = false;
    public $showSecondProductDropdown = false;

    public $showDeleteModal = false;
    public $deleteId = null;

    // Search & pagination
    public $search = '';
    public $perPage = 10;

    // Edit modal fields
    public $showEditModal = false;
    public $edit_id;
    public $edit_name;
    public $edit_code;
    public $edit_description;
    public $edit_startDate;
    public $edit_endDate;
    public $edit_type;
    public $edit_selected_branches = [];
    public $edit_selected_products = [];
    public $edit_selected_second_products = [];
    public $editBranchDropdown = false;
    public $editProductDropdown = false;
    public $editSecondProductDropdown = false;

    // View modal fields
    public $showViewModal = false;
    public $view_name;
    public $view_code;
    public $view_type;
    public $view_startDate;
    public $view_endDate;
    public $view_selected_branches = [];
    public $view_selected_products = [];
    public $view_selected_second_products = [];
    public $view_description;

    // Load branches and products
    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
        $this->products = Product::orderBy('name')->get();
    }

    // Validation rules
    protected function rules()
    {
        $rules = [
            'promo_name' => 'required|string|max:255',
            'promo_code' => 'nullable|string|max:100|unique:promos,code',
            'promo_description' => 'nullable|string|max:500',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'selected_branches' => 'required|array|min:1',
            'selected_branches.*' => 'exists:branches,id',
            'selected_products' => 'required|array|min:1',
            'selected_products.*' => 'exists:products,id',
            'promo_type' => 'required|string|in:' . implode(',', $this->promo_type_options),
        ];

        if ($this->promo_type === 'Buy one Take one') {
            $rules['selected_second_products'] = 'required|array|min:1';
            $rules['selected_second_products.*'] = 'exists:products,id';
        }

        return $rules;
    }

    // Create Promo
    public function submit()
    {
        $this->validate();

        Promo::create([
            'name' => $this->promo_name,
            'code' => $this->promo_code,
            'description' => $this->promo_description,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'branch' => json_encode($this->selected_branches),
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
            'selected_branches',
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
        $this->view_selected_branches = json_decode($promo->branch, true) ?? [];
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
        $this->edit_selected_branches = json_decode($promo->branch, true) ?? [];
        $this->edit_selected_products = json_decode($promo->product, true) ?? [];
        $this->edit_selected_second_products = json_decode($promo->second_product ?? '[]', true);

        $this->showEditModal = true;
    }

    // Update Promo
    public function update()
    {
        $promo = Promo::findOrFail($this->edit_id);

        $promo->update([
            'name' => $this->edit_name,
            'code' => $this->edit_code,
            'description' => $this->edit_description,
            'startDate' => $this->edit_startDate,
            'endDate' => $this->edit_endDate,
            'type' => $this->edit_type,
            'branch' => json_encode($this->edit_selected_branches),
            'product' => json_encode($this->edit_selected_products),
            'second_product' => $this->edit_type === 'Buy one Take one' ? json_encode($this->edit_selected_second_products) : null,
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'Promo updated successfully!');
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

    public function updatedEditSelectedProducts()
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
    }

    // Close modals
    public function cancelEdit()
    {
        $this->showEditModal = false;
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
            return $this->products;
        }

        $firstProductId = $this->edit_selected_products[0];
        $firstPrice = $this->products->firstWhere('id', $firstProductId)->price ?? 0;

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
