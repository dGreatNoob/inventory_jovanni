<!-- Products Grid View -->
<div class="p-6">
    @if(count($products) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @foreach($products as $product)
                <div class="group relative bg-white dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 overflow-hidden">
                    <!-- Selection Checkbox -->
                    <div class="absolute top-2 left-2 z-10">
                        <input type="checkbox" 
                               wire:click="toggleProductSelection({{ $product->id }})"
                               @if(in_array($product->id, $selectedProducts)) checked @endif
                               class="h-4 w-4 text-gray-600 focus:ring-gray-500 dark:focus:ring-gray-400 border-gray-300 rounded">
                    </div>

                    <!-- Stock Status Badge -->
                    <div class="absolute top-2 right-2 z-10">
                        @if($product->total_quantity <= 0)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                Out of Stock
                            </span>
                        @elseif($product->total_quantity < 10)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                Low Stock
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                In Stock
                            </span>
                        @endif
                    </div>

                    <!-- Product Image -->
                    <flux:modal.trigger name="product-details">
                    <div class="aspect-square bg-gray-100 dark:bg-gray-600 overflow-hidden cursor-pointer" wire:click="openProductViewer({{ $product->id }})">
                        @if($product->primary_image)
                            <img src="{{ asset('storage/photos/' . $product->primary_image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    </flux:modal.trigger>

                    <!-- Barcode Display -->
                    @if($product->barcode)
                        @php $isSale = str(\Illuminate\Support\Str::upper((string) $product->price_note))->startsWith('SAL'); @endphp
                        <div class="h-24 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center space-y-1 px-3">
                            <div class="px-2 py-1 rounded border {{ $isSale ? 'bg-red-600 border-red-600 shadow-lg' : 'bg-white border-gray-200 dark:border-gray-600' }}">
                                <img src="{{ app(\App\Services\BarcodeService::class)->generateBarcodePNG($product->barcode, 1, 42) }}"
                                     alt="Barcode {{ $product->barcode }}" class="h-10" style="image-rendering: pixelated;">
                            </div>
                            <p class="text-xs font-mono {{ $isSale ? 'text-red-700 dark:text-red-300' : 'text-gray-800 dark:text-gray-200' }}">{{ $product->barcode }}</p>
                        </div>
                    @endif

                    <!-- Product Info -->
                    <div class="p-3">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ $product->remarks ?? $product->name }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            SKU: {{ $product->sku }}
                        </p>
                        @if($product->category)
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $product->category->name }}
                            </p>
                        @endif
                        
                        <div class="mt-2 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    ₱{{ number_format($product->price, 2) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Qty: {{ number_format($product->total_quantity) }} {{ $product->uom }}
                                </p>
                            </div>
                            <div class="flex space-x-1">
                                @can('product edit')
                                <flux:button 
                                    wire:click="editProduct({{ $product->id }})" 
                                    variant="ghost"
                                    size="sm"
                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                                    title="Edit"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </flux:button>
                                @endcan
                                <flux:modal.trigger name="delete-product">
                                    @can ('product delete')
                                    <flux:button 
                                        wire:click="deleteProduct({{ $product->id }})" 
                                        variant="ghost"
                                        size="sm"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        title="Delete"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </flux:button>
                                    @endcan
                                </flux:modal.trigger>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No products found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new product.</p>
        </div>
    @endif
</div>
