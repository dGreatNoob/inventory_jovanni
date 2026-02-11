<x-slot:header>Purchase Order</x-slot:header>
<x-slot:subheader>Create Purchase Order</x-slot:subheader>

<div class="pb-6">
    {{-- Flash Messages --}}
    @if (session()->has('error'))
        <x-flash-message type="error" :message="session('error')" />
    @endif

    @if (session()->has('success'))
        <x-flash-message type="success" :message="session('success')" />
    @endif

    {{-- Two-Column Layout: Main (70%) + Sidebar (30%) --}}
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-6">

        {{-- ============================================
            LEFT COLUMN (70%): Main Content
        ============================================ --}}
        <div class="lg:col-span-7 space-y-6">

            {{-- PO Details: 2×3 Grid (Input-style) --}}
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Purchase Order Details</h3>

                    <form wire:submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- PO Number (readonly) --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">PO Number</label>
                                <div class="bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2.5 text-sm text-zinc-500 dark:text-zinc-400">
                                    Auto-generated on save
                                </div>
                            </div>

                            {{-- Ordered By (readonly) --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Ordered By</label>
                                <div class="bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $ordered_by }}
                                </div>
                            </div>

                            {{-- Supplier (searchable, editable) --}}
                            <div class="md:col-span-2"
                                x-data="{
                                    open: false,
                                    search: '',
                                    selectedId: @entangle('supplier_id').live,
                                    options: @js($suppliers->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->toArray()),
                                    get filtered() {
                                        if (!this.search) return this.options;
                                        return this.options.filter(o =>
                                            o.name.toLowerCase().includes(this.search.toLowerCase())
                                        );
                                    },
                                    selectedLabel() {
                                        const found = this.options.find(o => o.id === this.selectedId);
                                        return found ? found.name : '';
                                    },
                                    select(option) {
                                        this.selectedId = option.id;
                                        this.search = '';
                                        this.open = false;
                                        $wire.set('supplier_id', option.id);
                                    }
                                }"
                            >
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    Supplier <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        x-model="search"
                                        @focus="open = true"
                                        @click.away="open = false"
                                        :placeholder="selectedLabel() || 'Search supplier...'"
                                        class="bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full px-4 py-2.5 transition-all"
                                    />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>

                                    <div
                                        x-show="open"
                                        x-transition
                                        class="absolute z-20 w-full mt-1 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                                    >
                                        <template x-if="filtered.length === 0">
                                            <div class="px-3 py-2 text-sm text-zinc-500 dark:text-zinc-400">
                                                No suppliers found
                                            </div>
                                        </template>
                                        <template x-for="option in filtered" :key="option.id">
                                            <button
                                                type="button"
                                                class="w-full text-left px-3 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700 flex items-center justify-between transition-colors"
                                                @click="select(option)"
                                            >
                                                <span x-text="option.name" class="text-zinc-900 dark:text-white"></span>
                                                <span
                                                    x-show="option.id === selectedId"
                                                    class="text-xs text-indigo-600 dark:text-indigo-400"
                                                >
                                                    Selected
                                                </span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                @error('supplier_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Currency (from supplier, readonly) --}}
                            @if($selectedCurrency)
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Currency</label>
                                    <div class="bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300">
                                        {{ $selectedCurrency->name }} ({{ $selectedCurrency->symbol }})
                                    </div>
                                </div>
                            @endif

                            {{-- Receiving Department (editable) --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    Receiving Department <span class="text-red-500">*</span>
                                </label>
                                <select
                                    wire:model="deliver_to"
                                    class="bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full px-4 py-2.5">
                                    <option value="">Select department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                @error('deliver_to')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Order Date (editable) --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    Order Date <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    wire:model="order_date"
                                    class="bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full px-4 py-2.5"
                                />
                                @error('order_date')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Expected Delivery Date (editable) --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Expected Delivery Date</label>
                                <input
                                    type="date"
                                    wire:model="expected_delivery_date"
                                    class="bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full px-4 py-2.5"
                                />
                                @error('expected_delivery_date')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Order Items Table --}}
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Order Items</h3>
                        <button
                            type="button"
                            wire:click="openModal"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Item
                        </button>
                    </div>

                    {{-- Items Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
                            <thead class="text-xs text-zinc-700 uppercase bg-zinc-100 dark:bg-zinc-800 dark:text-zinc-300">
                                <tr>
                                    <th scope="col" class="px-5 py-3">Product</th>
                                    <th scope="col" class="px-5 py-3 text-right">Unit Price</th>
                                    <th scope="col" class="px-5 py-3 text-right">Quantity</th>
                                    <th scope="col" class="px-5 py-3 text-right">Line Total</th>
                                    <th scope="col" class="px-5 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @forelse($paginatedOrderedItems as $index => $item)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                        <td class="px-5 py-4">
                                            <div class="font-medium text-zinc-900 dark:text-white">
                                                {{ $item['name'] ?? 'N/A' }}
                                            </div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 space-x-2">
                                                @if(!empty($item['product_number']))
                                                    <span class="font-mono">ID: {{ $item['product_number'] }}</span>
                                                @endif
                                                @if(!empty($item['sku']))
                                                    <span class="font-mono">SKU: {{ $item['sku'] }}</span>
                                                @endif
                                            </div>
                                            @if(!empty($item['color']))
                                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                                    {{ $item['color'] }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-right text-zinc-700 dark:text-zinc-300">
                                            {{ $selectedCurrency?->symbol ?? '₱' }}{{ number_format($item['unit_price'] ?? 0, 2) }}
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200">
                                                {{ number_format($item['order_qty'] ?? 0, 2) }} {{ $item['uom'] ?? 'pcs' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-right font-semibold text-zinc-900 dark:text-white">
                                            {{ $selectedCurrency?->symbol ?? '₱' }}{{ number_format($item['total_price'] ?? 0, 2) }}
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            <button
                                                type="button"
                                                wire:click="removeItem({{ count($orderedItems) - 1 - ($index + (($orderedItemsPage - 1) * $orderedItemsPerPage)) }})"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <svg class="w-16 h-16 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="text-lg font-medium text-zinc-900 dark:text-white mb-2">No Items Added</p>
                                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                                Click "Add Item" to start building your purchase order
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination (if needed) --}}
                    @if($orderedItemsTotalPages > 1)
                        <div class="mt-4 flex items-center justify-between px-4">
                            <div class="flex items-center space-x-2">
                                <label class="text-sm text-zinc-600 dark:text-zinc-400">Per Page:</label>
                                <select
                                    wire:model.live="orderedItemsPerPage"
                                    class="bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 text-sm rounded-lg px-3 py-1.5">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    wire:click="previousOrderedItemsPage"
                                    :disabled="$orderedItemsPage <= 1"
                                    class="px-3 py-1.5 text-sm bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                    Previous
                                </button>
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">
                                    Page {{ $orderedItemsPage }} of {{ $orderedItemsTotalPages }}
                                </span>
                                <button
                                    type="button"
                                    wire:click="nextOrderedItemsPage"
                                    :disabled="$orderedItemsPage >= $orderedItemsTotalPages"
                                    class="px-3 py-1.5 text-sm bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                    Next
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ============================================
            RIGHT COLUMN (30%): Sticky Sidebar
        ============================================ --}}
        <div class="lg:col-span-3">
            <div class="sticky top-6 space-y-6">

                {{-- Order Summary Card --}}
                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Order Summary</h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Total Items</span>
                                <span class="text-lg font-bold text-zinc-900 dark:text-white">{{ count($orderedItems) }}</span>
                            </div>

                            <div class="flex justify-between items-center p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Total Quantity</span>
                                <span class="text-lg font-bold text-zinc-900 dark:text-white">{{ number_format($this->totalQuantity, 2) }}</span>
                            </div>

                            <div class="flex justify-between items-center p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200 dark:border-indigo-800">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Total Cost</span>
                                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $selectedCurrency?->symbol ?? '₱' }}{{ number_format($this->totalAmount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Item Breakdown Card --}}
                @if(!empty($orderedItems))
                    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Item Breakdown</h3>

                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($orderedItems as $item)
                                    <div class="pb-3 border-b border-zinc-200 dark:border-zinc-700 last:border-0">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white mb-1">
                                            {{ $item['name'] ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 flex items-center justify-between">
                                            <span>{{ number_format($item['order_qty'] ?? 0, 2) }} × {{ $selectedCurrency?->symbol ?? '₱' }}{{ number_format($item['unit_price'] ?? 0, 2) }}</span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">
                                                {{ $selectedCurrency?->symbol ?? '₱' }}{{ number_format($item['total_price'] ?? 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 pt-4 border-t-2 border-zinc-300 dark:border-zinc-600">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-zinc-900 dark:text-white">Total Cost:</span>
                                    <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ $selectedCurrency?->symbol ?? '₱' }}{{ number_format($this->totalAmount, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Primary Actions --}}
                <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm">
                    <div class="p-6 space-y-3">
                        <button
                            type="button"
                            wire:click="submit"
                            :disabled="empty($orderedItems)"
                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-zinc-300 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Submit Purchase Order
                        </button>

                        <a
                            href="{{ route('pomanagement.purchaseorder') }}"
                            class="w-full inline-flex items-center justify-center px-6 py-2.5 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-sm font-medium rounded-lg transition-colors">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Item Modal (keep existing modal logic) --}}
    <x-modal wire:model="showModal" class="max-w-4xl max-h-[90vh]">
        {{-- Modal content stays the same as original - just keeping the existing modal --}}
        @include('livewire.pages.POmanagement.purchase-order.partials.add-item-modal')
    </x-modal>
</div>
