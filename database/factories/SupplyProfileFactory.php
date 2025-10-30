<?php

namespace Database\Factories;

use App\Models\SupplyProfile;
use App\Models\ItemType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupplyProfile>
 */
class SupplyProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supply_item_class' => $this->faker->randomElement(['consumable', 'non-consumable']),
            'item_type_id' => ItemType::factory(),
            'supply_description' => $this->faker->sentence(3),
            'supply_qty' => $this->faker->randomFloat(2, 1, 1000),
            'supply_uom' => $this->faker->randomElement(['pcs', 'kg', 'box', 'roll', 'set', 'bottle', 'can']),
            'supply_min_qty' => $this->faker->randomFloat(2, 1, 100),
            'supply_price1' => $this->faker->randomFloat(2, 10, 1000),
            'supply_price2' => $this->faker->randomFloat(2, 10, 1000),
            'supply_price3' => $this->faker->randomFloat(2, 10, 1000),
            'supply_sku' => $this->faker->unique()->bothify('SKU-######??'),
        ];
    }
}
