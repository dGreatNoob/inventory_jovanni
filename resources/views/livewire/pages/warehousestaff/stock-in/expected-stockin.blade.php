<div class="py-4 sm:py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Expected Stock-In</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Record expected quantities from a Purchase Order. These will be available for allocation before physical stock arrives.
            </p>
        </div>

        @if($message)
            <div class="mb-4 rounded-lg p-4 {{ $messageType === 'error' ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200' : 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200' }}">
                {{ $message }}
            </div>
        @endif

        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                <label for="po-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Select Purchase Order
                </label>
                <select
                    id="po-select"
                    wire:model.live="selectedPurchaseOrderId"
                    class="w-full rounded-lg border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="">-- Choose a PO --</option>
                    @foreach($this->availablePurchaseOrders as $po)
                        <option value="{{ $po->id }}">
                            {{ $po->po_num }} — {{ $po->supplier?->name ?? 'N/A' }}
                            @if($po->order_date)
                                ({{ $po->order_date->format('M d, Y') }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            @if($selectedPurchaseOrderId && $this->getSelectedPurchaseOrderProperty())
                @php
                    $po = $this->getSelectedPurchaseOrderProperty();
                    $products = $this->getProductsForSelectedPOProperty();
                    $existingExpected = $this->existingExpected;
                @endphp

                <div class="p-6">
                    @if($products->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">No products on this PO.</p>
                    @else
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            Enter expected quantity per product (0 to clear):
                        </p>
                        <div class="space-y-4">
                            @foreach($products as $product)
                                @php
                                    $record = $existingExpected->get($product->id);
                                    $receivedQty = $record ? (float) $record->received_quantity : 0;
                                @endphp
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 py-3 border-b border-gray-100 dark:border-zinc-700 last:border-0">
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
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 sm:w-40">
                                        <input
                                            type="number"
                                            min="0"
                                            step="0.001"
                                            wire:model="expectedQuantities.{{ $product->id }}"
                                            class="block w-full rounded-lg border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                            placeholder="0"
                                        />
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex justify-end">
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
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
