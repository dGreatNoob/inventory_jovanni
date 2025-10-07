<!-- Bulk Actions Category Modal -->
<x-product-management-modal 
    name="bulk-actions-category"
    :show="$showBulkActionModal"
    title="Bulk Actions"
    :description="'You have selected ' . count($selectedCategories) . ' category(ies). Choose an action to perform on all selected categories.'"
    size="lg"
    icon="bulk"
    icon-color="indigo"
>
    <div class="space-y-6">
        <flux:select 
            wire:model="bulkAction" 
            label="Action" 
            placeholder="Select Action"
            class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
        >
            <option value="">Select Action</option>
            <option value="delete">Delete Categories</option>
            <option value="activate">Activate Categories</option>
            <option value="deactivate">Deactivate Categories</option>
            <option value="update_parent">Update Parent Category</option>
        </flux:select>

        @if($bulkAction === 'update_parent')
            <flux:select 
                wire:model="bulkActionValue" 
                label="New Parent Category" 
                placeholder="Select Parent Category"
                class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
            >
                <option value="">No Parent (Root Category)</option>
                @foreach($parentCategories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </flux:select>
        @endif

        @if($bulkAction === 'delete')
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
                            <p>This action will permanently delete all selected categories. This cannot be undone.</p>
                            <p class="mt-1">Categories with products or subcategories will be affected.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($bulkAction === 'activate')
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Activate Categories</h3>
                        <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                            <p>This will activate all selected categories, making them visible and available for use.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($bulkAction === 'deactivate')
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Deactivate Categories</h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>This will deactivate all selected categories, making them hidden and unavailable for use.</p>
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
        
        <flux:button 
            wire:click="performBulkAction" 
            variant="primary"
            :disabled="!$bulkAction || ($bulkAction === 'update_parent' && !$bulkActionValue)"
        >
            Apply Action
        </flux:button>
    </x-slot>
</x-product-management-modal>