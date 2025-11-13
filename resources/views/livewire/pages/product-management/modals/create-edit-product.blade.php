@php
    $isEditing = ($isEditMode ?? ($editingProduct !== null));
@endphp

<div
    x-data="{ open: @entangle('showProductPanel').live }"
    x-cloak
    x-on:keydown.escape.window="if (open) { open = false; $wire.closeProductPanel(); }"
>
    <template x-teleport="body">
        <div
            x-show="open"
            x-transition.opacity
            class="fixed inset-0 z-50 flex"
        >
            <div
                x-show="open"
                x-transition.opacity
                class="fixed inset-0 bg-neutral-900/30 dark:bg-neutral-900/50"
                @click="open = false; $wire.closeProductPanel()"
            ></div>

            <section
                x-show="open"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="relative ml-auto flex h-full w-full max-w-4xl"
            >
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 dark:bg-indigo-400"></div>

                <div class="ml-[0.25rem] flex h-full w-full flex-col bg-white shadow-xl dark:bg-zinc-900">
                    <header class="flex items-start justify-between border-b border-gray-200 px-6 py-5 dark:border-zinc-700">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    {{ $isEditing ? 'Edit Product' : 'Create New Product' }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $isEditing ? 'Update the product information below.' : 'Fill in the product information to add it to your inventory.' }}
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-gray-500 dark:hover:bg-zinc-800 dark:hover:text-gray-200"
                            @click="open = false; $wire.closeProductPanel()"
                            aria-label="Close product panel"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </header>

                    <div class="flex-1 overflow-hidden">
                        <form wire:submit.prevent="saveProduct" class="flex h-full flex-col">
                            <div class="flex-1 overflow-y-auto px-6 py-6">
                                <div class="space-y-8">
                                    <!-- Product Details -->
                                    <section class="space-y-4">
                                        <div>
                                            <flux:heading size="md" class="text-gray-900 dark:text-white">Product Details</flux:heading>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Core identifiers customers and staff search by.</p>
                                        </div>

                                        <div class="space-y-4">
                                            <flux:input
                                                wire:model="form.name"
                                                label="Product Name"
                                                required
                                                placeholder="Enter product name"
                                                class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                            />
                                            @error('form.name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                <div>
                                                    <flux:input
                                                        wire:model="form.sku"
                                                        label="SKU"
                                                        required
                                                        placeholder="Enter SKU"
                                                        class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                    />
                                                    @error('form.sku') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                </div>

                                                <div class="flex flex-col">
                                                    <flux:input
                                                        wire:model="form.barcode"
                                                        label="Barcode"
                                                        placeholder="Enter 13-digit barcode (first 6 fixed + price)"
                                                        class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                        inputmode="numeric"
                                                        pattern="\\d{13}"
                                                        maxlength="13"
                                                    />
                                                    @error('form.barcode') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        <svg class="mr-1 inline h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Enter 13 digits. First 6 fixed from product info, last 7 represent price.
                                                    </p>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                                <textarea
                                                    wire:model="form.remarks"
                                                    rows="3"
                                                    placeholder="Enter product description"
                                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                ></textarea>
                                            </div>
                                            @error('form.remarks') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        </div>
                                    </section>

                                    <!-- Inventory -->
                                    <section class="space-y-4">
                                        <div>
                                            <flux:heading size="md" class="text-gray-900 dark:text-white">Inventory</flux:heading>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Track quantities and handling information.</p>
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Unit of Measure</label>
                                                <select
                                                    wire:model="form.uom"
                                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                >
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
                                                @error('form.uom') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>

                                            <div>
                                                <flux:input
                                                    wire:model="form.shelf_life_days"
                                                    label="Shelf Life (Days)"
                                                    type="number"
                                                    placeholder="Enter shelf life"
                                                    class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                />
                                                @error('form.shelf_life_days') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>
                                        </div>

                                        <flux:input
                                            wire:model="form.initial_quantity"
                                            label="Initial Quantity"
                                            type="number"
                                            step="0.01"
                                            placeholder="Enter initial quantity"
                                            class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        />
                                        @error('form.initial_quantity') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </section>

                                    <!-- Categorization -->
                                    <section class="space-y-4">
                                        <div>
                                            <flux:heading size="md" class="text-gray-900 dark:text-white">Categorization</flux:heading>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Organize the product within your catalog tree.</p>
                                        </div>

                                        <div class="space-y-4">
                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Root Category <span class="text-red-500">*</span>
                                                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400">(Select main category first)</span>
                                                </label>
                                                <select
                                                    wire:model.live="form.root_category_id"
                                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                >
                                                    <option value="">Select Root Category</option>
                                                    @foreach($this->rootCategories as $rootCategory)
                                                        <option value="{{ $rootCategory->id }}">{{ $rootCategory->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('form.root_category_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Sub-category
                                                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400">(Optional - for more specific classification)</span>
                                                </label>
                                                <select
                                                    wire:model="form.category_id"
                                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                    @disabled(empty($form['root_category_id']))
                                                >
                                                    <option value="">No Sub-category (use root category only)</option>
                                                    @if(!empty($filteredSubcategories))
                                                        @foreach($filteredSubcategories as $subcategory)
                                                            <option value="{{ $subcategory['id'] }}">{{ $subcategory['name'] }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @if(empty($form['root_category_id']))
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        <svg class="mr-1 inline h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Select a root category first to see available sub-categories
                                                    </p>
                                                @elseif(empty($filteredSubcategories))
                                                    <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                                                        <svg class="mr-1 inline h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        This root category has no sub-categories yet
                                                    </p>
                                                @endif
                                                @error('form.category_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Supplier -->
                                    <section class="space-y-4">
                                        <div>
                                            <flux:heading size="md" class="text-gray-900 dark:text-white">Supplier</flux:heading>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Link the product to its sourcing partner.</p>
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                                                <select
                                                    wire:model.live="form.supplier_id"
                                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                >
                                                    <option value="">Select Supplier</option>
                                                    @foreach($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('form.supplier_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>

                                            <div>
                                                <flux:input
                                                    wire:model="form.supplier_code"
                                                    label="Supplier Code"
                                                    placeholder="Auto-filled from supplier"
                                                    readonly
                                                    class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                />
                                                @error('form.supplier_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Pricing -->
                                    <section class="space-y-4">
                                        <div>
                                            <flux:heading size="md" class="text-gray-900 dark:text-white">Pricing</flux:heading>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Define how the product is sold and promoted.</p>
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Product Type</label>
                                                <select
                                                    wire:model="form.product_type"
                                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                >
                                                    <option value="regular">Regular Item</option>
                                                    <option value="sale">Sale Item</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Pricing Note</label>
                                                <select
                                                    wire:model="form.price_note"
                                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                >
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

                                        @php $isSale = ($form['product_type'] ?? 'regular') === 'sale'; @endphp
                                        <div class="flex items-center justify-between rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                            <span>Tagging: Regular = White Tag; Sale = Red Tag.</span>
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $isSale ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                                <span class="mr-1 h-2 w-2 rounded-full {{ $isSale ? 'bg-red-500' : 'bg-gray-500' }}"></span>
                                                {{ $isSale ? 'Red Tag (Sale)' : 'White Tag (Regular)' }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                            <div>
                                                <flux:input
                                                    wire:model="form.price"
                                                    label="Selling Price"
                                                    type="number"
                                                    step="0.01"
                                                    required
                                                    placeholder="0.00"
                                                    class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                />
                                                @error('form.price') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>
                                            <div class="flex items-end justify-end">
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
                                                    class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                />
                                                @error('form.cost') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Advanced Pricing -->
                                    <section class="space-y-4">
                                        <div>
                                            <flux:heading size="md" class="text-gray-900 dark:text-white">Advanced Pricing</flux:heading>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Optional price levels and discount tiers.</p>
                                        </div>

                                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                            <div>
                                                <div class="mb-2 flex items-center justify-between">
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

                                            <div>
                                                <div class="mb-2 flex items-center justify-between">
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
                                    </section>

                                    <!-- Status -->
                                    <section class="space-y-2 border-t border-gray-200 pt-4 dark:border-gray-700">
                                        <flux:heading size="md" class="text-gray-900 dark:text-white">Status</flux:heading>
                                        <div class="flex items-center">
                                            <input
                                                type="checkbox"
                                                wire:model="form.disabled"
                                                id="form.disabled"
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            >
                                            <label for="form.disabled" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                                Disabled
                                            </label>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Check this to disable the product</p>
                                    </section>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 bg-white px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $isEditing ? 'Last saved product details will remain unchanged until you click update.' : 'Review details carefully before creating a new product.' }}
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <flux:button type="button" wire:click="resetForm" variant="ghost">
                                            Reset
                                        </flux:button>

                                        <flux:button type="submit" variant="primary">
                                            {{ $isEditing ? 'Update Product' : 'Create Product' }}
                                        </flux:button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </template>
</div>
