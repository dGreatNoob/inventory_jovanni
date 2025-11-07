<?php

namespace App\Livewire\Pages\Allocation;

use App\Models\BatchAllocation;
use App\Models\SalesReceipt;
use App\Models\SalesReceiptItem;
use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
#[Title('Allocation - Sales')]
class Sales extends Component
{
    public $availableBatches = [];
    public $selectedBatchId = null;
    public $batchReceipts = [];
    public $selectedReceipt = null;
    public $showReceiptDetails = false;

    // Receipt confirmation fields
    public $receiptItems = [];
    public $showConfirmModal = false;

    // Item update fields
    public $itemId = null;
    public $received_qty = 0;
    public $damaged_qty = 0;
    public $missing_qty = 0;
    public $remarks = '';
    public $showEditItemModal = false;

    public function mount()
    {
        $this->loadAvailableBatches();
    }

    public function loadAvailableBatches()
    {
        $this->availableBatches = BatchAllocation::with(['branchAllocations.branch'])
            ->where('status', 'dispatched')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function loadBatchReceipts()
    {
        if (!$this->selectedBatchId) {
            $this->batchReceipts = [];
            return;
        }

        $this->batchReceipts = SalesReceipt::with([
            'branch',
            'items.product'
        ])
        ->where('batch_allocation_id', $this->selectedBatchId)
        ->get()
        ->map(function ($receipt) {
            $receipt->total_items = $receipt->items->count();
            $receipt->total_allocated_qty = $receipt->items->sum('allocated_qty');
            $receipt->total_received_qty = $receipt->items->sum('received_qty');
            $receipt->total_sold_qty = $receipt->items->sum('sold_qty');
            $receipt->total_damaged_qty = $receipt->items->sum('damaged_qty');
            return $receipt;
        });
    }

    public function selectBatch($batchId)
    {
        $this->selectedBatchId = $batchId;
        $this->loadBatchReceipts();
    }

    public function viewReceiptDetails($receiptId)
    {
        $this->selectedReceipt = SalesReceipt::with([
            'branch',
            'batchAllocation',
            'items.product'
        ])->find($receiptId);
        $this->showReceiptDetails = true;
    }

    public function closeReceiptDetails()
    {
        $this->showReceiptDetails = false;
        $this->selectedReceipt = null;
        $this->loadBatchReceipts(); // Refresh the table data
    }

    public function openConfirmModal($receiptId)
    {
        $this->selectedReceipt = SalesReceipt::with(['items.product'])->find($receiptId);
        
        if (!$this->selectedReceipt) {
            session()->flash('error', 'Receipt not found.');
            return;
        }

        $this->receiptItems = $this->selectedReceipt->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_name' => $item->product->name ?? 'Unknown Product',
                'allocated_qty' => $item->allocated_qty,
                'received_qty' => $item->received_qty,
                'damaged_qty' => $item->damaged_qty,
                'missing_qty' => $item->missing_qty,
                'sold_qty' => $item->sold_qty,
                'status' => $item->status,
            ];
        })->toArray();
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->selectedReceipt = null;
        $this->receiptItems = [];
        $this->loadBatchReceipts(); // Refresh the table data
    }

    public function confirmReceipt()
    {
        if (!$this->selectedReceipt) {
            session()->flash('error', 'No receipt selected.');
            return;
        }

        DB::beginTransaction();
        try {
            // Update receipt status and date
            $this->selectedReceipt->update([
                'status' => 'received',
                'date_received' => now()->toDateString(),
            ]);

            // Update all items to received status with actual quantities
            foreach ($this->receiptItems as $itemData) {
                $totalReceived = $itemData['received_qty'] + $itemData['damaged_qty'];
                $missingQty = $itemData['allocated_qty'] - $totalReceived;

                SalesReceiptItem::where('id', $itemData['id'])->update([
                    'received_qty' => $itemData['received_qty'],
                    'damaged_qty' => $itemData['damaged_qty'],
                    'missing_qty' => $missingQty,
                    'status' => 'received',
                ]);
            }

            // Update corresponding BranchAllocation status to 'received'
            $branchAllocation = BranchAllocation::where('batch_allocation_id', $this->selectedReceipt->batch_allocation_id)
                ->where('branch_id', $this->selectedReceipt->branch_id)
                ->first();

            if ($branchAllocation) {
                $branchAllocation->update(['status' => 'received']);
            }

            DB::commit();
            session()->flash('message', 'Receipt confirmed successfully.');
            $this->closeConfirmModal();
            $this->loadBatchReceipts();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Failed to confirm receipt: ' . $e->getMessage());
        }
    }

    public function openEditItemModal($itemId)
    {
        $item = SalesReceiptItem::with('salesReceipt')->find($itemId);
        
        if (!$item) {
            session()->flash('error', 'Item not found.');
            return;
        }

        if ($item->salesReceipt->status === 'received') {
            session()->flash('error', 'Cannot edit item after receipt is confirmed.');
            return;
        }

        $this->itemId = $itemId;
        $this->received_qty = $item->received_qty;
        $this->damaged_qty = $item->damaged_qty;
        $this->missing_qty = $item->missing_qty;
        $this->missing_qty = $item->allocated_qty - ($item->received_qty + $item->damaged_qty);
        $this->remarks = $item->remarks ?? '';
        $this->showEditItemModal = true;
    }

    public function closeEditItemModal()
    {
        $this->showEditItemModal = false;
        $this->itemId = null;
        $this->received_qty = 0;
        $this->damaged_qty = 0;
        $this->missing_qty = 0;
        $this->remarks = '';
        $this->loadBatchReceipts(); // Refresh the table data
    }

    public function updateItem()
    {
        $item = SalesReceiptItem::with('salesReceipt')->find($this->itemId);
        
        if (!$item) {
            session()->flash('error', 'Item not found.');
            return;
        }

        if ($item->salesReceipt->status === 'received') {
            session()->flash('error', 'Cannot edit item after receipt is confirmed.');
            return;
        }

        // Validate quantities
        $totalReceived = $this->received_qty + $this->damaged_qty;
        if ($totalReceived > $item->allocated_qty) {
            session()->flash('error', 'Total received quantity cannot exceed allocated quantity.');
            return;
        }

        $item->update([
            'received_qty' => $this->received_qty,
            'damaged_qty' => $this->damaged_qty,
            'missing_qty' => $item->allocated_qty - $totalReceived,
            'remarks' => $this->remarks,
        ]);

        session()->flash('message', 'Item updated successfully.');
        $this->closeEditItemModal();
        $this->loadBatchReceipts();
    }

    public function markAsSold($itemId, $quantity = 1)
    {
        $item = SalesReceiptItem::with('salesReceipt')->find($itemId);
        
        if (!$item) {
            session()->flash('error', 'Item not found.');
            return;
        }

        if ($item->salesReceipt->status !== 'received') {
            session()->flash('error', 'Can only mark items as sold after receipt is confirmed.');
            return;
        }

        $availableQty = $item->received_qty - $item->sold_qty;
        if ($quantity > $availableQty) {
            session()->flash('error', 'Cannot sell more than available quantity.');
            return;
        }

        $item->update([
            'sold_qty' => $item->sold_qty + $quantity,
            'sold_at' => now(),
            'sold_by' => 'System',
            'status' => $item->sold_qty + $quantity >= $item->received_qty ? 'sold' : 'partial_sold',
        ]);

        session()->flash('message', 'Item marked as sold successfully.');
        $this->loadBatchReceipts();
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300',
            'received' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
            'partial_sold' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300',
            'sold' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300',
        };
    }

    public function render()
    {
        return view('livewire.pages.allocation.sales');
    }

    public function updatedShowReceiptDetails($value)
    {
        if (!$value) {
            $this->loadBatchReceipts();
        }
    }

    public function updatedShowConfirmModal($value)
    {
        if (!$value) {
            $this->loadBatchReceipts();
        }
    }

    public function updatedShowEditItemModal($value)
    {
        if (!$value) {
            $this->loadBatchReceipts();
        }
    }

    public function updatingItemId($value)
    {
        if (!$value) {
            $this->received_qty = 0;
            $this->damaged_qty = 0;
            $this->missing_qty = 0;
            $this->remarks = '';
        }
    }

    public function updatedReceivedQty()
    {
        if ($this->itemId) {
            $item = SalesReceiptItem::find($this->itemId);
            if ($item) {
                $this->missing_qty = $item->allocated_qty - ($this->received_qty + $this->damaged_qty);
            }
        }
    }

    public function updatedDamagedQty()
    {
        if ($this->itemId) {
            $item = SalesReceiptItem::find($this->itemId);
            if ($item) {
                $this->missing_qty = $item->allocated_qty - ($this->received_qty + $this->damaged_qty);
            }
        }
    }
}
