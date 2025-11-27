# Allocation Sales Module - Database Tables Analysis

**Route:** `/allocation/sales`  
**Component:** `App\Livewire\Pages\Allocation\Sales`  
**View:** `resources/views/livewire/pages/allocation/sales.blade.php`

## Directly Used Tables

### 1. `batch_allocations`
- **Model:** `App\Models\BatchAllocation`
- **Usage:**
  - Load dispatched batches for selection
  - Filter batches by date
  - Display batch information
  - Link to sales receipts
- **Relationships:**
  - `hasMany` → `branchAllocations` (BranchAllocation model)
  - `hasMany` → `salesReceipts` (SalesReceipt model, indirect)
- **Fields Used:**
  - `id` - Batch allocation ID (selection, filtering)
  - `ref_no` - Reference number (display, export filenames)
  - `transaction_date` - Transaction date (filtering by date)
  - `status` - Status (filtered to 'dispatched' only)
  - `created_at` - Creation timestamp (sorting)
- **Methods:**
  - `BatchAllocation::with(['branchAllocations.branch'])->where('status', 'dispatched')` - Load dispatched batches
  - `BatchAllocation::whereDate('transaction_date', $date)` - Filter by date
  - `BatchAllocation::orderBy('created_at', 'desc')` - Order by creation date
- **Features:**
  - Only shows batches with status 'dispatched'
  - Date filtering support
  - Eager loading of branch allocations and branches

### 2. `sales_receipts` (Primary Table)
- **Model:** `App\Models\SalesReceipt`
- **Usage:**
  - Main CRUD operations (read, update)
  - Load receipts for selected batch
  - Receipt confirmation (update status and date)
  - Display receipt details
  - Export PDF/Excel
- **Relationships:**
  - `belongsTo` → `batchAllocation` (BatchAllocation model)
  - `belongsTo` → `branch` (Branch model)
  - `hasMany` → `items` (SalesReceiptItem model)
- **Fields Used:**
  - `id` - Receipt ID (primary key, selection)
  - `batch_allocation_id` - Batch allocation reference (foreign key, filtering)
  - `branch_id` - Branch reference (foreign key, display)
  - `status` - Receipt status (pending, received) (updates, validation)
  - `date_received` - Date received (updates on confirmation)
  - `created_at` - Creation timestamp
  - `updated_at` - Update timestamp
- **Methods:**
  - `SalesReceipt::with(['branch', 'items.product'])->where('batch_allocation_id', $batchId)->get()` - Load receipts for batch
  - `SalesReceipt::with(['branch', 'batchAllocation', 'items.product'])->find($id)` - Load receipt details
  - `$receipt->update(['status' => 'received', 'date_received' => now()])` - Confirm receipt
- **Features:**
  - Status management (pending → received)
  - Date received tracking
  - One receipt per branch per batch
  - Receipt confirmation workflow
  - PDF/Excel export support

### 3. `sales_receipt_items` (Primary Table)
- **Model:** `App\Models\SalesReceiptItem`
- **Usage:**
  - Main CRUD operations (read, update)
  - Track item quantities (allocated, received, damaged, missing, sold)
  - Item status management
  - Item editing and updates
  - Mark items as sold
  - Export data
- **Relationships:**
  - `belongsTo` → `salesReceipt` (SalesReceipt model)
  - `belongsTo` → `product` (Product model)
- **Fields Used:**
  - `id` - Item ID (primary key, editing)
  - `sales_receipt_id` - Sales receipt reference (foreign key, filtering)
  - `product_id` - Product reference (foreign key, display)
  - `allocated_qty` - Allocated quantity (display, calculation)
  - `received_qty` - Received quantity (editing, updates, validation)
  - `damaged_qty` - Damaged quantity (editing, updates)
  - `missing_qty` - Missing quantity (auto-calculated, updates)
  - `sold_qty` - Sold quantity (updates, tracking)
  - `status` - Item status (pending, received, partial_sold, sold) (updates, display)
  - `remarks` - Item remarks (editing, updates)
  - `sold_at` - Sold timestamp (updates)
  - `sold_by` - User who sold (updates)
- **Methods:**
  - `SalesReceiptItem::where('id', $id)->update([...])` - Update item quantities
  - `SalesReceiptItem::with('salesReceipt')->find($id)` - Load item with receipt
  - `$item->update(['sold_qty' => $qty, 'sold_at' => now()])` - Mark as sold
  - `$receipt->items->sum('allocated_qty')` - Calculate totals
  - `$receipt->items->sum('received_qty')` - Calculate totals
  - `$receipt->items->sum('sold_qty')` - Calculate totals
- **Features:**
  - Quantity tracking (allocated, received, damaged, missing, sold)
  - Missing quantity auto-calculation (allocated - received - damaged)
  - Status management (pending → received → partial_sold → sold)
  - Item editing (only before receipt confirmation)
  - Mark as sold functionality (only after receipt confirmation)
  - Validation (total received cannot exceed allocated)

### 4. `branch_allocations`
- **Model:** `App\Models\BranchAllocation`
- **Usage:**
  - Update status to 'received' when receipt is confirmed
  - Link to batch allocation and branch
- **Relationships:**
  - `belongsTo` → `batchAllocation` (BatchAllocation model)
  - `belongsTo` → `branch` (Branch model)
  - `hasMany` → `items` (BranchAllocationItem model)
- **Fields Used:**
  - `id` - Branch allocation ID
  - `batch_allocation_id` - Batch allocation reference (foreign key, filtering)
  - `branch_id` - Branch reference (foreign key, filtering)
  - `status` - Status (updates to 'received' on receipt confirmation)
- **Methods:**
  - `BranchAllocation::where('batch_allocation_id', $batchId)->where('branch_id', $branchId)->first()` - Find branch allocation
  - `$branchAllocation->update(['status' => 'received'])` - Update status
- **Features:**
  - Status updated to 'received' when receipt is confirmed
  - Links batch allocation to branch

### 5. `products`
- **Model:** `App\Models\Product`
- **Usage:**
  - Display product information in receipt items
  - Product name and details
  - Unit price for calculations
  - Export data
- **Relationships:**
  - `hasMany` → `salesReceiptItems` (SalesReceiptItem model, indirect)
- **Fields Used:**
  - `id` - Product ID (foreign key)
  - `name` - Product name (display, export)
  - `price` / `selling_price` - Product price (calculations, export)
  - `sku` - Product SKU (if needed)
- **Methods:**
  - `$item->product->name` - Get product name
  - `$item->product->price ?? 0` - Get product price
- **Features:**
  - Eager loaded with receipt items
  - Used in export calculations

### 6. `branches`
- **Model:** `App\Models\Branch`
- **Usage:**
  - Display branch information
  - Branch name in receipts
  - Export filenames
- **Relationships:**
  - `hasMany` → `branchAllocations` (BranchAllocation model, indirect)
  - `hasMany` → `salesReceipts` (SalesReceipt model, indirect)
- **Fields Used:**
  - `id` - Branch ID (foreign key)
  - `name` - Branch name (display, export filenames)
  - `code` - Branch code (if needed)
- **Methods:**
  - `$receipt->branch->name` - Get branch name
  - Eager loaded with sales receipts
- **Features:**
  - Used in receipt display and export filenames

## Summary

**Total Tables Used: 6**

1. ✅ `batch_allocations` - Dispatched batches selection
2. ✅ `sales_receipts` - Primary table for receipt management
3. ✅ `sales_receipt_items` - Primary table for item quantity tracking
4. ✅ `branch_allocations` - Status updates on receipt confirmation
5. ✅ `products` - Product information display
6. ✅ `branches` - Branch information display

## Notes

- **Workflow:** Receipt confirmation workflow (pending → received)
- **Quantity Tracking:** Tracks allocated, received, damaged, missing, and sold quantities
- **Missing Quantity:** Auto-calculated as (allocated - received - damaged)
- **Status Management:** Item statuses: pending → received → partial_sold → sold
- **Validation:** Total received (received + damaged) cannot exceed allocated quantity
- **Edit Restrictions:** Items can only be edited before receipt confirmation
- **Mark as Sold:** Items can only be marked as sold after receipt confirmation
- **Date Filtering:** Can filter batches by transaction date
- **Export Support:** PDF and Excel export for receipts
- **Transaction Safety:** Uses database transactions for receipt confirmation
- **Status Updates:** Updates both sales_receipt and branch_allocation status on confirmation
- **Calculations:** Automatic calculation of totals (items, quantities, prices)
- **Eager Loading:** Uses eager loading to prevent N+1 queries

