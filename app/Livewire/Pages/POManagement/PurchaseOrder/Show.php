<?php

namespace App\Livewire\Pages\POManagement\PurchaseOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderApprovalLog;
use App\Models\ProductOrder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductColor;
use App\Models\Currency;
use App\Services\ProductService;
use App\Support\ProductSearchHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Enums\PurchaseOrderStatus;

class Show extends Component
{
    use WithPagination;
    public $purchaseOrder;
    public $Id;
    
    // For APPROVED status form
    public $expected_delivery_date;
    public $expected_quantities = [];
    public $manufactured_dates = [];
    public $cancellation_reason;

    // Public properties for blade access
    public $totalQuantity;
    public $totalPrice;
    public $totalExpected;
    public $totalDelivered;
    public $totalReceived;
    public $totalDestroyed;
    public $batches; // ✅ ADDED - Store batches for this PO

    // Reason modal for Close/Reopen (replaces prompt())
    public $reasonModalAction = '';
    public $reasonModalReason = '';

    // Add Item modal (for TO_RECEIVE - same as Create page)
    public $showAddItemModal = false;
    public $selected_product;
    public $unit_price;
    public $order_qty;
    public $search = '';
    public $categoryFilter = '';
    public $addBySupplierCode = '';
    public $addMode = 'search';
    public $addNewProductColorSearch = '';
    public $addNewProductColorId = null;
    public $addNewProductColorDropdown = false;
    public $addNewProductSellingPrice = '';

    // Items per page for pagination
    public $itemsPerPage = 25;

    public function mount($Id)
    {
        $this->Id = $Id;
        
        Log::info('Loading Purchase Order details', [
            'po_id' => $Id,
            'user' => 'Wts135',
            'timestamp' => '2025-11-11 08:01:50',
        ]);
        
        // Load purchase order with relationships (currency for multi-currency display)
        $this->purchaseOrder = PurchaseOrder::with([
            'supplier',
            'currency',
            'productOrders.product.category',
            'productOrders.product.supplier',
            'orderedByUser',
            'approverInfo',
            'department',
            'approvalLogs.user',
            'deliveries',
        ])->findOrFail($Id);

        // Initialize computed properties
        $this->updateComputedProperties();
        
        // ✅ ADDED - Load batches for this PO
        $this->loadBatches();

        Log::info('Purchase Order loaded successfully', [
            'po_id' => $Id,
            'po_num' => $this->purchaseOrder->po_num,
            'status' => $this->purchaseOrder->status->value,
            'batch_count' => $this->batches->count(),
            'user' => 'Wts135',
            'timestamp' => '2025-11-11 08:01:50',
        ]);
    }

    /**
     * ✅ ADDED - Load batches for THIS purchase order ONLY
     */
    protected function loadBatches()
    {
        Log::info('Loading batches for Purchase Order', [
            'po_id' => $this->purchaseOrder->id,
            'po_num' => $this->purchaseOrder->po_num,
            'user' => 'Wts135',
            'timestamp' => '2025-11-11 08:01:50',
        ]);
        
        // ✅ Filter by purchase_order_id (not product_id)
        $this->batches = \App\Models\ProductBatch::with(['product', 'receivedByUser'])
            ->where('purchase_order_id', $this->purchaseOrder->id)
            ->orderBy('received_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        Log::info('Batches loaded successfully', [
            'po_id' => $this->purchaseOrder->id,
            'po_num' => $this->purchaseOrder->po_num,
            'batch_count' => $this->batches->count(),
            'product_ids' => $this->batches->pluck('product_id')->unique()->toArray(),
            'user' => 'Wts135',
            'timestamp' => '2025-11-11 08:01:50',
        ]);
    }

    // Update all computed properties (totals from full PO, not paginated subset)
    protected function updateComputedProperties()
    {
        $this->totalQuantity = (int) $this->purchaseOrder->productOrders->sum('quantity');
        $this->totalPrice = $this->purchaseOrder->productOrders->sum('total_price');
        $this->totalExpected = (int) $this->purchaseOrder->productOrders->sum(function ($order) {
            return $order->expected_qty ?? $order->quantity;
        });
        $this->totalDelivered = (int) $this->purchaseOrder->productOrders->sum('received_quantity');
        $this->totalReceived = (int) $this->purchaseOrder->productOrders->sum('received_quantity');
        $this->totalDestroyed = (int) $this->purchaseOrder->productOrders->sum('destroyed_quantity') ?? 0;
    }

    /**
     * Paginated line items for the items table (e.g. 25 per page).
     * Totals/summaries remain from full PO via updateComputedProperties().
     */
    public function getProductOrdersPaginatedProperty()
    {
        return $this->purchaseOrder->productOrders()
            ->with(['product.category', 'product.supplier'])
            ->paginate($this->itemsPerPage, ['*'], 'items_page', request()->get('items_page', 1));
    }

    /**
     * Get totals array for the summary bar in the items table.
     * All totals computed from the full PO, not the paginated subset.
     */
    public function getTotalsProperty()
    {
        return [
            'count' => $this->purchaseOrder->productOrders->count(),
            'quantity' => $this->totalQuantity,
            'expected' => $this->totalExpected,
            'received' => $this->totalReceived,
            'destroyed' => $this->totalDestroyed,
            'price' => $this->totalPrice,
        ];
    }

    // Update computed properties when expected quantities change
    public function updatedExpectedQuantities()
    {
        $this->totalExpected = $this->purchaseOrder->productOrders->sum(function($order) {
            return $this->expected_quantities[$order->id] ?? ($order->expected_qty ?? $order->quantity);
        });
    }

    public function ApprovePurchaseOrder()
    {
        try {
            DB::beginTransaction();
            
            Log::info('Starting approval process', [
                'po_id' => $this->purchaseOrder->id,
                'po_num' => $this->purchaseOrder->po_num,
                'current_status' => $this->purchaseOrder->status->value,
                'user' => 'Wts135',
                'timestamp' => '2025-11-11 08:01:50',
            ]);
            
            // ✅ CORRECT - Compare enum to enum
            if ($this->purchaseOrder->status === PurchaseOrderStatus::PENDING) {
                // ✅ First approval: PENDING → APPROVED
                $autoExpectedDate = now()->addDays(7)->format('Y-m-d');
                
                $this->purchaseOrder->update([
                    'status' => PurchaseOrderStatus::APPROVED,
                    'approver' => Auth::id(),
                    'approved_at' => now(),
                    'approved_by' => Auth::id(),
                    'expected_delivery_date' => $autoExpectedDate,
                ]);

                PurchaseOrderApprovalLog::create([
                    'purchase_order_id' => $this->purchaseOrder->id,
                    'user_id' => Auth::id(),
                    'action' => 'approved',
                    'remarks' => 'Purchase order approved by ' . Auth::user()->name . '. Auto-expected delivery: ' . $autoExpectedDate,
                    'ip_address' => request()->ip(),
                ]);

                DB::commit();
                
                Log::info('Purchase order approved (PENDING → APPROVED)', [
                    'po_id' => $this->purchaseOrder->id,
                    'po_num' => $this->purchaseOrder->po_num,
                    'expected_delivery_date' => $autoExpectedDate,
                    'user' => 'Wts135',
                    'timestamp' => '2025-11-11 08:01:50',
                ]);
                
                session()->flash('message', 'Purchase order #' . $this->purchaseOrder->po_num . ' approved successfully! Expected delivery: ' . now()->addDays(7)->format('M d, Y'));
                
            } elseif ($this->purchaseOrder->status === PurchaseOrderStatus::APPROVED) {
                // ✅ Second approval: APPROVED → TO_RECEIVE (no batch number handling)

                $this->purchaseOrder->update([
                    'status' => PurchaseOrderStatus::TO_RECEIVE,
                ]);

                PurchaseOrderApprovalLog::create([
                    'purchase_order_id' => $this->purchaseOrder->id,
                    'user_id' => Auth::id(),
                    'action' => 'approved',
                    'remarks' => 'Purchase order marked as ready for receiving.',
                    'ip_address' => request()->ip(),
                ]);

                DB::commit();

                // Refresh data
                $this->purchaseOrder->refresh();
                $this->updateComputedProperties();
                $this->loadBatches();

                Log::info('Purchase order marked as ready to receive (APPROVED → TO_RECEIVE)', [
                    'po_id' => $this->purchaseOrder->id,
                    'po_num' => $this->purchaseOrder->po_num,
                    'user' => 'Wts135',
                    'timestamp' => '2025-11-11 08:01:50',
                ]);

                session()->flash('message', 'Purchase order is now ready for receiving.');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval error', [
                'po_id' => $this->purchaseOrder->id ?? null,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => 'Wts135',
                'timestamp' => '2025-11-11 08:01:50',
            ]);
            session()->flash('error', 'Failed to approve purchase order: ' . $e->getMessage());
        }
    }
    
    public function RejectPurchaseOrder()
    {
        try {
            DB::transaction(function () {
                $this->validate([
                    'cancellation_reason' => 'required|string|min:5|max:500',
                ]);

                Log::info('Rejecting purchase order', [
                    'po_id' => $this->purchaseOrder->id,
                    'po_num' => $this->purchaseOrder->po_num,
                    'reason' => $this->cancellation_reason,
                    'user' => 'Wts135',
                    'timestamp' => '2025-11-11 08:01:50',
                ]);

                $this->purchaseOrder->update([
                    'status' => PurchaseOrderStatus::CANCELLED,
                    'cancellation_reason' => $this->cancellation_reason,
                    'cancelled_at' => now(),
                    'cancelled_by' => Auth::id(),
                ]);
                
                // Log the rejection action
                PurchaseOrderApprovalLog::create([
                    'purchase_order_id' => $this->purchaseOrder->id,
                    'user_id' => Auth::id(),
                    'action' => 'rejected',
                    'remarks' => $this->cancellation_reason,
                    'ip_address' => request()->ip(),
                ]);
                
                // Refresh the purchase order and computed properties
                $this->purchaseOrder->refresh();
                $this->updateComputedProperties();
                
                Log::info('Purchase order rejected successfully', [
                    'po_id' => $this->purchaseOrder->id,
                    'po_num' => $this->purchaseOrder->po_num,
                    'user' => 'Wts135',
                    'timestamp' => '2025-11-11 08:01:50',
                ]);
                
                session()->flash('message', 'Purchase order rejected.');
            });
            
        } catch (\Exception $e) {
            Log::error('Rejection error', [
                'po_id' => $this->purchaseOrder->id ?? null,
                'message' => $e->getMessage(),
                'user' => 'Wts135',
                'timestamp' => '2025-11-11 08:01:50',
            ]);
            session()->flash('error', 'Failed to reject purchase order: ' . $e->getMessage());
        }
    }

    public function ReturnToApproved()
    {
        try {
            DB::transaction(function () {
                $this->validate([
                    'cancellation_reason' => 'required|string|min:5|max:500',
                ]);

                Log::info('Returning purchase order to approved status', [
                    'po_id' => $this->purchaseOrder->id,
                    'po_num' => $this->purchaseOrder->po_num,
                    'reason' => $this->cancellation_reason,
                    'user' => 'Wts135',
                    'timestamp' => '2025-11-11 08:01:50',
                ]);

                $this->purchaseOrder->update([
                    'status' => PurchaseOrderStatus::APPROVED,
                    'loaded_date' => null,
                    'return_reason' => $this->cancellation_reason,
                ]);

                // Log the return action
                PurchaseOrderApprovalLog::create([
                    'purchase_order_id' => $this->purchaseOrder->id,
                    'user_id' => Auth::id(),
                    'action' => 'returned_to_approved',
                    'remarks' => 'Returned to approved: ' . $this->cancellation_reason,
                    'ip_address' => request()->ip(),
                ]);

                // Refresh the purchase order and computed properties
                $this->purchaseOrder->refresh();
                $this->updateComputedProperties();
                $this->loadBatches(); // ✅ ADDED - Refresh batches
                
                Log::info('Purchase order returned to approved status successfully', [
                    'po_id' => $this->purchaseOrder->id,
                    'po_num' => $this->purchaseOrder->po_num,
                    'user' => 'Wts135',
                    'timestamp' => '2025-11-11 08:01:50',
                ]);
                
                session()->flash('message', 'Purchase order returned to approved status. Batch numbers cleared.');
            });
            
        } catch (\Exception $e) {
            Log::error('Return to approved error', [
                'po_id' => $this->purchaseOrder->id ?? null,
                'message' => $e->getMessage(),
                'user' => 'Wts135',
                'timestamp' => '2025-11-11 08:01:50',
            ]);
            session()->flash('error', 'Failed to return purchase order: ' . $e->getMessage());
        }
    }

    public function closeForFulfillment(?string $reason = null): void
    {
        if (! $this->purchaseOrder->canCloseForFulfillment()) {
            session()->flash('error', 'This purchase order cannot be closed for fulfillment.');
            return;
        }

        try {
            DB::transaction(function () use ($reason) {
                $remarks = $reason && trim($reason) !== ''
                    ? trim($reason)
                    : 'Manually closed for fulfillment (short or complete).';

                $this->purchaseOrder->update([
                    'status' => PurchaseOrderStatus::RECEIVED,
                    'del_on' => $this->purchaseOrder->del_on ?? now(),
                ]);

                PurchaseOrderApprovalLog::create([
                    'purchase_order_id' => $this->purchaseOrder->id,
                    'user_id' => Auth::id(),
                    'action' => 'closed_for_fulfillment',
                    'remarks' => $remarks,
                    'ip_address' => request()->ip(),
                ]);

                $this->purchaseOrder->refresh();
                $this->updateComputedProperties();
                $this->loadBatches();
            });

            session()->flash('message', 'Purchase order closed for fulfillment.');
        } catch (\Exception $e) {
            Log::error('Close for fulfillment error', [
                'po_id' => $this->purchaseOrder->id ?? null,
                'message' => $e->getMessage(),
            ]);
            session()->flash('error', 'Failed to close purchase order: ' . $e->getMessage());
        }
    }

    public function reopenPurchaseOrder(?string $reason = null): void
    {
        if ($this->purchaseOrder->status !== PurchaseOrderStatus::RECEIVED) {
            session()->flash('error', 'Only received purchase orders can be reopened.');
            return;
        }

        try {
            DB::transaction(function () use ($reason) {
                $remarks = $reason && trim($reason) !== ''
                    ? trim($reason)
                    : 'PO reopened for receiving or editing.';

                $this->purchaseOrder->update([
                    'status' => PurchaseOrderStatus::TO_RECEIVE,
                ]);

                PurchaseOrderApprovalLog::create([
                    'purchase_order_id' => $this->purchaseOrder->id,
                    'user_id' => Auth::id(),
                    'action' => 'reopened',
                    'remarks' => $remarks,
                    'ip_address' => request()->ip(),
                ]);

                $this->purchaseOrder->refresh();
                $this->updateComputedProperties();
                $this->loadBatches();
            });

            session()->flash('message', 'Purchase order reopened. You can receive more or edit lines.');
        } catch (\Exception $e) {
            Log::error('Reopen purchase order error', [
                'po_id' => $this->purchaseOrder->id ?? null,
                'message' => $e->getMessage(),
            ]);
            session()->flash('error', 'Failed to reopen purchase order: ' . $e->getMessage());
        }
    }

    /** Open the reason modal for Close or Reopen (confirmation with optional reason). */
    public function openReasonModal(string $action): void
    {
        if (! in_array($action, ['close', 'reopen'], true)) {
            return;
        }
        $this->reasonModalAction = $action;
        $this->reasonModalReason = '';
        // Flux modal listens for modal-show; use Flux::modal() for correct event dispatch
        $this->modal('po-reason-modal')->show();
    }

    /** Submit reason from modal and call Close or Reopen, then close modal. */
    public function submitReasonModal(): void
    {
        $reason = $this->reasonModalReason ? trim($this->reasonModalReason) : null;
        if ($this->reasonModalAction === 'close') {
            $this->closeForFulfillment($reason);
        } elseif ($this->reasonModalAction === 'reopen') {
            $this->reopenPurchaseOrder($reason);
        }
        $this->reasonModalAction = '';
        $this->reasonModalReason = '';
        $this->dispatch('close-modal', name: 'po-reason-modal');
    }

    /** Add Item modal (TO_RECEIVE only) - same modal as Create page */
    public function openAddItemModal(): void
    {
        if ($this->purchaseOrder->status !== PurchaseOrderStatus::TO_RECEIVE) {
            session()->flash('error', 'Items can only be added when the PO is in To Receive status.');
            return;
        }
        $this->showAddItemModal = true;
        $this->reset([
            'selected_product', 'unit_price', 'order_qty', 'search', 'categoryFilter',
            'addBySupplierCode', 'addNewProductColorSearch', 'addNewProductColorId', 'addNewProductColorDropdown', 'addNewProductSellingPrice',
        ]);
        $this->addMode = 'search';
    }

    public function closeModal(): void
    {
        $this->showAddItemModal = false;
        $this->reset([
            'selected_product', 'unit_price', 'order_qty', 'search', 'categoryFilter',
            'addBySupplierCode', 'addMode',
            'addNewProductColorSearch', 'addNewProductColorId', 'addNewProductColorDropdown', 'addNewProductSellingPrice',
        ]);
    }

    /**
     * Update PO totals (total_qty, total_price) from line items.
     * Call this after adding items in Items Ready for Receiving so stock-in reflects the latest.
     */
    public function updatePurchaseOrder(): void
    {
        if ($this->purchaseOrder->status !== PurchaseOrderStatus::TO_RECEIVE) {
            session()->flash('error', 'PO can only be updated when in To Receive status.');
            return;
        }

        try {
            $totalQty = $this->purchaseOrder->productOrders()->sum('quantity');
            $totalPrice = $this->purchaseOrder->productOrders()->sum('total_price');

            $this->purchaseOrder->update([
                'total_qty' => $totalQty,
                'total_price' => $totalPrice,
            ]);

            $this->purchaseOrder->refresh();
            $this->purchaseOrder->load(['productOrders.product']);
            $this->updateComputedProperties();

            session()->flash('message', 'Purchase order updated. Totals synced for stock-in.');
        } catch (\Throwable $e) {
            Log::error('Failed to update purchase order totals', [
                'po_id' => $this->purchaseOrder->id,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'Failed to update PO: ' . $e->getMessage());
        }
    }

    public function getProductsProperty()
    {
        $supplierId = $this->purchaseOrder->supplier_id ?? null;
        if (empty($supplierId)) {
            return new LengthAwarePaginator(collect([]), 0, 5, 1);
        }

        $baseQuery = Product::with(['category', 'supplier', 'color'])
            ->where('supplier_id', $supplierId)
            ->where('disabled', false);

        if ($this->categoryFilter) {
            $baseQuery->where('category_id', $this->categoryFilter);
        }

        $rawSearch = trim((string) $this->search);
        if ($rawSearch === '') {
            return $baseQuery->orderBy('name')->paginate(5, ['*'], 'add_item_page');
        }

        $segments = preg_split('/[\\s\\-_]+/', strtolower($rawSearch), -1, PREG_SPLIT_NO_EMPTY);
        if (empty($segments)) {
            return $baseQuery->orderBy('name')->paginate(5, ['*'], 'add_item_page');
        }

        $searchableFields = ['product_number', 'supplier_code', 'name', 'remarks', 'sku'];
        $query = $baseQuery->where(function ($qb) use ($segments, $searchableFields) {
            foreach ($segments as $segment) {
                if ($segment === '') continue;
                $pattern = '%' . $segment . '%';
                $qb->where(function ($inner) use ($pattern, $searchableFields) {
                    foreach ($searchableFields as $field) {
                        $inner->orWhere($field, 'like', $pattern);
                    }
                });
            }
        });

        $firstSegment = $segments[0];
        $prefixPattern = $firstSegment . '%';
        $candidates = $query
            ->orderByRaw("CASE WHEN LOWER(product_number) LIKE ? THEN 0 ELSE 1 END", [$prefixPattern])
            ->orderBy('product_number')
            ->limit(300)
            ->get();

        $filtered = $candidates->filter(function ($product) use ($rawSearch) {
            return ProductSearchHelper::matchesAnyField($rawSearch, [
                (string) ($product->product_number ?? ''),
                (string) ($product->supplier_code ?? ''),
                (string) ($product->name ?? ''),
                (string) ($product->remarks ?? ''),
                (string) ($product->sku ?? ''),
            ]);
        })->values();

        $perPage = 5;
        $currentPage = LengthAwarePaginator::resolveCurrentPage('add_item_page') ?: 1;
        $currentItems = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentItems,
            $filtered->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'add_item_page']
        );
    }

    public function getCategoriesProperty()
    {
        return Category::with('parent')
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();
    }

    public function getSelectedCurrencyProperty()
    {
        return $this->purchaseOrder?->currency
            ?? $this->purchaseOrder?->supplier?->defaultCurrency
            ?? Currency::base();
    }

    public function getFilteredCreateColorsProperty()
    {
        $colors = ProductColor::orderBy('code')->get();
        $query = trim($this->addNewProductColorSearch ?? '');
        if ($query === '') {
            return $colors;
        }
        $lower = strtolower($query);
        return $colors->filter(fn ($c) => str_contains(strtolower($c->code ?? ''), $lower)
            || str_contains(strtolower($c->name ?? ''), $lower)
            || str_contains(strtolower($c->shortcut ?? ''), $lower))->values();
    }

    public function selectCreateColor($id)
    {
        $color = ProductColor::find($id);
        $this->addNewProductColorId = $id ? (int) $id : null;
        $this->addNewProductColorDropdown = false;
        $this->addNewProductColorSearch = $color ? ($color->code . ($color->name ? ' - ' . $color->name : '')) : '';
    }

    public function selectProduct($id)
    {
        $this->selected_product = $id;
        $product = Product::find($id);
        if ($product) {
            $this->unit_price = $product->cost ?? 0;
        }
    }

    public function addItem()
    {
        $this->validate([
            'selected_product' => 'required|exists:products,id',
            'order_qty' => 'required|numeric|min:0.01',
            'unit_price' => 'nullable|numeric|min:0',
        ], [
            'selected_product.required' => 'Please select a product.',
            'order_qty.required' => 'Please enter quantity.',
            'order_qty.min' => 'Quantity must be at least 0.01.',
            'unit_price.min' => 'Unit price cannot be negative.',
        ]);

        // Ensure unit_price has a value (default 0 if null/empty)
        $unitPrice = $this->unit_price !== '' && $this->unit_price !== null
            ? (float) $this->unit_price
            : 0;

        $product = Product::with(['category', 'supplier'])->find($this->selected_product);
        if (! $product) {
            session()->flash('error', 'Product not found.');
            return;
        }

        if ($this->purchaseOrder->wasReopened() && (int) $product->supplier_id !== (int) $this->purchaseOrder->supplier_id) {
            session()->flash('error', 'For reopened POs, you can only add items from the same supplier.');
            return;
        }

        $orderQty = (float) $this->order_qty;
        $existing = $this->purchaseOrder->productOrders()->where('product_id', $product->id)->first();

        try {
            DB::transaction(function () use ($product, $unitPrice, $orderQty, $existing) {
                $currencyId = $this->purchaseOrder->currency_id ?? $this->purchaseOrder->supplier?->defaultCurrency?->id ?? Currency::base()?->id;

                if ($existing) {
                    $newQty = $existing->quantity + $orderQty;
                    $newTotal = $existing->unit_price * $newQty;
                    $existing->update([
                        'quantity' => $newQty,
                        'total_price' => $newTotal,
                    ]);
                } else {
                    $totalPrice = $unitPrice * $orderQty;
                    ProductOrder::create([
                        'purchase_order_id' => $this->purchaseOrder->id,
                        'currency_id' => $currencyId,
                        'product_id' => $product->id,
                        'quantity' => $orderQty,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'status' => 'pending',
                        'received_quantity' => 0,
                    ]);
                }

                $this->purchaseOrder->update([
                    'total_qty' => $this->purchaseOrder->productOrders()->sum('quantity'),
                    'total_price' => $this->purchaseOrder->productOrders()->sum('total_price'),
                ]);
                $this->purchaseOrder->refresh();
                $this->purchaseOrder->load('productOrders.product.category', 'productOrders.product.supplier');
                $this->updateComputedProperties();
            });
            session()->flash('message', $existing ? 'Quantity updated for existing item.' : 'Item added to purchase order.');
            $this->closeModal();
        } catch (\Exception $e) {
            Log::error('Add item to PO error', ['po_id' => $this->purchaseOrder->id, 'message' => $e->getMessage()]);
            session()->flash('error', 'Failed to add item: ' . $e->getMessage());
        }
    }

    public function addItemBySupplierCode()
    {
        $this->validate([
            'addBySupplierCode' => 'required|string|max:255',
            'order_qty' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
            'addNewProductSellingPrice' => 'nullable|numeric|min:0',
        ], [
            'addBySupplierCode.required' => 'Please enter supplier code.',
            'order_qty.required' => 'Please enter quantity.',
            'unit_price.required' => 'Please enter unit price.',
        ]);

        $supplierId = $this->purchaseOrder->supplier_id;
        $supplierCode = trim($this->addBySupplierCode);
        $product = Product::with(['category', 'supplier', 'color'])
            ->where('supplier_id', $supplierId)
            ->where('supplier_code', $supplierCode)
            ->first();

        $createPayload = [
            'supplier_id' => $supplierId,
            'supplier_code' => $supplierCode,
            'unit_price' => (float) $this->unit_price,
        ];
        $sellingPrice = $this->addNewProductSellingPrice !== '' && $this->addNewProductSellingPrice !== null
            ? (float) $this->addNewProductSellingPrice
            : (float) $this->unit_price;
        $createPayload['price'] = $sellingPrice;
        if ($this->addNewProductColorId) {
            $createPayload['product_color_id'] = $this->addNewProductColorId;
        }

        if (! $product) {
            try {
                $product = app(ProductService::class)->createPlaceholderProduct($createPayload);
            } catch (\Exception $e) {
                session()->flash('error', 'Could not create placeholder product: ' . $e->getMessage());
                return;
            }
        }

        $orderQty = (float) $this->order_qty;
        $unitPrice = (float) $this->unit_price;
        $existing = $this->purchaseOrder->productOrders()->where('product_id', $product->id)->first();

        try {
            DB::transaction(function () use ($product, $unitPrice, $orderQty, $existing) {
                $currencyId = $this->purchaseOrder->currency_id ?? $this->purchaseOrder->supplier?->defaultCurrency?->id ?? Currency::base()?->id;

                if ($existing) {
                    $newQty = $existing->quantity + $orderQty;
                    $newTotal = $existing->unit_price * $newQty;
                    $existing->update([
                        'quantity' => $newQty,
                        'total_price' => $newTotal,
                    ]);
                } else {
                    $totalPrice = $unitPrice * $orderQty;
                    ProductOrder::create([
                        'purchase_order_id' => $this->purchaseOrder->id,
                        'currency_id' => $currencyId,
                        'product_id' => $product->id,
                        'quantity' => $orderQty,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'status' => 'pending',
                        'received_quantity' => 0,
                    ]);
                }

                $this->purchaseOrder->update([
                    'total_qty' => $this->purchaseOrder->productOrders()->sum('quantity'),
                    'total_price' => $this->purchaseOrder->productOrders()->sum('total_price'),
                ]);
                $this->purchaseOrder->refresh();
                $this->purchaseOrder->load('productOrders.product.category', 'productOrders.product.supplier');
                $this->updateComputedProperties();
            });
            session()->flash('message', $existing ? 'Quantity updated for existing item.' : 'Item added to purchase order.');
            $this->closeModal();
        } catch (\Exception $e) {
            Log::error('Add item by supplier code error', ['po_id' => $this->purchaseOrder->id, 'message' => $e->getMessage()]);
            session()->flash('error', 'Failed to add item: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.POmanagement.purchase-order.show');
    }
}