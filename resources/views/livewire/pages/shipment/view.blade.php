<div class="mb-14">
    <x-collapsible-card title="Shipment Details" open="true" size="full">
        <form x-show="open" x-transition>   
            <div class="grid gap-6 mb-2 md:grid-cols-2">         
                <x-dropdown 
                    wire:model.defer="status"
                    value="{{ $shipment_view->shipping_status }}" 
                    name="status" 
                    label="Status" 
                    :options="[
                        'ready' => 'ready', 
                        'shipped' => 'shipped', 
                        'delivered' => 'delivered', 
                        'cancelled' => 'cancelled'
                    ]"
                    placeholder="Select a Status" 
                    readonly 
                    disabled  
                />

               <x-input 
                    type="text" 
                    value="{{$shipment_view->scheduled_ship_date}}" 
                    name="scheduled_ship_date" 
                    label="Scheduled Ship Date" 
                    readonly 
                    disabled   
                />  
            </div>

            <div class="grid gap-6 mb-2 md:grid-cols-2">
                <x-dropdown 
                    readonly 
                    disabled 
                    value="{{$shipment_view->carrier_name}}" 
                    name="carrier_name" 
                    label="Carrier Name"  
                    :options="$company_results" 
                    placeholder="Carrier Name"
                />
                <x-input 
                    type="text" 
                    rows="8" 
                    value="{{$shipment_view->vehicle_plate_number}}" 
                    name="vehicle_plate_number" 
                    label="Vehicle Plate Number" 
                    readonly 
                    disabled   
                />
            </div>
              
            <div class="grid gap-6 mb-2 md:grid-cols-2">
                <x-input 
                    type="textarea" 
                    value="{{$shipment_view->special_handling_notes}}" 
                    rows="8" 
                    name="special_handling_notes" 
                    label="Special Handling Notes" 
                    readonly 
                    disabled 
                />
                <x-input 
                    type="text" 
                    value="{{$shipment_view->customer_name}}" 
                    rows="8" 
                    name="customer_name" 
                    label="Customer Name" 
                    readonly 
                    disabled 
                />
            </div>

            <div class="grid gap-6 mb-2 md:grid-cols-2">
                <x-input 
                    type="text" 
                    value="{{$shipment_view->customer_phone}}" 
                    rows="8" 
                    name="phone" 
                    label="Phone" 
                    readonly 
                    disabled 
                />
                <x-input 
                    type="text" 
                    value="{{$shipment_view->customer_email}}" 
                    rows="8" 
                    name="Email"
                    label="Email" 
                    readonly 
                    disabled 
                />
            </div>


            <div class="grid gap-6 mb-2 md:grid-cols-2">
                <x-input 
                    type="text" 
                    value="{{$shipment_view->delivery_method}}" 
                    rows="2" 
                    name="delivery_method" 
                    label="Delivery Method" 
                    readonly 
                    disabled   
                />
                
                <x-input 
                    type="text" 
                    value="{{$shipment_view->shipping_priority}}" 
                    rows="2" 
                    name="shipping_priority" 
                    label="Shipping Priority" 
                    readonly 
                    disabled   
                />  
            </div>      
        </form>

    </x-collapsible-card>

    <div
        class="ml-15 fixed bottom-0 right-0 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 w-full">

        <div class="flex justify-end space-x-4">
            <a href="{{ route('shipment.index') }}"
                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                Back to List
            </a>

            @php
                use App\Enums\Enum\PermissionEnum;
            @endphp

            {{-- @can(PermissionEnum::APPROVE_REQUEST_SLIP->value)              --}}
            {{-- @if($shipment_view->shipment_status !=='released' && $shipment_view->shipment_status !=='partially released') --}}
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
            {{-- @endif --}}
            {{-- @endcan --}}

        </div>
    </div>
</div>
