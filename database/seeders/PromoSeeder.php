<?php

namespace Database\Seeders;

use App\Models\Promo;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class PromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some products and branches to use in promos
        $products = Product::take(10)->get();
        $branches = Branch::take(5)->get();

        if ($products->isEmpty() || $branches->isEmpty()) {
            $this->command?->warn('Products or Branches not found. Please seed Products and Branches first.');
            return;
        }

        $promos = [
            [
                'name' => 'Summer Sale 2025',
                'code' => 'SUMMER2025',
                'description' => 'Get 20% off on selected handbags and backpacks',
                'startDate' => now()->addDays(7),
                'endDate' => now()->addDays(37),
                'branch' => $branches->take(3)->pluck('id')->toArray(),
                'product' => $products->take(5)->pluck('id')->toArray(),
                'second_product' => [],
                'type' => 'percentage',
            ],
            [
                'name' => 'Buy One Get One',
                'code' => 'BOGO2025',
                'description' => 'Buy any tote bag, get a clutch free',
                'startDate' => now()->addDays(10),
                'endDate' => now()->addDays(40),
                'branch' => $branches->take(2)->pluck('id')->toArray(),
                'product' => $products->where('name', 'like', '%Tote%')->take(3)->pluck('id')->toArray(),
                'second_product' => $products->where('name', 'like', '%Clutch%')->take(3)->pluck('id')->toArray(),
                'type' => 'bogo',
            ],
            [
                'name' => 'New Year Flash Sale',
                'code' => 'NY2025',
                'description' => 'Limited time offer: 30% off on all travel bags',
                'startDate' => now()->subDays(5),
                'endDate' => now()->addDays(25),
                'branch' => $branches->pluck('id')->toArray(),
                'product' => $products->where('name', 'like', '%Travel%')->take(4)->pluck('id')->toArray(),
                'second_product' => [],
                'type' => 'percentage',
            ],
            [
                'name' => 'Weekend Special',
                'code' => 'WEEKEND',
                'description' => '15% off on all laptop bags this weekend',
                'startDate' => now()->startOfWeek()->addDays(5),
                'endDate' => now()->startOfWeek()->addDays(7),
                'branch' => $branches->take(4)->pluck('id')->toArray(),
                'product' => $products->where('name', 'like', '%Laptop%')->take(3)->pluck('id')->toArray(),
                'second_product' => [],
                'type' => 'percentage',
            ],
            [
                'name' => 'Crossbody Combo',
                'code' => 'CROSSBODY',
                'description' => 'Buy any crossbody bag, get 50% off on accessories',
                'startDate' => now()->addDays(15),
                'endDate' => now()->addDays(45),
                'branch' => $branches->take(3)->pluck('id')->toArray(),
                'product' => $products->where('name', 'like', '%Crossbody%')->take(2)->pluck('id')->toArray(),
                'second_product' => $products->where('name', 'like', '%Accessory%')->take(3)->pluck('id')->toArray(),
                'type' => 'combo',
            ],
        ];

        foreach ($promos as $promoData) {
            // Use code as unique identifier
            Promo::updateOrCreate(
                ['code' => $promoData['code']],
                [
                    'name' => $promoData['name'],
                    'description' => $promoData['description'],
                    'startDate' => $promoData['startDate'],
                    'endDate' => $promoData['endDate'],
                    'branch' => json_encode($promoData['branch']),
                    'product' => json_encode($promoData['product']),
                    'second_product' => json_encode($promoData['second_product']),
                    'type' => $promoData['type'],
                ]
            );
        }
    }
}

