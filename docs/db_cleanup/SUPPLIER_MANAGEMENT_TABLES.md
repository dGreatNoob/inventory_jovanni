# Supplier Management Profile Module - Database Tables Analysis

**Route:** `/suppliermanagement/profile`  
**Component:** `App\Livewire\Pages\SupplierManagement\Profile\Index`  
**View Component:** `App\Livewire\Pages\SupplierManagement\Profile\View`

## Directly Used Tables

### 1. `suppliers` (Primary Table)
- **Model:** `App\Models\Supplier`
- **Usage:**
  - Main CRUD operations (create, read, update, delete)
  - Dashboard metrics (total suppliers, active suppliers)
  - Search and filtering
  - Status filtering (active, inactive, pending)
  - Category filtering (via JSON field `categories`)
- **Relationships:**
  - `hasMany` → `products` (Product model)
  - `hasMany` → `purchaseOrders` (PurchaseOrder model)
- **Features:**
  - Soft deletes (`deleted_at` column)
  - Activity logging (Spatie Activity Log)
  - JSON field: `categories` (array of category IDs)

### 2. `categories`
- **Model:** `App\Models\Category`
- **Usage:**
  - Filter dropdown for supplier categories
  - Display categories associated with suppliers
  - Get categories from supplier's products
- **Methods:**
  - `Category::active()` - Get active categories
  - `getAllCategoriesForSupplier()` - Merge supplier's direct categories + product categories

### 3. `products`
- **Model:** `App\Models\Product`
- **Usage:**
  - Count products per supplier (`products_count`)
  - Filter suppliers by product categories
  - Display in supplier view page (View component)
  - Edit/delete products from supplier view
- **Relationships:**
  - `belongsTo` → `supplier` (Supplier model)
  - `belongsTo` → `category` (Category model)

### 4. `purchase_orders`
- **Model:** `App\Models\PurchaseOrder`
- **Usage:**
  - Dashboard metrics (total orders, total value)
  - Display recent purchase orders in supplier view
  - Calculate delivery performance
  - Filter by supplier
- **Relationships:**
  - `belongsTo` → `supplier` (Supplier model)
  - `hasMany` → `productOrders` (ProductOrder model)
  - `hasMany` → `deliveries` (PurchaseOrderDelivery model)
- **Fields Used:**
  - `total_price` - Sum for total ordered value
  - `order_date` - Display and sorting
  - `expected_delivery_date` - Delivery tracking
  - `status` - Status display

### 5. `product_orders`
- **Model:** `App\Models\ProductOrder`
- **Usage:**
  - Calculate delivery performance (expected vs received)
  - Track received quantities
  - Track destroyed quantities
- **Relationships:**
  - `belongsTo` → `purchaseOrder` (PurchaseOrder model)
- **Fields Used:**
  - `expected_qty` / `quantity` - Expected quantity
  - `received_quantity` - Good items received
  - `destroyed_qty` - Damaged items

### 6. `purchase_order_deliveries`
- **Model:** `App\Models\PurchaseOrderDelivery`
- **Usage:**
  - Get latest delivery date for purchase orders
  - Track actual delivery dates
- **Relationships:**
  - `belongsTo` → `purchaseOrder` (PurchaseOrder model)
- **Fields Used:**
  - `delivery_date` - Actual delivery date

## Indirectly Used Tables (via Relationships)

### 7. `activity_log`
- **Package:** Spatie Activity Log
- **Usage:**
  - Automatic logging of supplier CRUD operations
  - Track changes to supplier records
- **Triggered by:**
  - Supplier model uses `LogsActivity` trait
  - Logs on create, update, delete

### 8. `users`
- **Model:** `App\Models\User`
- **Usage:**
  - Permission checks (`supplier view`, `supplier create`, `supplier edit`, `supplier delete`)
  - Activity log causer tracking
- **Methods:**
  - `auth()->user()->hasAnyPermission()`

## Summary

**Total Tables Used: 8**

1. ✅ `suppliers` - Primary table
2. ✅ `categories` - Filtering and display
3. ✅ `products` - Product count and filtering
4. ✅ `purchase_orders` - Order metrics and display
5. ✅ `product_orders` - Delivery tracking
6. ✅ `purchase_order_deliveries` - Delivery dates
7. ✅ `activity_log` - Audit trail
8. ✅ `users` - Permissions and activity tracking

## Notes

- **Soft Deletes:** `suppliers` table uses soft deletes
- **JSON Field:** `suppliers.categories` stores array of category IDs
- **Activity Logging:** All supplier operations are logged automatically
- **Relationships:** Complex filtering combines direct supplier categories with product categories

