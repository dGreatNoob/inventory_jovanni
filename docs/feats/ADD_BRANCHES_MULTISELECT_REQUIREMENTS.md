# Add Branches Multi-Select — Gmail-Style Requirements

## Overview

This document defines the requirements for the **Add Branches** multi-select in the Branch Management > Batch Management workflow. The goal is to align behavior with industry-standard list multi-selection patterns (e.g., Gmail, Google Drive) so users can select multiple branches predictably and efficiently.

**Location:** Branch Management → Batch Management → Add branches (modal)

**Related files:**
- `app/Livewire/Pages/Branch/Index.php`
- `resources/views/livewire/pages/branch/index.blade.php` (Add Branches modal section)

---

## Current Behavior (Baseline)

| Interaction      | Current behavior                                                                 |
|------------------|-----------------------------------------------------------------------------------|
| Plain click      | If row selected → remove it. If row not selected → **deselect all** (does not add clicked row) |
| Ctrl/Cmd+click   | Toggle single item ✓                                                              |
| Shift+click      | Range select ✓                                                                    |
| Click outside    | Deselect all ✓                                                                    |
| Checkbox         | Non-interactive (`pointer-events-none`), visual only                               |
| Select all       | Not available                                                                     |
| Keyboard         | None                                                                              |

**Main problem:** Plain click on an unselected row clears the selection instead of selecting that row. Users must use Ctrl+click to build a selection, which is non-obvious and not aligned with Gmail-style UX.

---

## Target Behavior (Gmail-Style)

### Reference: Gmail Multi-Select

- **Plain click** on row or checkbox → toggle that row (select if unselected, deselect if selected)
- **Shift+click** → select contiguous range from anchor to clicked row
- **Ctrl/Cmd+click** → toggle single row without affecting others
- **Select all** → select all visible/filtered items
- **Checkbox** → clickable, same behavior as row click
- **Keyboard** → arrow keys + Space to navigate and toggle (optional enhancement)

---

## Functional Requirements

### FR-1: Plain Click (Primary Fix)

| ID     | Requirement                                                                 | Priority |
|--------|-----------------------------------------------------------------------------|----------|
| FR-1.1 | Plain click on a row MUST toggle that row: select if unselected, deselect if selected | Must     |
| FR-1.2 | Plain click MUST NOT deselect other rows when the clicked row was unselected | Must     |
| FR-1.3 | Plain click MAY deselect all only when clicking on empty/container area (click outside rows) | Should   |

### FR-2: Checkbox Interaction

| ID     | Requirement                                                                 | Priority |
|--------|-----------------------------------------------------------------------------|----------|
| FR-2.1 | Checkbox MUST be clickable (remove `pointer-events-none`)                    | Must     |
| FR-2.2 | Checkbox click MUST behave identically to row click (toggle selection)       | Must     |
| FR-2.3 | Checkbox MUST reflect selected state visually (checked when selected)        | Must     |

### FR-3: Shift+Click Range Select

| ID     | Requirement                                                                 | Priority |
|--------|-----------------------------------------------------------------------------|----------|
| FR-3.1 | Shift+click MUST select contiguous range from anchor to clicked row         | Must     |
| FR-3.2 | Anchor = last row that was clicked (plain or Ctrl+click)                     | Must     |
| FR-3.3 | First Shift+click with no prior anchor SHOULD use first visible row or clicked row as one end of range | Should   |
| FR-3.4 | Shift+click SHOULD extend selection (add to existing) when clicking outside current range | Should   |

### FR-4: Ctrl/Cmd+Click

| ID     | Requirement                                                                 | Priority |
|--------|-----------------------------------------------------------------------------|----------|
| FR-4.1 | Ctrl/Cmd+click MUST toggle single row without affecting other selections     | Must     |
| FR-4.2 | Ctrl/Cmd+click MUST update anchor for subsequent Shift+click                 | Should   |

### FR-5: Select All

| ID     | Requirement                                                                 | Priority |
|--------|-----------------------------------------------------------------------------|----------|
| FR-5.1 | "Select all" MUST appear when there are candidates and not all are selected | Should   |
| FR-5.2 | "Select all" MUST select all visible/filtered candidates                     | Should   |
| FR-5.3 | When all are selected, label MAY change to "Deselect all" and clear selection | Should   |
| FR-5.4 | Select all scope = current filtered list (addBranchesCandidates)             | Must     |

### FR-6: Click Outside / Deselect All

| ID     | Requirement                                                                 | Priority |
|--------|-----------------------------------------------------------------------------|----------|
| FR-6.1 | Click on modal backdrop/container (not on a row) MUST deselect all           | Must     |
| FR-6.2 | Optional: ESC key to close modal or deselect all (per existing modal UX)     | Could    |

### FR-7: Visual Feedback

| ID     | Requirement                                                                 | Priority |
|--------|-----------------------------------------------------------------------------|----------|
| FR-7.1 | Selected rows MUST have distinct background (e.g., indigo tint)              | Must     |
| FR-7.2 | Hover state MUST be visible on rows                                         | Must     |
| FR-7.3 | Selection count MUST be shown in CTA (e.g., "Add N branch(es)")              | Must     |

### FR-8: Keyboard Support (Optional Enhancement)

| ID     | Requirement                                                                 | Priority |
|--------|-----------------------------------------------------------------------------|----------|
| FR-8.1 | Tab/Arrow keys to move focus between rows                                   | Could    |
| FR-8.2 | Space to toggle selection on focused row                                    | Could    |
| FR-8.3 | Shift+Space for range select from anchor to focused row                     | Could    |

---

## Interaction Matrix

| User Action        | Expected Result                                          |
|--------------------|----------------------------------------------------------|
| Click row          | Toggle that row (select/deselect)                        |
| Click checkbox     | Same as click row                                        |
| Shift+click row    | Range select from anchor to clicked row                  |
| Ctrl+click row     | Toggle that row only                                     |
| Click container    | Deselect all                                             |
| Select all link    | Select all visible candidates                            |
| Deselect all link  | Clear selection (when all selected)                      |

---

## Edge Cases

| Case                       | Expected behavior                                         |
|----------------------------|-----------------------------------------------------------|
| First click (no selection) | Select that row, set anchor                               |
| Search filters candidates  | Select all = only filtered candidates; selection persists within filtered set |
| Empty search results       | No rows, no select all                                    |
| Single candidate           | Plain click toggles; select all selects that one          |
| All selected + click one   | Deselect that one (toggle)                                |

---

## Technical Notes

### Backend (Livewire)

- `handlePlainBranchClick(int $branchId)` — **Change:** Toggle row instead of "deselect all when unselected".
- `toggleBranchSelection(int $branchId)` — Keep as-is for Ctrl+click.
- `selectBranchRange(int $fromIndex, int $toIndex)` — Keep; verify anchor/index logic.
- `selectAllBranches()` — **Add:** Select all IDs from `addBranchesCandidates`.
- `deselectAllBranches()` — Keep.

### Frontend (Blade/Alpine)

- Plain click handler: call toggle (same as Ctrl+click) instead of `handlePlainBranchClick` with current behavior.
- Remove `pointer-events-none` from checkbox; ensure checkbox click does not double-fire with label click.
- Add "Select all" / "Deselect all" link above the list.
- `lastClickedIndex` (anchor) for Shift+click: ensure it updates on plain click and Ctrl+click.

### Idempotency

- Re-running the modal (open, select, cancel, reopen) MUST reset selection and anchor.
- Search change MAY keep selection for IDs still in filtered list, or MAY clear (product decision).

---

## Acceptance Criteria

- [ ] Plain click on unselected row adds it to selection.
- [ ] Plain click on selected row removes it from selection.
- [ ] Checkbox is clickable and toggles selection.
- [ ] Shift+click selects range from anchor to clicked row.
- [ ] Ctrl+click toggles single row.
- [ ] Click outside rows deselects all.
- [ ] "Select all" selects all visible candidates.
- [ ] "Deselect all" (or equivalent) clears selection when appropriate.
- [ ] Selection count in CTA reflects current selection.
- [ ] No regression: existing Shift/Ctrl behavior remains correct.

---

## References

- Gmail inbox multi-select
- Google Drive file picker
- WAI-ARIA: [Listbox with multi-select](https://www.w3.org/WAI/ARIA/apg/patterns/listbox/)
- Current implementation: `Branch/Index.php`, `branch/index.blade.php` (Add Branches modal)
