<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Branch;
use App\Models\Shipment;
use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;

class BranchInventory extends Component
{
    use WithFileUploads;
    // Batch selection
    public $selectedBatch = null;
    public $batches = [];

    // Branch selection within batch
    public $selectedBranchId = null;
    public $batchBranches = [];

    // Shipment data for selected branch
    public $branchShipments = [];
    public $selectedShipmentDetails = null;

    // Search and filters
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $statusFilter = '';

    // Modal controls
    public $showShipmentDetailsModal = false;
    public $showUploadModal = false;
    public $showResultsModal = false;
    public $showSuccessModal = false;
    public $successMessage = '';

    // File upload properties
    public $textFile;
    public $uploadedBarcodeCount = 0;
    public $matchedBarcodeCount = 0;
    public $barcodeMatches = [];
    public $uploadResults = [];
    public $unmatchedBarcodes = [];
    public $validBarcodes = [];
    public $invalidBarcodes = [];

    public function mount()
    {
        $this->loadBatches();
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
        $this->branchShipments = [];
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
     * Select a branch to view its shipments
     */
    public function selectBranch($branchId)
    {
        $this->selectedBranchId = $branchId;
        $this->loadBranchShipments();
    }

    /**
     * Load shipments for the selected branch
     */
    protected function loadBranchShipments()
    {
        if (!$this->selectedBranchId) return;

        $query = Shipment::with([
            'branchAllocation.branch',
            'branchAllocation.items.product',
            'branchAllocation.batchAllocation'
        ])
        ->whereHas('branchAllocation', function ($q) {
            $q->where('branch_id', $this->selectedBranchId);
        })
        ->where('shipping_status', 'completed');

        // Apply filters
        if ($this->search) {
            $query->where('shipping_plan_num', 'like', '%' . $this->search . '%');
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->statusFilter) {
            $query->where('shipping_status', $this->statusFilter);
        }

        $shipments = $query->orderBy('created_at', 'desc')->get();

        $this->branchShipments = $shipments->map(function ($shipment) {
            $allocation = $shipment->branchAllocation;
            $totalItems = $allocation ? $allocation->items->where('box_id', null)->sum('quantity') : 0;
            $totalValue = $allocation ? $allocation->items->where('box_id', null)->sum(function ($item) {
                return $item->quantity * $item->getDisplayPriceAttribute();
            }) : 0;

            return [
                'id' => $shipment->id,
                'shipping_plan_num' => $shipment->shipping_plan_num,
                'shipment_date' => $shipment->created_at->format('M d, Y'),
                'carrier_name' => $shipment->carrier_name ?: 'N/A',
                'delivery_method' => $shipment->delivery_method ?: 'N/A',
                'status' => $shipment->shipping_status,
                'allocations' => $this->formatAllocationsForShipment($shipment),
                'total_items' => $totalItems,
                'total_value' => $totalValue,
            ];
        });
    }

    /**
     * Format allocations data for a shipment
     */
    protected function formatAllocationsForShipment($shipment)
    {
        $allocation = $shipment->branchAllocation;

        if (!$allocation) return [];

        return [
            [
                'id' => $allocation->id,
                'reference' => $allocation->batchAllocation->ref_no ?? 'N/A',
                'created_date' => $allocation->created_at->format('M d, Y'),
                'created_by' => 'System', // Default since no user relationship
                'products' => $allocation->items->where('box_id', null)->map(function ($item) {
                    $product = $item->product;
                    return [
                        'id' => $item->id,
                        'name' => $item->getDisplayNameAttribute(),
                        'barcode' => $product->barcode ?? 'N/A',
                        'sku' => $item->getDisplaySkuAttribute(),
                        'quantity' => $item->quantity,
                        'quantity_sold' => $item->sold_quantity,
                        'price' => $item->getDisplayPriceAttribute(),
                        'total' => $item->quantity * $item->getDisplayPriceAttribute(),
                        'status' => $this->getItemStatus($item),
                        'image_url' => $product->getPrimaryImageAttribute() ? asset('storage/photos/' . $product->getPrimaryImageAttribute()) : null,
                    ];
                }),
                'total_products' => $allocation->items->where('box_id', null)->count(),
                'total_quantity' => $allocation->items->where('box_id', null)->sum('quantity'),
            ]
        ];
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
     * View shipment details
     */
    public function viewShipmentDetails($shipmentId)
    {
        $shipment = collect($this->branchShipments)->firstWhere('id', $shipmentId);

        if ($shipment) {
            $this->selectedShipmentDetails = $shipment;
            $this->showShipmentDetailsModal = true;
        }
    }

    /**
     * Close shipment details modal
     */
    public function closeShipmentDetailsModal()
    {
        $this->showShipmentDetailsModal = false;
        $this->selectedShipmentDetails = null;
    }

    /**
     * Clear batch selection
     */
    public function clearBatchSelection()
    {
        $this->selectedBatch = null;
        $this->selectedBranchId = null;
        $this->batchBranches = [];
        $this->branchShipments = [];
        $this->selectedShipmentDetails = null;
        $this->showShipmentDetailsModal = false;
    }

    /**
     * Clear branch selection (within batch)
     */
    public function clearBranchSelection()
    {
        $this->selectedBranchId = null;
        $this->branchShipments = [];
        $this->selectedShipmentDetails = null;
        $this->showShipmentDetailsModal = false;
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
            $this->loadBranchShipments();
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
                $this->loadBranchShipments();
            }
        }
    }

    /**
     * Update hook to reload shipments when filters change
     */
    public function updated($property)
    {
        if (in_array($property, ['search', 'dateFrom', 'dateTo', 'statusFilter']) && $this->selectedBranchId) {
            $this->loadBranchShipments();
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
                $items = \App\Models\BranchAllocationItem::whereHas('product', function($q) use ($barcode) {
                    $q->where('barcode', $barcode);
                })
                ->whereHas('branchAllocation', function($q) {
                    $q->where('branch_id', $this->selectedBranchId);
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
            $this->loadBranchShipments();

            // Prepare success message
            $savedCount = count($this->validBarcodes);
            $skippedCount = count($this->invalidBarcodes);
            $message = "{$savedCount} barcode(s) saved successfully!";

            if ($skippedCount > 0) {
                $message .= " {$skippedCount} barcode(s) were skipped due to quantity limits.";
            }

            $this->successMessage = $message;
            $this->showSuccessModal = true;

        } catch (\Exception $e) {
            $this->addError('save', 'Error saving data: ' . $e->getMessage());
        }
    }

    /**
     * Close the results modal
     */
    public function closeResultsModal()
    {
        $this->showResultsModal = false;
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
        // Get all products from current branch shipments
        $allProducts = [];
        foreach ($this->branchShipments as $shipment) {
            foreach ($shipment['allocations'] as $allocation) {
                foreach ($allocation['products'] as $product) {
                    if (!empty($product['barcode']) && $product['barcode'] !== 'N/A') {
                        $allProducts[$product['barcode']] = [
                            'name' => $product['name'],
                            'sku' => $product['sku'],
                            'quantity' => $product['quantity'],
                            'quantity_sold' => 0,
                        ];
                    }
                }
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

        // Validate matched barcodes for quantity limits
        $this->validateBarcodeQuantities($matches);

        // Update the quantity sold in the branch shipments data
        $this->updateQuantitySoldInShipments($matches);
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
                ->whereHas('product', function($q) use ($barcode) {
                    $q->where('barcode', $barcode);
                })
                ->whereHas('branchAllocation', function($q) {
                    $q->where('branch_id', $this->selectedBranchId);
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
     * Update quantity sold in the shipments data
     */
    protected function updateQuantitySoldInShipments($matches)
    {
        foreach ($this->branchShipments as &$shipment) {
            foreach ($shipment['allocations'] as &$allocation) {
                foreach ($allocation['products'] as &$product) {
                    if (!empty($product['barcode']) && $product['barcode'] !== 'N/A' && isset($matches[$product['barcode']])) {
                        $product['quantity_sold'] += $matches[$product['barcode']]['quantity_sold'];
                    }
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.pages.branch.branch-inventory');
    }
}