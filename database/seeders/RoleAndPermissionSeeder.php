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

            RolesEnum::PRODUCTMANAGEMENT->value => [
                PermissionEnum::PRODUCT_VIEW,          // View product list and details
                PermissionEnum::PRODUCT_CREATE,        // Add new product profiles
                PermissionEnum::PRODUCT_EDIT,          // Edit existing product profiles
                PermissionEnum::PRODUCT_DELETE,        // Remove product profiles
                PermissionEnum::PRODUCT_EXPORT,        // Export product catalog/report
            ],

            RolesEnum::ABMANAGEMENT->value => [
                PermissionEnum::AGENT_VIEW,            // View agent list and details
                PermissionEnum::AGENT_CREATE,          // Add new agent profiles
                PermissionEnum::AGENT_EDIT,            // Edit agent profiles
                PermissionEnum::AGENT_DELETE,          // Remove agents
                PermissionEnum::BRANCH_VIEW,           // View branches list and details
                PermissionEnum::BRANCH_CREATE,         // Add new branches
                PermissionEnum::BRANCH_EDIT,           // Edit existing branch details
                PermissionEnum::BRANCH_DELETE,         // Remove branches
                PermissionEnum::AGENT_ASSIGN_BRANCH,   // Assign agents to branches
                PermissionEnum::AGENT_TRANSFER_BRANCH, // Transfer or reassign agent to another branch
            ],

            RolesEnum::SUPPLIERMANAGEMENT->value => [
                PermissionEnum::SUPPLIER_VIEW,         // View supplier list and details
                PermissionEnum::SUPPLIER_CREATE,       // Add new supplier
                PermissionEnum::SUPPLIER_EDIT,         // Edit supplier information
                PermissionEnum::SUPPLIER_DELETE,       // Remove supplier profile
                PermissionEnum::SUPPLIER_REPORT_VIEW,  // View supplier performance reports
            ],

            RolesEnum::POMANAGEMENT->value => [
                PermissionEnum::PO_VIEW,          // View purchase orders
                PermissionEnum::PO_CREATE,        // Create new purchase order
                PermissionEnum::PO_EDIT,          // Edit draft or pending POs
                PermissionEnum::PO_DELETE,        // Delete cancelled or draft POs
                PermissionEnum::PO_APPROVE,       // Approve or reject POs
                PermissionEnum::PO_RECEIVE,       // Mark PO items as delivered or partially received
                PermissionEnum::PO_REPORT_VIEW,   // View PO reports and summaries
            ],

            RolesEnum::USERMANAGEMENT->value => [
                PermissionEnum::USER_VIEW,           // View user list and details
                PermissionEnum::USER_CREATE,         // Add new system user
                PermissionEnum::USER_EDIT,           // Edit user info
                PermissionEnum::USER_DELETE,         // Remove user account
                PermissionEnum::ROLE_VIEW,           // View existing roles
                PermissionEnum::ROLE_CREATE,         // Create new roles
                PermissionEnum::ROLE_EDIT,           // Edit existing roles
                PermissionEnum::ROLE_DELETE,         // Remove roles
                PermissionEnum::PERMISSION_MANAGE,   // Manage permission definitions
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
