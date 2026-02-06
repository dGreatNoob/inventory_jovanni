---
name: po-lifecycle-design-and-change-plan
overview: Review and refine the Purchase Order lifecycle for this ERP, aligning it with the client’s requirements and existing allocation behavior, and propose concrete changes to statuses, data model, and UI flows in this codebase.
todos:
  - id: normalize-po-statuses
    content: Normalize PurchaseOrder statuses (open, closed, fulfilled, cancelled) and add helper methods for isOpen/isAllocatable/canEdit/etc.
    status: completed
  - id: align-create-edit-with-statuses
    content: Update PO create/edit Livewire components so Save & Create results in an open (auto-approved) PO and wire up manual open/close actions in the UI.
    status: completed
  - id: design-placeholder-line-support
    content: Design and implement minimal schema and model changes to support PO lines with partially defined products (only SKU/supplier code known at creation).
    status: completed
  - id: update-allocation-to-respect-po-status
    content: Update allocation logic to use new PurchaseOrder isAllocatable rules and to treat closed/cancelled/fulfilled POs as non-allocatable.
    status: completed
  - id: verify-receiving-updates-and-status
    content: Review and adjust receiving flows so they correctly update line received quantities, move PO to fulfilled when appropriate, and update inventory and allocations.
    status: completed
isProject: false
---

## Purchase Order Lifecycle Review & Change Plan

### 1. Target Lifecycle (Conceptual)

- **Supplier Management**
  - Users create and maintain suppliers before or during PO creation.
  - Supplier record must exist (or be created inline) to associate with a PO.
- **PO Creation**
  - User chooses a supplier and basic PO header data (order date, expected delivery, deliver-to, etc.).
  - Products can be:
    - **Fully specified** items (normal case), or
    - **Partially specified placeholders** where only SKU / supplier code / product number is known and full product master data will be completed later.
  - System must allow saving a PO even if some lines reference incomplete products (subject to validation rules that they’re still uniquely identifiable).
- **PO Details & Statuses**
  - Core PO statuses:
    - `draft` – optional, for POs being composed but not yet approved or allocatable.
    - `open` – active PO, auto-approved in this client’s flow once created; can be allocated and edited.
    - `closed` – manually closed by a user, no more edits or allocations allowed unless reopened.
    - `fulfilled` – all quantities received; auto or manually set, but still logically closed.
    - `cancelled` – completely voided PO, not allocatable.
  - For this client, **Save & Create** should move immediately to an **open/auto-approved** state so allocation can proceed even before receiving.
- **Allocation before Receiving**
  - Allocation logic can reserve incoming quantities from `open` POs even when they’re not yet received, as long as:
    - The PO is not `closed`, `fulfilled`, or `cancelled`.
    - The allocated quantity per line does not exceed ordered minus already allocated.
  - Allocation views should clearly distinguish **on-hand** vs **incoming (from POs)** stock, and be resilient to placeholder lines whose product details are finalized later.
- **Editing & Manual Open/Close**
  - Users can **edit an open PO**:
    - Add/remove lines.
    - Adjust quantities and dates.
  - Users can **manually close or reopen** an open PO:
    - Close: status → `closed`, allocations should stop using this PO for new reservations.
    - Reopen: status → `open` (if not fulfilled/cancelled) to allow further edits and allocations.
- **Receiving & Fulfilment**
  - Receiving process records actual received quantities per PO line.
  - For each line, track:
    - Ordered quantity.
    - Received quantity.
    - Remaining quantity.
  - PO-level status updates:
    - If all lines fully received → `fulfilled` (and effectively closed).
    - Partial reception → remains `open` (or `to_receive`), but still editable subject to business rules.
  - Inventory and allocation integration:
    - On receive, **move quantities from incoming to on-hand** and update any allocations that were pegged to that PO.
- **Lifecycle End**
  - PO ends in `fulfilled`, `closed`, or `cancelled`.
  - All downstream allocations and inventory movements reference a stable, auditable PO history.

---

### 2. Gaps & Constraints in Current Implementation (High-Level)

- **Status model**
  - Current PO status values include `pending`, `approved`, `to_receive`, `received`, `cancelled` (from PO index/show flows), but `Create` hardcodes `status => 'pending'` and then uses other flows to evolve this.
  - Manual open/close toggling is not clearly modeled or surfaced as a first-class concept; `received` and `to_receive` act as more automatic states.
- **Product placeholders**
  - The current PO create flow expects fully defined products in the product master and does not support placeholder lines that will be backfilled later; every line is tied to a real `products.id`.
- **Allocation integration**
  - Allocation code already has a sophisticated product-number search and can reserve against POs, but the PO side isn’t explicitly designed around an `open` vs `closed` distinction for allocation eligibility.
- **UI consistency**
  - The create/edit screens have grown more capable but need alignment around statuses, open/close actions, and visibility of incoming vs on-hand when editing or reviewing POs.

---

### 3. Proposed Changes for This Codebase

#### 3.1 Status & Lifecycle Modeling

- **Introduce or standardize status values** in `PurchaseOrder` model and database, mapping them to the conceptual lifecycle:
  - `pending` → use as a short-lived state for newly created but not yet auto-approved POs (optional; may be skipped for this client).
  - `open` → default after Save & Create; eligible for allocation and editing.
  - `closed` → manually set; no further editing/allocation.
  - `fulfilled` → all lines fully received; treated as closed.
  - `cancelled` → void; not allocatable or editable.
- **Map existing statuses**:
  - Where UI currently uses `approved`, `to_receive`, `received` etc., map them to the above or maintain them as a secondary status dimension (e.g., `receiving_status`) while using `open/closed/cancelled` as lifecycle flags.
- **Add helper methods** on `PurchaseOrder`:
  - `isOpen()`, `isAllocatable()`, `canEdit()`, `canReceive()`, `canClose()`, etc. to centralize business rules.

#### 3.2 Save & Create Behavior

- In `[app/Livewire/Pages/POManagement/PurchaseOrder/Create.php](app/Livewire/Pages/POManagement/PurchaseOrder/Create.php)`:
  - After `submit()`, create POs directly in `open` (auto-approved) status.
  - If a `draft` state is desired, support a separate **Save as Draft** action, but keep **Save & Create** as auto-approved/open.

#### 3.3 Support for Partially Defined Products

- Introduce a strategy for placeholder lines where only identifiers (e.g. SKU / supplier code / product_number) are known:
  - **Option A – Placeholder products table** (more robust, but heavier change):
    - Create a `po_line_placeholders` or extend `product_orders` to store raw SKU/supplier_code/name fields even if `product_id` is null.
    - Later, when a real `Product` is created or matched, backfill `product_id` and migrate quantities.
  - **Option B – Relax product constraint on PO line** (lighter change):
    - Allow `product_id` to point to a generic placeholder `Product` record per supplier/code, while storing raw SKU/code as authoritative for that PO.
    - Maintain additional fields `placeholder_name`, `placeholder_sku`, etc., on `product_orders` for audit.
  - For an initial, incremental step, favor **Option B** with minimal schema changes, then iterate if needed.

#### 3.4 Manual Open/Close Actions

- Add explicit **Open/Close controls** in PO show/edit views:
  - Buttons: `Close PO`, `Reopen PO`, visible based on `canClose()` / `canReopen()`.
  - Implement corresponding Livewire actions to toggle status.
- Ensure that when a PO is `closed` or `cancelled`:
  - Edit actions (add items, change quantities) are disabled.
  - Allocation modules treat it as **not allocatable**.

#### 3.5 Allocation Integration Rules

- Align allocation code to check `PurchaseOrder::isAllocatable()` instead of raw statuses.
- When receiving moves quantities from incoming to on-hand:
  - Decrease any outstanding PO-based allocations where appropriate, or mark them as fulfilled-from-PO.
  - Ensure that allocation UI surfaces incoming-from-PO quantities distinctly.

#### 3.6 Receiving Flow

- Confirm that receiving screens update per-line received quantities and PO-level status:
  - After each receiving event, recompute whether the PO is fully fulfilled.
  - If fully received and still `open`, move to `fulfilled` and treat as closed.

---

### 4. Implementation Roadmap (High-Level)

1. **Model & status refactor**
  - Update `PurchaseOrder` status constants / accessors to include `open`, `closed`, `fulfilled`, `cancelled`.
  - Migrate existing data if necessary and adjust badge/status rendering in PO index and show views.
2. **Create/Edit flow alignment**
  - Adjust `submit()` in PO create/edit Livewire components to set the correct status (open) and use helper methods.
  - Add manual Open/Close actions and enforce them in the UI (disable editing where appropriate).
3. **Placeholder line support design**
  - Decide between placeholder-record vs relaxed-product approach.
  - Design minimal schema and Livewire changes to allow adding lines with partial data while still uniquely identifying items later.
4. **Allocation checks**
  - Update allocation logic to respect new `isAllocatable()` rules for POs and lines.
  - Ensure pre-existing allocation UX from the allocation module plan still works with these rules.
5. **Receiving and inventory updates**
  - Confirm and, if needed, adjust receiving workflows to:
    - Update line-level received quantities.
    - Drive PO status (`open` → `fulfilled`).
    - Update inventory and reconcile allocations.
6. **UX polish**
  - Ensure PO list, create, edit, show, and allocation screens consistently surface:
    - PO status (open/closed/fulfilled/cancelled).
    - Whether lines are fully specified or still placeholders.
    - Incoming vs on-hand quantities where relevant.

This plan can be executed incrementally, starting with status normalization and Save & Create auto-approval, then layering in placeholder product support and tighter allocation/receiving integration.