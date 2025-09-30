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

                <div class="grid gap-6 mb-6 md:grid-cols-2">

                    <x-input type="text" wire:model="po_num" name="po_num" label="PO Number" disabled="true" />
                    <x-input type="text" wire:model="ordered_by" name="ordered_by" label="Ordered By" disabled="true" />
                    <x-dropdown wire:model="supplier_id" name="supplier_id" label="Supplier" :options="$suppliers->pluck('name', 'id')->toArray()" placeholder="Select a supplier" />
                        <x-dropdown wire:model="deliver_to" name="deliver_to" label="Receiving Department" :options="$departments->pluck('name', 'id')->toArray()" placeholder="Select department" searchable="true" />
                    <x-input type="date" wire:model="order_date" name="order_date" label="Order Date" />
                    <x-input type="date" wire:model="delivery_on" name="delivery_on" label="Delivery On" />
                    <x-input type="text" wire:model="payment_terms" name="payment_terms" label="Payment Terms" placeholder="Enter payment terms" />
                    <x-input type="text" wire:model="quotation" name="quotation" label="Quotation" placeholder="Enter quotation reference" />
                        @error('deliver_to') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

           
                </div>
            </form>
        </x-collapsible-card>

        <x-collapsible-card title="List of Items to Order" open="true" size="full">
                    <div class="flex items-center justify-between p-4 pr-10">
                        <div class="flex space-x-6">
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white">List of Items to Order</h5>
                        </div>
                        <div class="flex space-x-3 items-center">
                    <x-button type="button" wire:click="$set('showModal', true)" variant="primary">
                                <svg class="w-3.5 h-3.5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
                                </svg>
                                Add Item
                    </x-button>
                        </div>
                    </div>

                    <div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                        <div class="relative w-full max-w-2xl max-h-full">
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
                                    <div class="grid gap-6 mb-6 md:grid-cols-2">
                                        <div class="col-span-2">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                                </div>
                                                <input type="text" wire:model.live="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search by description, type, or class" />
                                            </div>
                                            
                                            <div class="mt-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg dark:border-gray-600">
                                                @forelse($supplyProfiles as $supply)
                                                    <div wire:click="selectSupplyProfile({{ $supply->id }})" class="p-3 hover:bg-blue-50 dark:hover:bg-blue-900/30 cursor-pointer {{ $selected_supply_profile == $supply->id ? 'bg-blue-100 dark:bg-blue-900/50 border-l-4 border-blue-500' : '' }}">
                                                        <div class="font-medium text-gray-900 dark:text-white">{{ $supply->supply_description }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ $supply->supply_sku }}</div>
                                                    </div>
                                                @empty
                                                    <div class="p-3 text-center text-gray-500 dark:text-gray-400">
                                                        No items found
                                                    </div>
                                                @endforelse
                                            </div>
                                            <div class="mt-2">
                                                {{ $supplyProfiles->links() }}
                                            </div>
                                        </div>
                                <x-input type="number" step="0.01" wire:model="unit_price" name="unit_price" label="Unit Price" placeholder="Enter unit price" />
                                <x-input type="number" step="0.01" wire:model="order_qty" name="order_qty" label="Order Quantity" placeholder="Enter quantity" />
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

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Item</th>
                                    <th scope="col" class="px-6 py-3">Unit Price</th>
                                    <th scope="col" class="px-6 py-3">Quantity</th>
                                    <th scope="col" class="px-6 py-3">Total Price</th>
                                    <th scope="col" class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginatedOrderedItems as $index => $item)
                                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                        <td class="px-6 py-4">{{ $item['description'] }}</td>
                                        <td class="px-6 py-4">{{ number_format($item['unit_price'], 2) }}</td>
                                        <td class="px-6 py-4">{{ number_format($item['order_qty'], 2) }}</td>
                                        <td class="px-6 py-4">{{ number_format($item['total_price'], 2) }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex space-x-2">
                                        <x-button type="button" wire:click="editItem({{ $index }})" variant="warning" size="sm">Edit</x-button>
                                        <x-button type="button" wire:click="removeItem({{ $index }})" variant="danger" size="sm">Remove</x-button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                        <td colspan="6" class="px-6 py-8 text-center">
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
                        </table>
                    </div>

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

        <div class="fixed bottom-0 right-0 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 w-full">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('supplies.PurchaseOrder') }}"  class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                    Cancel
                </a>
                <x-button type="button" wire:click="submit" variant="primary" :disabled="empty($orderedItems)">
                    Update Purchase Order
                </x-button>
            </div>
        </div>
    </div>
</div>