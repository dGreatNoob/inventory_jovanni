<?php

namespace App\Livewire\Pages\Allocation;

use App\Models\BatchAllocation;
use App\Models\Branch;
use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
#[Title('Allocation - Warehouse')]
class Warehouse extends Component
{
    public $batchAllocations = [];
    public $openBatches = []; // Track which batches are open/closed
    public $selectedBatch = null;
    public $showCreateBatchModal = false;
    public $showAddBranchesModal = false;
    public $showAddItemsModal = false;
    public $showEditItemModal = false;
    public $selectedBranchAllocation = null;
    public $selectedEditItem = null;

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
    public $transaction_date;
    public $remarks;
    public $ref_no;
    public $status = 'draft';
    public $batch_number = ''; // New field for batch number selection

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

    // Add branches fields
    public $availableBranches = [];
    public $selectedBranchIds = [];
    public $branchRemarks = [];

    // Edit item fields
    public $editProductQuantity = 1;
    public $editProductUnitPrice = null;

    public function mount()
    {
        $this->loadBatchAllocations();
        $this->loadAvailableBatchNumbers();
        $this->loadAvailableProducts();
        $this->showStepper = true;
        $this->ref_no = $this->generateRefNo();
    }

    public function loadBatchAllocations()
    {
        $query = BatchAllocation::with([
            'branchAllocations.branch',
            'branchAllocations.items.product'
        ])->orderBy('created_at', 'desc');

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('ref_no', 'like', '%' . $this->search . '%')
                  ->orWhere('remarks', 'like', '%' . $this->search . '%')
                  ->orWhereHas('branchAllocations.branch', function ($branchQuery) {
                      $branchQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply date range filters
        if ($this->dateFrom) {
            $query->where('transaction_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('transaction_date', '<=', $this->dateTo);
        }

        $this->batchAllocations = $query->get();
        
        // Initialize open states for all batches (default to closed)
        foreach ($this->batchAllocations as $batch) {
            if (!isset($this->openBatches[$batch->id])) {
                $this->openBatches[$batch->id] = false; // Default to closed
            }
        }
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

    public function loadAvailableProducts()
    {
        $this->availableProducts = Product::orderBy('name')->get();
    }

    public function loadBranchesByBatch()
    {
        if (empty($this->batch_number)) {
            $this->filteredBranchesByBatch = [];
            return;
        }

        $this->filteredBranchesByBatch = Branch::where('batch', $this->batch_number)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    // Stepper Navigation Methods
    public function nextStep()
    {
        if ($this->currentStep < 4) {
            $this->currentStep++;

            // When moving to step 2, load branches based on selected batch
            if ($this->currentStep === 2 && !empty($this->batch_number)) {
                $this->loadBranchesByBatch();
            }

            // When moving to step 3, initialize selected products for allocation
            if ($this->currentStep === 3) {
                $this->selectedProductIdsForAllocation = $this->availableProductsForBatch->pluck('id')->toArray();
                $this->loadMatrix();
            }
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
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
                $this->selectedProductIdsForAllocation = $this->availableProductsForBatch->pluck('id')->toArray();
                $this->loadMatrix();
            }
        }
    }

    public function resetStepper()
    {
        $this->currentStep = 1;
        $this->batch_number = '';
        $this->transaction_date = '';
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
            session()->flash('message', 'Allocations updated successfully.');
        } else {
            session()->flash('message', 'No changes made.');
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
        if (!$this->currentBatch) {
            session()->flash('error', 'No batch selected for dispatch.');
            return;
        }

        if ($this->currentBatch->status !== 'draft') {
            session()->flash('error', 'Only draft batches can be dispatched.');
            return;
        }

        if ($this->currentBatch->branchAllocations->isEmpty()) {
            session()->flash('error', 'Cannot dispatch batch without branches.');
            return;
        }

        foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
            if ($branchAllocation->items->isEmpty()) {
                session()->flash('error', 'Cannot dispatch batch. Branch "' . $branchAllocation->branch->name . '" has no items.');
                return;
            }
        }

        DB::beginTransaction();
        try {
            // Update batch status
            $this->currentBatch->update(['status' => 'dispatched']);

            // Create sales receipts for each branch
            foreach ($this->currentBatch->branchAllocations as $branchAllocation) {
                // Create sales receipt
                $salesReceipt = \App\Models\SalesReceipt::create([
                    'batch_allocation_id' => $this->currentBatch->id,
                    'branch_id' => $branchAllocation->branch_id,
                    'status' => 'pending',
                ]);

                // Create sales receipt items
                foreach ($branchAllocation->items as $item) {
                    \App\Models\SalesReceiptItem::create([
                        'sales_receipt_id' => $salesReceipt->id,
                        'product_id' => $item->product_id,
                        'allocated_qty' => $item->quantity,
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
            
            // Close stepper and reset
            $this->closeStepper();
            $this->loadBatchAllocations();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Failed to dispatch batch: ' . $e->getMessage());
        }
    }

    public function generateRefNo()
    {
        $lastBatch = BatchAllocation::orderBy('id', 'desc')->first();
        $nextNumber = $lastBatch ? $lastBatch->id + 1 : 1;
        return 'WT-' . now()->format('Ymd') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function openStepper()
    {
        $this->showStepper = true;
        $this->resetStepper();
        $this->ref_no = $this->generateRefNo();
        $this->loadAvailableBatchNumbers();
        $this->loadAvailableProducts();
    }

    public function closeStepper()
    {
        $this->showStepper = false;
        $this->resetStepper();
        $this->reset(['transaction_date', 'remarks', 'ref_no']);
    }

    public function createBatch()
    {
        $this->validate([
            'transaction_date' => 'required|date',
            'remarks' => 'nullable|string|max:1000',
            'ref_no' => 'required|string|unique:batch_allocations,ref_no',
            'batch_number' => 'required|string|exists:branches,batch',
        ]);

        // Create the batch allocation
        $batch = BatchAllocation::create([
            'transaction_date' => $this->transaction_date,
            'remarks' => $this->remarks,
            'ref_no' => $this->ref_no,
            'status' => $this->status,
        ]);

        // Automatically create branch allocations for all branches in the selected batch
        if (!empty($this->batch_number)) {
            $branches = Branch::where('batch', $this->batch_number)->get();

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
        $this->loadMatrix(); // Load the allocation matrix
        session()->flash('message', 'Batch allocation created successfully with branches from batch: ' . $this->batch_number);
        $this->nextStep(); // Move to next step
        $this->loadBatchAllocations();
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
        $this->editProductUnitPrice = $item->unit_price;
    }

    public function closeEditItemModal()
    {
        $this->showEditItemModal = false;
        $this->selectedEditItem = null;
        $this->editProductQuantity = 1;
        $this->editProductUnitPrice = null;
    }

    public function updateItem()
    {
        if (!$this->selectedEditItem) {
            session()->flash('error', 'No item selected for editing.');
            return;
        }

        $this->validate([
            'editProductQuantity' => 'required|integer|min:1',
            'editProductUnitPrice' => 'nullable|numeric|min:0',
        ]);

        $this->selectedEditItem->update([
            'quantity' => $this->editProductQuantity,
            'unit_price' => $this->editProductUnitPrice,
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
            if ($branchAllocation->items->isEmpty()) {
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

                // Create sales receipt items
                foreach ($branchAllocation->items as $item) {
                    \App\Models\SalesReceiptItem::create([
                        'sales_receipt_id' => $salesReceipt->id,
                        'product_id' => $item->product_id,
                        'allocated_qty' => $item->quantity,
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
        if ($batch->status !== 'draft') {
            session()->flash('error', 'Cannot delete non-draft batch.');
            return;
        }

        $batch->delete();
        session()->flash('message', 'Batch deleted successfully.');
        $this->loadBatchAllocations();
    }

    public function render()
    {
        return view('livewire.pages.allocation.warehouse');
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
            'EXP. DELIVERY DATE' => \Carbon\Carbon::parse($batch->transaction_date)->format('m/d/y'),
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
                    'EXP. DELIVERY DATE' => \Carbon\Carbon::parse($batch->transaction_date)->format('m/d/y'),
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
        $csvData[] = ['', '', '', '', '', '', '', '', '']; // Empty row
        $csvData[] = [
            'DR#' => '',
            'STORE CODE' => '',
            'STORE NAME' => '',
            'EXP. DELIVERY DATE' => '',
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
            'EXP. DELIVERY DATE' => '',
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
}
