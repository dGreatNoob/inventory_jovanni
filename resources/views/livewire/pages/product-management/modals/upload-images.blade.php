<!-- Upload Images Modal -->
<flux:modal
    name="upload-images"
    class="max-w-2xl"
>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900">
                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Upload Product Images</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Select multiple images to upload for a product. Accepted: JPG, PNG, WebP. Max 10MB each.</p>
            </div>
        </div>

        <form wire:submit.prevent="submitImageUpload" class="space-y-6">
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

            @if($selectedProductImages && count($selectedProductImages) > 0)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Existing Images for Selected Product ({{ count($selectedProductImages) }})
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($selectedProductImages as $img)
                            <div class="relative group">
                                <img src="{{ $img->url }}" alt="{{ $img->alt_text ?: 'Image' }}" class="w-full h-20 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                                @if($img->is_primary)
                                    <span class="absolute top-1 right-1 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Primary</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Image Upload -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Images <span class="text-red-500">*</span>
            </label>
            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 dark:border-gray-600 dark:hover:border-gray-500">
                <div class="space-y-3 text-center" x-data="{ isUploading: false, progress: 0 }"
                     x-on:livewire-upload-start="isUploading = true"
                     x-on:livewire-upload-finish="isUploading = false; progress = 0"
                     x-on:livewire-upload-error="isUploading = false"
                     x-on:livewire-upload-progress="progress = $event.detail.progress">
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
                        JPG, PNG, WebP up to 10MB each
                    </p>

                    <div x-show="isUploading" class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-blue-600 h-2.5" :style="`width: ${progress}%`"></div>
                    </div>
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

            <!-- Actions -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                <!-- Debug Panel -->
                <div class="text-left text-xs text-gray-600 dark:text-gray-300 space-y-1">
                    <div><span class="font-semibold">Debug:</span></div>
                    <div>Product ID: <span class="font-mono">{{ $uploadProductId ?: 'null' }}</span></div>
                    <div>Files Selected: <span class="font-mono">{{ is_countable($uploadImages) ? count($uploadImages ?? []) : 0 }}</span></div>
                    <div>Alt Text: <span class="font-mono">{{ $uploadAltText ?: 'null' }}</span></div>
                    <div>Set Primary: <span class="font-mono">{{ $uploadSetAsPrimary ? 'true' : 'false' }}</span></div>
                    <div wire:loading.delay.shortest wire:target="submitImageUpload" class="text-blue-600 dark:text-blue-400">Submitting...</div>
                    @error('uploadProductId') <div class="text-red-600 dark:text-red-400">Product Error: {{ $message }}</div> @enderror
                    @error('uploadImages.*') <div class="text-red-600 dark:text-red-400">File Error: {{ $message }}</div> @enderror
                    @error('uploadAltText') <div class="text-red-600 dark:text-red-400">Alt Error: {{ $message }}</div> @enderror
                </div>
                <div class="flex justify-end space-x-3">
                <flux:modal.close>
                    <flux:button variant="ghost" wire:click="resetUploadForm">Cancel</flux:button>
                </flux:modal.close>

                <flux:button
                        type="submit"
                        variant="primary"
                        :disabled="!$uploadProductId || !$uploadImages || count($uploadImages ?? []) === 0"
                    >
                        <span wire:loading.remove wire:target="submitImageUpload">Upload Images</span>
                        <span wire:loading wire:target="submitImageUpload" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Uploading...
                        </span>
                    </flux:button>
                </div>
            </div>
        </form>
    </div>
</flux:modal>
