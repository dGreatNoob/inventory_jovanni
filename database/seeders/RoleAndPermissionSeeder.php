<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use App\Models\Department;
use App\Enums\Enum\PermissionEnum;
use App\Enums\RolesEnum;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Step 1: Create all permissions
        foreach (PermissionEnum::cases() as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission->value],
                ['guard_name' => 'web']
            );
        }

        // ✅ Step 2: Define roles and their permissions
        $roles = [
            RolesEnum::PURCHASER->value => [
                PermissionEnum::CREATE_SUPPLY_PURCHASE_ORDER,
                PermissionEnum::APPROVE_SUPPLY_PURCHASE_ORDER,
                PermissionEnum::VIEW_SUPPLY_PURCHASE_ORDER,

                PermissionEnum::CREATE_RAWMAT_PURCHASE_ORDER,
                PermissionEnum::APPROVE_RAWMAT_PURCHASE_ORDER,
                PermissionEnum::VIEW_RAWMAT_PURCHASE_ORDER,

                PermissionEnum::CREATE_REQUEST_SLIP,
                PermissionEnum::APPROVE_REQUEST_SLIP,
                PermissionEnum::VIEW_REQUEST_SLIP,
                PermissionEnum::DELETE_REQUEST_SLIP,
            ],

            RolesEnum::RAWMAT->value => [
                PermissionEnum::CREATE_REQUEST_SLIP,
                PermissionEnum::VIEW_REQUEST_SLIP,
            ],

            RolesEnum::SUPPLY->value => [
                PermissionEnum::CREATE_REQUEST_SLIP,
                PermissionEnum::VIEW_REQUEST_SLIP,
            ],

            RolesEnum::SUPERADMIN->value => PermissionEnum::cases(), // all permissions
        ];

        // ✅ Step 3: Create roles and assign permissions
        foreach ($roles as $roleName => $permissions) {
            $role = Role::updateOrCreate(
                ['name' => $roleName, 'guard_name' => 'web']
            );

            $permissionNames = collect($permissions)->map(fn($p) => $p->value)->toArray();
            $role->syncPermissions($permissionNames);
        }

        // Create and assign roles to users with correct department IDs
        $adminDept = Department::where('name', 'Admin Department')->firstOrFail();
        $super_admin = User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => bcrypt('admin123!'),

            'department_id' => $adminDept->id,

        ]);
        $super_admin->assignRole(RolesEnum::SUPERADMIN->value);


        $purchaseDept = Department::where('name', 'Purchase Department')->firstOrFail();
        $purchaser = User::factory()->create([
            'name' => 'Mrs. Purchaser',
            'email' => 'purchaser@spc.com',
            'email_verified_at' => now(),
            'password' => bcrypt('admin123!'),
            'department_id' => $purchaseDept->id,
        ]);
        $purchaser->assignRole(RolesEnum::PURCHASER->value);

        $rawmatDept = Department::where('name', 'Raw Materials Department')->firstOrFail();
        $rawmat = User::factory()->create([
            'name' => 'Mr. Rawmat',
            'email' => 'rawmat@spc.com',
            'email_verified_at' => now(),
            'password' => bcrypt('admin123!'),
            'department_id' => $rawmatDept->id,
        ]);
        $rawmat->assignRole(RolesEnum::RAWMAT->value);

        $supplyDept = Department::where('name', 'Supply Department')->firstOrFail();
        $supply = User::factory()->create([
            'name' => 'Mr. Supply',
            'email' => 'supply@spc.com',
            'email_verified_at' => now(),
            'password' => bcrypt('admin123!'),
            'department_id' => $supplyDept->id,
        ]);
        $supply->assignRole(RolesEnum::SUPPLY->value);
    }
}
