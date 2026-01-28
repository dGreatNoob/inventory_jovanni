<?php

namespace Database\Factories;

use App\Models\BranchAllocationItem;
use App\Models\BranchAllocation;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BranchAllocationItem>
 */
class BranchAllocationItemFactory extends Factory
{
    protected $model = BranchAllocationItem::class;

    public function definition(): array
    {
        $product = Product::factory()->create();
        
        return [
            'branch_allocation_id' => BranchAllocation::factory(),
            'product_id' => $product->id,
            'quantity' => $this->faker->numberBetween(10, 100),
            'sold_quantity' => 0,
            'unit_price' => $this->faker->randomFloat(2, 10, 500),
            'product_snapshot_barcode' => $product->barcode,
            'product_snapshot_name' => $product->name,
            'product_snapshot_sku' => $product->sku,
            'box_id' => null,
        ];
    }
}
