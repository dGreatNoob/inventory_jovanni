# Product Management Module - Database Tables Analysis

**Route:** `/product-management`  
**Component:** `App\Livewire\Pages\ProductManagement\Index`  
**View:** `resources/views/livewire/pages/product-management/index.blade.php`  
**Service:** `App\Services\ProductService`

## Directly Used Tables

### 1. `products` (Primary Table)
- **Model:** `App\Models\Product`
- **Usage:**
  - Main CRUD operations (create, read, update, delete)
  - Search and filtering (by name, SKU, barcode, supplier, category)
  - Advanced filtering (category, supplier, stock level, price range)
  - Sorting (by various fields)
  - Grid and table view modes
  - Bulk operations (delete, update category, update supplier, enable/disable)
  - Product details modal
  - Image viewer
- **Relationships:**
  - `belongsTo` → `category` (Category model)
  - `belongsTo` → `supplier` (Supplier model)
  - `belongsTo` → `color` (ProductColor model)
  - `hasMany` → `images` (ProductImage model)
  - `hasMany` → `inventory` (ProductInventory model)
  - `hasMany` → `movements` (InventoryMovement model)
  - `hasMany` → `priceHistories` (ProductPriceHistory model)
  - `hasMany` → `batches` (ProductBatch model)
  - `belongsToMany` → `branches` (Branch model, via `branch_product` pivot)
- **Features:**
  - Soft deletes (`deleted_at` column)
  - JSON field: `specs` (array)
  - Auto-generated barcode from product_number + color_code + price
  - Price note tracking (REG1, SAL1, etc.)

### 2. `categories`
- **Model:** `App\Models\Category`
- **Usage:**
  - Filter dropdown for product categories
  - Display category in product list
  - Bulk update category for selected products
  - Statistics (total categories count)
- **Methods:**
  - `Category::active()` - Get active categories
- **Relationships:**
  - `hasMany` → `products` (Product model)

### 3. `suppliers`
- **Model:** `App\Models\Supplier`
- **Usage:**
  - Filter dropdown for suppliers
  - Display supplier in product list
  - Auto-populate supplier_code when supplier selected
  - Bulk update supplier for selected products
  - Statistics (total suppliers count)
  - Search products by supplier name
- **Methods:**
  - `Supplier::active()` - Get active suppliers
- **Relationships:**
  - `hasMany` → `products` (Product model)

### 4. `product_colors`
- **Model:** `App\Models\ProductColor`
- **Usage:**
  - Color dropdown in product form
  - Create new colors inline
  - Auto-generate color codes (4-digit numeric)
  - Used in barcode generation
  - Display color information
- **Fields Used:**
  - `code` - 4-digit numeric code
  - `name` - Color name
  - `shortcut` - Shortcut/abbreviation
- **Relationships:**
  - `hasMany` → `products` (Product model)

### 5. `product_images`
- **Model:** `App\Models\ProductImage`
- **Usage:**
  - Display product images in grid/table views
  - Image viewer modal (navigation between images)
  - Product details modal with image gallery
  - Primary image selection
  - Image sorting
- **Relationships:**
  - `belongsTo` → `product` (Product model)
- **Fields Used:**
  - `filename` - Image filename
  - `is_primary` - Primary image flag
  - `sort_order` - Display order
  - `alt_text` - Alt text for accessibility

### 6. `product_price_histories`
- **Model:** `App\Models\ProductPriceHistory`
- **Usage:**
  - Track price changes over time
  - Display price history in product form
  - Calculate price change counts (regular vs sale)
  - Generate next price note (REG1, REG2, SAL1, SAL2, etc.)
- **Relationships:**
  - `belongsTo` → `product` (Product model)
  - `belongsTo` → `changedBy` (User model)
- **Fields Used:**
  - `old_price` - Previous price
  - `new_price` - New price
  - `pricing_note` - Price note (REG1, SAL1, etc.)
  - `changed_at` - Change timestamp
  - `changed_by` - User who changed the price

### 7. `product_inventory`
- **Model:** `App\Models\ProductInventory`
- **Usage:**
  - Track product quantities by location
  - Calculate total quantity (sum of all inventory records)
  - Stock level filtering (in stock, low stock, out of stock)
  - Initial inventory creation when product is created
  - Display stock status
- **Relationships:**
  - `belongsTo` → `product` (Product model)
  - `belongsTo` → `location` (InventoryLocation model)
- **Fields Used:**
  - `quantity` - Total quantity
  - `reserved_quantity` - Reserved quantity
  - `available_quantity` - Available quantity (quantity - reserved)
  - `reorder_point` - Reorder threshold
  - `max_stock` - Maximum stock level

### 8. `inventory_movements`
- **Model:** `App\Models\InventoryMovement`
- **Usage:**
  - Track inventory movements (stock in/out)
  - Create initial movement when product is created
  - Analytics and reporting (via ProductService)
  - Movement history
- **Relationships:**
  - `belongsTo` → `product` (Product model)
  - `belongsTo` → `location` (InventoryLocation model)
  - `belongsTo` → `creator` (User model)
  - `morphTo` → `reference` (polymorphic)
- **Fields Used:**
  - `movement_type` - Type of movement (purchase, sale, adjustment, etc.)
  - `quantity` - Movement quantity
  - `unit_cost` - Unit cost
  - `total_cost` - Total cost
  - `reference_type` / `reference_id` - Polymorphic reference

## Indirectly Used Tables (via Relationships)

### 9. `inventory_locations`
- **Model:** `App\Models\InventoryLocation`
- **Usage:**
  - Referenced via `product_inventory` relationship
  - Location tracking for inventory records
- **Relationships:**
  - `hasMany` → `productInventory` (ProductInventory model)
  - `hasMany` → `inventoryMovements` (InventoryMovement model)

### 10. `branch_product` (Pivot Table)
- **Usage:**
  - Links products to branches
  - Stores branch stock levels
  - Created automatically when product is created
- **Fields:**
  - `product_id` - Product ID
  - `branch_id` - Branch ID
  - `stock` - Stock quantity in branch

### 11. `branches`
- **Model:** `App\Models\Branch`
- **Usage:**
  - Auto-attach products to all branches when created
  - Branch stock tracking via pivot table
- **Relationships:**
  - `belongsToMany` → `products` (Product model, via `branch_product` pivot)

### 12. `product_batches`
- **Model:** `App\Models\ProductBatch`
- **Usage:**
  - Batch tracking for products
  - Referenced via Product model relationship
- **Relationships:**
  - `belongsTo` → `product` (Product model)
  - `belongsTo` → `purchaseOrder` (PurchaseOrder model)
  - `belongsTo` → `receivedByUser` (User model)

### 13. `users`
- **Model:** `App\Models\User`
- **Usage:**
  - Permission checks (`product view`, `product create`, `product edit`, `product delete`, `product export`)
  - Track who created/updated products (`created_by`, `updated_by`)
  - Track who changed prices (`changed_by` in ProductPriceHistory)
  - Track who created inventory movements (`created_by`)
- **Methods:**
  - `auth()->user()->hasAnyPermission()`

## Summary

**Total Tables Used: 13**

1. ✅ `products` - Primary table
2. ✅ `categories` - Filtering and display
3. ✅ `suppliers` - Filtering and display
4. ✅ `product_colors` - Color management
5. ✅ `product_images` - Image management
6. ✅ `product_price_histories` - Price tracking
7. ✅ `product_inventory` - Inventory tracking
8. ✅ `inventory_movements` - Movement tracking
9. ✅ `inventory_locations` - Location tracking
10. ✅ `branch_product` - Branch stock pivot
11. ✅ `branches` - Branch information
12. ✅ `product_batches` - Batch tracking
13. ✅ `users` - Permissions and tracking

## Notes

- **Soft Deletes:** `products` table uses soft deletes
- **JSON Field:** `products.specs` stores array data
- **Barcode Generation:** Auto-generated from product_number (6 digits) + color_code (4 digits) + price (6 digits)
- **Price Note System:** Tracks price changes with notes (REG1, REG2, SAL1, SAL2, etc.)
- **Initial Inventory:** Creates ProductInventory record when product is created with initial_quantity
- **Branch Auto-Attach:** Automatically attaches new products to all branches via pivot table
- **Stock Calculation:** Total quantity is sum of all ProductInventory records (not a single column)
- **Image Management:** Supports primary image, sorting, and image viewer navigation
- **Bulk Operations:** Supports bulk delete, bulk update category, bulk update supplier, bulk enable/disable

