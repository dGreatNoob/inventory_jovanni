# Product Supplier Code Field Rename Plan

## Problem Statement

In the Product Create/Edit form, there's a field labeled "Supplier Code" that is currently:
- Auto-filled from the supplier's `code` field (which is the supplier's abbreviation/initials in Supplier Management)
- Read-only
- Confusing because it's not actually the supplier's code, but should be the supplier's barcode/SKU for that specific product

## Current State

### Supplier Management
- `suppliers.code` = Supplier abbreviation/initials (e.g., "ABC" for "ABC Company")
- This is the supplier's identifier code

### Product Management
- `products.supplier_code` = Currently auto-filled from `supplier.code`
- Field label: "Supplier Code"
- Placeholder: "Auto-filled from supplier"
- Read-only field
- Auto-fill logic in `updatedFormSupplierId()` method (line 204-212 in `Index.php`)

## Proposed Solution

### 1. Rename Field Label
**From:** "Supplier Code"  
**To:** "Supplier SKU" or "Supplier Barcode" or "Supplier Product Code"

**Recommendation:** "Supplier SKU" (most common term in inventory systems)

### 2. Remove Auto-fill Logic
- Remove the auto-fill code in `updatedFormSupplierId()` method
- Clear the field when supplier changes (don't auto-populate)

### 3. Make Field Editable
- Remove `readonly` attribute from the input field
- Allow users to manually enter the supplier's SKU/barcode for this product

### 4. Update Placeholder Text
**From:** "Auto-filled from supplier"  
**To:** "Enter supplier's SKU/barcode for this product" or "Enter supplier product code"

### 5. Update Helper Text
Add clarification that this is the supplier's product identifier, not the supplier's company code.

## Files to Modify

### Backend (PHP)
1. `app/Livewire/Pages/ProductManagement/Index.php`
   - Remove auto-fill logic in `updatedFormSupplierId()` method (lines 204-212)
   - Keep field clearing when supplier is deselected

### Frontend (Blade)
1. `resources/views/livewire/pages/product-management/modals/create-edit-product.blade.php`
   - Line 402-409: Update label, placeholder, remove readonly
   - Add helper text explaining the field purpose

2. `resources/views/livewire/pages/product-management/modals/product-details.blade.php`
   - Line 156: Update label from "Supplier Code" to "Supplier SKU"

### Database
- **Supplier Code:** No database changes needed (column name `supplier_code` can stay the same)
- **SKU Field:** Need to make `products.sku` column nullable
  - Current: `sku` is `NOT NULL` with `UNIQUE` constraint
  - Change: Make `sku` nullable (`NULL` allowed) while keeping unique constraint (NULL values won't conflict)

## Additional Requirement: Make SKU Field Optional

The regular "SKU" field (not Supplier SKU) should be optional when creating/editing products.

### Current State
- SKU field is marked as `required` in validation (line 642 in `Index.php`)
- SKU field has `required` attribute in the form (line 262 in `create-edit-product.blade.php`)
- SKU has unique constraint in validation
- Database: `products.sku` column is `NOT NULL` with `UNIQUE` constraint

### Changes Needed
1. **Database Migration:** Make `products.sku` column nullable
   - Create migration to alter column: `ALTER TABLE products MODIFY sku VARCHAR(255) NULL`
   - Unique constraint will still work (NULL values don't conflict with each other)
2. **Backend Validation:** Remove `required` from validation rules
   - Update validation to: `nullable|string|max:255|unique:products,sku`
   - Only check uniqueness if SKU is provided (Laravel handles this automatically with nullable)
3. **Frontend:** Remove `required` attribute from the input field in the blade template
4. **UI:** Update placeholder/helper text to indicate it's optional

## Implementation Steps

1. ✅ Create plan document (this file)
2. ⬜ Create database migration to make `products.sku` nullable
3. ⬜ Run migration
4. ⬜ Update `Index.php` - Remove auto-fill logic for supplier_code
5. ⬜ Update `Index.php` - Make SKU field optional in validation
6. ⬜ Update `create-edit-product.blade.php` - Rename supplier_code label, update placeholder, remove readonly
7. ⬜ Update `create-edit-product.blade.php` - Remove required from SKU field, update placeholder
8. ⬜ Update `product-details.blade.php` - Rename supplier_code label
9. ⬜ Test: Create new product without SKU, verify it works
10. ⬜ Test: Create new product, verify supplier_code field is editable and not auto-filled
11. ⬜ Test: Edit existing product, verify both fields work correctly

## Alternative Naming Options

If "Supplier SKU" doesn't fit, consider:
- "Supplier Barcode" - if it's primarily barcode-based
- "Supplier Product Code" - more descriptive but longer
- "Vendor SKU" - alternative terminology
- "Supplier Item Code" - clear but longer

**Recommendation:** "Supplier SKU" is the most concise and commonly understood term.

