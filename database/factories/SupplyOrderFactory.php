<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\SupplyProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupplyOrder>
 */
class SupplyOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            
            'purchase_order_id' => PurchaseOrder::where('po_type', 'supply')        ->inRandomOrder()
            ->first()?->id ?? PurchaseOrder::factory()->supply(),
            'supply_profile_id' => SupplyProfile::inRandomOrder()->first()?->id ?? SupplyProfile::factory(),

            'unit_price' => $this->faker->randomFloat(2, 1, 1000),
            'order_total_price' => $this->faker->randomFloat(2, 1, 10000),
            'final_total_price' => $this->faker->randomFloat(2, 1, 10000),
            'order_qty' => $this->faker->randomFloat(2, 1, 100),
            'received_qty' => null, 
            'remarks' => 'pending',
            'comment' => null,
            'status' => 'pending',
            'date_delivered' => null,
        ];
    }
}
