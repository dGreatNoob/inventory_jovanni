# Module Usage Analysis - Active vs Legacy

**Generated:** 2025-01-XX  
**Purpose:** Determine which additional modules are actively used vs legacy/commented out code.

## Summary

After analyzing routes, sidebar navigation, and database records, here's the status of each module:

## Module Status Breakdown

### ✅ ACTIVELY USED MODULES

#### 1. Supply Profiles & Related Tables
- **Status:** ✅ **ACTIVE**
- **Evidence:**
  - `supply_profiles` table has **15 records**
  - Used in Sales Return module (active route: `/sales-return`)
  - Used in Shipment QR Scanner
  - Used in Stock In/Out modules
  - Used in Reports
- **Tables:** `supply_profiles`, `supply_orders`, `supply_batches`, `item_types`
- **Keep:** ✅ YES

#### 2. Purchase Order Items
- **Status:** ✅ **ACTIVE**
- **Evidence:**
  - `purchase_order_items` table has **9 records**
  - Used in Purchase Order processing
- **Tables:** `purchase_order_items`
- **Keep:** ✅ YES

#### 3. Item Types
- **Status:** ✅ **ACTIVE**
- **Evidence:**
  - `item_types` table has **5 records**
  - Referenced by `supply_profiles` (foreign key)
- **Tables:** `item_types`
- **Keep:** ✅ YES

### ⚠️ LEGACY/COMMENTED OUT MODULES

#### 1. Paper Roll Warehouse (PRW)
- **Status:** ⚠️ **LEGACY - NOT IN SIDEBAR**
- **Evidence:**
  - Routes exist: `/prw/*` (inventory, purchase-order, profile)
  - Components exist and are functional
  - **NOT visible in sidebar navigation**
  - Tables have **0 records**:
    - `raw_mat_profiles`: 0 records
    - `raw_mat_orders`: 0 records
    - `raw_mat_invs`: 0 records
- **Tables:** `raw_mat_profiles`, `raw_mat_orders`, `raw_mat_invs`
- **Recommendation:** ⚠️ **REVIEW** - May be legacy code, but routes are accessible if needed
- **Keep:** ⚠️ **CONDITIONAL** - Keep if PRW module is planned for future use

#### 2. Request Slip
- **Status:** ⚠️ **COMMENTED OUT IN SIDEBAR**
- **Evidence:**
  - Route exists: `/RequestSlip`
  - Component exists: `RequestSlip\Index`
  - **COMMENTED OUT in sidebar** (lines 30-37)
  - Table has **0 records**: `request_slips`
- **Tables:** `request_slips`
- **Recommendation:** ⚠️ **REVIEW** - Route accessible but not in navigation
- **Keep:** ⚠️ **CONDITIONAL** - Keep if Request Slip feature is planned

#### 3. Sales Return
- **Status:** ⚠️ **COMMENTED OUT IN SIDEBAR**
- **Evidence:**
  - Routes exist: `/sales-return`, `/sales-return/{id}`
  - Components exist: `SalesManagement\SalesReturn`, `SalesManagement\ViewSalesReturn`
  - **COMMENTED OUT in sidebar** (lines 363-377)
  - Tables have **0 records**:
    - `sales_returns`: 0 records
    - `sales_return_items`: 0 records
  - **BUT:** Uses `supply_profiles` (which has 15 records)
- **Tables:** `sales_returns`, `sales_return_items`
- **Recommendation:** ⚠️ **REVIEW** - Route accessible but not in navigation
- **Keep:** ⚠️ **CONDITIONAL** - Keep if Sales Return feature is planned

#### 4. Sales Price
- **Status:** ⚠️ **COMMENTED OUT IN SIDEBAR**
- **Evidence:**
  - Route exists: `/sales-price`
  - Component exists: `SalesPrice\Index`
  - **COMMENTED OUT in sidebar** (lines 363-377)
  - Table has **0 records**: `sales_prices`
- **Tables:** `sales_prices`
- **Recommendation:** ⚠️ **REVIEW** - Route accessible but not in navigation
- **Keep:** ⚠️ **CONDITIONAL** - Keep if Sales Price feature is planned

#### 5. Sales Order Branch Items
- **Status:** ⚠️ **POTENTIALLY UNUSED**
- **Evidence:**
  - Table has **0 records**: `sales_order_branch_items`
  - May be used in Sales Order processing (needs verification)
- **Tables:** `sales_order_branch_items`
- **Recommendation:** ⚠️ **REVIEW** - Check if used in Sales Order workflow
- **Keep:** ⚠️ **CONDITIONAL** - Keep if used in Sales Order processing

#### 6. Notifications
- **Status:** ✅ **SYSTEM TABLE**
- **Evidence:**
  - Laravel notifications table
  - Table has **0 records** but is a system table
- **Tables:** `notifications`
- **Recommendation:** ✅ **KEEP** - System table, may be used by Laravel
- **Keep:** ✅ **YES** - System infrastructure

## Detailed Analysis

### Paper Roll Warehouse Module

**Routes:**
- `/prw/inventory` ✅ Exists
- `/prw/purchase-order` ✅ Exists
- `/prw/profile` ✅ Exists

**Sidebar:** ❌ **NOT VISIBLE** - No menu item

**Database:**
- `raw_mat_profiles`: 0 records
- `raw_mat_orders`: 0 records
- `raw_mat_invs`: 0 records

**Conclusion:** ⚠️ **LEGACY CODE** - Routes exist but module is not in navigation. May be:
- Legacy code from previous version
- Planned feature not yet activated
- Internal tool not exposed to users

**Recommendation:** 
- If not planning to use: Can remove tables and code
- If planning to use: Keep tables and activate in sidebar

### Request Slip Module

**Routes:**
- `/RequestSlip` ✅ Exists
- `/RequestSlip/{id}` ✅ Exists

**Sidebar:** ❌ **COMMENTED OUT** (lines 30-37)

**Database:**
- `request_slips`: 0 records

**Conclusion:** ⚠️ **COMMENTED OUT** - Feature exists but disabled in UI

**Recommendation:**
- If not planning to use: Can remove
- If planning to use: Uncomment sidebar and keep tables

### Sales Return Module

**Routes:**
- `/sales-return` ✅ Exists
- `/sales-return/{id}` ✅ Exists

**Sidebar:** ❌ **COMMENTED OUT** (lines 363-377)

**Database:**
- `sales_returns`: 0 records
- `sales_return_items`: 0 records
- Uses `supply_profiles` (15 records) ✅

**Conclusion:** ⚠️ **COMMENTED OUT** - Feature exists but disabled in UI. However, it uses active `supply_profiles` table.

**Recommendation:**
- If not planning to use: Can remove `sales_returns` and `sales_return_items` tables
- If planning to use: Uncomment sidebar and keep tables

### Sales Price Module

**Routes:**
- `/sales-price` ✅ Exists

**Sidebar:** ❌ **COMMENTED OUT** (lines 363-377)

**Database:**
- `sales_prices`: 0 records

**Conclusion:** ⚠️ **COMMENTED OUT** - Feature exists but disabled in UI

**Recommendation:**
- If not planning to use: Can remove
- If planning to use: Uncomment sidebar and keep tables

## Cleanup Recommendations

### Phase 1: Confirm with Stakeholders

Before removing any tables, confirm:
1. **Paper Roll Warehouse** - Is this module needed?
2. **Request Slip** - Is this feature needed?
3. **Sales Return** - Is this feature needed?
4. **Sales Price** - Is this feature needed?

### Phase 2: Safe to Remove (If Confirmed Unused)

If stakeholders confirm these modules are not needed:

#### High Confidence (Can Remove):
1. **`raw_mat_profiles`** - 0 records, not in sidebar
2. **`raw_mat_orders`** - 0 records, not in sidebar
3. **`raw_mat_invs`** - 0 records, not in sidebar
4. **`request_slips`** - 0 records, commented out in sidebar
5. **`sales_prices`** - 0 records, commented out in sidebar

#### Medium Confidence (Review First):
1. **`sales_returns`** - 0 records, commented out, but uses active `supply_profiles`
2. **`sales_return_items`** - 0 records, commented out
3. **`sales_order_branch_items`** - 0 records, may be used in Sales Order workflow

### Phase 3: Must Keep

1. **`supply_profiles`** ✅ - 15 records, actively used
2. **`supply_orders`** ✅ - Used by supply_profiles (foreign key)
3. **`supply_batches`** ✅ - Used by supply_profiles (foreign key)
4. **`item_types`** ✅ - 5 records, used by supply_profiles
5. **`purchase_order_items`** ✅ - 9 records, actively used
6. **`notifications`** ✅ - System table

## Action Items

### Immediate:
1. ✅ **Document findings** - This analysis
2. ⏭️ **Stakeholder confirmation** - Confirm which modules are needed
3. ⏭️ **Decision on legacy modules** - Remove or activate

### If Removing Legacy Modules:
1. Create migration to drop unused tables
2. Remove unused Livewire components
3. Remove unused routes
4. Clean up unused models
5. Update documentation

### If Activating Legacy Modules:
1. Uncomment sidebar navigation
2. Test functionality
3. Add to module documentation
4. Update consolidated tables summary

## Conclusion

**Status of Additional Modules:**
- ✅ **3 modules actively used** (Supply Profiles, Purchase Order Items, Item Types)
- ⚠️ **4 modules legacy/commented out** (PRW, Request Slip, Sales Return, Sales Price)
- ⚠️ **1 module needs review** (Sales Order Branch Items)

**Tables Status:**
- **Must Keep:** 6 tables (have data or are system tables)
- **Can Remove (if confirmed unused):** 5-8 tables (0 records, not in navigation)
- **Needs Review:** 3 tables (may be used in workflows)

**Next Step:** Confirm with stakeholders which legacy modules are needed before cleanup.

