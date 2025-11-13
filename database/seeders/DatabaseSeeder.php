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
        // Only seed user and role/permission data
        $this->call([
            DepartmentSeeder::class,
            RoleAndPermissionSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
