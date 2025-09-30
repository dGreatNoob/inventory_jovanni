<div class="mb-14">
    <x-collapsible-card title="Sales Order Details" open="true" size="full">
        <form x-show="open" x-transition>   
            <div class="grid gap-6 mb-2 md:grid-cols-2">         
                <x-dropdown                
                    value="{{ $sales_return_view->status }}" 
                    name="status" 
                    label="Status" 
                    :options="[                    
                        'pending'   => 'Pending',  
                        'approved'  => 'Approved',                   
                        'rejected'  => 'Rejected',
                        'confirmed' => 'Confirmed',
                        'processing'=> 'Processing',
                    ]"
                    placeholder="Select a Status" 
                    readonly 
                    disabled  
                />
           
                <x-input               
                    value="{{ $sales_return_view->salesorder->sales_order_number }}" 
                    name="sales_order_number" 
                    label="Sales Order Number"                 
                    placeholder="Select a Sales Order Number" 
                    readonly 
                    disabled  
                />
            </div>

            <div class="grid gap-6 mb-2 md:grid-cols-2">
                <x-dropdown 
                    readonly 
                    disabled 
                    value="{{$sales_return_view->customer_id}}" 
                    name="customerSelected" 
                    label="Customer Name"  
                    :options="$company_results" 
                    placeholder="Select a Customer"
                />

                <x-input 
                    type="text" 
                    rows="8" 
                    value="{{$sales_return_view->return_date}}" 
                    name="return_date" 
                    label="Return Date" 
                    readonly 
                    disabled
                />
            </div>
              
            <div class="grid gap-6 mb-2 md:grid-cols-2">
                <x-input 
                    type="text" 
                    value="{{$sales_return_view->return_reference}}" 
                    rows="8" 
                    name="return_reference" 
                    label="Return Reference" 
                    readonly 
                    disabled 
                />

                <?php 
                    $returnView = $sales_return_view->is_full_return ? 'Full Return' : "Partial Return";
                ?>

                <x-input 
                    type="text" 
                    value="{{$returnView}}" 
                    rows="8" 
                    name="is_full_return" 
                    label="Is Full Return" 
                    readonly 
                    disabled 
                />
            </div>
            
            <div class="grid gap-6 mb-2 md:grid-cols-2">
                <x-input 
                    type="text" 
                    value="{{$sales_return_view->total_refund}}" 
                    name="total_refund" 
                    label="Total Refund" 
                    readonly 
                    disabled   
                />       
                         
                <x-input 
                    readonly 
                    disabled 
                    value="{{$userName}}" 
                    name="processed_by" 
                    label="Processed By"                    
                    placeholder="Processed By"
                />
            </div>  
        </form>
    </x-collapsible-card>

    <div
        class="ml-15 fixed bottom-0 right-0 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 w-full">

        <div class="flex justify-end space-x-4">
            <a href="{{ route('salesorder.return') }}"
                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                Back to List
            </a>

            @php
                use App\Enums\Enum\PermissionEnum;
            @endphp
            {{-- @can(PermissionEnum::APPROVE_REQUEST_SLIP->value)              --}}
                <x-button 
                    type="button" 
                    wire:click="approveSalesOrder" 
                    :disabled="$sales_return_view->status === 'approved'" 
                    class="flex justify-end space-x-4">
                    Approve Sales Return
                </x-button>
                <x-button 
                    type="button" 
                    wire:click="rejectSalesOrder" 
                    variant="danger" 
                    :disabled="$sales_return_view->status === 'rejected'" 
                    class="flex justify-end space-x-4">
                    Reject Sales Return
                </x-button>
            {{-- @endcan --}}
        </div>
    </div>
</div>
