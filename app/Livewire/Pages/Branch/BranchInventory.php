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
     * Save matched barcodes to database
     */
    public function saveMatchedBarcodesToDatabase()
    {
        if (!$this->selectedBranchId || empty($this->validBarcodes)) {
            return;
        }

        try {
            // Save only the valid barcodes, distributing the quantity across available items
            foreach ($this->validBarcodes as $barcode => $result) {
                $items = \App\Models\BranchAllocationItem::where(function($q) use ($barcode) {
                    $q->where('product_snapshot_barcode', $barcode)
                      ->orWhereHas('product', function($sub) use ($barcode) {
                          $sub->where('barcode', $barcode);
                      });
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

                $remaining = $result['quantity_sold'];
                foreach ($items as $item) {
                    $available = $item->quantity - $item->sold_quantity;
                    if ($available > 0 && $remaining > 0) {
                        $increment = min($remaining, $available);
                        $item->increment('sold_quantity', $increment);
                        $remaining -= $increment;
                    }
                    if ($remaining <= 0) break;
                }
            }

            // Close modal and refresh data
            $this->showResultsModal = false;
            $this->loadBranchProducts();

            // Prepare success message
            $savedCount = count($this->validBarcodes);
            $skippedCount = count($this->invalidBarcodes);
            $message = "{$savedCount} barcode(s) saved successfully!";

            if ($skippedCount > 0) {
                $message .= " {$skippedCount} barcode(s) were skipped due to quantity limits.";
            }

            // Log activity for each updated product
            foreach ($this->validBarcodes as $barcode => $result) {
                Activity::create([
                    'log_name' => 'branch_inventory',
                    'description' => "Updated quantity sold for product {$barcode} in branch {$this->selectedBranchId}",
                    'subject_type' => BranchAllocationItem::class,
                    'subject_id' => null, // Since multiple items
                    'causer_type' => null, // No user
                    'causer_id' => null,
                    'properties' => [
                        'barcode' => $barcode,
                        'product_name' => $result['product_name'],
                        'quantity_sold' => $result['quantity_sold'],
                        'branch_id' => $this->selectedBranchId,
                        'uploaded_file' => true,
                    ],
                ]);
            }

            $this->successMessage = $message;
            $this->showSuccessModal = true;

        } catch (\Exception $e) {
            $this->addError('save', 'Error saving data: ' . $e->getMessage());
        }
    }

    /**
     * Sync similar barcodes to database
     */
    public function syncSimilarBarcodes()
    {
        if (!$this->selectedBranchId || empty($this->similarBarcodes)) {
            return;
        }

        try {
            foreach ($this->similarBarcodes as $item) {
                $barcode = $item['existing_barcode'];
                $quantitySold = $item['quantity_sold'];

                $items = \App\Models\BranchAllocationItem::where(function($q) use ($barcode) {
                    $q->where('product_snapshot_barcode', $barcode)
                      ->orWhereHas('product', function($sub) use ($barcode) {
                          $sub->where('barcode', $barcode);
                      });
                })
                ->whereHas('branchAllocation', function($q) {
                    $q->where('branch_id', $this->selectedBranchId);
                })
                ->whereHas('branchAllocation.shipments', function ($q) {
                    $q->where('shipping_status', 'completed');
                })
                ->where('box_id', null)
                ->orderBy('id')
                ->get();

                $remaining = $quantitySold;
                foreach ($items as $item) {
                    $available = $item->quantity - $item->sold_quantity;
                    if ($available > 0 && $remaining > 0) {
                        $increment = min($remaining, $available);
                        $item->increment('sold_quantity', $increment);
                        $remaining -= $increment;
                    }
                    if ($remaining <= 0) break;
                }
            }

            $this->showResultsModal = false;
            $this->loadBranchProducts();

            // Log activity for each synced product
            foreach ($this->similarBarcodes as $item) {
                Activity::create([
                    'log_name' => 'branch_inventory',
                    'description' => "Synced similar barcode {$item['uploaded_barcode']} to {$item['existing_barcode']} in branch {$this->selectedBranchId}",
                    'subject_type' => BranchAllocationItem::class,
                    'subject_id' => null, // Since multiple items
                    'causer_type' => null, // No user
                    'causer_id' => null,
                    'properties' => [
                        'barcode' => $item['existing_barcode'],
                        'uploaded_barcode' => $item['uploaded_barcode'],
                        'product_name' => $item['product_name'],
                        'quantity_sold' => $item['quantity_sold'],
                        'branch_id' => $this->selectedBranchId,
                        'uploaded_file' => true,
                        'synced_similar' => true,
                    ],
                ]);
            }

            $syncedCount = count($this->similarBarcodes);
            $this->successMessage = "{$syncedCount} similar barcode(s) synced successfully!";
            $this->showSuccessModal = true;

        } catch (\Exception $e) {
            $this->addError('sync', 'Error syncing data: ' . $e->getMessage());
        }
    }

    /**
     * Close the results modal
     */
    public function closeResultsModal()
    {
        $this->showResultsModal = false;
        $this->similarBarcodes = [];
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
     * Process barcode comparison with current branch products
     */
    protected function processBarcodeComparison($uploadedBarcodes)
    {
        // Get all products from current branch products
        $allProducts = [];
        foreach ($this->branchProducts as $product) {
            if (!empty($product['barcode']) && $product['barcode'] !== 'N/A') {
                $allProducts[$product['barcode']] = [
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'quantity' => $product['total_quantity'],
                    'quantity_sold' => 0,
                ];
            }
        }

        // Count matches for each barcode
        $barcodeCount = array_count_values($uploadedBarcodes);
        $matches = [];

        foreach ($barcodeCount as $barcode => $count) {
            if (isset($allProducts[$barcode])) {
                $matches[$barcode] = [
                    'product_name' => $allProducts[$barcode]['name'],
                    'sku' => $allProducts[$barcode]['sku'],
                    'quantity_sold' => $count,
                    'available_quantity' => $allProducts[$barcode]['quantity'],
                ];
            }
        }

        $this->matchedBarcodeCount = count($matches);
        $this->barcodeMatches = $matches;
        $this->uploadResults = $matches;

        // Collect unmatched barcodes
        $this->unmatchedBarcodes = [];
        foreach ($barcodeCount as $barcode => $count) {
            if (!isset($matches[$barcode])) {
                $this->unmatchedBarcodes[$barcode] = $count;
            }
        }

        // Find similar barcodes (same first 10 digits, different last 6 digits)
        $this->similarBarcodes = [];
        foreach ($this->unmatchedBarcodes as $uploadedBarcode => $count) {
            $prefix = substr($uploadedBarcode, 0, 10);
            $suffix = substr($uploadedBarcode, 10);
            foreach (array_keys($allProducts) as $existingBarcode) {
                $existingPrefix = substr($existingBarcode, 0, 10);
                $existingSuffix = substr($existingBarcode, 10);
                if ($prefix === $existingPrefix && $suffix !== $existingSuffix) {
                    $this->similarBarcodes[] = [
                        'uploaded_barcode' => $uploadedBarcode,
                        'existing_barcode' => $existingBarcode,
                        'product_name' => $allProducts[$existingBarcode]['name'],
                        'sku' => $allProducts[$existingBarcode]['sku'],
                        'quantity_sold' => $count,
                    ];
                    break; // One match per uploaded barcode
                }
            }
        }

        // Validate matched barcodes for quantity limits
        $this->validateBarcodeQuantities($matches);

        // Update the quantity sold in the branch products data
        $this->updateQuantitySoldInProducts($matches);
    }

    /**
     * Validate barcode quantities against available inventory
     */
    protected function validateBarcodeQuantities($matches)
    {
        $this->validBarcodes = [];
        $this->invalidBarcodes = [];

        foreach ($matches as $barcode => $result) {
            $items = \App\Models\BranchAllocationItem::with('product')
                ->where(function($q) use ($barcode) {
                    $q->where('product_snapshot_barcode', $barcode)
                      ->orWhereHas('product', function($sub) use ($barcode) {
                          $sub->where('barcode', $barcode);
                      });
                })
                ->whereHas('branchAllocation', function($q) {
                    $q->where('branch_id', $this->selectedBranchId);
                })
                ->whereHas('branchAllocation.shipments', function ($q) {
                    $q->where('shipping_status', 'completed');
                })
                ->where('box_id', null) // Only unpacked items
                ->get();

            $totalAllocated = $items->sum('quantity');
            $totalAlreadySold = $items->sum('sold_quantity');
            $newSold = $result['quantity_sold'];

            if ($totalAlreadySold + $newSold <= $totalAllocated) {
                $this->validBarcodes[$barcode] = $result;
            } else {
                $this->invalidBarcodes[$barcode] = [
                    'quantity_sold' => $newSold,
                    'available_quantity' => $totalAllocated - $totalAlreadySold,
                    'already_sold' => $totalAlreadySold,
                    'product_name' => $result['product_name'],
                    'sku' => $result['sku']
                ];
            }
        }
    }

    /**
     * Update quantity sold in the products data
     */
    protected function updateQuantitySoldInProducts($matches)
    {
        foreach ($this->branchProducts as &$product) {
            if (!empty($product['barcode']) && $product['barcode'] !== 'N/A' && isset($matches[$product['barcode']])) {
                $product['total_sold'] += $matches[$product['barcode']]['quantity_sold'];
                $product['remaining_quantity'] = $product['total_quantity'] - $product['total_sold'];
            }
        }
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