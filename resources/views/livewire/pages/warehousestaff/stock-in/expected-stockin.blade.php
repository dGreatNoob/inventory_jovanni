<div class="py-4 sm:py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="mb-6 flex flex-col gap-3">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <a href="{{ route('allocation.warehouse') }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full border border-gray-300 dark:border-zinc-600 text-gray-500 dark:text-gray-300 bg-white dark:bg-zinc-800">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M12.5 15L7.5 10L12.5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="font-medium">Back to Allocation</span>
                </a>
                <span class="text-gray-400 dark:text-gray-600">/</span>
                <span>Manual Expected Stock-In</span>
            </div>

            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                    Manual Expected Stock-In
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                    Reserve expected quantities from an approved Purchase Order so allocation can proceed before physical stock arrives.
                </p>
            </div>
        </div>

        @if($message)
            <div class="mb-4 rounded-lg p-4 {{ $messageType === 'error' ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200' : 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200' }}">
                {{ $message }}
            </div>
        @endif

        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700">
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                @php
                    $currentPo = $this->getSelectedPurchaseOrderProperty();
                @endphp
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Select Purchase Order
                </label>
                <div class="relative">
                    <button
                        type="button"
                        wire:click="$toggle('showPoDropdown')"
                        class="inline-flex w-full items-center justify-between gap-2 rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-left text-sm text-gray-900 dark:text-white shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <span class="flex-1 min-w-0">
                            @if($currentPo)
                                <span class="block truncate font-medium">
                                    {{ $currentPo->po_num }} — {{ $currentPo->supplier?->name ?? 'N/A' }}
                                </span>
                                <span class="mt-0.5 block text-xs text-gray-500 dark:text-gray-300">
                                    @if($currentPo->order_date)
                                        {{ $currentPo->order_date->format('M d, Y') }}
                                    @endif
                                    @if($currentPo->status)
                                        · Status: {{ $currentPo->status->value ?? $currentPo->status }}
                                    @endif
                                </span>
                            @else
                                <span class="block text-gray-400 dark:text-gray-500">
                                    Search and select a Purchase Order…
                                </span>
                            @endif
                        </span>
                        <span class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-300">
                            <svg aria-hidden="true" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 20 20">
                                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </button>

                    @if($showPoDropdown)
                        <div class="absolute z-30 mt-2 w-full rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-lg">
                            <div class="px-3 pt-3 pb-2 border-b border-gray-100 dark:border-zinc-700">
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg aria-hidden="true" class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input
                                        type="text"
                                        wire:model.debounce.400ms="poSearch"
                                        class="block w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 pl-9 pr-3 py-1.5 text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Search by PO # or supplier…"
                                    />
                                </div>
                                <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">
                                    Showing up to 25 matching POs.
                                </p>
                            </div>

                            <div class="max-h-72 overflow-y-auto py-1 text-sm">
                                @forelse($this->availablePurchaseOrders as $po)
                                    <button
                                        type="button"
                                        wire:click="selectPurchaseOrder({{ $po->id }})"
                                        class="flex w-full items-start gap-2 px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-zinc-700 {{ $selectedPurchaseOrderId === $po->id ? 'bg-indigo-50 dark:bg-indigo-900/30' : '' }}"
                                    >
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-gray-900 dark:text-white truncate">
                                                {{ $po->po_num }} — {{ $po->supplier?->name ?? 'N/A' }}
                                            </div>
                                            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-300">
                                                @if($po->order_date)
                                                    {{ $po->order_date->format('M d, Y') }}
                                                @endif
                                                @if($po->status)
                                                    · Status: {{ $po->status->value ?? $po->status }}
                                                @endif
                                            </div>
                                        </div>
                                        @if($selectedPurchaseOrderId === $po->id)
                                            <svg class="h-4 w-4 text-indigo-500" viewBox="0 0 20 20" fill="none">
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
                        </div>
                    @endif
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

                <div class="p-6 space-y-5">
                    <div
                        class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 px-4 py-3"
                    >
                        <div>
                            <p class="text-xs font-semibold tracking-wide text-indigo-700 dark:text-indigo-200 uppercase">
                                Purchase Order Summary
                            </p>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $po->po_num }} — {{ $po->supplier?->name ?? 'N/A' }}
                            </p>
                            <p class="mt-0.5 text-xs text-gray-600 dark:text-gray-300">
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
                                <dd class="font-semibold text-gray-900 dark:text-white">
                                    {{ $products->count() }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">PO Qty (total)</dt>
                                <dd class="font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($totalPoQty) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Expected vs Received</dt>
                                <dd class="font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($totalExpectedQty) }} expected
                                    @if($totalReceivedQty > 0)
                                        <span class="text-xs text-gray-500 dark:text-gray-300">
                                            · {{ number_format($totalReceivedQty) }} received
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Enter the expected quantity per product. Use shortcuts to quickly fill from PO quantity or clear all.
                        </p>
                        <div class="flex flex-wrap gap-2 justify-end">
                            <button
                                type="button"
                                wire:click="clearAllExpected"
                                class="inline-flex items-center px-3 py-1.5 text-xs sm:text-sm rounded-lg border border-gray-300 dark:border-zinc-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700"
                            >
                                Clear all
                            </button>
                            <button
                                type="button"
                                wire:click="fillAllToPOQty"
                                class="inline-flex items-center px-3 py-1.5 text-xs sm:text-sm rounded-lg border border-indigo-600 text-indigo-700 dark:text-indigo-100 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50"
                            >
                                Fill from PO qty
                            </button>
                        </div>
                    </div>

                    <div wire:loading.flex wire:target="selectedPurchaseOrderId" class="space-y-3">
                        @foreach(range(1, 4) as $i)
                            <div class="h-12 rounded-lg bg-gray-100 dark:bg-zinc-700 animate-pulse"></div>
                        @endforeach
                    </div>

                    <div wire:loading.remove wire:target="selectedPurchaseOrderId">
                        @if($products->isEmpty())
                            <p class="text-sm text-gray-500 dark:text-gray-400">No products on this PO.</p>
                        @else
                            <div class="hidden sm:grid grid-cols-[minmax(0,2fr)_minmax(0,1fr)] gap-4 px-1 pb-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                <span>Product</span>
                                <span class="text-right">Expected quantity</span>
                            </div>

                            <div class="space-y-4">
                                @foreach($products as $product)
                                    @php
                                        $record = $existingExpected->get($product->id);
                                        $receivedQty = $record ? (float) $record->received_quantity : 0;
                                        $poQty = (float) $po->productOrders->where('product_id', $product->id)->sum('quantity');
                                    @endphp
                                    <div class="flex flex-col sm:grid sm:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] sm:items-center gap-2 sm:gap-4 py-3 border-b border-gray-100 dark:border-zinc-700 last:border-0">
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-gray-900 dark:text-white truncate">
                                                {{ $product->name ?? $product->product_number ?? $product->sku ?? 'Product #' . $product->id }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $product->product_number ?? $product->sku ?? '' }}
                                                @if($product->color)
                                                    · {{ $product->color->name ?? '' }}
                                                @endif
                                                @if($receivedQty > 0)
                                                    · Received: {{ number_format($receivedQty) }}
                                                @endif
                                                · PO qty: {{ number_format($poQty) }}
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-end gap-2 sm:w-40">
                                            <input
                                                type="number"
                                                min="0"
                                                max="{{ (int) $poQty }}"
                                                step="0.001"
                                                wire:model="expectedQuantities.{{ $product->id }}"
                                                class="block w-28 sm:w-full rounded-lg border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm text-right"
                                                placeholder="0"
                                            />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="pt-2 flex justify-end border-t border-gray-100 dark:border-zinc-700">
                        <button
                            type="button"
                            wire:click="saveExpected"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="saveExpected">Save Expected Quantities</span>
                            <span wire:loading wire:target="saveExpected">Saving...</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
