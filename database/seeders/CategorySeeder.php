<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Main Categories
            [
                'name' => 'Handbags',
                'description' => 'Various types of handbags and purses',
                'parent_id' => null,
                'sort_order' => 1,
            ],
            [
                'name' => 'Backpacks',
                'description' => 'Backpacks for different purposes',
                'parent_id' => null,
                'sort_order' => 2,
            ],
            [
                'name' => 'Tote Bags',
                'description' => 'Large tote bags for shopping and daily use',
                'parent_id' => null,
                'sort_order' => 3,
            ],
            [
                'name' => 'Crossbody Bags',
                'description' => 'Crossbody bags for hands-free carrying',
                'parent_id' => null,
                'sort_order' => 4,
            ],
            [
                'name' => 'Clutches',
                'description' => 'Small clutches for evening and formal events',
                'parent_id' => null,
                'sort_order' => 5,
            ],
            [
                'name' => 'Laptop Bags',
                'description' => 'Bags designed for laptops and electronics',
                'parent_id' => null,
                'sort_order' => 6,
            ],
            [
                'name' => 'Travel Bags',
                'description' => 'Luggage and travel accessories',
                'parent_id' => null,
                'sort_order' => 7,
            ],
            [
                'name' => 'Accessories',
                'description' => 'Bag accessories and small items',
                'parent_id' => null,
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create([
                'entity_id' => 1,
                'name' => $categoryData['name'],
                'description' => $categoryData['description'],
                'parent_id' => $categoryData['parent_id'],
                'sort_order' => $categoryData['sort_order'],
                'slug' => Str::slug($categoryData['name']),
                'is_active' => true,
            ]);

            // Add subcategories for main categories
            if ($categoryData['name'] === 'Handbags') {
                $handbagSubcategories = [
                    ['name' => 'Leather Handbags', 'description' => 'Premium leather handbags', 'sort_order' => 1],
                    ['name' => 'Canvas Handbags', 'description' => 'Durable canvas handbags', 'sort_order' => 2],
                    ['name' => 'Designer Handbags', 'description' => 'High-end designer handbags', 'sort_order' => 3],
                ];

                foreach ($handbagSubcategories as $subData) {
                    Category::create([
                        'entity_id' => 1,
                        'name' => $subData['name'],
                        'description' => $subData['description'],
                        'parent_id' => $category->id,
                        'sort_order' => $subData['sort_order'],
                        'slug' => Str::slug($subData['name']),
                        'is_active' => true,
                    ]);
                }
            }

            if ($categoryData['name'] === 'Backpacks') {
                $backpackSubcategories = [
                    ['name' => 'School Backpacks', 'description' => 'Backpacks for students', 'sort_order' => 1],
                    ['name' => 'Hiking Backpacks', 'description' => 'Outdoor adventure backpacks', 'sort_order' => 2],
                    ['name' => 'Business Backpacks', 'description' => 'Professional backpacks', 'sort_order' => 3],
                ];

                foreach ($backpackSubcategories as $subData) {
                    Category::create([
                        'entity_id' => 1,
                        'name' => $subData['name'],
                        'description' => $subData['description'],
                        'parent_id' => $category->id,
                        'sort_order' => $subData['sort_order'],
                        'slug' => Str::slug($subData['name']),
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}