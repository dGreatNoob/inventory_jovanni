<!-- Delete Product Modal -->
<x-product-management-modal 
    name="delete-product"
    title="Delete Product"
    description="Are you sure you want to delete this product? This action cannot be undone and will permanently remove the product from your inventory."
    size="md"
    icon="delete"
    icon-color="red"
>
    <div class="space-y-4">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Warning</h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                        <p>You are about to delete <strong class="font-semibold">{{ $editingProduct->name ?? '' }}</strong>.</p>
                        <p class="mt-1">This action cannot be undone and will permanently remove the product from your inventory.</p>
                    </div>
                </div>
            </div>
        </div>

        @if($editingProduct && $editingProduct->inventory()->count() > 0)
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Inventory Warning</h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>This product has inventory records. Deleting it will also remove all associated inventory data.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <x-slot name="actions">
        <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
        </flux:modal.close>
        
        <flux:button wire:click="confirmDelete" variant="danger">
            Delete Product
        </flux:button>
    </x-slot>
</x-product-management-modal>