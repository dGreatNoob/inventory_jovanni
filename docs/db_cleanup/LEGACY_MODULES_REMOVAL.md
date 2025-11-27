# Legacy Modules Removal Documentation

**Date:** 2025-11-28  
**Status:** ✅ Migration Created

## Overview

This document tracks the removal of legacy/commented out modules that are not actively used in the application. These modules exist in code but are commented out in the sidebar navigation and have no data.

## Tables Removed

### Paper Roll Warehouse Module (PRW)
- **Status:** Not in sidebar, 0 records
- **Tables Removed:**
  1. `raw_mat_invs` - Raw material inventory
  2. `raw_mat_orders` - Raw material orders
  3. `raw_mat_profiles` - Raw material profiles

### Request Slip Module
- **Status:** Commented out in sidebar, 0 records
- **Tables Removed:**
  4. `request_slips` - Request slip records

### Sales Return Module
- **Status:** Commented out in sidebar, 0 records
- **Tables Removed:**
  5. `sales_returns` - Sales return headers
  6. `sales_return_items` - Sales return line items

### Sales Price Module
- **Status:** Commented out in sidebar, 0 records
- **Tables Removed:**
  7. `sales_prices` - Sales price records

### Sales Order Branch Items
- **Status:** 0 records, potentially unused
- **Tables Removed:**
  8. `sales_order_branch_items` - Sales order branch-specific items

## Migration Details

**Migration File:** `database/migrations/2025_11_28_000619_drop_legacy_module_tables.php`

**Drop Order:**
1. `raw_mat_invs` (child of raw_mat_orders)
2. `raw_mat_orders` (child of raw_mat_profiles)
3. `raw_mat_profiles`
4. `sales_return_items` (child of sales_returns)
5. `sales_returns`
6. `sales_order_branch_items`
7. `request_slips`
8. `sales_prices`

## Related Code to Remove (Optional Cleanup)

### Models (Can be removed)
- `app/Models/RawMatProfile.php`
- `app/Models/RawMatOrder.php`
- `app/Models/RawMatInv.php`
- `app/Models/RequestSlip.php`
- `app/Models/SalesReturn.php`
- `app/Models/SalesReturnItem.php`
- `app/Models/SalesOrderBranchItem.php`
- `app/Models/SalesPrice.php`

### Livewire Components (Can be removed)
- `app/Livewire/Pages/PaperRollWarehouse/` (entire directory)
- `app/Livewire/Pages/Requisition/RequestSlip/` (entire directory)
- `app/Livewire/Pages/SalesManagement/SalesReturn.php`
- `app/Livewire/Pages/SalesManagement/ViewSalesReturn.php`
- `app/Livewire/SalesPrice/Index.php`

### Routes (Can be removed)
From `routes/web.php`:
- Lines 8-9: RequestSlip imports
- Lines 18-25: PaperRollWarehouse imports
- Lines 60: SalesPrice import
- Line 92-93: `/RequestSlip` route
- Line 150: `/RequestSlip/{id}` route
- Lines 107-115: `/prw/*` routes
- Lines 209-210: `/sales-return` routes
- Line 213: `/sales-price` route
- Line 333: `/reports/sales-returns` route

### Views (Can be removed)
- `resources/views/livewire/pages/paper-roll-warehouse/` (entire directory)
- `resources/views/livewire/pages/requisition/request-slip/` (entire directory)
- `resources/views/livewire/pages/sales-management/sales-return.blade.php`
- `resources/views/livewire/pages/sales-management/view-sales-return.blade.php`
- `resources/views/livewire/sales-price/` (entire directory)

## Execution Steps

### Step 1: Run Migration
```bash
php artisan migrate
```

### Step 2: Verify Tables Dropped
```bash
php artisan db:show
# Or check database directly
```

### Step 3: (Optional) Remove Related Code
After confirming migration works:
1. Remove models listed above
2. Remove Livewire components listed above
3. Remove routes listed above
4. Remove views listed above
5. Clean up any remaining references

## Rollback

If needed, rollback the migration:
```bash
php artisan migrate:rollback --step=1
```

This will recreate the tables (empty) with their original structure.

## Notes

- **Data Loss:** All data in these tables will be permanently deleted
- **Foreign Keys:** Foreign key constraints are automatically dropped when tables are dropped
- **Dependencies:** No other active tables depend on these tables
- **Future Use:** If these modules are needed in the future, they can be recreated from scratch

## Verification

After migration, verify:
- [ ] Tables are dropped from database
- [ ] Application still works correctly
- [ ] No errors in logs
- [ ] Related code can be safely removed (optional)

