<?php

namespace App\Services;

use App\Models\ProductImage;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductImageService
{
    /**
     * Get product images
     */
    public function getProductImages(int $productId = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = ProductImage::with(['product']);

        if ($productId) {
            $query->where('product_id', $productId);
        }

        return $query->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create a new product image
     */
    public function createImage(array $data): ProductImage
    {
        return DB::transaction(function () use ($data) {
            // If setting as primary, unset other primary images for this product
            if ($data['is_primary'] ?? false) {
                ProductImage::where('product_id', $data['product_id'])
                    ->update(['is_primary' => false]);
            }

            // Set default sort order if not provided
            if (!isset($data['sort_order'])) {
                $maxOrder = ProductImage::where('product_id', $data['product_id'])
                    ->max('sort_order') ?? 0;
                $data['sort_order'] = $maxOrder + 1;
            }

            $image = ProductImage::create($data);

            return $image->load(['product']);
        });
    }

    /**
     * Update product image information
     */
    public function updateImage(ProductImage $image, array $data): ProductImage
    {
        return DB::transaction(function () use ($image, $data) {
            // If setting as primary, unset other primary images for this product
            if ($data['is_primary'] ?? false) {
                ProductImage::where('product_id', $image->product_id)
                    ->where('id', '!=', $image->id)
                    ->update(['is_primary' => false]);
            }

            $image->update($data);

            return $image->fresh(['product']);
        });
    }

    /**
     * Delete product image
     */
    public function deleteImage(ProductImage $image): bool
    {
        return DB::transaction(function () use ($image) {
            // Delete file from storage if it exists
            if ($image->filename && Storage::disk('public')->exists('photos/' . $image->filename)) {
                Storage::disk('public')->delete('photos/' . $image->filename);
            }

            // If this was the primary image, set another as primary
            if ($image->is_primary) {
                $newPrimary = ProductImage::where('product_id', $image->product_id)
                    ->where('id', '!=', $image->id)
                    ->first();
                
                if ($newPrimary) {
                    $newPrimary->update(['is_primary' => true]);
                }
            }

            $image->delete();

            return true;
        });
    }

    /**
     * Get image with full details
     */
    public function getImageDetails(int $imageId): ?ProductImage
    {
        return ProductImage::with(['product.category', 'product.supplier'])->find($imageId);
    }

    /**
     * Set image as primary
     */
    public function setAsPrimary(ProductImage $image): ProductImage
    {
        return DB::transaction(function () use ($image) {
            // Unset other primary images for this product
            ProductImage::where('product_id', $image->product_id)
                ->where('id', '!=', $image->id)
                ->update(['is_primary' => false]);

            // Set this image as primary
            $image->update(['is_primary' => true]);

            return $image->fresh(['product']);
        });
    }

    /**
     * Reorder images
     */
    public function reorderImages(array $imageIds): bool
    {
        return DB::transaction(function () use ($imageIds) {
            foreach ($imageIds as $index => $imageId) {
                ProductImage::where('id', $imageId)->update(['sort_order' => $index + 1]);
            }
            return true;
        });
    }

    /**
     * Upload image file
     */
    public function uploadImage(array $data): ProductImage
    {
        return DB::transaction(function () use ($data) {
            /** @var UploadedFile $file */
            $file = $data['image'];
            $productId = $data['product_id'];

            // Ensure directory exists
            Storage::disk('public')->makeDirectory('photos');

            // Generate unique filename based on original extension
            $originalExtension = strtolower($file->getClientOriginalExtension());
            $extension = in_array($originalExtension, ['jpg', 'jpeg', 'png', 'webp']) ? $originalExtension : 'jpg';
            $filename = Str::uuid() . '.' . $extension;

            Log::debug('ProductImageService.uploadImage: start', [
                'product_id' => $productId,
                'original_name' => $file->getClientOriginalName(),
                'original_mime' => $file->getMimeType(),
                'original_size' => $file->getSize(),
                'target_extension' => $extension,
                'target_filename' => $filename,
            ]);

            // Process image: resize to max width 1920, keep aspect ratio, quality ~85
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getPathname());

            // Scale down only if wider than 1920px
            try {
                // v3 scaleDown(width: ?, height: ?)
                $image = $image->scaleDown(1920);
            } catch (\Throwable $t) {
                Log::warning('ProductImageService.uploadImage: scaleDown failed, proceeding without resize', [
                    'error' => $t->getMessage(),
                ]);
            }

            // Encode according to extension
            $encoded = null;
            $mimeType = null;
            if (in_array($extension, ['jpg', 'jpeg'])) {
                $encoded = $image->toJpeg(85);
                $mimeType = 'image/jpeg';
            } elseif ($extension === 'png') {
                // PNG is lossless; use default compression, approximate quality via compression level
                $encoded = $image->toPng();
                $mimeType = 'image/png';
            } elseif ($extension === 'webp') {
                $encoded = $image->toWebp(85);
                $mimeType = 'image/webp';
            } else {
                // Fallback to JPEG
                $encoded = $image->toJpeg(85);
                $mimeType = 'image/jpeg';
                $filename = Str::uuid() . '.jpg';
                $extension = 'jpg';
            }

            // Persist to storage
            $binary = (string) $encoded;
            Storage::disk('public')->put('photos/' . $filename, $binary);

            $width = method_exists($image, 'width') ? $image->width() : null;
            $height = method_exists($image, 'height') ? $image->height() : null;

            // If setting as primary, unset other primary images for this product
            if ($data['is_primary'] ?? false) {
                ProductImage::where('product_id', $productId)
                    ->update(['is_primary' => false]);
            }

            // Get next sort order
            $maxOrder = ProductImage::where('product_id', $productId)
                ->max('sort_order') ?? 0;

            // Create image record
            $image = ProductImage::create([
                'product_id' => $productId,
                'filename' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $mimeType,
                'file_size' => strlen($binary),
                'width' => $width,
                'height' => $height,
                'alt_text' => $data['alt_text'] ?? null,
                'is_primary' => $data['is_primary'] ?? false,
                'sort_order' => $maxOrder + 1,
            ]);

            Log::debug('ProductImageService.uploadImage: completed', [
                'image_id' => $image->id,
                'stored_path' => 'photos/' . $filename,
                'width' => $width,
                'height' => $height,
                'size_bytes' => strlen($binary),
            ]);

            return $image->load(['product']);
        });
    }

    /**
     * Get product primary image
     */
    public function getPrimaryImage(int $productId): ?ProductImage
    {
        return ProductImage::where('product_id', $productId)
            ->where('is_primary', true)
            ->first();
    }

    /**
     * Get product image gallery
     */
    public function getProductGallery(int $productId): Collection
    {
        return ProductImage::where('product_id', $productId)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Delete all images for a product
     */
    public function deleteProductImages(int $productId): int
    {
        return DB::transaction(function () use ($productId) {
            $images = ProductImage::where('product_id', $productId)->get();
            $deletedCount = 0;

            foreach ($images as $image) {
                if ($this->deleteImage($image)) {
                    $deletedCount++;
                }
            }

            return $deletedCount;
        });
    }

    /**
     * Get image statistics
     */
    public function getImageStats(): array
    {
        $totalImages = ProductImage::count();
        $productsWithImages = Product::whereHas('images')->count();
        $productsWithoutImages = Product::whereDoesntHave('images')->count();
        $primaryImages = ProductImage::where('is_primary', true)->count();

        // Get file size statistics
        $totalSize = 0;
        $images = ProductImage::all();
        
        foreach ($images as $image) {
            if ($image->filename && Storage::disk('public')->exists('photos/' . $image->filename)) {
                $totalSize += Storage::disk('public')->size('photos/' . $image->filename);
            }
        }

        return [
            'total_images' => $totalImages,
            'products_with_images' => $productsWithImages,
            'products_without_images' => $productsWithoutImages,
            'primary_images' => $primaryImages,
            'total_size_bytes' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
        ];
    }

    /**
     * Clean up orphaned images
     */
    public function cleanupOrphanedImages(): int
    {
        $deletedCount = 0;
        $images = ProductImage::all();

        foreach ($images as $image) {
            // Check if file exists but product doesn't
            if ($image->filename && Storage::disk('public')->exists('photos/' . $image->filename)) {
                if (!$image->product) {
                    Storage::disk('public')->delete('photos/' . $image->filename);
                    $image->delete();
                    $deletedCount++;
                }
            }
        }

        return $deletedCount;
    }

    /**
     * Get images by file extension
     */
    public function getImagesByExtension(string $extension): Collection
    {
        return ProductImage::where('filename', 'like', '%.' . $extension)
            ->with(['product'])
            ->get();
    }

    /**
     * Resize image (placeholder for future implementation)
     */
    public function resizeImage(string $filename, int $width, int $height): bool
    {
        // This would implement image resizing logic
        // For now, just return true as placeholder
        return true;
    }
}
