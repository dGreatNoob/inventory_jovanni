<x-slot:header>Purchase Order</x-slot:header>
<x-slot:subheader>Create Purchase Order</x-slot:subheader>

<div class="">
    <div class="">
        {{-- Purchase Order Form --}}
        <section class="mb-5 max-w-xlg p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <form wire:submit.prevent="submit">
                @if (session()->has('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    {{ session('error') }}
                </div>
                @endif

                @if (session()->has('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    {{ session('success') }}
                </div>
                @endif

                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label for="po_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PO Number</label>
                        <div class="relative">
                            <select id="po_type" wire:model="po_type" disabled class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-10 appearance-none dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="products" selected>&lt;NEW&gt;</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="ordered_by" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ordered By</label>
                        <input type="text" id="ordered_by" wire:model="ordered_by" disabled class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                    <div>
                        <label for="supplier_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Supplier</label>
                        <div class="relative">
                            <select id="supplier_id" wire:model.live="supplier_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-10 appearance-none dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                <option value="">Select a supplier</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        @error('supplier_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="deliver_to" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Receiving Department</label>
                        <!-- Hidden input for Livewire model (must have value at render time!) -->
                        <input type="hidden" wire:model="deliver_to" value="{{ $deliver_to ?? '' }}">
                        <!-- Readonly display for user -->
                        <input type="text" value="Warehouse" readonly class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                        @error('deliver_to') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="order_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Order Date</label>
                        <input type="date" id="order_date" wire:model="order_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                        @error('order_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="expected_delivery_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Expected Delivery Date</label>
                        <input type="date" id="expected_delivery_date" wire:model="expected_delivery_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('expected_delivery_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </form>
        </section>

        {{-- Products List Section --}}
        <section>
            <div>
                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <div class="flex items-center justify-between p-4 pr-10">
                        <div class="flex space-x-6">
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white">List of Products to Order</h5>
                        </div>
                        <div class="flex space-x-3 items-center">
                            <button
                                type="button"
                                wire:click="openModal"
                                class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                title="Add Product"
                            >
                                <svg class="w-3.5 h-3.5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                </svg>
                                Add Product
                            </button>

                        </div>
                    </div>

                    {{-- Add Product Modal --}}
                    <div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50">
                        <div class="relative w-full max-w-4xl max-h-[90vh] bg-white rounded-lg shadow-xl dark:bg-gray-800 overflow-hidden flex flex-col">
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        Add Product to Purchase Order
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select a product from the supplier's catalog</p>
                                </div>
                                <button type="button" wire:click="closeModal" class="rounded-full p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <div class="flex-1 overflow-y-auto px-6 py-6">
                                    {{-- Search and Filters --}}
                                    <div class="grid gap-4 mb-4 md:grid-cols-2">
                                        <div>
                                            <label for="search" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Search Product</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                                    </svg>
                                                </div>
                                                <input type="text" wire:model.live.debounce.300ms="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search by name, SKU, Product ID, or Supplier Code..." />
                                            </div>
                                        </div>
                                        <div>
                                            <label for="categoryFilter" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category</label>
                                            <div class="relative">
                                                <select wire:model.live="categoryFilter" id="categoryFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-10 appearance-none dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                    <option value="">All Categories</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Product List --}}
                                    <div class="border border-gray-200 rounded-lg dark:border-gray-600 overflow-hidden">
                                        <div class="max-h-64 overflow-y-auto">
                                            @forelse($products as $product)
                                            <div wire:click="selectProduct({{ $product->id }})"
                                                class="p-4 hover:bg-blue-50 dark:hover:bg-blue-900/30 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0 {{ $selected_product == $product->id ? 'bg-blue-50 dark:bg-blue-900/50 border-l-4 border-blue-500' : '' }}">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1 space-y-1.5">
                                                        <div class="font-medium text-gray-900 dark:text-white">
                                                            {{ $product->remarks ?? $product->name }}
                                                            @if ($product->color)
                                                                <span class="text-gray-500 dark:text-gray-400"> · {{ $product->color->name ?? $product->color->code }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-sm font-mono text-gray-600 dark:text-gray-400">
                                                            SKU: {{ $product->sku ?? '—' }}
                                                        </div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                                            Supplier Code: {{ $product->supplier_code ?? '—' }}
                                                        </div>
                                                        @if ($product->category)
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ $product->category->name }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="text-right ml-4 flex-shrink-0">
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Cost</div>
                                                        <div class="font-semibold text-gray-900 dark:text-white">₱{{ number_format($product->cost, 2) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            @empty
                                            <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                </svg>
                                                <p>No products found</p>
                                            </div>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        {{ $products->links() }}
                                    </div>

                                    {{-- Quantity and Price Inputs --}}
                                    @if($selected_product)
                                    <div class="grid gap-4 md:grid-cols-2 border-t border-gray-200 dark:border-gray-600 pt-4 mt-4">
                                        <div>
                                            <label for="unit_price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit Price (₱)</label>
                                            <input type="number" step="0.01" wire:model="unit_price" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter unit price" required />
                                            @error('unit_price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label for="order_qty" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Order Quantity</label>
                                            <input type="number" step="0.01" wire:model="order_qty" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter quantity" required />
                                            @error('order_qty') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex-shrink-0 space-x-3">
                                    <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                                        Cancel
                                    </button>
                                    <button type="button" wire:click="addItem" @if(!$selected_product) disabled @endif class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Add Item
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ordered Items Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-5 py-3 text-left">Product Name</th>
                                    <th scope="col" class="px-5 py-3 text-left">SKU</th>
                                    <th scope="col" class="px-5 py-3 text-left">Supplier Code</th>
                                    <th scope="col" class="px-5 py-3 text-left">Category</th>
                                    <th scope="col" class="px-5 py-3 text-right">Unit Price</th>
                                    <th scope="col" class="px-5 py-3 text-right">Quantity</th>
                                    <th scope="col" class="px-5 py-3 text-right">Total Price</th>
                                    <th scope="col" class="px-5 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginatedOrderedItems as $index => $item)
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $item['name'] ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-sm font-mono text-gray-600 dark:text-gray-400">{{ $item['sku'] ?? '—' }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $item['supplier_code'] ?? '—' }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $item['category'] ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="text-sm text-gray-900 dark:text-white">₱{{ number_format($item['unit_price'] ?? 0, 2) }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ number_format($item['order_qty'] ?? 0, 2) }} {{ $item['uom'] ?? 'pcs' }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">₱{{ number_format($item['total_price'] ?? 0, 2) }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <button type="button" wire:click="removeItem({{ count($orderedItems) - 1 - ($index + (($orderedItemsPage - 1) * $orderedItemsPerPage)) }})" class="font-medium text-red-600 dark:text-red-500 hover:underline">Remove</button>
                                    </td>
                                </tr>
                                @empty
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                    <td colspan="8" class="px-6 py-8 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-4">
                                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="text-lg font-medium text-gray-900 dark:text-white">No Items Added</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Please add items to your purchase order using the "Add Product" button above.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(!empty($orderedItems))
                            <tfoot class="text-sm font-semibold text-gray-900 bg-gray-100 dark:bg-gray-700 dark:text-white">
                                <tr>
                                    <td colspan="4" class="px-5 py-3 text-right">Total:</td>
                                    <td class="px-5 py-3"></td>
                                    <td class="px-5 py-3 text-right">{{ number_format($this->totalQuantity, 2) }}</td>
                                    <td class="px-5 py-3 text-right">₱{{ number_format($this->totalAmount, 2) }}</td>
                                    <td class="px-5 py-3"></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="py-4 px-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <label class="text-sm font-medium text-gray-900 dark:text-white">Per Page:</label>
                                <div class="relative">
                                    <select wire:model.live="orderedItemsPerPage"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 pr-10 appearance-none dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            @if($orderedItemsTotalPages > 1)
                            <div class="flex items-center space-x-2">
                                <button wire:click="previousOrderedItemsPage" @if($orderedItemsPage <=1) disabled @endif class="px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    Previous
                                </button>
                                @for($i = 1; $i <= $orderedItemsTotalPages; $i++)
                                    <button wire:click="goToOrderedItemsPage({{ $i }})" class="px-3 py-1 text-sm font-medium {{ $orderedItemsPage == $i ? 'text-white bg-blue-600' : 'text-gray-500 bg-white border border-gray-300' }} rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                    {{ $i }}
                                    </button>
                                @endfor
                                <button wire:click="nextOrderedItemsPage" @if($orderedItemsPage>= $orderedItemsTotalPages) disabled @endif class="px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    Next
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Submit Button --}}
        <div class="fixed bottom-0 right-0 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 w-full">
            <div class="flex justify-end space-x-3">
                <div class="flex items-center space-x-6 mr-auto">
                    @if(!empty($orderedItems))
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium">Total Items:</span> {{ count($orderedItems) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium">Total Quantity:</span> {{ number_format($this->totalQuantity, 2) }}
                    </div>
                    <div class="text-lg font-bold text-gray-900 dark:text-white">
                        <span class="font-medium">Grand Total:</span> ₱{{ number_format($this->totalAmount, 2) }}
                    </div>
                    @endif
                </div>
                <button type="button" wire:click="submit" @if(empty($orderedItems)) disabled @endif class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 disabled:opacity-50 disabled:cursor-not-allowed">
                    Submit Purchase Order
                </button>
            </div>
        </div>
    </div>
</div>