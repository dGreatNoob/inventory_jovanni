<!-- Upload Images Modal -->
<x-product-management-modal 
    name="upload-images"
    :show="$showUploadModal"
    title="Upload Product Images"
    description="Select multiple images to upload for a product. Maximum file size: 5MB per image."
    size="2xl"
    icon="upload"
    icon-color="blue"
>
    <form wire:submit.prevent="uploadImages" class="space-y-6">
        <!-- Product Selection -->
        <div>
            <flux:select 
                wire:model="uploadProductId" 
                label="Product" 
                placeholder="Select a product..."
                required
                class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
            >
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                @endforeach
            </flux:select>
            @error('uploadProductId') 
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Image Upload -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Images <span class="text-red-500">*</span>
            </label>
            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 dark:border-gray-600 dark:hover:border-gray-500">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                        <label for="uploadImages" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                            <span>Upload files</span>
                            <input id="uploadImages" 
                                   type="file" 
                                   multiple 
                                   accept="image/*"
                                   wire:model="uploadImages" 
                                   class="sr-only">
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        PNG, JPG, GIF, WebP up to 5MB each
                    </p>
                </div>
            </div>
            @error('uploadImages.*') 
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Preview Uploaded Images -->
        @if($uploadImages)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Preview ({{ count($uploadImages) }} files selected)
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($uploadImages as $index => $image)
                        <div class="relative">
                            <img src="{{ $image->temporaryUrl() }}" 
                                 alt="Preview {{ $index + 1 }}"
                                 class="w-full h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="absolute top-1 right-1">
                                <flux:button 
                                    type="button" 
                                    wire:click="$set('uploadImages.{{ $index }}', null)"
                                    variant="danger"
                                    size="sm"
                                    class="!p-1 !min-w-0"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Alt Text -->
        <flux:input 
            wire:model="uploadAltText" 
            label="Alt Text (applied to all images)"
            placeholder="Enter alt text for accessibility"
            class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
        />
        @error('uploadAltText') 
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror

        <!-- Set as Primary -->
        <flux:checkbox 
            wire:model="uploadSetAsPrimary" 
            label="Set first image as primary"
        />
        @error('uploadSetAsPrimary') 
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror

        <x-slot name="actions">
            <flux:modal.close>
                <flux:button variant="ghost" wire:click="resetUploadForm">Cancel</flux:button>
            </flux:modal.close>
            
            <flux:button 
                type="submit" 
                variant="primary"
                :disabled="!$uploadProductId || !$uploadImages || $uploadImages.length === 0"
            >
                Upload Images
            </flux:button>
        </x-slot>
    </form>
</x-product-management-modal>
