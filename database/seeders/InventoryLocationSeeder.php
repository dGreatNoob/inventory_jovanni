<?php

namespace Database\Seeders;

use App\Models\InventoryLocation;
use Illuminate\Database\Seeder;

class InventoryLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Main Warehouse',
                'code' => 'WH001',
                'type' => 'warehouse',
                'address' => '100 Warehouse Drive',
                'manager_name' => 'Robert Anderson',
                'contact_phone' => '+1-555-0100',
                'capacity_sqft' => 10000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Downtown Store',
                'code' => 'ST001',
                'type' => 'store',
                'address' => '200 Main Street',
                'manager_name' => 'Lisa Martinez',
                'contact_phone' => '+1-555-0200',
                'capacity_sqft' => 2000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Shopping Mall Store',
                'code' => 'ST002',
                'type' => 'store',
                'address' => '300 Mall Avenue',
                'manager_name' => 'James Taylor',
                'contact_phone' => '+1-555-0300',
                'capacity_sqft' => 1500.00,
                'is_active' => true,
            ],
            [
                'name' => 'Display Center',
                'code' => 'DC001',
                'type' => 'display',
                'address' => '400 Showroom Boulevard',
                'manager_name' => 'Maria Garcia',
                'contact_phone' => '+1-555-0400',
                'capacity_sqft' => 5000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Transit Hub',
                'code' => 'TH001',
                'type' => 'transit',
                'address' => '500 Transit Way',
                'manager_name' => 'Kevin Lee',
                'contact_phone' => '+1-555-0500',
                'capacity_sqft' => 3000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Online Fulfillment Center',
                'code' => 'FC001',
                'type' => 'warehouse',
                'address' => '600 Fulfillment Street',
                'manager_name' => 'Jennifer White',
                'contact_phone' => '+1-555-0600',
                'capacity_sqft' => 8000.00,
                'is_active' => true,
            ],
        ];

        foreach ($locations as $locationData) {
            InventoryLocation::create([
                'entity_id' => 1,
                ...$locationData
            ]);
        }
    }
}