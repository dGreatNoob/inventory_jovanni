<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\BranchAllocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        return [
            'branch_allocation_id' => BranchAllocation::factory(),
            'shipping_plan_num' => 'SP-' . $this->faker->unique()->numerify('######'),
            'shipping_status' => $this->faker->randomElement(['pending', 'in_transit', 'completed']),
            'carrier_name' => $this->faker->randomElement(['LBC', 'J&T', 'Grab Express', 'Lalamove']),
            'delivery_method' => $this->faker->randomElement(['standard', 'express']),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'shipping_status' => 'completed',
        ]);
    }
}
