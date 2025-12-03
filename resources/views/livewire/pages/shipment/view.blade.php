<div class="mb-14">
    <x-collapsible-card title="Shipment Details" open="true" size="full">
        <form x-show="open" x-transition>
            <!-- Shipment Information Section -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
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
                            value="{{$shipment_view->shipping_plan_num}}"
                            name="shipping_plan_num"
                            label="Shipment Reference Number"
                            readonly
                            disabled
                        />
                    </div>

                    <div>
                        <x-input
                            type="text"
                            value="{{$shipment_view->scheduled_ship_date}}"
                            name="scheduled_ship_date"
                            label="Scheduled Ship Date"
                            readonly
                            disabled
                        />
                    </div>

                    <div>
                        <x-input
                            type="text"
                            value="{{$shipment_view->vehicle_plate_number}}"
                            name="vehicle_plate_number"
                            label="Vehicle Plate Number"
                            readonly
                            disabled
                        />
                    </div>
                </div>

                <div class="mt-4">
                    <x-input
                        type="text"
                        value="{{$shipment_view->delivery_method}}"
                        name="delivery_method"
                        label="Delivery Method"
                        readonly
                        disabled
                    />
                </div>
            </div>

            <!-- Status Information -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Status Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Shipping Status</label>
                        <span class="inline-block px-3 py-2 text-sm font-semibold rounded-full
                            @if ($shipment_view->shipping_status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                            @elseif($shipment_view->shipping_status === 'approved') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                            @elseif($shipment_view->shipping_status === 'in_transit') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300
                            @elseif($shipment_view->shipping_status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                            @elseif($shipment_view->shipping_status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                            @elseif($shipment_view->shipping_status === 'damaged') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                            @elseif($shipment_view->shipping_status === 'incomplete') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300 @endif">
                            {{ ucfirst(str_replace('_', ' ', $shipment_view->shipping_status)) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Created At</label>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ $shipment_view->created_at->format('M d, Y h:i A') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Branch Information -->
            @if($shipment_view->branchAllocation && $shipment_view->branchAllocation->branch)
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Branch Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Branch Name</label>
                        <p class="text-sm text-gray-900 dark:text-white font-medium">
                            {{ $shipment_view->branchAllocation->branch->name }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Branch Address</label>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ $shipment_view->branchAllocation->branch->address ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

        </form>
    </x-collapsible-card>

    <!-- Action Buttons -->
    <div class="fixed bottom-0 right-0 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 w-full">
        <div class="flex justify-end space-x-4">
            <a href="{{ route('shipment.index') }}"
                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                Back to List
            </a>

            @php
                use App\Enums\Enum\PermissionEnum;
            @endphp

            <x-button
                type="button"
                wire:click="approveSalesOrder"
                :disabled="$shipment_view->shipping_status !== 'pending'"
                class="flex justify-end space-x-4">
                Approve Shipment
            </x-button>

            <x-button
                type="button"
                wire:click="rejectSalesOrder"
                variant="danger"
                :disabled="$shipment_view->shipping_status === 'cancelled'"
                class="flex justify-end space-x-4">
                Cancel Shipment
            </x-button>
        </div>
    </div>
</div>
