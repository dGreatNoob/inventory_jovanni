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

                <div class="mb-4">
                    <label
                        for="deliveryMethod"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Delivery Method
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            value="{{ $shippingMethodDropDown[$sales_order_view->shipping_method] ?? '' }}"
                            name="deliveryMethod"
                            id="deliveryMethod"
                            placeholder=""
                            readonly=""
                            disabled=""
                            class="block w-full text-sm rounded-lg border  bg-gray-50 border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500  p-2.5"
                        >
                    </div>
                </div>
            </div>
             
            <!-- Branch Items Tables -->
            @if($sales_order_view->customers->count() > 0)
            <div class="grid gap-6 mb-5 md:grid-cols-1">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Branch Items
                    </h2>

                    @php
                        $overallTotal = 0;
                    @endphp

                    @foreach($sales_order_view->customers as $branch)
                        @php
                            $branchItems = $sales_order_view->branchItems()->where('branch_id', $branch->id)->with('product')->get();
                            $branchTotal = $branchItems->sum('subtotal');
                            $overallTotal += $branchTotal;
                        @endphp

                        @if($branchItems->count() > 0)
                            <div class="mb-6">
                                <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3 bg-gray-100 dark:bg-gray-700 p-3 rounded">
                                    {{ $branch->name }}
                                </h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-6 py-3">Product SKU</th>
                                                <th scope="col" class="px-6 py-3">Quantity</th>
                                                <th scope="col" class="px-6 py-3">Original Price</th>
                                                <th scope="col" class="px-6 py-3">Unit Price</th>
                                                <th scope="col" class="px-6 py-3">Subtotal</th>
                                                <th scope="col" class="px-6 py-3">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($branchItems as $item)
                                                <tr wire:key="{{ $item->id }}"
                                                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        {{ $item->product ? $item->product->sku : 'N/A' }}
                                                    </th>
                                                    <td class="px-6 py-4">{{ $item->quantity }}</td>
                                                    <td class="px-6 py-4">₱{{ number_format($item->original_unit_price, 2) }}</td>
                                                    <td class="px-6 py-4">
                                                        @if($editingItemId == $item->id)
                                                            <input
                                                                type="number"
                                                                step="0.01"
                                                                wire:model="editingPrice"
                                                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                                wire:keydown.enter="savePrice">
                                                        @else
                                                            ₱{{ number_format($item->unit_price, 2) }}
                                                            @if($item->unit_price != $item->original_unit_price)
                                                                <span class="text-red-500 text-xs">(Modified)</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 font-semibold">₱{{ number_format($item->subtotal, 2) }}</td>
                                                    <td class="px-6 py-4">
                                                        @if($editingItemId == $item->id)
                                                            <button wire:click="savePrice" class="text-green-600 hover:text-green-800 mr-2">Save</button>
                                                            <button wire:click="cancelEdit" class="text-gray-600 hover:text-gray-800">Cancel</button>
                                                        @else
                                                            <button wire:click="editPrice({{ $item->id }})" class="text-blue-600 hover:text-blue-800">Edit Price</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="bg-blue-50 dark:bg-blue-900/20 font-semibold">
                                                <td colspan="4" class="px-6 py-3 text-right">Branch Total:</td>
                                                <td class="px-6 py-3">₱{{ number_format($branchTotal, 2) }}</td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if($overallTotal > 0)
                        <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-green-900 dark:text-green-100">Overall Total Cost:</span>
                                <span class="text-xl font-bold text-green-600 dark:text-green-400">₱{{ number_format($overallTotal, 2) }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif
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
