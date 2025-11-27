<?php

namespace Database\Seeders;

use App\Models\BatchAllocation;
use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AllocationWarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $products = Product::where('disabled', false)->get();
        $branches = Branch::all();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please run ProductSeeder first.');
            return;
        }

        if ($branches->isEmpty()) {
            $this->command->warn('No branches found. Please run BranchSeeder first.');
            return;
        }

        $this->command->info('Creating batch allocations...');

        // Create 5 batch allocations with different statuses
        $statuses = ['draft', 'draft', 'dispatched', 'dispatched', 'completed'];
        
        for ($i = 1; $i <= 5; $i++) {
            $status = $statuses[$i - 1];
            $transactionDate = Carbon::now()->subDays(rand(0, 30));
            
            // Generate batch number
            $batchNumber = 'BATCH-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            
            // Generate reference number
            $refNo = 'REF-' . date('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);

            // Create batch allocation
            $batchAllocation = BatchAllocation::create([
                'ref_no' => $refNo,
                'batch_number' => $batchNumber,
                'transaction_date' => $transactionDate,
                'remarks' => "Batch allocation #{$i} - " . ucfirst($status) . " status",
                'status' => $status,
                'workflow_step' => $status === 'draft' ? 1 : ($status === 'dispatched' ? 2 : 3),
            ]);

            // Select 2-4 random branches for this batch
            $selectedBranches = $branches->random(rand(2, min(4, $branches->count())));
            
            foreach ($selectedBranches as $branch) {
                // Create branch allocation
                $branchAllocation = BranchAllocation::create([
                    'batch_allocation_id' => $batchAllocation->id,
                    'branch_id' => $branch->id,
                    'remarks' => "Allocation for {$branch->name}",
                    'status' => $status === 'draft' ? 'pending' : ($status === 'dispatched' ? 'dispatched' : 'received'),
                ]);

                // Add 2-5 products to this branch allocation
                $selectedProducts = $products->random(rand(2, min(5, $products->count())));
                
                foreach ($selectedProducts as $product) {
                    $quantity = rand(5, 50);
                    $unitPrice = $product->price ?? rand(100, 5000);
                    
                    BranchAllocationItem::create([
                        'branch_allocation_id' => $branchAllocation->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'scanned_quantity' => $status === 'dispatched' || $status === 'completed' ? $quantity : 0,
                        'unit_price' => $unitPrice,
                    ]);
                }
            }

            $this->command->info("Created batch allocation: {$batchAllocation->batch_number} ({$status})");
        }

        $this->command->info('Batch allocations seeded successfully!');
    }
}
