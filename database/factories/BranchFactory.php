<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Branch',
            'code' => 'BR-' . $this->faker->unique()->numerify('####'),
            'address' => $this->faker->address(),
            'contact_num' => $this->faker->phoneNumber(),
            'batch' => 'BATCH-' . $this->faker->numerify('####'),
            'category' => $this->faker->randomElement(['Retail', 'Wholesale', 'Online', 'Warehouse']),
        ];
    }
}
