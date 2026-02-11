# PO Create Page Layout Refactor — Align with Reference (Detail-Style)

**Scope:** `po-management/purchase-order/create` — Purchase Order create page  
**Target files:**
- `resources/views/livewire/pages/POmanagement/purchase-order/create.blade.php`
- `app/Livewire/Pages/POManagement/PurchaseOrder/Create.php` (only if new computed props for sidebar are needed)

**Goals:**
- Match the reference layout: main content (left) + sticky sidebar (right) with Order Summary and Item Breakdown
- Use the same visual language as the PO detail reference: status-style header card, 2×3 PO info grid, items table, summary cards
- Improve hierarchy and scannability: totals and primary action always visible in sidebar

---

## Reference Layout (Target)

- **Two-column:** Main content (left, ~2/3) + fixed/sticky sidebar (right, ~1/3)
- **Left:** Status card (icon + title + short description) → PO info as **2×3 grid of input-style fields** (read-only look: PO Number, Ordered By, Approved By, Supplier, Receiving Allocation, Loaded Date) → **Order Items** table (PRODUCT, ORDERED QTY, EXPECTED QTY, RECEIVED QTY, STATUS) with colored chips
- **Right:** **Order Summary** card (Total Items, Total Ordered/Expected/Received, Variance, QR) → **Item Breakdown** card (line items: qty × unit price = line total; then **Total Cost** prominent) → primary action (e.g. “Close PO”)
- **Visual:** Dark theme, card-based, clear hierarchy, section icons; no collapsibles

---

## Current Create Page (Gaps)

| Aspect | Reference | Current create |
|--------|-----------|----------------|
| Layout | Main + sidebar | Single column |
| Hierarchy | Status card → PO grid → items; summary + action in sidebar | Two collapsible cards; totals/actions at bottom |
| PO details | 2×3 grid, input-style (read-only look) | Editable form in 2-col grid |
| Items table | PRODUCT, ORDERED/EXPECTED/RECEIVED QTY chips, STATUS | Product ID, SKU, Name, Supplier Code, Unit Price, Qty, Total, Action |
| Summary | Dedicated Order Summary + Item Breakdown cards | Totals in table footer + inline near buttons |
| Primary action | Single CTA in sidebar | Submit + Cancel at bottom of page |
| Visuals | Cards, icons, dark surfaces | Collapsible cards, standard inputs |

---

## Refactor Plan

### 1. Main + sidebar layout

- **Left (~2/3):** All editable content (header card, PO form, items table)
- **Right (~1/3):** Sticky sidebar with Order Summary, Item Breakdown, and primary action
- Use e.g. `grid grid-cols-1 lg:grid-cols-3` with main in `lg:col-span-2`, sidebar in `lg:col-span-1`; sidebar `sticky top-*` so it stays in view

**Deliverable:** Create page uses two-column layout; sidebar does not scroll away with main content.

---

### 2. “New Purchase Order” header card (replace status card)

- Single card at top left: icon (e.g. document/edit), title **“New Purchase Order”**, short line **“Create a new purchase order and add line items.”**
- No “Status History” on create
- Same card styling as reference (background, border, padding; not collapsible)

**Deliverable:** Header card matches reference weight and position, copy appropriate for create.

---

### 3. PO details as 2×3 input-style grid

- Same (or subset of) fields as reference: **PO Number** (readonly, “Auto-generated on save”), **Ordered By** (readonly), **Approved By** → “—” or hide on create, **Supplier** (searchable), **Receiving Allocation** (current “Receiving Department” dropdown), **Order Date** (and **Expected Delivery** in same grid or below)
- Style as **label above value**, input-like boxes (e.g. dark `bg` + border) so it matches reference; keep Supplier, Receiving Allocation, and dates editable
- Currency can remain as a small readonly row or in grid for parity

**Deliverable:** PO details section looks like reference grid; create only differs by which fields are editable.

---

### 4. Order Items table aligned with reference

- **Columns:** PRODUCT (name + product number/SKU below), ORDERED QTY (single chip/box on create), Unit Price, Line Total, Action (Remove). Omit Expected/Received/Status for create
- Reuse reference table styling (header, row style, colored box for qty)
- Keep “Add Item” prominent (e.g. top-right of table section); keep existing Add Item modal

**Deliverable:** Table matches reference “language”; create shows subset of columns.

---

### 5. Right sidebar: Order Summary + Item Breakdown + action

- **Order Summary card:**
  - Total Items (count)
  - Total Ordered (quantity in units/pcs)
  - Total Cost (currency)
  - Omit Expected/Received/Variance and QR on create (or show QR only after save if draft PO numbers exist)
- **Item Breakdown card:**
  - One line per item: product name/ID, “qty × unit price = line total”
  - Bottom: **Total Cost: ₱X,XXX.XX** (large/bold), same as reference
- **Primary action:** Single button **“Submit Purchase Order”** (and optional “Cancel” as secondary or link)
- Sidebar **sticky** so totals and submit stay visible while scrolling

**Deliverable:** Sidebar mirrors reference structure; create shows only relevant summary fields and Item Breakdown.

---

### 6. Visual and component alignment

- Use same card, typography, and spacing as reference (and as show page if show is refactored to this layout)
- Prefer **non-collapsible** sections on create so structure matches reference
- Reuse or mirror reference dark theme classes (e.g. `dark:bg-zinc-800`, borders, text contrast)

**Deliverable:** Create and reference (and future show) feel like one system.

---

### 7. (Optional) Shared partials for create and show

- If show is later refactored to the same two-column + Order Summary + Item Breakdown layout:
  - Shared “PO info grid” partial (create vs show passes editable vs readonly by context)
  - Shared “Order Summary” / “Item Breakdown” partial that accepts totals and line items (create passes draft items; show passes saved PO)

**Deliverable:** One layout system for create and show; less duplication.

---

## Suggested order of implementation

| Order | Item | Rationale |
|-------|------|------------|
| 1 | Main + sidebar grid layout | Foundation for all other changes |
| 2 | Header card (“New Purchase Order”) | Establishes top-left hierarchy |
| 3 | PO details 2×3 grid | Same structure as reference |
| 4 | Order Items table (columns + styling) | Align with reference table |
| 5 | Sidebar: Order Summary + Item Breakdown + Submit | Totals and CTA always visible |
| 6 | Sticky sidebar + visual polish | Match reference theme and behavior |
| 7 | (Optional) Shared partials | If show is refactored to same layout |

---

## Risks & mitigations

- **Form wiring:** Moving fields into a grid must not break Livewire bindings (e.g. `wire:model`, supplier search, dropdowns). Keep same `name`/`wire:model` and validation rules.
- **Totals source:** Sidebar Order Summary and Item Breakdown must use same source as current totals (e.g. `$this->totalAmount`, `$this->totalQuantity`, `$orderedItems`) so values stay correct.
- **Empty state:** When no items, Order Summary and Item Breakdown should show zeros or “No items” and Submit disabled, consistent with current behavior.
- **Responsive:** On small screens, sidebar can stack below main content; ensure totals and Submit remain accessible without horizontal scroll.

---

## File checklist (for implementation)

- [ ] `resources/views/livewire/pages/POmanagement/purchase-order/create.blade.php` — two-column layout, header card, PO grid, items table, sidebar (Order Summary, Item Breakdown, Submit/Cancel)
- [ ] `app/Livewire/Pages/POManagement/PurchaseOrder/Create.php` — only if new computed properties are needed for sidebar (e.g. formatted line items for Item Breakdown)
- [ ] (Optional) New partials under `partials/` for PO grid, Order Summary, or Item Breakdown if shared with show later
