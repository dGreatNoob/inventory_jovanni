<x-slot:header>Purchase Order</x-slot:header>
<x-slot:subheader>Purchase Order Details</x-slot:subheader>

<div class="mb-14">
    <div class="">
        <x-collapsible-card title="Purchase Order Details" open="true" size="full">
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <x-input type="text" name="po_num" value="{{ $purchaseOrder->po_num }}" label="PO Number" disabled="true" />
                <x-input type="text" name="ordered_by" value="{{ $purchaseOrder->orderedByUser ? $purchaseOrder->orderedByUser->name : 'N/A' }}" label="Ordered By" disabled="true" />
                <x-input type="text" name="status" value="{{ str_replace('_', ' ', ucfirst($purchaseOrder->status)) }}" label="Status" disabled="true" />
                <x-input type="text" name="supplier" value="{{ $purchaseOrder->supplier->name }}" label="Supplier" disabled="true" />
                <x-input type="text" name="receiving_department" value="{{ str_replace('_', ' ', ucfirst($purchaseOrder->department->name)) }}" label="Receiving Department" disabled="true" />
                <x-input type="text" name="order_date" value="{{ $purchaseOrder->order_date->format('M d, Y') }}" label="Order Date" disabled="true" />
                <x-input type="text" name="expected_delivery" value="{{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('M d, Y') : 'N/A' }}" label="Expected Delivery" disabled="true" />
                <x-input type="text" name="payment_terms" value="{{ $purchaseOrder->payment_terms }}" label="Payment Terms" disabled="true" />
                <x-input type="text" name="quotation" value="{{ $purchaseOrder->quotation ?? 'N/A' }}" label="Quotation" disabled="true" />
                <x-input type="text" name="approver" value="{{ $purchaseOrder->approver ? $purchaseOrder->approverInfo->name : 'N/A' }}" label="Approver" disabled="true" />
            </div>
        </x-collapsible-card>

        <x-collapsible-card title="Ordered Items" open="true" size="full">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">SKU</th>
                            <th scope="col" class="px-6 py-3">Product Name</th>
                            <th scope="col" class="px-6 py-3">Category</th>
                            <th scope="col" class="px-6 py-3">Supplier</th>
                            <th scope="col" class="px-6 py-3">Supplier Code</th>
                            <th scope="col" class="px-6 py-3">Unit Price</th>
                            <th scope="col" class="px-6 py-3">Quantity</th>
                            <th scope="col" class="px-6 py-3">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrder->productOrders ?? [] as $item)
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item->product->sku ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $item->product->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $item->product->category->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $item->product->supplier->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $item->product->supplier_code ?? 'N/A' }}</td>
                                <td class="px-6 py-4">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-4">{{ number_format($item->quantity, 2) }} {{ $item->product->uom ?? 'pcs' }}</td>
                                <td class="px-6 py-4 font-semibold">₱{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td colspan="8" class="px-6 py-4 text-center">No items found</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <td colspan="6" class="px-6 py-3 text-right font-bold">Total:</td>
                            <td class="px-6 py-3 font-bold">{{ number_format($purchaseOrder->total_qty ?? 0, 2) }}</td>
                            <td class="px-6 py-3 font-bold">₱{{ number_format($purchaseOrder->total_price ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-collapsible-card>

        <div class="fixed bottom-0 right-0 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 w-full">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('warehouse.purchaseorder') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                    Back to List
                </a>

                @php
                    use App\Enums\Enum\PermissionEnum;
                @endphp

                @can(PermissionEnum::APPROVE_SUPPLY_PURCHASE_ORDER->value)
                    <x-button type="button" wire:click="ApprovePurchaseOrder" :disabled="$purchaseOrder->status === 'approved'" class="flex justify-end space-x-4">
                        Approve Purchase Order
                    </x-button>
                    <x-button type="button" variant="danger" :disabled="$purchaseOrder->status === 'rejected'" wire:click="RejectPurchaseOrder" class="flex justify-end space-x-4">
                        Reject Purchase Order
                    </x-button>
                @endcan
            </div>
        </div>
    </div>
</div>