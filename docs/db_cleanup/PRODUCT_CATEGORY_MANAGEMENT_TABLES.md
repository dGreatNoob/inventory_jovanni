# Product Category Management Module - Database Tables Analysis

**Route:** `/product-management/categories`  
**Component:** `App\Livewire\Pages\ProductManagement\CategoryManagement`  
**View:** `resources/views/livewire/pages/product-management/category-management.blade.php`  
**Service:** `App\Services\CategoryService`

## Directly Used Tables

### 1. `categories` (Primary Table)
- **Model:** `App\Models\Category`
- **Usage:**
  - Main CRUD operations (create, read, update, delete)
  - Search and filtering (by name, description)
  - Status filtering (active, inactive)
  - Sorting (by sort_order, name)
  - Category tree/hierarchy display
  - Bulk operations (delete, activate, deactivate)
  - Reordering categories
- **Relationships:**
  - `belongsTo` → `parent` (Category model, self-referential)
  - `hasMany` → `children` (Category model, self-referential)
  - `hasMany` → `products` (Product model)
- **Fields Used:**
  - `entity_id` - Multi-tenant support
  - `name` - Category name (search, display)
  - `description` - Category description (search, display)
  - `parent_id` - Parent category ID (for hierarchy)
  - `sort_order` - Display order
  - `slug` - URL-friendly slug (unique, auto-generated)
  - `is_active` - Active status (filtering, bulk actions)
- **Features:**
  - Soft deletes (`deleted_at` column)
  - Hierarchical structure (parent-child relationships)
  - Auto-generated slug from name
  - Sort order management
  - Product count tracking

### 2. `products`
- **Model:** `App\Models\Product`
- **Usage:**
  - Count products per category (for statistics)
  - Prevent deletion if category has products
  - Display product count in category list
  - Get top categories by product count
  - Load products when viewing category details
- **Relationships:**
  - `belongsTo` → `category` (Category model)
- **Methods:**
  - `Category::withCount('products')` - Count products per category
  - `$category->products()->count()` - Check if category has products

## Indirectly Used Tables (via Relationships)

### 3. `users`
- **Model:** `App\Models\User`
- **Usage:**
  - Permission checks (`category view`, `category create`, `category edit`, `category delete`)
- **Methods:**
  - `auth()->user()->hasAnyPermission()`

## Summary

**Total Tables Used: 3**

1. ✅ `categories` - Primary table
2. ✅ `products` - Product count and deletion prevention
3. ✅ `users` - Permissions

## Notes

- **Soft Deletes:** `categories` table uses soft deletes
- **Hierarchical Structure:** Supports parent-child relationships (self-referential)
- **Slug Generation:** Auto-generates URL-friendly slug from category name
- **Deletion Protection:** Cannot delete category if it has associated products
- **Sort Order:** Categories can be reordered via `sort_order` field
- **Flat Structure:** Currently uses flat structure (parent_id set to null), but supports hierarchy
- **Statistics:** Tracks total categories, active/inactive counts, and top categories by product count
- **Category Tree:** Can build hierarchical tree structure for display

