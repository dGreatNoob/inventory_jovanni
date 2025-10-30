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
            <!-- Left Column -->
            <div class="space-y-6">
            <!-- Product Details -->
            <div class="space-y-4">
                <flux:heading size="md" class="text-gray-900 dark:text-white">Product Details</flux:heading>
                
                <flux:input 
                    wire:model="form.name" 
                    label="Product Name" 
                    required
                    placeholder="Enter product name"
                    class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                />
                @error('form.name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input 
                            wire:model="form.sku" 
                            label="SKU" 
                            required
                            placeholder="Enter SKU"
                            class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        />
                        @error('form.sku') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col">
                        <flux:input 
                            wire:model="form.barcode" 
                            label="Barcode" 
                            placeholder="Enter 13-digit barcode (first 6 fixed + price)"
                            class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                            inputmode="numeric"
                            pattern="\\d{13}"
                            maxlength="13"
                        />
                        @error('form.barcode') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <svg class="inline w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            Enter 13 digits. First 6 fixed from product info, last 7 represent price.
                        </p>
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
                <!-- Inventory moved up under description -->
                <div class="space-y-4">
                    <flux:heading size="sm" class="text-gray-900 dark:text-white">Inventory</flux:heading>
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
                </div>
            </div>

            </div>

            <!-- Right Column -->
            <div class="space-y-6">
            <!-- Categorization -->
            <div class="space-y-4">
                <flux:heading size="md" class="text-gray-900 dark:text-white">Categorization</flux:heading>
                
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
                            @disabled(empty($form['root_category_id']))>
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
                    <select wire:model.live="form.supplier_id" 
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
                    placeholder="Auto-filled from supplier"
                    readonly
                    class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                />
                @error('form.supplier_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <!-- Pricing -->
            <div class="space-y-4">
                <!-- <flux:heading size="md" class="text-gray-900 dark:text-white">Pricing</flux:heading> -->
                <!-- Product Type / Pricing Note row at top of pricing (right column) -->
                <div class="grid grid-cols-2 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Type</label>
                        <select wire:model="form.product_type" 
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="regular">Regular Item</option>
                            <option value="sale">Sale Item</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pricing Note</label>
                        <select wire:model="form.price_note" 
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select</option>
                            @if(($form['product_type'] ?? 'regular') === 'sale')
                                <option value="SAL1">SAL1</option>
                                <option value="SAL2">SAL2</option>
                            @else
                                <option value="REG1">REG1</option>
                                <option value="REG2">REG2</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tagging: Regular = White Tag; Sale = Red Tag.</p>
                    @php $isSale = ($form['product_type'] ?? 'regular') === 'sale'; @endphp
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $isSale ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                        <span class="w-2 h-2 rounded-full mr-1 {{ $isSale ? 'bg-red-500' : 'bg-gray-500' }}"></span>
                        {{ $isSale ? 'Red Tag (Sale)' : 'White Tag (Regular)' }}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 items-end">
                    <div>
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
                    </div>
                    <div class="flex items-end">
                        <flux:modal.trigger name="product-price-history">
                            <flux:button variant="ghost">View Price History</flux:button>
                        </flux:modal.trigger>
                    </div>
                    <div>
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
                    <div></div>
                </div>

            </div>
            </div>
            
        </div>

        <!-- Advanced Pricing (Full width) -->
        <div class="space-y-6 mt-2">
            <flux:heading size="md" class="text-gray-900 dark:text-white">Advanced Pricing</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Price Levels -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Price Levels</h4>
                        <flux:button wire:click="addPriceLevel" size="sm" variant="ghost">Add Level</flux:button>
                    </div>
                    <div class="space-y-2">
                        @foreach(($form['price_levels'] ?? []) as $idx => $pl)
                            <div class="grid grid-cols-3 gap-2">
                                <flux:input wire:model="form.price_levels.{{ $idx }}.label" placeholder="Label (e.g., REG1)" />
                                <flux:input wire:model="form.price_levels.{{ $idx }}.amount" type="number" step="0.01" placeholder="Amount" />
                                <flux:button wire:click="removePriceLevel({{ $idx }})" size="sm" variant="ghost">Remove</flux:button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Discount Tiers -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Discount Tiers</h4>
                        <flux:button wire:click="addDiscountTier" size="sm" variant="ghost">Add Tier</flux:button>
                    </div>
                    <div class="space-y-2">
                        @foreach(($form['discount_tiers'] ?? []) as $idx => $dt)
                            <div class="grid grid-cols-4 gap-2">
                                <flux:input wire:model="form.discount_tiers.{{ $idx }}.min_qty" type="number" step="1" placeholder="Min Qty" />
                                <flux:input wire:model="form.discount_tiers.{{ $idx }}.max_qty" type="number" step="1" placeholder="Max Qty" />
                                <flux:input wire:model="form.discount_tiers.{{ $idx }}.discount_percent" type="number" step="0.01" placeholder="% Discount" />
                                <flux:button wire:click="removeDiscountTier({{ $idx }})" size="sm" variant="ghost">Remove</flux:button>
                            </div>
                        @endforeach
                    </div>
                </div>
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