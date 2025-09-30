<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RawMatProfile>
 */
class RawMatProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gsm' => $this->faker->numberBetween(100, 500),
            'width_size' => $this->faker->numberBetween(50, 200),
            'classification' => 'Corrugating Medium',
            'supplier' => $this->faker->company(),
            'country_origin' => $this->faker->country(),
        ];
    }
}
