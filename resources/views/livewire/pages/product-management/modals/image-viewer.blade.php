<!-- Image Viewer Modal -->
<flux:modal name="image-viewer" class="max-w-4xl" :closable="false">
    <div class="space-y-6 pr-2">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/20">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <flux:heading size="lg" class="text-gray-900 dark:text-white">Image Viewer</flux:heading>
                    @if($viewingImage)
                        <flux:text class="text-gray-600 dark:text-gray-400">
                            {{ $viewingImage->product->name }} ({{ $viewingImage->product->sku }})
                        </flux:text>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-2">
                @if($viewingImage)
                    @if(!$viewingImage->is_primary)
                        <flux:button 
                            wire:click="setAsPrimary({{ $viewingImage->id }})" 
                            variant="ghost"
                            size="sm"
                            class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                            title="Set as Primary"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </flux:button>
                    @endif
                    
                    <flux:modal.trigger name="edit-image">
                        @can('image edit')
                        <flux:button 
                            wire:click="openEditModal({{ $viewingImage->id }})" 
                            variant="ghost"
                            size="sm"
                            class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                            title="Edit Image"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </flux:button>
                        @endcan
                    </flux:modal.trigger>
                    <flux:modal.trigger name="delete-image">
                        @can('image delete')
                        <flux:button 
                            wire:click="deleteImage({{ $viewingImage->id }})" 
                            variant="ghost"
                            size="sm"
                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                            title="Delete Image"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </flux:button>
                        @endcan
                    </flux:modal.trigger>
                @endif
            </div>
        </div>

        <!-- Image Content -->
        @if($viewingImage)
            <div class="space-y-6">
                <!-- Main Image -->
                <div class="relative text-center bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <button type="button" wire:click="viewerPrev" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/70 dark:bg-gray-800/70 hover:bg-white dark:hover:bg-gray-800 rounded-full p-2 shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <img src="{{ $this->viewingImageUrl ?? ($viewingImage->url ?? asset('storage/photos/' . $viewingImage->filename)) }}" 
                         alt="{{ $viewingImage->alt_text ?: ($viewingImage->product->name ?? 'Product image') }}"
                         class="max-w-full max-h-96 mx-auto rounded-lg shadow-lg object-contain"
                         onerror="this.src='{{ asset('images/placeholder.png') }}'; this.onerror=null;">
                    <button type="button" wire:click="viewerNext" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/70 dark:bg-gray-800/70 hover:bg-white dark:hover:bg-gray-800 rounded-full p-2 shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                <!-- Image Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Info -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Image Information</h4>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Alt Text</dt>
                                <dd class="text-gray-900 dark:text-white">
                                    {{ $viewingImage->alt_text ?: 'No alt text' }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500 dark:text-gray-400">File Size</dt>
                                <dd class="text-gray-900 dark:text-white">{{ $viewingImage->formatted_size }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Dimensions</dt>
                                <dd class="text-gray-900 dark:text-white">
                                    {{ $viewingImage->width }} × {{ $viewingImage->height }} pixels
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500 dark:text-gray-400">MIME Type</dt>
                                <dd class="text-gray-900 dark:text-white">{{ $viewingImage->mime_type }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Sort Order</dt>
                                <dd class="text-gray-900 dark:text-white">{{ $viewingImage->sort_order }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Product Info -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Product Information</h4>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Product Name</dt>
                                <dd class="text-gray-900 dark:text-white">{{ $viewingImage->product->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500 dark:text-gray-400">SKU</dt>
                                <dd class="text-gray-900 dark:text-white">{{ $viewingImage->product->sku }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Category</dt>
                                <dd class="text-gray-900 dark:text-white">{{ $viewingImage->product->category->name ?? 'No category' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Supplier</dt>
                                <dd class="text-gray-900 dark:text-white">{{ $viewingImage->product->supplier->name ?? 'No supplier' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Price</dt>
                                <dd class="text-gray-900 dark:text-white">${{ number_format($viewingImage->product->price, 2) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Status Badges -->
                <div class="flex items-center justify-center space-x-4">
                    @if($viewingImage->is_primary)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                            Primary Image
                        </span>
                    @endif
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Active
                    </span>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
            <flux:modal.close>
                <flux:button variant="primary">Close</flux:button>
            </flux:modal.close>
        </div>
    </div>
</flux:modal>
