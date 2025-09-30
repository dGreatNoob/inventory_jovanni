<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\RawMatProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RawMatOrder>
 */
class RawMatOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
      
        return [
            'purchase_order_id' => PurchaseOrder::where('po_type', 'raw_mat')->inRandomOrder()->first()?->id ?? PurchaseOrder::factory()->rawMat(),
            'raw_mat_profile_id' => RawMatProfile::inRandomOrder()->first()?->id ?? RawMatProfile::factory(),
        ];
    }
}
