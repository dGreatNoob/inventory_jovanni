<?php

namespace App\Console\Commands;

use App\Models\BranchAllocationItem;
use Illuminate\Console\Command;

class BackfillProductSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-product-snapshots {--chunk=100 : Number of records to process at once}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill product snapshot data for existing branch allocation items to ensure historical data integrity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chunkSize = (int) $this->option('chunk');

        $this->info('Starting product snapshot backfill...');

        $totalItems = BranchAllocationItem::whereNull('product_snapshot_name')->count();

        if ($totalItems === 0) {
            $this->info('No items need backfilling. All items already have snapshots.');
            return;
        }

        $this->info("Found {$totalItems} items that need snapshot backfilling.");
        $this->newLine();

        $bar = $this->output->createProgressBar($totalItems);
        $bar->start();

        $processed = 0;
        $errors = 0;

        BranchAllocationItem::with('product')
            ->whereNull('product_snapshot_name')
            ->chunk($chunkSize, function ($items) use ($bar, &$processed, &$errors) {
                foreach ($items as $item) {
                    try {
                        if ($item->product) {
                            $item->update([
                                'product_snapshot_name' => $item->product->name,
                                'product_snapshot_sku' => $item->product->sku,
                                'product_snapshot_barcode' => $item->product->barcode,
                                'product_snapshot_specs' => $item->product->specs,
                                'product_snapshot_price' => $item->product->price ?? $item->product->selling_price,
                                'product_snapshot_uom' => $item->product->uom,
                                'product_snapshot_created_at' => $item->product->created_at,
                            ]);
                        } else {
                            // Handle orphaned records
                            $item->update([
                                'product_snapshot_name' => 'Unknown Product (Deleted)',
                                'product_snapshot_sku' => 'N/A',
                                'product_snapshot_barcode' => 'N/A',
                            ]);
                        }

                        $processed++;
                    } catch (\Exception $e) {
                        $errors++;
                        $this->error("Error processing item {$item->id}: {$e->getMessage()}");
                    }

                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine(2);

        $this->info("Backfill completed!");
        $this->info("Processed: {$processed} items");
        if ($errors > 0) {
            $this->error("Errors: {$errors} items");
        }

        $remaining = BranchAllocationItem::whereNull('product_snapshot_name')->count();
        if ($remaining > 0) {
            $this->warn("{$remaining} items still need backfilling.");
        } else {
            $this->info('All items now have product snapshots for historical data integrity.');
        }
    }
}
