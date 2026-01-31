<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Premium Leather Co.',
                'address' => '123 Leather Street, New York, USA 10001',
                'contact_num' => '+1-555-0123',
                'contact_person' => 'John Smith',
                'email' => 'john@premiumleather.com',
                'phone' => '+1-555-0123',
                'city' => 'New York',
                'country' => 'USA',
                'postal_code' => '10001',
                'terms' => 'Net 30 days',
                'tax_id' => 'TAX123456',
                'credit_limit' => 50000.00,
                'payment_terms_days' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Canvas Works Ltd.',
                'address' => '456 Canvas Avenue, Los Angeles, USA 90001',
                'contact_num' => '+1-555-0456',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@canvasworks.com',
                'phone' => '+1-555-0456',
                'city' => 'Los Angeles',
                'country' => 'USA',
                'postal_code' => '90001',
                'terms' => 'Net 15 days',
                'tax_id' => 'TAX789012',
                'credit_limit' => 30000.00,
                'payment_terms_days' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Designer Bags International',
                'address' => '789 Designer Boulevard, Miami, USA 33101',
                'contact_num' => '+1-555-0789',
                'contact_person' => 'Michael Brown',
                'email' => 'michael@designerbags.com',
                'phone' => '+1-555-0789',
                'city' => 'Miami',
                'country' => 'USA',
                'postal_code' => '33101',
                'terms' => 'Net 45 days',
                'tax_id' => 'TAX345678',
                'credit_limit' => 100000.00,
                'payment_terms_days' => 45,
                'is_active' => true,
            ],
            [
                'name' => 'Tech Bags Solutions',
                'address' => '321 Tech Park, San Francisco, USA 94101',
                'contact_num' => '+1-555-0321',
                'contact_person' => 'Emily Davis',
                'email' => 'emily@techbags.com',
                'phone' => '+1-555-0321',
                'city' => 'San Francisco',
                'country' => 'USA',
                'postal_code' => '94101',
                'terms' => 'Net 30 days',
                'tax_id' => 'TAX901234',
                'credit_limit' => 75000.00,
                'payment_terms_days' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Travel Gear Pro',
                'address' => '654 Travel Lane, Seattle, USA 98101',
                'contact_num' => '+1-555-0654',
                'contact_person' => 'David Wilson',
                'email' => 'david@travelgear.com',
                'phone' => '+1-555-0654',
                'city' => 'Seattle',
                'country' => 'USA',
                'postal_code' => '98101',
                'terms' => 'Net 20 days',
                'tax_id' => 'TAX567890',
                'credit_limit' => 40000.00,
                'payment_terms_days' => 20,
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplierData) {
            // Use name as unique identifier since code might be nullable
            Supplier::updateOrCreate(
                ['name' => $supplierData['name']],
                [
                    'entity_id' => 1,
                    ...$supplierData
                ]
            );
        }
    }
}