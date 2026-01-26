<x-slot:header>Sales Return Management</x-slot:header>
<x-slot:subheader>Manage sales returns from branches</x-slot:subheader>

<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="transition duration-500 ease-in-out" x-transition>
            <x-flash-message />
        </div>
    @endif

    <!-- Create Sales Return Button -->
    <div class="mb-6">
        <x-button wire:click="openCreateModal" variant="primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create Sales Return
        </x-button>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
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
                class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Search sales returns..." required="">
        </div>
    </div>

    <!-- Sales Returns Table -->
    <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Sales Return No.</th>
                        <th scope="col" class="px-6 py-3">Branch</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($salesReturns as $salesReturn)
                        <tr wire:key="{{ $salesReturn->id }}"
                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $salesReturn->sales_return_number }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $salesReturn->branch->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-white text-xs font-semibold
                                    @if ($salesReturn->status === 'pending') bg-yellow-500
                                    @elseif ($salesReturn->status === 'completed') bg-green-600
                                    @else bg-gray-500 @endif">
                                    {{ ucfirst($salesReturn->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="viewSalesReturn({{ $salesReturn->id }})"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline mr-2">
                                    View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                No sales returns found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="py-4 px-3">
            {{ $salesReturns->links() }}
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal wire:model="showCreateModal" class="md:w-4/5">
        <div class="space-y-6">
            <!-- Modal Header -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    Create Sales Return
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Select a branch and products from completed shipments to create a sales return.
                </p>
            </div>

            <form wire:submit.prevent="createSalesReturn" class="space-y-6">
                <!-- Branch Selection -->
                <div>
                    <x-dropdown
                        wire:model.live="selectedBranch"
                        name="selectedBranch"
                        label="Branch"
                        :options="$branches->pluck('name', 'id')"
                        placeholder="Select Branch"
                        class="w-full"
                    />
                    @error('selectedBranch')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Shipment Selection -->
                @if($selectedBranch)
                <div>
                    <x-dropdown
                        wire:model.live="selectedShipment"
                        name="selectedShipment"
                        label="Completed Shipment"
                        :options="$shipments->pluck('shipping_plan_num', 'id')"
                        placeholder="Select Completed Shipment"
                        class="w-full"
                    />
                    @error('selectedShipment')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>
                @endif

                <!-- Return Reason -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Return Reason
                    </label>
                    <textarea
                        wire:model="returnReason"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Enter reason for return..."
                    ></textarea>
                    @error('returnReason')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Product Selection -->
                @if($selectedShipment && !empty($selectedItems))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Products to Return
                    </label>
                    <div class="space-y-3 max-h-60 overflow-y-auto">
                        @foreach($selectedItems as $index => $item)
                        <div class="flex items-center space-x-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $item['product_name'] }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    Available: {{ $item['available_quantity'] }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input
                                    type="number"
                                    wire:model="selectedItems.{{ $index }}.quantity"
                                    min="0"
                                    max="{{ $item['available_quantity'] }}"
                                    class="w-20 px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                    placeholder="Qty"
                                >
                                <input
                                    type="text"
                                    wire:model="selectedItems.{{ $index }}.reason"
                                    class="flex-1 px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                    placeholder="Reason"
                                >
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @error('selectedItems')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>
                @endif

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <x-button type="button" wire:click="closeCreateModal" variant="secondary">
                        Cancel
                    </x-button>
                    <x-button type="submit" variant="primary">
                        Create Sales Return
                    </x-button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- View Modal -->
    <x-modal wire:model="showViewModal" class="md:w-4/5">
        <div class="space-y-6">
            <!-- Modal Header -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    Sales Return Details
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $selectedSalesReturn ? $selectedSalesReturn->sales_return_number : '' }}
                </p>
            </div>

            @if($selectedSalesReturn)
            <div class="space-y-4">
                <!-- Return Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Branch</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $selectedSalesReturn->branch->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                            @if ($selectedSalesReturn->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                            @elseif($selectedSalesReturn->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300 @endif">
                            {{ ucfirst($selectedSalesReturn->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipment</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $selectedSalesReturn->shipment->shipping_plan_num ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created By</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $selectedSalesReturn->creator->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Return Reason -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Return Reason</label>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $selectedSalesReturn->reason ?? 'N/A' }}</p>
                </div>

                <!-- Return Items -->
                @if($selectedSalesReturn->items->count() > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Return Items</label>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-2">Product</th>
                                    <th class="px-4 py-2">Quantity</th>
                                    <th class="px-4 py-2">Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedSalesReturn->items as $item)
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                    <td class="px-4 py-2">{{ $item->branchAllocationItem->display_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $item->quantity }}</td>
                                    <td class="px-4 py-2">{{ $item->reason ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                    @if($selectedSalesReturn->status === 'pending')
                    <x-button wire:click="completeSalesReturn" variant="primary">
                        Complete Sales Return
                    </x-button>
                    @endif
                    <x-button type="button" wire:click="closeViewModal" variant="secondary">
                        Close
                    </x-button>
                </div>
            </div>
            @endif
        </div>
    </x-modal>
</div>
