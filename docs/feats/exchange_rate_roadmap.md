# Multi-Currency & Exchange Rate — Implementation Roadmap

**Status:** Phase 1–3 complete · Phase 4–5 pending  
**Last updated:** 2026-02-12

## Overview

Enable multi-currency support for product costs, purchase orders, and stock-in. Exchange rates are set manually in Settings (e.g. CNY → PHP) and may differ from market rates. Base reporting currency is PHP.

---

## Scope

| In scope | Out of scope |
|----------|--------------|
| Product cost in supplier currency | Product selling price (stays PHP) |
| Purchase order creation (CNY, PHP, etc.) | Sales/receivables multi-currency |
| Stock-in conversion to PHP | External FX APIs |
| Manual exchange rates in Settings | Automatic rate updates |
| Finance payables (basic) | Historical rate reporting UI |

---

## Phase 1: Foundation — Done

### 1.1 Settings table and model
- [x] Migration: `2026_02_12_000001_create_settings_table.php`
- [x] Model: `App\Models\Setting` with `get()`, `set()`, `$primaryKey = 'key'`
- [x] Key-value storage for exchange rates

### 1.2 Currencies table
- [x] Migration: `2026_02_12_000002_create_currencies_table.php`
- [x] Model: `App\Models\Currency`
- [x] Seeder: `CurrencySeeder` — PHP (base), CNY

### 1.3 Exchange rate settings page
- [x] Livewire: `App\Livewire\Settings\Currency`
- [x] View: `resources/views/livewire/settings/currency.blade.php`
- [x] Route: `/settings/currency`
- [x] Nav: “Exchange Rates” in Settings layout
- [x] Manual input for CNY → PHP rate

### 1.4 Exchange rate service
- [x] `App\Services\ExchangeRateService`
- [x] `getRate(string $from, string $to): float`
- [x] `convert(float $amount, string $from, string $to): float`
- [x] Reads from Settings (`exchange_rate_cny_to_php`)

---

## Phase 2: Supplier & Purchase Order — Done

### 2.1 Supplier currency
- [x] Migration: `add_default_currency_id_to_suppliers_table`
- [x] Model: `Supplier::defaultCurrency()` relation
- [x] UI: Currency dropdown in Supplier Management (create & edit)
- [x] Location: `app/Livewire/Pages/SupplierManagement/Profile/Index.php`

### 2.2 Purchase order currency
- [x] Migration: `add_currency_id_to_purchase_orders_table`
- [x] Model: `PurchaseOrder::currency()` relation
- [x] Default from supplier `default_currency_id`

### 2.3 ProductOrder currency
- [x] Migration: `add_currency_id_to_product_orders_table`
- [x] Model: `ProductOrder::currency()` relation

### 2.4 PO Create/Edit UI
- [x] Currency display from supplier on PO Create
- [x] Dynamic symbol (₱/¥) for unit price, total price, grand total
- [x] Edit panel shows PO/supplier currency
- [x] Backfill: `2026_02_12_000006_backfill_currency_id_on_purchase_orders` (existing POs → PHP)

---

## Phase 3: Stock-in & Cost Recording — Done

### 3.1 InventoryMovement
- [x] Migration: add `currency_id`, `exchange_rate_applied`, `unit_cost_original` (nullable)
- [x] Logic: `unit_cost`, `total_cost` stored in PHP; optional original amount for audit

### 3.2 Stock-in flow
- [x] On receive: read PO line `unit_price` and PO `currency_id`
- [x] Use `ExchangeRateService::getRate(po_currency, 'PHP')` from Settings
- [x] Create movement with converted PHP cost, `exchange_rate_applied`, `currency_id`
- [x] Update stock-in Livewire: `app/Livewire/Pages/Warehousestaff/StockIn/Index.php` (or Receive)

### 3.3 Product cost
- [x] Policy: update `Product.cost` on receive using **weighted average** (PHP). Formula: `new_cost = (old_qty * old_cost + received_qty * unit_cost_php) / (old_qty + received_qty)`. Only applied when good items are added to inventory.
- [x] Document policy

---

## Phase 4: Product Master (Optional) — Pending

### 4.1 Product cost currency
- [ ] Migration: add `cost_currency_id` (nullable) to products
- [ ] Default from supplier; allow override on product form
- [ ] Optional: `cost_original` for display in supplier currency

---

## Phase 5: Finance & Display — Pending

### 5.1 Finance
- [ ] Migration: add `currency_id` to `finances` (nullable, default PHP)
- [ ] Payables in PO currency; convert for totals via `ExchangeRateService`

### 5.2 Format helper
- [ ] Blade helper or component: `@formatMoney($amount, $currencyCode)`
- [ ] Replace hardcoded ₱ where amount + currency known

---

## File Reference

| Phase | Status | File |
|-------|--------|------|
| 1.1 | Done | `database/migrations/2026_02_12_000001_create_settings_table.php` |
| 1.1 | Done | `app/Models/Setting.php` |
| 1.2 | Done | `database/migrations/2026_02_12_000002_create_currencies_table.php` |
| 1.2 | Done | `app/Models/Currency.php` |
| 1.2 | Done | `database/seeders/CurrencySeeder.php` |
| 1.3 | Done | `app/Livewire/Settings/Currency.php` |
| 1.3 | Done | `resources/views/livewire/settings/currency.blade.php` |
| 1.3 | Done | `routes/web.php` (settings.currency) |
| 1.3 | Done | `resources/views/components/settings/layout.blade.php` |
| 1.4 | Done | `app/Services/ExchangeRateService.php` |
| 2.1 | Done | `database/migrations/2026_02_12_000003_add_default_currency_id_to_suppliers_table.php` |
| 2.1 | Done | `app/Models/Supplier.php` |
| 2.1 | Done | `app/Livewire/Pages/SupplierManagement/Profile/Index.php` |
| 2.1 | Done | `resources/views/livewire/pages/supplier-management/profile/index.blade.php` |
| 2.2 | Done | `database/migrations/2026_02_12_000004_add_currency_id_to_purchase_orders_table.php` |
| 2.2 | Done | `app/Models/PurchaseOrder.php` |
| 2.3 | Done | `database/migrations/2026_02_12_000005_add_currency_id_to_product_orders_table.php` |
| 2.3 | Done | `app/Models/ProductOrder.php` |
| 2.4 | Done | PO Create/Edit Livewire + views |
| 2.4 | Done | `database/migrations/2026_02_12_000006_backfill_currency_id_on_purchase_orders.php` |
| 3.1 | Done | `database/migrations/2026_02_12_000007_add_currency_fields_to_inventory_movements_table.php`, `app/Models/InventoryMovement.php` |
| 3.2 | Done | Stock-in Livewire: `app/Livewire/Pages/Warehousestaff/StockIn/Index.php` (receive + movement + product cost) |
| 4.1 | Pending | Migration: add `cost_currency_id` to products (optional) |
| 5.1 | Pending | Migration: add `currency_id` to finances |
| 5.2 | Pending | `FormatMoney` helper or Blade component |

---

## Migration Strategy

- Existing POs/product orders: backfilled with PHP (`currency_id` = base).
- Settings: no default exchange rate; user sets via Settings → Exchange Rates.
- Legacy data: all amounts remain PHP; no conversion applied.

---

## Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| Rate changes mid-flow | Use rate at PO creation or receipt; store `exchange_rate_applied` on movements |
| Rounding differences | Standardize decimal places and rounding rules |
| Mixed currency in one PO | One currency per PO; split into multiple POs if needed |
| Legacy data | Default to PHP; no conversion applied |

---

## Success Criteria

- [x] Admin can set CNY → PHP rate in Settings
- [x] Supplier can have default currency (PHP or CNY)
- [x] PO created in supplier’s default currency
- [x] PO Create/Edit shows correct currency symbol
- [x] Stock-in converts PO amounts to PHP using configured rate
- [x] Inventory movements store cost in PHP with rate used
