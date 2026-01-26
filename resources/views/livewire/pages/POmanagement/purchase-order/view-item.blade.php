<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    List Of Items in Inventory
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Purchase Order ID: {{ $poId }}
                </p>
            </div>
            <div>
                <a href="{{ url()->previous() }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">SPC #</th>
                        <th scope="col" class="px-6 py-3">Supplier Number</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Weight</th>
                        <th scope="col" class="px-6 py-3">Remaining Weight</th>
                        <th scope="col" class="px-6 py-3">Remarks</th>
                        <th scope="col" class="px-6 py-3">Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rawMatInvs as $inv)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $inv->spc_num }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $inv->supplier_num ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if ($inv->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @elseif($inv->status === 'for_delivery') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                    @elseif($inv->status === 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300 @endif">
                                    {{ str_replace('_', ' ', ucfirst($inv->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                {{ $inv->weight ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $inv->rem_weight ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if ($inv->remarks === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @elseif($inv->remarks === 'for_delivery') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                    @elseif($inv->remarks === 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300 @endif">
                                    {{ str_replace('_', ' ', ucfirst($inv->remarks)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                {{ optional($inv->purchaseOrder->order_date)->format('M d, Y') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No items found for this purchase order.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($rawMatInvs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $rawMatInvs->links() }}
            </div>
        @endif
    </div>
</div>