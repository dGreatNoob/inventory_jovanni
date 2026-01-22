# Unused Tables Analysis

**Generated:** 2025-01-XX  
**Purpose:** Detailed analysis of tables that exist in migrations but are not documented as used by any module.

## Tables from Migrations

Total tables found in migrations: **60**

## Documented Active Tables (38)

These tables are confirmed to be used by at least one module:

1. `users`
2. `products`
3. `categories`
4. `suppliers`
5. `branches`
6. `departments`
7. `activity_log`
8. `purchase_orders`
9. `product_orders`
10. `purchase_order_approval_logs`
11. `purchase_order_deliveries`
12. `product_colors`
13. `product_images`
14. `product_price_histories`
15. `product_inventory`
16. `inventory_movements`
17. `inventory_locations`
18. `product_batches`
19. `branch_product`
20. `batch_allocations`
21. `branch_allocations`
22. `branch_allocation_items`
23. `sales_receipts`
24. `sales_receipt_items`
25. `sales_orders`
26. `promos`
27. `finances`
28. `payments`
29. `agents`
30. `agent_branch_assignments`
31. `shipments`
32. `shipment_status_logs`
33. `customers`
34. `roles`
35. `permissions`
36. `role_has_permissions`
37. `model_has_roles`
38. `model_has_permissions`

## System/Infrastructure Tables (Keep - 7)

These are required by Laravel or third-party packages:

1. `cache` - Laravel cache
2. `cache_locks` - Laravel cache locks
3. `jobs` - Laravel queue jobs
4. `job_batches` - Laravel queue batches
5. `failed_jobs` - Laravel failed jobs
6. `sessions` - Laravel sessions
7. `password_reset_tokens` - Laravel password resets

## Tables Already Cleaned Up (3)

1. `item_classes` ✅ Dropped
2. `stock_batches` ✅ Dropped
3. `logs` ✅ Dropped (replaced by `activity_log`)

## Potential Unused Tables (12)

These tables exist in migrations but are NOT documented as used by any module:

### High Priority for Verification

1. **`raw_mat_profiles`**
   - **Migration:** `2025_05_12_094812_create_raw_mat_profiles_table.php`
   - **Status:** Needs codebase search
   - **Action:** Search for `RawMatProfile` model usage
   - **Risk:** Low if unused

2. **`raw_mat_orders`**
   - **Migration:** `2025_05_12_095415_create_raw_mat_orders_table.php`
   - **Status:** Needs codebase search
   - **Action:** Search for `RawMatOrder` model usage
   - **Risk:** Low if unused

3. **`raw_mat_invs`**
   - **Migration:** `2025_05_12_095647_create_raw_mat_invs_table.php`
   - **Status:** Needs codebase search
   - **Action:** Search for `RawMatInv` model usage
   - **Risk:** Low if unused

4. **`request_slips`**
   - **Migration:** `2025_05_14_052559_create_request_slips_table.php`
   - **Status:** Needs codebase search
   - **Action:** Search for `RequestSlip` model usage
   - **Risk:** Medium (may be used in approval workflows)

5. **`sales_returns`**
   - **Migration:** `2025_07_15_163015_create_sales_returns_table.php`
   - **Status:** Needs codebase search
   - **Action:** Search for `SalesReturn` model usage
   - **Risk:** Medium (may be used for return processing)

6. **`sales_return_items`**
   - **Migration:** `2025_07_15_163403_create_sales_return_items_table.php`
   - **Status:** Needs codebase search
   - **Action:** Search for `SalesReturnItem` model usage
   - **Risk:** Medium (may be used for return processing)

7. **`sales_order_branch_items`**
   - **Migration:** `2025_10_29_224229_create_sales_order_branch_items_table.php`
   - **Status:** Needs codebase search
   - **Action:** Search for usage in sales order processing
   - **Risk:** Medium (may be used in sales order processing)

8. **`sales_prices`**
   - **Migration:** `2025_10_21_105412_create_sales_prices_table.php`
   - **Status:** Needs codebase search
   - **Action:** Search for `SalesPrice` model usage
   - **Risk:** Medium (may be used for pricing)

9. **`supply_profiles`**
   - **Migration:** `2025_05_12_092401_create_supply_profiles_table.php`
   - **Status:** Needs codebase search
   - **Note:** This is different from `products` table
   - **Action:** Search for `SupplyProfile` model usage
   - **Risk:** Medium (may be used in purchase order processing)

10. **`supply_orders`**
    - **Migration:** `2025_05_12_094833_create_supply_orders_table.php`
    - **Status:** Needs codebase search
    - **Action:** Search for `SupplyOrder` model usage
    - **Risk:** Medium (may be used in purchase order processing)

11. **`supply_batches`**
    - **Migration:** `2025_07_24_134251_create_supply_batches_table.php`
    - **Status:** Needs codebase search
    - **Action:** Search for `SupplyBatch` model usage
    - **Risk:** Medium (may be used for batch tracking)

12. **`notifications`**
    - **Migration:** `2025_06_30_004543_create_notifications_table.php`
    - **Status:** Needs codebase search
    - **Action:** Search for Laravel notifications usage
    - **Risk:** Low (Laravel notifications table, may be unused)

13. **`item_types`**
    - **Migration:** `2025_05_11_223612_create_item_types_table.php`
    - **Status:** Needs codebase search
    - **Action:** Search for `ItemType` model usage
    - **Risk:** Medium (may be used in supply profiles)

14. **`purchase_order_items`**
    - **Migration:** `2025_09_12_000005_create_purchase_order_items_table.php`
    - **Status:** Needs verification
    - **Note:** May be used instead of `product_orders`
    - **Action:** Verify if this is used or if `product_orders` is the active table
    - **Risk:** High (may be actively used)

## Verification Steps

### Step 1: Codebase Search

For each potential unused table, search the codebase:

```bash
# Search for model usage
grep -r "RawMatProfile" app/
grep -r "raw_mat_profiles" app/ database/ resources/

# Search for table name in queries
grep -r "raw_mat_profiles" app/ database/ resources/

# Search for migrations
grep -r "raw_mat_profiles" database/migrations/
```

### Step 2: Database Check

```sql
-- Check if table has any records
SELECT COUNT(*) FROM raw_mat_profiles;

-- Check for foreign key dependencies
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_NAME = 'raw_mat_profiles';

-- Check for tables referencing this table
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_NAME = 'raw_mat_profiles';
```

### Step 3: Model Check

Check if model exists:
```bash
ls -la app/Models/RawMatProfile.php
ls -la app/Models/SupplyProfile.php
```

## Recommended Cleanup Order

### Phase 1: Low Risk Tables (If Unused)
1. `raw_mat_profiles`
2. `raw_mat_orders`
3. `raw_mat_invs`
4. `notifications` (if Laravel notifications not used)

### Phase 2: Medium Risk Tables (If Unused)
1. `sales_returns`
2. `sales_return_items`
3. `sales_order_branch_items`
4. `sales_prices`
5. `request_slips`

### Phase 3: High Risk Tables (Verify Carefully)
1. `supply_profiles` (may be actively used)
2. `supply_orders` (may be actively used)
3. `supply_batches` (may be actively used)
4. `purchase_order_items` (may be actively used)
5. `item_types` (may be used by supply profiles)

## Cleanup Script Template

```php
<?php
// verify_unused_tables.php

$tablesToVerify = [
    'raw_mat_profiles',
    'raw_mat_orders',
    'raw_mat_invs',
    'request_slips',
    'sales_returns',
    'sales_return_items',
    'sales_order_branch_items',
    'sales_prices',
    'supply_profiles',
    'supply_orders',
    'supply_batches',
    'notifications',
    'item_types',
    'purchase_order_items',
];

foreach ($tablesToVerify as $table) {
    echo "Checking: $table\n";
    
    // Check if model exists
    $modelName = str_replace('_', '', ucwords($table, '_'));
    $modelPath = "app/Models/$modelName.php";
    
    if (file_exists($modelPath)) {
        echo "  ✓ Model exists: $modelPath\n";
    } else {
        echo "  ✗ Model not found\n";
    }
    
    // Check codebase usage
    $grepResult = shell_exec("grep -r '$table' app/ database/ resources/ 2>/dev/null | wc -l");
    echo "  Found $grepResult references in codebase\n";
    
    // Check database
    try {
        $count = DB::table($table)->count();
        echo "  Records in database: $count\n";
    } catch (\Exception $e) {
        echo "  ✗ Table does not exist in database\n";
    }
    
    echo "\n";
}
```

## Next Steps

1. **Run Verification Script** - Execute the verification script for each table
2. **Codebase Search** - Search for each table/model in the codebase
3. **Database Check** - Verify table existence and record counts
4. **Dependency Check** - Check for foreign key dependencies
5. **Create Cleanup Migration** - Only after verification confirms unused status

## Notes

- Always backup before cleanup
- Verify on development/staging first
- Remove tables in phases
- Monitor application after each phase
- Keep backups for at least 30 days

