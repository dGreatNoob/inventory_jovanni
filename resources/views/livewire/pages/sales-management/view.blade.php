<div class="mb-14">
    <x-collapsible-card title="Sales Order Details" open="true" size="full">
        <form x-show="open" x-transition>   
            <div class="grid gap-6 mb-2 md:grid-cols-2">         
                <x-dropdown 
                    wire:model.defer="status"
                    value="{{ $sales_order_view->status }}" 
                    name="status" 
                    label="Status" 
                    :options="[
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'returned' => 'Returned',
                        'on hold' => 'On Hold',
                        'released' =>'Released',
                        'partially released' => 'Partially Released'
                    ]"
                    placeholder="Select a Status" 
                    readonly 
                    disabled  
                />

               <x-input
                    type="text"
                    value="{{ $sales_order_view->agents->pluck('name')->join(', ') }}"
                    name="selected_agents"
                    label="Selected Agent/s"
                    readonly
                    disabled
                />
            </div>

            <div class="grid gap-6 mb-2 md:grid-cols-2">
                <x-input
                    type="text"
                    value="{{ $sales_order_view->customers->pluck('name')->join(', ') }}"
                    name="selected_branches"
                    label="Selected Branch/es"
                    readonly
                    disabled
                />
                <x-input 
                    type="text" 
                    rows="8" 
                    value="{{$sales_order_view->contact_person_name}}" 
                    name="contactPersonName" 
                    label="Contact Person’s Name" 
                    readonly 
                    disabled   
                />
            </div>
              
            <div class="grid gap-6 mb-2 md:grid-cols-2">
                <x-input 
                    type="text" 
                    value="{{$sales_order_view->phone}}" 
                    rows="8" 
                    name="phone" 
                    label="Phone" 
                    readonly 
                    disabled 
                />
                <x-input 
                    type="text" 
                    value="{{$sales_order_view->email}}" 
                    rows="8" 
                    name="email" 
                    label="Email" 
                    readonly 
                    disabled 
                />
            </div>

            <x-input 
                type="textarea" 
                value="{{$sales_order_view->billing_address}}" 
                rows="2" 
                name="billingAddress" 
                label="Billing Address" 
                readonly 
                disabled   
            />
            
            <x-input 
                type="textarea" 
                value="{{$sales_order_view->shipping_address}}" 
                rows="2" 
                name="shippingAddress" 
                label="Shipping Address" 
                readonly 
                disabled   
            />  
          
            <div class="grid gap-6 mb-2 md:grid-cols-2">
                <x-input 
                    type="text" 
                    value="{{$paymentMethodDropdown[$sales_order_view->payment_method] ?? ''}}" name="paymentMethod" 
                    label="Payment Method" 
                    readonly 
                    disabled   
                />

                <div class="mb-4">                            
                    <label 
                        for="deliveryDate" 
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Shipping Method
                    </label>
                    <div class="relative">                            
                        <input 
                            type="text" 
                            value="{{ $shippingMethodDropDown[$sales_order_view->shipping_method] ?? '' }}" 
                            name="shippingMethod" 
                            id="shippingMethod"
                            placeholder="" 
                            readonly="" 
                            disabled="" 
                            class="block w-full text-sm rounded-lg border  bg-gray-50 border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500  p-2.5"
                        >
                    </div>                       
                </div>
            </div>
            <div class="grid gap-6 mb-2 md:grid-cols-2">
                <x-input 
                    type="text" 
                    value="{{$paymentTermsDropdown[$sales_order_view->payment_terms] ?? ''}}" 
                    name="paymentTerms" 
                    label="Payment terms" 
                    readonly 
                    disabled   
                />
                <x-input 
                    type="text" 
                    value="{{$sales_order_view->delivery_date}}" 
                    name="deliveryDate" 
                    label="Delivery date" 
                    readonly 
                    disabled   
                />
            </div> 
             
            <div class="grid gap-6 mb-5 md:grid-cols-1 ">
                <div class="overflow-x-auto">
                <h2 
                    class="text-lg font-semibold text-gray-900 dark:text-white">
                    Items
                </h2>
                <table class="mt-5 w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead
                        class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Product Sku
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Qty
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Item Price
                            </th>
                        </tr>
                    </thead>
                        <tbody>
                            {{-- @php
                                use App\Enums\Enum\PermissionEnum;
                            @endphp --}}
                            @forelse ($sales_order_view->items as $data)
                                <tr wire:key
                                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">                                       
                                        <?php
                                            echo $data->product ? ($data->product->sku ?? 'N/A') : 'Product Not Found';
                                        ?>
                                    </th>                                      
                                    <td class="px-6 py-4">
                                        {{$data->quantity}}
                                    </td>                                       
                                    <td class="px-6 py-4">
                                        {{$data->unit_price}}
                                    </td> 
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        No request sales orders found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
              </div>              
        </form>

    </x-collapsible-card>

    <div
        class="ml-15 fixed bottom-0 right-0 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 w-full">

        <div class="flex justify-end space-x-4">
            <a href="{{ route('salesorder.index') }}"
                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                Back to List
            </a>

            @php
                use App\Enums\Enum\PermissionEnum;
            @endphp

            {{-- @can(PermissionEnum::APPROVE_REQUEST_SLIP->value)              --}}
            @if($sales_order_view->status !=='released' && $sales_order_view->status !=='partially released')
                <x-button 
                    type="button" 
                    wire:click="approveSalesOrder" 
                    :disabled="$sales_order_view->status === 'approved'" 
                    class="flex justify-end space-x-4">
                    Approve Sales Order
                </x-button>
                <x-button 
                    type="button" 
                    wire:click="rejectSalesOrder" 
                    variant="danger" 
                    :disabled="$sales_order_view->status === 'rejected'" 
                    class="flex justify-end space-x-4">
                    Reject Sales Order
                </x-button>
            @endif
            {{-- @endcan --}}

        </div>
    </div>
</div>
