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

    public function mount(): void
    {
        $branchAllocationId = request()->query('branch_allocation_id');
        if ($branchAllocationId && is_numeric($branchAllocationId)) {
            $ba = BranchAllocation::with(['branch', 'batchAllocation'])->find((int) $branchAllocationId);
            if ($ba && $ba->branch_id && $ba->batchAllocation?->status === 'draft') {
                $this->selectedBranchId = $ba->branch_id;
                $this->selectedBranchAllocationId = $ba->id;
                $this->branchSearch = $ba->branch?->name ?? '';
                $this->loadAvailableBoxes();
            }
        }
    }

    /** Branches that have at least one draft allocation (for scanner selection) */
    public function getBranchesWithAllocationsProperty()
    {
        return Branch::whereHas('branchAllocations', function ($q) {
            $q->whereHas('batchAllocation', fn ($bq) => $bq->where('status', 'draft'));
        })->orderBy('name')->get();
    }

    /** Branches filtered by search (token-prefix + substring fallback) */
    public function getFilteredBranchesProperty()
    {
        $branches = $this->branchesWithAllocations;
        if (trim($this->branchSearch) === '') {
            return $branches;
        }
        $search = trim($this->branchSearch);
        $lower = strtolower($search);
        return $branches->filter(function ($b) use ($search, $lower) {
            $name = $b->name ?? '';
            $code = $b->code ?? '';
            $nameLower = strtolower($name);
            $codeLower = strtolower($code);
            return ProductSearchHelper::matchesAnyField($search, [$name, $code])
                || str_contains($nameLower, $lower)
                || str_contains($codeLower, $lower);
        })->values();
    }

    /** @return \Illuminate\Support\Collection<int> All draft branch_allocation IDs for selected branch */
    public function getBranchAllocationIdsForSelectedBranchProperty()
    {
        if (!$this->selectedBranchId) {
            return collect();
        }
        return BranchAllocation::where('branch_id', $this->selectedBranchId)
            ->whereHas('batchAllocation', fn ($q) => $q->where('status', 'draft'))
            ->pluck('id');
    }

    public function selectBranch(int $branchId): void
    {
        $branch = Branch::find($branchId);
        $this->selectedBranchId = $branchId;
        $this->updatedSelectedBranchId();
        $this->showBranchDropdown = false;
        $this->branchSearch = $branch?->name ?? '';
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

    /** True when all products for this branch are fully scanned (remaining = 0 for each) */
    public function getIsFullyScannedProperty(): bool
    {
        $products = $this->allocatableProducts;
        if ($products->isEmpty()) {
            return false;
        }
        return $products->every(fn ($p) => $p->remaining <= 0);
    }

    /** Mother DR number for this branch (summary reference) - null if no boxes/DRs */
    public function getSummaryDrNumberProperty(): ?string
    {
        if (!$this->selectedBranchAllocationId) {
            return null;
        }
        $mother = DeliveryReceipt::where('branch_allocation_id', $this->selectedBranchAllocationId)
            ->where('type', 'mother')
            ->first();
        return $mother?->dr_number;
    }

    /** Mother DR id for this branch (for Create shipment link) */
    public function getSummaryDrIdProperty(): ?int
    {
        if (!$this->selectedBranchAllocationId) {
            return null;
        }
        $mother = DeliveryReceipt::where('branch_allocation_id', $this->selectedBranchAllocationId)
            ->where('type', 'mother')
            ->first();
        return $mother?->id;
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

    /** Products allocatable to this branch (consolidated across all draft allocations) */
    public function getAllocatableProductsProperty()
    {
        $allocationIds = $this->branchAllocationIdsForSelectedBranch;
        if ($allocationIds->isEmpty()) {
            return collect();
        }

        $allocationRows = BranchAllocationItem::whereIn('branch_allocation_id', $allocationIds)
            ->whereNull('box_id')
            ->get();

        $consumptionRows = BranchAllocationItem::whereIn('branch_allocation_id', $allocationIds)
            ->whereNotNull('box_id')
            ->get();

        $productIds = $allocationRows->pluck('product_id')->unique()->merge(
            $consumptionRows->pluck('product_id')->unique()
        )->unique();

        return $productIds->map(function ($productId) use ($allocationRows, $consumptionRows) {
            $allocItems = $allocationRows->where('product_id', $productId);
            $consumeItems = $consumptionRows->where('product_id', $productId);
            $allocated = $allocItems->sum('quantity');
            $scanned = $consumeItems->sum('scanned_quantity');
            $first = $allocItems->first() ?? $consumeItems->first();
            return (object) [
                'name' => $first->display_name,
                'barcode' => $first->display_barcode,
                'allocated' => $allocated,
                'scanned' => $scanned,
                'remaining' => max(0, $allocated - $scanned),
            ];
        })
            ->sortBy('name', SORT_NATURAL)
            ->values();
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

    public function deleteBox(int $boxId): void
    {
        $box = Box::find($boxId);
        if (!$box) {
            session()->flash('error', 'Box not found.');
            return;
        }

        BranchAllocationItem::where('box_id', $boxId)->update([
            'scanned_quantity' => 0,
            'box_id' => null,
            'delivery_receipt_id' => null,
        ]);

        $box->delete();
        DeliveryReceipt::where('box_id', $boxId)->delete();

        $this->loadAvailableBoxes();

        if ((int) $this->selectedBoxId === $boxId) {
            $this->clearBoxSelection();
        }

        session()->flash('message', 'Box deleted. Scanned items are available for rescanning.');
    }

    public function generateDrForBox(int $boxId): void
    {
        $box = Box::find($boxId);
        if (!$box) {
            session()->flash('error', 'Box not found.');
            return;
        }

        $existingDr = DeliveryReceipt::where('box_id', $boxId)->first();
        if ($existingDr) {
            session()->flash('message', "DR already exists: {$existingDr->dr_number}");
            return;
        }

        $this->createDrForNewBox($boxId);
        $dr = DeliveryReceipt::where('box_id', $boxId)->first();
        session()->flash('message', $dr ? "DR created: {$dr->dr_number}" : 'DR created.');
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

    /** Export delivery receipt PDF (same as allocation module DR export) */
    public function exportDeliverySummary(): void
    {
        if (!$this->selectedBranchAllocationId) {
            session()->flash('error', 'No branch selected.');
            return;
        }

        $branchAllocation = BranchAllocation::find($this->selectedBranchAllocationId);
        if (!$branchAllocation) {
            session()->flash('error', 'Branch allocation not found.');
            return;
        }

        $url = route('allocation.delivery-receipt.generate', ['branchAllocationId' => $this->selectedBranchAllocationId]);
        $branchName = $branchAllocation->branch->name ?? 'Unknown';
        $refNo = $branchAllocation->batchAllocation->ref_no ?? 'Unknown';
        $filename = 'delivery_receipt_' . preg_replace('/[^\w\-_.]/', '_', $branchName . '_' . $refNo) . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        $this->dispatch('download-delivery-receipt', url: $url, filename: $filename);
        session()->flash('message', 'Delivery receipt PDF download started.');
    }

    /** Mark this branch allocation's boxes as dispatched */
    public function dispatchForShipment(): void
    {
        if (!$this->selectedBranchAllocationId) {
            session()->flash('error', 'No branch selected.');
            return;
        }

        Box::where('branch_allocation_id', $this->selectedBranchAllocationId)
            ->whereNull('dispatched_at')
            ->update(['dispatched_at' => now()]);

        $this->loadAvailableBoxes();
        session()->flash('message', 'Branch shipment dispatched. Summary DR: ' . ($this->summaryDrNumber ?? 'N/A'));
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

    public function updatedBarcodeInput(): void
    {
        if (trim($this->barcodeInput) !== '') {
            $this->processBarcodeScanner();
        }
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

        $branchName = $this->selectedBranchName ?? 'this branch';
        $allocationIds = $this->branchAllocationIdsForSelectedBranch;
        if ($allocationIds->isEmpty()) {
            $this->scanFeedback = 'No draft allocation found for this branch.';
            $this->scanStatus = 'error';
            $this->barcodeInput = '';
            return;
        }

        // Validation: find product and check allocation + branch
        $result = $this->validateScannedProduct($barcode);
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

        $allocatedQty = BranchAllocationItem::whereIn('branch_allocation_id', $allocationIds)
            ->where('product_id', $productId)
            ->whereNull('box_id')
            ->sum('quantity');

        $totalScannedQty = BranchAllocationItem::whereIn('branch_allocation_id', $allocationIds)
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

        $existingInBox = BranchAllocationItem::whereIn('branch_allocation_id', $allocationIds)
            ->where('product_id', $productId)
            ->where('box_id', $this->currentBox->id)
            ->first();

        if ($existingInBox) {
            $existingInBox->increment('scanned_quantity');
        } else {
            BranchAllocationItem::create([
                'branch_allocation_id' => $item->branch_allocation_id,
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
     * Validate scanned product: must be in an allocation for the selected branch.
     * Searches across all draft allocations for the branch. Supports barcode/sku lookup.
     */
    private function validateScannedProduct(string $barcode): array
    {
        $barcode = trim($barcode);
        if ($barcode === '') {
            return ['valid' => false, 'message' => 'Invalid barcode.', 'item' => null];
        }

        $allocationIds = $this->branchAllocationIdsForSelectedBranch;
        $selectedBranchName = $this->selectedBranchName ?? 'selected branch';

        // 1. Lookup in any allocation for this branch: allocation item matching barcode/sku
        $allocationItem = BranchAllocationItem::whereIn('branch_allocation_id', $allocationIds)
            ->where(function ($q) use ($barcode) {
                $q->where('product_snapshot_barcode', $barcode)
                  ->orWhere('product_snapshot_sku', $barcode)
                  ->orWhereHas('product', function ($pq) use ($barcode) {
                      $pq->where('barcode', $barcode)->orWhere('sku', $barcode);
                  });
            })
            ->first();

        if ($allocationItem) {
            return ['valid' => true, 'message' => null, 'item' => $allocationItem];
        }

        // 2. Product exists but not in this branch - find which branch has it
        $productId = Product::active()
            ->where(function ($q) use ($barcode) {
                $q->where('barcode', $barcode)->orWhere('sku', $barcode);
            })
            ->value('id');
        if (!$productId) {
            $productId = BranchAllocationItem::where(function ($q) use ($barcode) {
                $q->where('product_snapshot_barcode', $barcode)
                  ->orWhere('product_snapshot_sku', $barcode);
            })->value('product_id');
        }

        if ($productId) {
            $otherAllocation = BranchAllocationItem::with('branchAllocation.branch')
                ->where('product_id', $productId)
                ->when($allocationIds->isNotEmpty(), fn ($q) => $q->whereNotIn('branch_allocation_id', $allocationIds))
                ->first();
            $wrongBranch = $otherAllocation?->branchAllocation?->branch?->name ?? 'another branch';
            return [
                'valid' => false,
                'message' => "This product is allocated to {$wrongBranch}, not {$selectedBranchName}.",
                'item' => null,
            ];
        }

        return ['valid' => false, 'message' => 'This product is not in any allocation.', 'item' => null];
    }

    public function render()
    {
        return view('livewire.pages.allocation.scanning');
    }
}
