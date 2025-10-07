<!-- Bulk Actions Modal -->
<div x-data="{ show: @entangle('showBulkActionModal').live }" 
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Bulk Actions
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Perform actions on {{ count($selectedImages) }} selected image(s).
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="performBulkAction">
                <div class="bg-white dark:bg-gray-800 px-4 pb-4 sm:p-6">
                    <div class="space-y-6">
                        <!-- Action Selection -->
                        <div>
                            <label for="bulkAction" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Select Action <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <select wire:model="bulkAction" 
                                        id="bulkAction"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Choose an action...</option>
                                    <option value="set_primary">Set Primary Image</option>
                                    <option value="delete">Delete Selected Images</option>
                                </select>
                            </div>
                            @error('bulkAction') 
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Primary Image Selection (only show if set_primary is selected) -->
                        <div x-show="bulkAction === 'set_primary'" 
                             x-transition:enter="ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95">
                            <label for="bulkActionValue" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Select Primary Image
                            </label>
                            <div class="mt-1">
                                <select wire:model="bulkActionValue" 
                                        id="bulkActionValue"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Choose an image...</option>
                                    @foreach($selectedImages as $imageId)
                                        @php
                                            $image = \App\Models\ProductImage::find($imageId);
                                        @endphp
                                        @if($image)
                                            <option value="{{ $imageId }}">{{ $image->product->name }} - {{ $image->alt_text ?: 'No alt text' }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            @error('bulkActionValue') 
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Warning for Delete Action -->
                        <div x-show="bulkAction === 'delete'" 
                             x-transition:enter="ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                        Warning: Delete Action
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                        <p>This action will permanently delete the selected images. This cannot be undone.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Selected Images Count -->
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <strong>{{ count($selectedImages) }}</strong> image(s) selected for this action.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            :disabled="!bulkAction || (bulkAction === 'set_primary' && !bulkActionValue)"
                            :class="bulkAction && (bulkAction !== 'set_primary' || bulkActionValue) ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Execute Action
                    </button>
                    <button type="button"
                            wire:click="showBulkActionModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
