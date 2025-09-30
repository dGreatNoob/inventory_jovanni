<x-slot:header>Purchase Order</x-slot:header>
<x-slot:subheader>Product Purchase Order</x-slot:subheader>
<div class="">
    <div class="">
        <!-- Dashboard Section - Commented out as requested -->
        {{-- <x-purchase-orders-dashboard :stats="$dashboardStats" /> --}}
        <section>
            <div>
                @if (session()->has('message'))
                    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                        role="alert">
                        {{ session('message') }}
                    </div>
                @endif
                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <div class="flex items-center justify-between p-4 pr-10">
                        <div class="flex space-x-6">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" wire:model.live="search"
                                    class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search PO..." />
                            </div>

                            <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium text-gray-900 dark:text-white">Status:</label>
                                <select wire:model.live="statusFilter"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">All Status</option>
                                    <option value="for_approval">For Approval</option>
                                    <option value="to_receive">To Receive</option>
                                    <option value="received">Received</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex space-x-3 items-center">
                            <a href="{{ route('supplies.PurchaseOrder.create') }}"
                                class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                <svg class="w-3.5 h-3.5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 18 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M9 1v16M1 9h16" />
                                </svg>
                                Create Purchase Order
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-700">
                            <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-300 dark:border-gray-700">
                                <tr class="border-b border-gray-300 dark:border-gray-700">
                                    <th scope="col" class="px-6 py-3 border-b border-gray-300 dark:border-gray-700">QR Code</th>
                                    <th scope="col" class="px-6 py-3 border-b border-gray-300 dark:border-gray-700">PO #</th>
                                    <th scope="col" class="px-6 py-3 border-b border-gray-300 dark:border-gray-700">Supplier</th>
                                    <th scope="col" class="px-6 py-3 border-b border-gray-300 dark:border-gray-700">Status</th>
                                    <th scope="col" class="px-6 py-3 border-b border-gray-300 dark:border-gray-700">Order Date</th>
                                    <th scope="col" class="px-6 py-3 border-b border-gray-300 dark:border-gray-700">Receiving department </th>
                                    <th scope="col" class="px-6 py-3 border-b border-gray-300 dark:border-gray-700">Total Qty</th>
                                    <th scope="col" class="px-6 py-3 border-b border-gray-300 dark:border-gray-700">Total Price</th>
                                    <th scope="col" class="px-6 py-3 border-b border-gray-300 dark:border-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="border-t border-gray-300 dark:border-gray-700">
                                @forelse($purchaseOrders as $po)
                                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700">
                                        <td class="px-6 py-4 border-b border-gray-300 dark:border-gray-700">
                                            <button type="button" wire:click="showQrCode({{ $po->id }})"
                                                class="inline-block cursor-pointer hover:opacity-80 transition-opacity">
                                                {!! QrCode::size(70)->generate($po->po_num) !!}
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-300 dark:border-gray-700">{{ $po->po_num }}</td>
                                        <td class="px-6 py-4 border-b border-gray-300 dark:border-gray-700">{{ $po->supplier->name }}</td>
                                        <td class="px-6 py-4 border-b border-gray-300 dark:border-gray-700">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if ($po->status === 'for_approval') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                @elseif($po->status === 'to_receive') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                                @elseif($po->status === 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                @elseif($po->status === 'received') bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300
                                                @elseif($po->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300 @endif">
                                                {{ str_replace('_', ' ', ucfirst($po->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-300 dark:border-gray-700">{{ $po->order_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 border-b border-gray-300 dark:border-gray-700">{{ $po->department->name }}</td>
                                        <td class="px-6 py-4 border-b border-gray-300 dark:border-gray-700">{{ number_format($po->total_qty, 2) }}</td>
                                        <td class="px-6 py-4 border-b border-gray-300 dark:border-gray-700">{{ number_format($po->total_price, 2) }}</td>
                                        <td class="px-6 py-4 border-b border-gray-300 dark:border-gray-700">
                                            <div class="flex space-x-2">
                                                <x-button href="{{ route('supplies.PurchaseOrder.show', ['Id' => $po->id]) }}"
                                                    class="" variant="primary">View</x-button>
                                                @if ($po->status === 'pending')
                                                    <x-button href="{{ route('supplies.PurchaseOrder.edit', ['Id' => $po->id]) }}"
                                                        variant="warning">Edit</x-button>
                                                @endif
                                                <x-button type="button" wire:click="confirmDelete({{ $po->id }})"
                                                    variant="danger">Delete</x-button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700">
                                        <td colspan="9" class="px-6 py-4 text-center border-b border-gray-300 dark:border-gray-700">No purchase orders found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="py-4 px-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <label class="text-sm font-medium text-gray-900 dark:text-white">Per Page:</label>
                                <select wire:model.live="perPage"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div>
                                {{ $purchaseOrders->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Delete Modal -->
    @if ($showDeleteModal)
        <div
            class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
            <div class="relative w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <button type="button"
                        class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        wire:click="cancel">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                    <div class="p-6 text-center">
                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to
                            delete this purchase order?</h3>
                        <button type="button" wire:click="delete"
                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                            Yes, I'm sure
                        </button>
                        <button type="button" wire:click="cancel"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No,
                            cancel</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- QR Code Modal -->
    <div x-data="{ show: @entangle('showQrModal') }" x-show="show" x-cloak
        class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
        <div class="relative w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Purchase Order QR Code Details
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        wire:click="closeQrModal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-6">
                    @if($selectedPurchaseOrder)
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- QR Code Section -->
                        <div class="flex flex-col items-center justify-center p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">QR Code</h4>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                {!! QrCode::size(200)->generate($selectedPurchaseOrder->po_num) !!}
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 font-mono">{{ $selectedPurchaseOrder->po_num }}</p>
                        </div>
                        
                        <!-- Purchase Order Details Section -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Purchase Order Details</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">PO Number</label>
                                    <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $selectedPurchaseOrder->po_num }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $selectedPurchaseOrder->supplier->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                        @if ($selectedPurchaseOrder->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                        @elseif($selectedPurchaseOrder->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                        @elseif($selectedPurchaseOrder->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300 @endif">
                                        {{ str_replace('_', ' ', ucfirst($selectedPurchaseOrder->status)) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Date</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $selectedPurchaseOrder->order_date->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Date</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $selectedPurchaseOrder->del_on ? $selectedPurchaseOrder->del_on->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Receiving Department</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $selectedPurchaseOrder->department->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Terms</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $selectedPurchaseOrder->payment_terms ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Quantity</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ number_format($this->selectedPurchaseOrderTotalQuantity, 2) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Price</label>
                                    <p class="text-sm text-gray-900 dark:text-white font-semibold">₱{{ number_format($this->selectedPurchaseOrderTotalPrice, 2) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ordered By</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $selectedPurchaseOrder->orderedBy->name ?? 'N/A' }}</p>
                                </div>
                                @if($selectedPurchaseOrder->approverInfo)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved By</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $selectedPurchaseOrder->approverInfo->name }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Order Items Section -->
                    @if($selectedPurchaseOrder->supplyOrders->count() > 0)
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Items</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="px-6 py-3">SKU</th>
                                        <th class="px-6 py-3">Description</th>
                                        <th class="px-6 py-3">Quantity</th>
                                        <th class="px-6 py-3">Unit Price</th>
                                        <th class="px-6 py-3">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedPurchaseOrder->supplyOrders as $order)
                                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                        <td class="px-6 py-4 font-mono">{{ $order->supplyProfile->supply_sku ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $order->supplyProfile->supply_description ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ number_format($order->order_qty, 2) }}</td>
                                        <td class="px-6 py-4">₱{{ number_format($order->unit_price, 2) }}</td>
                                        <td class="px-6 py-4 font-semibold">₱{{ number_format($order->order_total_price, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <td colspan="2" class="px-6 py-3 text-right font-bold">Total:</td>
                                        <td class="px-6 py-3 font-bold">{{ number_format($this->selectedPurchaseOrderTotalQuantity, 2) }}</td>
                                        <td class="px-6 py-3"></td>
                                        <td class="px-6 py-3 font-bold">₱{{ number_format($this->selectedPurchaseOrderTotalPrice, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @endif
                    @endif
                </div>
                <div class="flex items-center justify-end p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <x-button type="button" wire:click="closeQrModal" variant="secondary">Close</x-button>
                    @if($selectedPurchaseOrder)
                    <x-button type="button" onclick="window.open('/purchase-order/print/{{ $selectedPurchaseOrder->po_num }}', '_blank', 'width=500,height=600')" variant="primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print QR Code
                    </x-button>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
