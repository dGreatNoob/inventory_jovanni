# Sales Promo Module - Database Tables Analysis

**Route:** `/sales-promo`  
**Component:** `App\Livewire\Pages\SalesManagement\SalesPromo`  
**View:** `resources/views/livewire/pages/sales-management/promo.blade.php`

## Directly Used Tables

### 1. `promos` (Primary Table)
- **Model:** `App\Models\Promo`
- **Usage:**
  - Main CRUD operations (create, read, update, delete)
  - Search and filtering (by name, code, type, status, date range)
  - Promo overlap validation
  - Statistics (total, active, upcoming promos)
- **Relationships:**
  - None (uses JSON columns for relationships)
- **Fields Used:**
  - `name` - Promo name (search, display)
  - `code` - Promo code (search, unique validation)
  - `description` - Promo description
  - `startDate` - Start date (filtering, overlap validation, status calculation)
  - `endDate` - End date (filtering, overlap validation, status calculation)
  - `branch` - JSON array of batch allocation IDs (stored as JSON, used for overlap validation)
  - `product` - JSON array of product IDs (stored as JSON, used for overlap validation)
  - `second_product` - JSON array of second product IDs (stored as JSON, used for overlap validation)
  - `type` - Promo type (filtering, validation)
- **Features:**
  - Overlap validation (prevents overlapping promos for same batch/product combinations)
  - Status calculation (active, upcoming, expired)
  - Date range filtering
  - Type filtering
  - JSON storage for batch allocations and products

### 2. `batch_allocations`
- **Model:** `App\Models\BatchAllocation`
- **Usage:**
  - Load batch allocations for selection in create/edit forms
  - Get batch allocation ref_nos for display
  - Filter products available for selected batch allocations
  - Overlap validation (check if batch allocations are already in promos)
- **Relationships:**
  - `hasMany` → `branchAllocations` (BranchAllocation model)
- **Fields Used:**
  - `id` - Batch allocation ID (selection, filtering)
  - `ref_no` - Reference number (display, conflict messages)
  - `transaction_date` - Transaction date
  - `batch_number` - Batch number
  - `status` - Status
  - `workflow_step` - Workflow step
- **Methods:**
  - `BatchAllocation::with(['branchAllocations.items.product'])` - Eager load related data
  - `BatchAllocation::orderBy('ref_no', 'desc')` - Order by reference number

### 3. `branch_allocations`
- **Model:** `App\Models\BranchAllocation`
- **Usage:**
  - Intermediate table between batch_allocations and branch_allocation_items
  - Used to get products for selected batch allocations
- **Relationships:**
  - `belongsTo` → `batchAllocation` (BatchAllocation model)
  - `hasMany` → `items` (BranchAllocationItem model)
- **Fields Used:**
  - `batch_allocation_id` - Batch allocation reference
  - `branch_id` - Branch reference (if applicable)

### 4. `branch_allocation_items`
- **Model:** `App\Models\BranchAllocationItem`
- **Usage:**
  - Get product IDs from branch allocation items in selected batch allocations
  - Filter available products for promo creation
  - Determine which products are available for selected batch allocations
- **Relationships:**
  - `belongsTo` → `branchAllocation` (BranchAllocation model)
  - `belongsTo` → `product` (Product model)
- **Fields Used:**
  - `branch_allocation_id` - Branch allocation reference
  - `product_id` - Product reference (used to filter available products)
  - `quantity` - Quantity allocated
  - `scanned_quantity` - Scanned quantity
  - `unit_price` - Unit price
- **Methods:**
  - `BranchAllocationItem::whereHas('branchAllocation', ...)` - Filter by batch allocation
  - `BranchAllocationItem::pluck('product_id')` - Get product IDs

### 5. `products`
- **Model:** `App\Models\Product`
- **Usage:**
  - Product selection for promos
  - Display product names
  - Filter products available for selected batch allocations
  - Overlap validation (check if products are already in promos)
  - Product name lookup for conflict messages
- **Relationships:**
  - `belongsTo` → `category` (Category model, if needed)
- **Fields Used:**
  - `id` - Product ID (selection, filtering, validation)
  - `name` - Product name (display, search, conflict messages)
  - `sku` - Product SKU (if needed)
- **Methods:**
  - `Product::orderBy('name')` - Order products by name
  - `Product::whereIn('id', $productIds)` - Filter by product IDs
  - `$products->firstWhere('id', $productId)` - Find product by ID

### 6. `branches`
- **Model:** `App\Models\Branch`
- **Usage:**
  - Load branches for selection (legacy support)
  - Display branch names (fallback for old data format)
  - Branch name lookup for batch allocation display
- **Relationships:**
  - None directly used in this module
- **Fields Used:**
  - `id` - Branch ID (legacy format support)
  - `name` - Branch name (display, fallback)
- **Methods:**
  - `Branch::orderBy('name')` - Order branches by name
  - `Branch::whereIn('id', $ids)->pluck('name')` - Get branch names (fallback)

## Summary

**Total Tables Used: 6**

1. ✅ `promos` - Primary table
2. ✅ `batch_allocations` - Batch allocation selection and filtering
3. ✅ `branch_allocations` - Intermediate table for batch allocation items
4. ✅ `branch_allocation_items` - Product filtering for batch allocations
5. ✅ `products` - Product selection and validation
6. ✅ `branches` - Legacy support and fallback display

## Notes

- **JSON Storage:** Promos store batch allocation IDs and product IDs as JSON arrays in `branch` and `product` columns
- **Overlap Validation:** Prevents creating overlapping promos for the same batch/product combinations during the same date range
- **Batch Allocation Focus:** The module primarily works with batch allocations (not direct branches), though it maintains backward compatibility with branch IDs
- **Product Filtering:** Products are filtered based on selected batch allocations - only products in those batch allocations are available for selection
- **Status Calculation:** Promos are categorized as active (current date between start and end), upcoming (start date in future), or expired (end date in past)
- **Type Options:** Supports promo types: "Buy one Take one", "70% Discount", "60% Discount", "50% Discount"
- **Date Range Filtering:** Can filter promos by start date and end date ranges
- **Conflict Detection:** When creating/editing promos, the system checks for date overlaps and batch/product conflicts, showing detailed error messages
- **Product Disabling:** Products that would cause conflicts are automatically disabled in the selection dropdown

