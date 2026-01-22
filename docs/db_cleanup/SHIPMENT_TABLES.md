# Shipment Module - Database Tables Analysis

**Route:** `/Shipment`  
**Components:** 
- `App\Livewire\Pages\Shipment\Index` (main listing and creation)
- `App\Livewire\Pages\Shipment\View` (view and approval)
- `App\Livewire\Pages\Shipment\QrScannder` (QR code scanning)
**Views:** 
- `resources/views/livewire/pages/shipment/index.blade.php`
- `resources/views/livewire/pages/shipment/view.blade.php`
- `resources/views/livewire/pages/qrcode/shipmentprint.blade.php`

## Directly Used Tables

### 1. `shipments` (Primary Table)
- **Model:** `App\Models\Shipment`
- **Usage:**
  - Main CRUD operations (create, read, update, delete)
  - Store shipment records linked to sales orders, batch allocations, and branch allocations
  - Search and filtering (by shipping_plan_num, customer_name, customer_address, delivery_method, vehicle_plate_number, shipping_priority, carrier_name)
  - Status filtering (pending, approved, shipped, delivered, cancelled)
  - QR code generation and scanning
  - Status management (mark as shipped, mark as delivered, approve, cancel)
- **Relationships:**
  - `belongsTo(Customer::class)` - Links shipment to customer
  - `belongsTo(User::class, 'approver_id')` - Links shipment to approver user
  - `belongsTo(SalesOrder::class)` - Links shipment to sales order (optional)
  - `belongsTo(BatchAllocation::class)` - Links shipment to batch allocation
  - `belongsTo(BranchAllocation::class)` - Links shipment to branch allocation
  - `hasMany(ShipmentStatusLog::class)` - Links shipment to status logs
- **Fields Used:**
  - `id` - Shipment ID (primary key, editing, deletion)
  - `shipping_plan_num` - Shipping plan number (unique, auto-generated, search, display, QR code)
  - `sales_order_id` - Foreign key to sales_orders table (nullable, relationship)
  - `batch_allocation_id` - Foreign key to batch_allocations table (relationship)
  - `branch_allocation_id` - Foreign key to branch_allocations table (relationship)
  - `customer_id` - Foreign key to customers table (nullable, relationship)
  - `customer_name` - Customer name (required, search, display)
  - `customer_address` - Customer address (required, search, display)
  - `customer_email` - Customer email (nullable, display)
  - `customer_phone` - Customer phone (required, display)
  - `delivery_method` - Delivery method (required, search, display, validation)
  - `carrier_name` - Carrier name (nullable, search, display)
  - `vehicle_plate_number` - Vehicle plate number (nullable, search, display)
  - `shipping_priority` - Shipping priority (nullable, search, display)
  - `special_handling_notes` - Special handling notes (nullable, display)
  - `shipping_status` - Shipping status (required, filtering, display, validation)
  - `scheduled_ship_date` - Scheduled ship date (required, display, validation)
  - `shipped_at` - Shipped timestamp (nullable, updated when marked as shipped)
  - `delivered_at` - Delivered timestamp (nullable, updated when marked as delivered)
  - `cancelled_reason` - Cancellation reason (nullable, display)
  - `cancelled_at` - Cancelled timestamp (nullable, display)
  - `approver_id` - Foreign key to users table (nullable, relationship)
  - `review_remarks` - Review remarks (nullable, display)
  - `status_history` - Status history JSON (nullable, display)
  - `created_at` - Creation timestamp
  - `updated_at` - Update timestamp
- **Methods:**
  - `Shipment::count()` - Count shipments for reference number generation
  - `Shipment::where('shipping_plan_num', $shipping_plan_num)->first()` - Find by shipping plan number
  - `Shipment::with('customer')->find($id)` - Find with customer relationship
  - `Shipment::with('salesOrder')->find($id)` - Find with sales order relationship
  - `Shipment::with('branchAllocation.items.product')->first()` - Find with branch allocation items and products
  - `Shipment::where('status', 'released')->with('customer')->get()` - Find released sales orders with customers
  - `Shipment::search($search)` - Search by multiple fields
  - `Shipment::filterStatus($status)` - Filter by shipping status
  - `Shipment::latest()->paginate($perPage)` - Paginate results
  - `Shipment::create([...])` - Create shipment
  - `Shipment::findOrFail($id)->update([...])` - Update shipment
  - `Shipment::deliveryMethodDropDown()` - Get delivery method options
- **Features:**
  - Shipping plan number auto-generation (format: SHIP-yyyymmdd###, e.g., SHIP-20250721-001)
  - Linked to sales orders (optional)
  - Linked to batch allocations and branch allocations
  - Multiple shipments can be created for multiple branch allocations
  - Status management (pending, approved, shipped, delivered, cancelled)
  - QR code generation for shipping plan number
  - Comprehensive filtering (status)
  - Search across multiple fields
  - Edit only allowed for pending shipments
  - Approval workflow (approve/reject)
  - Activity logging (via LogsActivity trait)

### 2. `sales_orders` (Related Table)
- **Model:** `App\Models\SalesOrder`
- **Usage:**
  - Load released sales orders for shipment creation
  - Display sales order information in shipment records
  - Link shipments to sales orders (optional)
- **Relationships:**
  - `belongsTo(Customer::class)` - Links sales order to customer
  - `hasMany(SalesOrderItem::class)` - Links sales order to items
- **Fields Used:**
  - `id` - Sales order ID (foreign key in shipments table, selection)
  - `status` - Sales order status (filtered to 'released')
  - `customer_ids` - Customer IDs (array, relationship)
- **Methods:**
  - `SalesOrder::where('status', 'released')->with('customer')->get()` - Load released sales orders with customers
- **Features:**
  - Only released sales orders are shown for shipment creation
  - Sales order information displayed in shipment records

### 3. `batch_allocations` (Related Table)
- **Model:** `App\Models\BatchAllocation`
- **Usage:**
  - Load dispatched batch allocations for shipment creation
  - Link shipments to batch allocations
  - Display batch allocation information in shipment records
- **Relationships:**
  - `hasMany(BranchAllocation::class)` - Links batch allocation to branch allocations
- **Fields Used:**
  - `id` - Batch allocation ID (foreign key in shipments table, selection)
  - `status` - Batch allocation status (filtered to 'dispatched')
  - `ref_no` - Reference number (display)
  - `batch_number` - Batch number (display)
  - `transaction_date` - Transaction date (display)
  - `remarks` - Remarks (display)
- **Methods:**
  - `BatchAllocation::with(['branchAllocations.branch'])->where('status', 'dispatched')->orderBy('created_at', 'desc')->get()` - Load dispatched batch allocations with branch allocations and branches
- **Features:**
  - Only dispatched batch allocations are shown for shipment creation
  - Batch allocation information displayed in shipment records

### 4. `branch_allocations` (Related Table)
- **Model:** `App\Models\BranchAllocation`
- **Usage:**
  - Load branch allocations for selected batch allocation
  - Link shipments to branch allocations
  - Display branch allocation information in shipment records
  - Multiple branch allocations can be selected for multiple shipments
- **Relationships:**
  - `belongsTo(BatchAllocation::class)` - Links branch allocation to batch allocation
  - `belongsTo(Branch::class)` - Links branch allocation to branch
  - `hasMany(BranchAllocationItem::class)` - Links branch allocation to items
- **Fields Used:**
  - `id` - Branch allocation ID (foreign key in shipments table, selection, validation)
  - `batch_allocation_id` - Foreign key to batch_allocations table (relationship)
  - `branch_id` - Foreign key to branches table (relationship)
  - `reference_number` - Reference number (display, used for shipping plan number)
  - `remarks` - Remarks (display)
  - `status` - Branch allocation status (display)
- **Methods:**
  - `BranchAllocation::with('branch')->where('batch_allocation_id', $batchAllocationId)->get()` - Load branch allocations for batch allocation with branches
  - `BranchAllocation::find($branchAllocId)` - Find branch allocation
- **Features:**
  - Branch allocations are loaded based on selected batch allocation
  - Multiple branch allocations can be selected for multiple shipments
  - Branch reference number can be used as shipping plan number
  - Branch allocation items are displayed in shipment QR code

### 5. `branch_allocation_items` (Related Table)
- **Model:** `App\Models\BranchAllocationItem`
- **Usage:**
  - Display branch allocation items in shipment QR code
  - Show product information for each item
- **Relationships:**
  - `belongsTo(BranchAllocation::class)` - Links item to branch allocation
  - `belongsTo(Product::class)` - Links item to product
- **Fields Used:**
  - `id` - Branch allocation item ID (display)
  - `branch_allocation_id` - Foreign key to branch_allocations table (relationship)
  - `product_id` - Foreign key to products table (relationship)
  - `quantity` - Quantity (display)
  - `scanned_quantity` - Scanned quantity (display)
  - `unit_price` - Unit price (display)
- **Methods:**
  - Accessed via `branchAllocation.items` relationship
- **Features:**
  - Branch allocation items displayed in shipment QR code
  - Product information shown for each item

### 6. `customers` (Related Table)
- **Model:** `App\Models\Customer`
- **Usage:**
  - Load customers for selection
  - Display customer information in shipment records
  - Link shipments to customers
- **Relationships:**
  - `hasMany(SalesOrder::class)` - Links customer to sales orders
  - `hasMany(Shipment::class)` - Links customer to shipments
- **Fields Used:**
  - `id` - Customer ID (foreign key in shipments table, selection)
  - `name` - Customer name (display, used in activity log)
  - `address` - Customer address (display)
  - `contact_num` - Contact number (display)
  - `tin_num` - TIN number (display)
- **Methods:**
  - `Customer::all()->pluck('name', 'id')` - Load all customers for dropdown
  - `Customer::find($customer_id)` - Find customer (used in activity log)
- **Features:**
  - Customer information displayed in shipment records
  - Customer name used in activity log

### 7. `users` (Related Table)
- **Model:** `App\Models\User`
- **Usage:**
  - Store approver information for shipments
  - Display approver information in shipment records
  - Used in activity logging
- **Relationships:**
  - `hasMany(Shipment::class, 'approver_id')` - Links user to approved shipments
- **Fields Used:**
  - `id` - User ID (foreign key in shipments table as approver_id, selection)
  - `name` - User name (display, used in activity log)
- **Methods:**
  - `Auth::id()` - Get current user ID for approver
  - `User::find($approver_id)` - Find user (used in activity log)
- **Features:**
  - Approver information stored when shipment is approved/cancelled
  - Approver name used in activity log

### 8. `products` (Related Table)
- **Model:** `App\Models\Product`
- **Usage:**
  - Display product information in branch allocation items
  - Show product details in shipment QR code
- **Relationships:**
  - `belongsToMany(BranchAllocationItem::class)` - Links product to branch allocation items
- **Fields Used:**
  - `id` - Product ID (foreign key in branch_allocation_items table, relationship)
  - Product fields accessed via `branchAllocation.items.product` relationship
- **Methods:**
  - Accessed via `branchAllocation.items.product` relationship
- **Features:**
  - Product information displayed in shipment QR code
  - Product details shown for each branch allocation item

### 9. `branches` (Related Table)
- **Model:** `App\Models\Branch`
- **Usage:**
  - Display branch information in branch allocations
  - Show branch details in shipment records
- **Relationships:**
  - `hasMany(BranchAllocation::class)` - Links branch to branch allocations
- **Fields Used:**
  - `id` - Branch ID (foreign key in branch_allocations table, relationship)
  - Branch fields accessed via `branchAllocation.branch` relationship
- **Methods:**
  - Accessed via `branchAllocation.branch` relationship
- **Features:**
  - Branch information displayed in shipment records
  - Branch details shown for each branch allocation

### 10. `shipment_status_logs` (Related Table)
- **Model:** `App\Models\ShipmentStatusLog`
- **Usage:**
  - Track shipment status changes
  - Store status history for shipments
- **Relationships:**
  - `belongsTo(Shipment::class)` - Links status log to shipment
  - `belongsTo(User::class, 'changed_by')` - Links status log to user who changed it
- **Fields Used:**
  - `id` - Status log ID (primary key)
  - `shipment_id` - Foreign key to shipments table (relationship)
  - `status` - Status value (display)
  - `changed_at` - Changed timestamp (display)
  - `changed_by` - Foreign key to users table (relationship)
- **Methods:**
  - Accessed via `shipment.statusLogs` relationship
- **Features:**
  - Status history tracked for shipments
  - User who changed status is recorded

### 11. `activity_log` (Related Table - Spatie Activity Log)
- **Model:** Spatie Activity Log (via `LogsActivity` trait)
- **Usage:**
  - Track all shipment changes
  - Store activity history for shipments
- **Fields Used:**
  - Activity log fields (subject_id, subject_type, description, properties, causer_id, causer_type, etc.)
- **Methods:**
  - Accessed via `LogsActivity` trait on Shipment model
- **Features:**
  - All shipment changes are logged
  - Activity history includes customer name, approver name, and all shipment fields
  - Activity log description includes formatted field changes

## Summary

**Total Tables Used: 11**

1. ✅ `shipments` - Primary table for shipment management
2. ✅ `sales_orders` - Related table for sales orders (optional)
3. ✅ `batch_allocations` - Related table for batch allocations
4. ✅ `branch_allocations` - Related table for branch allocations
5. ✅ `branch_allocation_items` - Related table for branch allocation items
6. ✅ `customers` - Related table for customers
7. ✅ `users` - Related table for approvers
8. ✅ `products` - Related table for products (via branch allocation items)
9. ✅ `branches` - Related table for branches (via branch allocations)
10. ✅ `shipment_status_logs` - Related table for status history
11. ✅ `activity_log` - Related table for activity logging (Spatie Activity Log)

## Notes

- **Shipping Plan Number Generation:** Auto-generates shipping plan number in format SHIP-yyyymmdd### (e.g., SHIP-20250721-001)
- **Multiple Shipments:** Can create multiple shipments for multiple branch allocations in one operation
- **Status Management:** Statuses include pending, approved, shipped, delivered, cancelled
- **Edit Restrictions:** Only pending shipments can be edited
- **Approval Workflow:** Shipments can be approved or cancelled by authorized users
- **QR Code Generation:** QR codes generated for shipping plan numbers
- **Comprehensive Filtering:** Filter by shipping status
- **Search Functionality:** Searches across shipping_plan_num, customer_name, customer_address, delivery_method, vehicle_plate_number, shipping_priority, carrier_name
- **Delivery Methods:** courier, pickup, truck, motorbike, in-house, cargo
- **Shipping Priorities:** same-day, next-day, normal, scheduled, backorder, rush, express
- **Date Validation:** Scheduled ship date cannot be in the past
- **Branch Reference Numbers:** Branch reference numbers can be used as shipping plan numbers
- **Activity Logging:** All shipment changes are logged with detailed information
- **Status History:** Status changes are tracked in shipment_status_logs table
- **Relationship Chain:** Shipments → Branch Allocations → Branch Allocation Items → Products
- **Eager Loading:** Relationships are eager loaded for performance (customer, salesOrder, branchAllocation.items.product, branchAllocation.branch)
- **Transaction Management:** Shipment creation uses database transactions for data integrity

