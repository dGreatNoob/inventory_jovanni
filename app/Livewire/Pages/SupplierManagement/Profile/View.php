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
    public $purchaseOrders;
    
    // ✅ Dashboard metrics
    public $totalOrders = 0;
    public $totalSpent = 0;
    public $activeProductCount = 0;
    public $deliveryPerformance = 0;
    public $deliveryPerformanceColor = 'gray';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    /**
     * Mount component and load supplier data
     */
    public function mount($id)
    {
        $this->supplier_id = $id;
        $this->loadSupplier();
        $this->loadDashboardMetrics();

        // Load only THIS supplier's purchase orders
        $this->purchaseOrders = PurchaseOrder::with(['productOrders', 'deliveries'])
            ->where('supplier_id', $this->supplier_id)
            ->latest()
            ->take(10)
            ->get();
    }

    /**
     * Load supplier information
     */
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

    /**
     * ✅ Load dashboard metrics for supplier
     */
    public function loadDashboardMetrics()
    {
        // 1. Total Orders (Count of Purchase Orders for this supplier)
        $this->totalOrders = PurchaseOrder::where('supplier_id', $this->supplier_id)->count();
        
        // 2. Total Spent (Sum of all PO total prices)
        $this->totalSpent = PurchaseOrder::where('supplier_id', $this->supplier_id)
            ->sum('total_price') ?? 0;
        
        // 3. Active Products (Products not disabled)
        $this->activeProductCount = Product::where('supplier_id', $this->supplier_id)
            ->where('disabled', false)
            ->count();
        
        // 4. Delivery Performance (% of orders with complete delivery)
        $this->calculateDeliveryPerformance();
    }

    /**
     * ✅ Calculate delivery performance percentage
     */
    private function calculateDeliveryPerformance()
    {
        $purchaseOrders = PurchaseOrder::where('supplier_id', $this->supplier_id)
            ->with('productOrders')
            ->get();
        
        $totalPOs = $purchaseOrders->count();
        
        if ($totalPOs === 0) {
            $this->deliveryPerformance = 0;
            $this->deliveryPerformanceColor = 'gray';
            return;
        }
        
        $fullyDeliveredCount = 0;
        
        foreach ($purchaseOrders as $po) {
            $allItemsDelivered = true;
            
            foreach ($po->productOrders as $productOrder) {
                $expected = $productOrder->expected_qty ?? $productOrder->quantity;
                $received = $productOrder->received_quantity ?? 0;
                $destroyed = $productOrder->destroyed_qty ?? 0;
                $totalDelivered = $received + $destroyed;
                
                // Check if this item is fully delivered
                if ($totalDelivered < $expected) {
                    $allItemsDelivered = false;
                    break;
                }
            }
            
            if ($allItemsDelivered && $po->productOrders->count() > 0) {
                $fullyDeliveredCount++;
            }
        }
        
        // Calculate percentage
        $this->deliveryPerformance = ($fullyDeliveredCount / $totalPOs) * 100;
        
        // Set color based on performance
        if ($this->deliveryPerformance >= 90) {
            $this->deliveryPerformanceColor = 'green';
        } elseif ($this->deliveryPerformance >= 70) {
            $this->deliveryPerformanceColor = 'yellow';
        } elseif ($this->deliveryPerformance >= 50) {
            $this->deliveryPerformanceColor = 'orange';
        } else {
            $this->deliveryPerformanceColor = 'red';
        }
    }

    /**
     * Modal handling: Confirm delete
     */
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Delete product
     */
    public function delete()
    {
        try {
            $product = Product::findOrFail($this->deleteId);
            
            if ($product) {
                $product->delete();
                
                session()->flash('message', 'Product successfully deleted from the system.');
                $this->showDeleteModal = false;
                $this->reset('deleteId');
                
                // Refresh metrics after deletion
                $this->loadDashboardMetrics();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting product: ' . $e->getMessage());
        }
    }

    /**
     * Cancel modal actions
     */
    public function cancel()
    {
        $this->showDeleteModal = false;
        $this->showEditModal = false;
        $this->resetValidation();
        $this->reset(['selectedItemId', 'edit_status', 'deleteId']);
    }

    /**
     * Edit product status
     */
    public function edit($id)
    {
        $item = Product::findOrFail($id);
        $this->selectedItemId = $id;
        $this->edit_status = $item->disabled ? 'inactive' : 'active';
        $this->showEditModal = true;
    }

    /**
     * Update product status
     */
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
        
        // Refresh active product count
        $this->loadDashboardMetrics();
    }

    /**
     * Render component
     */
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

        return view('livewire.pages.supplier-management.profile.view', [
            'items' => $items,
            'supplier' => Supplier::find($this->supplier_id),
        ]);
    }
}