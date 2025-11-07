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
    public $supplier_name, $supplier_code, $supplier_address, $contact_person, $contact_num, $email, $tin_num, $status;
    
    // Edit form properties
    public $edit_name, $edit_code, $edit_address, $edit_contact_person, $edit_contact_num, $edit_email, $edit_tin_num, $edit_status;
    
    // View modal properties
    public $view_name, $view_code, $view_address, $view_contact_person, $view_contact_num, $view_email, $view_tin_num, $view_status, $view_categories = [];
    
    // Table settings
    public $perPage = 10;
    public $search = '';
    
    // Modal states
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $showViewModal = false;
    
    // Selected item tracking
    public $deleteId = null;
    public $selectedItemId;

    protected $queryString = [
        'search' => ['except' => ''],
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
        $this->view_tin_num = $supplier->tin_num;
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
            'view_tin_num',
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
            // Edit mode validation
            return [
                'edit_name' => 'required|string|max:255',
                'edit_code' => 'required|string|max:50|unique:suppliers,code,' . $this->selectedItemId,
                'edit_address' => 'required|string|max:500',
                'edit_contact_person' => 'required|string|max:255',
                'edit_contact_num' => ['required', 'regex:/^[0-9+\-\(\)\s]+$/'],
                'edit_email' => 'required|email|unique:suppliers,email,' . $this->selectedItemId,
                'edit_tin_num' => 'nullable|string|max:255',
                'edit_status' => 'required|string|in:active,inactive,pending',
            ];
        }

        // Create mode validation
        return [
            'supplier_name' => 'required|string|max:255',
            'supplier_code' => 'required|string|max:50|unique:suppliers,code',
            'supplier_address' => 'required|string|max:500',
            'contact_person' => 'required|string|max:255',
            'contact_num' => ['required', 'regex:/^[0-9+\-\(\)\s]+$/'],
            'email' => 'required|email|unique:suppliers,email',
            'tin_num' => 'nullable|string|max:255',
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
            'tin_num' => $this->tin_num ?? '',
            'status' => 'pending',
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
            'tin_num',
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
        $this->edit_tin_num = $supplier->tin_num;
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

        $supplier->update([
            'name' => $this->edit_name,
            'code' => $this->edit_code,
            'address' => $this->edit_address,
            'contact_person' => $this->edit_contact_person,
            'contact_num' => $this->edit_contact_num,
            'email' => $this->edit_email,
            'tin_num' => $this->edit_tin_num,
            'status' => $this->edit_status,
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'Supplier profile updated successfully.');
        
        // Reset edit form
        $this->reset([
            'selectedItemId',
            'edit_name',
            'edit_code',
            'edit_address',
            'edit_contact_person',
            'edit_contact_num',
            'edit_email',
            'edit_tin_num',
            'edit_status',
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
            'tin_num',
            'edit_name',
            'edit_code',
            'edit_address',
            'edit_contact_person',
            'edit_contact_num',
            'edit_email',
            'edit_tin_num',
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

        return view('livewire.pages.supplier-management.profile.index', compact('items'));
    }
}