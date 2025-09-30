<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
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
            'total_price' => $this->faker->randomFloat(2, 100, 10000),
            'order_date' => $this->faker->date(),
            'po_num' => $this->faker->unique()->numberBetween(1000, 9999),
            'del_on' => $this->faker->date(),
            'del_to' => \App\Models\Department::inRandomOrder()->first()?->id,
            'supplier_id' => \App\Models\Supplier::inRandomOrder()->first()?->id ?? \App\Models\Supplier::factory(),
            'payment_terms' => $this->faker->randomElement(['15 days', '30 days']),
            'quotation' => $this->faker->word(),
            
            // 'po_type' => $this->faker->randomElement(['raw_mats', 'supply']),
            'total_qty' => $this->faker->numberBetween(1, 100),
            'ordered_by' => \App\Models\User::inRandomOrder()->first()?->id ?? \App\Models\User::factory(),
            'approver' => \App\Models\User::inRandomOrder()->first()?->id ?? \App\Models\User::factory(),
        ];
    }

    public function supply(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'po_type' => 'supply',
                'total_est_weight' => null,
            ];
        });
    }

    public function rawMat(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'po_type' => 'raw_mats',
                'total_est_weight' => $this->faker->randomFloat(2, 1, 1000),
            ];
        });
    }
}
