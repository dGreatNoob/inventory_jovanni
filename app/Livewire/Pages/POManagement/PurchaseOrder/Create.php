<?php

namespace App\Livewire\Pages\POManagement\PurchaseOrder;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\ProductOrder;
use App\Models\Supplier;
use App\Models\Department;
use App\Models\User;
use App\Models\Category;
use App\Enums\PurchaseOrderStatus;
use App\Services\ProductService;
use App\Support\ProductSearchHelper;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Create extends Component
{
    use WithPagination;

    public $ordered_by;
    public $supplier_id;
    public $order_date;
    public $expected_delivery_date;
    public $deliver_to;

    public $selected_product;
    public $unit_price;
    public $order_qty;
    public $search = '';
    public $categoryFilter = '';
    public $showModal = false;

    /** Add-by-supplier-code form */
    public $addBySupplierCode = '';
    public $addMode = 'search'; // 'search' | 'supplier_code'

    public $orderedItems = [];
    public $departments = [];
    public $categories = [];

    public $orderedItemsPerPage = 10;
    public $orderedItemsPage = 1;

    protected $rules = [
        'supplier_id' => 'required|exists:suppliers,id',
        'order_date' => 'required|date',
        'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
        'deliver_to' => 'required|exists:departments,id',
        'orderedItems' => 'required|array|min:1',
    ];

    protected $messages = [
        'supplier_id.required' => 'Please select a supplier.',
        'order_date.required' => 'Please select an order date.',
        'expected_delivery_date.after_or_equal' => 'Expected delivery date must be on or after the order date.',
        'deliver_to.required' => 'Please select a delivery department.',
        'orderedItems.required' => 'Please add at least one product.',
        'orderedItems.min' => 'Please add at least one product.',
    ];

    public function mount()
    {
        $this->departments = Department::all();
        $this->categories = Category::with('parent')
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();
        $this->ordered_by = Auth::user()->name;
        $this->order_date = now()->format('Y-m-d');

        // Ensure Warehouse department exists
        $warehouse = Department::where('name', 'Warehouse')->first();
        if (!$warehouse) {
            $warehouse = Department::create([
                'name' => 'Warehouse',
                // add any other necessary fields here, e.g. 'description' => '', ...
            ]);
            // Refresh departments list after adding
            $this->departments = Department::all();
        }
        $this->deliver_to = $warehouse->id;
    }

    public function updatedSupplierId()
    {
        $this->reset(['selected_product', 'unit_price', 'order_qty', 'search', 'categoryFilter']);
        $this->showModal = false;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }
    
    public function getProductsProperty()
    {
        if (empty($this->supplier_id)) {
            return new LengthAwarePaginator(
                collect([]),
                0,
                5,
                1
            );
        }

        $baseQuery = Product::with(['category', 'supplier', 'color'])
            ->where('supplier_id', $this->supplier_id)
            ->where('disabled', false);

        // Exclude products already added to the order
        $alreadyAddedIds = collect($this->orderedItems)
            ->pluck('product_id')
            ->filter()
            ->values()
            ->all();

        if (!empty($alreadyAddedIds)) {
            $baseQuery->whereNotIn('id', $alreadyAddedIds);
        }

        if ($this->categoryFilter) {
            $baseQuery->where('category_id', $this->categoryFilter);
        }

        $rawSearch = trim((string) $this->search);

        // No search text: fall back to simple paginated list
        if ($rawSearch === '') {
            return $baseQuery->orderBy('name')->paginate(5);
        }

        // Segment-aware prefilter: similar to allocation warehouse search
        $segments = preg_split('/[\\s\\-_]+/', strtolower($rawSearch), -1, PREG_SPLIT_NO_EMPTY);
        if (empty($segments)) {
            return $baseQuery->orderBy('name')->paginate(5);
        }

        $searchableFields = ['product_number', 'supplier_code', 'name', 'remarks', 'sku'];

        $query = $baseQuery->where(function ($qb) use ($segments, $searchableFields) {
            foreach ($segments as $segment) {
                if ($segment === '') {
                    continue;
                }
                $pattern = '%' . $segment . '%';
                $qb->where(function ($inner) use ($pattern, $searchableFields) {
                    foreach ($searchableFields as $field) {
                        $inner->orWhere($field, 'like', $pattern);
                    }
                });
            }
        });

        // Prioritize products where product_number starts with first segment
        $firstSegment = $segments[0];
        $prefixPattern = $firstSegment . '%';

        $candidates = $query
            ->orderByRaw("CASE WHEN LOWER(product_number) LIKE ? THEN 0 ELSE 1 END", [$prefixPattern])
            ->orderBy('product_number')
            ->limit(300)
            ->get();

        // Strict prefix-segmentation filter in PHP
        $filtered = $candidates->filter(function ($product) use ($rawSearch) {
            return ProductSearchHelper::matchesAnyField($rawSearch, [
                (string) ($product->product_number ?? ''),
                (string) ($product->supplier_code ?? ''),
                (string) ($product->name ?? ''),
                (string) ($product->remarks ?? ''),
                (string) ($product->sku ?? ''),
            ]);
        })->values();

        // Manual pagination for the filtered collection (5 per page)
        $perPage = 5;
        $currentPage = LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $currentItems = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentItems,
            $filtered->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    public function getSuppliersProperty()
    {
        return Supplier::orderBy('name')->get();
    }

    public function openModal()
    {
        if (empty($this->supplier_id)) {
            session()->flash('error', 'Please select a supplier first before adding products.');
            return;
        }
        
        $this->showModal = true;
        $this->reset(['selected_product', 'unit_price', 'order_qty', 'search', 'categoryFilter']);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['selected_product', 'unit_price', 'order_qty', 'search', 'categoryFilter', 'addBySupplierCode', 'addMode']);
    }

    public function selectProduct($id)
    {
        $this->selected_product = $id;
        $product = Product::find($id);
        
        if ($product) {
            $this->unit_price = $product->cost;
        }
    }
    
    public function addItem()
    {
        $this->validate([
            'selected_product' => 'required|exists:products,id',
            'order_qty' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0',
        ], [
            'selected_product.required' => 'Please select a product.',
            'order_qty.required' => 'Please enter quantity.',
            'order_qty.min' => 'Quantity must be at least 1.',
            'unit_price.required' => 'Please enter unit price.',
            'unit_price.min' => 'Unit price cannot be negative.',
        ]);

        $product = Product::with(['category', 'supplier', 'color'])->find($this->selected_product);

        // Check if product already exists in ordered items
        $existingIndex = collect($this->orderedItems)->search(function($item) use ($product) {
            return $item['product_id'] == $product->id;
        });

        if ($existingIndex !== false) {
            // Update quantity if product already exists
            $this->orderedItems[$existingIndex]['order_qty'] += $this->order_qty;
            $this->orderedItems[$existingIndex]['total_price'] = 
                $this->orderedItems[$existingIndex]['unit_price'] * $this->orderedItems[$existingIndex]['order_qty'];
            
            session()->flash('success', 'Product quantity updated in purchase order.');
        } else {
            // Add new item
            $this->orderedItems[] = [
                'product_id' => $product->id,
                'product_number' => $product->product_number ?? null,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'name' => $product->remarks ?? $product->name,
                'color' => $product->color ? ($product->color->name ?? $product->color->code) : null,
                'category' => $product->category ? $product->category->name : 'N/A',
                'supplier' => $product->supplier ? $product->supplier->name : 'N/A',
                'supplier_code' => $product->supplier_code ?? 'N/A',
                'unit_price' => (float)$this->unit_price,
                'order_qty' => (float)$this->order_qty,
                'total_price' => (float)($this->unit_price * $this->order_qty),
            ];
            
            session()->flash('success', 'Product added to purchase order.');
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

        if (empty($this->supplier_id)) {
            session()->flash('error', 'Please select a supplier first.');
            return;
        }

        $supplierCode = trim($this->addBySupplierCode);
        $product = Product::with(['category', 'supplier', 'color'])
            ->where('supplier_id', $this->supplier_id)
            ->where('supplier_code', $supplierCode)
            ->first();

        if (!$product) {
            try {
                $product = app(ProductService::class)->createPlaceholderProduct([
                    'supplier_id' => $this->supplier_id,
                    'supplier_code' => $supplierCode,
                    'unit_price' => (float) $this->unit_price,
                ]);
            } catch (\Exception $e) {
                session()->flash('error', 'Could not create placeholder product: ' . $e->getMessage());
                return;
            }
        }

        $existingIndex = collect($this->orderedItems)->search(fn ($item) => $item['product_id'] == $product->id);

        if ($existingIndex !== false) {
            $this->orderedItems[$existingIndex]['order_qty'] += (float) $this->order_qty;
            $this->orderedItems[$existingIndex]['total_price'] =
                $this->orderedItems[$existingIndex]['unit_price'] * $this->orderedItems[$existingIndex]['order_qty'];
            session()->flash('success', 'Product quantity updated in purchase order.');
        } else {
            $this->orderedItems[] = [
                'product_id' => $product->id,
                'product_number' => $product->product_number ?? null,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'name' => $product->remarks ?? $product->name,
                'color' => $product->color ? ($product->color->name ?? $product->color->code) : null,
                'category' => $product->category ? $product->category->name : 'N/A',
                'supplier' => $product->supplier ? $product->supplier->name : 'N/A',
                'supplier_code' => $product->supplier_code ?? 'N/A',
                'unit_price' => (float) $this->unit_price,
                'order_qty' => (float) $this->order_qty,
                'total_price' => (float) ($this->unit_price * $this->order_qty),
            ];
            session()->flash('success', 'Product added to purchase order.');
        }

        $this->closeModal();
    }

    public function removeItem($index)
    {
        unset($this->orderedItems[$index]);
        $this->orderedItems = array_values($this->orderedItems);
        session()->flash('success', 'Product removed from purchase order.');
    }

    public function getTotalAmountProperty()
    {
        return collect($this->orderedItems)->sum('total_price');
    }

    public function getTotalQuantityProperty()
    {
        return collect($this->orderedItems)->sum('order_qty');
    }

    /**
     * Generate PO number in the format "PO-YYYY-NNNNN" (e.g., PO-2025-60000)
     */
    protected function generatePoNum()
    {
        $year = now()->year;
        $lastPo = PurchaseOrder::where('po_num', 'like', "PO-$year-%")->orderByDesc('id')->first();

        $nextSequence = 60000;
        if ($lastPo) {
            // Extract sequence number from last PO
            $parts = explode('-', $lastPo->po_num);
            if (isset($parts[2])) {
                $lastSequence = intval($parts[2]);
                $nextSequence = max($nextSequence, $lastSequence + 1);
            }
        }

        return "PO-$year-$nextSequence";
    }

    public function submit() 
    { 
        if (empty($this->orderedItems)) { 
            session()->flash('error', 'Please add at least one product.'); 
            return; 
        } 

        $this->validate(); 

        try { 
            DB::beginTransaction(); 

            // Generate PO number with "PO-YYYY-NNNNN" format
            $poNum = $this->generatePoNum();

            $total_price = 0; 
            $total_qty = 0; 
            
            foreach ($this->orderedItems as $item) { 
                $total_price += (float)$item['total_price']; 
                $total_qty += (float)$item['order_qty']; 
            } 

            // Create the purchase order (auto-approved/open)
            $purchaseOrder = PurchaseOrder::create([ 
                'po_num' => $poNum,
                'status' => PurchaseOrderStatus::APPROVED,
                'supplier_id' => $this->supplier_id, 
                'order_date' => $this->order_date,
                'expected_delivery_date' => $this->expected_delivery_date,
                'ordered_by' => Auth::id(),
                'del_to' => $this->deliver_to,
                'del_on' => null,
                'total_qty' => $total_qty,
                'total_price' => $total_price, 
                'approver' => null,
            ]); 

            // Log for debugging
            Log::info('Purchase Order Created:', [
                'po_id' => $purchaseOrder->id,
                'po_num' => $poNum,
                'items_count' => count($this->orderedItems)
            ]);

            // Create product order items
            foreach ($this->orderedItems as $item) {
                $productOrderData = [
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => (float)$item['order_qty'],
                    'unit_price' => (float)$item['unit_price'],
                    'total_price' => (float)$item['total_price'],
                    'status' => 'pending',
                    'received_quantity' => 0,
                ];

                // Log what we're trying to insert
                Log::info('Creating ProductOrder:', $productOrderData);
                
                $productOrder = ProductOrder::create($productOrderData);
                
                // Log success
                Log::info('ProductOrder Created:', ['id' => $productOrder->id]);
            }

            DB::commit(); 
            
            session()->flash('success', 'Purchase order #' . $poNum . ' created successfully.'); 
            return redirect()->route('pomanagement.purchaseorder'); 

        } catch (\Exception $e) { 
            DB::rollBack(); 
            Log::error('Purchase Order Creation Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Error creating purchase order: ' . $e->getMessage()); 
        } 
    }

    public function getOrderedItemsPaginatedProperty()
    {
        $items = collect($this->orderedItems)->reverse();
        $start = ($this->orderedItemsPage - 1) * $this->orderedItemsPerPage;
        return $items->slice($start, $this->orderedItemsPerPage);
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
        return view('livewire.pages.POmanagement.purchase-order.create', [
            'products' => $this->getProductsProperty(),
            'suppliers' => $this->getSuppliersProperty(),
            'paginatedOrderedItems' => $this->orderedItemsPaginated,
            'orderedItemsTotalPages' => $this->orderedItemsTotalPages,
        ]);
    }
}