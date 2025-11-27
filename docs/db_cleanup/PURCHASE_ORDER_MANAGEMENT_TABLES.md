# Purchase Order Management Module - Database Tables Analysis

**Route:** `/po-management/purchase-order`  
**Component:** `App\Livewire\Pages\POManagement\PurchaseOrder\Index`  
**View:** `resources/views/livewire/pages/POmanagement/purchase-order/index.blade.php`

## Directly Used Tables

### 1. `purchase_orders` (Primary Table)
- **Model:** `App\Models\PurchaseOrder`
- **Usage:**
  - Main CRUD operations (list, view, edit, delete)
  - Search and filtering (by PO number, supplier, department, payment terms)
  - Status filtering (pending, approved, to_receive, received, cancelled)
  - Display purchase order list with pagination
  - Status transitions (mark as delivered, mark as received)
  - QR code generation for purchase orders
- **Relationships:**
  - `belongsTo` → `supplier` (Supplier model)
  - `belongsTo` → `department` (Department model)
  - `belongsTo` → `orderedByUser` (User model)
  - `belongsTo` → `approverInfo` (User model)
  - `belongsTo` → `approvedByUser` (User model)
  - `belongsTo` → `cancelledByUser` (User model)
  - `hasMany` → `productOrders` (ProductOrder model)
  - `hasMany` → `batches` (ProductBatch model)
  - `hasMany` → `approvalLogs` (PurchaseOrderApprovalLog model)
  - `hasMany` → `deliveries` (PurchaseOrderDelivery model)
- **Fields Used:**
  - `po_num` - PO number (search, display)
  - `status` - Status filtering and display
  - `order_date` - Display and sorting
  - `del_on` - Date received display
  - `total_qty` - Total quantity display
  - `total_price` - Total price display
  - `expected_delivery_date` - Delivery tracking
  - `payment_terms` - Search field

### 2. `product_orders`
- **Model:** `App\Models\ProductOrder`
- **Usage:**
  - Calculate delivery progress (total ordered vs received)
  - Determine "Partially Received" status
  - Track received quantities and destroyed quantities
  - Delete associated items when PO is deleted
- **Relationships:**
  - `belongsTo` → `purchaseOrder` (PurchaseOrder model)
  - `belongsTo` → `product` (Product model)
- **Fields Used:**
  - `quantity` - Total ordered quantity
  - `received_quantity` - Good items received
  - `destroyed_qty` - Damaged items
  - `expected_qty` - Expected quantity (if different from quantity)

### 3. `purchase_order_approval_logs`
- **Model:** `App\Models\PurchaseOrderApprovalLog`
- **Usage:**
  - Display approval history in modal
  - Log status changes (approved, rejected, delivered, received, to_receive)
  - Track user actions and timestamps
  - Display action counts in list view
- **Relationships:**
  - `belongsTo` → `purchaseOrder` (PurchaseOrder model)
  - `belongsTo` → `user` (User model)
- **Fields Used:**
  - `action` - Action type (approved, rejected, delivered, received, etc.)
  - `remarks` - Action remarks
  - `created_at` - Timestamp
  - `user_id` - User who performed action
  - `ip_address` - IP address tracking

### 4. `suppliers`
- **Model:** `App\Models\Supplier`
- **Usage:**
  - Display supplier name in purchase order list
  - Search purchase orders by supplier name
  - Filter and relationship loading
- **Relationships:**
  - `hasMany` → `purchaseOrders` (PurchaseOrder model)

### 5. `departments`
- **Model:** `App\Models\Department`
- **Usage:**
  - Display delivery department in purchase order list
  - Search purchase orders by department name
  - Filter and relationship loading
- **Relationships:**
  - `hasMany` → `purchaseOrders` (PurchaseOrder model, via `del_to`)

### 6. `users`
- **Model:** `App\Models\User`
- **Usage:**
  - Permission checks (`po create`, `po edit`, `po delete`, `po report view`)
  - Display user information in approval logs
  - Track who ordered, approved, or cancelled purchase orders
- **Relationships:**
  - `hasMany` → `purchaseOrders` (as `ordered_by`, `approver`, `approved_by`, `cancelled_by`)
  - `hasMany` → `approvalLogs` (PurchaseOrderApprovalLog model)

## Indirectly Used Tables (via Relationships)

### 7. `purchase_order_deliveries`
- **Model:** `App\Models\PurchaseOrderDelivery`
- **Usage:**
  - Track delivery records (via relationship)
  - Get delivery dates and DR numbers
- **Relationships:**
  - `belongsTo` → `purchaseOrder` (PurchaseOrder model)
  - `hasMany` → `supplyBatches` (SupplyBatch model)

### 8. `product_batches`
- **Model:** `App\Models\ProductBatch`
- **Usage:**
  - Track batches created from purchase orders
  - Link batches to purchase orders
- **Relationships:**
  - `belongsTo` → `purchaseOrder` (PurchaseOrder model)
  - `belongsTo` → `product` (Product model)
  - `belongsTo` → `receivedByUser` (User model)

### 9. `products`
- **Model:** `App\Models\Product`
- **Usage:**
  - Referenced via `product_orders` relationship
  - Product information for order items
- **Relationships:**
  - `hasMany` → `productOrders` (ProductOrder model)

## Analytics Tab (Optional)

### 10. Additional Tables (if Analytics component is used)
- **Component:** `App\Livewire\Pages\POManagement\PurchaseOrder\Analytics`
- May use additional tables for reporting and analytics
- Check Analytics component for specific table usage

## Summary

**Total Tables Used: 9-10**

1. ✅ `purchase_orders` - Primary table
2. ✅ `product_orders` - Order items and delivery tracking
3. ✅ `purchase_order_approval_logs` - Approval history
4. ✅ `suppliers` - Supplier information
5. ✅ `departments` - Delivery department
6. ✅ `users` - User tracking and permissions
7. ✅ `purchase_order_deliveries` - Delivery records
8. ✅ `product_batches` - Batch tracking
9. ✅ `products` - Product information (via relationships)
10. ⚠️ Additional tables (if Analytics component is used)

## Notes

- **Status Enum:** Uses `App\Enums\PurchaseOrderStatus` enum for status management
- **Soft Deletes:** Purchase orders can be deleted (cascades to product_orders)
- **Approval Workflow:** Tracks approval history with timestamps and user information
- **Partial Receiving:** Calculates partial receiving status based on received quantities
- **QR Codes:** Generates QR codes for purchase orders
- **Status Transitions:** 
  - `pending` → `approved` → `to_receive` → `received`
  - Can be `cancelled` at various stages
  - Can be marked as `delivered` (intermediate step)

