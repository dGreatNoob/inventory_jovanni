<!-- Products Grid View -->
<div class="p-6">
    @if(count($products) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                    <!-- Product Image -->
                    <div class="aspect-w-16 aspect-h-12 bg-gray-200 rounded-t-lg overflow-hidden">
                        @if($product->primary_image)
                            <img src="{{ Storage::url('products/' . $product->primary_image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Selection Checkbox -->
                        <div class="absolute top-2 left-2">
                            <input type="checkbox" 
                                   wire:click="toggleProductSelection({{ $product->id }})"
                                   @if(in_array($product->id, $selectedProducts)) checked @endif
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </div>

                        <!-- Stock Status Badge -->
                        <div class="absolute top-2 right-2">
                            @if($product->total_quantity <= 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Out of Stock
                                </span>
                            @elseif($product->total_quantity <= 10)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Low Stock
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    In Stock
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-gray-900 truncate">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    SKU: {{ $product->sku }}
                                </p>
                                @if($product->category)
                                    <p class="text-xs text-gray-500">
                                        {{ $product->category->name }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-2 flex items-center justify-between">
                            <div>
                                <p class="text-lg font-semibold text-gray-900">
                                    ${{ number_format($product->price, 2) }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Cost: ${{ number_format($product->cost, 2) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">
                                    Qty: {{ number_format($product->total_quantity) }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $product->uom }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-3 flex space-x-2">
                            <flux:button 
                                wire:click="editProduct({{ $product->id }})" 
                                variant="outline"
                                size="sm"
                                class="flex-1"
                            >
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </flux:button>
                            <flux:button 
                                wire:click="deleteProduct({{ $product->id }})" 
                                variant="danger"
                                size="sm"
                                class="flex-1"
                            >
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </flux:button>
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
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new product.</p>
            <div class="mt-6">
                <button wire:click="createProduct" 
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Product
                </button>
            </div>
        </div>
    @endif
</div>
