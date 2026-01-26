# Branch Inventory Module - Database Tables Analysis

**Route:** `/branch-inventory`  
**Component:** `App\Livewire\Pages\Branch\BranchInventory`  
**View:** `resources/views/livewire/pages/branch/branch-inventory.blade.php`

## Directly Used Tables

### 1. `branches`
- **Model:** `App\Models\Branch`
- **Usage:**
  - Load all branches for inventory display
  - Display branch names in inventory matrix
  - Eager load products relationship
- **Relationships:**
  - `belongsToMany` → `products` (Product model, via `branch_product` pivot table)
- **Fields Used:**
  - `id` - Branch ID (primary key, matrix mapping)
  - `name` - Branch name (display in table rows)
- **Methods:**
  - `Branch::with('products')->get()` - Load branches with products relationship
  - `$branch->products->find($productId)?->pivot->stock` - Get stock from pivot table
- **Features:**
  - Eager loading of products to prevent N+1 queries
  - Matrix view (branches as rows, products as columns)

### 2. `products`
- **Model:** `App\Models\Product`
- **Usage:**
  - Load all products for inventory display
  - Display product names and prices in table headers
  - Build inventory matrix
- **Relationships:**
  - `belongsToMany` → `branches` (Branch model, via `branch_product` pivot table)
- **Fields Used:**
  - `id` - Product ID (primary key, matrix mapping)
  - `name` - Product name (display in table headers)
  - `price` / `selling_price` - Product price (display in table headers)
- **Methods:**
  - `Product::all()` - Get all products
  - `$product->price ?? $product->selling_price ?? 0` - Get product price
- **Features:**
  - Matrix view (products as columns, branches as rows)
  - Price display in table headers

### 3. `branch_product` (Pivot Table)
- **Table:** `branch_product`
- **Usage:**
  - Store branch-product stock levels
  - Link branches to products with stock quantity
  - Read-only inventory display
- **Fields Used:**
  - `branch_id` - Branch reference (foreign key)
  - `product_id` - Product reference (foreign key)
  - `stock` - Stock quantity (display, default: 0)
- **Relationships:**
  - Links `branches` to `products` (many-to-many)
- **Methods:**
  - `$branch->products->find($productId)?->pivot->stock` - Get stock for branch-product combination
  - Access via `belongsToMany` relationship with `withPivot('stock')`
- **Features:**
  - Stock quantity per branch-product combination
  - Default stock value is 0 if no record exists
  - Read-only view (no editing capabilities in this module)

## Summary

**Total Tables Used: 3**

1. ✅ `branches` - Branch information and display
2. ✅ `products` - Product information and display
3. ✅ `branch_product` - Stock levels per branch-product combination

## Notes

- **Read-Only View:** This module is read-only, displaying current stock levels
- **Matrix Display:** Shows inventory as a matrix (branches = rows, products = columns)
- **Stock Mapping:** Builds a mapping of branch_id => product_id => stock for efficient display
- **Default Stock:** If no stock record exists for a branch-product combination, displays 0
- **Price Display:** Shows product prices in table headers
- **Eager Loading:** Uses eager loading to prevent N+1 queries when accessing pivot data
- **Pivot Access:** Accesses stock via `$branch->products->find($productId)?->pivot->stock`
- **No CRUD Operations:** This module only displays data, does not create, update, or delete stock records
- **Stock Management:** Stock is likely managed through other modules (allocation, sales receipts, etc.)

