<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AllocationFactory>
 */
class AllocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Production',
                'Maintenance',
                'Office',
                'Warehouse',
                'Quality Control',
                'Shipping',
                'Research & Development',
                'General Supplies',
                'Emergency Stock',
                'Safety Inventory'
            ]),
            'description' => $this->faker->sentence()
        ];
    }
}
