<?php

namespace App\Livewire\Pages\SupplierManagement\Profile;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\PurchaseOrder;
use App\Models\Product;

class View extends Component
{
    use WithPagination;
    
    public $supplier_id;
    public $availableCategories = [];
    public $supplier_name, $supplier_code, $supplier_address, $contact_person, $contact_num, $email, $tin_num, $status;
    public $perPage = 10;
    public $search = '';
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $deleteId;
    
    // Edit form properties
    public $selectedItemId;
    public $edit_status;

    // Modal handling methods
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            $product = Product::findOrFail($this->deleteId);
            
            // Delete the product from both products and supplier products
            if ($product) {
                // Delete from main products
                $product->delete();
                
                session()->flash('message', 'Product successfully deleted from the system.');
                $this->showDeleteModal = false;
                $this->reset('deleteId');
                
                // Refresh the counts and data
                $this->loadSupplier();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting product: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        // Close any open modals and clear edit state safely.
        $this->showDeleteModal = false;
        $this->showEditModal = false;

        // Clear validation errors
        $this->resetValidation();

        // Reset all form-related properties
        $this->reset(['selectedItemId', 'edit_status', 'deleteId']);
    }

    public function edit($id)
    {
        $item = Product::findOrFail($id);
        $this->selectedItemId = $id;
        $this->edit_status = $item->disabled ? 'inactive' : 'active';
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'edit_status' => 'required|in:active,inactive'
        ]);

        $product = Product::findOrFail($this->selectedItemId);
        $product->update([
            'disabled' => $this->edit_status === 'inactive'
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'Product updated successfully.');
    }

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount($id)
    {
        $this->supplier_id = $id;
        $this->loadSupplier();
    }

    public function loadSupplier()
    {
        $supplier = Supplier::findOrFail($this->supplier_id);
        
        $this->supplier_name = $supplier->name;
        $this->supplier_code = $supplier->code;
        $this->supplier_address = $supplier->address;
        $this->contact_person = $supplier->contact_person;
        $this->contact_num = $supplier->contact_num;
        $this->email = $supplier->email;
        $this->tin_num = $supplier->tin_num;
        $this->status = $supplier->status;

        $this->availableCategories = Category::active()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function render()
    {
        $search = trim($this->search);

        $items = Product::where('supplier_id', $this->supplier_id)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('supplier_sku', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($this->perPage);

        $activeProductCount = Product::where('supplier_id', $this->supplier_id)
            ->where('disabled', false)
            ->count();

        return view('livewire.pages.supplier-management.profile.view', [
            'items' => $items,
            'supplier' => Supplier::find($this->supplier_id),
            'activeProductCount' => $activeProductCount
        ]);
    }
}