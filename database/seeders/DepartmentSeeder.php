<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Admin Department', 'description' => 'Manages overall flow of the company.'],
            ['name' => 'Purchase Department', 'description' => 'Handles purchase orders and vendor management.'],
            ['name' => 'Supply Department', 'description' => 'Handles procurement and supply chain management.'],
            ['name' => 'Raw Materials Department', 'description' => 'Handles Paper Roll Warehouse and Distribution'],
            ['name' => 'Engineering Department', 'description' => 'Manages supply orders and inventory control.'], 
            ['name' => 'Bodegero Department', 'description' => 'Scans Stock Ins of Rawmat and Supply.'],
            
            
        ];

        foreach ($departments as $department) {
            \App\Models\Department::updateOrCreate(
                ['name' => $department['name']],
                $department
            );
        }
    }
}
