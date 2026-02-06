<?php

namespace App\Livewire\Pages\Allocation;

use App\Models\Branch;
use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;
use App\Models\Box;
use App\Models\DeliveryReceipt;
use App\Models\Product;
use App\Support\ProductSearchHelper;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
#[Title('Allocation - Packing / Scan')]
class Scanning extends Component
{
    /** @var int|null Selected branch ID (scanners only care about branch) */
    public ?int $selectedBranchId = null;
    /** @var int|null Resolved branch_allocation_id (from most recent draft for this branch) */
    public ?int $selectedBranchAllocationId = null;
    public ?int $selectedBoxId = null;

    public $barcodeInput = '';
    public $lastScannedBarcode = '';
    public $scanFeedback = '';
    /** @var string 'success'|'error'|'neutral' Explicit scan feedback status for styling */
    public string $scanStatus = 'neutral';

    public $availableBoxes = [];
    public $currentBox = null;
    public $currentDr = null;
    public $motherDr = null;

    public bool $showBranchDropdown = false;
    public string $branchSearch = '';

    /** Branches that have at least one draft allocation (for scanner selection) */
    public function getBranchesWithAllocationsProperty()
    {
        return Branch::whereHas('branchAllocations', function ($q) {
            $q->whereHas('batchAllocation', fn ($bq) => $bq->where('status', 'draft'));
        })->orderBy('name')->get();
    }

    /** Branches filtered by search (sequential token prefix match) */
    public function getFilteredBranchesProperty()
    {
        $branches = $this->branchesWithAllocations;
        if (trim($this->branchSearch) === '') {
            return $branches;
        }
        $search = trim($this->branchSearch);
        return $branches->filter(function ($b) use ($search) {
            return ProductSearchHelper::matchesAnyField($search, [
                $b->name ?? '',
                $b->code ?? '',
            ]);
        })->values();
    }

    public function selectBranch(int $branchId): void
    {
        $this->selectedBranchId = $branchId;
        $this->updatedSelectedBranchId();
        $this->showBranchDropdown = false;
        $this->branchSearch = '';
    }

    /** Selected branch name (for summary display) */
    public function getSelectedBranchNameProperty()
    {
        if (!$this->selectedBranchId) {
            return null;
        }
        return Branch::find($this->selectedBranchId)?->name;
    }

    /** Boxes for this branch with their DRs (for branch summary) */
    public function getBranchBoxesSummaryProperty()
    {
        if (!$this->selectedBranchAllocationId) {
            return collect();
        }
        return Box::where('branch_allocation_id', $this->selectedBranchAllocationId)
            ->with('deliveryReceipts')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($box) {
                $dr = $box->deliveryReceipts->first();
                return (object) [
                    'id' => $box->id,
                    'box_number' => $box->box_number,
                    'status' => $box->status,
                    'current_count' => $box->current_count,
                    'dr_number' => $dr?->dr_number ?? '—',
                ];
            });
    }

    /** Scanned items for the current box (moved from view for performance) */
    public function getScannedItemsProperty()
    {
        if (!$this->currentBox) {
            return collect();
        }
        return BranchAllocationItem::where('box_id', $this->currentBox->id)
            ->where('scanned_quantity', '>', 0)
            ->get();
    }

    /** Products allocatable to this branch (included in allocation, for this branch) */
    public function getAllocatableProductsProperty()
    {
        if (!$this->selectedBranchAllocationId) {
            return collect();
        }
        $items = BranchAllocationItem::where('branch_allocation_id', $this->selectedBranchAllocationId)
            ->whereNull('box_id')
            ->orderByRaw('COALESCE(product_snapshot_name, "") ASC')
            ->get();

        return $items->map(function ($item) {
            $scanned = BranchAllocationItem::where('branch_allocation_id', $this->selectedBranchAllocationId)
                ->where('product_id', $item->product_id)
                ->whereNotNull('box_id')
                ->sum('scanned_quantity');
            return (object) [
                'name' => $item->display_name,
                'barcode' => $item->display_barcode,
                'allocated' => $item->quantity,
                'scanned' => $scanned,
                'remaining' => max(0, $item->quantity - $scanned),
            ];
        });
    }

    /** Resolve branch to branch_allocation_id (prefer draft with products, else most recent draft) */
    private function resolveBranchAllocation(int $branchId): ?int
    {
        $withProducts = BranchAllocation::where('branch_id', $branchId)
            ->whereHas('batchAllocation', fn ($q) => $q->where('status', 'draft'))
            ->whereHas('items', fn ($q) => $q->whereNull('box_id'))
            ->orderByDesc('id')
            ->value('id');

        return $withProducts ?? BranchAllocation::where('branch_id', $branchId)
            ->whereHas('batchAllocation', fn ($q) => $q->where('status', 'draft'))
            ->orderByDesc('id')
            ->value('id');
    }

    public function updatedSelectedBranchId()
    {
        $this->selectedBranchAllocationId = $this->selectedBranchId
            ? $this->resolveBranchAllocation($this->selectedBranchId)
            : null;
        $this->selectedBoxId = null;
        $this->availableBoxes = [];
        $this->currentBox = null;
        $this->currentDr = null;
        $this->motherDr = null;
        $this->scanFeedback = '';
        $this->scanStatus = 'neutral';

        if ($this->selectedBranchAllocationId) {
            $this->loadAvailableBoxes();
        }
    }

    public function updatedSelectedBoxId()
    {
        if ($this->selectedBoxId) {
            $this->selectBox($this->selectedBoxId);
        } else {
            $this->clearBoxSelection();
        }
    }

    public function loadAvailableBoxes()
    {
        if (!$this->selectedBranchAllocationId) {
            return;
        }
        $this->availableBoxes = Box::where('branch_allocation_id', $this->selectedBranchAllocationId)
            ->where('status', '!=', 'closed')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createNewBox()
    {
        if (!$this->selectedBranchAllocationId) {
            session()->flash('error', 'Please select a branch first.');
            return;
        }

        $branchAllocation = BranchAllocation::with('branch')->find($this->selectedBranchAllocationId);
        if (!$branchAllocation) {
            session()->flash('error', 'Branch allocation not found.');
            return;
        }

        $boxNumber = 'BOX-' . $branchAllocation->branch->code . '-' . now()->format('YmdHis');

        $box = Box::create([
            'branch_allocation_id' => $this->selectedBranchAllocationId,
            'box_number' => $boxNumber,
            'status' => 'open',
            'current_count' => 0,
        ]);

        // Create DR for this box immediately (each box has its own DR)
        $this->createDrForNewBox($box->id);

        $this->loadAvailableBoxes();
        session()->flash('message', "New box created: {$boxNumber}");
    }

    public function selectBox(int $boxId)
    {
        $this->selectedBoxId = $boxId;
        $this->currentBox = Box::find($boxId);

        if ($this->currentBox) {
            $existingDr = DeliveryReceipt::where('box_id', $boxId)->first();
            if ($existingDr) {
                $this->currentDr = $existingDr;
            } else {
                $this->createDrForBox($boxId);
            }
            $this->scanFeedback = '';
            $this->scanStatus = 'neutral';
            $this->lastScannedBarcode = '';
        }
    }

    public function clearBoxSelection()
    {
        $this->selectedBoxId = null;
        $this->currentBox = null;
        $this->currentDr = null;
        $this->motherDr = null;
    }

    /** Create DR for a newly created box (does not set currentBox/currentDr) */
    private function createDrForNewBox(int $boxId): void
    {
        $box = Box::find($boxId);
        if (!$box) {
            return;
        }
        $branchAllocation = $box->branchAllocation;
        $existingBoxesCount = Box::where('branch_allocation_id', $branchAllocation->id)->count();
        $hasDispatchedBoxes = Box::where('branch_allocation_id', $branchAllocation->id)->whereNotNull('dispatched_at')->exists();
        $isMother = $existingBoxesCount === 1 || $hasDispatchedBoxes;

        $drNumber = 'DR-' . $branchAllocation->branch->code . '-' . now()->format('YmdHis') . '-' . $boxId;

        DeliveryReceipt::create([
            'branch_allocation_id' => $branchAllocation->id,
            'box_id' => $boxId,
            'dr_number' => $drNumber,
            'type' => $isMother ? 'mother' : 'child',
            'parent_dr_id' => $isMother ? null : (DeliveryReceipt::where('branch_allocation_id', $branchAllocation->id)->where('type', 'mother')->first()?->id),
            'status' => 'pending',
            'total_items' => 0,
            'scanned_items' => 0,
        ]);
    }

    private function createDrForBox(int $boxId)
    {
        $box = Box::find($boxId);
        if (!$box) {
            return;
        }

        $branchAllocation = $box->branchAllocation;
        $existingBoxesCount = Box::where('branch_allocation_id', $branchAllocation->id)->count();
        $hasDispatchedBoxes = Box::where('branch_allocation_id', $branchAllocation->id)->whereNotNull('dispatched_at')->exists();
        $isMother = $existingBoxesCount === 1 || $hasDispatchedBoxes;

        // Each box gets its own unique DR number (include box id for uniqueness)
        $drNumber = 'DR-' . $branchAllocation->branch->code . '-' . now()->format('YmdHis') . '-' . $boxId;

        $dr = DeliveryReceipt::create([
            'branch_allocation_id' => $branchAllocation->id,
            'box_id' => $boxId,
            'dr_number' => $drNumber,
            'type' => $isMother ? 'mother' : 'child',
            'parent_dr_id' => $isMother ? null : $this->getMotherDrId($branchAllocation->id),
            'status' => 'pending',
            'total_items' => 0,
            'scanned_items' => 0,
        ]);

        $this->currentDr = $dr;
        if ($isMother) {
            $this->motherDr = $dr;
        }
        session()->flash('message', "DR created for box: {$drNumber}");
    }

    private function getMotherDrId(int $branchAllocationId): ?int
    {
        if ($this->motherDr) {
            return $this->motherDr->id;
        }
        $motherDr = DeliveryReceipt::where('branch_allocation_id', $branchAllocationId)
            ->where('type', 'mother')
            ->first();
        return $motherDr?->id;
    }

    public function clearBarcodeInput()
    {
        $this->barcodeInput = '';
    }

    public function processBarcodeScanner()
    {
        if (empty(trim($this->barcodeInput))) {
            return;
        }

        $barcode = trim($this->barcodeInput);
        $this->lastScannedBarcode = $barcode;

        if (!$this->selectedBranchAllocationId) {
            $this->scanFeedback = 'Please select a branch first.';
            $this->scanStatus = 'error';
            $this->barcodeInput = '';
            return;
        }

        if (!$this->currentBox || !$this->currentDr) {
            $this->scanFeedback = 'Please select or create a box first.';
            $this->scanStatus = 'error';
            $this->barcodeInput = '';
            return;
        }

        $branchAllocation = BranchAllocation::with('branch')->find($this->selectedBranchAllocationId);
        if (!$branchAllocation) {
            $this->scanFeedback = 'Branch allocation not found.';
            $this->scanStatus = 'error';
            $this->barcodeInput = '';
            return;
        }

        // Validation: find product and check allocation + branch
        $result = $this->validateScannedProduct($barcode, $branchAllocation);
        if (!$result['valid']) {
            $this->scanFeedback = $result['message'];
            $this->scanStatus = 'error';
            session()->flash('scan_error', $result['message']);
            $this->barcodeInput = '';
            $this->dispatch('refocus-barcode-input');
            return;
        }

        $item = $result['item'];
        $productId = $item->product_id;
        $allocatedQty = $item->quantity;
        $branchName = $branchAllocation->branch->name;

        $existingScannedItem = BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
            ->where('product_id', $productId)
            ->where('box_id', $this->currentBox->id)
            ->first();

        $totalScannedQty = BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
            ->where('product_id', $productId)
            ->whereNotNull('box_id')
            ->sum('scanned_quantity');

        if ($totalScannedQty >= $allocatedQty) {
            $this->scanFeedback = "{$item->display_name} for {$branchName} - Already fully scanned.";
            $this->scanStatus = 'error';
            session()->flash('scan_warning', "Product '{$item->display_name}' for {$branchName} is already fully scanned.");
            $this->barcodeInput = '';
            $this->dispatch('refocus-barcode-input');
            return;
        }

        if ($existingScannedItem) {
            $existingScannedItem->increment('scanned_quantity');
        } else {
            BranchAllocationItem::create([
                'branch_allocation_id' => $branchAllocation->id,
                'product_id' => $productId,
                'quantity' => 1,
                'scanned_quantity' => 1,
                'unit_price' => $item->unit_price,
                'box_id' => $this->currentBox->id,
                'delivery_receipt_id' => $this->currentDr->id,
                'product_snapshot_name' => $item->product_snapshot_name,
                'product_snapshot_sku' => $item->product_snapshot_sku,
                'product_snapshot_barcode' => $item->product_snapshot_barcode,
                'product_snapshot_specs' => $item->product_snapshot_specs,
                'product_snapshot_price' => $item->product_snapshot_price,
                'product_snapshot_uom' => $item->product_snapshot_uom,
                'product_snapshot_created_at' => $item->product_snapshot_created_at,
            ]);
        }

        $this->currentBox->increment('current_count');
        $this->currentDr->increment('scanned_items');

        $remaining = $allocatedQty - ($totalScannedQty + 1);
        if ($remaining === 0) {
            $this->scanFeedback = "{$item->display_name} for {$branchName} - COMPLETE.";
            $this->scanStatus = 'success';
        } else {
            $this->scanFeedback = "{$item->display_name} for {$branchName} - " . ($totalScannedQty + 1) . "/{$allocatedQty} ({$remaining} remaining)";
            $this->scanStatus = 'success';
        }
        session()->flash('scan_success', $this->scanFeedback);

        $this->barcodeInput = '';
        $this->dispatch('refocus-barcode-input');
    }

    /**
     * Validate scanned product: must be in an allocation AND for the correct branch.
     */
    private function validateScannedProduct(string $barcode, BranchAllocation $selectedBranchAllocation): array
    {
        $productId = Product::where('barcode', $barcode)->value('id');
        if (!$productId) {
            $productId = BranchAllocationItem::where('product_snapshot_barcode', $barcode)
                ->whereNull('box_id')
                ->value('product_id');
        }
        if (!$productId) {
            return ['valid' => false, 'message' => 'This product is not in any allocation.', 'item' => null];
        }

        $allocationsForProduct = BranchAllocationItem::with('branchAllocation.branch')
            ->where('product_id', $productId)
            ->whereNull('box_id')
            ->get();

        if ($allocationsForProduct->isEmpty()) {
            return ['valid' => false, 'message' => 'This product is not in any allocation.', 'item' => null];
        }

        foreach ($allocationsForProduct as $item) {
            if ((int) $item->branch_allocation_id === (int) $selectedBranchAllocation->id) {
                return ['valid' => true, 'message' => null, 'item' => $item];
            }
        }

        $wrongBranch = $allocationsForProduct->first()->branchAllocation->branch->name ?? 'another branch';
        return [
            'valid' => false,
            'message' => "This product is allocated to {$wrongBranch}, not {$selectedBranchAllocation->branch->name}.",
            'item' => null,
        ];
    }

    public function render()
    {
        return view('livewire.pages.allocation.scanning');
    }
}
