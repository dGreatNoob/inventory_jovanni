<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Branch;
use App\Models\Shipment;
use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;
use App\Models\Promo;
use Spatie\Activitylog\Models\Activity;

class BranchInventory extends Component
{
    use WithFileUploads;
    // Batch selection
    public $selectedBatch = null;
    public $batches = [];

    // Branch selection within batch
    public $selectedBranchId = null;
    public $batchBranches = [];

    // Product data for selected branch
    public $branchProducts = [];
    public $selectedProductDetails = null;

    // Search and filters
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $statusFilter = '';

    // Modal controls
    public $showProductViewModal = false;
    public $showUploadModal = false;
    public $showResultsModal = false;
    public $showSuccessModal = false;
    public $showCustomerSalesModal = false;
    public $successMessage = '';

    // Customer sales properties
    public $salesBarcodeInput = '';
    public $selectedSalesProduct = null;
    public $salesQuantity = 1;
    public $salesItems = [];
    public $selectedAgentId = null;
    public $availableAgents = [];

    // File upload properties
    public $textFile;
    public $uploadedBarcodeCount = 0;
    public $matchedBarcodeCount = 0;
    public $barcodeMatches = [];
    public $uploadResults = [];
    public $unmatchedBarcodes = [];
    public $validBarcodes = [];
    public $invalidBarcodes = [];
    public $similarBarcodes = [];

    // Audit-specific properties
    public $auditResults = [];
    public $missingItems = [];      // Allocated but not scanned
    public $extraItems = [];        // Scanned but not allocated
    public $quantityVariances = []; // Quantity mismatches
    public $auditDate = null;
    public ?int $existingAuditIdForDay = null;
    public ?string $existingAuditCreatedAtForDay = null;
    public ?string $existingAuditDay = null;

    // Inline editing properties
    public $editingShipmentId = null;
    public $editingAllocatedQuantity = 0;

    // Modal tab properties
    public $activeHistoryTab = 'upload_history';

    /**
     * Set the active history tab
     */
    public function setActiveHistoryTab($tab)
    {
        $this->activeHistoryTab = $tab;
    }

    public function mount()
    {
        $this->loadBatches();
        $this->loadAgents();
    }

    /**
     * Load all batches with branch and shipment counts
     */
    protected function loadBatches()
    {
        // Get unique batch values from branches that have completed shipments
        $batchesWithShipments = Branch::whereHas('branchAllocations.shipments', function ($q) {
            $q->where('shipping_status', 'completed');
        })
        ->distinct()
        ->pluck('batch')
        ->filter()
        ->sort()
        ->values();

        $this->batches = $batchesWithShipments->map(function ($batchName) {
            $branchCount = Branch::where('batch', $batchName)
                ->whereHas('branchAllocations.shipments', function ($q) {
                    $q->where('shipping_status', 'completed');
                })
                ->count();

            $totalShipments = Shipment::whereHas('branchAllocation.branch', function ($q) use ($batchName) {
                $q->where('batch', $batchName);
            })
            ->where('shipping_status', 'completed')
            ->count();

            return [
                'name' => $batchName,
                'branch_count' => $branchCount,
                'total_shipments' => $totalShipments,
                'last_shipment_date' => $this->getLastShipmentDateForBatch($batchName),
            ];
        });
    }

    /**
     * Load all available agents
     */
    protected function loadAgents()
    {
        $this->availableAgents = \App\Models\Agent::orderBy('name')->get();
    }

    /**
     * Get the last shipment date for a batch
     */
    protected function getLastShipmentDateForBatch($batchName)
    {
        $lastShipment = Shipment::whereHas('branchAllocation.branch', function ($q) use ($batchName) {
            $q->where('batch', $batchName);
        })
        ->where('shipping_status', 'completed')
        ->latest('updated_at')
        ->first();

        return $lastShipment ? $lastShipment->updated_at->format('M d, Y') : null;
    }

    /**
     * Get the last shipment date for a branch
     */
    protected function getLastShipmentDate($branchId)
    {
        $lastShipment = Shipment::whereHas('branchAllocation', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })
        ->where('shipping_status', 'completed')
        ->latest('updated_at')
        ->first();

        return $lastShipment ? $lastShipment->updated_at->format('M d, Y') : null;
    }

    /**
     * Select a batch to view its branches
     */
    public function selectBatch($batchName)
    {
        $this->selectedBatch = $batchName;
        $this->selectedBranchId = null;
        $this->branchProducts = [];
        $this->loadBatchBranches();
    }

    /**
     * Load branches for the selected batch
     */
    protected function loadBatchBranches()
    {
        if (!$this->selectedBatch) return;

        $this->batchBranches = Branch::where('batch', $this->selectedBatch)
            ->whereHas('branchAllocations.shipments', function ($q) {
                $q->where('shipping_status', 'completed');
            })
            ->withCount([
                'branchAllocations as completed_shipments_count' => function ($query) {
                    $query->whereHas('shipments', function ($q) {
                        $q->where('shipping_status', 'completed');
                    });
                }
            ])
            ->get()
            ->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'code' => $branch->code,
                    'address' => $branch->address,
                    'completed_shipments_count' => $branch->completed_shipments_count,
                    'last_shipment_date' => $this->getLastShipmentDate($branch->id),
                ];
            });
    }

    /**
     * Select a branch to view its products
     */
    public function selectBranch($branchId)
    {
        $this->selectedBranchId = $branchId;
        $this->loadBranchProducts();
    }

    /**
     * Load products for the selected branch
     */
    protected function loadBranchProducts()
    {
        if (!$this->selectedBranchId) return;

        // Get all branch allocation items for completed shipments in this branch
        $items = \App\Models\BranchAllocationItem::with([
            'product',
            'branchAllocation.shipments',
            'branchAllocation.batchAllocation'
        ])
        ->whereHas('branchAllocation', function ($q) {
            $q->where('branch_id', $this->selectedBranchId);
        })
        ->whereHas('branchAllocation.shipments', function ($q) {
            $q->where('shipping_status', 'completed');
        })
        ->where('box_id', null) // Only unpacked items
        ->get();

        // Group by product_id
        $groupedProducts = $items->groupBy('product_id');

        $this->branchProducts = $groupedProducts->map(function ($productItems, $productId) {
            $product = $productItems->first()->product;
            $totalQuantity = $productItems->sum('quantity');
            $totalSold = $productItems->sum('sold_quantity');

            // Collect shipment details for this product
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
                'barcode' => $productItems->first()->getDisplayBarcodeAttribute(),
                'sku' => $productItems->first()->getDisplaySkuAttribute(),
                'total_quantity' => $totalQuantity,
                'total_sold' => $totalSold,
                'remaining_quantity' => $totalQuantity - $totalSold,
                'unit_price' => $productItems->first()->unit_price,
                'total_value' => $productItems->sum(function ($item) {
                    return $item->quantity * $item->unit_price;
                }),
                'image_url' => $product->getPrimaryImageAttribute() ? asset('storage/photos/' . $product->getPrimaryImageAttribute()) : null,
                'shipments' => $shipments,
            ];
        })->values();

        // Add active promos count to each product
        $this->branchProducts = $this->branchProducts->map(function ($product) {
            $productId = $product['id'];
            $batchAllocationIds = collect($product['shipments'])->pluck('batch_allocation_id')->filter()->unique();

            $activePromosCount = Promo::where('product', 'like', '%' . (string)$productId . '%')
                ->where(function($q) use ($batchAllocationIds) {
                    $q->where(function($sub) use ($batchAllocationIds) {
                        foreach ($batchAllocationIds as $id) {
                            $sub->orWhere('branch', 'like', '%' . (string)$id . '%');
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


    /**
     * Get status for an allocation item
     */
    protected function getItemStatus($item)
    {
        // Calculate total scanned quantity for this product across all boxes
        $totalScanned = \App\Models\BranchAllocationItem::where('branch_allocation_id', $item->branch_allocation_id)
            ->where('product_id', $item->product_id)
            ->whereNotNull('box_id')
            ->sum('scanned_quantity');

        $allocated = $item->quantity;

        if ($totalScanned >= $allocated) {
            return 'received';
        } elseif ($totalScanned > 0) {
            return 'partial';
        } else {
            return 'pending';
        }
    }

    /**
     * View product details
     */
    public function viewProductDetails($productId)
    {
        $product = collect($this->branchProducts)->firstWhere('id', $productId);

        if ($product) {
            $this->selectedProductDetails = $product;
            $this->showProductViewModal = true;
        }
    }

    /**
     * Close product view modal
     */
    public function closeProductViewModal()
    {
        $this->showProductViewModal = false;
        $this->selectedProductDetails = null;
        $this->editingShipmentId = null;
        $this->editingAllocatedQuantity = 0;
        $this->activeHistoryTab = 'upload_history';
    }

    /**
     * Start editing allocated quantity for a shipment
     */
    public function startEditingQuantity($shipmentId, $currentQuantity)
    {
        $this->editingShipmentId = $shipmentId;
        $this->editingAllocatedQuantity = $currentQuantity;
    }

    /**
     * Cancel editing allocated quantity
     */
    public function cancelEditingQuantity()
    {
        $this->editingShipmentId = null;
        $this->editingAllocatedQuantity = 0;
    }

    /**
     * Save the edited allocated quantity
     */
    public function saveAllocatedQuantity()
    {
        if (!$this->editingShipmentId || !$this->selectedProductDetails) {
            return;
        }

        $this->validate([
            'editingAllocatedQuantity' => 'required|integer|min:0',
        ]);

        try {
            // Find the BranchAllocationItem for this shipment and product
            $productId = $this->selectedProductDetails['id'];
            $shipment = collect($this->selectedProductDetails['shipments'])->firstWhere('id', $this->editingShipmentId);

            if (!$shipment) {
                session()->flash('error', 'Shipment not found.');
                return;
            }

            // Find the BranchAllocationItem
            $allocationItem = BranchAllocationItem::whereHas('branchAllocation.shipments', function($q) use ($shipment) {
                $q->where('id', $shipment['id']);
            })
            ->where('product_id', $productId)
            ->where('box_id', null) // Only unpacked items
            ->first();

            if (!$allocationItem) {
                session()->flash('error', 'Allocation item not found.');
                return;
            }

            // Check if new quantity is less than sold quantity
            if ($this->editingAllocatedQuantity < $allocationItem->sold_quantity) {
                session()->flash('error', 'Cannot reduce allocated quantity below sold quantity (' . $allocationItem->sold_quantity . ').');
                return;
            }

            // Store old quantity before update
            $oldQuantity = $allocationItem->quantity;

            // Update the quantity
            $allocationItem->update([
                'quantity' => $this->editingAllocatedQuantity
            ]);

            // Log activity
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
                    'branch_id' => $this->selectedBranchId,
                ],
            ]);

            // Reset editing state
            $this->editingShipmentId = null;
            $this->editingAllocatedQuantity = 0;

            // Refresh data
            $this->loadBranchProducts();
            $this->viewProductDetails($productId); // Refresh the modal data

            session()->flash('message', 'Allocated quantity updated successfully.');

        } catch (\Exception $e) {
            session()->flash('error', 'Error updating quantity: ' . $e->getMessage());
        }
    }

    /**
     * Clear batch selection
     */
    public function clearBatchSelection()
    {
        $this->selectedBatch = null;
        $this->selectedBranchId = null;
        $this->batchBranches = [];
        $this->branchProducts = [];
        $this->selectedProductDetails = null;
        $this->showProductViewModal = false;
    }

    /**
     * Clear branch selection (within batch)
     */
    public function clearBranchSelection()
    {
        $this->selectedBranchId = null;
        $this->branchProducts = [];
        $this->selectedProductDetails = null;
        $this->showProductViewModal = false;
    }

    /**
     * Clear all filters
     */
    public function clearFilters()
    {
        $this->search = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->statusFilter = '';
        if ($this->selectedBranchId) {
            $this->loadBranchProducts();
        }
    }

    /**
     * Refresh data
     */
    public function refreshData()
    {
        $this->loadBatches();
        if ($this->selectedBatch) {
            $this->loadBatchBranches();
            if ($this->selectedBranchId) {
                $this->loadBranchProducts();
            }
        }
    }

    /**
     * Update hook to reload products when filters change
     */
    public function updated($property)
    {
        if (in_array($property, ['search', 'dateFrom', 'dateTo', 'statusFilter']) && $this->selectedBranchId) {
            $this->loadBranchProducts();
        }
    }

    /**
     * Open the upload modal
     */
    public function openUploadModal()
    {
        $this->showUploadModal = true;
    }

    /**
     * Close the upload modal
     */
    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->textFile = null;
    }

    /**
     * Save audit results to database
     */
    public function saveAuditResults()
    {
        if (!$this->selectedBranchId || empty($this->auditResults)) {
            return;
        }

        try {
            // Prevent duplicates: only one audit per branch per calendar day
            $auditDay = $this->auditDate ? $this->auditDate->toDateString() : now()->toDateString();
            $existing = Activity::query()
                ->where('log_name', 'inventory_audit')
                ->where('subject_type', Branch::class)
                ->where('subject_id', $this->selectedBranchId)
                ->whereDate('created_at', $auditDay)
                ->latest('created_at')
                ->first();

            if ($existing) {
                $this->existingAuditIdForDay = $existing->id;
                $this->existingAuditCreatedAtForDay = optional($existing->created_at)->toDateTimeString();
                $this->existingAuditDay = $auditDay;

                $this->addError(
                    'audit',
                    "An audit is already saved for this branch on {$auditDay} (saved at {$this->existingAuditCreatedAtForDay})."
                );

                return;
            }

            // Save audit record to activity logs
            Activity::create([
                'log_name' => 'inventory_audit',
                'description' => "Inventory audit for branch {$this->selectedBranchId}",
                'subject_type' => Branch::class,
                'subject_id' => $this->selectedBranchId,
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

            // Close modal and refresh data
            $this->showResultsModal = false;
            $this->loadBranchProducts();

            // Prepare success message based on audit findings
            $hasVariances = count($this->missingItems) > 0 || count($this->extraItems) > 0 || count($this->quantityVariances) > 0;
            if ($hasVariances) {
                $this->successMessage = "Audit saved successfully! Found " . 
                    (count($this->missingItems) + count($this->extraItems) + count($this->quantityVariances)) . 
                    " variance(s).";
            } else {
                $this->successMessage = "Audit saved successfully! No variances found - inventory matches allocation.";
            }

            $this->showSuccessModal = true;

        } catch (\Exception $e) {
            $this->addError('audit', 'Error saving audit: ' . $e->getMessage());
        }
    }

    /**
     * View today's audit for the selected branch
     */
    public function viewTodaysAudit()
    {
        if (!$this->selectedBranchId || !$this->existingAuditIdForDay) {
            return;
        }

        // Redirect to reports page with filters
        return redirect()->route('reports.branch-inventory', [
            'selectedBranch' => $this->selectedBranchId,
            'dateFrom' => $this->existingAuditDay,
            'dateTo' => $this->existingAuditDay,
        ]);
    }

    /**
     * Close the results modal
     */
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

    /**
     * Close the success modal
     */
    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;
        $this->successMessage = '';
    }

    /**
     * Process the uploaded text file
     */
    public function processTextFile()
    {
        $this->validate([
            'textFile' => 'required|file|mimes:txt|max:1024',
        ]);

        // Reset previous results
        $this->similarBarcodes = [];

        try {
            // Read the file content
            $content = file_get_contents($this->textFile->getRealPath());
            $barcodes = $this->parseBarcodeFile($content);

            // Process the barcodes and compare with current products
            $this->processBarcodeComparison($barcodes);

            // Close upload modal and show results
            $this->showUploadModal = false;
            $this->showResultsModal = true;

        } catch (\Exception $e) {
            $this->addError('textFile', 'Error processing file: ' . $e->getMessage());
        }
    }

    /**
     * Parse the text file content to extract barcodes
     */
    protected function parseBarcodeFile($content)
    {
        // Split by new lines and filter out empty lines
        $lines = explode("\n", $content);
        $barcodes = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $barcodes[] = $line;
            }
        }

        $this->uploadedBarcodeCount = count($barcodes);
        return $barcodes;
    }

    /**
     * Process barcode comparison for inventory audit
     * Detects variances: missing items, extra items, and quantity mismatches
     */
    protected function processBarcodeComparison($uploadedBarcodes)
    {
        if (!$this->selectedBranchId) {
            return;
        }

        // Get all allocated products for this branch from BranchAllocationItem
        $allocatedItems = BranchAllocationItem::with('product')
            ->whereHas('branchAllocation', function ($q) {
                $q->where('branch_id', $this->selectedBranchId);
            })
            ->whereHas('branchAllocation.shipments', function ($q) {
                $q->where('shipping_status', 'completed');
            })
            ->where('box_id', null) // Only unpacked items
            ->get();

        // Build allocated products map by barcode
        $allocatedProducts = [];
        foreach ($allocatedItems as $item) {
            $barcode = $item->getDisplayBarcodeAttribute();
            if (!empty($barcode) && $barcode !== 'N/A') {
                if (!isset($allocatedProducts[$barcode])) {
                    $allocatedProducts[$barcode] = [
                        'product_id' => $item->product_id,
                        'product_name' => $item->getDisplayNameAttribute(),
                        'sku' => $item->getDisplaySkuAttribute(),
                        'allocated_quantity' => 0,
                    ];
                }
                $allocatedProducts[$barcode]['allocated_quantity'] += $item->quantity;
            }
        }

        // Count scanned barcodes
        $scannedBarcodeCount = array_count_values($uploadedBarcodes);
        $this->uploadedBarcodeCount = count($uploadedBarcodes);

        // Initialize audit results
        $this->missingItems = [];
        $this->extraItems = [];
        $this->quantityVariances = [];
        $this->auditDate = now();

        // Detect missing items (allocated but not scanned)
        foreach ($allocatedProducts as $barcode => $product) {
            $scannedCount = $scannedBarcodeCount[$barcode] ?? 0;
            
            if ($scannedCount == 0) {
                // Missing item - allocated but not scanned
                $this->missingItems[] = [
                    'barcode' => $barcode,
                    'product_name' => $product['product_name'],
                    'sku' => $product['sku'],
                    'allocated_quantity' => $product['allocated_quantity'],
                    'scanned_quantity' => 0,
                    'variance' => -$product['allocated_quantity'],
                ];
            } elseif ($scannedCount != $product['allocated_quantity']) {
                // Quantity variance - scanned count doesn't match allocated
                $this->quantityVariances[] = [
                    'barcode' => $barcode,
                    'product_name' => $product['product_name'],
                    'sku' => $product['sku'],
                    'allocated_quantity' => $product['allocated_quantity'],
                    'scanned_quantity' => $scannedCount,
                    'variance' => $scannedCount - $product['allocated_quantity'],
                ];
            }
        }

        // Detect extra items (scanned but not allocated to this branch)
        foreach ($scannedBarcodeCount as $barcode => $scannedCount) {
            if (!isset($allocatedProducts[$barcode])) {
                // Extra item - scanned but not allocated
                $this->extraItems[] = [
                    'barcode' => $barcode,
                    'scanned_quantity' => $scannedCount,
                ];
            }
        }

        // Build audit results summary
        $this->auditResults = [
            'total_allocated' => array_sum(array_column($allocatedProducts, 'allocated_quantity')),
            'total_scanned' => array_sum($scannedBarcodeCount),
            'total_products_allocated' => count($allocatedProducts),
            'total_products_scanned' => count($scannedBarcodeCount),
            'missing_items_count' => count($this->missingItems),
            'extra_items_count' => count($this->extraItems),
            'quantity_variances_count' => count($this->quantityVariances),
        ];

        // Keep matched count for UI compatibility
        $this->matchedBarcodeCount = count($allocatedProducts) - count($this->missingItems);
        $this->barcodeMatches = [];
        $this->uploadResults = [];
    }

    /**
     * Open customer sales modal
     */
    public function openCustomerSalesModal()
    {
        $this->showCustomerSalesModal = true;
        $this->resetSalesModal();
    }

    /**
     * Close customer sales modal
     */
    public function closeCustomerSalesModal()
    {
        $this->showCustomerSalesModal = false;
        $this->resetSalesModal();
    }

    /**
     * Reset sales modal properties
     */
    protected function resetSalesModal()
    {
        $this->salesBarcodeInput = '';
        $this->selectedSalesProduct = null;
        $this->salesQuantity = 1;
        $this->salesItems = [];
        $this->selectedAgentId = null;
    }

    /**
     * Process sales barcode input changes
     */
    public function updatedSalesBarcodeInput()
    {
        $barcode = trim($this->salesBarcodeInput);
        if (empty($barcode)) {
            $this->selectedSalesProduct = null;
            return;
        }

        // Find product by barcode in current branch products
        $product = collect($this->branchProducts)->first(function ($product) use ($barcode) {
            return $product['barcode'] === $barcode;
        });

        if ($product) {
            $this->selectedSalesProduct = $product;
        } else {
            $this->selectedSalesProduct = null;
        }
    }

    /**
     * Add sales item to the transaction
     */
    public function addSalesItem()
    {
        if (!$this->selectedSalesProduct || !$this->salesQuantity || $this->salesQuantity < 1) {
            return;
        }

        // Check if quantity exceeds available
        if ($this->salesQuantity > $this->selectedSalesProduct['remaining_quantity']) {
            session()->flash('error', 'Quantity exceeds available stock.');
            return;
        }

        $item = [
            'id' => $this->selectedSalesProduct['id'],
            'name' => $this->selectedSalesProduct['name'],
            'barcode' => $this->selectedSalesProduct['barcode'],
            'quantity' => $this->salesQuantity,
            'unit_price' => $this->selectedSalesProduct['unit_price'],
            'total' => $this->salesQuantity * $this->selectedSalesProduct['unit_price'],
        ];

        $this->salesItems[] = $item;

        // Reset for next item
        $this->salesBarcodeInput = '';
        $this->selectedSalesProduct = null;
        $this->salesQuantity = 1;
    }

    /**
     * Remove sales item from transaction
     */
    public function removeSalesItem($index)
    {
        if (isset($this->salesItems[$index])) {
            unset($this->salesItems[$index]);
            $this->salesItems = array_values($this->salesItems); // Reindex array
        }
    }

    /**
     * Clear all sales items
     */
    public function clearSalesItems()
    {
        $this->salesItems = [];
    }

    /**
     * Save customer sales transaction
     */
    public function saveCustomerSales()
    {
        if (empty($this->salesItems)) {
            return;
        }

        try {
            // Process each sales item
            foreach ($this->salesItems as $item) {
                // Find and update the corresponding branch allocation items
                $allocationItems = BranchAllocationItem::whereHas('product', function($q) use ($item) {
                    $q->where('id', $item['id']);
                })
                ->whereHas('branchAllocation', function($q) {
                    $q->where('branch_id', $this->selectedBranchId);
                })
                ->whereHas('branchAllocation.shipments', function ($q) {
                    $q->where('shipping_status', 'completed');
                })
                ->where('box_id', null) // Only unpacked items
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

            // Log activity
            $agent = $this->selectedAgentId ? \App\Models\Agent::find($this->selectedAgentId) : null;
            foreach ($this->salesItems as $item) {
                Activity::create([
                    'log_name' => 'branch_inventory',
                    'description' => "Customer sale recorded for product {$item['barcode']} in branch {$this->selectedBranchId}" . ($agent ? " by agent {$agent->name}" : ""),
                    'subject_type' => BranchAllocationItem::class,
                    'subject_id' => null,
                    'causer_type' => null,
                    'causer_id' => null,
                    'properties' => [
                        'product_id' => $item['id'],
                        'product_name' => $item['name'],
                        'barcode' => $item['barcode'],
                        'quantity_sold' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_amount' => $item['total'],
                        'branch_id' => $this->selectedBranchId,
                        'agent_id' => $this->selectedAgentId,
                        'agent_name' => $agent ? $agent->name : null,
                        'customer_sale' => true,
                    ],
                ]);
            }

            // Refresh data
            $this->loadBranchProducts();

            // Close modal and show success
            $this->closeCustomerSalesModal();
            $this->successMessage = 'Customer sales recorded successfully!';
            $this->showSuccessModal = true;

        } catch (\Exception $e) {
            session()->flash('error', 'Error recording sales: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.branch.branch-inventory');
    }
}