<?php

namespace Database\Factories;

use App\Models\BatchAllocation;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BatchAllocation>
 */
class BatchAllocationFactory extends Factory
{
    protected $model = BatchAllocation::class;

    public function definition(): array
    {
        return [
            // 'purchase_order_id' => PurchaseOrder::factory(), // Column doesn't exist in batch_allocations table
            'ref_no' => 'REF-' . $this->faker->unique()->numerify('######'),
            'status' => $this->faker->randomElement(['draft', 'dispatched', 'completed']),
            'workflow_step' => 1,
        ];
    }
}
