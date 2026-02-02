<!-- Upload Images Modal -->
<flux:modal
    name="upload-images"
    class="max-w-2xl"
>
    <div class="space-y-6 pr-2">
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
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Product <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Search by name, SKU, or Supplier Code</p>
            <div class="relative" wire:click.outside="$set('uploadProductDropdown', false)">
                <div wire:click="toggleUploadProductDropdown"
                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm cursor-pointer flex justify-between items-center min-h-[42px]">
                    <span class="{{ $uploadProductId ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                        @if($uploadProductId)
                            @php
                                $selectedProduct = collect($products)->firstWhere('id', (int) $uploadProductId);
                            @endphp
                            @if($selectedProduct)
                                {{ $selectedProduct->name }} ({{ $selectedProduct->sku }}{{ $selectedProduct->supplier_code ? ' · ' . $selectedProduct->supplier_code : '' }})
                            @else
                                Product #{{ $uploadProductId }}
                            @endif
                        @else
                            Select a product...
                        @endif
                    </span>
                    <svg class="w-4 h-4 ml-2 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                @if($uploadProductDropdown)
                    <div class="absolute z-20 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg dark:bg-gray-700 dark:border-gray-600">
                        <div class="p-2 border-b border-gray-200 dark:border-gray-600 sticky top-0 bg-white dark:bg-gray-700">
                            <input type="text"
                                wire:model.live.debounce.200ms="uploadProductSearch"
                                placeholder="Search by name, SKU, or Supplier Code..."
                                onclick="event.stopPropagation()"
                                class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-600 dark:text-white dark:placeholder-gray-400" />
                        </div>
                        <div class="max-h-48 overflow-auto">
                            @foreach($this->filteredUploadProducts as $product)
                                <button type="button"
                                        wire:click="selectUploadProduct({{ $product->id }})"
                                        class="w-full flex items-center px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600 last:border-b-0 {{ $uploadProductId == $product->id ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-900 dark:text-blue-100' : 'text-gray-900 dark:text-white' }}">
                                    <span class="flex-1 min-w-0">
                                        <span class="font-medium truncate block">{{ $product->name }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            SKU: {{ $product->sku ?? '—' }}{{ $product->supplier_code ? ' · Supplier Code: ' . $product->supplier_code : '' }}
                                        </span>
                                    </span>
                                    @if($uploadProductId == $product->id)
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @endif
                                </button>
                            @endforeach
                            @if($this->filteredUploadProducts->isEmpty())
                                <div class="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    @if($uploadProductSearch)
                                        No products match "{{ $uploadProductSearch }}"
                                    @else
                                        No products available
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
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
            <div
                 x-data="{
                    componentId: '{{ $this->id() }}',
                    isDragging: false,
                    isUploading: false,
                    progress: 0,
                    uploadFiles(files) {
                        const list = Array.from(files || []).filter(f => f instanceof File);
                        if (list.length === 0) return;
                        const lw = window.Livewire.find(this.componentId);
                        if (!lw) return;
                        this.isUploading = true;
                        const onDone = () => { this.isUploading = false; };
                        const onFail = () => { this.isUploading = false; };
                        const onProgress = (e) => { this.progress = e.detail.progress; };
                        if (list.length === 1) {
                            lw.upload('uploadImages', list[0], onDone, onFail, onProgress);
                        } else if (typeof lw.uploadMultiple === 'function') {
                            lw.uploadMultiple('uploadImages', list, onDone, onFail, onProgress);
                        } else {
                            // Fallback: queue files one-by-one
                            let remaining = list.length;
                            list.forEach(file => {
                                lw.upload('uploadImages', file, () => { if (--remaining === 0) onDone(); }, onFail, onProgress);
                            });
                        }
                    }
                 }"
                 x-on:dragover.prevent="isDragging = true"
                 x-on:dragleave.prevent="isDragging = false"
                 x-on:drop.prevent="isDragging = false; uploadFiles($event.dataTransfer.files)"
                 :class="isDragging ? 'border-blue-400 dark:border-blue-500' : 'border-gray-300 dark:border-gray-600'"
                 class="flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md hover:border-gray-400 dark:hover:border-gray-500">
                <div class="space-y-3 text-center"
                     x-data
                     x-on:livewire-upload-start="isUploading = true"
                     x-on:livewire-upload-finish="isUploading = false; progress = 0"
                     x-on:livewire-upload-error="isUploading = false"
                     x-on:livewire-upload-progress="progress = $event.detail.progress">
                    @if(empty($uploadImages))
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
                                        x-ref="fileInput"
                                        x-on:change="uploadFiles($event.target.files)"
                                        class="sr-only">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <template x-if="!isDragging">
                            <p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG, WebP up to 10MB each</p>
                        </template>
                        <template x-if="isDragging">
                            <p class="text-xs text-blue-600 dark:text-blue-400">Drop files to upload</p>
                        </template>
                    @endif

                    <div x-show="isUploading" class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-blue-600 h-2.5" :style="`width: ${progress}%`"></div>
                    </div>
                </div>
                @if($uploadImages)
                    <div class="mt-4 w-full text-left">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Preview ({{ count($uploadImages) }} files selected)
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($uploadImages as $index => $image)
                                @if($image)
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
                                    <div class="absolute bottom-1 left-1 bg-black/60 text-white text-[10px] px-1 py-0.5 rounded">{{ $image->getClientOriginalName() }}</div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            @error('uploadImages.*') 
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Alt Text -->
        <flux:input 
            wire:model.live="uploadAltText" 
            label="Alt Text (applied to all images)"
            placeholder="Enter alt text for accessibility"
            class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
        />
        @error('uploadAltText') 
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror

        <!-- Set as Primary -->
        <flux:checkbox 
            wire:model.live="uploadSetAsPrimary" 
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
                        wire:loading.attr="disabled" 
                        wire:target="submitImageUpload"
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
