# User Management Module - Database Tables Analysis

**Route:** `/user-management`  
**Component:** `App\Livewire\Pages\User\Index`  
**View:** `resources/views/livewire/pages/user/index.blade.php`

## Directly Used Tables

### 1. `users` (Primary Table)
- **Model:** `App\Models\User`
- **Usage:**
  - Main CRUD operations (create, read, update, delete)
  - Search and filtering (by name, email, department, role)
  - User authentication and authorization
  - Password management (hashing, updates)
- **Relationships:**
  - `belongsTo` → `department` (Department model)
  - `hasMany` → `roles` (via Spatie Permission - polymorphic)
  - `hasMany` → `purchaseOrders` (PurchaseOrder model, via `ordered_by`)
  - `hasMany` → `approvedPurchaseOrders` (PurchaseOrder model, via `approver`)
  - `hasMany` → `requestSlips` (RequestSlip model, via `requested_by`)
- **Fields Used:**
  - `id` - User ID (primary key, relationships)
  - `name` - User name (search, display, validation)
  - `email` - User email (search, display, unique validation, authentication)
  - `password` - Hashed password (authentication, updates)
  - `department_id` - Department reference (foreign key, filtering, display)
  - `created_at` - Creation timestamp (sorting)
  - `updated_at` - Update timestamp
- **Features:**
  - Password hashing using `Hash::make()`
  - Optional password updates (only if provided)
  - Email uniqueness validation
  - Search across name, email, department, and roles
  - Eager loading of department and roles relationships

### 2. `departments`
- **Model:** `App\Models\Department`
- **Usage:**
  - Department selection for user assignment
  - Search users by department name
  - Display department information with users
- **Relationships:**
  - `hasMany` → `users` (User model)
  - `hasMany` → `purchaseOrders` (PurchaseOrder model)
  - `hasMany` → `requestSlipsSentFrom` (RequestSlip model)
  - `hasMany` → `requestSlipsSentTo` (RequestSlip model)
- **Fields Used:**
  - `id` - Department ID (foreign key, validation)
  - `name` - Department name (search, display, ordering)
  - `description` - Department description (if displayed)
- **Methods:**
  - `Department::orderBy('name')` - Order departments by name
  - `User::whereHas('department', ...)` - Search users by department name

### 3. `roles` (Spatie Permission)
- **Model:** `Spatie\Permission\Models\Role`
- **Usage:**
  - Role selection for user assignment
  - Display user roles
  - Search users by role name
  - Role assignment and synchronization
- **Relationships:**
  - `belongsToMany` → `users` (via `model_has_roles` pivot table)
  - `belongsToMany` → `permissions` (via `role_has_permissions` pivot table)
- **Fields Used:**
  - `id` - Role ID (foreign key, validation, selection)
  - `name` - Role name (display, search, assignment)
  - `guard_name` - Guard name (default: 'web')
  - `created_at` - Creation timestamp
  - `updated_at` - Update timestamp
- **Methods:**
  - `Role::orderBy('name')` - Order roles by name
  - `Role::findOrFail($id)` - Find role by ID
  - `Role::where('name', $roleName)->value('id')` - Get role ID by name
  - `$user->assignRole($roleName)` - Assign role to user
  - `$user->syncRoles([$roleName])` - Sync roles for user
  - `$user->getRoleNames()` - Get user's role names
  - `User::whereHas('roles', ...)` - Search users by role name

## Indirectly Used Tables (via Spatie Permission)

### 4. `model_has_roles` (Pivot Table)
- **Table:** `model_has_roles`
- **Usage:**
  - Stores user-role relationships (polymorphic)
  - Used by Spatie Permission package for role assignments
  - Automatically managed by `assignRole()`, `syncRoles()`, etc.
- **Fields Used:**
  - `role_id` - Role ID (foreign key to `roles` table)
  - `model_type` - Model type (e.g., 'App\Models\User')
  - `model_id` - User ID (polymorphic foreign key)
- **Relationships:**
  - Links `users` to `roles` (many-to-many polymorphic)
- **Methods:**
  - Managed automatically by Spatie Permission trait methods
  - `DB::table('model_has_roles')->where('model_id', $user->id)->where('model_type', 'App\\Models\\User')->value('role_id')` - Direct query (used in blade view)

### 5. `permissions` (Spatie Permission)
- **Model:** `Spatie\Permission\Models\Permission`
- **Usage:**
  - Indirectly used through roles
  - Permission checks for access control
  - Not directly queried in this module, but used for authorization
- **Relationships:**
  - `belongsToMany` → `roles` (via `role_has_permissions` pivot table)
  - `belongsToMany` → `users` (via `model_has_permissions` pivot table)
- **Fields Used:**
  - `id` - Permission ID
  - `name` - Permission name (e.g., 'user view', 'user create')
  - `guard_name` - Guard name
- **Methods:**
  - `auth()->user()->hasAnyPermission(['user view'])` - Permission check

### 6. `role_has_permissions` (Pivot Table)
- **Table:** `role_has_permissions`
- **Usage:**
  - Stores role-permission relationships
  - Used by Spatie Permission package
  - Not directly queried in this module
- **Fields Used:**
  - `permission_id` - Permission ID (foreign key)
  - `role_id` - Role ID (foreign key)
- **Relationships:**
  - Links `roles` to `permissions` (many-to-many)

### 7. `model_has_permissions` (Pivot Table)
- **Table:** `model_has_permissions`
- **Usage:**
  - Stores direct user-permission relationships (if used)
  - Used by Spatie Permission package
  - Not directly queried in this module
- **Fields Used:**
  - `permission_id` - Permission ID (foreign key)
  - `model_type` - Model type
  - `model_id` - User ID (polymorphic foreign key)
- **Relationships:**
  - Links `users` directly to `permissions` (many-to-many polymorphic)

## Summary

**Total Tables Used: 7**

1. ✅ `users` - Primary table
2. ✅ `departments` - Department assignment and filtering
3. ✅ `roles` - Role assignment and filtering
4. ✅ `model_has_roles` - User-role relationships (pivot)
5. ✅ `permissions` - Permission checks (indirect)
6. ✅ `role_has_permissions` - Role-permission relationships (pivot, indirect)
7. ✅ `model_has_permissions` - User-permission relationships (pivot, indirect)

## Notes

- **Spatie Permission Package:** Uses Spatie Laravel Permission package for role and permission management
- **Polymorphic Relationships:** User-role and user-permission relationships are polymorphic (can work with any model)
- **Role Assignment:** Users can have one role assigned at a time (uses `syncRoles()` with single role)
- **Password Management:** Passwords are hashed using Laravel's `Hash::make()` and only updated if provided
- **Search Functionality:** Searches across user name, email, department name, and role name
- **Permission Checks:** Module checks for 'user view' permission before rendering
- **Email Uniqueness:** Email must be unique across all users (validated on create and update)
- **Department Assignment:** Users must be assigned to a department (required on create, nullable on update)
- **Role Selection:** Users must have a role assigned (required validation)
- **Eager Loading:** Users are loaded with `department` and `roles` relationships to avoid N+1 queries
- **URL Parameter:** Search query is stored in URL parameter `q` for shareable/bookmarkable URLs

