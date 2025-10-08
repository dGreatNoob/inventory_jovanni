<!-- Edit Image Modal -->
<flux:modal name="edit-image" class="max-w-lg">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/20">
                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <flux:heading size="lg" class="text-gray-900 dark:text-white">Edit Image</flux:heading>
                <flux:text class="mt-2 text-gray-600 dark:text-gray-400">Update the image information below.</flux:text>
            </div>
        </div>

        <!-- Form -->
        <form wire:submit.prevent="saveImage" class="space-y-6">
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
            <flux:input 
                wire:model="form.alt_text" 
                label="Alt Text"
                placeholder="Enter alt text for accessibility"
                class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
            />
            @error('form.alt_text') 
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <!-- Sort Order -->
            <flux:input 
                wire:model="form.sort_order" 
                type="number"
                label="Sort Order"
                min="0"
                placeholder="Enter sort order"
                class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
            />
            @error('form.sort_order') 
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <!-- Set as Primary -->
            <flux:checkbox 
                wire:model="form.is_primary" 
                label="Set as primary image for this product"
            />
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

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    Update Image
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>
