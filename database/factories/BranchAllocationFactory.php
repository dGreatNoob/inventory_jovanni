<?php

namespace Database\Factories;

use App\Models\BranchAllocation;
use App\Models\Branch;
use App\Models\BatchAllocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BranchAllocation>
 */
class BranchAllocationFactory extends Factory
{
    protected $model = BranchAllocation::class;

    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'batch_allocation_id' => BatchAllocation::factory(),
        ];
    }
}
