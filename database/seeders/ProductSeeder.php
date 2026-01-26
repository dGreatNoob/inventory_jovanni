<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\ProductColor;
use App\Models\ProductInventory;
use App\Models\ProductPriceHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $categories = Category::whereNull('parent_id')->get();
        $suppliers = Supplier::where('is_active', true)->get();
        $colors = ProductColor::all();

        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');
            return;
        }

        if ($suppliers->isEmpty()) {
            $this->command->warn('No suppliers found. Please run SupplierSeeder first.');
            return;
        }

        // Product data
        $products = [
            // Handbags
            [
                'name' => 'Classic Leather Handbag',
                'category' => 'Handbags',
                'sku' => 'HB-LEA-001',
                'product_number' => '001',
                'barcode' => '1234567890123',
                'description' => 'Premium genuine leather handbag with gold hardware',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 2999.00,
                'original_price' => 3499.00,
                'cost' => 1500.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Genuine Leather',
                    'dimensions' => '30cm x 25cm x 12cm',
                    'weight' => '0.8kg',
                    'hardware' => 'Gold',
                ],
            ],
            
            // WR811-33 Cross Bag - Multiple Colors
            [
                'name' => 'WR811-33',
                'category' => 'Crossbody Bags',
                'sku' => 'WR811-33-BRO',
                'product_number' => '15779',
                'barcode' => '1234567890200',
                'description' => 'Cross bag in brown color',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 1299.00,
                'original_price' => 1599.00,
                'cost' => 800.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Synthetic Leather',
                    'dimensions' => '25cm x 20cm x 8cm',
                    'weight' => '0.5kg',
                ],
            ],
            [
                'name' => 'WR811-33',
                'category' => 'Crossbody Bags',
                'sku' => 'WR811-33-RED',
                'product_number' => '15780',
                'barcode' => '1234567890201',
                'description' => 'Cross bag in red color',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 1299.00,
                'original_price' => 1599.00,
                'cost' => 800.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Synthetic Leather',
                    'dimensions' => '25cm x 20cm x 8cm',
                    'weight' => '0.5kg',
                ],
            ],
            [
                'name' => 'WR811-33',
                'category' => 'Crossbody Bags',
                'sku' => 'WR811-33-SPNK',
                'product_number' => '15781',
                'barcode' => '1234567890202',
                'description' => 'Cross bag in soft pink color',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 1299.00,
                'original_price' => 1599.00,
                'cost' => 800.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Synthetic Leather',
                    'dimensions' => '25cm x 20cm x 8cm',
                    'weight' => '0.5kg',
                ],
            ],
            [
                'name' => 'WR811-33',
                'category' => 'Crossbody Bags',
                'sku' => 'WR811-33-RBLU',
                'product_number' => '15782',
                'barcode' => '1234567890203',
                'description' => 'Cross bag in royal blue color',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 1299.00,
                'original_price' => 1599.00,
                'cost' => 800.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Synthetic Leather',
                    'dimensions' => '25cm x 20cm x 8cm',
                    'weight' => '0.5kg',
                ],
            ],

            // Travel Backpack - Multiple Colors
            [
                'name' => 'Professional Travel Backpack',
                'category' => 'Travel Bags',
                'sku' => 'TB-PRO-001-BLK',
                'product_number' => '003B',
                'barcode' => '1234567890213',
                'description' => 'Durable travel backpack with multiple compartments - Black',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 3499.00,
                'original_price' => 3999.00,
                'cost' => 2000.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Water-resistant Nylon',
                    'dimensions' => '50cm x 35cm x 20cm',
                    'weight' => '1.2kg',
                    'capacity' => '45L',
                ],
            ],
            [
                'name' => 'Professional Travel Backpack',
                'category' => 'Travel Bags',
                'sku' => 'TB-PRO-001-GRY',
                'product_number' => '003C',
                'barcode' => '1234567890204',
                'description' => 'Durable travel backpack with multiple compartments - Gray',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 3499.00,
                'original_price' => 3999.00,
                'cost' => 2000.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Water-resistant Nylon',
                    'dimensions' => '50cm x 35cm x 20cm',
                    'weight' => '1.2kg',
                    'capacity' => '45L',
                ],
            ],
            [
                'name' => 'Professional Travel Backpack',
                'category' => 'Travel Bags',
                'sku' => 'TB-PRO-001-BLU',
                'product_number' => '003D',
                'barcode' => '1234567890205',
                'description' => 'Durable travel backpack with multiple compartments - Blue',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 3499.00,
                'original_price' => 3999.00,
                'cost' => 2000.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Water-resistant Nylon',
                    'dimensions' => '50cm x 35cm x 20cm',
                    'weight' => '1.2kg',
                    'capacity' => '45L',
                ],
            ],

            [
                'name' => 'Designer Canvas Tote',
                'category' => 'Tote Bags',
                'sku' => 'TB-CAN-001',
                'product_number' => '002',
                'barcode' => '1234567890206',
                'description' => 'Large canvas tote bag with reinforced handles',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 899.00,
                'original_price' => 1199.00,
                'cost' => 450.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Canvas',
                    'dimensions' => '45cm x 35cm x 15cm',
                    'weight' => '0.5kg',
                ],
            ],
            [
                'name' => 'Premium Crossbody Bag',
                'category' => 'Crossbody Bags',
                'sku' => 'CB-PRE-001',
                'product_number' => '003',
                'barcode' => '1234567890125',
                'description' => 'Adjustable crossbody bag with multiple compartments',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 1599.00,
                'original_price' => 1999.00,
                'cost' => 800.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Synthetic Leather',
                    'dimensions' => '25cm x 20cm x 8cm',
                    'weight' => '0.4kg',
                ],
            ],
            [
                'name' => 'Evening Clutch',
                'category' => 'Clutches',
                'sku' => 'CL-EVE-001',
                'product_number' => '004',
                'barcode' => '1234567890126',
                'description' => 'Elegant evening clutch with chain strap',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 1299.00,
                'original_price' => 1599.00,
                'cost' => 650.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Satin',
                    'dimensions' => '20cm x 12cm x 3cm',
                    'weight' => '0.2kg',
                ],
            ],
            [
                'name' => 'Professional Laptop Backpack',
                'category' => 'Laptop Bags',
                'sku' => 'LB-PRO-001',
                'product_number' => '005',
                'barcode' => '1234567890127',
                'description' => 'Padded laptop compartment backpack for professionals',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 2499.00,
                'original_price' => 2999.00,
                'cost' => 1200.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Nylon',
                    'dimensions' => '40cm x 30cm x 15cm',
                    'weight' => '1.2kg',
                    'laptop_size' => 'Up to 15.6 inches',
                ],
            ],
            [
                'name' => 'School Backpack',
                'category' => 'Backpacks',
                'sku' => 'BP-SCH-001',
                'product_number' => '006',
                'barcode' => '1234567890128',
                'description' => 'Durable school backpack with multiple pockets',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 1199.00,
                'original_price' => 1499.00,
                'cost' => 600.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Polyester',
                    'dimensions' => '38cm x 28cm x 14cm',
                    'weight' => '0.9kg',
                ],
            ],
            [
                'name' => 'Hiking Backpack',
                'category' => 'Backpacks',
                'sku' => 'BP-HIK-001',
                'product_number' => '007',
                'barcode' => '1234567890207',
                'description' => 'Waterproof hiking backpack with chest and waist straps',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 3499.00,
                'original_price' => 3999.00,
                'cost' => 1750.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Ripstop Nylon',
                    'dimensions' => '50cm x 35cm x 20cm',
                    'weight' => '1.5kg',
                    'capacity' => '30L',
                ],
            ],
            [
                'name' => 'Travel Duffel Bag',
                'category' => 'Travel Bags',
                'sku' => 'TB-DUF-001',
                'product_number' => '008',
                'barcode' => '1234567890208',
                'description' => 'Large duffel bag for travel and gym',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 1799.00,
                'original_price' => 2199.00,
                'cost' => 900.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Polyester',
                    'dimensions' => '60cm x 30cm x 30cm',
                    'weight' => '1.0kg',
                ],
            ],
            [
                'name' => 'Business Briefcase',
                'category' => 'Laptop Bags',
                'sku' => 'LB-BRI-001',
                'product_number' => '009',
                'barcode' => '1234567890209',
                'description' => 'Professional leather briefcase with combination lock',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 3999.00,
                'original_price' => 4499.00,
                'cost' => 2000.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Genuine Leather',
                    'dimensions' => '42cm x 32cm x 8cm',
                    'weight' => '1.8kg',
                ],
            ],
            [
                'name' => 'Mini Backpack',
                'category' => 'Backpacks',
                'sku' => 'BP-MIN-001',
                'product_number' => '010',
                'barcode' => '1234567890210',
                'description' => 'Compact mini backpack for daily essentials',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 799.00,
                'original_price' => 999.00,
                'cost' => 400.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Polyester',
                    'dimensions' => '28cm x 20cm x 10cm',
                    'weight' => '0.3kg',
                ],
            ],
            [
                'name' => 'Leather Wallet',
                'category' => 'Accessories',
                'sku' => 'AC-WAL-001',
                'product_number' => '011',
                'barcode' => '1234567890211',
                'description' => 'Genuine leather wallet with card slots',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 599.00,
                'original_price' => 799.00,
                'cost' => 300.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Genuine Leather',
                    'dimensions' => '10cm x 8cm x 2cm',
                    'weight' => '0.1kg',
                ],
            ],
            [
                'name' => 'Travel Organizer',
                'category' => 'Accessories',
                'sku' => 'AC-ORG-001',
                'product_number' => '012',
                'barcode' => '1234567890212',
                'description' => 'Travel organizer with multiple compartments',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 499.00,
                'original_price' => 699.00,
                'cost' => 250.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Nylon',
                    'dimensions' => '25cm x 18cm x 5cm',
                    'weight' => '0.2kg',
                ],
            ],
            [
                'name' => 'Premium Messenger Bag',
                'category' => 'Crossbody Bags',
                'sku' => 'CB-MES-001',
                'product_number' => '013',
                'barcode' => '1234567890135',
                'description' => 'Stylish messenger bag with laptop compartment',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 2199.00,
                'original_price' => 2599.00,
                'cost' => 1100.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Canvas',
                    'dimensions' => '38cm x 28cm x 10cm',
                    'weight' => '0.7kg',
                ],
            ],
            [
                'name' => 'Weekend Travel Bag',
                'category' => 'Travel Bags',
                'sku' => 'TB-WEE-001',
                'product_number' => '014',
                'barcode' => '1234567890136',
                'description' => 'Spacious weekend travel bag with wheels',
                'uom' => 'pcs',
                'product_type' => 'regular',
                'price' => 2799.00,
                'original_price' => 3299.00,
                'cost' => 1400.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Polycarbonate',
                    'dimensions' => '55cm x 40cm x 25cm',
                    'weight' => '3.5kg',
                ],
            ],
            [
                'name' => 'Designer Leather Handbag',
                'category' => 'Handbags',
                'sku' => 'HB-DES-001',
                'product_number' => '015',
                'barcode' => '1234567890137',
                'description' => 'Luxury designer handbag with signature hardware',
                'uom' => 'pcs',
                'product_type' => 'premium',
                'price' => 8999.00,
                'original_price' => 9999.00,
                'cost' => 4500.00,
                'price_note' => 'REG1',
                'shelf_life_days' => null,
                'specs' => [
                    'material' => 'Premium Leather',
                    'dimensions' => '32cm x 26cm x 14cm',
                    'weight' => '1.0kg',
                    'hardware' => 'Gold Plated',
                ],
            ],
        ];

        $this->command->info('Creating products...');

        foreach ($products as $productData) {
            // Find category
            $category = $categories->firstWhere('name', $productData['category']);
            if (!$category) {
                $this->command->warn("Category '{$productData['category']}' not found, skipping product: {$productData['name']}");
                continue;
            }

            // Get random supplier and color
            $supplier = $suppliers->random();
            $color = $colors->isNotEmpty() ? $colors->random() : null;

            // Generate supplier code if not set
            $supplierCode = $supplier->code ?? strtoupper(substr($supplier->name, 0, 3)) . '-' . rand(100, 999);

            // Create or update product
            $product = Product::updateOrCreate(
                ['sku' => $productData['sku']],
                [
                    'entity_id' => 1,
                    'name' => $productData['name'],
                    'product_number' => $productData['product_number'],
                    'sku' => $productData['sku'],
                    'barcode' => $productData['barcode'],
                    'remarks' => $productData['description'],
                    'category_id' => $category->id,
                    'supplier_id' => $supplier->id,
                    'supplier_code' => $supplierCode,
                    'product_type' => $productData['product_type'],
                    'product_color_id' => $color?->id,
                    'uom' => $productData['uom'],
                    'price' => $productData['price'],
                    'original_price' => $productData['original_price'],
                    'cost' => $productData['cost'],
                    'price_note' => $productData['price_note'],
                    'shelf_life_days' => $productData['shelf_life_days'],
                    'specs' => $productData['specs'],
                    'disabled' => false,
                    'soft_card' => null,
                    'created_by' => 1,
                    'updated_by' => 1,
                ]
            );

            // Create initial inventory
            $initialQuantity = rand(10, 100);
            ProductInventory::updateOrCreate(
                [
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => $initialQuantity,
                    'available_quantity' => $initialQuantity,
                    'reserved_quantity' => 0,
                ]
            );

            // Create price history
            ProductPriceHistory::create([
                'product_id' => $product->id,
                'old_price' => null,
                'new_price' => $productData['price'],
                'pricing_note' => $productData['price_note'],
                'changed_by' => 1,
                'changed_at' => now(),
            ]);

            $this->command->info("Created/Updated: {$product->name} (SKU: {$product->sku})");
        }

        $this->command->info('Products seeded successfully!');
    }
}