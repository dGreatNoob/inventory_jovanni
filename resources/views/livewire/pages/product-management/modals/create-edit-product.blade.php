<!-- Create/Edit Product Modal -->
<x-product-management-modal 
    name="create-edit-product"
    :title="($isEditMode ?? ($editingProduct !== null)) ? 'Edit Product' : 'Create New Product'"
    :description="($isEditMode ?? ($editingProduct !== null)) ? 'Update the product information below.' : 'Fill in the product information to add it to your inventory.'"
    size="4xl"
    icon="edit"
    icon-color="indigo"
>
    <form wire:submit.prevent="saveProduct" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="space-y-4">
                <flux:heading size="md" class="text-gray-900 dark:text-white">Basic Information</flux:heading>
                
                <flux:input 
                    wire:model="form.name" 
                    label="Product Name" 
                    required
                    placeholder="Enter product name"
                    class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                />
                @error('form.name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                <div class="grid grid-cols-2 gap-4">
                    <flux:input 
                        wire:model="form.sku" 
                        label="SKU" 
                        required
                        placeholder="Enter SKU"
                        class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    />
                    @error('form.sku') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                    <div>
                        <flux:input 
                            wire:model="form.barcode" 
                            label="Barcode" 
                            placeholder="Auto-generated on save"
                            class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                            readonly
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <svg class="inline w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            Barcode is auto-generated for internal tracking
                        </p>
                        @error('form.barcode') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea wire:model="form.remarks" 
                              rows="3"
                              placeholder="Enter product description"
                              class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>
                @error('form.remarks') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <!-- Category & Supplier -->
            <div class="space-y-4">
                <flux:heading size="md" class="text-gray-900 dark:text-white">Classification</flux:heading>
                
                <!-- Root Category (Step 1) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Root Category <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">(Select main category first)</span>
                    </label>
                    <select wire:model.live="form.root_category_id" 
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Select Root Category</option>
                        @foreach($this->rootCategories as $rootCategory)
                            <option value="{{ $rootCategory->id }}">{{ $rootCategory->name }}</option>
                        @endforeach
                    </select>
                    @error('form.root_category_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Subcategory (Step 2) - Cascading -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Sub-category
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">(Optional - for more specific classification)</span>
                    </label>
                    <select wire:model="form.category_id" 
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            @if(empty($form['root_category_id'])) disabled @endif>
                        <option value="">No Sub-category (use root category only)</option>
                        @if(!empty($filteredSubcategories))
                            @foreach($filteredSubcategories as $subcategory)
                                <option value="{{ $subcategory['id'] }}">{{ $subcategory['name'] }}</option>
                            @endforeach
                        @endif
                    </select>
                    @if(empty($form['root_category_id']))
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <svg class="inline w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            Select a root category first to see available sub-categories
                        </p>
                    @elseif(empty($filteredSubcategories))
                        <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                            <svg class="inline w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            This root category has no sub-categories yet
                        </p>
                    @endif
                    @error('form.category_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supplier</label>
                    <select wire:model="form.supplier_id" 
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('form.supplier_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                <flux:input 
                    wire:model="form.supplier_code" 
                    label="Supplier Code" 
                    placeholder="Enter supplier code"
                    class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                />
                @error('form.supplier_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <!-- Pricing -->
            <div class="space-y-4">
                <flux:heading size="md" class="text-gray-900 dark:text-white">Pricing</flux:heading>
                
                <div class="grid grid-cols-2 gap-4">
                    <flux:input 
                        wire:model="form.price" 
                        label="Selling Price" 
                        type="number"
                        step="0.01"
                        required
                        placeholder="0.00"
                        class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    />
                    @error('form.price') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                    <flux:input 
                        wire:model="form.cost" 
                        label="Cost" 
                        type="number"
                        step="0.01"
                        required
                        placeholder="0.00"
                        class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    />
                    @error('form.cost') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <flux:input 
                    wire:model="form.price_note" 
                    label="Price Note" 
                    placeholder="Enter price notes"
                    class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                />
                @error('form.price_note') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <!-- Inventory -->
            <div class="space-y-4">
                <flux:heading size="md" class="text-gray-900 dark:text-white">Inventory</flux:heading>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit of Measure</label>
                        <select wire:model="form.uom" 
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="pcs">Pieces</option>
                            <option value="kg">Kilograms</option>
                            <option value="lbs">Pounds</option>
                            <option value="g">Grams</option>
                            <option value="oz">Ounces</option>
                            <option value="l">Liters</option>
                            <option value="ml">Milliliters</option>
                            <option value="m">Meters</option>
                            <option value="cm">Centimeters</option>
                            <option value="ft">Feet</option>
                            <option value="in">Inches</option>
                        </select>
                    </div>
                    @error('form.uom') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                    <flux:input 
                        wire:model="form.shelf_life_days" 
                        label="Shelf Life (Days)" 
                        type="number"
                        placeholder="Enter shelf life"
                        class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    />
                    @error('form.shelf_life_days') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <flux:input 
                    wire:model="form.initial_quantity" 
                    label="Initial Quantity" 
                    type="number"
                    step="0.01"
                    placeholder="Enter initial quantity"
                    class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                />
                @error('form.initial_quantity') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                    <select wire:model="form.location_id" 
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Select Location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }} ({{ $location->type }})</option>
                        @endforeach
                    </select>
                </div>
                @error('form.location_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Status -->
        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <input type="checkbox" 
                       wire:model="form.disabled" 
                       id="form.disabled"
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="form.disabled" class="ml-2 block text-sm text-gray-900 dark:text-white">
                    Disabled
                </label>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Check this to disable the product</p>
        </div>

        <x-slot name="actions">
            <flux:button wire:click="resetForm" variant="ghost">
                Reset
            </flux:button>
            
            <flux:button wire:click="saveProduct" variant="primary">
                {{ ($isEditMode ?? ($editingProduct !== null)) ? 'Update Product' : 'Create Product' }}
            </flux:button>
        </x-slot>
    </form>
</x-product-management-modal>