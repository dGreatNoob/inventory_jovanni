<?php

namespace App\Livewire\Pages\SalesManagement;

use App\Enums\Enum\PermissionEnum;
use App\Enums\RolesEnum;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\SalesOrder;
use App\Models\SalesOrderBranchItem;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class View extends Component
{

    public $salesOrderId;
    public $salesOrderResult;
    public $company_results;
    public $product_results;
    public $shippingMethodDropDown = [];
    public $paymentMethodDropdown = [];
    public $paymentTermsDropdown = [];
    
    // Modal properties for Change Price feature
    public $showChangePriceModal = false;
    public $selectedBranchItemId = null;
    public $availableBranches = [];
    public $availableProducts = [];
    public $filteredProducts = [];
    public $currentUnitPrice = '';
    public $newUnitPrice = '';
    public $selectedBranchIds = [];
    public $selectedProductId = '';
    
    public function mount($salesOrderId)
    {
        $this->salesOrderId = $salesOrderId;
        $this->salesOrderResult = SalesOrder::findOrFail($salesOrderId);
        $this->company_results  = \App\Models\Customer::all()->pluck('name', 'id');
        $this->product_results  = \App\Models\Product::all()->pluck('name', 'id');
        $this->shippingMethodDropDown = SalesOrder::shippingMethodDropDown();
        $this->paymentMethodDropdown  = SalesOrder::paymentMethodDropdown();
        $this->paymentTermsDropdown   = SalesOrder::paymentTermsDropdown();
        
        // Load available branches and products for Change Price modal
        $this->availableBranches = Branch::all()->pluck('name', 'id');
        $this->availableProducts = Product::all()->pluck('name', 'id');
        $this->filteredProducts = Product::all()->pluck('name', 'id');
    }

    public function approveSalesOrder()
    {
        // if (!Auth::user()->can(PermissionEnum::APPROVE_REQUEST_SLIP->value)) {

        //     abort(403, 'You do not have permission to approve this request slip.');

        // } else {
        //     $this->request_slip->update([
        //         'status' => 'approved',
        //         'approver' => Auth::user()->id,

        //     ]);
        //     session()->flash('message', 'Request Slip approved successfully.');
        //     return redirect()->route('requisition.requestslip');
        // }

        $this->salesOrderResult->status = 'approved'; 
        $this->salesOrderResult->approver = Auth::user()->id; 
        $this->salesOrderResult->save();

        session()->flash('message', 'Sales order approved successfully.');
        return redirect()->route('salesorder.index');
    }

    public function rejectSalesOrder()
    {
        // if (!Auth::user()->can(PermissionEnum::APPROVE_REQUEST_SLIP->value)) {

        //     abort(403, 'You do not have permission to approve this request slip.');

        // } else {
        //     $this->request_slip->update([
        //         'status' => 'rejected',
        //         'approver' => Auth::user()->id,

        //     ]);
        //     session()->flash('message', 'Request Slip Rejected.');
        //     return redirect()->route('requisition.requestslip');
        // }
               
        $this->salesOrderResult->status = 'rejected'; 
        $this->salesOrderResult->approver = Auth::user()->id; 
        $this->salesOrderResult->save();
       
        session()->flash('message', 'Sales Order rejected successfully.');
        return redirect()->route('salesorder.index');
    }



    /**
     * Filter products based on selected branch
     */
    public function updatedSelectedBranchId($branchId)
    {
        // Reset product selection when branch changes
        $this->selectedProductId = '';
        $this->currentUnitPrice = '';
        $this->newUnitPrice = '';

        if ($branchId) {
            // For now, show all products. You can implement branch-specific filtering here
            // if you have a relationship between branches and products
            $this->filteredProducts = Product::all()->pluck('name', 'id');
        } else {
            $this->filteredProducts = [];
        }
    }

    /**
     * Update current unit price when product is selected
     */
    public function updatedSelectedProductId($productId)
    {
        $this->currentUnitPrice = '';
        $this->newUnitPrice = '';

        if ($productId) {
            // Get the current unit price from the product's base price
            $product = Product::find($productId);
            if ($product) {
                $this->currentUnitPrice = $product->price;
                $this->newUnitPrice = $product->price; // Set as default
            }
        }
    }

    /**
     * Open Change Price Modal
     */
    public function openChangePriceModal($branchItemId = null)
    {
        $this->resetErrorBag();
        $this->selectedBranchItemId = $branchItemId;
        
        if ($branchItemId) {
            // Get existing branch item data
            $branchItem = SalesOrderBranchItem::find($branchItemId);
            if ($branchItem) {
                $this->selectedBranchIds = $branchItem->branch_id;
                $this->selectedProductId = $branchItem->product_id;
                $this->currentUnitPrice = $branchItem->unit_price;
                $this->newUnitPrice = $branchItem->unit_price;
                $this->filteredProducts = Product::all()->pluck('name', 'id');
            }
        } else {
            // Reset for new entry
            $this->reset([
                'selectedBranchIds',
                'selectedProductId',
                'currentUnitPrice',
                'newUnitPrice'
            ]);
            $this->filteredProducts = Product::all()->pluck('name', 'id');
        }
        
        $this->showChangePriceModal = true;
    }

    /**
     * Close Change Price Modal
     */
    public function closeChangePriceModal()
    {
        $this->resetErrorBag();
        $this->reset([
            'selectedBranchItemId',
            'selectedBranchIds',
            'selectedProductId',
            'currentUnitPrice',
            'newUnitPrice',
            'filteredProducts'
        ]);
        $this->showChangePriceModal = false;
    }

    /**
     * Update Price for Branch Item
     */
    public function updatePrice()
    {
        $this->validate([
            'selectedBranchIds' => 'required|array|min:1',
            'selectedBranchIds.*' => 'exists:branches,id',
            'newUnitPrice' => 'required|numeric|min:0.01',
        ], [
            'selectedBranchIds.required' => 'Please select a branch.',
            'selectedBranchIds.exists' => 'Selected branch does not exist.',
            'selectedProductId.required' => 'Please select a product.',
            'selectedProductId.exists' => 'Selected product does not exist.',
            'newUnitPrice.required' => 'Please enter a unit price.',
            'newUnitPrice.numeric' => 'Unit price must be a valid number.',
            'newUnitPrice.min' => 'Unit price must be at least 0.01.',
        ]);

        try {
            if ($this->selectedBranchItemId) {
                // Update existing branch item
                $branchItem = SalesOrderBranchItem::find($this->selectedBranchItemId);
                if ($branchItem) {
                    $branchItem->update([
                        'branch_id' => $this->selectedBranchIds,
                        'product_id' => $this->selectedProductId,
                        'unit_price' => $this->newUnitPrice,
                        'subtotal' => $this->newUnitPrice * $branchItem->quantity,
                    ]);
                    session()->flash('message', 'Price updated successfully.');
                } else {
                    session()->flash('error', 'Branch item not found.');
                }
            } else {
                // Create new branch item
                $salesOrder = SalesOrder::find($this->salesOrderId);
                if ($salesOrder) {
                    // Check if this branch-product combination already exists
                    $existingItem = SalesOrderBranchItem::where('sales_order_id', $this->salesOrderId)
                        ->where('branch_id', $this->selectedBranchIds)
                        ->where('product_id', $this->selectedProductId)
                        ->first();
                    
                    if ($existingItem) {
                        // If it exists, update quantity instead of creating new
                        $existingItem->update([
                            'quantity' => $existingItem->quantity + 1,
                            'unit_price' => $this->newUnitPrice,
                            'subtotal' => ($existingItem->quantity + 1) * $this->newUnitPrice,
                        ]);
                        session()->flash('message', 'Item quantity updated successfully.');
                    } else {
                        // Create new item with default quantity of 1
                        SalesOrderBranchItem::create([
                            'sales_order_id' => $this->salesOrderId,
                            'branch_id' => $this->selectedBranchIds,
                            'product_id' => $this->selectedProductId,
                            'unit_price' => $this->newUnitPrice,
                            'quantity' => 1,
                            'subtotal' => $this->newUnitPrice,
                        ]);
                        session()->flash('message', 'Branch item added successfully.');
                    }
                } else {
                    session()->flash('error', 'Sales order not found.');
                }
            }
            
            $this->closeChangePriceModal();
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
            Log::error('Change Price Error: ' . $e->getMessage(), [
                'sales_order_id' => $this->salesOrderId,
                'selectedBranchIds' => $this->selectedBranchIds,
                'selectedProductId' => $this->selectedProductId,
                'newUnitPrice' => $this->newUnitPrice,
                'exception' => $e
            ]);
        }
    }

    public function render()
    {
        $salesOrder = SalesOrder::with(['customers', 'branchItems.product'])->find($this->salesOrderId);

        return view('livewire.pages.sales-management.view', [
            'sales_order_view' => $salesOrder,
        ]);
    }
}
