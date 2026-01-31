<?php

namespace App\Console\Commands;

use App\Services\ProductService;
use Illuminate\Console\Command;

class ApplyPendingProductPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:apply-pending-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply pending product prices whose effective date is today or in the past';

    /**
     * Execute the console command.
     */
    public function handle(ProductService $productService): int
    {
        $this->info('Applying due pending product prices...');

        $count = $productService->applyDuePendingPrices();

        $this->info("Applied pending prices for {$count} product(s).");

        return self::SUCCESS;
    }
}
