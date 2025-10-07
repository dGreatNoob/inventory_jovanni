<!-- Image Viewer Modal -->
<div x-data="{ show: @entangle('showImageViewer').live }" 
     x-show="show" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" 
             x-on:click="show = false"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                Image Viewer
                            </h3>
                            @if($viewingImage)
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $viewingImage->product->name }} ({{ $viewingImage->product->sku }})
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($viewingImage && !$viewingImage->is_primary)
                            <button wire:click="setAsPrimary({{ $viewingImage->id }})" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:bg-yellow-900 dark:text-yellow-300 dark:hover:bg-yellow-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                Set Primary
                            </button>
                        @endif
                        <button wire:click="showImageViewer = false" 
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Close
                        </button>
                    </div>
                </div>
            </div>

            <!-- Image Content -->
            <div class="bg-white dark:bg-gray-800 px-4 pb-4 sm:p-6">
                @if($viewingImage)
                    <div class="space-y-6">
                        <!-- Main Image -->
                        <div class="text-center">
                            <img src="{{ $viewingImage->url }}" 
                                 alt="{{ $viewingImage->alt_text ?: $viewingImage->product->name }}"
                                 class="max-w-full max-h-96 mx-auto rounded-lg shadow-lg object-contain">
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
            </div>
        </div>
    </div>
</div>
