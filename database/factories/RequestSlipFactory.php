<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestSlip>
 */
class RequestSlipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => 'pending',
            'purpose' => $this->faker->randomElement(['Raw Materials', 'Supply', 'Consumables/Borrow']),
            'description' => $this->faker->paragraph,
            'request_date' => $this->faker->date(),
            'sent_from' => Department::inRandomOrder()->first()?->id ?? Department::factory(),
            'sent_to' => $this->faker->randomElement([2, 3]),
            'requested_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'approver' => null,
            
        ];
    }
}
