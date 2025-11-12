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

    // Add branches fields
    public $availableBranches = [];
    public $selectedBranchIds = [];
    public $branchRemarks = [];

    // Add items fields
    public $availableProducts = [];
    public $selectedProductId = null;
    public $productQuantity = 1;
    public $productUnitPrice = null;

    // Edit item fields
    public $editProductQuantity = 1;
    public $editProductUnitPrice = null;

    public function mount()
    {
        $this->loadBatchAllocations();
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

    public function generateRefNo()
    {
        $lastBatch = BatchAllocation::orderBy('id', 'desc')->first();
        $nextNumber = $lastBatch ? $lastBatch->id + 1 : 1;
        return 'WT-' . now()->format('Ymd') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function openCreateBatchModal()
    {
        $this->showCreateBatchModal = true;
        $this->ref_no = $this->generateRefNo();
    }

    public function closeCreateBatchModal()
    {
        $this->showCreateBatchModal = false;
        $this->reset(['transaction_date', 'remarks', 'ref_no']);
    }

    public function createBatch()
    {
        $this->validate([
            'transaction_date' => 'required|date',
            'remarks' => 'nullable|string|max:1000',
            'ref_no' => 'required|string|unique:batch_allocations,ref_no',
        ]);

        BatchAllocation::create([
            'transaction_date' => $this->transaction_date,
            'remarks' => $this->remarks,
            'ref_no' => $this->ref_no,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Batch allocation created successfully.');
        $this->closeCreateBatchModal();
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
