<!-- Delete Image Modal -->
<flux:modal name="delete-image" class="max-w-lg">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-red-100 dark:bg-red-900/20">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <flux:heading size="lg" class="text-gray-900 dark:text-white">Delete Image</flux:heading>
                <flux:text class="mt-2 text-gray-600 dark:text-gray-400">
                    Are you sure you want to delete this image? This action cannot be undone.
                </flux:text>
            </div>
        </div>

        <!-- Content -->
        @if($editingImage)
            <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
                <div class="flex items-center space-x-3">
                    <img src="{{ $editingImage->url }}" 
                         alt="{{ $editingImage->alt_text ?: $editingImage->product->name }}"
                         class="h-16 w-16 object-cover rounded-lg border border-red-200 dark:border-red-800">
                    <div>
                        <p class="text-sm text-red-800 dark:text-red-200">
                            <strong>Product:</strong> {{ $editingImage->product->name }}
                        </p>
                        <p class="text-sm text-red-600 dark:text-red-300">
                            <strong>SKU:</strong> {{ $editingImage->product->sku }}
                        </p>
                        @if($editingImage->is_primary)
                            <p class="text-sm text-red-600 dark:text-red-300">
                                <strong>Status:</strong> Primary Image
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>

            <flux:button wire:click="confirmDelete" variant="danger">
                Delete Image
            </flux:button>
        </div>
    </div>
</flux:modal>
