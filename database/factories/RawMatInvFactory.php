<?php

namespace Database\Factories;

use App\Models\RawMatOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RawMatInv>
 */
class RawMatInvFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
        return [
            'raw_mat_order_id' => RawMatOrder::inRandomOrder()
                ->first()?->id ?? RawMatOrder::factory(),
            'spc_num' => $this->faker->unique()->numberBetween(1000, 9999),
            'supplier_num' => 'BCM'. $this->faker->unique()->numberBetween(1000, 9999),
            'weight' => $weight = $this->faker->numberBetween(100, 1000),
            'rem_weight' => $weight,
            'remarks' => 'pending',
            'comment' => null ,
            'date_delivered' => null,
            'status' => 'pending',
        ];
    }
}
