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
                                        Select Batch Allocation
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
                                            wire:model.live="selectedBranchId"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        <option value="">Select a branch...</option>
                                        @foreach($availableBranches as $branchAlloc)
                                            <option value="{{ $branchAlloc->id }}">{{ $branchAlloc->branch->name ?? 'Branch #' . $branchAlloc->id }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Allocation Dispatch -->
                            @if($selectedBranchAllocation)
                            @php
                                $scannedItems = $selectedBranchAllocation->items->where('scanned_quantity', '>', 0)->groupBy('product_id')->map(function ($items) {
                                    $firstItem = $items->first();
                                    $totalQuantity = $items->sum('scanned_quantity');
                                    return [
                                        'product' => $firstItem->product,
                                        'quantity' => $totalQuantity,
                                    ];
                                });
                            @endphp
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    Allocation Dispatch for {{ $selectedBranchAllocation->branch->name }}
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-64 overflow-y-auto mb-6">
                                    @forelse($scannedItems as $item)
                                        <div class="p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $item['product']->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                SKU: {{ $item['product']->sku }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                Scanned Quantity: {{ $item['quantity'] }}
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-8 col-span-full">
                                            <p class="text-gray-500 dark:text-gray-400">
                                                No scanned items found for this branch.
                                            </p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            @endif
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
                                    {{ ($editValue) ? 'Update Shipment' : 'Create Shipment' }}
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
                                        <option value="completed">Completed</option>
                                        <option value="pending">Pending</option>
                                        <option value="in_transit">In Transit</option>
                                        <option value="damaged">Damaged</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Shipment Reference Number</th>
                                        <th scope="col" class="px-6 py-3">Branch</th>
                                        <th scope="col" class="px-6 py-3">Shipping Date</th>
                                        <th scope="col" class="px-6 py-3">Status</th>
                                        <th scope="col" class="px-6 py-3">Delivery Method</th>
                                        <th scope="col" class="px-6 py-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($shipments as $data)
                                        <tr wire:key
                                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ ucfirst($data->shipping_plan_num) }}
                                            </th>
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $data->branchAllocation->branch->name ?? '—' }}
                                            </th>
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ \Carbon\Carbon::parse($data->scheduled_ship_date)->format('M d, Y') }}
                                            </th>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="px-2 py-1 rounded-full text-white text-xs font-semibold
                                                    @if ($data->shipping_status === 'pending') bg-yellow-500
                                                    @elseif ($data->shipping_status === 'approved') bg-blue-500
                                                    @elseif ($data->shipping_status === 'in_transit') bg-orange-500
                                                    @elseif ($data->shipping_status === 'completed') bg-green-600
                                                    @elseif ($data->shipping_status === 'cancelled') bg-red-600
                                                    @elseif ($data->shipping_status === 'damaged') bg-red-600
                                                    @elseif ($data->shipping_status === 'incomplete') bg-yellow-500
                                                    @else bg-gray-500 @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $data->shipping_status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $deliveryMethods[$data->delivery_method] ?? $data->delivery_method }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($data->shipping_status == 'pending')
                                                    <div x-data>
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
                                        <td colspan="5" class="text-center py-4">
                                            No shipping request found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="py-4 px-3">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <label for="perPage" class="text-sm font-medium text-gray-900 dark:text-white">Per Page</label>
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