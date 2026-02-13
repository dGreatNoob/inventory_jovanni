<?php

namespace App\Livewire\Pages\POManagement\PurchaseOrder;

use App\Models\Currency;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\ProductOrder;
use App\Models\Supplier;
use App\Models\Department;
use App\Models\Category;
use App\Services\ProductService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Edit extends Component
{
    use WithPagination;

    // Form properties
    public $purchaseOrder;
    public $Id;
    public $po_num;
    public $po_type = 'products';
    public $ordered_by;

    #[Validate('required|exists:suppliers,id')]
    public $supplier_id;

    #[Validate('required|date')]
    public $order_date;

    #[Validate('nullable|date|after_or_equal:order_date')]
    public $expected_delivery_date;

    #[Validate('required|exists:departments,id')]
    public $deliver_to;

    // Item modal properties
    public $selected_product;
    public $unit_price;
    public $order_qty;

    #[Url(as: 'q')]
    public $search = '';
    public $categoryFilter = '';

    public $showModal = false;
    public $editingItemIndex = null;

    /** Add-by-supplier-code form */
    public $addBySupplierCode = '';
    public $addMode = 'search'; // 'search' | 'supplier_code'

    // Cache for ordered items
    #[Validate('required|array|min:1')]
    public $orderedItems = [];

    public int $orderedItemsPerPage = 10;
    public $orderedItemsPage = 1;

    protected $messages = [
        'orderedItems.required' => 'Please add at least one item to the order.',
        'orderedItems.min' => 'Please add at least one item to the order.',
        'supplier_id.required' => 'Please select a supplier.',
        'supplier_id.exists' => 'The selected supplier is invalid.',
        'deliver_to.required' => 'Please select a receiving department.',
        'deliver_to.exists' => 'The selected department is invalid.',
        'selected_product.required' => 'Please select a product.',
        'selected_product.exists' => 'The selected product is invalid.',
        'order_qty.required' => 'Please enter the order quantity.',
        'order_qty.numeric' => 'Order quantity must be a number.',
        'order_qty.min' => 'Order quantity must be greater than 0.',
        'unit_price.required' => 'Please enter the unit price.',
        'unit_price.numeric' => 'Unit price must be a number.',
        'unit_price.min' => 'Unit price must be 0 or greater.',
    ];

    protected $itemRules = [
        'selected_product' => 'required|exists:products,id',
        'order_qty' => 'required|numeric|min:1',
        'unit_price' => 'required|numeric|min:0',
    ];

    public function mount($Id)
    {
        $this->Id = $Id;
        $this->purchaseOrder = PurchaseOrder::with([
            'supplier.defaultCurrency',
            'currency',
            'productOrders.product.category',
            'orderedByUser',
            'department',
            'approvalLogs',
        ])->findOrFail($Id);

        if (! $this->purchaseOrder->canEdit()) {
            session()->flash('error', 'Only open purchase orders can be edited.');
            return redirect()->route('pomanagement.purchaseorder');
        }

        // Set form values
        $this->po_num = $this->purchaseOrder->po_num;
        $this->po_type = $this->purchaseOrder->po_type;
        $this->ordered_by = $this->purchaseOrder->orderedByUser ? $this->purchaseOrder->orderedByUser->name : '';
        $this->supplier_id = $this->purchaseOrder->supplier_id;
        $this->order_date = $this->purchaseOrder->order_date->format('Y-m-d');
        $this->expected_delivery_date = $this->purchaseOrder->expected_delivery_date ? $this->purchaseOrder->expected_delivery_date->format('Y-m-d') : null;
        $this->deliver_to = $this->purchaseOrder->del_to;

        // Set ordered items from productOrders
        foreach ($this->purchaseOrder->productOrders as $order) {
            $this->orderedItems[] = [
                'id' => $order->id,
                'product_id' => $order->product_id,
                'sku' => $order->product->sku,
                'name' => $order->product->name,
                'category' => $order->product->category->name ?? 'N/A',
                'supplier' => $order->product->supplier->name ?? 'N/A',
                'supplier_code' => $order->product->supplier_code ?? 'N/A',
                'unit_price' => $order->unit_price,
                'order_qty' => $order->quantity,
                'total_price' => $order->total_price,
            ];
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

    public function updatedSupplierId()
    {
        $this->reset(['selected_product', 'unit_price', 'order_qty', 'search', 'categoryFilter']);
    }
    
    public function getProductsProperty()
    {
        if (empty($this->supplier_id)) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                5,
                1
            );
        }

        $query = Product::with(['category', 'supplier'])
            ->where('supplier_id', $this->supplier_id)
            ->where('disabled', false)
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%')
                  ->orWhere('barcode', 'like', '%' . $this->search . '%');
            });

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        return $query->orderBy('name')->paginate(5);
    }

    public function getCategoriesProperty()
    {
        return Category::with('parent')
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();
    }

    public function getDepartmentsProperty()
    {
        return Department::orderBy('name')->get();
    }

    public function getSuppliersProperty()
    {
        return Supplier::when($this->supplier_id, function ($q) {
            $q->where('is_active', true)->orWhere('id', $this->supplier_id);
        }, fn ($q) => $q->where('is_active', true))
            ->with('defaultCurrency')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get the currency for the PO (from PO, supplier, or base PHP).
     */
    public function getSelectedCurrencyProperty()
    {
        return $this->purchaseOrder?->currency
            ?? $this->purchaseOrder?->supplier?->defaultCurrency
            ?? Currency::base();
    }

    public function openModal()
    {
        if (empty($this->supplier_id)) {
            session()->flash('error', 'Please select a supplier first.');
            return;
        }
        
        $this->showModal = true;
        $this->reset(['selected_product', 'unit_price', 'order_qty', 'search', 'categoryFilter', 'editingItemIndex']);
    }

    public function selectProduct($id)
    {
        $this->selected_product = $id;
        $product = Product::find($id);
        
        if ($product) {
            $this->unit_price = $product->cost;
        }
    }

    public function editItem($index)
    {
        $items = $this->orderedItemsPaginated;
        if (!isset($items[$index])) {
            session()->flash('error', 'Invalid item index.');
            return;
        }

        $item = $items[$index];
        $actualIndex = array_search($item, $this->orderedItems);
        
        if ($actualIndex === false) {
            session()->flash('error', 'Item not found in the order.');
            return;
        }

        $this->editingItemIndex = $actualIndex;
        $this->selected_product = $item['product_id'];
        $this->unit_price = $item['unit_price'];
        $this->order_qty = $item['order_qty'];
        $this->showModal = true;
    }
    
    public function closeModal()
    {
        $this->reset(['selected_product', 'unit_price', 'order_qty', 'search', 'categoryFilter', 'editingItemIndex', 'addBySupplierCode', 'addMode']);
        $this->showModal = false;
    }

        public function addItem()
    {
        $this->validate($this->itemRules);

        $product = Product::with(['category', 'supplier'])->find($this->selected_product);

        if (! $product) {
            session()->flash('error', 'Product not found.');
            return;
        }

        // Reopened POs: only allow adding items from the same supplier
        if ($this->purchaseOrder->wasReopened() && (int) $product->supplier_id !== (int) $this->purchaseOrder->supplier_id) {
            session()->flash('error', 'For reopened purchase orders, you can only add items from the same supplier (' . ($this->purchaseOrder->supplier?->name ?? 'N/A') . ').');
            return;
        }

        if ($this->editingItemIndex !== null) {
            // Update existing item
            $this->orderedItems[$this->editingItemIndex] = [
                'id' => $this->orderedItems[$this->editingItemIndex]['id'] ?? null,
                'product_id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category->name ?? 'N/A',
                'supplier' => $product->supplier->name ?? 'N/A',
                'supplier_code' => $product->supplier_code ?? 'N/A',
                'unit_price' => $this->unit_price,
                'order_qty' => $this->order_qty,
                'total_price' => $this->unit_price * $this->order_qty,
            ];
            session()->flash('success', 'Item updated successfully.');
        } else {
            // Check for duplicates
            $exists = collect($this->orderedItems)->firstWhere('product_id', $product->id);
            if ($exists) {
                session()->flash('error', 'This product is already in the order list.');
                return;
            }

            // Add new item
            $this->orderedItems[] = [
                'id' => null,
                'product_id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category->name ?? 'N/A',
                'supplier' => $product->supplier->name ?? 'N/A',
                'supplier_code' => $product->supplier_code ?? 'N/A',
                'unit_price' => $this->unit_price,
                'order_qty' => $this->order_qty,
                'total_price' => $this->unit_price * $this->order_qty,
            ];
            session()->flash('success', 'Item added successfully.');
        }

        $this->closeModal();
    }

    public function addItemBySupplierCode()
    {
        $this->validate([
            'addBySupplierCode' => 'required|string|max:255',
            'order_qty' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
        ], [
            'addBySupplierCode.required' => 'Please enter supplier code.',
            'order_qty.required' => 'Please enter quantity.',
            'order_qty.min' => 'Quantity must be at least 0.01.',
            'unit_price.required' => 'Please enter unit price.',
            'unit_price.min' => 'Unit price cannot be negative.',
        ]);

        // For reopened POs, use PO's supplier; otherwise use form's supplier_id
        $supplierId = $this->purchaseOrder->wasReopened()
            ? $this->purchaseOrder->supplier_id
            : $this->supplier_id;

        if (empty($supplierId)) {
            session()->flash('error', 'Please select a supplier first.');
            return;
        }

        $supplierCode = trim($this->addBySupplierCode);
        $product = Product::with(['category', 'supplier', 'color'])
            ->where('supplier_id', $supplierId)
            ->where('supplier_code', $supplierCode)
            ->first();

        if (! $product) {
            try {
                $product = app(ProductService::class)->createPlaceholderProduct([
                    'supplier_id' => $supplierId,
                    'supplier_code' => $supplierCode,
                    'unit_price' => (float) $this->unit_price,
                ]);
            } catch (\Exception $e) {
                session()->flash('error', 'Could not create placeholder product: ' . $e->getMessage());
                return;
            }
        }

        $exists = collect($this->orderedItems)->firstWhere('product_id', $product->id);
        if ($exists) {
            session()->flash('error', 'This product is already in the order list.');
            return;
        }

        $this->orderedItems[] = [
            'id' => null,
            'product_id' => $product->id,
            'product_number' => $product->product_number ?? null,
            'sku' => $product->sku,
            'name' => $product->remarks ?? $product->name,
            'category' => $product->category ? $product->category->name : 'N/A',
            'supplier' => $product->supplier ? $product->supplier->name : 'N/A',
            'supplier_code' => $product->supplier_code ?? 'N/A',
            'unit_price' => (float) $this->unit_price,
            'order_qty' => (float) $this->order_qty,
            'total_price' => (float) ($this->unit_price * $this->order_qty),
        ];
        session()->flash('success', 'Item added successfully.');
        $this->closeModal();
    }

    public function removeItem($index)
    {
        $items = $this->orderedItemsPaginated;
        if (!isset($items[$index])) {
            session()->flash('error', 'Invalid item index.');
            return;
        }

        $item = $items[$index];
        $actualIndex = array_search($item, $this->orderedItems);
        
        if ($actualIndex === false) {
            session()->flash('error', 'Item not found in the order.');
            return;
        }

        unset($this->orderedItems[$actualIndex]);
        $this->orderedItems = array_values($this->orderedItems);
        
        if (empty($this->orderedItemsPaginated) && $this->orderedItemsPage > 1) {
            $this->orderedItemsPage--;
        }
        
        session()->flash('success', 'Item removed successfully.');
    }

    public function submit()
    {
        if (empty($this->orderedItems)) {
            session()->flash('error', 'Please add at least one item to the order.');
            return;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            // Calculate totals
            $total_qty = 0;
            $total_price = 0;
            
            foreach ($this->orderedItems as $item) {
                $total_qty += $item['order_qty'];
                $total_price += $item['total_price'];
            }

            $selectedCurrency = $this->selectedCurrency;
            $currencyId = $selectedCurrency?->id ?? Currency::base()?->id;

            // Update purchase order
            $this->purchaseOrder->update([
                'supplier_id' => $this->supplier_id,
                'currency_id' => $currencyId,
                'order_date' => $this->order_date,
                'expected_delivery_date' => $this->expected_delivery_date,
                'del_to' => $this->deliver_to,
                'total_qty' => $total_qty,
                'total_price' => $total_price,
            ]);

            // Get existing product order IDs
            $existingOrderIds = $this->purchaseOrder->productOrders->pluck('id')->toArray();
            $updatedOrderIds = [];

            // Update or create product orders
            foreach ($this->orderedItems as $item) {
                if (isset($item['id']) && $item['id']) {
                    // Update existing product order
                    ProductOrder::where('id', $item['id'])->update([
                        'currency_id' => $currencyId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['order_qty'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total_price'],
                    ]);
                    $updatedOrderIds[] = $item['id'];
                } else {
                    // Create new product order
                    $newOrder = ProductOrder::create([
                        'purchase_order_id' => $this->purchaseOrder->id,
                        'currency_id' => $currencyId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['order_qty'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total_price'],
                        'status' => 'pending',
                        'received_quantity' => 0,
                    ]);
                    $updatedOrderIds[] = $newOrder->id;
                }
            }

            // Delete product orders that were removed
            $ordersToDelete = array_diff($existingOrderIds, $updatedOrderIds);
            if (!empty($ordersToDelete)) {
                ProductOrder::whereIn('id', $ordersToDelete)->delete();
            }

            DB::commit();

            session()->flash('success', 'Purchase order updated successfully.');
            return redirect()->route('pomanagement.purchaseorder');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to update purchase order: ' . $e->getMessage());
        }
    }

    public function getTotalAmountProperty()
    {
        return collect($this->orderedItems)->sum('total_price');
    }

    public function getTotalQuantityProperty()
    {
        return collect($this->orderedItems)->sum('order_qty');
    }

    public function getOrderedItemsPaginatedProperty()
    {
        $items = collect($this->orderedItems);
        $start = ($this->orderedItemsPage - 1) * $this->orderedItemsPerPage;
        return $items->slice($start, $this->orderedItemsPerPage)->values();
    }

    public function getOrderedItemsTotalPagesProperty()
    {
        return ceil(count($this->orderedItems) / $this->orderedItemsPerPage);
    }

    public function updatedOrderedItemsPerPage()
    {
        $this->orderedItemsPage = 1;
    }

    public function nextOrderedItemsPage()
    {
        if ($this->orderedItemsPage < $this->orderedItemsTotalPages) {
            $this->orderedItemsPage++;
        }
    }

    public function previousOrderedItemsPage()
    {
        if ($this->orderedItemsPage > 1) {
            $this->orderedItemsPage--;
        }
    }

    public function goToOrderedItemsPage($page)
    {
        if ($page >= 1 && $page <= $this->orderedItemsTotalPages) {
            $this->orderedItemsPage = $page;
        }
    }

    public function render()
    {
        return view('livewire.pages.POmanagement.purchase-order.edit', [
            'products' => $this->products,
            'suppliers' => $this->suppliers,
            'departments' => $this->departments,
            'categories' => $this->categories,
            'selectedCurrency' => $this->selectedCurrency,
            'paginatedOrderedItems' => $this->orderedItemsPaginated,
            'orderedItemsTotalPages' => $this->orderedItemsTotalPages,
        ]);
    }
}