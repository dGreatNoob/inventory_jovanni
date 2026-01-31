<x-slot:header>Purchase Order</x-slot:header>
<x-slot:subheader>Edit Purchase Order</x-slot:subheader>
<div class="">
    <div class="">
        <x-collapsible-card title="Purchase Order Details" open="true" size="full">
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
                    <x-input type="text" wire:model="po_num" name="po_num" label="PO Number" disabled="true" />
                    <x-input type="text" wire:model="ordered_by" name="ordered_by" label="Ordered By" disabled="true" />
                    <x-dropdown wire:model="supplier_id" name="supplier_id" label="Supplier" :options="$suppliers->pluck('name', 'id')->toArray()" placeholder="Select a supplier" />
                    <x-dropdown wire:model="deliver_to" name="deliver_to" label="Receiving Department" :options="$departments->pluck('name', 'id')->toArray()" placeholder="Select department" searchable="true" />
                    <x-input type="date" wire:model="order_date" name="order_date" label="Order Date" />
                    <x-input type="date" wire:model="expected_delivery_date" name="expected_delivery_date" label="Expected Delivery Date" />
                </div>
            </form>
        </x-collapsible-card>

        <x-collapsible-card title="List of Items to Order" open="true" size="full">
            <div class="flex items-center justify-between p-4 pr-10">
                <div class="flex space-x-6">
                    <h5 class="text-lg font-bold text-gray-900 dark:text-white">List of Items to Order</h5>
                </div>
                <div class="flex space-x-3 items-center">
                    <x-button type="button" wire:click="openModal" variant="primary">
                        <svg class="w-3.5 h-3.5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
                        </svg>
                        Add Item
                    </x-button>
                </div>
            </div>

            <!-- Add Item Modal -->
            <div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black bg-opacity-50">
                <div class="relative w-full max-w-4xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ $editingItemIndex !== null ? 'Edit Item' : 'Add Item to Purchase Order' }}
                            </h3>
                            <button type="button" wire:click="closeModal" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <!-- Search and Filter -->
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Search Products</label>
                                    <input type="text" wire:model.live="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Search by name, SKU, or barcode" />
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category</label>
                                    <select wire:model.live="categoryFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Product List -->
                            <div class="border border-gray-200 rounded-lg dark:border-gray-600 max-h-64 overflow-y-auto">
                                @forelse($products as $product)
                                    <div wire:click="selectProduct({{ $product->id }})" class="p-3 hover:bg-blue-50 dark:hover:bg-blue-900/30 cursor-pointer border-b last:border-b-0 {{ $selected_product == $product->id ? 'bg-blue-100 dark:bg-blue-900/50 border-l-4 border-blue-500' : '' }}">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            SKU: {{ $product->sku }} | Category: {{ $product->category->name ?? 'N/A' }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-3 text-center text-gray-500 dark:text-gray-400">
                                        @if(empty($supplier_id))
                                            Please select a supplier first
                                        @else
                                            No products found
                                        @endif
                                    </div>
                                @endforelse
                            </div>
                            
                            @if($products->hasPages())
                                <div class="mt-2">
                                    {{ $products->links() }}
                                </div>
                            @endif

                            <!-- Quantity and Price -->
                            <div class="grid gap-4 md:grid-cols-2">
                                <x-input type="number" step="0.01" wire:model="unit_price" name="unit_price" label="Unit Price" placeholder="Enter unit price" />
                                <x-input type="number" step="0.01" wire:model="order_qty" name="order_qty" label="Quantity" placeholder="Enter quantity" />
                            </div>
                        </div>

                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <x-button type="button" wire:click="addItem" variant="primary">
                                {{ $editingItemIndex !== null ? 'Update Item' : 'Add Item' }}
                            </x-button>
                            <x-button type="button" wire:click="closeModal" variant="secondary">
                                Cancel
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ordered Items Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">SKU</th>
                            <th scope="col" class="px-6 py-3">Product Name</th>
                            <th scope="col" class="px-6 py-3">Category</th>
                            <th scope="col" class="px-6 py-3">Supplier Code</th>
                            <th scope="col" class="px-6 py-3">Unit Price</th>
                            <th scope="col" class="px-6 py-3">Quantity</th>
                            <th scope="col" class="px-6 py-3">Total</th>
                            <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginatedOrderedItems as $index => $item)
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item['sku'] }}</td>
                                <td class="px-6 py-4">{{ $item['name'] }}</td>
                                <td class="px-6 py-4">{{ $item['category'] }}</td>
                                <td class="px-6 py-4">{{ $item['supplier_code'] }}</td>
                                <td class="px-6 py-4">₱{{ number_format($item['unit_price'], 2) }}</td>
                                <td class="px-6 py-4">{{ number_format($item['order_qty'], 2) }}</td>
                                <td class="px-6 py-4 font-semibold">₱{{ number_format($item['total_price'], 2) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <x-button type="button" wire:click="editItem({{ $index }})" variant="warning" size="sm">Edit</x-button>
                                        <x-button type="button" wire:click="removeItem({{ $index }})" variant="danger" size="sm">Remove</x-button>
                                    </div>
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
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Please add items to your purchase order using the "Add Item" button above.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="text-sm font-semibold text-gray-900 bg-gray-100 dark:bg-gray-700 dark:text-white">
                        <tr>
                            <td colspan="5" class="px-6 py-3 text-right">Total:</td>
                            <td class="px-6 py-3">{{ number_format($totalQuantity ?? 0, 2) }}</td>
                            <td class="px-6 py-3">₱{{ number_format($totalAmount ?? 0, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Pagination -->
            <div class="py-4 px-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-900 dark:text-white">Per Page:</label>
                        <x-dropdown wire:model.live="orderedItemsPerPage" name="orderedItemsPerPage" :options="[
                            '5' => '5',
                            '10' => '10',
                            '25' => '25',
                            '50' => '50',
                            '100' => '100'
                        ]" />
                    </div>
                    @if($orderedItemsTotalPages > 1)
                        <div class="flex items-center space-x-2">
                            <x-button type="button" wire:click="previousOrderedItemsPage" variant="secondary" size="sm" :disabled="$orderedItemsPage <= 1">
                                Previous
                            </x-button>
                            @for($i = 1; $i <= $orderedItemsTotalPages; $i++)
                                <x-button type="button" wire:click="goToOrderedItemsPage({{ $i }})" 
                                    :variant="$orderedItemsPage == $i ? 'primary' : 'secondary'" 
                                    size="sm">
                                    {{ $i }}
                                </x-button>
                            @endfor
                            <x-button type="button" wire:click="nextOrderedItemsPage" variant="secondary" size="sm" :disabled="$orderedItemsPage >= $orderedItemsTotalPages">
                                Next
                            </x-button>
                        </div>
                    @endif
                </div>
            </div>
        </x-collapsible-card>

        <!-- Fixed Bottom Actions -->
        <div class="fixed bottom-0 right-0 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 w-full">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('pomanagement.purchaseorder') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                    Cancel
                </a>
                <x-button type="button" wire:click="submit" variant="primary" :disabled="empty($orderedItems)">
                    Update Purchase Order
                </x-button>
            </div>
        </div>
    </div>
</div>