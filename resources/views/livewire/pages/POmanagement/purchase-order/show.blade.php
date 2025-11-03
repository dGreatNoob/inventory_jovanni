<x-slot:header>Purchase Order</x-slot:header>
<x-slot:subheader>Purchase Order Details</x-slot:subheader>

<div class="pb-20">

    {{--<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- QR Code Panel -->
        <div class="bg-zinc-900 dark:bg-zinc-900 rounded-lg border border-zinc-700 p-8 flex flex-col items-center shadow">
            <h3 class="text-lg font-semibold text-zinc-100 mb-6 text-center">QR Code</h3>
            <div class="bg-white p-6 rounded-xl shadow-lg border-4 border-yellow-300 mb-4 flex justify-center">
                {!! QrCode::size(220)->generate($purchaseOrder->po_num) !!}
            </div>
            <button onclick="window.print()" type="button"
                class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 dark:bg-yellow-700 dark:hover:bg-yellow-800 dark:focus:ring-yellow-800 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print QR Code
            </button>
        </div>
        <!-- Details Panel -->
        <div class="bg-zinc-900 dark:bg-zinc-900 rounded-lg border border-zinc-700 p-8 shadow">
            <h3 class="text-lg font-semibold text-zinc-100 mb-6 text-center">Purchase Order Details</h3>
            <div class="space-y-4">
                <div>
                    <span class="block text-xs font-bold text-zinc-400 mb-1">Supplier:</span>
                    <span class="block px-3 py-2 bg-blue-900 text-blue-100 rounded-lg font-semibold">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-zinc-400 mb-1">Department:</span>
                    <span class="block px-3 py-2 bg-green-900 text-green-100 rounded-lg font-semibold">{{ $purchaseOrder->department->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-zinc-400 mb-1">Order Date:</span>
                    <span class="block px-3 py-2 bg-purple-900 text-purple-100 rounded-lg font-semibold">{{ $purchaseOrder->order_date->format('M d, Y') }}</span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-zinc-400 mb-1">Ordered By:</span>
                    <span class="block px-3 py-2 bg-yellow-900 text-yellow-100 rounded-lg font-semibold">{{ $purchaseOrder->orderedByUser ? $purchaseOrder->orderedByUser->name : 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>--}}
    <!-- Purchase Order Details Card -->
    <x-collapsible-card title="Purchase Order Details" open="true" size="full">
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <!-- PO Number -->
            <x-input 
                type="text" 
                name="po_num" 
                value="{{ $purchaseOrder->po_num }}" 
                label="PO Number" 
                disabled="true" />

            <!-- Ordered By -->
            <x-input 
                type="text" 
                name="ordered_by" 
                value="{{ $purchaseOrder->orderedByUser ? $purchaseOrder->orderedByUser->name : 'N/A' }}" 
                label="Ordered By" 
                disabled="true" />

            <!-- Status -->
            <x-input 
                type="text" 
                name="status" 
                value="{{ str_replace('_', ' ', ucfirst($purchaseOrder->status)) }}" 
                label="Status" 
                disabled="true" />

            <!-- Supplier -->
            <x-input 
                type="text" 
                name="supplier" 
                value="{{ $purchaseOrder->supplier->name ?? 'N/A' }}" 
                label="Supplier" 
                disabled="true" />

            <!-- Receiving Department -->
            <x-input 
                type="text" 
                name="receiving_department" 
                value="{{ $purchaseOrder->department->name ?? 'N/A' }}" 
                label="Receiving Department" 
                disabled="true" />

            <!-- Order Date -->
            <x-input 
                type="text" 
                name="order_date" 
                value="{{ $purchaseOrder->order_date->format('M d, Y') }}" 
                label="Order Date" 
                disabled="true" />

            <!-- Expected Delivery -->
            <x-input 
                type="text" 
                name="expected_delivery" 
                value="{{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('M d, Y') : 'N/A' }}" 
                label="Expected Delivery" 
                disabled="true" />

            <!-- Payment Terms -->
            <x-input 
                type="text" 
                name="payment_terms" 
                value="{{ $purchaseOrder->payment_terms }}" 
                label="Payment Terms" 
                disabled="true" />

            <!-- Approver -->
            <x-input 
                type="text" 
                name="approver" 
                value="{{ $purchaseOrder->approver ? $purchaseOrder->approverInfo->name : 'N/A' }}" 
                label="Approver" 
                disabled="true" />
        </div>
    </x-collapsible-card>

    <!-- Ordered Items Card -->
    <x-collapsible-card title="Ordered Items" open="true" size="full">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">SKU</th>
                        <th scope="col" class="px-6 py-3">Product Name</th>
                        <th scope="col" class="px-6 py-3">Category</th>
                        <th scope="col" class="px-6 py-3">Supplier</th>
                        <th scope="col" class="px-6 py-3">Supplier Code</th>
                        <th scope="col" class="px-6 py-3 text-right">Unit Price</th>
                        <th scope="col" class="px-6 py-3 text-right">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-right">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrder->productOrders ?? [] as $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $item->product->sku ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">{{ $item->product->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $item->product->category->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $item->product->supplier->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $item->product->supplier_code ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-right">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-6 py-4 text-right">
                                {{ number_format($item->quantity, 2) }} {{ $item->product->uom ?? 'pcs' }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">
                                ₱{{ number_format($item->total_price, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-500 dark:text-gray-400">No Items Found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="text-sm font-semibold text-gray-900 bg-gray-100 dark:bg-gray-700 dark:text-white">
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-right">Total:</td>
                        <td class="px-6 py-4 text-right">{{ number_format($purchaseOrder->total_qty ?? 0, 2) }}</td>
                        <td class="px-6 py-4 text-right">₱{{ number_format($purchaseOrder->total_price ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-collapsible-card>

    <!-- Approval History Section (Optional - if you want to show approval logs) -->
    @if($purchaseOrder->approvalLogs && $purchaseOrder->approvalLogs->count() > 0)
        <x-collapsible-card title="Approval History" open="false" size="full">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">Date & Time</th>
                            <th scope="col" class="px-4 py-3">User</th>
                            <th scope="col" class="px-4 py-3">Action</th>
                            <th scope="col" class="px-4 py-3">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->approvalLogs as $log)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $log->created_at->format('M d, Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3">{{ $log->user->name ?? 'System' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if ($log->action === 'approved') bg-green-100 text-green-800
                                        @elseif($log->action === 'rejected') bg-red-100 text-red-800
                                        @elseif($log->action === 'delivered') bg-blue-100 text-blue-800
                                        @elseif($log->action === 'received') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $log->remarks ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-collapsible-card>
    @endif

    <!-- Fixed Bottom Actions -->
    <div class="fixed bottom-0 right-0 left-0 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 z-40">
        <div class="flex justify-end space-x-3 max-w-screen-2xl mx-auto">
            <a href="{{ route('pomanagement.purchaseorder') }}" 
                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 dark:hover:text-white dark:focus:ring-gray-700">
                Back to List
            </a>

            @php
                use App\Enums\Enum\PermissionEnum;
            @endphp

            @can('po approve')
                @if($purchaseOrder->status === 'pending')
                    <button type="button" 
                        wire:click="ApprovePurchaseOrder"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approve Purchase Order
                    </button>
                    
                    <button type="button" 
                        wire:click="RejectPurchaseOrder"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reject Purchase Order
                    </button>
                @else
                    <div class="px-5 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400">
                        Status: <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $purchaseOrder->status)) }}</span>
                    </div>
                @endif
            @endcan
        </div>
    </div>
</div>