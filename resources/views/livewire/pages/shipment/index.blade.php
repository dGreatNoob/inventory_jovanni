<x-slot:header>Shipment</x-slot:header>
<x-slot:subheader>Manage Shipments</x-slot:subheader>

<div class="pb-6">
    <div class="w-full flex flex-col gap-4">
        @if ($errors->any())
            <div class="rounded-lg p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                <ul class="list-disc pl-5 text-red-800 dark:text-red-200 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Create New Shipment -->
        <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg" x-data="{ open: @entangle('showCreateSection').live }" x-effect="if (open) $el.scrollIntoView({ behavior: 'smooth', block: 'start' })" id="create-shipment-section">
            <button type="button"
                @click="open = !open"
                class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create New Shipment</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Add a new shipment</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                    :class="{ 'rotate-180': open }"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-collapse class="border-t border-gray-200 dark:border-gray-700">
                <form wire:submit.prevent="createShipment" class="p-6 space-y-6">
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
                                        label="Default Vehicle Plate (if not set per vehicle)"
                                        placeholder="e.g. ABC-1234"
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

                                @if(!$editValue)
                                <!-- Summary DR Selection (create path) -->
                                <div>
                                    <label for="selectedSummaryDrId" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                        Select Summary DR
                                    </label>
                                    <select id="selectedSummaryDrId"
                                            wire:model.live="selectedSummaryDrId"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        <option value="">Select a Summary DR from Packing/Scan...</option>
                                        @foreach($availableSummaryDRs as $dr)
                                            <option value="{{ $dr->id }}">{{ $dr->dr_number }} | {{ $dr->branchAllocation->branch->name ?? '—' }} | {{ $dr->branchAllocation->batchAllocation->ref_no ?? '—' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @else
                                <!-- Edit: show Summary DR read-only -->
                                @if($this->editingShipment && $this->editingShipment->deliveryReceipt)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Summary DR</label>
                                    <p class="text-sm font-mono text-gray-900 dark:text-white">{{ $this->editingShipment->deliveryReceipt->dr_number }}</p>
                                </div>
                                @endif
                                @endif
                            </div>

                            <!-- Single Summary DR Preview (create path) -->
                            @if(!$editValue && $this->selectedSummaryDr)
                            @php
                                $drIds = [$this->selectedSummaryDr->id];
                                $childDRs = \App\Models\DeliveryReceipt::where('parent_dr_id', $this->selectedSummaryDr->id)->pluck('id');
                                $drIds = array_merge($drIds, $childDRs->toArray());
                                $drScannedItems = \App\Models\BranchAllocationItem::whereIn('delivery_receipt_id', $drIds)->where('scanned_quantity', '>', 0)->with('product')->get();
                                $drTotalItems = $drScannedItems->sum('scanned_quantity');
                                $drUniqueProducts = $drScannedItems->unique('product_id')->count();
                            @endphp
                            <div class="mt-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    Summary DR: {{ $this->selectedSummaryDr->dr_number }}
                                </h4>
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $this->selectedSummaryDr->branchAllocation->branch->name ?? '—' }} · {{ $this->selectedSummaryDr->branchAllocation->batchAllocation->ref_no ?? '—' }}</p>
                                            <div class="mt-2">
                                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Plate Number</label>
                                                <input type="text"
                                                    wire:model.defer="vehiclePlates.{{ $this->selectedSummaryDr->id }}"
                                                    placeholder="{{ $vehicle_plate_number ?: 'e.g. ABC-1234' }}"
                                                    class="w-full md:w-48 px-3 py-2 text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                />
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $drTotalItems }} items</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $drUniqueProducts }} unique products</div>
                                        </div>
                                    </div>
                                    @if($drScannedItems->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs mt-2">
                                        @foreach($drScannedItems->take(3) as $item)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">{{ $item->product->name ?? '—' }}</span>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $item->scanned_quantity }}</span>
                                            </div>
                                        @endforeach
                                        @if($drScannedItems->count() > 3)
                                            <div class="text-gray-500 dark:text-gray-400 italic">+{{ $drScannedItems->count() - 3 }} more products</div>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Edit: existing shipment vehicles -->
                            @if($editValue && $this->editingShipment && $this->editingShipment->vehicles->count() > 0)
                            <div class="mt-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Shipment vehicles</h4>
                                <div class="space-y-4">
                                    @foreach($this->editingShipment->vehicles as $vehicle)
                                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                            <div class="flex justify-between items-center">
                                                <span class="font-medium text-gray-900 dark:text-white">DR {{ $vehicle->deliveryReceipt->dr_number ?? '—' }}</span>
                                                <input type="text"
                                                    wire:model.defer="vehiclePlates.{{ $vehicle->delivery_receipt_id }}"
                                                    placeholder="Plate number"
                                                    class="w-48 px-3 py-2 text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                />
                                            </div>
                                        </div>
                                    @endforeach
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
            </div>
        </section>

        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="transition duration-500 ease-in-out" x-transition>
                <x-flash-message />
            </div>
        @endif

        @if (session()->has('error'))
            <div class="rounded-lg p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                <p class="text-red-800 dark:text-red-200 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Shipment List -->
        <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Shipment List</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Track and manage all shipments</p>
                </div>
                <button type="button" wire:click="openCreateSection" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Shipment
                </button>
            </div>

            <!-- Search and Filter Section -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search shipments...">
                        </div>
                        <div>
                            <label for="statusFilter" class="sr-only">Status</label>
                            <select id="statusFilter" wire:model.live="statusFilter"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-40 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
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
                    <div class="flex items-center space-x-3">
                        <label class="text-sm font-medium text-gray-900 dark:text-white whitespace-nowrap">Per Page</label>
                        <select wire:model.live="perPage"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-24 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Results Info -->
            <div class="px-6 py-3 text-sm text-gray-700 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50">
                @if($shipments->total() > 0)
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $shipments->total() }}</span> shipments found
                @else
                    <span class="font-semibold text-gray-900 dark:text-white">No shipments found</span>
                @endif
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Shipment Reference Number</th>
                                        <th scope="col" class="px-6 py-3">Vehicles / DR</th>
                                        <th scope="col" class="px-6 py-3">Branch</th>
                                        <th scope="col" class="px-6 py-3">Shipping Date</th>
                                        <th scope="col" class="px-6 py-3">Status</th>
                                        <th scope="col" class="px-6 py-3">Delivery Method</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse ($shipments as $data)
                                        <tr wire:key="shipment-{{ $data->id }}"
                                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ ucfirst($data->shipping_plan_num) }}
                                            </th>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                                @if($data->vehicles->isNotEmpty())
                                                    @foreach($data->vehicles as $v)
                                                        <div>{{ $v->plate_number ?: '—' }} (DR: {{ $v->deliveryReceipt->dr_number ?? '—' }})</div>
                                                    @endforeach
                                                @else
                                                    {{ $data->deliveryReceipt->dr_number ?? $data->vehicle_plate_number ?? '—' }}
                                                @endif
                                            </td>
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
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                            No shipments found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $shipments->links('livewire::tailwind', ['scrollTo' => false]) }}
            </div>
        </div>
    </div>
</div>