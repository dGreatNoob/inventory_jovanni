<!-- Bulk Actions Modal -->
<flux:modal name="bulk-actions-image" class="max-w-lg">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/20">
                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                </svg>
            </div>
            <div class="flex-1">
                <flux:heading size="lg" class="text-gray-900 dark:text-white">Bulk Actions</flux:heading>
                <flux:text class="mt-2 text-gray-600 dark:text-gray-400">
                    Perform actions on {{ count($selectedImages) }} selected image(s).
                </flux:text>
            </div>
        </div>

        <!-- Form -->
        <form wire:submit.prevent="performBulkAction" class="space-y-6">
            <!-- Action Selection -->
            <flux:select 
                wire:model.live="bulkAction" 
                label="Select Action" 
                placeholder="Choose an action..."
                required
                class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
            >
                <option value="set_primary">Set Primary Image</option>
                <option value="delete">Delete Selected Images</option>
            </flux:select>
            @error('bulkAction') 
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <!-- Primary Image Selection (only show if set_primary is selected) -->
            @if($bulkAction === 'set_primary')
                <div x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100">
                    <flux:select 
                        wire:model="bulkActionValue" 
                        label="Select Primary Image"
                        placeholder="Choose an image..."
                        class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    >
                        @foreach($selectedImages as $imageId)
                            @php
                                $image = \App\Models\ProductImage::find($imageId);
                            @endphp
                            @if($image)
                                <option value="{{ $imageId }}">{{ $image->product->name }} - {{ $image->alt_text ?: 'No alt text' }}</option>
                            @endif
                        @endforeach
                    </flux:select>
                    @error('bulkActionValue') 
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <!-- Warning for Delete Action -->
            @if($bulkAction === 'delete')
                <div x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
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
            @endif

            <!-- Selected Images Count -->
            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <strong>{{ count($selectedImages) }}</strong> image(s) selected for this action.
                </p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button 
                    type="submit" 
                    variant="primary"
                    :disabled="!$bulkAction || ($bulkAction === 'set_primary' && !$bulkActionValue)"
                >
                    Execute Action
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>
