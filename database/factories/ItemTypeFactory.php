<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemType>
 */
class ItemTypeFactory extends Factory
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
                'Paper',
                'Ink',
                'Adhesive',
                'Coating',
                'Packaging',
                'Labels',
                'Tapes',
                'Cleaning Supplies',
                'Tools',
                'Safety Equipment'
            ]),
            'description' => $this->faker->sentence()
        ];
    }
}
