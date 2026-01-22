# Database Cleanup Plan

**Generated:** 2025-01-XX  
**Purpose:** Systematic plan to identify and remove unused database tables and optimize the database schema.

## Overview

This cleanup plan is based on the comprehensive module analysis that documented all tables used across 20 modules. The goal is to:
1. Identify tables that exist in migrations but are not used by any module
2. Identify tables that may be candidates for consolidation
3. Create a safe, step-by-step cleanup process
4. Document all cleanup actions for rollback capability

## Phase 1: Analysis & Verification

### Step 1.1: Extract All Tables from Migrations
- [ ] Run script to extract all table names from migration files
- [ ] Create a complete list of tables that exist in the database schema
- [ ] Compare with documented tables from module analysis

### Step 1.2: Check Database State
- [ ] Connect to database and list all existing tables
- [ ] Check for tables that exist in DB but not in migrations (orphaned tables)
- [ ] Check for tables in migrations but not in DB (missing tables)
- [ ] Verify foreign key constraints

### Step 1.3: Identify Unused Tables
- [ ] Compare migration tables with documented tables
- [ ] Identify tables not referenced in any module
- [ ] Check for tables with zero records (candidates for removal)
- [ ] Verify no hidden dependencies (check foreign keys pointing to these tables)

## Phase 2: Pre-Cleanup Verification

### Step 2.1: Backup Strategy
- [ ] Create full database backup before cleanup
- [ ] Document backup location and timestamp
- [ ] Test backup restoration process

### Step 2.2: Dependency Check
For each candidate table:
- [ ] Check for foreign key constraints (tables referencing this table)
- [ ] Check for models that might use this table
- [ ] Search codebase for table name references
- [ ] Check for views or stored procedures using the table

### Step 2.3: Data Preservation
- [ ] Export data from tables to be removed (if any data exists)
- [ ] Document data export locations
- [ ] Create CSV/JSON backups of removed table data

## Phase 3: Cleanup Execution

### Tables Already Cleaned Up (Completed)
✅ **DONE** - These tables were already removed:
- `item_classes` - Dropped via migration `2025_11_27_230811_drop_unused_tables.php`
- `stock_batches` - Dropped via migration `2025_11_27_230811_drop_unused_tables.php`
- `logs` - Dropped via migration `2025_11_27_230811_drop_unused_tables.php` (replaced by `activity_log`)

### Potential Cleanup Candidates

Based on migration analysis, the following tables exist in migrations but need verification:

#### Category A: Likely Unused (Need Verification)

1. **`raw_mat_profiles`**
   - **Status:** Needs verification
   - **Action:** Search codebase for usage
   - **Risk:** Low (if unused)

2. **`raw_mat_orders`**
   - **Status:** Needs verification
   - **Action:** Search codebase for usage
   - **Risk:** Low (if unused)

3. **`raw_mat_invs`**
   - **Status:** Needs verification
   - **Action:** Search codebase for usage
   - **Risk:** Low (if unused)

4. **`request_slips`**
   - **Status:** Needs verification
   - **Action:** Search codebase for usage
   - **Risk:** Medium (may be used in approval workflows)

5. **`sales_returns`**
   - **Status:** Needs verification
   - **Action:** Search codebase for usage
   - **Risk:** Medium (may be used for return processing)

6. **`sales_return_items`**
   - **Status:** Needs verification
   - **Action:** Search codebase for usage
   - **Risk:** Medium (may be used for return processing)

7. **`sales_order_branch_items`**
   - **Status:** Needs verification
   - **Action:** Search codebase for usage
   - **Risk:** Medium (may be used in sales order processing)

8. **`sales_prices`**
   - **Status:** Needs verification
   - **Action:** Search codebase for usage
   - **Risk:** Medium (may be used for pricing)

9. **`supply_profiles`**
   - **Status:** Needs verification
   - **Action:** Search codebase for usage
   - **Risk:** Medium (may be used in purchase order processing)

10. **`supply_orders`**
    - **Status:** Needs verification
    - **Action:** Search codebase for usage
    - **Risk:** Medium (may be used in purchase order processing)

11. **`supply_batches`**
    - **Status:** Needs verification
    - **Action:** Search codebase for usage
    - **Risk:** Medium (may be used for batch tracking)

12. **`notifications`**
    - **Status:** Needs verification
    - **Action:** Search codebase for usage
    - **Risk:** Low (Laravel notifications table, may be unused)

13. **`cache`**
    - **Status:** System table
    - **Action:** Keep (Laravel cache table)
    - **Risk:** N/A

14. **`cache_locks`**
    - **Status:** System table
    - **Action:** Keep (Laravel cache locks)
    - **Risk:** N/A

15. **`jobs`**
    - **Status:** System table
    - **Action:** Keep (Laravel queue jobs)
    - **Risk:** N/A

16. **`job_batches`**
    - **Status:** System table
    - **Action:** Keep (Laravel queue batches)
    - **Risk:** N/A

17. **`failed_jobs`**
    - **Status:** System table
    - **Action:** Keep (Laravel failed jobs)
    - **Risk:** N/A

18. **`sessions`**
    - **Status:** System table
    - **Action:** Keep (Laravel sessions)
    - **Risk:** N/A

19. **`password_reset_tokens`**
    - **Status:** System table
    - **Action:** Keep (Laravel password resets)
    - **Risk:** N/A

#### Category B: System/Infrastructure Tables (Keep)

These tables are required by Laravel or third-party packages:
- `users` ✅ Keep
- `departments` ✅ Keep
- `migrations` ✅ Keep (Laravel system)
- `activity_log` ✅ Keep (Spatie Activity Log)
- `roles` ✅ Keep (Spatie Permissions)
- `permissions` ✅ Keep (Spatie Permissions)
- `role_has_permissions` ✅ Keep (Spatie Permissions)
- `model_has_roles` ✅ Keep (Spatie Permissions)
- `model_has_permissions` ✅ Keep (Spatie Permissions)

#### Category C: Documented Active Tables (Keep)

All 38 tables documented in `CONSOLIDATED_TABLES_SUMMARY.md` are actively used:
- All tables listed in the consolidated summary should be **KEPT**
- These tables are verified to be used by at least one module

## Phase 4: Verification Script

### Script to Run Before Cleanup

```php
<?php
// verify_table_usage.php
// Run this script to verify table usage before cleanup

$documentedTables = [
    // Core Tables
    'users', 'products', 'categories', 'suppliers', 'branches', 'departments', 'activity_log',
    
    // Purchase Order Tables
    'purchase_orders', 'product_orders', 'purchase_order_approval_logs', 'purchase_order_deliveries',
    
    // Product Tables
    'product_colors', 'product_images', 'product_price_histories', 'product_inventory',
    'inventory_movements', 'inventory_locations', 'product_batches', 'branch_product',
    
    // Allocation Tables
    'batch_allocations', 'branch_allocations', 'branch_allocation_items',
    
    // Sales Tables
    'sales_receipts', 'sales_receipt_items', 'sales_orders', 'promos',
    
    // Finance Tables
    'finances', 'payments',
    
    // Agent & Branch Tables
    'agents', 'agent_branch_assignments',
    
    // Shipment Tables
    'shipments', 'shipment_status_logs', 'customers',
    
    // Permission Tables
    'roles', 'permissions', 'role_has_permissions', 'model_has_roles', 'model_has_permissions',
];

$migrationTables = [
    // Extract from migrations
    // ... (to be populated)
];

$unusedTables = array_diff($migrationTables, $documentedTables);

echo "Unused tables found: " . count($unusedTables) . "\n";
foreach ($unusedTables as $table) {
    echo "  - $table\n";
}
```

## Phase 5: Cleanup Migration Template

### Template for Cleanup Migration

```php
<?php
// database/migrations/YYYY_MM_DD_HHMMSS_cleanup_unused_tables_phase_X.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Drop foreign key constraints first
        // Step 2: Drop tables
        // Step 3: Document what was removed
        
        $tablesToRemove = [
            // Add verified unused tables here
        ];
        
        foreach ($tablesToRemove as $table) {
            // Check if table exists
            if (Schema::hasTable($table)) {
                // Drop foreign keys first
                $this->dropForeignKeys($table);
                
                // Drop table
                Schema::dropIfExists($table);
                
                echo "Dropped table: $table\n";
            }
        }
    }
    
    public function down(): void
    {
        // Recreate tables for rollback
        // This should match the original migration structure
    }
    
    private function dropForeignKeys(string $table): void
    {
        // Get all foreign keys for the table
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$table]);
        
        foreach ($foreignKeys as $fk) {
            Schema::table($table, function (Blueprint $table) use ($fk) {
                $table->dropForeign($fk->CONSTRAINT_NAME);
            });
        }
    }
};
```

## Phase 6: Execution Checklist

### Pre-Execution
- [ ] Database backup created and verified
- [ ] All unused tables identified and verified
- [ ] Dependencies checked (no foreign keys pointing to tables to be removed)
- [ ] Codebase searched for any references to tables to be removed
- [ ] Data exported (if any exists in tables to be removed)
- [ ] Team notified of cleanup operation
- [ ] Maintenance window scheduled (if needed)

### Execution
- [ ] Run verification script
- [ ] Review verification results
- [ ] Create cleanup migration
- [ ] Test migration on development/staging environment
- [ ] Run migration on production
- [ ] Verify tables are removed
- [ ] Check application functionality

### Post-Execution
- [ ] Verify application still works correctly
- [ ] Check for any errors in logs
- [ ] Update documentation
- [ ] Document cleanup in changelog
- [ ] Archive backup for future reference

## Phase 7: Monitoring & Validation

### After Cleanup
- [ ] Monitor application for 24-48 hours
- [ ] Check error logs for any issues
- [ ] Verify all modules still function correctly
- [ ] Check database performance
- [ ] Document any issues encountered

## Risk Assessment

### Low Risk Tables
- Tables with zero records
- Tables with no foreign key dependencies
- Tables not referenced in any code

### Medium Risk Tables
- Tables with data but no active usage
- Tables with foreign key dependencies (need to drop FKs first)
- Tables referenced in commented code

### High Risk Tables
- Tables with active data
- Tables with many foreign key dependencies
- Tables referenced in production code

## Rollback Plan

### If Issues Occur
1. Restore database from backup
2. Re-run migrations to recreate dropped tables
3. Restore data from exported CSV/JSON files
4. Investigate root cause
5. Update cleanup plan based on findings

## Next Steps

1. **Immediate:** Run verification script to identify all unused tables
2. **Short-term:** Verify each candidate table is truly unused
3. **Medium-term:** Create and test cleanup migrations
4. **Long-term:** Execute cleanup in phases, monitoring after each phase

## Notes

- Always backup before cleanup
- Test on development/staging first
- Remove tables in phases, not all at once
- Monitor application after each phase
- Keep backups for at least 30 days after cleanup
- Document all cleanup actions

## Related Documentation

- `CONSOLIDATED_TABLES_SUMMARY.md` - Complete list of documented tables
- Individual module analysis files in `docs/db_cleanup/` directory
- Migration files in `database/migrations/` directory

