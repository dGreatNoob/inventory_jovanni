<?php

namespace Database\Seeders;

use App\Models\BatchAllocation;
use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;
use App\Models\SalesReceipt;
use App\Models\SalesReceiptItem;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AllocationSalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get dispatched batch allocations
        $dispatchedBatches = BatchAllocation::where('status', 'dispatched')
            ->with(['branchAllocations.items.product'])
            ->get();

        if ($dispatchedBatches->isEmpty()) {
            $this->command->warn('No dispatched batch allocations found. Please run AllocationWarehouseSeeder first or create dispatched batches.');
            return;
        }

        $this->command->info('Creating sales receipts for dispatched batches...');

        foreach ($dispatchedBatches as $batch) {
            foreach ($batch->branchAllocations as $branchAllocation) {
                // Check if receipt already exists
                $existingReceipt = SalesReceipt::where('batch_allocation_id', $batch->id)
                    ->where('branch_id', $branchAllocation->branch_id)
                    ->first();

                if ($existingReceipt) {
                    $this->command->info("Receipt already exists for batch {$batch->batch_number} - branch {$branchAllocation->branch->name}, skipping...");
                    continue;
                }

                // Create sales receipt
                $salesReceipt = SalesReceipt::create([
                    'batch_allocation_id' => $batch->id,
                    'branch_id' => $branchAllocation->branch_id,
                    'status' => 'pending', // pending, confirmed, completed
                    'date_received' => Carbon::now()->subDays(rand(0, 7)),
                ]);

                // Create receipt items from branch allocation items
                foreach ($branchAllocation->items as $allocationItem) {
                    $allocatedQty = $allocationItem->quantity;
                    $receivedQty = $allocatedQty; // Assume all received initially
                    $damagedQty = rand(0, 2); // Random damaged items (0-2)
                    $missingQty = rand(0, 1); // Random missing items (0-1)
                    $soldQty = rand(0, (int)($receivedQty * 0.7)); // Random sold items (up to 70% of received)

                    SalesReceiptItem::create([
                        'sales_receipt_id' => $salesReceipt->id,
                        'product_id' => $allocationItem->product_id,
                        'allocated_qty' => $allocatedQty,
                        'received_qty' => $receivedQty - $damagedQty - $missingQty,
                        'damaged_qty' => $damagedQty,
                        'missing_qty' => $missingQty,
                        'sold_qty' => $soldQty,
                        'status' => $soldQty > 0 ? 'sold' : 'pending',
                        'remarks' => $damagedQty > 0 || $missingQty > 0 
                            ? "Damaged: {$damagedQty}, Missing: {$missingQty}" 
                            : null,
                        'sold_at' => $soldQty > 0 ? Carbon::now()->subDays(rand(0, 3)) : null,
                        'sold_by' => $soldQty > 0 ? 1 : null, // Assuming user ID 1 exists
                    ]);
                }

                $this->command->info("Created sales receipt for batch {$batch->batch_number} - branch {$branchAllocation->branch->name}");
            }
        }

        $this->command->info('Sales receipts seeded successfully!');
    }
}
