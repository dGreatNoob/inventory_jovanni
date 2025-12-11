<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Shipment;
use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;

class BranchInventory extends Component
{
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

    public function render()
    {
        return view('livewire.pages.branch.branch-inventory');
    }
}