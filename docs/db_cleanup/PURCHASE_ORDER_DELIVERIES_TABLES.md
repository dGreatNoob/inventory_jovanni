# Purchase Order Deliveries Module - Database Tables Analysis

**Route:** `/po-management/deliveries`  
**Component:** `App\Livewire\Pages\POManagement\PurchaseOrder\PODeliveries`  
**View:** `resources/views/livewire/pages/POmanagement/purchase-order/PO-deliveries.blade.php`

## Directly Used Tables

### 1. `purchase_orders` (Primary Table)
- **Model:** `App\Models\PurchaseOrder`
- **Usage:**
  - List purchase orders with delivery status (`to_receive`, `received`)
  - Search by PO number
  - Filter by delivery status (complete, partial, pending)
  - Display order information (PO number, order date, expected delivery date)
  - Calculate delivery progress
- **Relationships:**
  - `belongsTo` → `supplier` (Supplier model)
  - `hasMany` → `productOrders` (ProductOrder model)
- **Fields Used:**
  - `po_num` - PO number (search, display)
  - `status` - Filter by delivery status (`to_receive`, `received`)
  - `order_date` - Display order date
  - `expected_delivery_date` - Display expected delivery date
- **Filtering:**
  - Status filter: `to_receive` or `received`
  - Custom filters:
    - `complete`: Status is `received`
    - `partial`: Status is `to_receive` AND has received quantities > 0
    - `pending`: Status is `to_receive` AND has received quantities = 0

### 2. `product_orders`
- **Model:** `App\Models\ProductOrder`
- **Usage:**
  - Calculate delivery progress (total ordered vs received)
  - Determine delivery status (complete, partial, pending)
  - Track received quantities and destroyed quantities
  - Calculate progress percentage
- **Relationships:**
  - `belongsTo` → `purchaseOrder` (PurchaseOrder model)
  - `belongsTo` → `product` (Product model)
- **Fields Used:**
  - `quantity` - Total ordered quantity
  - `expected_qty` - Expected quantity (if different from quantity)
  - `received_quantity` - Good items received
  - `destroyed_qty` - Damaged items
- **Calculations:**
  - Total Ordered: `sum('quantity')`
  - Total Expected: `sum('expected_qty ?? quantity')`
  - Total Received: `sum('received_quantity')`
  - Total Destroyed: `sum('destroyed_qty')`
  - Total Delivered: `received_quantity + destroyed_qty`
  - Progress: `(total_delivered / total_expected) * 100`

### 3. `suppliers`
- **Model:** `App\Models\Supplier`
- **Usage:**
  - Display supplier name in deliveries list
  - Search purchase orders by supplier name
  - Relationship loading for display
- **Relationships:**
  - `hasMany` → `purchaseOrders` (PurchaseOrder model)

## Indirectly Used Tables (via Relationships)

### 4. `products`
- **Model:** `App\Models\Product`
- **Usage:**
  - Referenced via `product_orders` relationship
  - Product information for order items (indirectly used)
- **Relationships:**
  - `hasMany` → `productOrders` (ProductOrder model)

## Summary

**Total Tables Used: 4**

1. ✅ `purchase_orders` - Primary table
2. ✅ `product_orders` - Delivery progress calculation
3. ✅ `suppliers` - Supplier information
4. ✅ `products` - Product information (via relationships)

## Notes

- **Status Filtering:** Only shows purchase orders with status `to_receive` or `received`
- **Progress Calculation:** 
  - Uses `expected_qty` if available, otherwise falls back to `quantity`
  - Progress = (total delivered / total expected) * 100
  - Total delivered = received_quantity + destroyed_qty
- **Delivery Status Logic:**
  - **Complete**: Progress >= 100% OR status is `received`
  - **Partial**: Progress > 0% AND progress < 100%
  - **Pending**: Progress = 0%
- **Actions:**
  - View: Links to purchase order detail page
  - Start Receiving: Links to warehouse stock-in page (for `to_receive` status only)
- **No Direct Delivery Table:** This module does NOT directly use `purchase_order_deliveries` table. It calculates delivery status from `product_orders` received quantities.

