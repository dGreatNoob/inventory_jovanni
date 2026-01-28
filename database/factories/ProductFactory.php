<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'sku' => 'SKU-' . $this->faker->unique()->numerify('######'),
            'barcode' => $this->faker->unique()->numerify('############'),
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'cost' => $this->faker->randomFloat(2, 5, 500),
            'uom' => $this->faker->randomElement(['pcs', 'box', 'pack']),
            'disabled' => false,
        ];
    }
}
