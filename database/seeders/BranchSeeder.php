<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'SM Megamall Branch',
                'code' => 'BR001',
                'category' => 'mall',
                'address' => 'SM Megamall, Ortigas Avenue, Mandaluyong City',
                'subclass1' => 'Premium',
                'subclass2' => 'High Traffic',
                'contact_num' => '+63-2-8888-1234',
                'manager_name' => 'Anna Rodriguez',
                'remarks' => 'Main flagship store',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
            ],
            [
                'name' => 'Greenhills Shopping Center',
                'code' => 'BR002',
                'category' => 'mall',
                'address' => 'Greenhills Shopping Center, San Juan City',
                'subclass1' => 'Standard',
                'subclass2' => 'Medium Traffic',
                'contact_num' => '+63-2-8888-2345',
                'manager_name' => 'Carlos Mendoza',
                'remarks' => 'Located near food court',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
            ],
            [
                'name' => 'Robinsons Galleria',
                'code' => 'BR003',
                'category' => 'mall',
                'address' => 'Robinsons Galleria, Ortigas Avenue, Quezon City',
                'subclass1' => 'Premium',
                'subclass3' => 'VIP Section',
                'contact_num' => '+63-2-8888-3456',
                'manager_name' => 'Lisa Tan',
                'remarks' => 'VIP customer area available',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
            ],
            [
                'name' => 'Ayala Center Cebu',
                'code' => 'BR004',
                'category' => 'mall',
                'address' => 'Ayala Center Cebu, Cebu City',
                'subclass1' => 'Standard',
                'subclass2' => 'High Traffic',
                'contact_num' => '+63-32-8888-4567',
                'manager_name' => 'Miguel Santos',
                'remarks' => 'Regional branch',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
            ],
            [
                'name' => 'Glorietta Makati',
                'code' => 'BR005',
                'category' => 'mall',
                'address' => 'Glorietta 3, Ayala Center, Makati City',
                'subclass1' => 'Premium',
                'subclass2' => 'High Traffic',
                'subclass4' => 'Corporate',
                'contact_num' => '+63-2-8888-5678',
                'manager_name' => 'Patricia Cruz',
                'remarks' => 'Corporate client focus',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
            ],
            [
                'name' => 'SM North EDSA',
                'code' => 'BR006',
                'category' => 'mall',
                'address' => 'SM North EDSA, Quezon City',
                'subclass1' => 'Standard',
                'subclass2' => 'High Traffic',
                'contact_num' => '+63-2-8888-6789',
                'manager_name' => 'James Rivera',
                'remarks' => 'High volume location',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
            ],
            [
                'name' => 'Trinoma',
                'code' => 'BR007',
                'category' => 'mall',
                'address' => 'Trinoma Mall, North Avenue, Quezon City',
                'subclass1' => 'Standard',
                'contact_num' => '+63-2-8888-7890',
                'manager_name' => 'Rosa Fernandez',
                'remarks' => 'Family-friendly location',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
            ],
            [
                'name' => 'Online Warehouse',
                'code' => 'BR008',
                'category' => 'warehouse',
                'address' => '123 Industrial Road, Laguna',
                'subclass1' => 'Online',
                'subclass2' => 'Fulfillment',
                'contact_num' => '+63-49-8888-8901',
                'manager_name' => 'Daniel Ong',
                'remarks' => 'E-commerce fulfillment center',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
            ],
        ];

        foreach ($branches as $branchData) {
            Branch::updateOrCreate(
                ['code' => $branchData['code']],
                $branchData
            );
        }
    }
}

