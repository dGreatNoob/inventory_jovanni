# Category Hierarchy Implementation Guide

## 📚 Overview

The product management system now supports **hierarchical categories** with proper visual representation. Products can be tagged with either **root categories** or **subcategories**, and the UI clearly shows the relationship between them.

---

## 🏗️ Database Structure

### Categories Table
```sql
CREATE TABLE categories (
    id bigint PRIMARY KEY,
    entity_id bigint DEFAULT 1,
    name varchar(255),
    description text,
    parent_id bigint NULL,           -- Self-referencing FK for hierarchy
    sort_order int DEFAULT 0,
    slug varchar(255) UNIQUE,
    is_active tinyint(1) DEFAULT 1,
    deleted_at timestamp NULL,       -- Soft deletes
    created_at timestamp,
    updated_at timestamp,
    
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE
);
```

### Products Table
```sql
CREATE TABLE products (
    id bigint PRIMARY KEY,
    category_id bigint,              -- FK to categories table
    name varchar(255),
    sku varchar(255),
    ...
    
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

---

## 🔗 Category Relationships

### Root Category (parent_id = NULL)
- Top-level categories
- Can have multiple subcategories
- Examples: "Electronics", "Clothing", "Food & Beverage"

### Sub-Category (parent_id = {parent_id})
- Child categories under a root category
- Examples: 
  - "Electronics" → "Laptops", "Phones", "Accessories"
  - "Clothing" → "Shirts", "Pants", "Shoes"

### Example Hierarchy
```
📁 Electronics (Root Category)
  ↳ 📂 Laptops (Sub-category)
  ↳ 📂 Phones (Sub-category)
  ↳ 📂 Accessories (Sub-category)

📁 Clothing (Root Category)
  ↳ 📂 Shirts (Sub-category)
  ↳ 📂 Pants (Sub-category)
  ↳ 📂 Shoes (Sub-category)

📁 Food & Beverage (Root Category)
  ↳ 📂 Snacks (Sub-category)
  ↳ 📂 Beverages (Sub-category)
```

---

## 🎨 UI Implementation

### 1. **Product Creation/Edit Form**

**Location:** `resources/views/livewire/pages/product-management/modals/create-edit-product.blade.php`

**Visual Display:**
```
Category (Root categories and subcategories)
┌─────────────────────────────────────────┐
│ Select Category                      ▼  │
├─────────────────────────────────────────┤
│ Electronics (Root Category)             │  ← Root category
│ Electronics › Laptops                   │  ← Subcategory with parent
│ Electronics › Phones                    │  ← Subcategory with parent
│ Clothing (Root Category)                │  ← Root category
│ Clothing › Shirts                       │  ← Subcategory with parent
│ Clothing › Pants                        │  ← Subcategory with parent
└─────────────────────────────────────────┘
```

**Features:**
- Root categories show "(Root Category)" label
- Subcategories show full path: `Parent › Child`
- Uses `›` symbol for better readability
- Hint text explains the hierarchy

### 2. **Filter Dropdown**

**Location:** `resources/views/livewire/pages/product-management/index.blade.php`

**Visual Display:**
```
Category
┌─────────────────────────────────────────┐
│ All Categories                       ▼  │
├─────────────────────────────────────────┤
│ Electronics                             │  ← Root category
│ Electronics › Laptops                   │  ← Subcategory with parent
│ Electronics › Phones                    │  ← Subcategory with parent
│ Clothing                                │  ← Root category
│ Clothing › Shirts                       │  ← Subcategory with parent
└─────────────────────────────────────────┘
```

---

## 💻 Code Implementation

### 1. **Category Model Enhancements**

**File:** `app/Models/Category.php`

```php
// Get full hierarchical path
public function getFullNameAttribute(): string
{
    return $this->parent ? $this->parent->name . ' > ' . $this->name : $this->name;
}

// Get indented name for UI display
public function getIndentedNameAttribute(): string
{
    return $this->parent ? '↳ ' . $this->name : $this->name;
}

// Check if root category
public function isRootCategory(): bool
{
    return is_null($this->parent_id);
}

// Check if subcategory
public function isSubCategory(): bool
{
    return !is_null($this->parent_id);
}

// Get hierarchical list for dropdowns
public static function getHierarchicalList($entityId = null)
{
    $query = self::with('parent')
        ->active()
        ->orderBy('parent_id')
        ->orderBy('sort_order')
        ->orderBy('name');

    if ($entityId) {
        $query->byEntity($entityId);
    }

    return $query->get()->map(function ($category) {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'full_name' => $category->full_name,
            'indented_name' => $category->indented_name,
            'parent_id' => $category->parent_id,
            'is_root' => $category->isRootCategory(),
        ];
    });
}
```

### 2. **Loading Categories with Parent**

**File:** `app/Livewire/Pages/ProductManagement/Index.php`

```php
public function loadFilters()
{
    // Load hierarchical categories with parent information
    $this->categories = Category::with('parent')
        ->active()
        ->orderBy('parent_id')      // Root categories first
        ->orderBy('sort_order')     // Then by custom sort order
        ->orderBy('name')           // Finally by name
        ->get(['id', 'name', 'parent_id', 'sort_order']);
    
    // ... other filters
}
```

### 3. **Blade Template Logic**

**File:** `resources/views/livewire/pages/product-management/modals/create-edit-product.blade.php`

```blade
<select wire:model="form.category_id">
    <option value="">Select Category</option>
    @foreach($categories as $category)
        @if($category->parent_id)
            <!-- Subcategory with full path -->
            <option value="{{ $category->id }}" class="pl-4">
                {{ $category->parent ? $category->parent->name . ' › ' . $category->name : $category->name }}
            </option>
        @else
            <!-- Root category -->
            <option value="{{ $category->id }}" class="font-semibold">
                {{ $category->name }} (Root Category)
            </option>
        @endif
    @endforeach
</select>
```

---

## 🎯 Business Rules

### Product Tagging
1. **Products can be tagged with ANY category** (root or subcategory)
2. Root categories can serve as broad classifications
3. Subcategories provide more specific classification
4. Each product has **exactly one category**

### Example Usage:

**Option 1: Tag with Root Category**
- Product: "Various Electronics Bundle"
- Category: "Electronics" (Root)

**Option 2: Tag with Subcategory** (Recommended)
- Product: "MacBook Pro 16-inch"
- Category: "Electronics › Laptops"

**Option 3: Tag with Another Subcategory**
- Product: "iPhone 15 Pro"
- Category: "Electronics › Phones"

---

## 📊 Filtering Behavior

When filtering by category:
- **Selecting a root category**: Shows products tagged with that root category only
- **Selecting a subcategory**: Shows products tagged with that specific subcategory only

**Future Enhancement:** Add option to filter by parent category and include all child categories.

---

## 🔍 Category Management

**URL:** `http://localhost:8001/product-management/categories`

**Features:**
- Create root categories (leave parent_id empty)
- Create subcategories (select a parent)
- Move categories (change parent)
- Reorder categories (sort_order)
- Activate/deactivate categories
- Delete categories (cascades to products)

---

## 🚀 Usage Examples

### Creating a New Product

1. **Navigate to:** Product Management
2. **Click:** "Add Product" button
3. **Select Category:**
   - See all categories with hierarchy
   - Root categories labeled as "(Root Category)"
   - Subcategories show full path: "Parent › Child"
4. **Choose appropriate category:**
   - Broad classification → Select root category
   - Specific classification → Select subcategory
5. **Save product**

### Filtering Products

1. **Click:** "Filters" button
2. **Category Dropdown:**
   - Select "All Categories" → Show all products
   - Select "Electronics" → Show products in Electronics root category
   - Select "Electronics › Laptops" → Show only laptop products

---

## 🎨 Visual Design Elements

### Symbols Used:
- `›` - Separator between parent and child (better than `>` or `→`)
- `↳` - Visual indicator for subcategory (in indented view)
- `(Root Category)` - Label for root categories

### Ordering:
1. Categories ordered by `parent_id` (nulls first = roots first)
2. Then by `sort_order` (custom sorting)
3. Finally by `name` (alphabetical)

This ensures root categories appear before their children in dropdowns.

---

## 🔄 Database Queries

### Get All Categories Hierarchically
```php
$categories = Category::with('parent')
    ->active()
    ->orderBy('parent_id')
    ->orderBy('sort_order')
    ->orderBy('name')
    ->get();
```

### Get Only Root Categories
```php
$rootCategories = Category::whereNull('parent_id')
    ->active()
    ->orderBy('name')
    ->get();
```

### Get Subcategories of a Root
```php
$subcategories = Category::where('parent_id', $rootCategoryId)
    ->active()
    ->orderBy('sort_order')
    ->orderBy('name')
    ->get();
```

### Get Products with Category Hierarchy
```php
$products = Product::with('category.parent')
    ->get();

foreach ($products as $product) {
    echo $product->name . " - " . $product->category->full_name;
    // Output: "MacBook Pro - Electronics › Laptops"
}
```

---

## ✅ Summary

**What Changed:**
1. ✅ Category Model now has helper methods for hierarchy
2. ✅ Product form shows hierarchical category list
3. ✅ Filter dropdown shows hierarchical categories
4. ✅ Visual indicators distinguish root from subcategories
5. ✅ Full category path displayed for better UX

**Benefits:**
- 📊 Better product organization
- 🔍 Easier filtering and search
- 👁️ Clear visual hierarchy
- 🎯 More specific product classification
- 📈 Improved inventory management

**Next Steps:**
- Test category selection in product creation
- Create sample categories with hierarchy
- Tag products with appropriate categories
- Use filters to verify functionality

---

## 🐛 Troubleshooting

### Issue: Parent category not showing in dropdown
**Solution:** Ensure `with('parent')` is used when loading categories

### Issue: Categories not ordered correctly
**Solution:** Check the `orderBy` clauses in the query

### Issue: Can't see subcategories
**Solution:** Verify `is_active = true` for both parent and child categories

---

*Last Updated: October 8, 2025*

