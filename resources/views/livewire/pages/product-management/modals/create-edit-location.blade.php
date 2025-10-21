<!-- Create/Edit Location Modal -->
<x-product-management-modal 
    name="create-edit-location"
    :show="$showCreateModal || $showEditModal"
    :title="$editingLocation ? 'Edit Location' : 'Create New Location'"
    :description="$editingLocation ? 'Update the location information below.' : 'Fill in the details to create a new inventory location.'"
    size="lg"
    icon="edit"
    icon-color="blue"
>
    <form wire:submit.prevent="saveLocation" class="space-y-6">
        <div class="space-y-4">
            <flux:input 
                wire:model="form.name" 
                label="Location Name" 
                required
                placeholder="Enter location name"
                class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
            />
            @error('form.name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                <select wire:model="form.type" 
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="warehouse">Warehouse</option>
                    <option value="store">Store</option>
                    <option value="office">Office</option>
                    <option value="other">Other</option>
                </select>
            </div>
            @error('form.type') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                <textarea wire:model="form.address" 
                          rows="3"
                          placeholder="Enter location address"
                          class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
            </div>
            @error('form.address') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea wire:model="form.description" 
                          rows="3"
                          placeholder="Enter location description"
                          class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
            </div>
            @error('form.description') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Status -->
        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <input type="checkbox" 
                       wire:model="form.is_active" 
                       id="form.is_active"
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="form.is_active" class="ml-2 block text-sm text-gray-900 dark:text-white">
                    Active Location
                </label>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Check this to make the location active and available for inventory</p>
        </div>

        <x-slot name="actions">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            
            <flux:button type="submit" variant="primary">
                {{ $editingLocation ? 'Update Location' : 'Create Location' }}
            </flux:button>
        </x-slot>
    </form>
</x-product-management-modal>