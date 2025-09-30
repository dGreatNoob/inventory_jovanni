<x-slot:header>Purchase Order</x-slot:header>
<x-slot:subheader>Create Purchase Order</x-slot:subheader>
<div class="">

    <div class="">
        <section
            class="mb-5 max-w-xlg p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <form wire:submit.prevent="submit">
                @if (session()->has('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    {{ session('error') }}
                </div>
                @endif

                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label for="po_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PO Number</label>
                        <select id="po_type" wire:model="po_type" disabled class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="raw_mats" selected>&lt;NEW&gt;</option>
                        </select>
                    </div>
                    <div>
                        <label for="ordered_by" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ordered By</label>
                        <input type="text" id="ordered_by" wire:model="ordered_by" disabled class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                    <div>
                        <label for="supplier_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Supplier</label>
                        <select id="supplier_id" wire:model="supplier_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                            <option value="">Select a supplier</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="deliver_to" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Receiving Department</label>
                        <select id="deliver_to" wire:model="deliver_to" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                            <option value="">Select department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('deliver_to') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="order_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Order Date</label>
                        <input type="date" id="order_date" wire:model="order_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                        @error('order_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="payment_terms" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Payment Terms</label>
                        <input type="text" id="payment_terms" wire:model="payment_terms" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter payment terms" required />
                        @error('payment_terms') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="quotation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quotation</label>
                        <input type="text" id="quotation" wire:model="quotation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter quotation reference" required />
                        @error('quotation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </form>
        </section>
        <section>
            <div>
                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <div class="flex items-center justify-between p-4 pr-10">
                        <div class="flex space-x-6">
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white">List of Materials to Order</h5>
                        </div>
                        <div class="flex space-x-3 items-center">
                            <button type="button" wire:click="$set('showModal', true)" class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                <svg class="w-3.5 h-3.5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                </svg>
                                Add Material
                            </button>
                        </div>
                    </div>

                    <div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                        <div class="relative w-full max-w-2xl max-h-full">
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        Add Material to Purchase Order
                                    </h3>
                                    <button type="button" wire:click="$set('showModal', false)" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <div class="p-6 space-y-6">
                                    <div class="grid gap-6 mb-6 md:grid-cols-2">
                                        <div class="col-span-2">
                                            <label for="search" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Search Material</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                                    </svg>
                                                </div>
                                                <input type="text" wire:model.live="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search by description, type, or class" />
                                            </div>

                                            <div class="mt-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg dark:border-gray-600">
                                                @forelse($rawmatProfiles as $rawmat)
                                                <div wire:click="selectRawmatProfile({{ $rawmat->id }})"
                                                    class="p-3 hover:bg-blue-50 dark:hover:bg-blue-900/30 cursor-pointer {{ $selected_rawmat_profile == $rawmat->id ? 'bg-blue-100 dark:bg-blue-900/50 border-l-4 border-blue-500' : '' }}">
                                                    <div class="font-medium text-gray-900 dark:text-white">{{ $rawmat->classification }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $rawmat->supplier }} - {{ $rawmat->country_origin }}</div>
                                                </div>
                                                @empty
                                                <div class="p-3 text-center text-gray-500 dark:text-gray-400">
                                                    No items found
                                                </div>
                                                @endforelse
                                            </div>
                                            <div class="mt-2">
                                                {{ $rawmatProfiles->links() }}
                                            </div>
                                        </div>
                                        <div>
                                            <label for="order_qty" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Order Quantity</label>
                                            <input type="number" step="0.01" wire:model="order_qty" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter quantity" required />
                                            @error('order_qty') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                                    <button type="button" wire:click="addItem" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Add Item</button>
                                    <button type="button" wire:click="$set('showModal', false)" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Classification</th>
                                    <th scope="col" class="px-6 py-3">GSM</th>
                                    <th scope="col" class="px-6 py-3">Width Size</th>
                                    <th scope="col" class="px-6 py-3">Supplier</th>
                                    <th scope="col" class="px-6 py-3">Country Origin</th>
                                    <th scope="col" class="px-6 py-3">Order Quantity</th>
                                    <th scope="col" class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginatedOrderedItems as $index => $item)
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                    <td class="px-6 py-4">{{ $item['classification'] ?? '' }}</td>
                                    <td class="px-6 py-4">{{ $item['gsm'] ?? ''}}</td>
                                    <td class="px-6 py-4">{{ $item['width_size'] ?? ''}}</td>
                                    <td class="px-6 py-4">{{ $item['supplier'] ?? ''}}</td>
                                    <td class="px-6 py-4">{{ $item['country_origin'] ?? ''}}</td>
                                    <td class="px-6 py-4">{{ $item['order_qty'] ?? ''}}</td>
                                    <td class="px-6 py-4">
                                        <button type="button" wire:click="removeItem({{ count($orderedItems) - 1 - ($index + (($orderedItemsPage - 1) * $orderedItemsPerPage)) }})" class="font-medium text-red-600 dark:text-red-500 hover:underline">Remove</button>
                                    </td>
                                </tr>
                                @empty
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                    <td colspan="7" class="px-6 py-8 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-4">
                                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="text-lg font-medium text-gray-900 dark:text-white">No Items Added</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Please add items to your purchase order using the "Add Material" button above.</p>
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
                                <select wire:model.live="orderedItemsPerPage"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
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
        <section>
            <div x-data="{ show: @entangle('showViewModal') }" x-show="show" x-cloak 
                class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black bg-opacity-50">
                <div class="relative w-full max-w-6xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                List Of Orders
                            </h3>
                            <button type="button" wire:click="$set('showViewModal', false)" 
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">ID Number</th>
                                        <th scope="col" class="px-6 py-3">Classification</th>
                                        <th scope="col" class="px-6 py-3">GSM</th>
                                        <th scope="col" class="px-6 py-3">Width Size</th>
                                        <th scope="col" class="px-6 py-3">Supplier</th>
                                        <th scope="col" class="px-6 py-3">Country Origin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">1</td>
                                        <td class="px-6 py-4">24</td>
                                        <td class="px-6 py-4">11</td>
                                        <td class="px-6 py-4">11</td>
                                        <td class="px-6 py-4">jess</td>
                                        <td class="px-6 py-4">Philippines</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="fixed bottom-0 right-0 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 w-full">
            <div class="flex justify-end">
                <button type="button" wire:click="submit" @if(empty($orderedItems)) disabled @endif class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 disabled:opacity-50 disabled:cursor-not-allowed">
                    Submit Purchase Order
                </button>
            </div>
        </div>
    </div>
</div>

</div>