<x-slot:header>Allocation</x-slot:header>
<x-slot:subheader>Manual Stock-In</x-slot:subheader>
<x-slot:headerHref>{{ route('allocation.warehouse') }}</x-slot:headerHref>

<div class="pb-6">
    <div class="w-full flex flex-col gap-4">
        @if($message)
            <div class="rounded-lg p-4 {{ $messageType === 'error' ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200' : 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200' }}">
                {{ $message }}
            </div>
        @endif

        {{-- Select Purchase Order --}}
        <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-visible">
            <div class="px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Select Purchase Order</h2>
                <div class="relative flex-1 min-w-0 w-full" x-data x-on:click.outside="$wire.set('showPoDropdown', false)" x-on:keydown.escape.window="$wire.set('showPoDropdown', false)">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Purchase Order</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="poSearch"
                            wire:focus="$set('showPoDropdown', true)"
                            placeholder="Search by PO # or supplier…"
                            autocomplete="off"
                            aria-label="Search and select Purchase Order"
                            class="block w-full min-h-[38px] h-10 pl-10 pr-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />

                    @if($showPoDropdown)
                        <div class="absolute left-0 right-0 mt-2 z-50 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-xl py-1 max-h-64 overflow-y-auto"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">
                                <p class="px-3 py-1.5 text-[11px] text-gray-400 dark:text-gray-500">
                                    Showing up to 25 matching POs.
                                </p>
                                @forelse($this->availablePurchaseOrders as $po)
                                    <button
                                        type="button"
                                        wire:click="selectPurchaseOrder({{ $po->id }})"
                                        class="flex w-full items-start gap-2 px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 {{ $selectedPurchaseOrderId === $po->id ? 'bg-blue-50 dark:bg-blue-900/30' : '' }} text-gray-900 dark:text-white"
                                    >
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium truncate">
                                                {{ $po->po_num }} — {{ $po->supplier?->name ?? 'N/A' }}
                                            </div>
                                            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                                @if($po->order_date)
                                                    {{ $po->order_date->format('M d, Y') }}
                                                @endif
                                                @if($po->status)
                                                    · Status: {{ $po->status->value ?? $po->status }}
                                                @endif
                                            </div>
                                        </div>
                                        @if($selectedPurchaseOrderId === $po->id)
                                            <svg class="h-4 w-4 text-blue-500 shrink-0" viewBox="0 0 20 20" fill="none">
                                                <path d="M5 11.5L8.5 15L15 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        @endif
                                    </button>
                                @empty
                                    <div class="px-3 py-3 text-xs text-gray-500 dark:text-gray-400">
                                        No purchase orders match your search.
                                    </div>
                                @endforelse
                        </div>
                    @endif
                </div>
                </div>
            </div>
        </div>

        @if($selectedPurchaseOrderId && $this->getSelectedPurchaseOrderProperty())
            @php
                $po = $this->getSelectedPurchaseOrderProperty();
                $products = $this->getProductsForSelectedPOProperty();
                $existingExpected = $this->existingExpected;
                $totalPoQty = (float) $po->productOrders->sum('quantity');
                $totalExpectedQty = (float) $existingExpected->sum('expected_quantity');
                $totalReceivedQty = (float) $existingExpected->sum('received_quantity');
            @endphp

            {{-- PO Summary --}}
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg">
                <div class="px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Purchase Order Summary</h2>
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $po->po_num }} — {{ $po->supplier?->name ?? 'N/A' }}
                            </p>
                            <p class="mt-0.5 text-xs text-gray-600 dark:text-gray-400">
                                @if($po->order_date)
                                    Ordered on {{ $po->order_date->format('M d, Y') }}
                                @endif
                                @if($po->status)
                                    · Status: <span class="font-medium">{{ $po->status->value ?? $po->status }}</span>
                                @endif
                            </p>
                        </div>
                        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs sm:text-sm">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Lines</dt>
                                <dd class="font-semibold text-gray-900 dark:text-white">{{ $products->count() }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">PO Qty (total)</dt>
                                <dd class="font-semibold text-gray-900 dark:text-white">{{ number_format($totalPoQty) }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Expected vs Received</dt>
                                <dd class="font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($totalExpectedQty) }} expected
                                    @if($totalReceivedQty > 0)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">· {{ number_format($totalReceivedQty) }} received</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Product List --}}
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Expected Quantities</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Reserve expected quantities per product so allocation can proceed before physical stock arrives.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2 justify-end shrink-0">
                            <button
                                type="button"
                                wire:click="clearAllExpected"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-blue-500 focus:border-blue-500"
                            >
                                Clear all
                            </button>
                            <button
                                type="button"
                                wire:click="fillAllToPOQty"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Fill from PO qty
                            </button>
                        </div>
                    </div>

                    <div wire:loading.flex wire:target="selectedPurchaseOrderId" class="space-y-3 mt-4">
                        @foreach(range(1, 4) as $i)
                            <div class="h-12 rounded-lg bg-gray-100 dark:bg-gray-700 animate-pulse"></div>
                        @endforeach
                    </div>

                    <div wire:loading.remove wire:target="selectedPurchaseOrderId">
                        @if($products->isEmpty())
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">No products on this PO.</p>
                        @else
                            {{-- Product table: same structure as warehouse/for-dispatch tables --}}
                            <div class="overflow-x-auto border-t border-gray-200 dark:border-gray-700 mt-4">
                                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 min-w-full">
                                <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKU / Color / PO qty</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Expected quantity</th>
                                    </tr>
                                </thead>
                                <tbody wire:loading.class="opacity-60">
                                    @foreach($products as $product)
                                        @php
                                            $record = $existingExpected->get($product->id);
                                            $receivedQty = $record ? (float) $record->received_quantity : 0;
                                            $poQty = (float) $po->productOrders->where('product_id', $product->id)->sum('quantity');
                                        @endphp
                                        <tr wire:key="product-{{ $product->id }}" class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $product->name ?? $product->product_number ?? $product->sku ?? 'Product #' . $product->id }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                {{ $product->product_number ?? $product->sku ?? '—' }}
                                                @if($product->color)
                                                    · {{ $product->color->name ?? '' }}
                                                @endif
                                                · PO qty: {{ number_format($poQty) }}
                                                @if($receivedQty > 0)
                                                    · Received: {{ number_format($receivedQty) }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="{{ (int) $poQty }}"
                                                    step="0.001"
                                                    wire:model="expectedQuantities.{{ $product->id }}"
                                                    class="block w-28 sm:w-32 rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm text-right p-2"
                                                    placeholder="0"
                                                />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="pt-5 flex justify-end border-t border-gray-200 dark:border-gray-700 mt-5">
                        <button
                            type="button"
                            wire:click="saveExpected"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="saveExpected">Save Expected Quantities</span>
                            <span wire:loading wire:target="saveExpected">Saving...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
