<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use App\Services\ProductImageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProductImageController extends Controller
{
    protected ProductImageService $imageService;

    public function __construct(ProductImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of product images
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $productId = $request->get('product_id');
            $perPage = $request->get('per_page', 20);

            $images = $this->imageService->getProductImages($productId, $perPage);

            return response()->json([
                'success' => true,
                'data' => $images,
                'message' => 'Product images retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product images: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product image
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'filename' => 'required|string|max:255',
                'alt_text' => 'nullable|string|max:255',
                'is_primary' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            $image = $this->imageService->createImage($validated);

            return response()->json([
                'success' => true,
                'data' => $image,
                'message' => 'Product image created successfully'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating product image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product image
     */
    public function show(int $id): JsonResponse
    {
        try {
            $image = $this->imageService->getImageDetails($id);

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product image not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $image,
                'message' => 'Product image retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified product image
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $image = ProductImage::findOrFail($id);

            $validated = $request->validate([
                'filename' => 'sometimes|required|string|max:255',
                'alt_text' => 'nullable|string|max:255',
                'is_primary' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            $updatedImage = $this->imageService->updateImage($image, $validated);

            return response()->json([
                'success' => true,
                'data' => $updatedImage,
                'message' => 'Product image updated successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product image
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $image = ProductImage::findOrFail($id);
            $this->imageService->deleteImage($image);

            return response()->json([
                'success' => true,
                'message' => 'Product image deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set primary image
     */
    public function setPrimary(int $id): JsonResponse
    {
        try {
            $image = ProductImage::findOrFail($id);
            $this->imageService->setAsPrimary($image);

            return response()->json([
                'success' => true,
                'message' => 'Primary image set successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error setting primary image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder images
     */
    public function reorder(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'image_ids' => 'required|array|min:1',
                'image_ids.*' => 'integer|exists:product_images,id',
            ]);

            $this->imageService->reorderImages($validated['image_ids']);

            return response()->json([
                'success' => true,
                'message' => 'Images reordered successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reordering images: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload image file
     */
    public function upload(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB max
                'alt_text' => 'nullable|string|max:255',
                'is_primary' => 'boolean',
            ]);

            $image = $this->imageService->uploadImage($validated);

            return response()->json([
                'success' => true,
                'data' => $image,
                'message' => 'Image uploaded successfully'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }
}
