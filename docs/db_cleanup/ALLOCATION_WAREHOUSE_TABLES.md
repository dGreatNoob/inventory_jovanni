# Allocation Warehouse Module - Database Tables Analysis

**Route:** `/allocation/warehouse`  
**Component:** `App\Livewire\Pages\Allocation\Warehouse`  
**View:** `resources/views/livewire/pages/allocation/warehouse.blade.php`

## Directly Used Tables

### 1. `batch_allocations` (Primary Table)
- **Model:** `App\Models\BatchAllocation`
- **Usage:**
  - Main CRUD operations (create, read, update, delete)
  - Batch allocation workflow management
  - Search and filtering (by ref_no, remarks, branch name, date range)
  - Status management (draft, dispatched)
  - Workflow step tracking
- **Relationships:**
  - `hasMany` → `branchAllocations` (BranchAllocation model)
- **Fields Used:**
  - `id` - Batch allocation ID (primary key, relationships)
  - `ref_no` - Reference number (unique, search, display, generation)
  - `batch_number` - Batch number (selection, filtering, validation)
  - `transaction_date` - Transaction date (filtering, display)
  - `remarks` - Remarks (search, display, editing)
  - `status` - Status (draft, dispatched) (filtering, validation, updates)
  - `workflow_step` - Workflow step (1-4) (tracking, persistence)
  - `created_at` - Creation timestamp (sorting)
  - `updated_at` - Update timestamp
- **Methods:**
  - `BatchAllocation::create([...])` - Create new batch
  - `BatchAllocation::with(['branchAllocations.branch', 'branchAllocations.items.product'])` - Eager load relationships
  - `BatchAllocation::orderBy('created_at', 'desc')` - Order by creation date
  - `BatchAllocation::where('ref_no', 'like', ...)` - Search by reference number
  - `$batch->update([...])` - Update batch details
  - `$batch->delete()` - Delete batch (only if draft)
- **Features:**
  - Reference number auto-generation (format: WT-YYYYMMDD-####)
  - Workflow step tracking (1: Create batch, 2: Add branches, 3: Allocate products, 4: Dispatch)
  - Status validation (only draft batches can be edited/dispatched)
  - Date range filtering
  - Search across ref_no, remarks, and branch names

### 2. `branch_allocations`
- **Model:** `App\Models\BranchAllocation`
- **Usage:**
  - Link branches to batch allocations
  - Store branch-specific allocation details
  - Filter branches by batch number
  - Add/remove branches from batches
- **Relationships:**
  - `belongsTo` → `batchAllocation` (BatchAllocation model)
  - `belongsTo` → `branch` (Branch model)
  - `hasMany` → `items` (BranchAllocationItem model)
- **Fields Used:**
  - `id` - Branch allocation ID (primary key, relationships)
  - `batch_allocation_id` - Batch allocation reference (foreign key)
  - `branch_id` - Branch reference (foreign key, filtering)
  - `remarks` - Branch-specific remarks (editing, display)
  - `status` - Status (pending, etc.) (default: 'pending')
  - `created_at` - Creation timestamp
  - `updated_at` - Update timestamp
- **Methods:**
  - `BranchAllocation::create([...])` - Create branch allocation
  - `BranchAllocation::where('batch_allocation_id', $batchId)` - Filter by batch
  - `$branchAllocation->delete()` - Remove branch from batch
- **Features:**
  - Automatically created when batch is created (based on batch_number)
  - Can be added/removed from draft batches
  - Branch-specific remarks support

### 3. `branch_allocation_items`
- **Model:** `App\Models\BranchAllocationItem`
- **Usage:**
  - Store product allocations per branch
  - Track scanned quantities for barcode scanning
  - Product allocation matrix management
  - Dispatch validation
- **Relationships:**
  - `belongsTo` → `branchAllocation` (BranchAllocation model)
  - `belongsTo` → `product` (Product model)
- **Fields Used:**
  - `id` - Item ID (primary key)
  - `branch_allocation_id` - Branch allocation reference (foreign key)
  - `product_id` - Product reference (foreign key, validation)
  - `quantity` - Allocated quantity (editing, validation, display)
  - `scanned_quantity` - Scanned quantity (barcode scanning, validation)
  - `unit_price` - Unit price (calculation, display)
  - `created_at` - Creation timestamp
  - `updated_at` - Update timestamp
- **Methods:**
  - `BranchAllocationItem::create([...])` - Create allocation item
  - `BranchAllocationItem::where('branch_allocation_id', ...)->where('product_id', ...)` - Find existing item
  - `BranchAllocationItem::where('branch_allocation_id', ...)->where('product_id', ...)->delete()` - Remove item
  - `$item->update(['scanned_quantity' => $qty])` - Update scanned quantity
  - `$item->update(['quantity' => $qty])` - Update allocated quantity
- **Features:**
  - Duplicate product prevention per branch
  - Barcode scanning support (increments scanned_quantity)
  - Matrix allocation (allocate multiple products to multiple branches)
  - Scanned quantity tracking for dispatch validation
  - Unit price from product's selling price

### 4. `branches`
- **Model:** `App\Models\Branch`
- **Usage:**
  - Branch selection for allocations
  - Filter branches by batch number
  - Display branch information
  - Search batches by branch name
- **Relationships:**
  - `hasMany` → `branchAllocations` (BranchAllocation model, indirect)
  - `hasMany` → `salesReceipts` (SalesReceipt model, indirect)
- **Fields Used:**
  - `id` - Branch ID (foreign key, selection)
  - `name` - Branch name (display, search)
  - `code` - Branch code (display, VDR export)
  - `address` - Branch address (display)
  - `batch` - Batch number (filtering, selection)
- **Methods:**
  - `Branch::where('batch', $batchNumber)->get()` - Get branches by batch number
  - `Branch::whereNotNull('batch')->distinct()->pluck('batch')` - Get available batch numbers
  - `Branch::whereNotIn('id', $existingIds)->get()` - Get branches not in batch
  - `Branch::orderBy('name')` - Order branches by name
- **Features:**
  - Batch number filtering (branches grouped by batch)
  - Branch code used in VDR export
  - Search batches by branch name

### 5. `products`
- **Model:** `App\Models\Product`
- **Usage:**
  - Product selection for allocation
  - Display product information
  - Barcode scanning (match barcode to product)
  - Unit price retrieval
  - Product allocation matrix
- **Relationships:**
  - `hasMany` → `branchAllocationItems` (BranchAllocationItem model, indirect)
  - `hasMany` → `salesReceiptItems` (SalesReceiptItem model, indirect)
- **Fields Used:**
  - `id` - Product ID (foreign key, selection, validation)
  - `name` - Product name (display, search)
  - `sku` - Product SKU (display, VDR export)
  - `barcode` - Product barcode (barcode scanning, matching)
  - `price` / `selling_price` - Product price (unit price calculation)
- **Methods:**
  - `Product::orderBy('name')->get()` - Get all products ordered by name
  - `Product::find($id)` - Find product by ID
  - `$item->product->barcode` - Get product barcode for scanning
  - `$product->price ?? $product->selling_price ?? 0` - Get unit price
- **Features:**
  - Barcode scanning support (matches scanned barcode to product)
  - Unit price from product's price or selling_price
  - SKU used in VDR export
  - Product allocation matrix (allocate multiple products to multiple branches)

### 6. `sales_receipts`
- **Model:** `App\Models\SalesReceipt`
- **Usage:**
  - Created when batch is dispatched
  - Track receipt status per branch
  - Link to batch allocation
- **Relationships:**
  - `belongsTo` → `batchAllocation` (BatchAllocation model)
  - `belongsTo` → `branch` (Branch model)
  - `hasMany` → `items` (SalesReceiptItem model)
- **Fields Used:**
  - `id` - Sales receipt ID (primary key)
  - `batch_allocation_id` - Batch allocation reference (foreign key)
  - `branch_id` - Branch reference (foreign key)
  - `status` - Receipt status (default: 'pending')
  - `date_received` - Date received (if applicable)
  - `created_by` - User who dispatched (tracking)
  - `dispatched_at` - Dispatch timestamp (tracking)
- **Methods:**
  - `SalesReceipt::create([...])` - Create sales receipt on dispatch
- **Features:**
  - Automatically created when batch is dispatched
  - One receipt per branch per batch
  - Tracks who dispatched and when

### 7. `sales_receipt_items`
- **Model:** `App\Models\SalesReceiptItem`
- **Usage:**
  - Created when batch is dispatched
  - Track item quantities and status per receipt
  - Store allocated, scanned, received, damaged, missing, and sold quantities
- **Relationships:**
  - `belongsTo` → `salesReceipt` (SalesReceipt model)
  - `belongsTo` → `product` (Product model)
- **Fields Used:**
  - `id` - Item ID (primary key)
  - `sales_receipt_id` - Sales receipt reference (foreign key)
  - `product_id` - Product reference (foreign key)
  - `allocated_qty` - Allocated quantity (from branch_allocation_items.quantity)
  - `scanned_qty` - Scanned quantity (from branch_allocation_items.scanned_quantity)
  - `received_qty` - Received quantity (default: 0)
  - `damaged_qty` - Damaged quantity (default: 0)
  - `missing_qty` - Missing quantity (default: 0)
  - `sold_qty` - Sold quantity (default: 0)
  - `status` - Item status (default: 'pending')
  - `remarks` - Item remarks
  - `sold_at` - Sold timestamp
  - `sold_by` - User who sold
- **Methods:**
  - `SalesReceiptItem::create([...])` - Create receipt item on dispatch
- **Features:**
  - Automatically created when batch is dispatched
  - Copies allocated and scanned quantities from branch_allocation_items
  - Tracks various quantity types for inventory reconciliation

## Indirectly Used Tables (via Relationships)

### 8. `activity_log` (Spatie Activity Log)
- **Table:** `activity_log`
- **Usage:**
  - Log batch dispatch activities
  - Track who dispatched and when
  - Store dispatch metadata
- **Methods:**
  - `activity()->performedOn($batch)->causedBy($user)->log('Batch dispatched')` - Log activity
- **Fields Used:**
  - `subject_type` - Model type (BatchAllocation)
  - `subject_id` - Batch allocation ID
  - `causer_type` - User model type
  - `causer_id` - User ID
  - `description` - Activity description
  - `properties` - JSON metadata (batch_ref, batch_number, branches_count, total_items)
- **Note:** Used via Spatie Activity Log package for audit trail

### 9. `users`
- **Model:** `App\Models\User`
- **Usage:**
  - Track who dispatched batches (created_by, sold_by)
  - Activity log causer
- **Methods:**
  - `auth()->id()` - Get current user ID
  - `auth()->user()` - Get current user
- **Fields Used:**
  - `id` - User ID (foreign key for created_by, sold_by, causer_id)

## Summary

**Total Tables Used: 9**

1. ✅ `batch_allocations` - Primary table for batch management
2. ✅ `branch_allocations` - Branch-to-batch relationships
3. ✅ `branch_allocation_items` - Product allocations per branch
4. ✅ `branches` - Branch information and filtering
5. ✅ `products` - Product information and allocation
6. ✅ `sales_receipts` - Receipts created on dispatch
7. ✅ `sales_receipt_items` - Receipt items with quantity tracking
8. ✅ `activity_log` - Activity logging (Spatie Activity Log)
9. ✅ `users` - User tracking for dispatch and activities

## Notes

- **Workflow Steps:** 4-step workflow (1: Create batch, 2: Add branches, 3: Allocate products, 4: Dispatch)
- **Barcode Scanning:** Supports barcode scanning to track scanned quantities per product per branch
- **Matrix Allocation:** Can allocate multiple products to multiple branches in a matrix view
- **Dispatch Validation:** Requires all products to be fully scanned before dispatch
- **Status Management:** Only draft batches can be edited; dispatched batches are read-only
- **Reference Number:** Auto-generated format: WT-YYYYMMDD-####
- **Batch Number:** Links to branches via `branches.batch` field
- **VDR Export:** Can export Vendor Delivery Receipt (VDR) as CSV/Excel
- **Sales Receipts:** Automatically created on dispatch, one per branch
- **Quantity Tracking:** Tracks allocated, scanned, received, damaged, missing, and sold quantities
- **Activity Logging:** Uses Spatie Activity Log for audit trail
- **Transaction Safety:** Uses database transactions for dispatch operations
- **Eager Loading:** Uses eager loading to prevent N+1 queries

