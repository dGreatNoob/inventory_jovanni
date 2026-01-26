# Roles & Permissions Module - Database Tables Analysis

**Route:** `/roles-permissions`  
**Component:** `App\Livewire\Pages\RolePermission\Index`  
**View:** `resources/views/livewire/pages/role-permission/index.blade.php`

## Directly Used Tables

### 1. `roles` (Primary Table)
- **Model:** `Spatie\Permission\Models\Role`
- **Usage:**
  - Main CRUD operations (create, read, update, delete)
  - Search and filtering (by role name, permission name)
  - Role name uniqueness validation
  - Display roles with their permissions
- **Relationships:**
  - `belongsToMany` → `permissions` (via `role_has_permissions` pivot table)
  - `belongsToMany` → `users` (via `model_has_roles` pivot table, indirect)
- **Fields Used:**
  - `id` - Role ID (primary key, relationships, editing)
  - `name` - Role name (display, search, validation, unique constraint)
  - `guard_name` - Guard name (default: 'web', set on creation)
  - `created_at` - Creation timestamp (sorting)
  - `updated_at` - Update timestamp
- **Methods:**
  - `Role::create(['name' => $name, 'guard_name' => 'web'])` - Create role
  - `Role::findOrFail($id)` - Find role by ID
  - `Role::with('permissions')` - Eager load permissions
  - `Role::where('name', 'like', "%{$search}%")` - Search by name
  - `Role::latest()` - Order by creation date
  - `$role->update(['name' => $name])` - Update role name
  - `$role->syncPermissions($permissions)` - Sync role permissions
  - `$role->permissions` - Get role's permissions
  - `$role->permissions->pluck('name')` - Get permission names
  - `$role->delete()` - Delete role
- **Features:**
  - Role name must be unique
  - Guard name defaults to 'web'
  - Permissions are synced (replaces all existing permissions)
  - Eager loading of permissions to avoid N+1 queries

### 2. `permissions` (Primary Table)
- **Model:** `Spatie\Permission\Models\Permission`
- **Usage:**
  - Load all available permissions for selection
  - Display permissions assigned to roles
  - Search roles by permission name
  - Permission selection in create/edit forms
- **Relationships:**
  - `belongsToMany` → `roles` (via `role_has_permissions` pivot table)
  - `belongsToMany` → `users` (via `model_has_permissions` pivot table, indirect)
- **Fields Used:**
  - `id` - Permission ID (primary key)
  - `name` - Permission name (display, selection, search, validation)
  - `guard_name` - Guard name (default: 'web')
  - `created_at` - Creation timestamp
  - `updated_at` - Update timestamp
- **Methods:**
  - `Permission::orderBy('name')->get()` - Get all permissions ordered by name
  - `Role::whereHas('permissions', function ($q) use ($search) { $q->where('name', 'like', "%{$search}%"); })` - Search roles by permission name
  - `$role->permissions` - Get role's permissions (via relationship)
- **Features:**
  - Permissions are loaded from database but also referenced from `PermissionEnum` enum
  - Permission names must match enum values
  - Permissions are grouped by category in the UI (using `PermissionEnum::category()`)

### 3. `role_has_permissions` (Pivot Table)
- **Table:** `role_has_permissions`
- **Usage:**
  - Stores role-permission relationships (many-to-many)
  - Managed automatically by Spatie Permission package
  - Used by `syncPermissions()` method to sync role permissions
- **Fields Used:**
  - `permission_id` - Permission ID (foreign key to `permissions` table)
  - `role_id` - Role ID (foreign key to `roles` table)
- **Relationships:**
  - Links `roles` to `permissions` (many-to-many)
- **Methods:**
  - `$role->syncPermissions($permissions)` - Syncs permissions (deletes old, adds new)
  - Automatically managed by Spatie Permission package
- **Features:**
  - Composite primary key on `permission_id` and `role_id`
  - Cascade delete on role deletion
  - Automatically synced when using `syncPermissions()`

## Indirectly Used Tables (via Spatie Permission)

### 4. `model_has_roles` (Pivot Table)
- **Table:** `model_has_roles`
- **Usage:**
  - Not directly queried in this module
  - Used by Spatie Permission to link users to roles
  - Referenced indirectly when roles are assigned to users
- **Fields Used:**
  - `role_id` - Role ID (foreign key)
  - `model_type` - Model type (e.g., 'App\Models\User')
  - `model_id` - User ID (polymorphic foreign key)
- **Note:** This table is not directly used in the Roles & Permissions module, but roles created here are used in the User Management module

### 5. `model_has_permissions` (Pivot Table)
- **Table:** `model_has_permissions`
- **Usage:**
  - Not directly queried in this module
  - Used by Spatie Permission for direct user-permission assignments
  - Not used in this module's workflow
- **Note:** This table exists but is not used in the Roles & Permissions module

## Summary

**Total Tables Used: 3 (Directly)**

1. ✅ `roles` - Primary table for role management
2. ✅ `permissions` - Primary table for permission management
3. ✅ `role_has_permissions` - Pivot table linking roles to permissions

**Indirect Tables: 2**

4. ⚠️ `model_has_roles` - User-role relationships (not directly used in this module)
5. ⚠️ `model_has_permissions` - User-permission relationships (not directly used in this module)

## Notes

- **Spatie Permission Package:** Uses Spatie Laravel Permission package for role and permission management
- **Permission Enum:** Permissions are also defined in `App\Enums\Enum\PermissionEnum` enum class
- **Permission Categories:** Permissions are grouped by category in the UI using `PermissionEnum::category()` method
- **Select All/Deselect All:** Quick actions to select or deselect all permissions from the enum
- **Permission Sync:** Uses `syncPermissions()` which replaces all existing permissions (not additive)
- **Role Name Uniqueness:** Role names must be unique (validated on create and update)
- **Guard Name:** All roles and permissions use 'web' guard name
- **Search Functionality:** Searches across role name and permission names
- **Permission Validation:** At least one permission must be selected when creating/updating a role
- **Eager Loading:** Roles are loaded with permissions to avoid N+1 queries
- **Permission Display:** Permissions are displayed as badges/tags in the roles table
- **Permission Enum Reference:** The module uses `PermissionEnum::cases()` to get all available permissions, but stores/retrieves them from the database
- **Cascade Delete:** When a role is deleted, its relationships in `role_has_permissions` are automatically deleted (cascade)

