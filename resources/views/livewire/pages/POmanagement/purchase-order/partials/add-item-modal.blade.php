{{-- Add Item Modal Content --}}
{{-- Header --}}
<div class="flex items-start justify-between mb-4">
    <div>
        <h3 class="text-xl font-semibold text-zinc-900 dark:text-white">
            Add Item to Purchase Order
        </h3>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
            Search existing products or add new product by supplier code (creates placeholder if needed)
        </p>
    </div>
    <button
        type="button"
        wire:click="closeModal"
        class="ml-3 inline-flex items-center rounded-full p-2 text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100 dark:text-zinc-500 dark:hover:text-zinc-300 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-indigo-400"
    >
        <span class="sr-only">Close</span>
        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
        </svg>
    </button>
</div>

{{-- Tabs: Search | Add new Product --}}
<div class="flex border-b border-zinc-200 dark:border-zinc-600 mb-4">
    <button
        type="button"
        wire:click="$set('addMode', 'search')"
        class="px-4 py-2 text-sm font-medium rounded-t-lg {{ $addMode === 'search' ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border-b-2 border-indigo-500' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}"
    >
        Search existing product
    </button>
    <button
        type="button"
        wire:click="$set('addMode', 'supplier_code')"
        class="px-4 py-2 text-sm font-medium rounded-t-lg {{ $addMode === 'supplier_code' ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border-b-2 border-indigo-500' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}"
    >
        Add new Product
    </button>
</div>

@if($addMode === 'supplier_code')
    {{-- Add new Product form --}}
    <div class="space-y-4">
        <div>
            <label for="addBySupplierCode" class="block mb-2 text-sm font-medium text-zinc-900 dark:text-white">Supplier Code</label>
            <input
                type="text"
                id="addBySupplierCode"
                wire:model.live="addBySupplierCode"
                placeholder="Enter supplier code (e.g. ABC-123)"
                class="block w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 p-2.5 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20 dark:placeholder-zinc-400"
            />
            @error('addBySupplierCode') <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="addNewProductColor" class="block mb-2 text-sm font-medium text-zinc-900 dark:text-white">Color Code</label>
            <div class="relative" x-data x-on:click.outside="$wire.set('addNewProductColorDropdown', false)" x-on:keydown.escape.window="$wire.set('addNewProductColorDropdown', false)">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                        <svg aria-hidden="true" class="h-5 w-5 text-zinc-400 dark:text-zinc-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        id="addNewProductColor"
                        wire:model.live.debounce.300ms="addNewProductColorSearch"
                        wire:focus="$set('addNewProductColorDropdown', true)"
                        placeholder="Search by color code or name…"
                        autocomplete="off"
                        aria-label="Search and select color"
                        class="block w-full min-h-[38px] h-10 pl-10 pr-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm text-zinc-900 dark:text-white placeholder-zinc-500 dark:placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400"
                    />
                    @if($addNewProductColorDropdown)
                        <div class="absolute z-30 mt-2 w-full max-h-48 overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-600 bg-white dark:bg-zinc-800 shadow-lg py-1">
                            @forelse($this->filteredCreateColors as $color)
                                <button
                                    type="button"
                                    wire:click="selectCreateColor({{ $color->id }})"
                                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-zinc-50 dark:hover:bg-zinc-700 {{ $addNewProductColorId == $color->id ? 'bg-indigo-50 dark:bg-indigo-900/30' : 'text-zinc-900 dark:text-white' }}"
                                >
                                    <span class="flex-1 min-w-0 font-medium truncate">{{ $color->code }}{{ $color->name ? ' - ' . $color->name : '' }}</span>
                                    @if($addNewProductColorId == $color->id)
                                        <svg class="h-4 w-4 text-indigo-500 shrink-0" viewBox="0 0 20 20" fill="none">
                                            <path d="M5 11.5L8.5 15L15 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @endif
                                </button>
                            @empty
                                <div class="px-3 py-3 text-xs text-zinc-500 dark:text-zinc-400">
                                    No colors match your search.
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label for="supplier_code_unit_price" class="block mb-2 text-sm font-medium text-zinc-900 dark:text-white">Unit Price ({{ $selectedCurrency?->symbol ?? '₱' }})</label>
                <input
                    type="number"
                    id="supplier_code_unit_price"
                    wire:model.live="unit_price"
                    step="0.01"
                    min="0"
                    class="block w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 p-2.5 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
                />
                @error('unit_price') <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="addNewProductSellingPrice" class="block mb-2 text-sm font-medium text-zinc-900 dark:text-white">Selling Price (₱)</label>
                <input
                    type="number"
                    id="addNewProductSellingPrice"
                    wire:model.live="addNewProductSellingPrice"
                    step="0.01"
                    min="0"
                    placeholder="Optional"
                    class="block w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 p-2.5 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:placeholder-zinc-400 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
                />
                @error('addNewProductSellingPrice') <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="supplier_code_order_qty" class="block mb-2 text-sm font-medium text-zinc-900 dark:text-white">Order Quantity</label>
                <input
                    type="number"
                    id="supplier_code_order_qty"
                    wire:model.live="order_qty"
                    step="0.01"
                    min="0.01"
                    class="block w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 p-2.5 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
                />
                @error('order_qty') <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
@else
{{-- Search and Filters --}}
<div class="grid gap-4 mb-4 md:grid-cols-2">
    <div>
        <label class="block mb-2 text-sm font-medium text-zinc-900 dark:text-white">Search Product</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-400" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 dark:placeholder-zinc-400 dark:focus:ring-indigo-400 dark:focus:border-indigo-400"
                placeholder="Search by name, SKU, Product ID, or Supplier Code..."
            />
        </div>
    </div>
    <div>
        <label class="block mb-2 text-sm font-medium text-zinc-900 dark:text-white">Category</label>
        <select
            wire:model.live="categoryFilter"
            class="bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 pr-10 appearance-none dark:focus:ring-indigo-400 dark:focus:border-indigo-400"
        >
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- Product List --}}
<div class="border border-zinc-200 dark:border-zinc-600 rounded-lg overflow-hidden">
    <div class="max-h-64 overflow-y-auto">
        @forelse($products as $product)
            <div
                wire:click="selectProduct({{ $product->id }})"
                class="p-4 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 cursor-pointer border-b border-zinc-200 dark:border-zinc-600 last:border-b-0 {{ $selected_product == $product->id ? 'bg-indigo-50 dark:bg-indigo-900/30 border-l-4 border-indigo-500' : '' }}"
            >
                <div class="flex justify-between items-start">
                    <div class="flex-1 space-y-1.5">
                        <div class="font-medium text-zinc-900 dark:text-white">
                            {{ $product->remarks ?? $product->name }}
                            @if ($product->color)
                                <span class="text-zinc-500 dark:text-zinc-400">
                                    · {{ $product->color->name ?? $product->color->code }}
                                </span>
                            @endif
                        </div>
                        <div class="text-sm font-mono text-zinc-600 dark:text-zinc-400">
                            ID: {{ $product->product_number ?? '—' }}
                        </div>
                        <div class="text-sm font-mono text-zinc-600 dark:text-zinc-400">
                            SKU: {{ $product->sku ?? '—' }}
                        </div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                            Supplier Code: {{ $product->supplier_code ?? '—' }}
                        </div>
                        @if ($product->category)
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $product->category->name }}
                            </div>
                        @endif
                    </div>
                    <div class="text-right ml-4 flex-shrink-0">
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Cost</div>
                        <div class="font-semibold text-zinc-900 dark:text-white">
                            ₱{{ number_format($product->cost, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-zinc-500 dark:text-zinc-400">
                <svg class="w-12 h-12 mx-auto mb-2 text-zinc-400 dark:text-zinc-500" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p>No products found</p>
            </div>
        @endforelse
    </div>
</div>
<div class="mt-3">
    {{ $products->links() }}
</div>

{{-- Quantity and Price Inputs (search mode) --}}
@if($selected_product)
    <div class="mt-4 border-t border-zinc-200 dark:border-zinc-600 pt-4">
        <div class="mb-3 text-sm text-zinc-700 dark:text-zinc-300">
            <span class="font-medium">Selected product:</span>
            <span class="ml-1">
                {{-- Display basic identifier info from the currently selected row --}}
                {{ optional($products->firstWhere('id', $selected_product))->remarks
                    ?? optional($products->firstWhere('id', $selected_product))->name }}
            </span>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
        <x-input
            type="number"
            name="unit_price"
            :label="'Unit Price (' . ($selectedCurrency?->symbol ?? '₱') . ')'"
            wire:model="unit_price"
            step="0.01"
            required="true"
        />
        <x-input
            type="number"
            name="order_qty"
            label="Order Quantity"
            wire:model="order_qty"
            step="0.01"
            required="true"
        />
        </div>
    </div>
@endif
@endif

{{-- Modal Footer --}}
<div class="mt-6 flex items-center justify-end space-x-3">
    <x-button type="button" variant="secondary" size="md" wire:click="closeModal">
        Cancel
    </x-button>
    @if($addMode === 'supplier_code')
        <x-button
            type="button"
            variant="primary"
            size="md"
            wire:click="addItemBySupplierCode"
            wire:loading.attr="disabled"
            :disabled="empty(trim($addBySupplierCode ?? '')) || (float)($order_qty ?? 0) < 0.01 || (float)($unit_price ?? -1) < 0"
        >
            Add Item
        </x-button>
    @else
        <x-button
            type="button"
            variant="primary"
            size="md"
            wire:click="addItem"
            :disabled="!$selected_product"
        >
            Add Item
        </x-button>
    @endif
</div>
