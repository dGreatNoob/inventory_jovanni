<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use App\Models\Branch;
use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;
use App\Models\Product;
use App\Models\BranchTransfer as BranchTransferModel;
use App\Models\BranchTransferItem;
use Illuminate\Support\Facades\Auth;

class BranchTransfer extends Component
{
    // Stepper state
    public $currentStep = 1;
    public $showStepper = false;

    // Step 1: Source Branch Selection
    public $sourceBranchId = null;
    public $availableBranches = [];

    // Step 2: Product Selection
    public $selectedProductIds = [];
    public $availableProducts = [];

    // Step 3: Destination Branch Selection
    public $destinationBranchId = null;
    public $availableDestinationBranches = [];

    // Step 4: Transfer Execution
    public $transferQuantities = [];
    public $selectedProducts = [];

    // Computed properties
    public $sourceBranch = null;
    public $destinationBranch = null;

    // Transfer history
    public $transfers = [];
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';

    public function mount()
    {
        $this->loadAvailableBranches();
        $this->loadTransfers();
    }

    /**
     * Load branches that have received products
     */
    protected function loadAvailableBranches()
    {
        $this->availableBranches = Branch::whereHas('branchAllocations.items', function ($q) {
            $q->where('box_id', null); // Only unpacked items
        })
        ->with(['branchAllocations.items' => function ($q) {
            $q->where('box_id', null);
        }])
        ->get()
        ->filter(function ($branch) {
            // Only include branches that have items with "received" status
            return $branch->branchAllocations->some(function ($allocation) {
                return $allocation->items->some(function ($item) use ($allocation) {
                    $totalScanned = BranchAllocationItem::where('branch_allocation_id', $allocation->id)
                        ->where('product_id', $item->product_id)
                        ->whereNotNull('box_id')
                        ->sum('scanned_quantity');
                    return $totalScanned > 0; // Has received items
                });
            });
        })
        ->values();
    }

    /**
     * Open the stepper
     */
    public function openStepper()
    {
        $this->showStepper = true;
        $this->currentStep = 1;
        $this->resetForm();
    }

    /**
     * Close the stepper
     */
    public function closeStepper()
    {
        $this->showStepper = false;
        $this->currentStep = 1;
        $this->resetForm();
    }

    /**
     * Reset form data
     */
    protected function resetForm()
    {
        $this->sourceBranchId = null;
        $this->selectedProductIds = [];
        $this->destinationBranchId = null;
        $this->transferQuantities = [];
        $this->availableProducts = [];
        $this->availableDestinationBranches = [];
        $this->selectedProducts = [];
        $this->sourceBranch = null;
        $this->destinationBranch = null;
    }

    /**
     * Go to next step
     */
    public function nextStep()
    {
        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }

    /**
     * Go to previous step
     */
    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    /**
     * Step 1: Select source branch
     */
    public function selectSourceBranch()
    {
        $this->validate([
            'sourceBranchId' => 'required|exists:branches,id',
        ]);

        $this->sourceBranch = Branch::find($this->sourceBranchId);
        $this->loadAvailableProducts();
        $this->nextStep();
    }

    /**
     * Load products available for transfer from source branch
     */
    protected function loadAvailableProducts()
    {
        if (!$this->sourceBranchId) return;

        $products = [];

        $allocations = BranchAllocation::where('branch_id', $this->sourceBranchId)
            ->with(['items.product.color'])
            ->get();

        foreach ($allocations as $allocation) {
            foreach ($allocation->items->where('box_id', null) as $item) {
                // Calculate total scanned quantity for this product
                $totalScanned = BranchAllocationItem::where('branch_allocation_id', $allocation->id)
                    ->where('product_id', $item->product_id)
                    ->whereNotNull('box_id')
                    ->sum('scanned_quantity');

                // Only include products that have been received (scanned)
                if ($totalScanned > 0) {
                    $product = $item->product;
                    $products[] = [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'name' => $item->getDisplayNameAttribute(),
                        'sku' => $item->getDisplaySkuAttribute(),
                        'available_quantity' => $totalScanned,
                        'color' => $product->color,
                    ];
                }
            }
        }

        $this->availableProducts = collect($products)->unique('product_id')->values()->all();
    }

    /**
     * Step 3: Select destination branch
     */
    public function selectDestinationBranch()
    {
        $this->validate([
            'destinationBranchId' => 'required|exists:branches,id|different:sourceBranchId',
        ]);

        $this->destinationBranch = Branch::find($this->destinationBranchId);
        $this->loadSelectedProducts();
        $this->nextStep();
    }

    /**
     * Load selected products data
     */
    protected function loadSelectedProducts()
    {
        if (empty($this->selectedProductIds)) return;

        $this->selectedProducts = collect($this->availableProducts)
            ->whereIn('id', $this->selectedProductIds)
            ->values()
            ->all();
    }

    /**
     * Load available destination branches (all branches except source)
     */
    public function updatedSourceBranchId()
    {
        $this->availableDestinationBranches = Branch::where('id', '!=', $this->sourceBranchId)
            ->get();
    }

    /**
     * Execute the transfer
     */
    public function executeTransfer()
    {
        $this->validate([
            'transferQuantities' => 'required|array',
            'transferQuantities.*' => 'nullable|integer|min:0',
        ]);

        // Validate quantities don't exceed available stock
        foreach ($this->selectedProducts as $product) {
            $transferQty = $this->transferQuantities[$product['product_id']] ?? 0;
            if ($transferQty > $product['available_quantity']) {
                $this->addError("transferQuantities.{$product['product_id']}", "Transfer quantity cannot exceed available stock ({$product['available_quantity']})");
                return;
            }
        }

        // Check if at least one product has a transfer quantity > 0
        $hasValidTransfer = collect($this->transferQuantities)->some(function ($qty) {
            return $qty > 0;
        });

        if (!$hasValidTransfer) {
            $this->addError('transferQuantities', 'Please enter transfer quantities for at least one product.');
            return;
        }

        try {
            // Create the transfer record
            $transfer = BranchTransferModel::create([
                'transfer_number' => BranchTransferModel::generateTransferNumber(),
                'source_branch_id' => $this->sourceBranchId,
                'destination_branch_id' => $this->destinationBranchId,
                'status' => 'completed',
                'created_by' => Auth::id(),
                'completed_at' => now(),
            ]);

            // Create transfer items and update inventory
            foreach ($this->selectedProducts as $product) {
                $transferQty = $this->transferQuantities[$product['product_id']] ?? 0;

                if ($transferQty > 0) {
                    // Find the branch allocation item
                    $allocationItem = BranchAllocationItem::find($product['id']);

                    if ($allocationItem) {
                        // Create transfer item record
                        BranchTransferItem::create([
                            'branch_transfer_id' => $transfer->id,
                            'product_id' => $product['product_id'],
                            'branch_allocation_item_id' => $allocationItem->id,
                            'quantity' => $transferQty,
                            'unit_price' => $allocationItem->getDisplayPriceAttribute(),
                            'total_value' => $transferQty * $allocationItem->getDisplayPriceAttribute(),
                            'status' => 'transferred',
                        ]);

                        // Note: In a real implementation, you would also need to:
                        // 1. Create new branch allocation items for the destination branch
                        // 2. Update inventory levels
                        // 3. Handle the transfer of scanned items from boxes
                        // This is a simplified version for demonstration
                    }
                }
            }

            session()->flash('message', 'Branch transfer completed successfully! Transfer #' . $transfer->transfer_number);
            $this->closeStepper();
            $this->loadTransfers();

        } catch (\Exception $e) {
            $this->addError('transfer', 'Error executing transfer: ' . $e->getMessage());
        }
    }

    /**
     * Load transfer history
     */
    protected function loadTransfers()
    {
        $query = BranchTransferModel::with(['sourceBranch', 'destinationBranch', 'creator', 'items.product'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->search) {
            $query->where('transfer_number', 'like', '%' . $this->search . '%');
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $this->transfers = $query->get();
    }

    /**
     * Clear filters
     */
    public function clearFilters()
    {
        $this->search = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->loadTransfers();
    }

    /**
     * Handle property updates for search and filters
     */
    public function updated($property)
    {
        if (in_array($property, ['search', 'dateFrom', 'dateTo'])) {
            $this->loadTransfers();
        }
    }

    public function render()
    {
        return view('livewire.pages.branch.branch-transfer');
    }
}