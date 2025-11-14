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
                                                        wire:model="form.product_number"
                                                        label="Product ID"
                                                        required
                                                        placeholder="6-digit Product ID"
                                                        inputmode="numeric"
                                                        pattern="\\d*"
                                                        maxlength="6"
                                                        class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                    />
                                                    @error('form.product_number') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        Enter a 6-digit identifier (leading zeros allowed).
                                                    </p>
                                                </div>

                                                <div>
                                                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Color Code</label>
                                                    <div class="flex flex-col gap-2">
                                                        <div
                                                            x-data="{
                                                                colors: @js($colors),
                                                                open: false,
                                                                search: '',
                                                                selectedId: @entangle('form.product_color_id').live,
                                                                get filtered() {
                                                                    if (!this.search) {
                                                                        return this.colors;
                                                                    }
                                                                    const q = this.search.toLowerCase();
                                                                    return this.colors.filter((color) => {
                                                                        return [
                                                                            color.code ?? '',
                                                                            color.name ?? '',
                                                                            color.shortcut ?? ''
                                                                        ].join(' ').toLowerCase().includes(q);
                                                                    });
                                                                },
                                                                displayLabel(color) {
                                                                    if (!color) return '';
                                                                    return color.shortcut
                                                                        ? `${color.name} - ${color.shortcut}`
                                                                        : color.name;
                                                                },
                                                                select(color) {
                                                                    this.selectedId = String(color.id);
                                                                    this.search = this.displayLabel(color);
                                                                    this.open = false;
                                                                },
                                                                init() {
                                                                    this.$watch('selectedId', (value) => {
                                                                        if (!value) {
                                                                            return;
                                                                        }
                                                                        const existing = this.colors.find((color) => String(color.id) === String(value));
                                                                        if (existing) {
                                                                            this.search = this.displayLabel(existing);
                                                                        }
                                                                    });

                                                                    if (this.selectedId) {
                                                                        const existing = this.colors.find((color) => String(color.id) === String(this.selectedId));
                                                                        if (existing) {
                                                                            this.search = this.displayLabel(existing);
                                                                        }
                                                                    }
                                                                }
                                                            }"
                                                            x-init="init()"
                                                            class="relative"
                                                            x-on:click.away="open = false"
                                                            x-on:keydown.escape.window="open = false"
                                                        >
                                                            <input
                                                                type="text"
                                                                x-model="search"
                                                                @focus="open = true"
                                                                @input="open = true; selectedId = '';"
                                                            class="block h-11 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                                placeholder="Search color by code, name, or shortcut"
                                                                autocomplete="off"
                                                                required
                                                            />

                                                            <div
                                                                x-show="open"
                                                                x-transition
                                                                class="absolute z-30 mt-2 max-h-60 w-full overflow-auto rounded-md border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"
                                                            >
                                                                <template x-if="filtered.length === 0">
                                                                    <p class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                                                                        No colors found. Adjust your search or add a new color.
                                                                    </p>
                                                                </template>

                                                                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                                                                    <template x-for="color in filtered" :key="color.id">
                                                                        <li>
                                                                            <button
                                                                                type="button"
                                                                                class="w-full px-3 py-2 text-left transition hover:bg-indigo-50 dark:hover:bg-indigo-500/20"
                                                                                @click="select(color)"
                                                                            >
                                                                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                                                    <span x-text="color.name"></span>
                                                                                    <span x-show="color.shortcut" x-text="` - ${color.shortcut}`"></span>
                                                                                </p>
                                                                            </button>
                                                                        </li>
                                                                    </template>
                                                                </ul>
                                                            </div>

                                                            <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Selected: <span class="font-medium text-gray-700 dark:text-gray-200" x-text="search || 'None'"></span>
                                                                </p>
                                                                <flux:button type="button" wire:click="startColorCreation" variant="ghost" size="sm">
                                                                    Add Color
                                                                </flux:button>
                                                            </div>
                                                        </div>

                                                        @error('form.product_color_id') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                                                        @if($showColorForm)
                                                            <div class="space-y-2 rounded-md border border-dashed border-gray-300 p-3 dark:border-gray-600">
                                                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                                                                    <flux:input
                                                                        wire:model="colorForm.code"
                                                                        label="Color ID"
                                                                        placeholder="{{ $latestColorCode ? 'Enter next 4-digit ID (last: ' . $latestColorCode . ')' : 'Enter 4-digit ID' }}"
                                                                        inputmode="text"
                                                                        maxlength="8"
                                                                        class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                                    />
                                                                    <div class="sm:col-span-2">
                                                                        <flux:input
                                                                            wire:model="colorForm.name"
                                                                            label="Color Name"
                                                                            placeholder="Enter color name"
                                                                            class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                                        />
                                                                    </div>
                                                                </div>
                                                                <flux:input
                                                                    wire:model="colorForm.shortcut"
                                                                    label="Shortcut (optional)"
                                                                    placeholder="3-5 char symbol"
                                                                    class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                                />

                                                                <div class="flex items-center gap-2 pt-1">
                                                                    <flux:button type="button" wire:click="saveNewColor" variant="primary" size="sm">
                                                                        Save Color
                                                                    </flux:button>
                                                                    <flux:button type="button" wire:click="cancelColorCreation" variant="ghost" size="sm">
                                                                        Cancel
                                                                    </flux:button>
                                                                </div>
                                                                @error('colorForm.code') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                                @error('colorForm.name') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                                @error('colorForm.shortcut') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

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
                                                        placeholder="Auto-generated from Product ID + Color"
                                                        class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                        inputmode="numeric"
                                                        pattern="\\d{10}"
                                                        maxlength="10"
                                                        readonly
                                                    />
                                                    @error('form.barcode') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        <svg class="mr-1 inline h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        10-digit format: first 6 digits = Product ID, last 4 digits = Color ID.
                                                    </p>
                                                </div>
                                            </div>

                                            <div>
                                                <flux:input
                                                    wire:model="form.remarks"
                                                    label="Description"
                                                    readonly
                                                    class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                />
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
                                                class="block h-11 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
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
                                                class="block h-11 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
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
                                                class="block h-11 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
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

                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                            <div class="sm:col-span-1">
                                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                                                <select
                                                    wire:model.live="form.supplier_id"
                                                    class="block h-11 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                >
                                                    <option value="">Select Supplier</option>
                                                    @foreach($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('form.supplier_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>

                                            <div class="sm:col-span-1">
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

                                            <div class="sm:col-span-1">
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
                                                    class="block h-11 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                >
                                                    <option value="regular">Regular Item</option>
                                                    <option value="sale">Sale Item</option>
                                                </select>
                                            </div>
                                            <div>
                                                <flux:input
                                                    wire:model="form.price_note"
                                                    label="Price Note"
                                                    readonly
                                                    class="dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                />
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    Auto-managed (increments when selling price changes). Regular price changes (REG*): {{ $regularPriceChangeCount }} |
                                                    Sale markdowns (SAL*): {{ $salePriceChangeCount }}
                                                </p>
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
                                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Selling Price</label>
                                                <div class="flex items-center gap-2">
                                                    <flux:input
                                                        wire:model="form.price"
                                                        type="number"
                                                        step="0.01"
                                                        required
                                                        placeholder="0.00"
                                                        class="flex-1 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                    />
                                                    <flux:modal.trigger name="product-price-history">
                                                        <flux:button variant="ghost">View Price History</flux:button>
                                                    </flux:modal.trigger>
                                                </div>
                                                @error('form.price') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            @php
                                                $priceContext = ($form['product_type'] ?? 'regular') === 'sale' ? ($lastSalePrice ?? null) : ($lastRegularPrice ?? null);
                                                $priceLabel = ($form['product_type'] ?? 'regular') === 'sale' ? 'Sale' : 'Regular';
                                            @endphp
                                            @if($priceContext && isset($priceContext['price']))
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    Last {{ strtolower($priceLabel) }} price: ₱{{ number_format($priceContext['price'], 2) }}
                                                    @if(!empty($priceContext['note']))
                                                        <span class="font-medium text-gray-600 dark:text-gray-300">({{ $priceContext['note'] }})</span>
                                                    @endif
                                                    @if(!empty($priceContext['changed_at']))
                                                        <span>
                                                            on {{ \Carbon\Carbon::parse($priceContext['changed_at'])->format('d M Y, h:i A') }}
                                                        </span>
                                                    @endif
                                                </p>
                                            @else
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    No previous {{ strtolower($priceLabel) }} price recorded yet.
                                                </p>
                                            @endif
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
