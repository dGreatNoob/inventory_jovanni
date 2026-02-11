{{--
    Unified PO Items Table Partial

    Props:
    - $items: Paginated collection of product orders
    - $variant: 'pending' | 'approved' | 'to_receive' | 'received'
    - $currencySymbol: Currency symbol (e.g. '₱')
    - $totals: Array with keys: count, quantity, expected, received, destroyed, price
--}}
@php
    $variant = $variant ?? 'pending';
    $currencySymbol = $currencySymbol ?? '₱';
    $totals = $totals ?? [];
@endphp

<div class="space-y-0" x-data="{
    search: '',
    visibleCount: {{ $items->count() }},
    totalCount: {{ $items->count() }},
    filterRow(row) {
        if (this.search.trim() === '') return true;

        const searchTerm = this.search.toLowerCase();
        const rowText = row.textContent.toLowerCase();

        return rowText.includes(searchTerm);
    },
    updateVisibleCount() {
        this.$nextTick(() => {
            const rows = this.$refs.tableBody.querySelectorAll('tr[data-item-row]');
            this.visibleCount = Array.from(rows).filter(row => row.style.display !== 'none').length;
        });
    }
}" x-init="$watch('search', () => updateVisibleCount())">
    {{-- Sticky Summary Bar --}}
    <div class="sticky top-[72px] z-[9] bg-gradient-to-r from-zinc-50 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900 border-b border-zinc-300 dark:border-zinc-600 shadow-sm">
        <div class="flex items-center gap-6 px-6 py-3 text-sm flex-wrap">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span class="font-semibold text-zinc-900 dark:text-white" x-text="search ? visibleCount : {{ $items->count() }}"></span>
                <span class="text-zinc-600 dark:text-zinc-400" x-text="search ? (visibleCount === 1 ? 'Item Shown' : 'Items Shown') : 'Total Items'"></span>
                <template x-if="search && visibleCount < totalCount">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">of <span x-text="totalCount"></span></span>
                </template>
            </div>

            <div class="h-5 w-px bg-zinc-300 dark:bg-zinc-600"></div>

            <div class="flex items-center gap-2">
                <span class="text-zinc-600 dark:text-zinc-400">Qty:</span>
                <span class="font-semibold text-zinc-900 dark:text-white">{{ number_format($totals['quantity'] ?? 0) }}</span>
            </div>

            @if($variant !== 'pending')
                <div class="h-5 w-px bg-zinc-300 dark:bg-zinc-600"></div>
                <div class="flex items-center gap-2">
                    <span class="text-zinc-600 dark:text-zinc-400">Expected:</span>
                    <span class="font-semibold text-blue-600 dark:text-blue-400">{{ number_format($totals['expected'] ?? 0) }}</span>
                </div>
            @endif

            @if($variant === 'received' || $variant === 'to_receive')
                <div class="h-5 w-px bg-zinc-300 dark:bg-zinc-600"></div>
                <div class="flex items-center gap-2">
                    <span class="text-zinc-600 dark:text-zinc-400">Received:</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($totals['received'] ?? 0) }}</span>
                </div>
            @endif

            @if($variant === 'received' && ($totals['destroyed'] ?? 0) > 0)
                <div class="h-5 w-px bg-zinc-300 dark:bg-zinc-600"></div>
                <div class="flex items-center gap-2">
                    <span class="text-zinc-600 dark:text-zinc-400">Destroyed:</span>
                    <span class="font-semibold text-red-600 dark:text-red-400">{{ number_format($totals['destroyed'] ?? 0) }}</span>
                </div>
            @endif

            <div class="h-5 w-px bg-zinc-300 dark:bg-zinc-600"></div>

            <div class="flex items-center gap-2">
                <span class="text-zinc-600 dark:text-zinc-400">Total Price:</span>
                <span class="font-bold text-lg text-zinc-900 dark:text-white">{{ $currencySymbol }}{{ number_format($totals['price'] ?? 0, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Search + Items Per Page Selector --}}
    <div class="flex items-center justify-between gap-4 px-6 py-4 bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
        <div class="flex-1 max-w-md">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input
                    type="text"
                    x-model="search"
                    placeholder="Search products, SKU, supplier code, category..."
                    class="block w-full pl-10 pr-10 py-2 text-sm text-zinc-900 border border-zinc-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-zinc-700 dark:border-zinc-600 dark:placeholder-zinc-400 dark:text-white transition-all">
                <button
                    type="button"
                    x-show="search.length > 0"
                    @click="search = ''"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300"
                    x-transition>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <label for="items-per-page" class="text-sm text-zinc-600 dark:text-zinc-400 whitespace-nowrap">Items per page:</label>
            <select
                id="items-per-page"
                wire:model.live="itemsPerPage"
                class="block py-2 pl-3 pr-10 text-sm border-zinc-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white transition-all">
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    {{-- Bounded Scroll Container with Sticky Table Header --}}
    <div class="max-h-[60vh] overflow-y-auto overflow-x-auto relative border-b border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
            <thead class="sticky top-0 z-[1] text-xs text-zinc-700 uppercase bg-zinc-100 dark:bg-zinc-700 dark:text-zinc-300 shadow-sm">
                <tr>
                    <th scope="col" class="px-5 py-3 text-left font-semibold">Product Name</th>
                    <th scope="col" class="px-5 py-3 text-left font-semibold">SKU</th>
                    <th scope="col" class="px-5 py-3 text-left font-semibold">Supplier Code</th>
                    <th scope="col" class="px-5 py-3 text-left font-semibold">Category</th>

                    @if($variant === 'pending')
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Unit Price</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Quantity</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Total Price</th>
                    @elseif($variant === 'approved')
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Unit Price</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Ordered Qty</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Expected Qty</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Total Price</th>
                    @elseif($variant === 'to_receive')
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Unit Price</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Expected Qty</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Received Qty</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Total Price</th>
                    @elseif($variant === 'received')
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Unit Price</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Expected Qty</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Received Qty</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Destroyed Qty</th>
                        <th scope="col" class="px-5 py-3 text-center font-semibold">Status</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Total Price</th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700" x-ref="tableBody">
                @forelse($items as $order)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150"
                        data-item-row
                        x-show="filterRow($el)"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100">
                        <td class="px-5 py-4 font-medium text-zinc-900 dark:text-white">
                            {{ $order->product->name ?? 'N/A' }}
                        </td>
                        <td class="px-5 py-4 text-zinc-700 dark:text-zinc-300">
                            {{ $order->product->sku ?? 'N/A' }}
                        </td>
                        <td class="px-5 py-4 text-zinc-700 dark:text-zinc-300">
                            {{ $order->product->supplier->code ?? 'N/A' }}
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                {{ $order->product->category->name ?? 'N/A' }}
                            </span>
                        </td>

                        @if($variant === 'pending')
                            <td class="px-5 py-4 text-right text-zinc-700 dark:text-zinc-300">
                                {{ $currencySymbol }}{{ number_format($order->unit_price ?? 0, 2) }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-zinc-900 dark:text-white">
                                {{ number_format($order->quantity ?? 0) }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-zinc-900 dark:text-white">
                                {{ $currencySymbol }}{{ number_format($order->total_price ?? 0, 2) }}
                            </td>
                        @elseif($variant === 'approved')
                            <td class="px-5 py-4 text-right text-zinc-700 dark:text-zinc-300">
                                {{ $currencySymbol }}{{ number_format($order->unit_price ?? 0, 2) }}
                            </td>
                            <td class="px-5 py-4 text-right text-zinc-700 dark:text-zinc-300">
                                {{ number_format($order->quantity ?? 0) }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-blue-600 dark:text-blue-400">
                                {{ number_format($order->expected_qty ?? $order->quantity ?? 0) }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-zinc-900 dark:text-white">
                                {{ $currencySymbol }}{{ number_format($order->total_price ?? 0, 2) }}
                            </td>
                        @elseif($variant === 'to_receive')
                            <td class="px-5 py-4 text-right text-zinc-700 dark:text-zinc-300">
                                {{ $currencySymbol }}{{ number_format($order->unit_price ?? 0, 2) }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-blue-600 dark:text-blue-400">
                                {{ number_format($order->expected_qty ?? $order->quantity ?? 0) }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-green-600 dark:text-green-400">
                                {{ number_format($order->received_quantity ?? 0) }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-zinc-900 dark:text-white">
                                {{ $currencySymbol }}{{ number_format($order->total_price ?? 0, 2) }}
                            </td>
                        @elseif($variant === 'received')
                            <td class="px-5 py-4 text-right text-zinc-700 dark:text-zinc-300">
                                {{ $currencySymbol }}{{ number_format($order->unit_price ?? 0, 2) }}
                            </td>
                            <td class="px-5 py-4 text-right text-blue-600 dark:text-blue-400">
                                {{ number_format($order->expected_qty ?? $order->quantity ?? 0) }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-green-600 dark:text-green-400">
                                {{ number_format($order->received_quantity ?? 0) }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-red-600 dark:text-red-400">
                                {{ number_format($order->destroyed_quantity ?? 0) }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @php
                                    $receivedQty = $order->received_quantity ?? 0;
                                    $expectedQty = $order->expected_qty ?? $order->quantity ?? 0;
                                    $status = $receivedQty >= $expectedQty ? 'Complete' : ($receivedQty > 0 ? 'Partial' : 'Pending');
                                    $badgeColor = $status === 'Complete' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($status === 'Partial' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300');
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-zinc-900 dark:text-white">
                                {{ $currencySymbol }}{{ number_format($order->total_price ?? 0, 2) }}
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="20" class="px-6 py-16">
                            <div class="text-center">
                                <svg class="w-16 h-16 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">
                                    No items in this purchase order
                                </h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    This PO doesn't have any items yet
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse

                {{-- No Results from Filter --}}
                <tr x-show="search && visibleCount === 0" x-cloak>
                    <td colspan="20" class="px-6 py-16">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">
                                No items match "<span x-text="search" class="font-semibold text-indigo-600 dark:text-indigo-400"></span>"
                            </h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4">
                                Try adjusting your search terms
                            </p>
                            <button
                                @click="search = ''"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear search
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Pagination Controls --}}
    @if($items->hasPages())
        <div class="px-6 py-4 bg-white dark:bg-zinc-800 border-t border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">
                    <span x-show="!search">
                        Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} items
                    </span>
                    <span x-show="search" x-cloak>
                        <span x-text="visibleCount"></span> <span x-text="visibleCount === 1 ? 'item' : 'items'"></span> match your search on this page
                    </span>
                </div>
                <div>
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
