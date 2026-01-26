<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Production environment: Only seed essential data
        if (app()->environment('production')) {
            $this->call([
                DepartmentSeeder::class,
                RoleAndPermissionSeeder::class,
                SuperAdminSeeder::class,
                ProductColorSeeder::class,
                CategorySeeder::class,
                SupplierSeeder::class,
            ]);
        } else {
            // Development/Testing: Seed all data
            $this->call([
                DepartmentSeeder::class,
                RoleAndPermissionSeeder::class,
                SuperAdminSeeder::class,
                ProductColorSeeder::class,
                CategorySeeder::class,
                SupplierSeeder::class,
                ProductSeeder::class,
                BranchSeeder::class,
                AgentSeeder::class,
                AgentBranchAssignmentSeeder::class,
                PurchaseOrderSeeder::class,
                InventoryLocationSeeder::class,
                FinanceSeeder::class,
                PromoSeeder::class,
            ]);
        }
    }
}
