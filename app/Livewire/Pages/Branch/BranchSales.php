<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\BranchCustomerSale;
use App\Models\Branch;
use App\Models\Agent;
use App\Models\BranchAllocationItem;
use App\Models\Promo;
use App\Models\SalesOrder;

class BranchSales extends Component
{
    use WithPagination;

    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 10;
    public $selectedBranchId = '';
    public $selectedAgentId = '';
    public $showDetailsModal = false;
    public $selectedSale = null;

    // Create stepper state
    public $showCreateStepper = false;
    public $currentStep = 1;

    // Step 1: Branch Sales Form
    public $createBranchId = '';
    public $createTransactionDate = '';
    public $createSellingArea = '';
    public $createAgentId = '';
    public $createTerm = '';
    public $createRemarks = '';

    // Step 1: Searchable dropdowns
    public $createBranchSearch = '';
    public $createBranchDropdown = false;
    public $createAgentSearch = '';
    public $createAgentDropdown = false;

    // Step 2: Sales items
    public $salesItems = [];
    public $salesBarcodeInput = '';
    public $salesQuantity = 1;
    public $branchProducts = [];

    // Add Item modal
    public $showAddItemModal = false;
    public $addItemSearch = '';
    public $addItemBarcodeSearch = false;
    public $addItemSelectedProduct = null;
    public $addItemQuantity = 1;
    public $addItemUnitPrice = 0;
    public $addItemDiscountPercent = 0;
    public $addItemDiscountAmount = 0;
    public $addItemRemarks = '';
    public $addItemPromoName = '';
    public $availableAgents = [];
    public $sellingAreaOptions = [];
    public $lastAddedItem = null;

    // Success feedback
    public $successMessage = '';
    public $showSuccessModal = false;

    // Draft editing (when resuming a draft)
    public $editingDraftId = null;

    public function mount()
    {
        // Set default date range to current month
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->createTransactionDate = now()->format('Y-m-d');
    }

    /**
     * Payment terms for dropdown (reuse SalesOrder options)
     */
    public function getTermOptionsProperty()
    {
        return SalesOrder::paymentTermsDropdown();
    }

    /**
     * Filtered branches for Step 1 searchable dropdown
     */
    public function getFilteredCreateBranchesProperty()
    {
        $branches = Branch::orderBy('name')->get();
        $query = trim($this->createBranchSearch ?? '');
        if ($query === '') {
            return $branches;
        }
        $lower = strtolower($query);
        return $branches->filter(fn ($b) => str_contains(strtolower($b->name ?? ''), $lower)
            || str_contains(strtolower($b->code ?? ''), $lower))->values();
    }

    /**
     * Filtered agents for Step 1 searchable dropdown
     */
    public function getFilteredCreateAgentsProperty()
    {
        $agents = Agent::orderBy('name')->get();
        $query = trim($this->createAgentSearch ?? '');
        if ($query === '') {
            return $agents;
        }
        $lower = strtolower($query);
        return $agents->filter(fn ($a) => str_contains(strtolower($a->name ?? ''), $lower)
            || str_contains(strtolower($a->agent_code ?? ''), $lower))->values();
    }

    public function selectCreateBranch($id)
    {
        $branch = Branch::find($id);
        $this->createBranchId = $id ? (string) $id : '';
        $this->createBranchDropdown = false;
        $this->createBranchSearch = $branch ? $branch->name : '';
        if ($this->createBranchId && $this->currentStep === 1) {
            $this->loadSellingAreaOptions();
        }
    }

    public function selectCreateAgent($id)
    {
        $agent = Agent::find($id);
        $this->createAgentId = $id ? (string) $id : '';
        $this->createAgentDropdown = false;
        $this->createAgentSearch = $agent ? ($agent->agent_code . ' - ' . $agent->name) : '';
    }

    /**
     * Open create stepper and reset state
     */
    public function openCreateStepper()
    {
        $this->editingDraftId = null;
        $this->showCreateStepper = true;
        $this->currentStep = 1;
        $this->resetCreateForm();
    }

    /**
     * Close create stepper
     */
    public function closeCreateStepper()
    {
        $this->showCreateStepper = false;
        $this->currentStep = 1;
        $this->resetCreateForm();
    }

    /**
     * Reset create form state
     */
    protected function resetCreateForm()
    {
        $this->editingDraftId = null;
        $this->createBranchId = '';
        $this->createTransactionDate = now()->format('Y-m-d');
        $this->createSellingArea = '';
        $this->createAgentId = '';
        $this->createTerm = '';
        $this->createRemarks = '';
        $this->createBranchSearch = '';
        $this->createBranchDropdown = false;
        $this->createAgentSearch = '';
        $this->createAgentDropdown = false;
        $this->salesItems = [];
        $this->salesBarcodeInput = '';
        $this->salesQuantity = 1;
        $this->branchProducts = [];
        $this->availableAgents = [];
        $this->sellingAreaOptions = [];
        $this->resetAddItemModal();
    }

    /**
     * Reset Add Item modal state
     */
    protected function resetAddItemModal()
    {
        $this->showAddItemModal = false;
        $this->addItemSearch = '';
        $this->addItemBarcodeSearch = false;
        $this->addItemSelectedProduct = null;
        $this->addItemQuantity = 1;
        $this->addItemUnitPrice = 0;
        $this->addItemDiscountPercent = 0;
        $this->addItemDiscountAmount = 0;
        $this->addItemRemarks = '';
        $this->addItemPromoName = '';
    }

    /**
     * Step 1: Validate and go to Step 2
     */
    public function step1Next()
    {
        $this->validate([
            'createBranchId' => 'required|exists:branches,id',
            'createTransactionDate' => 'required|date',
            'createAgentId' => 'required|exists:agents,id',
        ], [
            'createBranchId.required' => 'Please select a branch.',
            'createTransactionDate.required' => 'Transaction date is required.',
            'createAgentId.required' => 'Please select a sales agent.',
        ]);

        $this->loadBranchProductsForCreate();
        $this->loadAgentsForBranch();
        $this->loadSellingAreaOptions();
        $this->currentStep = 2;
    }

    /**
     * Save Step 1 as draft and close stepper (no items yet)
     */
    public function saveAsDraftAndClose()
    {
        $this->validate([
            'createBranchId' => 'required|exists:branches,id',
            'createTransactionDate' => 'required|date',
            'createAgentId' => 'required|exists:agents,id',
        ], [
            'createBranchId.required' => 'Please select a branch.',
            'createTransactionDate.required' => 'Transaction date is required.',
            'createAgentId.required' => 'Please select a sales agent.',
        ]);

        try {
            $sale = BranchCustomerSale::create([
                'branch_id' => $this->createBranchId,
                'selling_area' => $this->createSellingArea ?: null,
                'agent_id' => $this->createAgentId ?: null,
                'transaction_date' => $this->createTransactionDate,
                'term' => $this->createTerm ?: null,
                'remarks' => $this->createRemarks ?: null,
                'total_amount' => 0,
                'status' => 'draft',
            ]);

            $this->closeCreateStepper();
            session()->flash('success', "Draft saved successfully. Reference: {$sale->ref_no}");
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving draft: ' . $e->getMessage());
        }
    }

    /**
     * Resume editing a draft sale
     */
    public function resumeDraft($saleId)
    {
        $sale = BranchCustomerSale::draft()->findOrFail($saleId);

        $this->editingDraftId = $sale->id;
        $this->createBranchId = $sale->branch_id;
        $this->createTransactionDate = $sale->transaction_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->createSellingArea = $sale->selling_area ?? '';
        $this->createAgentId = $sale->agent_id ?? '';
        $this->createTerm = $sale->term ?? '';
        $this->createRemarks = $sale->remarks ?? '';

        $this->loadBranchProductsForCreate();
        $this->loadAgentsForBranch();
        $this->loadSellingAreaOptions();

        $this->salesItems = [];
        foreach ($sale->items as $line) {
            $this->salesItems[] = [
                'id' => $line->product_id,
                'name' => $line->product_name,
                'product_number' => null,
                'barcode' => $line->barcode,
                'quantity' => $line->quantity,
                'unit_price' => (float) $line->unit_price,
                'discount_percent' => (float) ($line->discount_percent ?? 0),
                'discount_amount' => (float) ($line->discount_amount ?? 0),
                'net_price' => (float) $line->net_price,
                'total' => (float) $line->total_amount,
                'remarks' => $line->remarks ?? '',
                'promo_name' => null,
            ];
            $this->updateBranchProductsRemaining($line->product_id, $line->quantity);
        }

        $this->showCreateStepper = true;
        $this->currentStep = 2;
    }

    /**
     * Load branch products for the selected branch (from completed shipments)
     */
    protected function loadBranchProductsForCreate()
    {
        if (!$this->createBranchId) {
            $this->branchProducts = [];
            return;
        }

        $items = BranchAllocationItem::with(['product', 'branchAllocation.shipments', 'branchAllocation.batchAllocation'])
            ->whereHas('branchAllocation', fn($q) => $q->where('branch_id', $this->createBranchId))
            ->whereHas('branchAllocation.shipments', fn($q) => $q->where('shipping_status', 'completed'))
            ->whereNull('box_id')
            ->get();

        $grouped = $items->groupBy('product_id');
        $this->branchProducts = $grouped->map(function ($productItems, $productId) {
            $product = $productItems->first()->product;
            $totalQuantity = $productItems->sum('quantity');
            $totalSold = $productItems->sum('sold_quantity');
            $remaining = $totalQuantity - $totalSold;
            $unitPrice = $productItems->first()->unit_price;

            return [
                'id' => (int) $productId,
                'name' => $productItems->first()->product_snapshot_name ?: $product->name,
                'product_number' => $product->product_number ?? null,
                'sku' => $productItems->first()->product_snapshot_sku ?: $product->sku ?? null,
                'barcode' => $productItems->first()->product_snapshot_barcode ?: $product->barcode ?? null,
                'total_quantity' => $totalQuantity,
                'total_sold' => $totalSold,
                'remaining_quantity' => $remaining,
                'unit_price' => (float) $unitPrice,
            ];
        })->values()->toArray();
    }

    /**
     * Load agents for branch (all agents for now; can filter by assignment later)
     */
    protected function loadAgentsForBranch()
    {
        $this->availableAgents = Agent::orderBy('name')->get();
    }

    /**
     * Load selling area options from branch
     */
    protected function loadSellingAreaOptions()
    {
        $branch = Branch::find($this->createBranchId);
        $this->sellingAreaOptions = $branch ? $branch->getSellingAreas() : [];
    }

    /**
     * Step 2: Go back to Step 1
     */
    public function step2Back()
    {
        $this->currentStep = 1;
    }

    /**
     * Open Add Item modal
     */
    public function openAddItemModal()
    {
        $this->resetAddItemModal();
        $this->showAddItemModal = true;
    }

    /**
     * Close Add Item modal
     */
    public function closeAddItemModal()
    {
        $this->showAddItemModal = false;
        $this->resetAddItemModal();
    }

    /**
     * Filtered branch products for Add Item modal.
     * Only returns results when user has typed a search (no default list).
     * Uses prefix segmentation token matching: e.g. "TY-209" matches "TY123-209abc".
     * Barcode mode: results not shown (auto-add handles it).
     */
    public function getFilteredBranchProductsForAddItemProperty()
    {
        $products = $this->branchProducts;
        $search = trim($this->addItemSearch ?? '');
        $barcodeMode = (bool) $this->addItemBarcodeSearch;

        if (empty($search)) {
            return [];
        }

        if ($barcodeMode) {
            return [];
        }

        $tokens = preg_split('/[\s\-_]+/', strtolower($search), -1, PREG_SPLIT_NO_EMPTY);
        if (empty($tokens)) {
            return [];
        }

        return collect($products)->filter(function ($p) use ($tokens) {
            $searchable = implode(' ', array_filter([
                $p['name'] ?? '',
                $p['product_number'] ?? '',
                $p['sku'] ?? '',
                $p['barcode'] ?? '',
            ]));
            $segments = preg_split('/[\s\-_]+/', strtolower($searchable), -1, PREG_SPLIT_NO_EMPTY);

            foreach ($tokens as $token) {
                $found = false;
                foreach ($segments as $seg) {
                    if (str_starts_with($seg, $token)) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    return false;
                }
            }
            return true;
        })->take(20)->values()->toArray();
    }

    /**
     * Select product in Add Item modal
     */
    public function selectAddItemProduct($productId)
    {
        $product = collect($this->branchProducts)->firstWhere('id', (int) $productId);
        if (!$product) {
            return;
        }
        if (($product['remaining_quantity'] ?? 0) < 1) {
            session()->flash('error', 'No stock available for this product.');
            return;
        }
        $this->addItemSelectedProduct = $product;
        $this->addItemUnitPrice = (float) ($product['unit_price'] ?? 0);
        $this->addItemQuantity = 1;
        $this->addItemDiscountPercent = 0;
        $this->addItemDiscountAmount = 0;
        $this->addItemPromoName = $this->getPromoNameForProduct($product['id']);
        $this->addItemRemarks = '';
    }

    /**
     * Get active promo name for a product (if tagged)
     */
    protected function getPromoNameForProduct($productId)
    {
        $promos = Promo::where('startDate', '<=', now())
            ->where('endDate', '>=', now())
            ->get();
        $productId = (int) $productId;
        foreach ($promos as $promo) {
            $productIds = is_array($promo->product) ? $promo->product : (json_decode($promo->product ?? '[]', true) ?? []);
            $secondIds = is_array($promo->second_product ?? null) ? $promo->second_product : (json_decode($promo->second_product ?? '[]', true) ?? []);
            if (in_array($productId, $productIds) || in_array($productId, $secondIds)) {
                return $promo->name;
            }
        }
        return '';
    }

    /**
     * When in barcode search mode and search matches exactly one product, auto-add it.
     * Uses pre-filled values (qty, price, discount, remarks) if user set them, else defaults.
     */
    public function updatedAddItemSearch()
    {
        if (!(bool) $this->addItemBarcodeSearch) {
            return;
        }

        $search = trim($this->addItemSearch ?? '');
        if (empty($search)) {
            return;
        }

        $searchLower = strtolower($search);
        $matches = collect($this->branchProducts)->filter(function ($p) use ($searchLower) {
            $barcode = strtolower(trim($p['barcode'] ?? ''));
            $productNumber = strtolower(trim($p['product_number'] ?? ''));
            return $barcode === $searchLower || $productNumber === $searchLower;
        })->values();

        if ($matches->count() !== 1) {
            return;
        }

        $product = $matches->first();
        if (($product['remaining_quantity'] ?? 0) < 1) {
            session()->flash('error', 'No stock available for this product.');
            return;
        }

        $this->addItemSelectedProduct = $product;
        $this->addItemPromoName = $this->getPromoNameForProduct($product['id']);

        $qty = (int) $this->addItemQuantity;
        $price = (float) $this->addItemUnitPrice;
        $this->addItemQuantity = $qty > 0 ? $qty : 1;
        $this->addItemUnitPrice = $price > 0 ? $price : (float) ($product['unit_price'] ?? 0);
        $this->addItemDiscountPercent = (float) ($this->addItemDiscountPercent ?? 0);
        $this->recalcAddItemDiscount();

        $this->addItemFromModal(true);
        $this->addItemSearch = '';
    }

    /**
     * Recalculate discount amount when qty, price, or discount % changes
     */
    public function updatedAddItemQuantity()
    {
        $this->recalcAddItemDiscount();
    }

    public function updatedAddItemUnitPrice()
    {
        $this->recalcAddItemDiscount();
    }

    public function updatedAddItemDiscountPercent()
    {
        $this->recalcAddItemDiscount();
    }

    protected function recalcAddItemDiscount()
    {
        $subtotal = (float) $this->addItemUnitPrice * max(1, (int) $this->addItemQuantity);
        $pct = (float) ($this->addItemDiscountPercent ?? 0);
        $this->addItemDiscountAmount = round($subtotal * ($pct / 100), 2);
    }

    /**
     * Add item from modal - $andAddNew: true = stay open for another
     */
    public function addItemFromModal($andAddNew = false)
    {
        if (!$this->addItemSelectedProduct) {
            session()->flash('error', 'Please select a product.');
            return;
        }
        $p = $this->addItemSelectedProduct;
        $qty = max(1, (int) $this->addItemQuantity);
        if ($qty > ($p['remaining_quantity'] ?? 0)) {
            session()->flash('error', 'Quantity exceeds available stock.');
            return;
        }
        $unitPrice = (float) $this->addItemUnitPrice;
        $discountPct = (float) ($this->addItemDiscountPercent ?? 0);
        $discountAmt = (float) ($this->addItemDiscountAmount ?? 0);
        $subtotal = $unitPrice * $qty;
        $total = $subtotal - $discountAmt;
        $netPrice = $qty > 0 ? $total / $qty : $unitPrice;

        $item = [
            'id' => $p['id'],
            'name' => $p['name'],
            'product_number' => $p['product_number'] ?? null,
            'barcode' => $p['barcode'] ?? null,
            'quantity' => $qty,
            'unit_price' => $unitPrice,
            'discount_percent' => $discountPct,
            'discount_amount' => $discountAmt,
            'net_price' => $netPrice,
            'total' => $total,
            'remarks' => trim($this->addItemRemarks ?? ''),
            'promo_name' => $this->addItemPromoName ?: null,
            'promo_id' => null,
        ];

        $this->salesItems[] = $item;
        $this->updateBranchProductsRemaining($p['id'], $qty);

        if ($andAddNew) {
            $this->addItemSelectedProduct = null;
            $this->addItemQuantity = 1;
            $this->addItemUnitPrice = 0;
            $this->addItemDiscountPercent = 0;
            $this->addItemDiscountAmount = 0;
            $this->addItemRemarks = '';
            $this->addItemPromoName = '';
            $this->addItemSearch = '';
        } else {
            $this->closeAddItemModal();
        }
    }

    /**
     * Step 2: Process barcode/product_number scan (kept for barcode scanner quick-add if needed)
     */
    public function processSalesBarcode()
    {
        $barcode = trim($this->salesBarcodeInput);
        if (empty($barcode)) {
            $this->dispatch('refocus-sales-barcode');
            return;
        }

        $product = collect($this->branchProducts)->first(function ($p) use ($barcode) {
            return ($p['barcode'] ?? '') === $barcode
                || ($p['product_number'] ?? '') === $barcode;
        });

        if (!$product) {
            session()->flash('error', 'Product not found for: ' . $barcode);
            $this->salesBarcodeInput = '';
            $this->dispatch('refocus-sales-barcode');
            return;
        }

        if ($product['remaining_quantity'] < 1) {
            session()->flash('error', 'No stock available for this product.');
            $this->salesBarcodeInput = '';
            $this->dispatch('refocus-sales-barcode');
            return;
        }

        $qty = min($this->salesQuantity ?: 1, $product['remaining_quantity']);
        $unitPrice = (float) ($product['unit_price'] ?? 0);
        $discountPct = 0;
        $discountAmt = 0;
        $netPrice = $unitPrice;

        $item = [
            'id' => $product['id'],
            'name' => $product['name'],
            'product_number' => $product['product_number'] ?? null,
            'barcode' => $product['barcode'] ?? null,
            'quantity' => $qty,
            'unit_price' => $unitPrice,
            'discount_percent' => $discountPct,
            'discount_amount' => $discountAmt,
            'net_price' => $netPrice,
            'total' => $netPrice * $qty,
            'remarks' => '',
            'promo_name' => null,
        ];

        $this->salesItems[] = $item;
        $this->lastAddedItem = $item;
        $this->salesBarcodeInput = '';
        $this->salesQuantity = 1;
        $this->dispatch('refocus-sales-barcode');

        // Update branch products remaining
        $this->updateBranchProductsRemaining($product['id'], $qty);
    }

    /**
     * Update remaining quantity in branch products after adding item
     */
    protected function updateBranchProductsRemaining($productId, $soldQty)
    {
        foreach ($this->branchProducts as $i => $p) {
            if ($p['id'] == $productId) {
                $this->branchProducts[$i]['remaining_quantity'] -= $soldQty;
                break;
            }
        }
    }

    /**
     * Remove item from sales list
     */
    public function removeSalesItem($index)
    {
        if (isset($this->salesItems[$index])) {
            $item = $this->salesItems[$index];
            unset($this->salesItems[$index]);
            $this->salesItems = array_values($this->salesItems);

            // Restore remaining in branch products
            foreach ($this->branchProducts as $i => $p) {
                if ($p['id'] == $item['id']) {
                    $this->branchProducts[$i]['remaining_quantity'] += $item['quantity'];
                    break;
                }
            }
        }
    }

    /**
     * Step 2: Validate and go to Step 3
     */
    public function step2Next()
    {
        if (empty($this->salesItems)) {
            session()->flash('error', 'Please add at least one item before continuing.');
            return;
        }
        $this->currentStep = 3;
    }

    /**
     * Step 3: Go back to Step 2
     */
    public function step3Back()
    {
        $this->currentStep = 2;
    }

    /**
     * Step 3: Save branch sales
     */
    public function saveBranchSales()
    {
        if (empty($this->salesItems)) {
            session()->flash('error', 'Please add at least one item before saving.');
            return;
        }

        try {
            DB::beginTransaction();

            $totalAmount = collect($this->salesItems)->sum('total');

            if ($this->editingDraftId) {
                $sale = BranchCustomerSale::findOrFail($this->editingDraftId);
                $sale->update([
                    'branch_id' => $this->createBranchId,
                    'selling_area' => $this->createSellingArea ?: null,
                    'agent_id' => $this->createAgentId ?: null,
                    'transaction_date' => $this->createTransactionDate,
                    'term' => $this->createTerm ?: null,
                    'remarks' => $this->createRemarks ?: null,
                    'total_amount' => $totalAmount,
                    'status' => 'completed',
                ]);
                $sale->items()->delete();
            } else {
                $sale = BranchCustomerSale::create([
                    'branch_id' => $this->createBranchId,
                    'selling_area' => $this->createSellingArea ?: null,
                    'agent_id' => $this->createAgentId ?: null,
                    'transaction_date' => $this->createTransactionDate,
                    'term' => $this->createTerm ?: null,
                    'remarks' => $this->createRemarks ?: null,
                    'total_amount' => $totalAmount,
                ]);
            }

            foreach ($this->salesItems as $item) {
                $sale->items()->create([
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'barcode' => $item['barcode'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'net_price' => $item['net_price'] ?? $item['unit_price'],
                    'total_amount' => $item['total'],
                    'remarks' => $item['remarks'] ?? null,
                    'promo_id' => $item['promo_id'] ?? null,
                ]);
            }

            foreach ($this->salesItems as $item) {
                $allocationItems = BranchAllocationItem::whereHas('product', fn($q) => $q->where('id', $item['id']))
                    ->whereHas('branchAllocation', fn($q) => $q->where('branch_id', $this->createBranchId))
                    ->whereHas('branchAllocation.shipments', fn($q) => $q->where('shipping_status', 'completed'))
                    ->whereNull('box_id')
                    ->orderBy('id')
                    ->get();

                $remaining = $item['quantity'];
                foreach ($allocationItems as $ai) {
                    $available = $ai->quantity - $ai->sold_quantity;
                    if ($available > 0 && $remaining > 0) {
                        $inc = min($remaining, $available);
                        $ai->increment('sold_quantity', $inc);
                        $remaining -= $inc;
                    }
                    if ($remaining <= 0) break;
                }
            }

            DB::commit();

            $this->closeCreateStepper();
            $this->successMessage = "Customer sales recorded successfully! Reference Number: {$sale->ref_no}";
            $this->showSuccessModal = true;
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error recording sales: ' . $e->getMessage());
        }
    }

    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;
        $this->successMessage = '';
    }

    /**
     * When branch changes in Step 1, load selling area options
     */
    public function updatedCreateBranchId()
    {
        if ($this->createBranchId && $this->currentStep === 1) {
            $this->loadSellingAreaOptions();
        }
    }

    /**
     * Auto-process barcode on input (for scanners)
     */
    public function updatedSalesBarcodeInput()
    {
        $barcode = trim($this->salesBarcodeInput);
        if (!empty($barcode)) {
            $this->processSalesBarcode();
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingSelectedBranchId()
    {
        $this->resetPage();
    }

    public function updatingSelectedAgentId()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->selectedBranchId = '';
        $this->selectedAgentId = '';
        $this->resetPage();
    }

    public function viewSaleDetails($saleId)
    {
        $this->selectedSale = BranchCustomerSale::with(['branch', 'agent', 'items.product'])
            ->findOrFail($saleId);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedSale = null;
    }

    public function render()
    {
        $query = BranchCustomerSale::with(['branch', 'agent', 'items'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->search) {
            $query->where(function($q) {
                $q->where('ref_no', 'like', '%' . $this->search . '%')
                  ->orWhereHas('branch', function($subq) {
                      $subq->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('agent', function($subq) {
                      $subq->where('name', 'like', '%' . $this->search . '%')
                           ->orWhere('agent_code', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->selectedBranchId) {
            $query->where('branch_id', $this->selectedBranchId);
        }

        if ($this->selectedAgentId) {
            $query->where('agent_id', $this->selectedAgentId);
        }

        $sales = $query->paginate($this->perPage);

        // Get branches and agents for filters
        $branches = Branch::orderBy('name')->get();
        $agents = Agent::orderBy('name')->get();

        return view('livewire.pages.branch.branch-sales', [
            'sales' => $sales,
            'branches' => $branches,
            'agents' => $agents,
        ]);
    }
}
