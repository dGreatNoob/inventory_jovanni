<?php

namespace App\Livewire\Pages\SupplierManagement\Profile;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseOrder;

class Index extends Component
{
    use WithPagination;

    // Dashboard metrics
    public $totalSuppliers = 0;
    public $activeSuppliers = 0;
    public $totalOrders = 0;
    public $totalValue = 0;

    // Create form properties
    public $supplier_name, $supplier_code, $supplier_address, $contact_person, $contact_num, $email, $status;
    
    // Edit form properties
    public $edit_name, $edit_code, $edit_address, $edit_contact_person, $edit_contact_num, $edit_email, $edit_status;
    
    // View modal properties
    public $view_name, $view_code, $view_address, $view_contact_person, $view_contact_num, $view_email, $view_status, $view_categories = [];
    
    // Table settings
    public $perPage = 10;
    public $search = '';
    
    // Filter properties
    public $statusFilter = '';
    public $categoryFilter = '';
    
    // Modal states
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $showViewModal = false;
    public $showCreatePanel = false;
    
    // Selected item tracking
    public $deleteId = null;
    public $selectedItemId;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
    ];

    /**
     * Mount component and load dashboard metrics
     */
    public function mount()
    {
        $this->loadDashboardMetrics();
    }

    /**
     * Load dashboard overview metrics
     */
    public function loadDashboardMetrics()
    {
        // 1. Total Suppliers
        $this->totalSuppliers = Supplier::count();
        
        // 2. Active Suppliers
        $this->activeSuppliers = Supplier::where('status', 'active')->count();
        
        // 3. Total Orders (Purchase Orders)
        $this->totalOrders = PurchaseOrder::count();
        
        // 4. Total Value (Sum of all PO prices)
        $this->totalValue = PurchaseOrder::sum('total_price') ?? 0;
    }

    /**
     * Reset pagination when search is updated
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when per page is updated
     */
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when filters are updated
     */
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        $this->reset([
            'search',
            'statusFilter',
            'categoryFilter',
        ]);
        $this->resetPage();
    }

    /**
     * View supplier details in modal
     */
    public function view($id)
    {
        $supplier = Supplier::findOrFail($id);

        $this->view_name = $supplier->name;
        $this->view_code = $supplier->code;
        $this->view_address = $supplier->address;
        $this->view_contact_person = $supplier->contact_person;
        $this->view_contact_num = $supplier->contact_num;
        $this->view_email = $supplier->email;
        $this->view_status = $supplier->status;
        $this->view_categories = $supplier->categories ?? [];

        $this->showViewModal = true;
    }

    /**
     * Close the view modal
     */
    public function closeViewModal()
    {
        $this->reset([
            'view_name',
            'view_code',
            'view_address',
            'view_contact_person',
            'view_contact_num',
            'view_email',
            'view_status',
            'view_categories',
            'showViewModal',
        ]);
    }

    /**
     * Dynamic validation rules for create/edit
     */
    public function rules()
    {
        if ($this->selectedItemId) {
            // Edit mode: allow partial updates; only validate fields that have values
            return [
                'edit_name' => 'nullable|string|max:255',
                'edit_code' => 'nullable|string|max:50|unique:suppliers,code,' . $this->selectedItemId,
                'edit_address' => 'nullable|string|max:500',
                'edit_contact_person' => 'nullable|string|max:255',
                'edit_contact_num' => ['nullable', 'regex:/^[0-9+\-\(\)\s]*$/'],
                'edit_email' => 'nullable|email',
                'edit_status' => 'nullable|string|in:active,inactive',
            ];
        }

        // Create mode validation
        return [
            'supplier_name' => 'required|string|max:255',
            'supplier_code' => 'required|string|max:50|unique:suppliers,code',
            'supplier_address' => 'required|string|max:500',
            'contact_person' => 'required|string|max:255',
            'contact_num' => ['required', 'regex:/^[0-9+\-\(\)\s]+$/'],
            'email' => 'required|email',
        ];
    }

    /**
     * Create new supplier
     */
    public function submit()
    {
        $this->validate();

        Supplier::create([
            'entity_id' => 1, // Default entity ID
            'name' => $this->supplier_name,
            'code' => $this->supplier_code,
            'address' => $this->supplier_address,
            'contact_person' => $this->contact_person,
            'contact_num' => $this->contact_num,
            'email' => $this->email,
            'status' => 'active',
            'is_active' => true,
        ]);

        session()->flash('message', 'Supplier profile added successfully.');
        
        $this->reset([
            'supplier_name',
            'supplier_code',
            'supplier_address',
            'contact_person',
            'contact_num',
            'email',
            'showCreatePanel',
        ]);

        // Refresh dashboard metrics
        $this->loadDashboardMetrics();
    }

    /**
     * Load supplier data into edit modal
     */
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        $this->selectedItemId = $id;
        $this->edit_name = $supplier->name;
        $this->edit_code = $supplier->code;
        $this->edit_address = $supplier->address;
        $this->edit_contact_person = $supplier->contact_person;
        $this->edit_contact_num = $supplier->contact_num;
        $this->edit_email = $supplier->email;
        $this->edit_status = $supplier->status;

        $this->showEditModal = true;
    }

    /**
     * Update supplier record
     */
    public function update()
    {
        $this->validate();

        $supplier = Supplier::findOrFail($this->selectedItemId);

        // Only update fields that have values; keep existing for empty fields
        $supplier->update([
            'name' => filled($this->edit_name) ? $this->edit_name : $supplier->name,
            'code' => filled($this->edit_code) ? $this->edit_code : $supplier->code,
            'address' => filled($this->edit_address) ? $this->edit_address : $supplier->address,
            'contact_person' => filled($this->edit_contact_person) ? $this->edit_contact_person : $supplier->contact_person,
            'contact_num' => filled($this->edit_contact_num) ? $this->edit_contact_num : $supplier->contact_num,
            'email' => filled($this->edit_email) ? $this->edit_email : $supplier->email,
            'status' => filled($this->edit_status) ? $this->edit_status : $supplier->status,
        ]);

        session()->flash('message', 'Supplier profile updated successfully.');
        
        // Reset edit form and close panel
        $this->reset([
            'selectedItemId',
            'edit_name',
            'edit_code',
            'edit_address',
            'edit_contact_person',
            'edit_contact_num',
            'edit_email',
            'edit_status',
            'showEditModal',
        ]);

        // Refresh dashboard metrics
        $this->loadDashboardMetrics();
    }

    /**
     * Show delete confirmation modal
     */
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Delete supplier
     */
    public function delete()
    {
        try {
            $supplier = Supplier::findOrFail($this->deleteId);
            $supplier->delete();

            session()->flash('message', 'Supplier profile deleted successfully.');
            
            // Refresh dashboard metrics
            $this->loadDashboardMetrics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting supplier: ' . $e->getMessage());
        }

        $this->cancel();
    }

    /**
     * Close the create panel
     */
    public function closeCreatePanel()
    {
        $this->resetValidation();
        $this->reset([
            'showCreatePanel',
            'supplier_name',
            'supplier_code',
            'supplier_address',
            'contact_person',
            'contact_num',
            'email',
        ]);
    }

    /**
     * Reset the create form
     */
    public function resetForm()
    {
        $this->resetValidation();
        $this->reset([
            'supplier_name',
            'supplier_code',
            'supplier_address',
            'contact_person',
            'contact_num',
            'email',
        ]);
    }

    /**
     * Cancel and close modals
     */
    public function cancel()
    {
        $this->resetValidation();
        $this->reset([
            'showDeleteModal',
            'showEditModal',
            'deleteId',
            'selectedItemId',
            'supplier_name',
            'supplier_code',
            'supplier_address',
            'contact_person',
            'contact_num',
            'email',
            'edit_name',
            'edit_code',
            'edit_address',
            'edit_contact_person',
            'edit_contact_num',
            'edit_email',
            'edit_status',
        ]);
    }

    /**
     * Get all categories associated with a supplier (direct + from products)
     */
    public function getAllCategoriesForSupplier($supplier)
    {
        // Get supplier's direct categories
        $supplierCategories = $supplier->categories ?? [];
        
        // Get categories from supplier's products
        $productCategories = $supplier->products()
            ->whereNotNull('category_id')
            ->distinct()
            ->pluck('category_id')
            ->toArray();
        
        // Merge and remove duplicates
        $allCategoryIds = array_unique(array_merge($supplierCategories, $productCategories));
        
        // Get category models
        return Category::whereIn('id', $allCategoryIds)->get();
    }

    /**
     * Render component with suppliers list
     */
    public function render()
    {
        if (!auth()->user()->hasAnyPermission(['supplier view'])) {
            return view('livewire.pages.errors.403');
        }

        $search = trim($this->search);

        $items = Supplier::when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('contact_num', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        })
        ->when($this->statusFilter, function ($query) {
            $query->where('status', $this->statusFilter);
        })
        ->when($this->categoryFilter, function ($query) {
            $query->where(function ($q) {
                // Filter by direct supplier categories (JSON field) - check if category ID or name exists in JSON
                $q->whereJsonContains('categories', $this->categoryFilter)
                  // Or filter by products that have this category
                  ->orWhereHas('products', function ($productQuery) {
                      $productQuery->where('category_id', $this->categoryFilter);
                  });
            });
        })
        ->with([
            'products' => function($query) {
                $query->select('id', 'supplier_id', 'category_id')
                      ->whereNotNull('category_id');
            },
            'purchaseOrders' => function($query) {
                $query->select('id', 'supplier_id', 'total_price', 'order_date')
                      ->orderBy('order_date', 'desc');
            }
        ])
        ->withCount(['products' => function ($query) {
            $query->where('disabled', false); // Active products only
        }])
        ->latest()
        ->paginate($this->perPage);

        // Get all categories for the filter dropdown
        $categories = Category::active()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($category) {
                return [$category->id => $category->name];
            });

        return view('livewire.pages.supplier-management.profile.index', compact('items', 'categories'));
    }
}