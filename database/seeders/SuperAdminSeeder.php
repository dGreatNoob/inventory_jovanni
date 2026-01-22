<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            [
                'email' => 'superadmin@jovanni.com',
            ],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123!'),
            ]
        );

        $permissions = Permission::all()->pluck('name')->toArray();

        if (! empty($permissions)) {
            $user->syncPermissions($permissions);
        }
    }
}

