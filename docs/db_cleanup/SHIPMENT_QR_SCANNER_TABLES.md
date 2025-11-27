# Shipment QR Scanner Module - Database Tables Analysis

**Route:** `/Shipment/scan`  
**Component:** `App\Livewire\Pages\Shipment\QrScannder`  
**View:** `resources/views/livewire/pages/shipment/scan-item.blade.php`

## Directly Used Tables

### 1. `shipments` (Primary Table)
- **Model:** `App\Models\Shipment`
- **Usage:**
  - Find shipments by shipping plan number (QR code scanning)
  - Update shipment status (in_transit, completed, damaged, incomplete)
  - Update review remarks
  - Validate shipment status (only approved or in_transit can be processed)
  - Display shipment information
- **Relationships:**
  - `belongsTo(BranchAllocation::class)` - Links shipment to branch allocation
- **Fields Used:**
  - `id` - Shipment ID (primary key)
  - `shipping_plan_num` - Shipping plan number (unique, QR code search, display)
  - `shipping_status` - Shipping status (validation, update)
  - `review_remarks` - Review remarks (update)
  - `branch_allocation_id` - Foreign key to branch_allocations table (relationship)
- **Methods:**
  - `Shipment::with('branchAllocation.items.product')->where('shipping_plan_num', $code)->first()` - Find shipment by shipping plan number with relationships
  - `Shipment::findOrFail($id)->update([...])` - Update shipment status and remarks
- **Features:**
  - QR code scanning to find shipments by shipping plan number
  - Manual input of shipment reference number
  - Status validation (only approved or in_transit shipments can be processed)
  - Status updates based on item receiving status (completed, damaged, incomplete)
  - Review remarks stored on submission

### 2. `branch_allocations` (Related Table)
- **Model:** `App\Models\BranchAllocation`
- **Usage:**
  - Access branch allocation items for review
  - Get branch ID for inventory updates
  - Validate that shipment has associated branch allocation with items
- **Relationships:**
  - `hasMany(BranchAllocationItem::class)` - Links branch allocation to items
  - `belongsTo(Branch::class)` - Links branch allocation to branch
- **Fields Used:**
  - `id` - Branch allocation ID (relationship)
  - `branch_id` - Foreign key to branches table (used for inventory updates)
- **Methods:**
  - Accessed via `shipment.branchAllocation` relationship
  - `branchAllocation->items` - Access branch allocation items
  - `branchAllocation->branch_id` - Get branch ID for inventory updates
- **Features:**
  - Branch allocation items are reviewed during shipment scanning
  - Branch ID used to update branch inventory

### 3. `branch_allocation_items` (Related Table)
- **Model:** `App\Models\BranchAllocationItem`
- **Usage:**
  - Display items for review
  - Update receiving status and remarks for each item
  - Process items based on receiving status (good, destroyed, incomplete)
- **Relationships:**
  - `belongsTo(BranchAllocation::class)` - Links item to branch allocation
  - `belongsTo(Product::class)` - Links item to product
- **Fields Used:**
  - `id` - Branch allocation item ID (primary key, used for status/remarks arrays)
  - `branch_allocation_id` - Foreign key to branch_allocations table (relationship)
  - `product_id` - Foreign key to products table (relationship)
  - `quantity` - Quantity (display, used for inventory updates)
  - `receiving_status` - Receiving status (update: good, destroyed, incomplete)
  - `receiving_remarks` - Receiving remarks (update)
- **Methods:**
  - `BranchAllocationItem::find($item->id)->update([...])` - Update receiving status and remarks
- **Features:**
  - Each item can have individual receiving status and remarks
  - Status options: good, destroyed, incomplete
  - Only items with 'good' status update inventory
  - All items must have status set before submission

### 4. `products` (Related Table)
- **Model:** `App\Models\Product`
- **Usage:**
  - Display product information in branch allocation items
  - Access product inventory records
  - Used in activity logging
- **Relationships:**
  - `hasMany(ProductInventory::class)` - Links product to inventory records
- **Fields Used:**
  - `id` - Product ID (foreign key in branch_allocation_items table, relationship)
  - Product fields accessed via `branchAllocation.items.product` relationship
- **Methods:**
  - `$product->inventory()->orderBy('created_at', 'asc')->first()` - Get oldest inventory record
  - `$product->inventory()->orderBy('created_at', 'asc')->first()->quantity` - Get initial quantity
  - `$product->inventory()->orderBy('created_at', 'asc')->first()->save()` - Update inventory quantity
- **Features:**
  - Product information displayed in review step
  - Inventory records accessed for quantity deduction
  - Oldest inventory record is used for FIFO (First In First Out) inventory management

### 5. `product_inventory` (Related Table)
- **Model:** `App\Models\ProductInventory`
- **Usage:**
  - Deduct quantity from initial inventory when items are received as 'good'
  - Track inventory quantities for products
  - FIFO inventory management (oldest inventory record is used first)
- **Relationships:**
  - `belongsTo(Product::class)` - Links inventory record to product
- **Fields Used:**
  - `id` - Product inventory ID (primary key)
  - `product_id` - Foreign key to products table (relationship)
  - `quantity` - Quantity (update: decremented when items are received)
  - `created_at` - Creation timestamp (used for ordering: oldest first)
- **Methods:**
  - `ProductInventory::where('product_id', $productId)->orderBy('created_at', 'asc')->first()` - Get oldest inventory record
  - `$inventory->quantity -= $qtyChange; $inventory->save()` - Deduct quantity
- **Features:**
  - FIFO inventory management (oldest inventory record is used first)
  - Quantity is only deducted if inventory record exists and has sufficient quantity
  - Inventory quantity is decremented when items are received as 'good'

### 6. `branch_product` (Pivot Table)
- **Model:** Direct DB table access (no model)
- **Usage:**
  - Update branch inventory stock when items are received as 'good'
  - Track product stock per branch
- **Fields Used:**
  - `branch_id` - Foreign key to branches table (primary key part)
  - `product_id` - Foreign key to products table (primary key part)
  - `stock` - Stock quantity (update: incremented when items are received)
- **Methods:**
  - `DB::table('branch_product')->where([['branch_id', '=', $branchId], ['product_id', '=', $productId]])->first()` - Get existing pivot record
  - `DB::table('branch_product')->updateOrInsert([...], ['stock' => $newStock])` - Update or insert branch product stock
- **Features:**
  - Branch inventory is updated when items are received as 'good'
  - Stock is incremented by the received quantity
  - Pivot table is created if it doesn't exist (updateOrInsert)
  - Stock calculation: newStock = (existing stock or 0) + received quantity

### 7. `activity_log` (Related Table - Spatie Activity Log)
- **Model:** Spatie Activity Log (via `activity()` helper)
- **Usage:**
  - Log shipment review activities
  - Track inventory changes for supply profiles
  - Store user who performed the action
- **Fields Used:**
  - Activity log fields (subject_id, subject_type, description, properties, causer_id, causer_type, etc.)
- **Methods:**
  - `activity('Shipment Review')->causedBy(Auth::user())->performedOn($supplyProfile)->withProperties([...])->log(...)` - Log shipment review activity
- **Features:**
  - Activity logging for shipment reviews
  - Logs include SKU, shipping plan number, and allocated quantity
  - User who performed the action is recorded
  - Supply profile changes are logged

### 8. `users` (Related Table)
- **Model:** `App\Models\User`
- **Usage:**
  - Get current user for activity logging
  - Track who performed the shipment review
- **Fields Used:**
  - `id` - User ID (used via Auth::user())
- **Methods:**
  - `Auth::user()` - Get current authenticated user
  - `Auth::id()` - Get current user ID
- **Features:**
  - Current user is recorded in activity logs
  - User information used for tracking shipment reviews

## Workflow Steps

### Step 0: Scan QR Code
- Scan QR code or manually enter shipment reference number
- Find shipment by shipping plan number
- Validate shipment status (must be 'approved' or 'in_transit')
- Validate that shipment has associated branch allocation with items
- Advance to Step 1 (Review)

### Step 1: Review Items
- Display all branch allocation items
- Set receiving status for each item (good, destroyed, incomplete)
- Set receiving remarks for each item (optional)
- Set general remarks (optional)
- Advance to Step 2 (Submit)

### Step 2: Submit Report
- Validate that all items have statuses set
- Process each item based on receiving status:
  - **Good items:**
    - Update branch allocation item receiving_status and receiving_remarks
    - Update branch_product stock (increment by quantity)
    - Deduct from product_inventory (oldest record, FIFO)
    - Log activity for supply profile
  - **Destroyed/Incomplete items:**
    - Update branch allocation item receiving_status and receiving_remarks
    - No inventory updates
- Update shipment status based on overall item statuses:
  - All good → 'completed'
  - Has destroyed → 'damaged'
  - Has incomplete → 'incomplete'
- Update shipment review_remarks
- Advance to Step 3 (Finish)

### Step 3: Finish
- Display completion message
- Show shipment reference number
- Option to scan another shipment

## Summary

**Total Tables Used: 8**

1. ✅ `shipments` - Primary table for shipment management
2. ✅ `branch_allocations` - Related table for branch allocations
3. ✅ `branch_allocation_items` - Related table for branch allocation items
4. ✅ `products` - Related table for products
5. ✅ `product_inventory` - Related table for product inventory (FIFO management)
6. ✅ `branch_product` - Pivot table for branch inventory stock
7. ✅ `activity_log` - Related table for activity logging (Spatie Activity Log)
8. ✅ `users` - Related table for users (activity logging)

## Notes

- **QR Code Scanning:** Scans shipping plan number from QR code or manual input
- **Status Validation:** Only shipments with status 'approved' or 'in_transit' can be processed
- **Item Review:** Each branch allocation item can have individual receiving status and remarks
- **Receiving Statuses:** good, destroyed, incomplete
- **Inventory Updates:** Only items with 'good' status update inventory
- **Branch Inventory:** Branch product stock is incremented when items are received as 'good'
- **FIFO Inventory Management:** Oldest product inventory record is used first (First In First Out)
- **Inventory Deduction:** Quantity is deducted from oldest product_inventory record
- **Shipment Status:** Shipment status is updated based on overall item statuses (completed, damaged, incomplete)
- **Activity Logging:** All shipment reviews are logged with user, SKU, shipping plan number, and allocated quantity
- **Multi-Step Workflow:** 4-step workflow (Scan QR, Review, Submit, Finish)
- **Validation:** All items must have statuses set before submission
- **Error Handling:** Comprehensive error messages for invalid shipments, missing items, and missing statuses
- **Manual Input:** Supports manual entry of shipment reference number
- **State Management:** Maintains scanned shipment number and item statuses/remarks throughout workflow
- **Database Transactions:** Not explicitly used, but operations are atomic
- **Eager Loading:** Relationships are eager loaded for performance (branchAllocation.items.product)

