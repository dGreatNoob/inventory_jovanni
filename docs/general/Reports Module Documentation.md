# Reports Refactor Guide — 2025-12-18

This change replaces the old Product Management analytics dashboard with a dedicated Reports module, adds a comprehensive Product Inventory report, enhances the Purchase Orders report, and updates navigation and routes accordingly.

## Summary
- Replaced Inventory Dashboard with two focused reports: Product Inventory and Purchase Orders.
- Introduced filters, KPIs, trends, and basic exports (CSV stub) for Product Inventory.
- Updated Purchase Orders report to support date, supplier, and status filters, KPIs, trends, and recent orders.
- Cleaned up navigation to reflect the new Reports section.
- Removed the old Product Management “Analytics” dashboard and its route.

## Why
- Provide clearer separation between day-to-day management and analytics/reporting.
- Make reports easier to find, filter, and extend.
- Lay groundwork for future export formats (XLSX/PDF) and additional reporting widgets.

## What Changed

### Removed
- Inventory Dashboard component and view:
  - [app/Livewire/Pages/ProductManagement/InventoryDashboard.php](app/Livewire/Pages/ProductManagement/InventoryDashboard.php)
  - [resources/views/livewire/pages/product-management/inventory-dashboard.blade.php](resources/views/livewire/pages/product-management/inventory-dashboard.blade.php)
- Stock Available simple report (replaced by comprehensive Product Inventory):
  - [app/Livewire/Pages/Reports/StockAvailable.php](app/Livewire/Pages/Reports/StockAvailable.php)

### Added
- Product Inventory report (new Livewire component + view):
  - [app/Livewire/Pages/Reports/ProductInventoryReport.php](app/Livewire/Pages/Reports/ProductInventoryReport.php)
  - [resources/views/livewire/pages/reports/product-inventory.blade.php](resources/views/livewire/pages/reports/product-inventory.blade.php)

### Updated
- Purchase Orders report (filters, KPIs, trends, recent orders):
  - [app/Livewire/Pages/Reports/PurchaseOrders.php](app/Livewire/Pages/Reports/PurchaseOrders.php)
  - [resources/views/livewire/pages/reports/purchase-orders.blade.php](resources/views/livewire/pages/reports/purchase-orders.blade.php)
- Navigation (sidebar and mobile) to surface Reports and remove legacy Analytics items:
  - [resources/views/components/layouts/app/sidebar.blade.php](resources/views/components/layouts/app/sidebar.blade.php)
  - [resources/views/components/mobile-navigation.blade.php](resources/views/components/mobile-navigation.blade.php)
  - [resources/views/components/product-management-nav.blade.php](resources/views/components/product-management-nav.blade.php)
- Routes:
  - [routes/web.php](routes/web.php)
    - Removed `product-management.dashboard` route.
    - Mapped `reports.stock-available` to the new `ProductInventoryReport`.
    - Kept/updated `reports.purchase-orders`.

## New/Changed Routes
- Removed: `product-management.dashboard`
- Reports:
  - `reports.stock-available` → Product Inventory report
  - `reports.purchase-orders` → Enhanced Purchase Orders report

## How To Use

### Product Inventory
- Navigate via Reports → Product Inventory.
- Filters: quick period (7/30/90/365 days), date range, search (SKU/name), category, supplier, location.
- Table columns: toggle visibility; sortable by Product and On-hand.
- KPIs and summaries: totals, category/supplier subtotals, alerts.
- Exports: CSV available; XLSX/PDF are stubs with on-screen notices for now.

### Purchase Orders
- Navigate via Reports → Purchase Orders.
- Filters: date range (order_date), supplier, status (enum-backed).
- KPIs: total POs, pending, to receive, received, total value.
- Trends: 6-month order count by month.
- Supplier insights: top suppliers by order count and value.
- Recent orders: latest 5 with status badges and totals.

## Breaking/Behavior Changes
- The Product Management “Analytics” dashboard is no longer available.
  - If you had bookmarks or code pointing to `product-management.dashboard`, update to the appropriate Reports route.
- The old Stock Available component is removed; use the Product Inventory report instead.

## Developer Notes
- Purchase Orders report now depends on `App\Enums\PurchaseOrderStatus` and assumes `order_date` is set for trend grouping.
- Product Inventory valuation uses `quantity * products.cost`. Future improvement: average cost field if/when available.
- Location subtotals are conditional on a `product_inventory.location_id` column existing.
- No database migrations or third-party packages were added in this change set.

