<!-- Edit Image Modal -->
<div x-data="{ show: @entangle('showEditModal').live }" 
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
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             x-on:click="show = false"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Edit Image
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Update the image information below.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="saveImage">
                <div class="bg-white dark:bg-gray-800 px-4 pb-4 sm:p-6">
                    <div class="space-y-6">
                        <!-- Image Preview -->
                        @if($editingImage)
                            <div class="text-center">
                                <img src="{{ $editingImage->url }}" 
                                     alt="{{ $editingImage->alt_text ?: $editingImage->product->name }}"
                                     class="mx-auto h-32 w-32 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $editingImage->product->name }} ({{ $editingImage->product->sku }})
                                </p>
                            </div>
                        @endif

                        <!-- Alt Text -->
                        <div>
                            <label for="form.alt_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Alt Text
                            </label>
                            <div class="mt-1">
                                <input type="text" 
                                       wire:model="form.alt_text" 
                                       id="form.alt_text"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                       placeholder="Enter alt text for accessibility">
                            </div>
                            @error('form.alt_text') 
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label for="form.sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Sort Order
                            </label>
                            <div class="mt-1">
                                <input type="number" 
                                       wire:model="form.sort_order" 
                                       id="form.sort_order"
                                       min="0"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                       placeholder="Enter sort order">
                            </div>
                            @error('form.sort_order') 
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Set as Primary -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   wire:model="form.is_primary" 
                                   id="form.is_primary"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="form.is_primary" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                Set as primary image for this product
                            </label>
                        </div>
                        @error('form.is_primary') 
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <!-- Image Info -->
                        @if($editingImage)
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Image Information</h4>
                                <dl class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <dt class="font-medium text-gray-500 dark:text-gray-400">File Size</dt>
                                        <dd class="text-gray-900 dark:text-white">{{ $editingImage->formatted_size }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-500 dark:text-gray-400">Dimensions</dt>
                                        <dd class="text-gray-900 dark:text-white">
                                            {{ $editingImage->width }}x{{ $editingImage->height }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-500 dark:text-gray-400">MIME Type</dt>
                                        <dd class="text-gray-900 dark:text-white">{{ $editingImage->mime_type }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-500 dark:text-gray-400">Uploaded</dt>
                                        <dd class="text-gray-900 dark:text-white">{{ $editingImage->created_at->format('M j, Y') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update Image
                    </button>
                    <button type="button"
                            wire:click="resetForm"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
