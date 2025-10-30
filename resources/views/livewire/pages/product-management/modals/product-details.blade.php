<!-- Product Details Modal with Image Viewer Navigation -->
<flux:modal name="product-details" class="max-w-4xl">
    @if($editingProduct)
        <div class="space-y-6 pr-2">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                        @if($editingProduct->primary_image)
                            <img src="{{ asset('storage/photos/' . $editingProduct->primary_image) }}" alt="{{ $editingProduct->name }}" class="h-12 w-12 object-cover">
                        @endif
                    </div>
                    <div>
                        <flux:heading size="lg" class="text-gray-900 dark:text-white">{{ $editingProduct->name }}</flux:heading>
                        <flux:text class="text-gray-600 dark:text-gray-400">SKU: {{ $editingProduct->sku }}</flux:text>
                    </div>
                </div>
                @php $isSale = str($editingProduct->price_note ?? '')->startsWith('SAL'); @endphp
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $isSale ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                    <span class="w-2 h-2 rounded-full mr-1 {{ $isSale ? 'bg-red-500' : 'bg-gray-500' }}"></span>
                    {{ $isSale ? 'Red Tag (Sale)' : 'White Tag (Regular)' }}
                </span>
            </div>

            <!-- Main Content: Image with Prev/Next -->
            <div class="relative text-center bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                @if($viewingImage)
                    <button type="button" wire:click="viewerPrev" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/70 dark:bg-gray-800/70 hover:bg-white dark:hover:bg-gray-800 rounded-full p-2 shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <img src="{{ $viewingImage->url }}" alt="{{ $editingProduct->name }}" class="max-w-full max-h-96 mx-auto rounded-lg shadow-lg object-contain">
                    <button type="button" wire:click="viewerNext" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/70 dark:bg-gray-800/70 hover:bg-white dark:hover:bg-gray-800 rounded-full p-2 shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                @else
                    <div class="py-16 text-gray-400">No images for this product.</div>
                @endif
            </div>

            <!-- Details Grids -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Classification</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Category</dt><dd class="text-gray-900 dark:text-white">{{ $editingProduct->category->name ?? 'N/A' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Supplier</dt><dd class="text-gray-900 dark:text-white">{{ $editingProduct->supplier->name ?? 'N/A' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Supplier Code</dt><dd class="text-gray-900 dark:text-white">{{ $editingProduct->supplier_code ?: '—' }}</dd></div>
                    </dl>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Pricing</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Price</dt><dd class="text-gray-900 dark:text-white">₱{{ number_format($editingProduct->price, 2) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Cost</dt><dd class="text-gray-900 dark:text-white">₱{{ number_format($editingProduct->cost, 2) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Product Type</dt><dd class="text-gray-900 dark:text-white">{{ $isSale ? 'Sale' : 'Regular' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Pricing Note</dt><dd class="text-gray-900 dark:text-white">{{ $editingProduct->price_note ?: '—' }}</dd></div>
                    </dl>
                    <div class="mt-3 flex justify-end">
                        <flux:modal.trigger name="product-price-history">
                            <flux:button variant="ghost" size="sm">View Price History</flux:button>
                        </flux:modal.trigger>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Inventory</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Total Qty</dt><dd class="text-gray-900 dark:text-white">{{ number_format($editingProduct->total_quantity) }} {{ $editingProduct->uom }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Shelf Life</dt><dd class="text-gray-900 dark:text-white">{{ $editingProduct->shelf_life_days ?: 'N/A' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Disabled</dt><dd class="text-gray-900 dark:text-white">{{ $editingProduct->disabled ? 'Yes' : 'No' }}</dd></div>
                    </dl>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Identifiers</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500 dark:text-gray-400">Barcode</dt><dd class="text-gray-900 dark:text-white">{{ $editingProduct->barcode ?: '—' }}</dd></div>
                    </dl>
                </div>
            </div>

            @if($editingProduct->remarks)
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Description</h4>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $editingProduct->remarks }}</p>
                </div>
            @endif
        </div>
    @endif
</flux:modal>


