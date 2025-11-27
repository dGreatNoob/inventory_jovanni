# Consolidated Database Tables Summary

**Generated:** 2025-01-XX  
**Purpose:** Comprehensive summary of all database tables used across all modules in the inventory management system.

## Overview

This document consolidates all database table usage analysis from individual module documentation. It provides a complete view of which tables are used by which modules, helping identify:
- Tables that are actively used
- Tables that may be unused or candidates for cleanup
- Module dependencies and relationships

## Table Usage by Module

### 1. Supplier Management (`/suppliermanagement/profile`)
**Tables Used:**
- `suppliers` (Primary)
- `categories`
- `products`
- `purchase_orders`
- `product_orders`
- `purchase_order_deliveries`
- `activity_log`
- `users`

### 2. Purchase Order Management (`/po-management/purchase-order`)
**Tables Used:**
- `purchase_orders` (Primary)
- `product_orders`
- `purchase_order_approval_logs`
- `suppliers`
- `departments`
- `users`
- `purchase_order_deliveries`
- `product_batches`
- `products`

### 3. Purchase Order Deliveries (`/po-management/deliveries`)
**Tables Used:**
- `purchase_orders`
- `product_orders`
- `suppliers`
- `products`

### 4. Product Management (`/product-management`)
**Tables Used:**
- `products` (Primary)
- `categories`
- `suppliers`
- `product_colors`
- `product_images`
- `product_price_histories`
- `product_inventory`
- `inventory_movements`
- `inventory_locations`
- `branch_product`
- `branches`
- `product_batches`
- `users`

### 5. Product Category Management (`/product-management/categories`)
**Tables Used:**
- `categories` (Primary)
- `products`
- `users`

### 6. Product Image Gallery (`/product-management/images`)
**Tables Used:**
- `product_images` (Primary)
- `products`
- `categories`
- `users`

### 7. Sales Promo (`/sales-promo`)
**Tables Used:**
- `promos` (Primary)
- `batch_allocations`
- `branch_allocations`
- `branch_allocation_items`
- `products`
- `branches`

### 8. User Management (`/user-management`)
**Tables Used:**
- `users` (Primary)
- `departments`
- `roles`
- `model_has_roles`
- `permissions`
- `role_has_permissions`
- `model_has_permissions`

### 9. Roles & Permissions (`/roles-permissions`)
**Tables Used:**
- `roles` (Primary)
- `permissions`
- `role_has_permissions`

### 10. Allocation Warehouse (`/allocation/warehouse`)
**Tables Used:**
- `batch_allocations` (Primary)
- `branch_allocations`
- `branch_allocation_items`
- `branches`
- `products`
- `sales_receipts`
- `sales_receipt_items`
- `activity_log`
- `users`

### 11. Allocation Sales (`/allocation/sales`)
**Tables Used:**
- `batch_allocations` (Primary)
- `sales_receipts`
- `sales_receipt_items`
- `branch_allocations`
- `products`
- `branches`

### 12. Agent Management (`/agentmanagement/profile`)
**Tables Used:**
- `agents` (Primary)
- `agent_branch_assignments`
- `branches`
- `users`

### 13. Branch Management (`/Branchmanagement/profile`)
**Tables Used:**
- `branches` (Primary)
- `agent_branch_assignments`
- `agents`
- `users`

### 14. Branch Inventory (`/branch-inventory`)
**Tables Used:**
- `branches` (Primary)
- `products`
- `branch_product`

### 15. Finance Receivables (`/finance/receivables`)
**Tables Used:**
- `finances` (Primary)
- `branches`
- `agents`
- `users`

### 16. Finance Expenses (`/finance/expenses`)
**Tables Used:**
- `finances` (Primary)

### 17. Finance Payments (`/finance/payments`)
**Tables Used:**
- `payments` (Primary)
- `finances`

### 18. Shipment (`/Shipment`)
**Tables Used:**
- `shipments` (Primary)
- `sales_orders`
- `batch_allocations`
- `branch_allocations`
- `branch_allocation_items`
- `customers`
- `users`
- `products`
- `branches`
- `shipment_status_logs`
- `activity_log`

### 19. Shipment QR Scanner (`/Shipment/scan`)
**Tables Used:**
- `shipments` (Primary)
- `branch_allocations`
- `branch_allocation_items`
- `products`
- `product_inventory`
- `branch_product`
- `activity_log`
- `users`

### 20. Activity Logs (`/activity-logs`)
**Tables Used:**
- `activity_log` (Primary)
- `departments`
- `users`
- `roles`
- `model_has_roles`

## Complete Table List

### Core Tables (Used by Multiple Modules)

1. **`users`** - User accounts and authentication
   - Used by: Supplier Management, PO Management, Product Management, User Management, Roles & Permissions, Allocation Warehouse, Agent Management, Branch Management, Finance Receivables, Shipment, Shipment QR Scanner, Activity Logs

2. **`products`** - Product master data
   - Used by: Supplier Management, PO Management, PO Deliveries, Product Management, Product Category Management, Product Image Gallery, Sales Promo, Allocation Warehouse, Allocation Sales, Branch Inventory, Shipment, Shipment QR Scanner

3. **`categories`** - Product categories
   - Used by: Supplier Management, Product Management, Product Category Management, Product Image Gallery

4. **`suppliers`** - Supplier master data
   - Used by: Supplier Management, PO Management, PO Deliveries

5. **`branches`** - Branch/warehouse locations
   - Used by: Product Management, Sales Promo, Allocation Warehouse, Allocation Sales, Agent Management, Branch Management, Branch Inventory, Finance Receivables, Shipment

6. **`departments`** - Department/organizational units
   - Used by: PO Management, User Management, Activity Logs

7. **`activity_log`** - System activity logging (Spatie Activity Log)
   - Used by: Supplier Management, Allocation Warehouse, Shipment, Shipment QR Scanner, Activity Logs

### Purchase Order Tables

8. **`purchase_orders`** - Purchase order headers
   - Used by: Supplier Management, PO Management, PO Deliveries

9. **`product_orders`** - Purchase order line items
   - Used by: Supplier Management, PO Management, PO Deliveries

10. **`purchase_order_approval_logs`** - PO approval history
    - Used by: PO Management

11. **`purchase_order_deliveries`** - PO delivery records
    - Used by: Supplier Management, PO Management

### Product-Related Tables

12. **`product_colors`** - Product color options
    - Used by: Product Management

13. **`product_images`** - Product image gallery
    - Used by: Product Management, Product Image Gallery

14. **`product_price_histories`** - Product price change history
    - Used by: Product Management

15. **`product_inventory`** - Product inventory records
    - Used by: Product Management, Shipment QR Scanner

16. **`inventory_movements`** - Inventory movement transactions
    - Used by: Product Management

17. **`inventory_locations`** - Inventory storage locations
    - Used by: Product Management

18. **`product_batches`** - Product batch/lot tracking
    - Used by: Product Management, PO Management

19. **`branch_product`** - Branch inventory pivot table
    - Used by: Product Management, Branch Inventory, Shipment QR Scanner

### Allocation Tables

20. **`batch_allocations`** - Batch allocation headers
    - Used by: Sales Promo, Allocation Warehouse, Allocation Sales, Shipment

21. **`branch_allocations`** - Branch allocation headers
    - Used by: Sales Promo, Allocation Warehouse, Allocation Sales, Shipment, Shipment QR Scanner

22. **`branch_allocation_items`** - Branch allocation line items
    - Used by: Sales Promo, Allocation Warehouse, Allocation Sales, Shipment, Shipment QR Scanner

### Sales Tables

23. **`sales_receipts`** - Sales receipt headers
    - Used by: Allocation Warehouse, Allocation Sales

24. **`sales_receipt_items`** - Sales receipt line items
    - Used by: Allocation Warehouse, Allocation Sales

25. **`sales_orders`** - Sales order headers
    - Used by: Shipment

26. **`promos`** - Promotional campaigns
    - Used by: Sales Promo

### Finance Tables

27. **`finances`** - Finance records (payables/receivables/expenses)
    - Used by: Finance Receivables, Finance Expenses, Finance Payments

28. **`payments`** - Payment transactions
    - Used by: Finance Payments

### Agent & Branch Management Tables

29. **`agents`** - Agent master data
    - Used by: Agent Management, Branch Management, Finance Receivables

30. **`agent_branch_assignments`** - Agent-branch assignments
    - Used by: Agent Management, Branch Management

### Shipment Tables

31. **`shipments`** - Shipment records
    - Used by: Shipment, Shipment QR Scanner

32. **`shipment_status_logs`** - Shipment status change history
    - Used by: Shipment

33. **`customers`** - Customer master data
    - Used by: Shipment

### Permission & Role Tables (Spatie Permissions)

34. **`roles`** - User roles
    - Used by: User Management, Roles & Permissions, Activity Logs

35. **`permissions`** - System permissions
    - Used by: User Management, Roles & Permissions

36. **`role_has_permissions`** - Role-permission assignments
    - Used by: User Management, Roles & Permissions

37. **`model_has_roles`** - User-role assignments
    - Used by: User Management, Activity Logs

38. **`model_has_permissions`** - Direct user-permission assignments
    - Used by: User Management

## Table Usage Statistics

### Most Used Tables (Used by 5+ Modules)
1. `users` - 12 modules
2. `products` - 12 modules
3. `branches` - 9 modules
4. `activity_log` - 5 modules

### Medium Usage Tables (Used by 2-4 Modules)
- `categories` - 4 modules
- `suppliers` - 3 modules
- `departments` - 3 modules
- `purchase_orders` - 3 modules
- `product_orders` - 3 modules
- `branch_allocations` - 5 modules
- `branch_allocation_items` - 5 modules
- `batch_allocations` - 4 modules
- `finances` - 3 modules
- `roles` - 3 modules
- `model_has_roles` - 2 modules

### Single Module Tables (Used by 1 Module)
- `purchase_order_approval_logs`
- `purchase_order_deliveries`
- `product_colors`
- `product_images`
- `product_price_histories`
- `inventory_movements`
- `inventory_locations`
- `product_batches`
- `sales_receipts`
- `sales_receipt_items`
- `sales_orders`
- `promos`
- `payments`
- `agents`
- `agent_branch_assignments`
- `shipments`
- `shipment_status_logs`
- `customers`
- `permissions`
- `role_has_permissions`
- `model_has_permissions`

## Module Dependencies

### High Dependency Modules (Use 10+ Tables)
1. **Product Management** - 13 tables
2. **Shipment** - 11 tables
3. **Allocation Warehouse** - 9 tables
4. **Purchase Order Management** - 9 tables

### Medium Dependency Modules (Use 5-9 Tables)
1. **Supplier Management** - 8 tables
2. **Allocation Sales** - 6 tables
3. **User Management** - 7 tables
4. **Sales Promo** - 6 tables
5. **Shipment QR Scanner** - 8 tables
6. **Finance Receivables** - 4 tables

### Low Dependency Modules (Use 1-4 Tables)
1. **Product Category Management** - 3 tables
2. **Product Image Gallery** - 4 tables
3. **Roles & Permissions** - 3 tables
4. **Branch Inventory** - 3 tables
5. **Finance Expenses** - 1 table
6. **Finance Payments** - 2 tables
7. **Activity Logs** - 5 tables
8. **Agent Management** - 4 tables
9. **Branch Management** - 4 tables
10. **PO Deliveries** - 4 tables

## Notes for Cleanup

### Tables Already Cleaned Up
- `item_classes` - Dropped (unused)
- `stock_batches` - Dropped (unused)
- `logs` - Dropped (replaced by `activity_log`)

### Potential Cleanup Candidates
Based on the analysis, all documented tables are actively used by at least one module. However, consider reviewing:

1. **Tables with single module usage** - These may be candidates for consolidation if functionality is merged
2. **Pivot tables** - Ensure all pivot tables (`branch_product`, `model_has_roles`, etc.) are necessary
3. **Log/history tables** - Verify if all log tables are needed or can be consolidated

### Tables Not Analyzed
The following tables may exist in the database but were not analyzed:
- Any tables not referenced by the analyzed modules
- System tables (migrations, sessions, cache, etc.)
- Third-party package tables not covered in module analysis

## Recommendations

1. **Keep All Documented Tables** - All tables listed in this document are actively used
2. **Review Undocumented Tables** - Check for tables in the database that are not listed here
3. **Monitor Table Growth** - Track table sizes, especially for log/history tables
4. **Consider Archiving** - For high-volume log tables, consider archiving old records
5. **Documentation** - Keep this document updated as new modules are added

## Related Documentation

Individual module analysis documents are available in the `docs/db_cleanup/` directory:
- `SUPPLIER_MANAGEMENT_TABLES.md`
- `PURCHASE_ORDER_MANAGEMENT_TABLES.md`
- `PURCHASE_ORDER_DELIVERIES_TABLES.md`
- `PRODUCT_MANAGEMENT_TABLES.md`
- `PRODUCT_CATEGORY_MANAGEMENT_TABLES.md`
- `PRODUCT_IMAGE_GALLERY_TABLES.md`
- `SALES_PROMO_TABLES.md`
- `USER_MANAGEMENT_TABLES.md`
- `ROLES_PERMISSIONS_TABLES.md`
- `ALLOCATION_WAREHOUSE_TABLES.md`
- `ALLOCATION_SALES_TABLES.md`
- `AGENT_MANAGEMENT_TABLES.md`
- `BRANCH_MANAGEMENT_TABLES.md`
- `BRANCH_INVENTORY_TABLES.md`
- `FINANCE_RECEIVABLES_TABLES.md`
- `FINANCE_EXPENSES_TABLES.md`
- `FINANCE_PAYMENTS_TABLES.md`
- `SHIPMENT_TABLES.md`
- `SHIPMENT_QR_SCANNER_TABLES.md`
- `ACTIVITY_LOGS_TABLES.md`

