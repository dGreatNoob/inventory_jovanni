<?php

namespace App\Livewire\Pages\ProductManagement;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ProductImageService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Layout('components.layouts.app')]
#[Title('Product Image Gallery')]
class ProductImageGallery extends Component
{
    use WithPagination, WithFileUploads;

    // Search and Filters
    public $search = '';
    public $categoryFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 20;
    public $viewMode = 'grid'; // grid or list

    // Data
    public $products = [];
    public $categories = [];
    public $selectedImages = [];
    public $showFilters = false;

    // Modals
    public $editingImage = null;
    public $viewingImage = null;
    public $viewerProductId = null;
    public $viewerImages = [];
    public $viewerIndex = 0;

    // Upload
    public $uploadImages = [];
    public $uploadProductId = '';
    public $uploadProductSearch = '';
    public $uploadProductDropdown = false;
    public $uploadAltText = '';
    public $uploadSetAsPrimary = false;

    // Form Data
    public $form = [
        'alt_text' => '',
        'is_primary' => false,
        'sort_order' => 0,
    ];

    // Bulk Actions
    public $bulkAction = '';
    public $bulkActionValue = '';

    protected $productImageService;

    public function boot(ProductImageService $productImageService)
    {
        $this->productImageService = $productImageService;
    }

    public function mount()
    {
        $this->loadFilters();
    }

    public function loadFilters()
    {
        $this->products = Product::with('category')
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'supplier_code', 'product_number']);
        $this->categories = Category::active()->orderBy('name')->get(['id', 'name']);
    }

    public function getFilteredUploadProductsProperty()
    {
        $products = collect($this->products);
        $query = trim($this->uploadProductSearch);
        if ($query === '') {
            return $products->take(100)->values();
        }

        // Normalize query: remove spaces, convert to lowercase
        $normalizedQuery = strtolower(preg_replace('/\s+/', '', $query));
        
        // Generate prefix segments (e.g., "LD2501" -> ["L", "LD", "LD2", "LD25", "LD250", "LD2501"])
        $prefixes = [];
        for ($i = 1; $i <= strlen($normalizedQuery); $i++) {
            $prefixes[] = substr($normalizedQuery, 0, $i);
        }

        // Tokenize query BEFORE normalization (split by spaces, hyphens, underscores)
        // e.g., "LD 2501" -> ["LD", "2501"], "LD2501-81" -> ["LD2501", "81"]
        // Then normalize each token
        $queryTokens = preg_split('/[\s\-_]+/', $query);
        $queryTokens = array_filter(array_map(function($t) {
            $normalized = strtolower(trim($t));
            return $normalized !== '' ? $normalized : null;
        }, $queryTokens));

        // Detect structured product-number style query: has letters, digits, and a separator
        $hasLetters = (bool) preg_match('/[A-Za-z]/', $query);
        $hasDigits  = (bool) preg_match('/\d/', $query);
        $hasSep     = (bool) preg_match('/[\s\-_]/', $query);
        $isProductNumberQuery = $hasLetters && $hasDigits && $hasSep;

        return $products->filter(function ($p) use ($normalizedQuery, $prefixes, $queryTokens, $isProductNumberQuery) {
            // Normalize product fields
            $name = strtolower(preg_replace('/\s+/', '', (string) ($p->name ?? '')));
            $sku = strtolower(preg_replace('/\s+/', '', (string) ($p->sku ?? '')));
            $supplierCode = strtolower(preg_replace('/\s+/', '', (string) ($p->supplier_code ?? '')));
            $productNumber = strtolower(preg_replace('/\s+/', '', (string) ($p->product_number ?? '')));

            // If this looks like a product-number query (e.g. "LD-127"),
            // restrict results to product_number only:
            // - first token must match as a prefix
            // - all remaining tokens must also appear in product_number.
            if ($isProductNumberQuery && !empty($queryTokens)) {
                if (empty($productNumber)) {
                    return false;
                }

                $tokens = array_values($queryTokens);
                foreach ($tokens as $index => $token) {
                    if (strlen($token) < 1) {
                        continue;
                    }

                    if ($index === 0) {
                        if (!str_starts_with($productNumber, $token)) {
                            return false;
                        }
                    } else {
                        if (!str_contains($productNumber, $token)) {
                            return false;
                        }
                    }
                }

                return true;
            }

            // 1. Exact/contains match (original behavior)
            if (str_contains($name, $normalizedQuery)
                || str_contains($sku, $normalizedQuery)
                || str_contains($supplierCode, $normalizedQuery)
                || str_contains($productNumber, $normalizedQuery)) {
                return true;
            }

            // 2. Prefix segmentation matching for product_number
            // Check if any prefix matches the start of product_number
            if (!empty($productNumber)) {
                foreach ($prefixes as $prefix) {
                    if (strlen($prefix) >= 2 && str_starts_with($productNumber, $prefix)) {
                        return true;
                    }
                }
            }

            // 3. Token-based matching
            // Check if all query tokens are found in any product field
            if (!empty($queryTokens)) {
                $allTokensMatch = true;
                foreach ($queryTokens as $token) {
                    if (strlen($token) < 1) {
                        continue;
                    }
                    $tokenFound = str_contains($name, $token)
                        || str_contains($sku, $token)
                        || str_contains($supplierCode, $token)
                        || str_contains($productNumber, $token);
                    
                    if (!$tokenFound) {
                        $allTokensMatch = false;
                        break;
                    }
                }
                if ($allTokensMatch) {
                    return true;
                }
            }

            return false;
        })->take(100)->values();
    }

    public function toggleUploadProductDropdown()
    {
        $this->uploadProductDropdown = !$this->uploadProductDropdown;
    }

    public function selectUploadProduct($productId = null)
    {
        $this->uploadProductId = $productId ? (string) $productId : '';
        $this->uploadProductDropdown = false;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->resetPage();
    }

    /**
     * Images on the current page of product cards (for selection and view count).
     */
    public function getImagesProperty()
    {
        return $this->productCards->getCollection()->pluck('images')->flatten(1);
    }

    public function getProductCardsProperty()
    {
        $query = Product::whereHas('images');

        if ($this->categoryFilter) {
            $query->where('category_id', (int) $this->categoryFilter);
        }

        if ($this->search) {
            $search = "%{$this->search}%";
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('sku', 'like', $search);
            })->orWhereHas('images', function ($qi) use ($search) {
                $qi->where('alt_text', 'like', $search);
            });
        }

        // Eager load images ordered by primary then sort_order
        $query->with(['images' => function ($q) {
            $q->orderByDesc('is_primary')->orderBy('sort_order')->orderBy('created_at', 'desc');
        }]);

        return $query->orderBy('name')->paginate($this->perPage);
    }

    public function getStatsProperty()
    {
        return $this->productImageService->getImageStats();
    }

    public function openEditModal($imageId)
    {
        $this->editingImage = ProductImage::findOrFail($imageId);
        $this->loadImageData();
    }

    public function openImageViewer($imageId)
    {
        $this->viewingImage = ProductImage::findOrFail($imageId);
    }

    public function deleteImage($imageId)
    {
        $this->editingImage = ProductImage::findOrFail($imageId);
    }

    public function confirmDelete()
    {
        if ($this->editingImage) {
            try {
                $this->productImageService->deleteImage($this->editingImage);
                $this->editingImage = null;
                $this->dispatch('close-modal', name: 'delete-image');
                session()->flash('message', 'Image deleted successfully.');
            } catch (\Exception $e) {
                session()->flash('error', 'Error deleting image: ' . $e->getMessage());
            }
        }
    }

    public function toggleImageSelection($imageId)
    {
        if (in_array($imageId, $this->selectedImages)) {
            $this->selectedImages = array_diff($this->selectedImages, [$imageId]);
        } else {
            $this->selectedImages[] = $imageId;
        }
    }

    public function selectAllImages()
    {
        $this->selectedImages = collect($this->images)->pluck('id')->toArray();
    }

    public function clearSelection()
    {
        $this->selectedImages = [];
    }


    public function performBulkAction()
    {
        if (empty($this->selectedImages) || empty($this->bulkAction)) {
            return;
        }

        try {
            switch ($this->bulkAction) {
                case 'delete':
                    foreach ($this->selectedImages as $imageId) {
                        $image = ProductImage::findOrFail($imageId);
                        $this->productImageService->deleteImage($image);
                    }
                    session()->flash('message', 'Selected images deleted successfully.');
                    break;
                case 'set_primary':
                    if ($this->bulkActionValue) {
                        $image = ProductImage::findOrFail($this->bulkActionValue);
                        $this->productImageService->setAsPrimary($image);
                        session()->flash('message', 'Primary image set successfully.');
                    }
                    break;
            }

            $this->clearSelection();
            $this->bulkAction = '';
            $this->bulkActionValue = '';
            $this->dispatch('close-modal', name: 'bulk-actions-image');

        } catch (\Exception $e) {
            session()->flash('error', 'Error performing bulk action: ' . $e->getMessage());
        }
    }

    public function resetUploadForm()
    {
        $this->uploadImages = [];
        $this->uploadProductId = '';
        $this->uploadProductSearch = '';
        $this->uploadProductDropdown = false;
        $this->uploadAltText = '';
        $this->uploadSetAsPrimary = false;
    }

    public function resetForm()
    {
        $this->form = [
            'alt_text' => '',
            'is_primary' => false,
            'sort_order' => 0,
        ];
    }

    public function loadImageData()
    {
        if ($this->editingImage) {
            $this->form = [
                'alt_text' => $this->editingImage->alt_text,
                'is_primary' => $this->editingImage->is_primary,
                'sort_order' => $this->editingImage->sort_order,
            ];
        }
    }

    // Renamed to avoid collision with $uploadImages property
    public function submitImageUpload()
    {
        $this->validate([
            'uploadImages.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240', // 10MB max
            'uploadProductId' => 'required|exists:products,id',
            'uploadAltText' => 'nullable|string|max:255',
            'uploadSetAsPrimary' => 'boolean',
        ]);

        try {
            if (!auth()->check()) {
                abort(403, 'Unauthorized');
            }
            foreach ($this->uploadImages as $image) {
                $this->productImageService->uploadImage([
                    'image' => $image,
                    'product_id' => $this->uploadProductId,
                    'alt_text' => $this->uploadAltText,
                    'is_primary' => $this->uploadSetAsPrimary,
                ]);
            }

            // Close upload modal and immediately open viewer for the product to show all uploaded photos
            $this->dispatch('close-modal', name: 'upload-images');
            $this->openProductViewer($this->uploadProductId);
            $this->dispatch('open-modal', name: 'image-viewer');
            $this->resetUploadForm();
            session()->flash('message', 'Images uploaded successfully.');

        } catch (\Throwable $e) {
            session()->flash('error', 'Error uploading images: ' . $e->getMessage());
        }
    }

    public function getSelectedProductImagesProperty()
    {
        if (!$this->uploadProductId) {
            return collect();
        }
        return $this->productImageService->getProductGallery((int) $this->uploadProductId);
    }

    public function openProductViewer($productId, $startImageId = null)
    {
        $images = $this->productImageService->getProductGallery((int) $productId);
        $this->viewerProductId = (int) $productId;
        $this->viewerImages = $images->pluck('id')->values()->all();
        $this->viewerIndex = 0;
        if ($startImageId) {
            $index = array_search((int) $startImageId, $this->viewerImages, true);
            if ($index !== false) {
                $this->viewerIndex = $index;
            }
        } else {
            // Try to start at primary image if available
            $primary = $images->firstWhere('is_primary', true);
            if ($primary) {
                $idx = array_search($primary->id, $this->viewerImages, true);
                if ($idx !== false) {
                    $this->viewerIndex = $idx;
                }
            }
        }
        $currentId = $this->viewerImages[$this->viewerIndex] ?? null;
        $this->viewingImage = $currentId ? ProductImage::with('product')->find($currentId) : null;
    }

    public function viewerPrev()
    {
        if (empty($this->viewerImages)) {
            return;
        }
        $count = count($this->viewerImages);
        $this->viewerIndex = ($this->viewerIndex - 1 + $count) % $count;
        $currentId = $this->viewerImages[$this->viewerIndex] ?? null;
        $this->viewingImage = $currentId ? ProductImage::with('product')->find($currentId) : null;
    }

    public function viewerNext()
    {
        if (empty($this->viewerImages)) {
            return;
        }
        $count = count($this->viewerImages);
        $this->viewerIndex = ($this->viewerIndex + 1) % $count;
        $currentId = $this->viewerImages[$this->viewerIndex] ?? null;
        $this->viewingImage = $currentId ? ProductImage::with('product')->find($currentId) : null;
    }

    public function selectAllCurrentProducts()
    {
        $ids = [];
        foreach ($this->productCards as $product) {
            $cover = $product->images->first();
            if ($cover) {
                $ids[] = $cover->id;
            }
        }
        $this->selectedImages = $ids;
    }

    public function getViewingImageUrlProperty(): ?string
    {
        if (!$this->viewingImage || empty($this->viewingImage->filename)) {
            return null;
        }
        
        return asset('storage/photos/' . $this->viewingImage->filename);
    }

    public function saveImage()
    {
        $this->validate([
            'form.alt_text' => 'nullable|string|max:255',
            'form.is_primary' => 'boolean',
            'form.sort_order' => 'nullable|integer|min:0',
        ]);

        try {
            $this->productImageService->updateImage($this->editingImage, $this->form);
            $this->resetForm();
            $this->editingImage = null;
            $this->dispatch('close-modal', name: 'edit-image');
            session()->flash('message', 'Image updated successfully.');

        } catch (\Exception $e) {
            session()->flash('error', 'Error updating image: ' . $e->getMessage());
        }
    }

    public function setAsPrimary($imageId)
    {
        try {
            $image = ProductImage::findOrFail($imageId);
            $this->productImageService->setAsPrimary($image);
            session()->flash('message', 'Image set as primary successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error setting primary image: ' . $e->getMessage());
        }
    }

    public function reorderImages($imageIds)
    {
        try {
            $this->productImageService->reorderImages($imageIds);
            session()->flash('message', 'Images reordered successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error reordering images: ' . $e->getMessage());
        }
    }

    public function render()
    {
        if (!auth()->user()->hasAnyPermission(['image view'])) {
            return view('livewire.pages.errors.403');
        }

        return view('livewire.pages.product-management.product-image-gallery', [
            'images' => $this->images,
            'stats' => $this->stats,
            'productCards' => $this->productCards,
            'selectedProductImages' => $this->selectedProductImages,
        ]);
    }
}
