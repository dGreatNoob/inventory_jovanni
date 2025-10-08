<?php

namespace App\Livewire\Pages\ProductManagement;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
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
    public $productFilter = '';
    public $extensionFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 20;
    public $viewMode = 'grid'; // grid or list

    // Data
    public $products = [];
    public $selectedImages = [];
    public $showFilters = false;

    // Modals
    public $showUploadModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showBulkActionModal = false;
    public $showImageViewer = false;
    public $editingImage = null;
    public $viewingImage = null;

    // Upload
    public $uploadImages = [];
    public $uploadProductId = '';
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
            ->get(['id', 'name', 'sku']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedProductFilter()
    {
        $this->resetPage();
    }

    public function updatedExtensionFilter()
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
        $this->productFilter = '';
        $this->extensionFilter = '';
        $this->resetPage();
    }

    public function getImagesProperty()
    {
        $filters = [
            'product_id' => $this->productFilter,
            'extension' => $this->extensionFilter,
        ];

        return $this->productImageService->getProductImages(
            $this->productFilter ?: null,
            $this->perPage
        );
    }

    public function getStatsProperty()
    {
        return $this->productImageService->getImageStats();
    }

    public function openUploadModal()
    {
        $this->resetUploadForm();
        $this->showUploadModal = true;
    }

    public function openEditModal($imageId)
    {
        $this->editingImage = ProductImage::findOrFail($imageId);
        $this->loadImageData();
        $this->showEditModal = true;
    }

    public function openImageViewer($imageId)
    {
        $this->viewingImage = ProductImage::findOrFail($imageId);
        $this->showImageViewer = true;
    }

    public function deleteImage($imageId)
    {
        $this->editingImage = ProductImage::findOrFail($imageId);
        $this->showDeleteModal = true;
    }

    public function confirmDelete()
    {
        if ($this->editingImage) {
            try {
                $this->productImageService->deleteImage($this->editingImage);
                $this->showDeleteModal = false;
                $this->editingImage = null;
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

    public function openBulkActionModal()
    {
        if (empty($this->selectedImages)) {
            session()->flash('error', 'Please select images first.');
            return;
        }
        $this->showBulkActionModal = true;
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
            $this->showBulkActionModal = false;
            $this->bulkAction = '';
            $this->bulkActionValue = '';

        } catch (\Exception $e) {
            session()->flash('error', 'Error performing bulk action: ' . $e->getMessage());
        }
    }

    public function resetUploadForm()
    {
        $this->uploadImages = [];
        $this->uploadProductId = '';
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

            $this->showUploadModal = false;
            $this->resetUploadForm();
            session()->flash('message', 'Images uploaded successfully.');

        } catch (\Throwable $e) {
            session()->flash('error', 'Error uploading images: ' . $e->getMessage());
        }
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
            $this->showEditModal = false;
            $this->resetForm();
            $this->editingImage = null;
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
        return view('livewire.pages.product-management.product-image-gallery', [
            'images' => $this->images,
            'stats' => $this->stats,
        ]);
    }
}
