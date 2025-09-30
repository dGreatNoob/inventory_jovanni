<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            
            'name' => $this->faker->randomElement([
                'HR',
                'Finance',
                'IT',
                'Marketing',
                'Sales',
                'Operations',
                'Customer Service',
                'Research and Development',
                'Legal',
                'Procurement'
            ]),
            'description' => $this->faker->text(100),

        ];
    }
}
