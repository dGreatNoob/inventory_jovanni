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
            ['name' => 'Engineering Department', 'description' => 'Manages product orders and inventory control.'], 
            ['name' => 'Warehouse Department', 'description' => 'Manages inventory and stock operations.'],
            
            
        ];

        foreach ($departments as $department) {
            \App\Models\Department::updateOrCreate(
                ['name' => $department['name']],
                $department
            );
        }
    }
}
