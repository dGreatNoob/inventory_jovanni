<?php

namespace App\Livewire\Pages\Allocation;

use App\Models\BatchAllocation;
use App\Models\Branch;
use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;
use App\Models\Product;
use App\Models\Box;
use App\Models\DeliveryReceipt;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
#[Title('Allocation - Warehouse')]
class Warehouse extends Component
{
    public $batchAllocations = [];
    public array $batch_numbers = [];

    public $openBatches = []; // Track which batches are open/closed
    public $selectedBatch = null;
    public $showCreateBatchModal = false;
    public $showAddBranchesModal = false;
    public $showAddItemsModal = false;
    public $showEditItemModal = false;
    public $selectedBranchAllocation = null;
    public $selectedEditItem = null;
    public $scannedQuantities = [];
    public $dispatchProducts = [];
    public $barcodeInput = '';
    public $lastScannedBarcode = '';
    public $scanFeedback = '';
    public $activeBranchId = null; // Track which branch is currently being scanned
    public $editingBatchId = null;
    public $isEditing = false;
    public $batchSteps = [];

    // Box and DR related properties
    public $availableBoxes = [];
    public $selectedBoxId = null;
    public $currentBox = null;
    public $currentDr = null;
    public $motherDr = null;
    public $showBarcodeScannerModal = false;


    // VDR Export fields
    public $showVDRPreviewModal = false;
    public $selectedBatchForVDR = null;
    public $vendorCode = '';
    public $vendorName = '';

    // Filter fields
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';

    // Create batch fields
    public $remarks;
    public $ref_no;
    public $status = 'draft';
    public $batch_number = ''; // Old field for single batch number selection
    public $selectedBatchNumbers = []; // New field for multiple batch number selection

    // Stepper workflow fields
    public $currentStep = 1;
    public $availableBatchNumbers = [];
    public $filteredBranchesByBatch = [];
    public $currentBatch = null;
    public $showStepper = false;
    public $selectedBranchAllocationId = null;

    // Product allocation fields
    public $availableProducts = [];
    public $selectedProductId = null;
    public $productQuantity = 1;
    public $productUnitPrice = null;
    public $productAllocations = []; // Array to store product allocations for all branches
    public $branchQuantities = []; // Array of branch_id => quantity for per-branch allocation
    public $matrixQuantities = []; // Matrix: branch_id => product_id => quantity
    public $selectedProductIdsForAllocation = []; // Selected products for allocation matrix
    public $temporarySelectedProducts = []; // Temporary selection for current filter

    // Product filtering fields
    public $availableCategories = [];
    public $selectedCategoryId = null;
    public $selectedProductFilterName = null;
    public $showAllProducts = false;
    public $filteredProducts = [];

    // Add branches fields
    public $availableBranches = [];
    public $selectedBranchIds = [];
    public $branchRemarks = [];
    public $branchReferenceNumbers = [];

    // Edit item fields
    public $editProductQuantity = 1;

    public function mount()
    {
        // Set defaults
        $this->status = 'draft';

        // Generate reference number
        $this->ref_no = $this->generateRefNo();

        // Load data
        $this->loadAvailableBatchNumbers();
        $this->loadAvailableProducts();
        $this->loadAvailableCategories();
        $this->loadBatchAllocations();

        // Initialize batch steps for the table
        $this->initializeBatchSteps();

        // Hide stepper on initial load
        $this->showStepper = false;
        $this->currentStep = 1;

        // Initialize empty scanned quantities
        $this->scannedQuantities = [];

        // Initialize selected batch numbers
        $this->selectedBatchNumbers = [];
    }

    public function initializeScannedQuantities()
    {
        // Load scanned quantities from database for current batch
        $this->scannedQuantities = [];

        if (!$this->currentBatch) {
            return;
        }

        // Refresh batch to get latest data
        $this->currentBatch->refresh();

        foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
            $this->scannedQuantities[$branchAllocation->id] = [];

            // Only process original allocation items (without box_id)
            $originalItems = $branchAllocation->items()->whereNull('box_id')->get();
            foreach ($originalItems as $item) {
                // Calculate total scanned quantity for this product across all boxes
                $totalScannedQty = BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
                    ->where('product_id', $item->product_id)
                    ->whereNotNull('box_id')
                    ->sum('scanned_quantity');

                $this->scannedQuantities[$branchAllocation->id][$item->product_id] = $totalScannedQty;
            }
        }
    }

    private function initializeBatchSteps()
    {
        $this->batchSteps = [];
        
        if (empty($this->batchAllocations)) {
            return;
        }
        
        foreach ($this->batchAllocations as $batch) {
            // If this batch is currently being edited, use the UI step
            if ($this->isEditing && $this->currentBatch && $this->currentBatch->id === $batch->id) {
                $this->batchSteps[$batch->id] = $this->currentStep;
            } else {
                // Otherwise determine step from data
                $this->batchSteps[$batch->id] = $this->determineBatchStep($batch);
            }
        }
    }

    private function determineBatchStep($batch)
    {
        // If batch is dispatched, it's completed
        if ($batch->status === 'dispatched') {
            return 4;
        }
        
        // Use saved workflow_step if available
        if ($batch->workflow_step) {
            return $batch->workflow_step;
        }
        
        // Fallback: determine from data (for old batches without workflow_step)
        $hasProducts = false;
        foreach ($batch->branchAllocations as $branchAllocation) {
            if ($branchAllocation->items()->whereNull('box_id')->count() > 0) {
                $hasProducts = true;
                break;
            }
        }
        
        if ($hasProducts) {
            return 4; // Has products, ready for dispatch
        }
        
        if ($batch->branchAllocations->count() > 0) {
            return 2; // Has branches, viewing branches
        }
        
        if ($batch->batch_number) {
            return 2;
        }
        
        return 1;
    }

    
    public function getTotalItemsCount()
    {
        if (!$this->currentBatch) {
            return 0;
        }

        $total = 0;
        foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
            // Only count original allocation items (without box_id)
            $total += $branchAllocation->items()->whereNull('box_id')->count();
        }

        return $total;
    }

    public function getUniqueProductsCount()
    {
        if (!$this->currentBatch) {
            return 0;
        }

        $uniqueProductIds = [];
        foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
            // Only check original allocation items (without box_id)
            $originalItems = $branchAllocation->items()->whereNull('box_id')->get();
            foreach ($originalItems as $item) {
                $uniqueProductIds[$item->product_id] = true;
            }
        }

        return count($uniqueProductIds);
    }

    public function getPendingItemsCount()
    {
        if (!$this->currentBatch) {
            return 0;
        }

        return $this->getTotalQuantitiesCount() - $this->getTotalScannedQuantitiesCount();
    }

    public function getTotalBoxesCount()
    {
        if (!$this->currentBatch) {
            return 0;
        }

        $totalBoxes = 0;
        foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
            $totalBoxes += Box::where('branch_allocation_id', $branchAllocation->id)->count();
        }

        return $totalBoxes;
    }

    public function getTotalQuantitiesCount()
    {
        if (!$this->currentBatch) {
            return 0;
        }

        $totalQuantities = 0;
        foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
            // Only count original allocation items (without box_id)
            $originalItems = $branchAllocation->items()->whereNull('box_id')->get();
            foreach ($originalItems as $item) {
                $totalQuantities += $item->quantity;
            }
        }

        return $totalQuantities;
    }

    public function getTotalScannedQuantitiesCount()
    {
        if (!$this->currentBatch) {
            return 0;
        }

        $totalScannedQuantities = 0;
        foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
            // Sum scanned quantities from items that have been scanned into boxes
            $totalScannedQuantities += BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
                ->whereNotNull('box_id')
                ->sum('scanned_quantity');
        }

        return $totalScannedQuantities;
    }
    public function getFullyScannedCount()
    {
        if (!$this->currentBatch) {
            return 0;
        }

        $count = 0;

        // Refresh to get latest data
        $this->currentBatch->refresh();

        foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
            // Only check original allocation items (without box_id)
            $originalItems = $branchAllocation->items()->whereNull('box_id')->get();
            foreach ($originalItems as $item) {
                // Calculate total scanned quantity for this product across all boxes
                $totalScannedQty = BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
                    ->where('product_id', $item->product_id)
                    ->whereNotNull('box_id')
                    ->sum('scanned_quantity');

                if ($totalScannedQty >= $item->quantity) {
                    $count++;
                }
            }
        }

        return $count;
    }

    public function isBranchComplete($branchAllocationId)
    {
        if (!$this->currentBatch) {
            return false;
        }

        $branchAllocation = $this->currentBatch->branchAllocations->find($branchAllocationId);

        if (!$branchAllocation) {
            return false;
        }

        // Only check original allocation items (without box_id)
        $originalItems = $branchAllocation->items()->whereNull('box_id')->get();
        foreach ($originalItems as $item) {
            // Calculate total scanned quantity for this product across all boxes
            $totalScannedQty = BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
                ->where('product_id', $item->product_id)
                ->whereNotNull('box_id')
                ->sum('scanned_quantity');

            if ($totalScannedQty < $item->quantity) {
                return false;
            }
        }

        return true;
    }

    public function allProductsFullyScanned()
    {
        if (!$this->currentBatch) {
            return false;
        }

        // Refresh to get latest data
        $this->currentBatch->refresh();

        foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
            // Only check original allocation items (without box_id)
            $originalItems = $branchAllocation->items()->whereNull('box_id')->get();
            foreach ($originalItems as $item) {
                // Calculate total scanned quantity for this product across all boxes
                $totalScannedQty = BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
                    ->where('product_id', $item->product_id)
                    ->whereNotNull('box_id')
                    ->sum('scanned_quantity');

                if ($totalScannedQty < $item->quantity) {
                    return false;
                }
            }
        }

        return true;
    }
    public function loadBatchAllocations()
    {
        $query = BatchAllocation::with([
            'branchAllocations.branch',
            'branchAllocations.items.product'
        ])->orderBy('created_at', 'desc');

        // Apply filters...
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('ref_no', 'like', '%' . $this->search . '%')
                ->orWhere('remarks', 'like', '%' . $this->search . '%')
                ->orWhereHas('branchAllocations.branch', function ($branchQuery) {
                    $branchQuery->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        if ($this->dateFrom) {
            $query->where('transaction_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('transaction_date', '<=', $this->dateTo);
        }

        $this->batchAllocations = $query->get();
        
        // Initialize open states
        foreach ($this->batchAllocations as $batch) {
            if (!isset($this->openBatches[$batch->id])) {
                $this->openBatches[$batch->id] = false;
            }
        }
        
        // Initialize batch steps
        $this->initializeBatchSteps();
    }

    public function loadAvailableBatchNumbers()
    {
        // Get unique batch numbers from Branch model
        $this->availableBatchNumbers = Branch::whereNotNull('batch')
            ->where('batch', '!=', '')
            ->distinct()
            ->pluck('batch')
            ->sort()
            ->values()
            ->toArray();
    }
    public function processBarcodeScanner()
    {
        if (empty($this->barcodeInput)) {
            return;
        }

        $barcode = trim($this->barcodeInput);
        $this->lastScannedBarcode = $barcode;

        // Check if a branch is selected
        if (!$this->activeBranchId) {
            $this->scanFeedback = "⚠️ Please select a branch first before scanning!";
            session()->flash('scan_warning', 'You must select which branch you are scanning for.');
            $this->barcodeInput = '';
            return;
        }

        // Check if a box and DR are selected
        if (!$this->currentBox || !$this->currentDr) {
            $this->scanFeedback = "⚠️ Please select a box and create/open a DR first!";
            session()->flash('scan_warning', 'You must select a box and have an active DR before scanning.');
            $this->barcodeInput = '';
            return;
        }

        // Find the branch allocation
        $branchAllocation = $this->currentBatch->branchAllocations->find($this->activeBranchId);

        if (!$branchAllocation) {
            $this->scanFeedback = "❌ Selected branch not found!";
            $this->barcodeInput = '';
            return;
        }

        // Find the product in this branch's items
        $productFound = false;

        foreach ($branchAllocation->items as $item) {
            // Use snapshot barcode for historical integrity - ensures scanning works even if product barcode changed
            if ($item->display_barcode === $barcode) {
                $productId = $item->product_id;
                $allocatedQty = $item->quantity;
                $branchName = $branchAllocation->branch->name;

                // Check if this product has already been scanned into the current box
                $existingScannedItem = BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
                    ->where('product_id', $productId)
                    ->where('box_id', $this->currentBox->id)
                    ->first();

                // Calculate total scanned quantity for this product across all boxes in this branch
                $totalScannedQty = BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
                    ->where('product_id', $productId)
                    ->whereNotNull('box_id')
                    ->sum('scanned_quantity');

                // Check if we can still scan more of this product
                if ($totalScannedQty >= $allocatedQty) {
                    $this->scanFeedback = "⚠️ {$item->display_name} for {$branchName} - Already fully scanned!";
                    session()->flash('scan_warning', "Product '{$item->display_name}' for {$branchName} is already fully scanned.");
                    $productFound = true;
                    break;
                }

                if ($existingScannedItem) {
                    // Increment existing scanned item in this box
                    $existingScannedItem->increment('scanned_quantity');
                    $newScannedQty = $existingScannedItem->scanned_quantity;
                } else {
                    // Create new scanned item record for this box
                    $newScannedItem = BranchAllocationItem::create([
                        'branch_allocation_id' => $branchAllocation->id,
                        'product_id' => $productId,
                        'quantity' => 1, // This represents the scanned quantity in this box
                        'scanned_quantity' => 1,
                        'unit_price' => $item->unit_price,
                        'box_id' => $this->currentBox->id,
                        'delivery_receipt_id' => $this->currentDr->id,
                        // Copy snapshot data
                        'product_snapshot_name' => $item->product_snapshot_name,
                        'product_snapshot_sku' => $item->product_snapshot_sku,
                        'product_snapshot_barcode' => $item->product_snapshot_barcode,
                        'product_snapshot_specs' => $item->product_snapshot_specs,
                        'product_snapshot_price' => $item->product_snapshot_price,
                        'product_snapshot_uom' => $item->product_snapshot_uom,
                        'product_snapshot_created_at' => $item->product_snapshot_created_at,
                    ]);
                    $newScannedQty = 1;
                }

                // Update box count
                $this->currentBox->increment('current_count');

                // Update DR scanned items count
                $this->currentDr->increment('scanned_items');

                // Update component state (for real-time display)
                if (!isset($this->scannedQuantities[$this->activeBranchId])) {
                    $this->scannedQuantities[$this->activeBranchId] = [];
                }
                $this->scannedQuantities[$this->activeBranchId][$productId] = $totalScannedQty + 1;

                $remaining = $allocatedQty - ($totalScannedQty + 1);

                if ($remaining === 0) {
                    $this->scanFeedback = "✅ {$item->display_name} for {$branchName} - COMPLETE!";
                    session()->flash('scan_success', "Product '{$item->display_name}' for {$branchName} is complete!");
                } else {
                    $this->scanFeedback = "✅ {$item->display_name} for {$branchName} - " . ($totalScannedQty + 1) . "/{$allocatedQty} ({$remaining} remaining)";
                }

                $productFound = true;
                break;
            }
        }

        if (!$productFound) {
            $this->scanFeedback = "❌ Barcode '{$barcode}' not found in {$branchAllocation->branch->name}'s allocation!";
            session()->flash('scan_error', "Barcode '{$barcode}' is not allocated to the selected branch.");
        }

        // Clear input for next scan
        $this->barcodeInput = '';

        // Refresh batch to get updated scanned quantities
        $this->currentBatch->refresh();

        // Keep focus on input field
        $this->dispatch('refocus-barcode-input');
    }
    public function resetScannedQuantities($branchAllocationId = null)
    {
        if (!$this->currentBatch) {
            return;
        }

        if ($branchAllocationId) {
            // Reset for specific branch - delete all box-specific scanned item records
            $branchAllocation = $this->currentBatch->branchAllocations->find($branchAllocationId);

            if ($branchAllocation) {
                // Delete all scanned item records for this branch that have box_id (scanned items)
                BranchAllocationItem::where('branch_allocation_id', $branchAllocationId)
                    ->whereNotNull('box_id')
                    ->delete();

                // Reset box counts and DR counts
                $boxes = Box::where('branch_allocation_id', $branchAllocationId)->get();
                foreach ($boxes as $box) {
                    $box->update(['current_count' => 0]);
                    DeliveryReceipt::where('box_id', $box->id)->update(['scanned_items' => 0]);
                }

                session()->flash('message', 'Scanned quantities reset for ' . $branchAllocation->branch->name);
            }
        } else {
            // Reset for all branches - delete all box-specific scanned item records for the batch
            $branchAllocationIds = $this->currentBatch->branchAllocations->pluck('id')->toArray();

            // Delete all scanned item records for this batch that have box_id (scanned items)
            BranchAllocationItem::whereIn('branch_allocation_id', $branchAllocationIds)
                ->whereNotNull('box_id')
                ->delete();

            // Reset box counts and DR counts for all boxes in the batch
            $boxes = Box::whereIn('branch_allocation_id', $branchAllocationIds)->get();
            foreach ($boxes as $box) {
                $box->update(['current_count' => 0]);
                DeliveryReceipt::where('box_id', $box->id)->update(['scanned_items' => 0]);
            }

            session()->flash('message', 'All scanned quantities have been reset.');
        }

        // Reload scanned quantities
        $this->initializeScannedQuantities();

        // Refresh batch
        $this->currentBatch->refresh();
    }
    public function setActiveBranch($branchAllocationId)
    {
        $this->activeBranchId = $branchAllocationId;
        $this->scanFeedback = '';

        $branchAllocation = $this->currentBatch->branchAllocations->find($branchAllocationId);
        if ($branchAllocation) {
            session()->flash('message', "Now scanning for: {$branchAllocation->branch->name}");
            $this->loadAvailableBoxes($branchAllocationId);
        }
    }

    public function loadAvailableBoxes($branchAllocationId)
    {
        $this->availableBoxes = Box::where('branch_allocation_id', $branchAllocationId)
            ->where('status', '!=', 'closed')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createNewBox()
    {
        if (!$this->activeBranchId) {
            session()->flash('error', 'Please select a branch first.');
            return;
        }

        $branchAllocation = $this->currentBatch->branchAllocations->find($this->activeBranchId);
        if (!$branchAllocation) {
            session()->flash('error', 'Branch allocation not found.');
            return;
        }

        // Generate unique box number
        $boxNumber = 'BOX-' . $branchAllocation->branch->code . '-' . now()->format('YmdHis');

        $box = Box::create([
            'branch_allocation_id' => $this->activeBranchId,
            'box_number' => $boxNumber,
            'status' => 'open',
            'current_count' => 0,
        ]);

        $this->loadAvailableBoxes($this->activeBranchId);
        session()->flash('message', "New box created: {$boxNumber}");
    }

    public function selectBox($boxId)
    {
        $this->selectedBoxId = $boxId;
        $this->currentBox = Box::find($boxId);

        if ($this->currentBox) {
            // Check if this box already has a DR
            $existingDr = DeliveryReceipt::where('box_id', $boxId)->first();

            if ($existingDr) {
                $this->currentDr = $existingDr;
            } else {
                // Create new DR for this box
                $this->createDrForBox($boxId);
            }

            // Open the barcode scanner modal
            $this->showBarcodeScannerModal = true;

            // Clear any previous scan feedback
            $this->scanFeedback = '';
            $this->lastScannedBarcode = '';
        }
    }

    public function closeBarcodeScannerModal()
    {
        $this->showBarcodeScannerModal = false;
        $this->scanFeedback = '';
        $this->lastScannedBarcode = '';

        // Reset selected box to "Select" status
        $this->selectedBoxId = null;
        $this->currentBox = null;
        $this->currentDr = null;
        $this->motherDr = null;
    }

    public function deleteBox($boxId)
    {
        $box = Box::find($boxId);
        if (!$box) {
            session()->flash('error', 'Box not found.');
            return;
        }

        // Reset scanned quantities for items in this box (make them available for rescanning)
        BranchAllocationItem::where('box_id', $boxId)->update([
            'scanned_quantity' => 0,
            'box_id' => null,
            'delivery_receipt_id' => null,
        ]);

        // Delete the box
        $box->delete();

        // Delete associated DR
        DeliveryReceipt::where('box_id', $boxId)->delete();

        // Reload available boxes
        $this->loadAvailableBoxes($this->activeBranchId);

        // If the deleted box was currently selected, reset selection
        if ($this->selectedBoxId == $boxId) {
            $this->selectedBoxId = null;
            $this->currentBox = null;
            $this->currentDr = null;
            $this->showBarcodeScannerModal = false;
        }

        session()->flash('message', 'Box deleted and scanned quantities reset (items available for rescanning).');
    }

    public function declareBoxFull()
    {
        if (!$this->currentBox) {
            session()->flash('error', 'No box selected.');
            return;
        }

        // Update box status to full
        $this->currentBox->update(['status' => 'full']);

        // Close scanner modal
        $this->closeBarcodeScannerModal();

        // Reload available boxes to reflect the change
        $this->loadAvailableBoxes($this->activeBranchId);

        session()->flash('message', 'Box declared as full.');
    }

    private function createDrForBox($boxId)
    {
        $box = Box::find($boxId);
        if (!$box) return;

        $branchAllocation = $box->branchAllocation;

        // Check if this is the first box for this branch (mother DR)
        $existingBoxesCount = Box::where('branch_allocation_id', $branchAllocation->id)->count();

        $isMother = $existingBoxesCount === 1; // First box is mother

        // Generate DR number
        $drNumber = 'DR-' . $branchAllocation->branch->code . '-' . now()->format('YmdHis');

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

        session()->flash('message', "New DR created: {$drNumber} ({$dr->type})");
    }

    private function getMotherDrId($branchAllocationId)
    {
        if ($this->motherDr) {
            return $this->motherDr->id;
        }

        // Find existing mother DR for this branch
        $motherDr = DeliveryReceipt::where('branch_allocation_id', $branchAllocationId)
            ->where('type', 'mother')
            ->first();

        return $motherDr ? $motherDr->id : null;
    }

    public function updatedBarcodeInput($value)
    {
        // Some barcode scanners automatically add Enter/Return
        // If your scanner does, the processBarcodeScanner will be called automatically
        
        // If your scanner doesn't add Enter, you can process after a certain length
        if (strlen($value) >= 10) { // Adjust length based on your barcode format
            $this->processBarcodeScanner();
        }
    }


    public function loadAvailableProducts()
    {
        $this->availableProducts = Product::orderBy('name')->get();
    }

    public function loadAvailableCategories()
    {
        $this->availableCategories = \App\Models\Category::orderBy('name')->get();
    }

    public function filterProducts()
    {
        $query = Product::query();

        if ($this->selectedCategoryId) {
            $query->where('category_id', $this->selectedCategoryId);
        }

        if ($this->selectedProductFilterName) {
            $query->where('name', $this->selectedProductFilterName);
        }

        $this->filteredProducts = $query->orderBy('name')->get();
        $this->showAllProducts = false;

        // Clear temporary selection when filtering
        $this->temporarySelectedProducts = [];
    }

    public function addSelectedProductsToAllocation()
    {
        // Add temporary selections to main allocation list
        $this->selectedProductIdsForAllocation = array_unique(array_merge(
            $this->selectedProductIdsForAllocation,
            $this->temporarySelectedProducts
        ));
        
        // Clear temporary selection
        $this->temporarySelectedProducts = [];
        
        session()->flash('message', 'Products added to allocation list!');
    }

    public function selectAllVisible()
    {
        // Get all visible products based on current filter
        $visibleProducts = $this->showAllProducts ? $this->availableProducts : $this->filteredProducts;
        $visibleProductIds = $visibleProducts->pluck('id')->toArray();
        
        // Add all visible products to temporary selection
        $this->temporarySelectedProducts = array_unique(array_merge(
            $this->temporarySelectedProducts,
            $visibleProductIds
        ));
    }

    public function removeProductFromAllocation($productId)
    {
        // Remove product from allocation list
        $this->selectedProductIdsForAllocation = array_values(array_filter(
            $this->selectedProductIdsForAllocation,
            function($id) use ($productId) {
                return $id != $productId;
            }
        ));

        // Remove product from matrix quantities
        foreach ($this->matrixQuantities as $branchId => $products) {
            if (isset($products[$productId])) {
                unset($this->matrixQuantities[$branchId][$productId]);
            }
        }

        session()->flash('message', 'Product removed from allocation!');
    }


    public function toggleProductGroup($productName)
    {
        // Get all product IDs for this base product name from filtered products
        $products = $this->showAllProducts ? $this->availableProducts : $this->filteredProducts;
        
        // Filter products that have the same product name
        $productGroupIds = $products->filter(function($product) use ($productName) {
            return $product->name === $productName;
        })->pluck('id')->toArray();

        // Check if all products in this group are already selected
        $allSelected = !array_diff($productGroupIds, $this->selectedProductIdsForAllocation);

        if ($allSelected) {
            // Deselect all products in this group
            $this->selectedProductIdsForAllocation = array_diff(
                $this->selectedProductIdsForAllocation,
                $productGroupIds
            );
        } else {
            // Select all products in this group
            $this->selectedProductIdsForAllocation = array_unique(array_merge(
                $this->selectedProductIdsForAllocation,
                $productGroupIds
            ));
        }
    }

    public function showAllProducts()
    {
        $this->showAllProducts = true;
        $this->selectedCategoryId = null;
        $this->selectedProductFilterName = null;
        $this->filteredProducts = $this->availableProducts;
    }

    public function loadBranchesByBatch()
    {
        if (empty($this->selectedBatchNumbers)) {
            $this->filteredBranchesByBatch = [];
            return;
        }

        // Load branches from all selected batches (works for both editing and creating)
        $branches = Branch::whereIn('batch', $this->selectedBatchNumbers)
            ->orderBy('name')
            ->get();

        $this->filteredBranchesByBatch = $branches->map(function($branch) {
            return [
                'id' => $branch->id,
                'name' => $branch->name,
                'address' => $branch->address ?? '',
                'code' => $branch->code ?? '',
                'batch' => $branch->batch,
            ];
        })->toArray();
    }

    public function updateBranchAllocationsForBatch()
    {
        if (!$this->currentBatch || empty($this->selectedBatchNumbers)) {
            return;
        }

        // Get current branch allocations
        $currentBranchIds = $this->currentBatch->branchAllocations->pluck('branch_id')->toArray();
        
        // Get new branch IDs from selected batches
        $newBranchIds = Branch::whereIn('batch', $this->selectedBatchNumbers)->pluck('id')->toArray();

        // Remove branch allocations that are no longer selected
        $branchesToRemove = array_diff($currentBranchIds, $newBranchIds);
        if (!empty($branchesToRemove)) {
            \App\Models\BranchAllocation::where('batch_allocation_id', $this->currentBatch->id)
                ->whereIn('branch_id', $branchesToRemove)
                ->delete();
        }

        // Add new branch allocations for newly selected branches
        $branchesToAdd = array_diff($newBranchIds, $currentBranchIds);
        foreach ($branchesToAdd as $branchId) {
            \App\Models\BranchAllocation::create([
                'batch_allocation_id' => $this->currentBatch->id,
                'branch_id' => $branchId,
                'status' => 'pending',
            ]);
        }
    }

    // Stepper Navigation Methods
    public function nextStep()
    {
        if ($this->currentStep < 4) {
            $this->currentStep++;

            // Save workflow step to database
            if ($this->currentBatch) {
                $this->currentBatch->update(['workflow_step' => $this->currentStep]);
            }

            // When moving to step 2, load branches
            if ($this->currentStep === 2 && !empty($this->batch_number)) {
                $this->loadBranchesByBatch();
            }

            // When moving to step 3, initialize products
            if ($this->currentStep === 3) {
                $this->loadMatrix();

                if ($this->isEditing && $this->currentBatch) {
                    $this->loadProductAllocations();
                }
            }

            // When moving to step 4, load dispatch products
            if ($this->currentStep === 4) {
                $this->loadDispatchProducts();
                
                if ($this->isEditing && $this->currentBatch) {
                    $this->initializeScannedQuantities();
                }
            }
        }
    }
    //editing existing batch
    public function editRecord($batchId)
    {
        $this->editingBatchId = $batchId;
        $this->isEditing = true;

        // Load the batch
        $batch = BatchAllocation::with('branchAllocations.items.product', 'branchAllocations.branch')->find($batchId);

        if (!$batch) {
            session()->flash('error', 'Batch not found.');
            return;
        }

        // Populate fields
        $this->currentBatch = $batch;
        $this->batch_number = $batch->batch_number ?? '';
        // Parse batch_number if it contains multiple batches
        if (strpos($batch->batch_number, ',') !== false) {
            $this->selectedBatchNumbers = array_map('trim', explode(',', $batch->batch_number));
        } else {
            $this->selectedBatchNumbers = $batch->batch_number ? [$batch->batch_number] : [];
        }
        $this->ref_no = $batch->ref_no;
        $this->status = $batch->status;
        $this->remarks = $batch->remarks;

        // Load available batch numbers
        $this->loadAvailableBatchNumbers();

        // Load the saved workflow step from database
        $this->currentStep = $batch->workflow_step ?? $this->determineCurrentStep($batch);

        // Load data based on step
        if ($this->currentStep >= 2 && !empty($this->selectedBatchNumbers)) {
            $this->loadBranchesByBatch();
        }

        if ($this->currentStep >= 3) {
            $this->loadMatrixForEditing($batch);
        }

        if ($this->currentStep >= 4) {
            $this->loadScannedQuantitiesForEditing($batch);
        }

        // Open stepper
        $this->showStepper = true;

        session()->flash('message', 'Editing batch: ' . $batch->ref_no . ' (Step ' . $this->currentStep . ')');
    }

    // Add a method to load product allocations
    private function loadProductAllocations()
    {
        if (!$this->currentBatch) {
            $this->productAllocations = [];
            return;
        }

        $productData = [];
        
        foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
            foreach ($branchAllocation->items as $item) {
                $productId = $item->product_id;
                
                if (!isset($productData[$productId])) {
                    $colorName = $item->product->color ? $item->product->color->name : '';
                    $productDisplayName = $item->product->name . ($colorName ? ' ' . $colorName : '');
                    
                    $productData[$productId] = [
                        'product_id' => $item->product_id,
                        'product_name' => $productDisplayName,
                        'image' => $item->product->primary_image,
                        'quantity' => 0,
                        'unit_price' => $item->unit_price,
                        'total_value' => 0,
                        'branch_count' => 0,
                    ];
                }
                
                $productData[$productId]['quantity'] += $item->quantity;
                $productData[$productId]['total_value'] += ($item->quantity * $item->unit_price);
                $productData[$productId]['branch_count']++;
            }
        }
        
        $this->productAllocations = collect($productData)->map(function ($data) {
            return [
                'product_id' => $data['product_id'],
                'product_name' => $data['product_name'],
                'image' => $data['image'],
                'quantity' => $data['quantity'],
                'unit_price' => $data['unit_price'],
                'total_value' => $data['total_value'],
                'applied_to_branches' => $data['branch_count'] . ' ' . \Illuminate\Support\Str::plural('branch', $data['branch_count']),
            ];
        })->values()->toArray();
    }

    private function determineCurrentStep($batch)
    {
        // If batch is dispatched, go to step 4 (view only)
        if ($batch->status === 'dispatched') {
            return 4;
        }
        
        // Check if products are allocated (Step 3 complete)
        $hasProducts = false;
        foreach ($batch->branchAllocations as $branchAllocation) {
            if ($branchAllocation->items()->whereNull('box_id')->count() > 0) {
                $hasProducts = true;
                break;
            }
        }
        
        if ($hasProducts) {
            // Products allocated, go to step 4 (dispatch)
            return 4;
        }
        
        // Check if branches are added (Step 2 complete)
        if ($batch->branchAllocations->count() > 0) {
            // Branches added, go to step 3 (add products)
            return 3;
        }
        
        // Only batch created, go to step 2 (add branches)
        return 2;
    }

    private function loadMatrixForEditing($batch)
    {
        $this->matrixQuantities = [];
        $this->selectedProductIdsForAllocation = [];
        
        foreach ($batch->branchAllocations as $branchAllocation) {
            foreach ($branchAllocation->items as $item) {
                $this->matrixQuantities[$branchAllocation->id][$item->product_id] = $item->quantity;
                
                // Add to selected products
                if (!in_array($item->product_id, $this->selectedProductIdsForAllocation)) {
                    $this->selectedProductIdsForAllocation[] = $item->product_id;
                }
            }
        }
        
        // Reload product allocations
        $this->loadProductAllocations();
    }

    // Load scanned quantities for editing
    private function loadScannedQuantitiesForEditing($batch)
    {
        // Initialize scanned quantities structure
        $this->scannedQuantities = [];
        
        // You can optionally load previously scanned quantities if you're storing them
        // For now, we'll initialize them as empty for a fresh scan
        foreach ($batch->branchAllocations as $branchAllocation) {
            $this->scannedQuantities[$branchAllocation->id] = [];
            
            foreach ($branchAllocation->items as $item) {
                $this->scannedQuantities[$branchAllocation->id][$item->product_id] = 0;
            }
        }
    }
    public function loadDispatchProducts()
    {
        if (!$this->currentBatch) {
            $this->dispatchProducts = [];
            return;
        }

        // Get all branch allocation items for this batch, grouped by product
        $items = \App\Models\BranchAllocationItem::query()
            ->join('branch_allocations', 'branch_allocation_items.branch_allocation_id', '=', 'branch_allocations.id')
            ->where('branch_allocations.batch_allocation_id', $this->currentBatch->id)
            ->select(
                'branch_allocation_items.product_id',
                \DB::raw('COALESCE(branch_allocation_items.product_snapshot_name, products.name) as product_name'),
                \DB::raw('COALESCE(branch_allocation_items.product_snapshot_barcode, products.barcode) as barcode'),
                \DB::raw('SUM(branch_allocation_items.quantity) as total_quantity'),
                \DB::raw('AVG(branch_allocation_items.unit_price) as avg_unit_price'),
                \DB::raw('COUNT(DISTINCT branch_allocations.branch_id) as branch_count')
            )
            ->leftJoin('products', 'branch_allocation_items.product_id', '=', 'products.id')
            ->groupBy(
                'branch_allocation_items.product_id',
                \DB::raw('COALESCE(branch_allocation_items.product_snapshot_name, products.name)'),
                \DB::raw('COALESCE(branch_allocation_items.product_snapshot_barcode, products.barcode)')
            )
            ->get();

        $this->dispatchProducts = $items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'barcode' => $item->barcode ?? 'N/A',
                'quantity' => (int) $item->total_quantity,
                'unit_price' => (float) $item->avg_unit_price,
                'total_value' => (int) $item->total_quantity * (float) $item->avg_unit_price,
                'applied_to_branches' => (int) $item->branch_count . ' ' . \Illuminate\Support\Str::plural('branch', (int) $item->branch_count)
            ];
        })->toArray();
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            
            // Save workflow step to database
            if ($this->currentBatch) {
                $this->currentBatch->update(['workflow_step' => $this->currentStep]);
            }
            
            // Reload data for the previous step
            if ($this->currentStep === 2) {
                $this->loadBranchesByBatch();
            }
            
            if ($this->currentStep === 3) {
                $this->loadMatrix();
                
                if ($this->isEditing && $this->currentBatch) {
                    $this->loadProductAllocations();
                }
            }
        }
    }

    public function goToStep($step)
    {
        if ($step >= 1 && $step <= 4) {
            $this->currentStep = $step;

            // Load branches if going to step 2 with batch selected
            if ($step === 2 && !empty($this->batch_number)) {
                $this->loadBranchesByBatch();
            }

            // Initialize selected products if going to step 3
            if ($step === 3) {
                $this->loadMatrix();
            }
        }
    }

    public function resetStepper()
    {
        $this->currentStep = 1;
        $this->batch_number = '';
        $this->remarks = '';
        $this->ref_no = '';
        $this->status = 'draft';
        $this->filteredBranchesByBatch = [];
        $this->currentBatch = null;
        $this->selectedBranchIds = [];
        $this->branchRemarks = [];
        $this->selectedProductId = null;
        $this->productQuantity = 1;
        $this->productUnitPrice = null;
        $this->productAllocations = [];
        $this->selectedBranchAllocationId = null;
    }

    // Stepper-specific methods
    public function addProductToAllBranches()
    {
        if (!$this->currentBatch || !$this->selectedProductId) {
            session()->flash('error', 'Please select a product.');
            return;
        }

        // Validate
        $this->validate([
            'selectedProductId' => 'required|exists:products,id',
            'productQuantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($this->selectedProductId);
        $sellingPrice = $product->price ?? $product->selling_price ?? 0;

        // Add the product to all branches in the batch
        foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
            // Check for duplicate products per branch
            $existingItem = BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
                ->where('product_id', $this->selectedProductId)
                ->first();

            if ($existingItem) {
                continue; // Skip if already exists
            }

            // Create the item for this branch
            BranchAllocationItem::create([
                'branch_allocation_id' => $branchAllocation->id,
                'product_id' => $this->selectedProductId,
                'quantity' => $this->productQuantity,
                'unit_price' => $sellingPrice,
            ]);
        }

        // Store allocation info for tracking
        $this->productAllocations[] = [
            'product_id' => $this->selectedProductId,
            'product_name' => $product->name,
            'quantity' => $this->productQuantity,
            'unit_price' => $sellingPrice,
            'total_value' => $this->productQuantity * $sellingPrice,
            'applied_to_branches' => $this->currentBatch->branchAllocations->count()
        ];

        // Reset form
        $this->selectedProductId = null;
        $this->productQuantity = 1;

        // Reload batch data
        $this->loadBatchAllocations();

        session()->flash('message', 'Product added to all branches successfully.');
    }

    public function addProductToBranches()
    {
        if (!$this->currentBatch || !$this->selectedProductId) {
            session()->flash('error', 'Please select a product.');
            return;
        }

        // Validate
        $this->validate([
            'selectedProductId' => 'required|exists:products,id',
        ]);

        $product = Product::find($this->selectedProductId);
        $sellingPrice = $product->price ?? $product->selling_price ?? 0;

        $addedCount = 0;

        // Add the product to selected branches with specified quantities
        foreach ($this->branchQuantities as $branchAllocationId => $quantity) {
            $quantity = (int) $quantity;
            if ($quantity > 0) {
                // Check for duplicate products per branch
                $existingItem = BranchAllocationItem::where('branch_allocation_id', $branchAllocationId)
                    ->where('product_id', $this->selectedProductId)
                    ->first();

                if (!$existingItem) {
                    // Create the item for this branch
                    BranchAllocationItem::create([
                        'branch_allocation_id' => $branchAllocationId,
                        'product_id' => $this->selectedProductId,
                        'quantity' => $quantity,
                        'unit_price' => $sellingPrice,
                    ]);
                    $addedCount++;
                }
            }
        }

        if ($addedCount > 0) {
            // Store allocation info for tracking
            $this->productAllocations[] = [
                'product_id' => $this->selectedProductId,
                'product_name' => $product->name,
                'quantity' => 'Varies', // Since quantities vary per branch
                'unit_price' => $sellingPrice,
                'total_value' => 'Varies', // Varies per branch
                'applied_to_branches' => $addedCount
            ];

            // Reset form
            $this->selectedProductId = null;
            $this->branchQuantities = [];

            // Reload batch data
            $this->loadBatchAllocations();

            session()->flash('message', 'Product added to ' . $addedCount . ' branch(es) successfully.');
        } else {
            session()->flash('error', 'No valid quantities entered for any branch.');
        }
    }

    public function getAvailableProductsForBatchProperty()
    {
        // Return all products for the matrix
        return Product::orderBy('name')->get();
    }

    public function loadMatrix()
    {
        if (!$this->currentBatch) {
            $this->matrixQuantities = [];
            return;
        }

        // Initialize matrix with existing allocations for selected products
        $this->matrixQuantities = [];
        if (!empty($this->selectedProductIdsForAllocation)) {
            foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
                foreach ($this->selectedProductIdsForAllocation as $productId) {
                    // Find existing allocation for this product and branch
                    $existingItem = $branchAllocation->items->where('product_id', $productId)->first();
                    $this->matrixQuantities[$branchAllocation->id][$productId] = $existingItem ? $existingItem->quantity : 0;
                }
            }
        }
    }

    public function saveMatrixAllocations()
    {
        if (!$this->currentBatch) {
            session()->flash('error', 'No batch selected.');
            return;
        }

        if (empty($this->selectedProductIdsForAllocation)) {
            session()->flash('error', 'No products selected for allocation.');
            return;
        }

        $changes = 0;

        // First, delete any existing allocation items for products that are no longer selected
        $branchAllocationIds = $this->currentBatch->branchAllocations->pluck('id')->toArray();
        $deletedItems = BranchAllocationItem::whereIn('branch_allocation_id', $branchAllocationIds)
            ->whereNotIn('product_id', $this->selectedProductIdsForAllocation)
            ->delete();
        
        if ($deletedItems > 0) {
            $changes += $deletedItems;
        }

        foreach ($this->matrixQuantities as $branchAllocationId => $products) {
            foreach ($products as $productId => $quantity) {
                // Only process selected products
                if (!in_array($productId, $this->selectedProductIdsForAllocation)) {
                    continue;
                }

                $quantity = (int) $quantity;

                $existingItem = BranchAllocationItem::where('branch_allocation_id', $branchAllocationId)
                    ->where('product_id', $productId)
                    ->first();

                if ($quantity > 0) {
                    $product = Product::find($productId);
                    $sellingPrice = $product->price ?? $product->selling_price ?? 0;

                    if ($existingItem) {
                        // Update existing
                        $existingItem->update([
                            'quantity' => $quantity,
                            'unit_price' => $sellingPrice,
                        ]);
                    } else {
                        // Create new
                        BranchAllocationItem::create([
                            'branch_allocation_id' => $branchAllocationId,
                            'product_id' => $productId,
                            'quantity' => $quantity,
                            'unit_price' => $sellingPrice,
                        ]);
                    }
                    $changes++;
                } elseif ($existingItem) {
                    // Delete if quantity is 0
                    $existingItem->delete();
                    $changes++;
                }
            }
        }

        if ($changes > 0) {
            $this->loadMatrix(); // Reload to reflect changes
            $this->loadBatchAllocations();
            session()->flash('success', "Allocations updated successfully!");
        } else {
            session()->flash('info', 'No changes were made to the allocations.');
        }
    }

    public function removeProductAllocation($index)
    {
        if (isset($this->productAllocations[$index])) {
            $allocation = $this->productAllocations[$index];

            // Remove from all branches
            foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
                BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
                    ->where('product_id', $allocation['product_id'])
                    ->delete();
            }

            // Remove from tracking array
            array_splice($this->productAllocations, $index, 1);

            // Reload batch data
            $this->loadBatchAllocations();

            session()->flash('message', 'Product allocation removed from all branches.');
        }
    }

    public function dispatchBatchFromStepper()
    {
        // Validate batch exists
        if (!$this->currentBatch) {
            session()->flash('error', 'No batch selected for dispatch.');
            return;
        }

        // Validate batch status
        if ($this->currentBatch->status !== 'draft') {
            session()->flash('error', 'Only draft batches can be dispatched.');
            return;
        }

        // Validate branches exist
        if ($this->currentBatch->branchAllocations->isEmpty()) {
            session()->flash('error', 'Cannot dispatch batch without branches.');
            return;
        }

        // Validate all branches have items
        foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
            if ($branchAllocation->items()->whereNull('box_id')->count() === 0) {
                session()->flash('error', 'Cannot dispatch batch. Branch "' . $branchAllocation->branch->name . '" has no items.');
                return;
            }
        }

        // Note: Dispatch is now allowed even if all products are not fully scanned
        // This supports partial dispatching based on actual container/truck capacity

        DB::beginTransaction();
        try {
            // Update batch status and workflow step
            $this->currentBatch->update([
                'status' => 'dispatched',
                'workflow_step' => 4, // Mark workflow as complete
            ]);

            // Create sales receipts for each branch
            foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
                // Create sales receipt
                $salesReceipt = \App\Models\SalesReceipt::create([
                    'batch_allocation_id' => $this->currentBatch->id,
                    'branch_id' => $branchAllocation->branch_id,
                    'status' => 'pending',
                    'created_by' => auth()->id(), // Track who dispatched
                    'dispatched_at' => now(), // Track when dispatched
                ]);

                // Create sales receipt items (only for original allocation items)
                $originalItems = $branchAllocation->items()->whereNull('box_id')->get();
                foreach ($originalItems as $item) {
                    // Calculate scanned quantity for this product
                    $scannedQty = BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
                        ->where('product_id', $item->product_id)
                        ->whereNotNull('box_id')
                        ->sum('scanned_quantity');
                    
                    \App\Models\SalesReceiptItem::create([
                        'sales_receipt_id' => $salesReceipt->id,
                        'product_id' => $item->product_id,
                        'allocated_qty' => $item->quantity,
                        'scanned_qty' => $scannedQty, // Save actual scanned quantity
                        'received_qty' => 0,
                        'damaged_qty' => 0,
                        'missing_qty' => 0,
                        'sold_qty' => 0,
                        'status' => 'pending',
                    ]);
                }
            }

            DB::commit();
            
            // Log the dispatch activity
            activity()
                ->performedOn($this->currentBatch)
                ->causedBy(auth()->user())
                ->withProperties([
                    'batch_ref' => $this->currentBatch->ref_no,
                    'batch_number' => $this->currentBatch->batch_number,
                    'branches_count' => $this->currentBatch->branchAllocations->count(),
                    'total_items' => $this->getTotalItemsCount(),
                ])
                ->log('Batch dispatched successfully');
            
            session()->flash('message', 'Batch "' . $this->currentBatch->ref_no . '" dispatched successfully! Sales receipts have been generated for ' . $this->currentBatch->branchAllocations->count() . ' branch(es).');
            
            // Close stepper and reset
            $this->closeStepper();
            $this->loadBatchAllocations();
            
        } catch (\Exception $e) {
            DB::rollback();
            
            // Log the error
            \Log::error('Batch dispatch failed', [
                'batch_id' => $this->currentBatch->id,
                'ref_no' => $this->currentBatch->ref_no,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            session()->flash('error', 'Failed to dispatch batch: ' . $e->getMessage());
        }
    }

    

    public function getProductAllocationsForDispatch()
    {
        if (!$this->currentBatch) {
            return [];
        }

        // Get all product allocations grouped by product
        $allocations = DB::table('branch_allocation_products as bap')
            ->join('branch_allocations as ba', 'bap.branch_allocation_id', '=', 'ba.id')
            ->join('products as p', 'bap.product_id', '=', 'p.id')
            ->where('ba.batch_allocation_id', $this->currentBatch->id)
            ->select(
                'p.id as product_id',
                'p.name as product_name',
                'p.barcode',
                DB::raw('SUM(bap.quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT ba.branch_id) as branch_count')
            )
            ->groupBy('p.id', 'p.name', 'p.barcode')
            ->get();

        return $allocations->map(function ($allocation) {
            return [
                'product_id' => $allocation->product_id,
                'product_name' => $allocation->product_name,
                'barcode' => $allocation->barcode,
                'quantity' => $allocation->total_quantity,
                'applied_to_branches' => $allocation->branch_count . ' branches'
            ];
        })->toArray();
    }

    public function generateRefNo()
    {
        $lastBatch = BatchAllocation::orderBy('id', 'desc')->first();
        $nextNumber = $lastBatch ? $lastBatch->id + 1 : 1;
        return 'A' . now()->format('Ymd') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function openStepper()
    {
        // Reset to create mode
        $this->isEditing = false;
        $this->editingBatchId = null;
        $this->currentBatch = null;
        $this->currentStep = 1; // Start at step 1

        // Reset form fields to defaults
        $this->status = 'draft';
        $this->ref_no = $this->generateRefNo();
        $this->batch_number = '';
        $this->selectedBatchNumbers = []; // Reset selected batches
        $this->remarks = '';

        // Reset allocations
        $this->selectedProductIdsForAllocation = [];
        $this->matrixQuantities = [];
        $this->productAllocations = [];
        $this->scannedQuantities = [];
        $this->activeBranchId = null;
        $this->barcodeInput = '';
        $this->scanFeedback = '';
        $this->lastScannedBarcode = '';
        $this->branchRemarks = [];

        // Reset filtering - don't show products by default
        $this->selectedCategoryId = null;
        $this->selectedProductFilterName = null;
        $this->showAllProducts = false; // Changed to false to not show products by default
        $this->filteredProducts = [];

        // Load fresh data
        $this->loadAvailableBatchNumbers();
        $this->loadAvailableProducts();

        // Show the stepper
        $this->showStepper = true;
    }

    public function closeStepper()
    {
        $this->showStepper = false;
        $this->currentStep = 1;
        $this->isEditing = false;
        $this->editingBatchId = null;
        
        // Reset all form fields
        $this->reset([
            'batch_number',
            'ref_no',
            'remarks',
            'status',
            'currentBatch',
            'branchRemarks',
            'selectedProductIdsForAllocation',
            'matrixQuantities',
            'productAllocations',
            'activeBranchId',
            'scannedQuantities',
            'barcodeInput',
            'scanFeedback',
            'lastScannedBarcode'
        ]);

        // Reset to defaults
        $this->status = 'draft';
        $this->ref_no = 'WT-' . now()->format('Ymd') . '-' . str_pad(
            BatchAllocation::whereDate('created_at', today())->count() + 1,
            4,
            '0',
            STR_PAD_LEFT
        );
        
        // Reload batch allocations list
        $this->loadBatchAllocations();
        
        session()->flash('message', 'Modal Closed, Process Complete!');
    }

    public function createBatch()
    {
        // Validation rules
        $rules = [
            'remarks' => 'nullable|string|max:1000',
            'selectedBatchNumbers' => 'required|array|min:1',
        ];

        if (!$this->isEditing) {
            $rules['ref_no'] = 'required|string|unique:batch_allocations,ref_no';
        } else {
            $rules['ref_no'] = 'required|string|unique:batch_allocations,ref_no,' . $this->editingBatchId;
        }

        $this->validate($rules);

        if ($this->isEditing && $this->currentBatch) {
            // UPDATE EXISTING BATCH
            $updateData = [
                'batch_number' => implode(', ', $this->selectedBatchNumbers),
                'remarks' => $this->remarks,
                'status' => $this->status,
                'workflow_step' => $this->currentStep, // Save current step
            ];
            
            // Only update transaction_date if it exists
            if (isset($this->transaction_date)) {
                $updateData['transaction_date'] = $this->transaction_date;
            }
            
            $this->currentBatch->update($updateData);

            // If batch numbers changed, update branch allocations
            $this->updateBranchAllocationsForBatch();

            $this->currentBatch->refresh();
            $this->loadBranchesByBatch();

            session()->flash('message', 'Batch details updated successfully.');

        } else {
            // CREATE NEW BATCH
            $batch = BatchAllocation::create([
                'ref_no' => $this->ref_no,
                'batch_number' => implode(', ', $this->selectedBatchNumbers),
                'remarks' => $this->remarks,
                'status' => $this->status,
                'workflow_step' => 1, // Start at step 1
            ]);

            // Create branch allocations from all selected batches
            if (!empty($this->selectedBatchNumbers)) {
                $branches = Branch::whereIn('batch', $this->selectedBatchNumbers)->get();

                foreach ($branches as $branch) {
                    BranchAllocation::create([
                        'batch_allocation_id' => $batch->id,
                        'branch_id' => $branch->id,
                        'remarks' => $this->branchRemarks[$branch->id] ?? null,
                        'status' => 'pending',
                    ]);
                }
            }

            $this->currentBatch = $batch;
            $this->editingBatchId = $batch->id;
            $this->isEditing = true;

            $this->loadBranchesByBatch();

            session()->flash('message', 'Batch allocation created successfully with branches from batches: ' . implode(', ', $this->selectedBatchNumbers));

            // Move to step 2
            $this->currentStep = 2;

            // Update workflow_step in database
            $this->currentBatch->update(['workflow_step' => 2]);

            $this->loadMatrix();
        }

        // Reload batch allocations
        $this->loadBatchAllocations();
    }

    // Separate method for updating batch details
    public function updateBatchDetails()
    {
        $this->validate([
            'selectedBatchNumbers' => 'required|array|min:1',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $this->currentBatch->update([
            'batch_number' => implode(', ', $this->selectedBatchNumbers),
            'remarks' => $this->remarks,
            'status' => $this->status,
        ]);

        // Update branch allocations if batch numbers changed
        $this->updateBranchAllocationsForBatch();

        $this->currentBatch->refresh();
        $this->loadBranchesByBatch();
        
        session()->flash('message', 'Batch details updated successfully.');
        
        // Reload batch allocations list
        $this->loadBatchAllocations();
        
        // Update batch step tracking without changing current step
        if ($this->currentBatch) {
            $this->batchSteps[$this->currentBatch->id] = $this->currentStep;
        }
    }

    public function openAddBranchesModal(BatchAllocation $batch)
    {
        $this->selectedBatch = $batch;
        $this->showAddBranchesModal = true;
        
        // Get branches not already in this batch
        $existingBranchIds = $batch->branchAllocations->pluck('branch_id')->toArray();
        $this->availableBranches = Branch::whereNotIn('id', $existingBranchIds)->orderBy('name')->get();
    }

    public function closeAddBranchesModal()
    {
        $this->showAddBranchesModal = false;
        $this->selectedBatch = null;
        $this->selectedBranchIds = [];
        $this->branchRemarks = [];
    }

    public function addBranchesToBatch()
    {
        if (empty($this->selectedBranchIds)) {
            session()->flash('error', 'Please select at least one branch.');
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($this->selectedBranchIds as $branchId) {
                BranchAllocation::create([
                    'batch_allocation_id' => $this->selectedBatch->id,
                    'branch_id' => $branchId,
                    'remarks' => $this->branchRemarks[$branchId] ?? null,
                    'status' => 'pending',
                ]);
            }

            DB::commit();
            session()->flash('message', 'Branches added to batch successfully.');
            $this->closeAddBranchesModal();
            $this->loadBatchAllocations();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Failed to add branches: ' . $e->getMessage());
        }
    }

    public function openAddItemsModal(BranchAllocation $branchAllocation)
    {
        $this->selectedBranchAllocation = $branchAllocation;
        $this->showAddItemsModal = true;
        $this->availableProducts = Product::orderBy('name')->get();
    }

    public function closeAddItemsModal()
    {
        $this->showAddItemsModal = false;
        $this->selectedBranchAllocation = null;
        $this->selectedProductId = null;
        $this->productQuantity = 1;
        $this->productUnitPrice = null;
    }

    public function addItemToBranch()
    {
        // Custom validation for duplicate products
        if ($this->selectedProductId && $this->selectedBranchAllocation) {
            $existingItem = BranchAllocationItem::where('branch_allocation_id', $this->selectedBranchAllocation->id)
                ->where('product_id', $this->selectedProductId)
                ->first();

            if ($existingItem) {
                $this->addError('selectedProductId', 'This product has already been added to this branch allocation.');
                return;
            }
        }

        $this->validate([
            'selectedProductId' => 'required|exists:products,id',
            'productQuantity' => 'required|integer|min:1',
            'productUnitPrice' => 'nullable|numeric|min:0',
        ]);

        BranchAllocationItem::create([
            'branch_allocation_id' => $this->selectedBranchAllocation->id,
            'product_id' => $this->selectedProductId,
            'quantity' => $this->productQuantity,
            'unit_price' => $this->productUnitPrice,
        ]);

        session()->flash('message', 'Item added to branch successfully.');
        $this->closeAddItemsModal();
        $this->loadBatchAllocations();
    }

    public function openEditItemModal(BranchAllocationItem $item)
    {
        if ($item->branchAllocation->batchAllocation->status !== 'draft') {
            session()->flash('error', 'Cannot edit item from non-draft batch.');
            return;
        }

        $this->selectedEditItem = $item;
        $this->showEditItemModal = true;
        $this->editProductQuantity = $item->quantity;
    }

    public function closeEditItemModal()
    {
        $this->showEditItemModal = false;
        $this->selectedEditItem = null;
        $this->editProductQuantity = 1;
    }

    public function updateItem()
    {
        if (!$this->selectedEditItem) {
            session()->flash('error', 'No item selected for editing.');
            return;
        }

        $this->validate([
            'editProductQuantity' => 'required|integer|min:1',
        ]);

        $this->selectedEditItem->update([
            'quantity' => $this->editProductQuantity,
        ]);

        session()->flash('message', 'Item updated successfully.');
        $this->closeEditItemModal();
        $this->loadBatchAllocations();
    }

    public function removeBranch(BranchAllocation $branchAllocation)
    {
        if ($branchAllocation->batchAllocation->status !== 'draft') {
            session()->flash('error', 'Cannot remove branch from non-draft batch.');
            return;
        }

        $branchAllocation->delete();
        session()->flash('message', 'Branch removed from batch successfully.');
        $this->loadBatchAllocations();
    }

    public function removeItem(BranchAllocationItem $item)
    {
        if ($item->branchAllocation->batchAllocation->status !== 'draft') {
            session()->flash('error', 'Cannot remove item from non-draft batch.');
            return;
        }

        $item->delete();
        session()->flash('message', 'Item removed successfully.');
        $this->loadBatchAllocations();
    }

    public function dispatchBatch(BatchAllocation $batch)
    {
        if ($batch->status !== 'draft') {
            session()->flash('error', 'Only draft batches can be dispatched.');
            return;
        }

        if ($batch->branchAllocations->isEmpty()) {
            session()->flash('error', 'Cannot dispatch batch without branches.');
            return;
        }

        foreach ($batch->branchAllocations as $branchAllocation) {
            if ($branchAllocation->items()->whereNull('box_id')->count() === 0) {
                session()->flash('error', 'Cannot dispatch batch. Branch "' . $branchAllocation->branch->name . '" has no items.');
                return;
            }
        }

        DB::beginTransaction();
        try {
            // Update batch status
            $batch->update(['status' => 'dispatched']);

            // Create sales receipts for each branch
            foreach ($batch->branchAllocations as $branchAllocation) {
                // Create sales receipt
                $salesReceipt = \App\Models\SalesReceipt::create([
                    'batch_allocation_id' => $batch->id,
                    'branch_id' => $branchAllocation->branch_id,
                    'status' => 'pending',
                ]);

                // Create sales receipt items (only for original allocation items)
                $originalItems = $branchAllocation->items()->whereNull('box_id')->get();
                foreach ($originalItems as $item) {
                    // Calculate scanned quantity for this product
                    $scannedQty = BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
                        ->where('product_id', $item->product_id)
                        ->whereNotNull('box_id')
                        ->sum('scanned_quantity');
                    
                    \App\Models\SalesReceiptItem::create([
                        'sales_receipt_id' => $salesReceipt->id,
                        'product_id' => $item->product_id,
                        'allocated_qty' => $item->quantity,
                        'scanned_qty' => $scannedQty, // Save actual scanned quantity
                        'received_qty' => 0,
                        'damaged_qty' => 0,
                        'missing_qty' => 0,
                        'sold_qty' => 0,
                        'status' => 'pending',
                    ]);
                }
            }

            DB::commit();
            session()->flash('message', 'Batch dispatched successfully and sales allocations have been generated.');
            $this->loadBatchAllocations();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Failed to dispatch batch: ' . $e->getMessage());
        }
    }

    public function removeBatch(BatchAllocation $batch)
    {
        $batch->delete();
        session()->flash('message', 'Batch deleted successfully.');
        $this->loadBatchAllocations();
    }

    public function render()
    {
        return view('livewire.pages.allocation.warehouse', [
            'batches' => $this->batchAllocations
        ]);
    }

    public function toggleBatch($batchId)
    {
        $this->openBatches[$batchId] = !$this->openBatches[$batchId];
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->dateFrom = '';
        $this->dateTo = '';
    }

    public function updatedSearch()
    {
        $this->loadBatchAllocations();
    }

    public function updatedDateFrom()
    {
        $this->loadBatchAllocations();
    }

    public function updatedDateTo()
    {
        $this->loadBatchAllocations();
    }

    public function updatedSelectedProductIdsForAllocation()
    {
        $this->loadMatrix();
    }

    // VDR Export Methods
    public function openVDRPreview(BatchAllocation $batch)
    {
        $this->selectedBatchForVDR = $batch;
        $this->showVDRPreviewModal = true;
        // Set default vendor info
        $this->vendorCode = '104148'; // Default vendor code
        $this->vendorName = 'JKF CORP.'; // Default vendor name
    }

    public function closeVDRPreview()
    {
        $this->showVDRPreviewModal = false;
        $this->selectedBatchForVDR = null;
        $this->vendorCode = '';
        $this->vendorName = '';
    }

    public function exportToExcel()
    {
        if (!$this->selectedBatchForVDR) {
            session()->flash('error', 'No batch selected for export.');
            return;
        }

        // Validation
        $this->validate([
            'vendorCode' => 'required|string',
            'vendorName' => 'required|string',
        ]);

        try {
            // Generate file name
            $fileName = 'VDR_' . $this->selectedBatchForVDR->ref_no . '_' . now()->format('YmdHis') . '.csv';
            
            // Generate CSV content
            $csvContent = $this->generateVDRCSVContent();
            
            // Set session data for download
            session(['vdr_csv_content' => $csvContent]);
            session(['vdr_filename' => $fileName]);
            
            // For Livewire, we'll redirect to a download route or use JavaScript
            // For now, return the data and let JavaScript handle the download
            $this->dispatch('download-vdr', content: $csvContent, filename: $fileName);
            
            // Store the data temporarily for backup download
            cache()->put('vdr_export_' . time(), [
                'content' => $csvContent,
                'filename' => $fileName
            ], 300); // Store for 5 minutes
            
            session()->flash('message', 'VDR CSV file should download automatically. If it doesn\'t, check your browser\'s download folder or downloads bar.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export VDR: ' . $e->getMessage());
        }
    }

    private function generateVDRCSVContent()
    {
        $batch = $this->selectedBatchForVDR;
        $csvData = [];
        
        // Header information
        $csvData[] = [
            'DR#' => $batch->ref_no,
            'STORE CODE' => '',
            'STORE NAME' => '',
            'DP' => '',
            'SD' => '',
            'CL' => '',
            'SKU #' => '',
            'QTY' => ''
        ];

        $totalQty = 0;
        $uniqueSkus = collect();

        // Process each branch allocation
        foreach ($batch->branchAllocations as $branchAllocation) {
            $branch = $branchAllocation->branch;
            $storeCode = $branch->code ?? '';
            $storeName = $branch->name ?? '';

            // Process each item
            foreach ($branchAllocation->items as $item) {
                $product = $item->product;
                $skuNumber = $product->sku ?? $product->id;
                
                $csvData[] = [
                    'DR#' => $batch->ref_no,
                    'STORE CODE' => $storeCode,
                    'STORE NAME' => $storeName,
                    'DP' => '04', // Default values
                    'SD' => '10',
                    'CL' => '72007',
                    'SKU #' => $skuNumber,
                    'QTY' => $item->quantity
                ];

                $totalQty += $item->quantity;
                $uniqueSkus->push($skuNumber);
            }
        }

        // Add summary rows
        $csvData[] = ['', '', '', '', '', '', '', '']; // Empty row
        $csvData[] = [
            'DR#' => '',
            'STORE CODE' => '',
            'STORE NAME' => '',
            'DP' => '',
            'SD' => '',
            'CL' => '',
            'SKU #' => 'TOTAL QTY: ' . $totalQty,
            'QTY' => ''
        ];
        $csvData[] = [
            'DR#' => '',
            'STORE CODE' => '',
            'STORE NAME' => '',
            'DP' => '',
            'SD' => '',
            'CL' => '',
            'SKU #' => 'TOTAL SKU/S: ' . $uniqueSkus->unique()->count(),
            'QTY' => ''
        ];

        // Create CSV content
        $csvContent = '';
        foreach ($csvData as $row) {
            $csvContent .= implode(',', array_map(function($field) {
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row)) . "\n";
        }

        return $csvContent;
    }

    public function manualDownload()
    {
        if (!$this->selectedBatchForVDR) {
            session()->flash('error', 'No batch selected for download.');
            return;
        }

        // Validation
        $this->validate([
            'vendorCode' => 'required|string',
            'vendorName' => 'required|string',
        ]);

        try {
            // Generate CSV content
            $csvContent = $this->generateVDRCSVContent();
            $fileName = 'VDR_' . $this->selectedBatchForVDR->ref_no . '_' . now()->format('YmdHis') . '.csv';
            
            // Store in session for direct download
            session(['vdr_csv_content' => $csvContent]);
            session(['vdr_filename' => $fileName]);
            
            // Close modal and show success
            $this->closeVDRPreview();
            session()->flash('message', 'VDR file is ready for download. Please check your browser downloads or Downloads folder.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate VDR: ' . $e->getMessage());
        }
    }

    public function printVDR()
    {
        if (!$this->selectedBatchForVDR) {
            session()->flash('error', 'No batch selected for printing.');
            return;
        }

        // Validation
        $this->validate([
            'vendorCode' => 'required|string',
            'vendorName' => 'required|string',
        ]);

        try {
            // Open print window
            $this->dispatch('open-vdr-print', batchId: $this->selectedBatchForVDR->id);
            session()->flash('message', 'Opening VDR print view in new window...');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to open VDR print view: ' . $e->getMessage());
        }
    }

    public function exportVDRToExcel()
    {
        if (!$this->selectedBatchForVDR) {
            session()->flash('error', 'No batch selected for export.');
            return;
        }

        // Validation
        $this->validate([
            'vendorCode' => 'required|string',
            'vendorName' => 'required|string',
        ]);

        try {
            // Open Excel export in new window (this will trigger download)
            $excelUrl = route('allocation.vdr.excel', [
                'batchId' => $this->selectedBatchForVDR->id,
                'vendor_code' => urlencode($this->vendorCode),
                'vendor_name' => urlencode($this->vendorName),
            ]);

            $this->dispatch('open-excel-download', url: $excelUrl);
            session()->flash('message', 'Opening VDR Excel export in new window...');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export VDR to Excel: ' . $e->getMessage());
        }
    }

    public function exportAllocationToPDF()
    {
        if (!$this->currentBatch) {
            session()->flash('error', 'No batch selected for export.');
            return;
        }

        try {
            // Generate PDF URL for allocation matrix
            $pdfUrl = route('allocation.matrix.pdf', [
                'batchId' => $this->currentBatch->id
            ]);

            // Dispatch event to open PDF in new window
            $this->dispatch('open-pdf-download', url: $pdfUrl);
            session()->flash('message', 'Opening allocation matrix PDF in new window...');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export allocation to PDF: ' . $e->getMessage());
        }
    }

    public function generateDeliveryReceipt($branchAllocationId)
    {
        try {
            // Get the branch allocation to validate scanning completion
            $branchAllocation = \App\Models\BranchAllocation::find($branchAllocationId);

            if (!$branchAllocation) {
                session()->flash('error', 'Branch allocation not found.');
                return;
            }

            // Validate that all items are fully scanned
            $allItemsScanned = true;
            $incompleteItems = [];

            foreach ($branchAllocation->items as $item) {
                $scannedQty = $item->scanned_quantity ?? 0;
                $allocatedQty = $item->quantity;

                if ($scannedQty < $allocatedQty) {
                    $allItemsScanned = false;
                    $incompleteItems[] = $item->product->name . " (" . $scannedQty . "/" . $allocatedQty . ")";
                }
            }

            if (!$allItemsScanned) {
                $errorMessage = "Cannot generate delivery receipt. The following items are not fully scanned: " . implode(", ", $incompleteItems);
                session()->flash('error', $errorMessage);
                return;
            }

            // Generate the delivery receipt URL
            $deliveryReceiptUrl = route('allocation.delivery-receipt.generate', [
                'branchAllocationId' => $branchAllocationId
            ]);

            $branchName = $branchAllocation->branch->name ?? 'Unknown';
            $refNo = $branchAllocation->batchAllocation->ref_no ?? 'Unknown';

            // Clean filename from special characters
            $cleanBranchName = preg_replace('/[^\w\-_.]/', '_', $branchName);
            $cleanRefNo = preg_replace('/[^\w\-_.]/', '_', $refNo);

            $filename = "delivery_receipt_{$cleanBranchName}_{$cleanRefNo}_" . now()->format('Y-m-d_H-i-s') . '.pdf';

            // Dispatch event to JavaScript to handle download
            $this->dispatch('download-delivery-receipt', url: $deliveryReceiptUrl, filename: $filename);

            session()->flash('message', 'Delivery receipt generation started. PDF should download automatically.');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate delivery receipt: ' . $e->getMessage());
        }
    }
}
