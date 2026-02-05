---
name: Allocation Stock Source Design
overview: Senior ERP design analysis comparing pre-receipt allocation (stock + PO expected) vs. manual stock-in with PO reference. Recommends the manual/expected-inventory approach for reporting clarity and single source of truth.
todos: []
isProject: false
---

# Allocation Stock Source: ERP Design Analysis

## The Two Approaches

### Approach A: Pre-Receipt Allocation (Current)

- **Available to allocate** = `ProductInventory.available_quantity` + `ProductOrder.remaining` (from linked PO)
- Allocation can exceed physical warehouse stock
- Two sources of "available": ProductInventory and ProductOrder

### Approach B: Manual Stock-In with PO Reference (Your Idea)

- Add expected quantity into the inventory ledger (or a dedicated "expected" bucket) with a PO reference
- Allocation always draws from warehouse/inventory ledger
- Single source of truth for available quantity

---

## Design Assessment

### Approach A: Drawbacks


| Issue                     | Impact                                                                                                                                          |
| ------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------- |
| Dual source of truth      | Reports must combine ProductInventory + ProductOrder. Queries are more complex and easier to get wrong.                                         |
| Over-commitment risk      | If PO is delayed/cancelled, allocations exceed actual stock. No clean way to "release" committed quantity.                                      |
| Reconciliation complexity | "What can we fulfill today?" vs "What have we allocated?" need different formulas. Inventory reports vs allocation reports use different logic. |
| Audit trail               | Allocation does not record which PO the quantity was sourced from. Hard to trace back when stock does not arrive.                               |


### Approach B: Advantages


| Benefit                | Impact                                                                                                  |
| ---------------------- | ------------------------------------------------------------------------------------------------------- |
| Single source of truth | All "available" logic lives in the inventory ledger. Reports and allocation use the same data.          |
| Clear audit trail      | Expected quantity tied to PO. When real receipt happens, expected is reduced and physical is increased. |
| Standard ERP pattern   | Matches "on order" / "in transit" / "expected receipts" in many ERPs.                                   |
| Simpler reporting      | Inventory report = sum of ledger buckets. Allocation = reads from same ledger.                          |


---

## Recommended Model: Expected Inventory Ledger

Use an **expected inventory** bucket linked to PO, instead of computing from ProductOrder on the fly.

```mermaid
erDiagram
    ProductInventory ||--o{ ProductInventoryExpected : "has expected"
    ProductInventoryExpected }o--|| PurchaseOrder : "from PO"
    
    ProductInventory {
        product_id PK
        quantity "physical"
        available_quantity "physical - reserved"
    }
    
    ProductInventoryExpected {
        product_id PK
        purchase_order_id PK
        expected_quantity int
        received_quantity int "filled on real stock-in"
    }
```



**Available to allocate** = `ProductInventory.available_quantity` + `SUM(ProductInventoryExpected.expected_quantity - received_quantity)` for the product (optionally filtered by linked PO).

**Manual stock-in with PO reference** = Create/update `ProductInventoryExpected` for a product + PO with `expected_quantity`. User enters "We expect X from PO-YYY."

**Real stock-in** = When goods arrive via normal Stock-In:

1. Increment `ProductInventory.quantity`
2. Increment `ProductInventoryExpected.received_quantity` for that product + PO
3. Optionally mark PO/ProductOrder as received as today

No double-counting: expected is reduced as physical is increased.

---

## Alternative: Simpler "Expected" on ProductInventory

If you want minimal schema changes, add to `product_inventory`:

- `expected_quantity` (decimal, default 0)
- `expected_from_po_id` (nullable FK to purchase_orders)

**Manual stock-in**: User selects PO, product, quantity. Set `expected_quantity` and `expected_from_po_id`.

**Available** = `quantity` + `expected_quantity` (or only when `expected_from_po_id` matches batch's linked PO).

**Real stock-in**: Increment `quantity`, decrement `expected_quantity` by received amount, clear `expected_from_po_id` when fully received.

Caveat: Only one PO per product can be tracked this way. For multiple POs per product, use the separate `product_inventory_expected` table.

---

## Comparison Summary


| Aspect            | Pre-Receipt (Current)          | Manual Stock-In / Expected Ledger                |
| ----------------- | ------------------------------ | ------------------------------------------------ |
| Source of truth   | Two (Inventory + ProductOrder) | One (Inventory ledger)                           |
| Reporting         | More complex                   | Simpler                                          |
| PO traceability   | Weak                           | Strong (expected linked to PO)                   |
| Implementation    | Already done                   | New table + manual stock-in UI                   |
| Double-count risk | None                           | None if real stock-in updates expected correctly |


---

## Recommendation

From an ERP design perspective, **Approach B (manual stock-in with PO reference)** is the better long-term design because:

1. **Single source of truth** – allocation and reports read from the same inventory model.
2. **Clear audit trail** – expected quantity is tied to PO and reconciled on receipt.
3. **Simpler mental model** – "what’s in the warehouse" includes both physical and expected.

**Migration path**:

1. Introduce `product_inventory_expected` (or add expected fields to ProductInventory).
2. Add a "Manual Stock-In (Expected)" UI: select PO, product, quantity; creates/updates expected record.
3. Change allocation availability to: `stock + expected` from this ledger (instead of ProductOrder).
4. Adjust Stock-In flow to decrement expected and increment physical when receiving.
5. Phase out the current "available = stock + ProductOrder.remaining" logic.

The current pre-receipt approach works and is common in distribution, but the manual stock-in model will scale better and keep reporting and allocations consistent over time.