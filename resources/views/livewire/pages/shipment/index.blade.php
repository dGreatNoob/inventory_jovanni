<x-slot:header>Shipment</x-slot:header>
<x-slot:subheader>Track and manage all outgoing shipments linked to approved sales orders.</x-slot:subheader>
<div>
    <div>       
@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
            <div x-data="{ show: @entangle('showQrModal') }" x-show="show" x-cloak
                class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                <div class="relative w-full max-w-4xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Shipment QR Code Details
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
                            @if($getShipmentDetails)
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- QR Code Section -->
                                <div class="flex flex-col items-center justify-center p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">QR Code</h4>
                                    <div class="bg-white p-4 rounded-lg shadow-sm">
                                        {!! QrCode::size(200)->generate($getShipmentDetails->shipping_plan_num) !!}
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 font-mono">
                                        {{ $getShipmentDetails->shipping_plan_num }}
                                    </p>
                                </div>
                                
                                <!-- Purchase Order Details Section -->
                                <div class="space-y-4">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Shippment Details</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipping Plan Number</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">
                                                {{ $getShipmentDetails->shipping_plan_num }}
                                            </p>
                                        </div>                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                                @if ($getShipmentDetails->shipping_status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                @elseif($getShipmentDetails->shipping_status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                @elseif($getShipmentDetails->shipping_status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300 @endif">
                                                {{ str_replace('_', ' ', ucfirst($getShipmentDetails->shipping_status)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipping Date</label>
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                 {{ \Carbon\Carbon::parse($getShipmentDetails->scheduled_ship_date)->format('M d, Y') }}
                                            </p>
                                        </div>                              
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Price</label>
                                            <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                                @if($getShipmentDetails->branchAllocation)
                                                            ₱{{ number_format($getShipmentDetails->branchAllocation->items->sum('unit_price')) }}
                                                        @else
                                                            N/A
                                                        @endif
                                            </p>
                                        </div>
                                       
                                        @if($getShipmentDetails->approver_id)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved By</label>
                                            <p class="text-sm text-gray-900 dark:text-white">                                                
                                                {{ $getShipmentDetails->approver->name }}
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Order Items Section -->
                            @if($getShipmentDetails->branchAllocation && $getShipmentDetails->branchAllocation->items->count() > 0)
                            <div class="mt-6">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Allocated Items</h4>
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
                                            @foreach($getShipmentDetails->branchAllocation->items as $item)
                                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                                <td class="px-6 py-4 font-mono">{{ $item->product->supply_sku ?? 'N/A' }}</td>
                                                <td class="px-6 py-4">{{ $item->product->supply_description ?? 'N/A' }}</td>
                                                <td class="px-6 py-4">{{ number_format($item->quantity, 2) }}</td>
                                                <td class="px-6 py-4">₱{{ number_format($item->unit_price, 2) }}</td>
                                                <td class="px-6 py-4 font-semibold">₱{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                            @endif
                        </div>
                        <div class="flex items-center justify-end p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <x-button type="button" wire:click="closeQrModal" variant="secondary">Close</x-button>
                            @if($getShipmentDetails)
                            <x-button type="button" onclick="window.open('/shipment/print/{{ $getShipmentDetails->shipping_plan_num }}', '_blank', 'width=500,height=600')" variant="primary">
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

        <!-- Create Sales Order Card -->
        <x-collapsible-card title="Create New Shipment" open="false" size="full">          
            <form wire:submit.prevent="createShipment" class="space-y-6">
                        <!-- Order Information Section -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Shipment Information
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <x-input
                                        type="text"
                                        wire:model.defer="shipping_plan_num"
                                        name="shipping_plan_num"
                                        label="Shipment Reference Number"
                                        placeholder="Shipment Reference Number"
                                        class="w-full"
                                    />
                                </div>
                                <div>

                                    <x-input
                                        type="date"
                                        wire:model.defer="scheduled_ship_date"
                                        name="scheduled_ship_date"
                                        label="Shipping Date"
                                        placeholder="Select Shipping Date"
                                        class="w-full"
                                    />
                                </div>

                                <div>

                                    <x-input
                                        type="text"
                                        wire:model.defer="vehicle_plate_number"
                                        name="vehicle_plate_number"
                                        label="Vehicle Plate Number"
                                        placeholder="Vehicle Plate Number"
                                        class="w-full"
                                    />
                                </div>

                                <div>
                                    <x-dropdown
                                        wire:model.live="delivery_method"
                                        name="delivery_method"
                                        label="Delivery Method"
                                        :options="$deliveryMethods"
                                        placeholder="Select Delivery Method"
                                        class="w-full"
                                    />
                                </div>

                                <!-- Batch Selection -->
                                <div>
                                    <label for="selectedBatchId" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                        Select Batch Reference
                                    </label>
                                    <select id="selectedBatchId"
                                            wire:model.live="selectedBatchId"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        <option value="">Select a dispatched batch...</option>
                                        @foreach($availableBatches as $batch)
                                            <option value="{{ $batch->id }}">{{ $batch->ref_no }} - {{ \Carbon\Carbon::parse($batch->transaction_date)->format('M d, Y') }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Branch Selection -->
                                <div>
                                    <label for="selectedBranchId" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                        Select Branch
                                    </label>
                                    <select id="selectedBranchId"
                                            wire:model.defer="selectedBranchId"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                            :disabled="!$wire.selectedBatchId">
                                        <option value="">Select a branch...</option>
                                        @foreach($availableBranches as $branchAlloc)
                                            <option value="{{ $branchAlloc->id }}">{{ $branchAlloc->branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information Section -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Customer Information
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-input 
                                    type="text" 
                                    wire:model.defer="customer_name" 
                                    name="customer_name" 
                                    label="Customer Name" 
                                    placeholder="Enter Customer Name"
                                />

                                <x-input 
                                    type="text" 
                                    wire:model.defer="customer_address" 
                                    name="customer_address" 
                                    label="Customer Address" 
                                    placeholder="Enter Customer Address"
                                />
                                
                                
                                <x-input
                                    type="tel"
                                    wire:model.defer="phone"
                                    name="phone"
                                    label="Phone Number"
                                    placeholder="Enter phone number"
                                />
                            </div>
                          </div>

                                               

                        <!-- Form Actions -->
                        <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-600">
                           
                                <x-button 
                                    type="submit" 
                                    variant="primary"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ ($editValue) ? 'Update Shipment' : 'Create Shippment' }}
                                </x-button>
                          
                        </div>
                    </form>
        </x-collapsible-card>
        
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="transition duration-500 ease-in-out" x-transition>
                <x-flash-message />
            </div>
        @endif

        @if (session()->has('error'))          
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
            
   
        <!-- Sales Orders List Card -->
        <x-collapsible-card title="Shipment List" open="true" size="full">
            <section>
            <div>
                <!-- Start coding here -->
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
                                <input type="text" wire:model.live.debounce.300ms="search"
                                    class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search Shipment..." required="">
                            </div>
                            <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium text-gray-900 dark:text-white">Status:</label>
                                <select wire:model.live="statusFilter"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">All Status</option>                                  
                                    <option value="ready">Ready</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="approved">Approved</option>
                                    <option value="failed">Failed</option>
                                    <option value="returned">Returned</option>
                                    <option value="processing">Processing</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_transit">In Transit</option>
                                    <option value="incomplete">Incomplete</option>
                                    <option value="damaged">Damaged</option> 
                                </select>                                
                            </div> 
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th>
                                        QRCode
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Shipment Reference Number
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Customer
                                    </th>
                                     <th scope="col" class="px-6 py-3">
                                         Status
                                     </th>
                                     <th scope="col" class="px-6 py-3">
                                         Delivery Method
                                     </th>
                                    <th scope="col" class="px-6 py-3">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($shipments as $data)
                                    <tr wire:key
                                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                        <th>
                                            <button wire:click="showShipmentQrCode('{{ $data->shipping_plan_num }}')">
                                                {!! QrCode::size(60)->generate($data->shipping_plan_num) !!}
                                            </button>
                                        </th>
                                        <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ ucfirst($data->shipping_plan_num) }}
                                        </th>  
                                        <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ ucfirst($data->customer_name) }}
                                        </th>
                                        <td class="px-6 py-4">
                                            <span
                                                class="
                                                    px-2 py-1 rounded-full text-white text-xs font-semibold
                                                    @if ($data->shipping_status === 'pending') bg-yellow-500
                                                    @elseif ($data->shipping_status === 'processing') bg-green-600
                                                    @elseif ($data->shipping_status === 'cancelled') bg-red-600
                                                    @else bg-gray-500 @endif">
                                                {{ ucfirst($data->shipping_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                           {{ $deliveryMethods[$data->delivery_method] ?? $data->delivery_method }}
                                        </td>
                                        <td class="px-6 py-4">                                           
                                            @if($data->shipping_status == 'pending')
                                                <div x-data>        <!-- Your content here -->
                                                    <button 
                                                    wire:click="edit({{ $data->id }})" 
                                                    @click="window.scrollTo({top: 0, behavior: 'smooth'})">Edit
                                                    </button>
                                                </div>
                                            @endif

                                            <a href="{{ route('shipping.view',$data->id) }}"
                                                class="font-medium px-1 text-grey-600 dark:text-blue-500 hover:underline"> 
                                                View
                                            </a>                                           
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            No shipping request found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="py-4 px-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Per Page Selection -->
                            <div class="flex items-center space-x-4">
                                <label for="perPage" class="text-sm font-medium text-gray-900 dark:text-white">Per
                                    Page
                                </label>
                                <select 
                                    id="perPage" 
                                    wire:model.live="perPage"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <!-- Pagination Links -->                          
                            <div>
                                {{$shipments->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        </x-collapsible-card>
    </div>
</div>