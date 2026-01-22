# Product Image Gallery Module - Database Tables Analysis

**Route:** `/product-management/images`  
**Component:** `App\Livewire\Pages\ProductManagement\ProductImageGallery`  
**View:** `resources/views/livewire/pages/product-management/product-image-gallery.blade.php`  
**Service:** `App\Services\ProductImageService`

## Directly Used Tables

### 1. `product_images` (Primary Table)
- **Model:** `App\Models\ProductImage`
- **Usage:**
  - Main CRUD operations (create, read, update, delete)
  - Image upload and file management
  - Search and filtering (by product, extension)
  - Sorting (by sort_order, created_at)
  - Primary image management
  - Image reordering
  - Bulk operations (delete, set primary)
  - Statistics (total images, primary images)
- **Relationships:**
  - `belongsTo` → `product` (Product model)
- **Fields Used:**
  - `product_id` - Product reference (filtering, relationships)
  - `filename` - Image filename (storage, display, extension filtering)
  - `original_filename` - Original uploaded filename
  - `alt_text` - Alt text for accessibility (search, display)
  - `mime_type` - MIME type (jpg, jpeg, png, webp)
  - `file_size` - File size in bytes (statistics)
  - `width` - Image width in pixels
  - `height` - Image height in pixels
  - `is_primary` - Primary image flag (filtering, bulk actions)
  - `sort_order` - Display order (sorting, reordering)
- **Features:**
  - Image upload with automatic resizing (max 1920px width)
  - Primary image management (only one primary per product)
  - Sort order management
  - File storage in `storage/photos/`
  - Thumbnail generation support
  - Extension filtering (jpg, jpeg, png, webp)
  - Orphaned image cleanup

### 2. `products`
- **Model:** `App\Models\Product`
- **Usage:**
  - Product selection for image uploads
  - Filter images by product
  - Display product information with images
  - Search by product name/SKU
  - Statistics (products with/without images)
  - Product cards view (grouped by product)
- **Relationships:**
  - `hasMany` → `images` (ProductImage model)
  - `belongsTo` → `category` (Category model, loaded for filters)
- **Methods:**
  - `Product::whereHas('images')` - Products with images
  - `Product::whereDoesntHave('images')` - Products without images
  - `Product::with('images')` - Eager load images
  - `$product->images` - Get product images
- **Fields Used:**
  - `id` - Product ID (filtering, relationships)
  - `name` - Product name (search, display)
  - `sku` - Product SKU (search, display)
  - `category_id` - Category reference (loaded for filters)

## Indirectly Used Tables (via Relationships)

### 3. `categories`
- **Model:** `App\Models\Category`
- **Usage:**
  - Loaded with products for filter dropdown
  - Display category information (if needed)
- **Relationships:**
  - `hasMany` → `products` (Product model)
- **Methods:**
  - `Product::with('category')` - Eager load category

### 4. `users`
- **Model:** `App\Models\User`
- **Usage:**
  - Permission checks (`image view`, `image create`, `image edit`, `image delete`)
  - Authentication check for uploads
- **Methods:**
  - `auth()->user()->hasAnyPermission()`
  - `auth()->check()`

## Summary

**Total Tables Used: 4**

1. ✅ `product_images` - Primary table
2. ✅ `products` - Product selection and filtering
3. ✅ `categories` - Category information (via products)
4. ✅ `users` - Permissions

## Notes

- **File Storage:** Images stored in `storage/app/public/photos/` with UUID filenames
- **Image Processing:** Automatic resizing to max 1920px width, quality ~85%
- **Primary Image:** Only one primary image per product (automatically managed)
- **Sort Order:** Images can be reordered via `sort_order` field
- **Extension Support:** JPG, JPEG, PNG, WebP
- **Thumbnails:** Support for thumbnail generation (stored in `storage/photos/thumbnails/`)
- **Statistics:** Tracks total images, products with/without images, primary images, total storage size
- **Orphaned Cleanup:** Service method to clean up images without products
- **Bulk Operations:** Delete multiple images, set primary image
- **View Modes:** Grid view (product cards) and list view (image list)
- **Image Viewer:** Full-screen image viewer with navigation (prev/next)

