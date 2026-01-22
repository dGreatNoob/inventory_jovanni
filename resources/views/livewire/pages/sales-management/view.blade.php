<div class="mb-14">
    <!-- Flash Messages -->
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif
    @if (session()->has('message'))
        <div class="mb-4 p-4 text-sm rounded-lg bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-400 border border-green-200 dark:border-green-800">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 text-sm rounded-lg bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 border border-red-200 dark:border-red-800">
            {{ session('error') }}
        </div>
    @endif

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
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Branch Items
                        </h2>
                    </div>

                    @php
                        $overallTotal = 0;
                        $overallQuantity = 0;
                    @endphp

                    @foreach($sales_order_view->customers as $branch)
                        @php
                            $branchItems = $sales_order_view->branchItems()->where('branch_id', $branch->id)->with('product')->get();
                            $branchTotal = $branchItems->sum('subtotal');
                            $branchQuantity = $branchItems->sum('quantity');
                            $overallTotal += $branchTotal;
                            $overallQuantity += $branchQuantity;
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
                                                <th scope="col" class="px-6 py-3">Product Name</th>
                                                <th scope="col" class="px-6 py-3">Product SKU</th>
                                                <th scope="col" class="px-6 py-3">Quantity</th>
                                                <th scope="col" class="px-6 py-3">Unit Price</th>
                                                <th scope="col" class="px-6 py-3">Subtotal</th>
                                                <th scope="col" class="px-6 py-3">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($branchItems as $item)
                                                <tr wire:key="{{ $item->id }}"
                                                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        {{ $item->product ? $item->product->name : 'N/A' }}
                                                    </th>
                                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        {{ $item->product ? $item->product->sku : 'N/A' }}
                                                    </th>
                                                    <td class="px-6 py-4">{{ $item->quantity }}</td>
                                                    <td class="px-6 py-4">
                                                        ₱{{ number_format($item->unit_price, 2) }}
                                                    </td>
                                                    <td class="px-6 py-4 font-semibold">
                                                        ₱{{ number_format($item->subtotal, 2) }}
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <button
                                                            type="button"
                                                            wire:click="openChangePriceModal({{ $item->id }})"
                                                            class="text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-xs px-3 py-1.5 text-center dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800"
                                                        >
                                                            Edit Price
                                                        </button>
                                                    </td>
                                            @endforeach
                                            <tr class="bg-blue-50 dark:bg-blue-900/20 font-semibold">
                                                <td colspan="2" class="px-6 py-3 text-right">Branch Total:</td>
                                                <td class="px-6 py-3">{{ number_format($branchQuantity) }}</td>
                                                <td class="px-6 py-3"></td>
                                                <td class="px-6 py-3">₱{{ number_format($branchTotal, 2) }}</td>
                                                <td class="px-6 py-3"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if($overallTotal > 0)
                        <div class="mt-6">
                            <table class="w-full text-lg text-left rtl:text-right text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                <tbody>
                                    <tr class="bg-green-50 dark:bg-green-900/20 font-semibold">
                                        <td class="px-6 py-3"></td>
                                        <td class="px-4 py-3"></td>
                                        <td colspan="3" class="px-6 py-3 text-right">Total Quantity</td>
                                        <td class="px-6 py-3 text-center">{{ number_format($overallQuantity) }}</td>
                                        <td class="px-6 py-3 text-right">Total Sales</td>
                                        <td class="px-6 py-3 text-center">₱{{ number_format($overallTotal, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </form>

    </x-collapsible-card>

    <!-- Change Price Modal -->
    @if($showChangePriceModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-4">
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $selectedBranchItemId ? 'Edit' : 'Change Price' }}
                    </h3>
                    <button
                        type="button"
                        wire:click="closeChangePriceModal"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    >
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                
                <form wire:submit.prevent="updatePrice" class="p-4">
                    <div class="grid gap-4 mb-4 sm:grid-cols-1">
                        <div>
                            <label for="selectedBranchId" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Branch
                            </label>
                            <select
                                wire:model="selectedBranchId"
                                id="selectedBranchId"
                                disabled
                                class="block w-full text-sm rounded-lg border bg-gray-50 border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 p-2.5"
                                required
                            >
                                <option value="">Select a Branch</option>
                                @foreach($availableBranches as $branchId => $branchName)
                                    <option value="{{ $branchId }}">{{ $branchName }}</option>
                                @endforeach
                            </select>
                            @error('selectedBranchId')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="selectedProductId" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Product
                            </label>
                            <select
                                wire:model="selectedProductId"
                                id="selectedProductId"
                                disabled
                                class="block w-full text-sm rounded-lg border bg-gray-50 border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 p-2.5"
                                required
                                {{ empty($selectedBranchId) ? 'disabled' : '' }}
                            >
                                <option value="">Select a Product</option>
                                @foreach($filteredProducts as $productId => $productName)
                                    <option value="{{ $productId }}">{{ $productName }}</option>
                                @endforeach
                            </select>
                            @error('selectedProductId')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($currentUnitPrice)
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Current Unit Price
                            </label>
                            <div class="block w-full text-sm rounded-lg border border-gray-300 bg-gray-100 dark:bg-gray-700 dark:border-gray-600 p-2.5">
                                ₱{{ number_format($currentUnitPrice, 2) }}
                            </div>
                        </div>
                        @endif
                        
                        <div>
                            <label for="newUnitPrice" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Unit Price
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="0.01"
                                wire:model="newUnitPrice"
                                id="newUnitPrice"
                                class="block w-full text-sm rounded-lg border bg-gray-50 border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 p-2.5"
                                placeholder="Enter unit price"
                                required
                            >
                            @error('newUnitPrice')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-4 pt-4">
                        <button
                            type="button"
                            wire:click="closeChangePriceModal"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm font-medium px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        >
                            {{ $selectedBranchItemId ? 'Update Price' : 'Add Item' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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
        