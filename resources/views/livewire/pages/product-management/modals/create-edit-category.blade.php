<!-- Create/Edit Category Modal -->
<x-product-management-modal 
    name="create-edit-category"
    :show="$showCreateModal || $showEditModal"
    :title="$editingCategory ? 'Edit Category' : 'Create New Category'"
    :description="$editingCategory ? 'Update the category information below.' : 'Fill in the category information to organize your products.'"
    size="lg"
    icon="edit"
    icon-color="indigo"
>
    <form wire:submit.prevent="saveCategory" class="space-y-6">
        <div class="space-y-4">
            <flux:input 
                wire:model="form.name" 
                label="Category Name" 
                required
                placeholder="Enter category name"
                class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
            />
            @error('form.name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea wire:model="form.description" 
                          rows="3"
                          placeholder="Enter category description"
                          class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
            </div>
            @error('form.description') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Parent Category</label>
                    <select wire:model="form.parent_id" 
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">No Parent (Root Category)</option>
                        @foreach($parentCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('form.parent_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                <flux:input 
                    wire:model="form.sort_order" 
                    label="Sort Order" 
                    type="number"
                    placeholder="Enter sort order"
                    class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                />
                @error('form.sort_order') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <flux:input 
                wire:model="form.slug" 
                label="Slug" 
                placeholder="Enter URL slug (auto-generated if empty)"
                class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
            />
            @error('form.slug') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

            <flux:input 
                wire:model="form.meta_title" 
                label="Meta Title" 
                placeholder="Enter meta title for SEO"
                class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
            />
            @error('form.meta_title') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meta Description</label>
                <textarea wire:model="form.meta_description" 
                          rows="2"
                          placeholder="Enter meta description for SEO"
                          class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
            </div>
            @error('form.meta_description') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Status -->
        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <flux:checkbox 
                wire:model="form.is_active" 
                label="Active"
                description="Check this to make the category active and visible"
            />
        </div>

        <x-slot name="actions">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            
            <flux:button type="submit" variant="primary">
                {{ $editingCategory ? 'Update Category' : 'Create Category' }}
            </flux:button>
        </x-slot>
    </form>
</x-product-management-modal>