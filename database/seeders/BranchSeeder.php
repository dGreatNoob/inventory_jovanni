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
            // LUZON Branches
            [
                'name' => 'SM Megamall Branch',
                'code' => 'LUZ001',
                'category' => 'mall',
                'address' => 'SM Megamall, Ortigas Avenue, Mandaluyong City',
                'subclass1' => 'Premium',
                'subclass2' => 'High Traffic',
                'contact_num' => '+63-2-8888-1234',
                'manager_name' => 'Anna Rodriguez',
                'remarks' => 'Main flagship store',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
                'batch' => 'BATCH-01',
            ],
            [
                'name' => 'Greenhills Shopping Center',
                'code' => 'LUZ002',
                'category' => 'mall',
                'address' => 'Greenhills Shopping Center, San Juan City',
                'subclass1' => 'Standard',
                'subclass2' => 'Medium Traffic',
                'contact_num' => '+63-2-8888-2345',
                'manager_name' => 'Carlos Mendoza',
                'remarks' => 'Located near food court',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
                'batch' => 'BATCH-01',
            ],
            [
                'name' => 'Robinsons Galleria',
                'code' => 'LUZ003',
                'category' => 'mall',
                'address' => 'Robinsons Galleria, Ortigas Avenue, Quezon City',
                'subclass1' => 'Premium',
                'subclass3' => 'VIP Section',
                'contact_num' => '+63-2-8888-3456',
                'manager_name' => 'Lisa Tan',
                'remarks' => 'VIP customer area available',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
                'batch' => 'BATCH-01',
            ],
            [
                'name' => 'Glorietta Makati',
                'code' => 'LUZ004',
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
                'batch' => 'BATCH-01',
            ],
            [
                'name' => 'SM North EDSA',
                'code' => 'LUZ005',
                'category' => 'mall',
                'address' => 'SM North EDSA, Quezon City',
                'subclass1' => 'Standard',
                'subclass2' => 'High Traffic',
                'contact_num' => '+63-2-8888-6789',
                'manager_name' => 'James Rivera',
                'remarks' => 'High volume location',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
                'batch' => 'BATCH-01',
            ],

            // VISAYAS Branches  
            [
                'name' => 'Ayala Center Cebu',
                'code' => 'VIS001',
                'category' => 'mall',
                'address' => 'Ayala Center Cebu, Cebu City',
                'subclass1' => 'Standard',
                'subclass2' => 'High Traffic',
                'contact_num' => '+63-32-8888-4567',
                'manager_name' => 'Miguel Santos',
                'remarks' => 'Regional flagship store',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
                'batch' => 'BATCH-02',
            ],
            [
                'name' => 'SM City Iloilo',
                'code' => 'VIS002',
                'category' => 'mall',
                'address' => 'SM City Iloilo, Mandurriao, Iloilo City',
                'subclass1' => 'Standard',
                'contact_num' => '+63-33-8888-7890',
                'manager_name' => 'Rosa Fernandez',
                'remarks' => 'Western Visayas hub',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
                'batch' => 'BATCH-02',
            ],

            // MINDANAO Branches
            [
                'name' => 'Abreeza Mall Davao',
                'code' => 'MIN001',
                'category' => 'mall',
                'address' => 'Abreeza Mall, J.P. Laurel Avenue, Davao City',
                'subclass1' => 'Premium',
                'subclass2' => 'High Traffic',
                'contact_num' => '+63-82-8888-9012',
                'manager_name' => 'Antonio Reyes',
                'remarks' => 'Mindanao flagship store',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
                'batch' => 'BATCH-03',
            ],
            [
                'name' => 'Centrio Mall Cagayan de Oro',
                'code' => 'MIN002',
                'category' => 'mall',
                'address' => 'Centrio Mall, Corrales Avenue, Cagayan de Oro City',
                'subclass1' => 'Standard',
                'contact_num' => '+63-88-8888-3456',
                'manager_name' => 'Maria Gonzales',
                'remarks' => 'Northern Mindanao branch',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
                'batch' => 'BATCH-03',
            ],

            // WAREHOUSE
            [
                'name' => 'Main Warehouse',
                'code' => 'WH001',
                'category' => 'warehouse',
                'address' => '123 Industrial Road, Laguna',
                'subclass1' => 'Distribution',
                'subclass2' => 'Fulfillment',
                'contact_num' => '+63-49-8888-8901',
                'manager_name' => 'Daniel Ong',
                'remarks' => 'Main distribution center',
                'company_name' => 'Jovanni Bags Inc.',
                'company_tin' => '123-456-789-000',
                'batch' => 'BATCH-04',
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

