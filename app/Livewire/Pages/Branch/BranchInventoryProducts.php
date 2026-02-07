<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\BranchAllocationItem;
use App\Models\BranchCustomerSale;
use App\Models\Promo;
use Spatie\Activitylog\Models\Activity;

class BranchInventoryProducts extends Component
{
    use WithFileUploads;

    public Branch $branch;

    public $branchProducts = [];
    public $selectedProductDetails = null;

    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $statusFilter = '';

    public $showProductViewModal = false;
    public $showUploadModal = false;
    public $showResultsModal = false;
    public $showSuccessModal = false;
    public $showCustomerSalesModal = false;
    public $successMessage = '';

    public $salesBarcodeInput = '';
    public $selectedSalesProduct = null;
    public $salesQuantity = 1;
    public $salesItems = [];
    public $selectedSellingArea = null;
    public $sellingAreaOptions = [];
    public $sellingAreaSearch = '';
    public $sellingAreaDropdown = false;
    public $selectedAgentId = null;
    public $availableAgents = [];
    public $agentSearch = '';
    public $agentDropdown = false;
    public $lastAddedItem = null;

    public $textFile;
    public $uploadedBarcodeCount = 0;
    public $matchedBarcodeCount = 0;
    public $barcodeMatches = [];
    public $uploadResults = [];
    public $unmatchedBarcodes = [];
    public $validBarcodes = [];
    public $invalidBarcodes = [];
    public $similarBarcodes = [];

    public $auditResults = [];
    public $missingItems = [];
    public $extraItems = [];
    public $quantityVariances = [];
    public $auditDate = null;
    public ?int $existingAuditIdForDay = null;
    public ?string $existingAuditCreatedAtForDay = null;
    public ?string $existingAuditDay = null;

    public $editingShipmentId = null;
    public $editingAllocatedQuantity = 0;
    public $activeHistoryTab = 'upload_history';

    public function mount(Branch $branch)
    {
        $this->branch = $branch;
        $this->loadAgents();
        $this->loadProducts();
    }

    public function setActiveHistoryTab($tab)
    {
        $this->activeHistoryTab = $tab;
    }

    protected function loadAgents()
    {
        $this->availableAgents = \App\Models\Agent::orderBy('name')->get();
    }

    protected function loadProducts()
    {
        $branchId = $this->branch->id;

        $items = BranchAllocationItem::with([
            'product',
            'branchAllocation.shipments',
            'branchAllocation.batchAllocation',
        ])
        ->whereHas('branchAllocation', fn ($q) => $q->where('branch_id', $branchId))
        ->whereHas('branchAllocation.shipments', fn ($q) => $q->where('shipping_status', 'completed'))
        ->where('box_id', null)
        ->get();

        $groupedProducts = $items->groupBy('product_id');

        $this->branchProducts = $groupedProducts->map(function ($productItems, $productId) {
            $product = $productItems->first()->product;
            $totalQuantity = $productItems->sum('quantity');
            $totalSold = $productItems->sum('sold_quantity');

            $shipments = $productItems->map(function ($item) {
                $shipment = $item->branchAllocation->shipments->where('shipping_status', 'completed')->first();
                if (!$shipment) return null;
                $allocation = $item->branchAllocation;
                return [
                    'id' => $shipment->id,
                    'shipping_plan_num' => $shipment->shipping_plan_num,
                    'shipment_date' => $shipment->created_at->format('M d, Y'),
                    'carrier_name' => $shipment->carrier_name ?: 'N/A',
                    'delivery_method' => $shipment->delivery_method ?: 'N/A',
                    'allocation_reference' => $allocation->batchAllocation->ref_no ?? 'N/A',
                    'batch_allocation_id' => $allocation->batchAllocation->id ?? null,
                    'barcode' => $item->getDisplayBarcodeAttribute(),
                    'allocated_quantity' => $item->quantity,
                    'sold_quantity' => $item->sold_quantity,
                    'price' => $item->unit_price,
                    'total' => $item->quantity * $item->unit_price,
                ];
            })->filter();

            return [
                'id' => $productId,
                'name' => $productItems->first()->getDisplayNameAttribute(),
                'product_number' => $product->product_number ?? null,
                'sku' => $productItems->first()->getDisplaySkuAttribute(),
                'supplier_code' => $product->supplier_code ?? null,
                'barcode' => $productItems->first()->getDisplayBarcodeAttribute(),
                'total_quantity' => $totalQuantity,
                'total_sold' => $totalSold,
                'remaining_quantity' => $totalQuantity - $totalSold,
                'unit_price' => $productItems->first()->unit_price,
                'total_value' => $productItems->sum(fn ($item) => $item->quantity * $item->unit_price),
                'image_url' => $product->getPrimaryImageAttribute() ? asset('storage/photos/' . $product->getPrimaryImageAttribute()) : null,
                'shipments' => $shipments,
            ];
        })->values();

        $this->branchProducts = $this->branchProducts->map(function ($product) {
            $productId = $product['id'];
            $batchAllocationIds = collect($product['shipments'])->pluck('batch_allocation_id')->filter()->unique();

            $activePromosCount = Promo::where('product', 'like', '%' . (string) $productId . '%')
                ->where(function ($q) use ($batchAllocationIds) {
                    $q->where(function ($sub) use ($batchAllocationIds) {
                        foreach ($batchAllocationIds as $id) {
                            $sub->orWhere('branch', 'like', '%' . (string) $id . '%');
                        }
                    });
                })
                ->where('startDate', '<=', now())
                ->where('endDate', '>=', now())
                ->count();

            $product['active_promos_count'] = $activePromosCount;
            return $product;
        });
    }

    public function viewProductDetails($productId)
    {
        $product = collect($this->branchProducts)->firstWhere('id', $productId);
        if ($product) {
            $this->selectedProductDetails = $product;
            $this->showProductViewModal = true;
        }
    }

    public function closeProductViewModal()
    {
        $this->showProductViewModal = false;
        $this->selectedProductDetails = null;
        $this->editingShipmentId = null;
        $this->editingAllocatedQuantity = 0;
        $this->activeHistoryTab = 'upload_history';
    }

    public function startEditingQuantity($shipmentId, $currentQuantity)
    {
        $this->editingShipmentId = $shipmentId;
        $this->editingAllocatedQuantity = $currentQuantity;
    }

    public function cancelEditingQuantity()
    {
        $this->editingShipmentId = null;
        $this->editingAllocatedQuantity = 0;
    }

    public function saveAllocatedQuantity()
    {
        if (!$this->editingShipmentId || !$this->selectedProductDetails) return;

        $this->validate(['editingAllocatedQuantity' => 'required|integer|min:0']);

        try {
            $productId = $this->selectedProductDetails['id'];
            $shipment = collect($this->selectedProductDetails['shipments'])->firstWhere('id', $this->editingShipmentId);

            if (!$shipment) {
                session()->flash('error', 'Shipment not found.');
                return;
            }

            $allocationItem = BranchAllocationItem::whereHas('branchAllocation.shipments', fn ($q) => $q->where('id', $shipment['id']))
                ->where('product_id', $productId)
                ->where('box_id', null)
                ->first();

            if (!$allocationItem) {
                session()->flash('error', 'Allocation item not found.');
                return;
            }

            if ($this->editingAllocatedQuantity < $allocationItem->sold_quantity) {
                session()->flash('error', 'Cannot reduce allocated quantity below sold quantity (' . $allocationItem->sold_quantity . ').');
                return;
            }

            $oldQuantity = $allocationItem->quantity;
            $allocationItem->update(['quantity' => $this->editingAllocatedQuantity]);

            Activity::create([
                'log_name' => 'branch_inventory',
                'description' => "Updated allocated quantity for product {$this->selectedProductDetails['barcode']} in shipment {$shipment['shipping_plan_num']}",
                'subject_type' => BranchAllocationItem::class,
                'subject_id' => $allocationItem->id,
                'causer_type' => null,
                'causer_id' => null,
                'properties' => [
                    'product_id' => $productId,
                    'product_name' => $this->selectedProductDetails['name'],
                    'barcode' => $this->selectedProductDetails['barcode'],
                    'shipment_id' => $this->editingShipmentId,
                    'old_quantity' => $oldQuantity,
                    'new_quantity' => $this->editingAllocatedQuantity,
                    'branch_id' => $this->branch->id,
                ],
            ]);

            $this->editingShipmentId = null;
            $this->editingAllocatedQuantity = 0;
            $this->loadProducts();
            $this->viewProductDetails($productId);
            session()->flash('message', 'Allocated quantity updated successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating quantity: ' . $e->getMessage());
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->statusFilter = '';
        $this->loadProducts();
    }

    public function refreshData()
    {
        $this->loadProducts();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'dateFrom', 'dateTo', 'statusFilter'])) {
            $this->loadProducts();
        }
    }

    public function openUploadModal()
    {
        $this->showUploadModal = true;
    }

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->textFile = null;
    }

    public function saveAuditResults()
    {
        if (empty($this->auditResults)) return;

        try {
            $auditDay = $this->auditDate ? $this->auditDate->toDateString() : now()->toDateString();
            $existing = Activity::query()
                ->where('log_name', 'inventory_audit')
                ->where('subject_type', Branch::class)
                ->where('subject_id', $this->branch->id)
                ->whereDate('created_at', $auditDay)
                ->latest('created_at')
                ->first();

            if ($existing) {
                $this->existingAuditIdForDay = $existing->id;
                $this->existingAuditCreatedAtForDay = optional($existing->created_at)->toDateTimeString();
                $this->existingAuditDay = $auditDay;
                $this->addError('audit', "An audit is already saved for this branch on {$auditDay} (saved at {$this->existingAuditCreatedAtForDay}).");
                return;
            }

            Activity::create([
                'log_name' => 'inventory_audit',
                'description' => "Inventory audit for branch {$this->branch->id}",
                'subject_type' => Branch::class,
                'subject_id' => $this->branch->id,
                'causer_type' => null,
                'causer_id' => null,
                'properties' => [
                    'audit_date' => $this->auditDate ? $this->auditDate->toDateTimeString() : now()->toDateTimeString(),
                    'total_scanned' => $this->auditResults['total_scanned'] ?? 0,
                    'total_allocated' => $this->auditResults['total_allocated'] ?? 0,
                    'total_products_allocated' => $this->auditResults['total_products_allocated'] ?? 0,
                    'total_products_scanned' => $this->auditResults['total_products_scanned'] ?? 0,
                    'missing_items' => $this->missingItems,
                    'extra_items' => $this->extraItems,
                    'quantity_variances' => $this->quantityVariances,
                    'missing_items_count' => count($this->missingItems),
                    'extra_items_count' => count($this->extraItems),
                    'quantity_variances_count' => count($this->quantityVariances),
                ],
            ]);

            $this->showResultsModal = false;
            $this->loadProducts();

            $totalVariances = count($this->missingItems) + count($this->extraItems) + count($this->quantityVariances);
            $message = "Inventory audit saved successfully! ";
            if ($totalVariances > 0) {
                $parts = [];
                if (count($this->missingItems) > 0) $parts[] = count($this->missingItems) . " missing";
                if (count($this->extraItems) > 0) $parts[] = count($this->extraItems) . " extra";
                if (count($this->quantityVariances) > 0) $parts[] = count($this->quantityVariances) . " quantity mismatch(es)";
                $message .= "Found {$totalVariances} variance(s): " . implode(", ", $parts) . ".";
            } else {
                $message .= "No variances found - inventory matches allocation.";
            }

            $this->successMessage = $message;
            $this->showSuccessModal = true;
        } catch (\Exception $e) {
            $this->addError('save', 'Error saving audit data: ' . $e->getMessage());
        }
    }

    public function viewTodaysAudit()
    {
        $auditDay = $this->existingAuditDay ?? ($this->auditDate ? $this->auditDate->toDateString() : now()->toDateString());
        return redirect()->to(route('reports.branch-inventory', [
            'selectedBranch' => $this->branch->id,
            'dateFrom' => $auditDay,
            'dateTo' => $auditDay,
        ]));
    }

    public function closeResultsModal()
    {
        $this->showResultsModal = false;
        $this->missingItems = [];
        $this->extraItems = [];
        $this->quantityVariances = [];
        $this->auditResults = [];
        $this->existingAuditIdForDay = null;
        $this->existingAuditCreatedAtForDay = null;
        $this->existingAuditDay = null;
        $this->resetErrorBag('audit');
    }

    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;
        $this->successMessage = '';
    }

    public function processTextFile()
    {
        $this->validate(['textFile' => 'required|file|mimes:txt|max:1024']);
        $this->similarBarcodes = [];
        try {
            $content = file_get_contents($this->textFile->getRealPath());
            $barcodes = $this->parseBarcodeFile($content);
            $this->processBarcodeComparison($barcodes);
            $this->showUploadModal = false;
            $this->showResultsModal = true;
        } catch (\Exception $e) {
            $this->addError('textFile', 'Error processing file: ' . $e->getMessage());
        }
    }

    protected function parseBarcodeFile($content)
    {
        $lines = explode("\n", $content);
        $barcodes = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) $barcodes[] = $line;
        }
        $this->uploadedBarcodeCount = count($barcodes);
        return $barcodes;
    }

    protected function processBarcodeComparison($uploadedBarcodes)
    {
        $branchId = $this->branch->id;

        $allocatedItems = BranchAllocationItem::with('product')
            ->whereHas('branchAllocation', fn ($q) => $q->where('branch_id', $branchId))
            ->whereHas('branchAllocation.shipments', fn ($q) => $q->where('shipping_status', 'completed'))
            ->where('box_id', null)
            ->get();

        $allocatedProducts = [];
        foreach ($allocatedItems as $item) {
            $barcode = $item->getDisplayBarcodeAttribute();
            if (!empty($barcode) && $barcode !== 'N/A') {
                if (!isset($allocatedProducts[$barcode])) {
                    $product = $item->product;
                    $allocatedProducts[$barcode] = [
                        'product_id' => $item->product_id,
                        'product_number' => $product ? ($product->product_number ?? null) : null,
                        'product_name' => $item->getDisplayNameAttribute(),
                        'sku' => $item->getDisplaySkuAttribute(),
                        'supplier_code' => $product ? ($product->supplier_code ?? null) : null,
                        'allocated_quantity' => 0,
                    ];
                }
                $allocatedProducts[$barcode]['allocated_quantity'] += $item->quantity;
            }
        }

        $scannedBarcodeCount = array_count_values($uploadedBarcodes);
        $this->uploadedBarcodeCount = count($uploadedBarcodes);
        $this->missingItems = [];
        $this->extraItems = [];
        $this->quantityVariances = [];
        $this->auditDate = now();

        foreach ($allocatedProducts as $barcode => $product) {
            $scannedCount = $scannedBarcodeCount[$barcode] ?? 0;
            if ($scannedCount == 0) {
                $this->missingItems[] = [
                    'barcode' => $barcode,
                    'product_number' => $product['product_number'] ?? null,
                    'product_name' => $product['product_name'],
                    'sku' => $product['sku'],
                    'supplier_code' => $product['supplier_code'] ?? null,
                    'allocated_quantity' => $product['allocated_quantity'],
                    'scanned_quantity' => 0,
                    'variance' => -$product['allocated_quantity'],
                ];
            } elseif ($scannedCount != $product['allocated_quantity']) {
                $this->quantityVariances[] = [
                    'barcode' => $barcode,
                    'product_number' => $product['product_number'] ?? null,
                    'product_name' => $product['product_name'],
                    'sku' => $product['sku'],
                    'supplier_code' => $product['supplier_code'] ?? null,
                    'allocated_quantity' => $product['allocated_quantity'],
                    'scanned_quantity' => $scannedCount,
                    'variance' => $scannedCount - $product['allocated_quantity'],
                ];
            }
        }

        foreach ($scannedBarcodeCount as $barcode => $scannedCount) {
            if (!isset($allocatedProducts[$barcode])) {
                $this->extraItems[] = ['barcode' => $barcode, 'scanned_quantity' => $scannedCount];
            }
        }

        $this->auditResults = [
            'total_allocated' => array_sum(array_column($allocatedProducts, 'allocated_quantity')),
            'total_scanned' => array_sum($scannedBarcodeCount),
            'total_products_allocated' => count($allocatedProducts),
            'total_products_scanned' => count($scannedBarcodeCount),
            'missing_items_count' => count($this->missingItems),
            'extra_items_count' => count($this->extraItems),
            'quantity_variances_count' => count($this->quantityVariances),
        ];
        $this->matchedBarcodeCount = count($allocatedProducts) - count($this->missingItems);
        $this->barcodeMatches = [];
        $this->uploadResults = [];
    }

    public function openCustomerSalesModal()
    {
        $this->showCustomerSalesModal = true;
        $this->resetSalesModal();
        $this->loadSellingAreaOptions();
    }

    public function closeCustomerSalesModal()
    {
        $this->showCustomerSalesModal = false;
        $this->resetSalesModal();
    }

    protected function resetSalesModal()
    {
        $this->salesBarcodeInput = '';
        $this->selectedSalesProduct = null;
        $this->salesQuantity = 1;
        $this->salesItems = [];
        $this->selectedSellingArea = null;
        $this->sellingAreaSearch = '';
        $this->sellingAreaDropdown = false;
        $this->selectedAgentId = null;
        $this->agentSearch = '';
        $this->agentDropdown = false;
        $this->lastAddedItem = null;
    }

    protected function loadSellingAreaOptions()
    {
        $this->sellingAreaOptions = $this->branch->getSellingAreas();
    }

    public function toggleSellingAreaDropdown()
    {
        if (!empty($this->sellingAreaOptions)) {
            $this->sellingAreaDropdown = !$this->sellingAreaDropdown;
        }
    }

    public function toggleAgentDropdown()
    {
        $this->agentDropdown = !$this->agentDropdown;
    }

    public function selectSellingArea($value = null)
    {
        $this->selectedSellingArea = $value ?? '';
        $this->sellingAreaDropdown = false;
    }

    public function selectAgent($agentId = null)
    {
        $this->selectedAgentId = $agentId ? (int) $agentId : null;
        $this->agentDropdown = false;
    }

    public function getFilteredSellingAreaOptionsProperty()
    {
        $options = $this->sellingAreaOptions;
        $query = trim($this->sellingAreaSearch);
        if ($query === '') return collect($options)->values();
        $lower = strtolower($query);
        return collect($options)->filter(fn ($v) => str_contains(strtolower((string) $v), $lower))->values();
    }

    public function getFilteredAvailableAgentsProperty()
    {
        $agents = collect($this->availableAgents);
        $query = trim($this->agentSearch);
        if ($query === '') return $agents;
        $lower = strtolower($query);
        return $agents->filter(fn ($agent) =>
            str_contains(strtolower((string) ($agent->name ?? '')), $lower)
            || str_contains(strtolower((string) ($agent->agent_code ?? '')), $lower)
        )->values();
    }

    public function updatedSalesBarcodeInput()
    {
        $this->processSalesBarcode();
    }

    public function processSalesBarcode()
    {
        if (!$this->selectedAgentId) {
            session()->flash('error', 'Please select an agent before scanning products.');
            $this->dispatch('refocus-sales-barcode');
            return;
        }
        $barcode = trim($this->salesBarcodeInput);
        if (empty($barcode)) {
            $this->dispatch('refocus-sales-barcode');
            return;
        }
        $product = collect($this->branchProducts)->first(fn ($p) => $p['barcode'] === $barcode);
        if (!$product) {
            session()->flash('error', 'Product not found for barcode: ' . $barcode);
            $this->salesBarcodeInput = '';
            $this->selectedSalesProduct = null;
            $this->dispatch('refocus-sales-barcode');
            return;
        }
        if (1 > $product['remaining_quantity']) {
            session()->flash('error', 'Quantity exceeds available stock.');
            $this->dispatch('refocus-sales-barcode');
            return;
        }
        $this->salesItems[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'product_number' => $product['product_number'] ?? null,
            'sku' => $product['sku'] ?? null,
            'supplier_code' => $product['supplier_code'] ?? null,
            'barcode' => $product['barcode'],
            'quantity' => 1,
            'unit_price' => $product['unit_price'],
            'total' => 1 * $product['unit_price'],
        ];
        $this->lastAddedItem = $this->salesItems[array_key_last($this->salesItems)];
        $this->salesBarcodeInput = '';
        $this->selectedSalesProduct = null;
        $this->salesQuantity = 1;
        $this->dispatch('refocus-sales-barcode');
    }

    public function clearSalesBarcodeInput()
    {
        $this->salesBarcodeInput = '';
        $this->selectedSalesProduct = null;
        $this->dispatch('refocus-sales-barcode');
    }

    public function addSalesItem()
    {
        if (!$this->selectedSalesProduct || !$this->salesQuantity || $this->salesQuantity < 1) return;
        if ($this->salesQuantity > $this->selectedSalesProduct['remaining_quantity']) {
            session()->flash('error', 'Quantity exceeds available stock.');
            return;
        }
        $this->salesItems[] = [
            'id' => $this->selectedSalesProduct['id'],
            'name' => $this->selectedSalesProduct['name'],
            'product_number' => $this->selectedSalesProduct['product_number'] ?? null,
            'sku' => $this->selectedSalesProduct['sku'] ?? null,
            'supplier_code' => $this->selectedSalesProduct['supplier_code'] ?? null,
            'barcode' => $this->selectedSalesProduct['barcode'],
            'quantity' => $this->salesQuantity,
            'unit_price' => $this->selectedSalesProduct['unit_price'],
            'total' => $this->salesQuantity * $this->selectedSalesProduct['unit_price'],
        ];
        $this->salesBarcodeInput = '';
        $this->selectedSalesProduct = null;
        $this->salesQuantity = 1;
    }

    public function removeSalesItem($index)
    {
        if (isset($this->salesItems[$index])) {
            unset($this->salesItems[$index]);
            $this->salesItems = array_values($this->salesItems);
        }
    }

    public function clearSalesItems()
    {
        $this->salesItems = [];
    }

    public function saveCustomerSales()
    {
        if (!$this->selectedAgentId) {
            session()->flash('error', 'Please select an agent before saving sales.');
            return;
        }
        if (empty($this->salesItems)) {
            session()->flash('error', 'Please add at least one item before saving sales.');
            return;
        }

        try {
            DB::beginTransaction();
            $totalAmount = collect($this->salesItems)->sum('total');

            $sale = BranchCustomerSale::create([
                'branch_id' => $this->branch->id,
                'selling_area' => $this->selectedSellingArea ?: null,
                'agent_id' => $this->selectedAgentId ?: null,
                'total_amount' => $totalAmount,
            ]);

            foreach ($this->salesItems as $item) {
                $sale->items()->create([
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'barcode' => $item['barcode'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_amount' => $item['total'],
                ]);
            }

            foreach ($this->salesItems as $item) {
                $allocationItems = BranchAllocationItem::whereHas('product', fn ($q) => $q->where('id', $item['id']))
                    ->whereHas('branchAllocation', fn ($q) => $q->where('branch_id', $this->branch->id))
                    ->whereHas('branchAllocation.shipments', fn ($q) => $q->where('shipping_status', 'completed'))
                    ->where('box_id', null)
                    ->orderBy('id')
                    ->get();
                $remainingQuantity = $item['quantity'];
                foreach ($allocationItems as $allocationItem) {
                    $available = $allocationItem->quantity - $allocationItem->sold_quantity;
                    if ($available > 0 && $remainingQuantity > 0) {
                        $increment = min($remainingQuantity, $available);
                        $allocationItem->increment('sold_quantity', $increment);
                        $remainingQuantity -= $increment;
                    }
                    if ($remainingQuantity <= 0) break;
                }
            }

            $agent = $this->selectedAgentId ? \App\Models\Agent::find($this->selectedAgentId) : null;
            foreach ($this->salesItems as $item) {
                Activity::create([
                    'log_name' => 'branch_inventory',
                    'description' => "Customer sale recorded for product {$item['barcode']} in branch {$this->branch->id}" . ($this->selectedSellingArea ? " at selling area {$this->selectedSellingArea}" : "") . ($agent ? " by agent {$agent->name}" : ""),
                    'subject_type' => BranchCustomerSale::class,
                    'subject_id' => $sale->id,
                    'causer_type' => null,
                    'causer_id' => null,
                    'properties' => [
                        'product_id' => $item['id'],
                        'product_name' => $item['name'],
                        'barcode' => $item['barcode'],
                        'quantity_sold' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_amount' => $item['total'],
                        'branch_id' => $this->branch->id,
                        'selling_area' => $this->selectedSellingArea,
                        'agent_id' => $this->selectedAgentId,
                        'agent_name' => $agent ? $agent->name : null,
                        'customer_sale' => true,
                    ],
                ]);
            }

            DB::commit();
            $this->loadProducts();
            $this->closeCustomerSalesModal();
            $this->successMessage = "Customer sales recorded successfully! Reference Number: {$sale->ref_no}";
            $this->showSuccessModal = true;
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error recording sales: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.branch.branch-inventory-products');
    }
}
