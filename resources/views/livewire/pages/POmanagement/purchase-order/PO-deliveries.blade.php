<div>
    <x-slot:header>Deliveries</x-slot:header>
    <x-slot:subheader>Manage delivery orders and track shipments</x-slot:subheader>

    <div class="">
        <div class="">
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex items-center justify-between p-4 pr-10">
                <div class="flex space-x-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search by PO # or supplier..." />
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-900 dark:text-white">Status:</label>
                        <select wire:model.live="statusFilter"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">All Deliveries</option>
                            <option value="complete">Complete</option>
                            <option value="partial">Partial</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ $purchaseOrders->count() }} of {{ $purchaseOrders->total() }} deliveries
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">PO Number</th>
                            <th scope="col" class="px-6 py-3">Supplier</th>
                            <th scope="col" class="px-6 py-3">Order Date</th>
                            <th scope="col" class="px-6 py-3">Expected Delivery</th>
                            <th scope="col" class="px-6 py-3">Progress</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $po)
                            @php
                                // Calculate delivery progress
                                $totalOrdered = (float) $po->productOrders->sum('quantity');
                                $totalExpected = (float) $po->productOrders->sum(function($order) {
                                    return $order->expected_qty ?? $order->quantity;
                                });
                                $totalReceived = (float) $po->productOrders->sum('received_quantity');
                                $totalDestroyed = (float) ($po->productOrders->sum('destroyed_qty') ?? 0);
                                $totalDelivered = $totalReceived + $totalDestroyed;
                                
                                // Use expected as baseline (what supplier committed), fallback to ordered
                                $denominator = $totalExpected > 0 ? $totalExpected : $totalOrdered;
                                $progressPercentage = $denominator > 0 ? min(($totalDelivered / $denominator) * 100, 100) : 0;
                                
                                // Determine delivery status
                               // Determine delivery status based on progress first
                            if ($progressPercentage >= 100 || $po->status->value === 'received') {
                                $status = 'complete';
                                $statusLabel = 'Complete';
                                $statusColor = 'emerald';
                                $statusIcon = '✅';
                            } elseif ($totalDelivered > 0 && $totalDelivered < $denominator) {
                                $status = 'partial';
                                $statusLabel = 'In Progress';
                                $statusColor = 'amber';
                                $statusIcon = '🔄';
                            } else {
                                $status = 'pending';
                                $statusLabel = 'Pending';
                                $statusColor = 'rose';
                                $statusIcon = '⏳';
                            }
                            @endphp
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">                              
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    <span class="font-mono text-indigo-600 dark:text-indigo-400">{{ $po->po_num }}</span>
                                </td>
                                <td class="px-6 py-4">{{ $po->supplier->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $po->order_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4">{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('M d, Y') : 'Not set' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3 min-w-[140px]">
                                        <div class="flex-1">
                                            <div class="flex justify-between text-xs text-zinc-600 dark:text-zinc-400 mb-1">
                                                <span>{{ number_format($totalDelivered, 0) }}/{{ number_format($denominator, 0) }}</span>
                                                <span>{{ number_format($progressPercentage, 0) }}%</span>
                                            </div>
                                            <div class="w-full bg-zinc-200 dark:bg-gray-600 rounded-full h-2">
                                                <div 
                                                    class="h-2 rounded-full transition-all duration-500 ease-out 
                                                    @if($status === 'complete') bg-emerald-500
                                                    @elseif($status === 'partial') bg-amber-500
                                                    @else bg-rose-500 @endif"
                                                    style="width: {{ $progressPercentage }}%"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span>{{ $statusIcon }}</span>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                            @if($status === 'complete') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                                            @elseif($status === 'partial') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                                            @else bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300 @endif">
                                            {{ $statusLabel }}
                                        </span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- View Button -->
                                        <a href="{{ route('pomanagement.purchaseorder.show', ['Id' => $po->id]) }}"
                                           class="inline-flex items-center justify-center w-9 h-9 text-zinc-600 hover:text-indigo-600 hover:bg-indigo-50 dark:text-zinc-400 dark:hover:text-indigo-400 dark:hover:bg-indigo-900/20 rounded-lg transition-all duration-200 group/action"
                                           title="View Purchase Order Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        
                                        <!-- Start Receiving Button (for TO_RECEIVE status) -->
                                        @if($po->status === 'to_receive')
                                        <a href="{{ route('warehousestaff.stockin') }}?po={{ $po->po_num }}"
                                           target="_blank"
                                           class="inline-flex items-center justify-center w-9 h-9 text-zinc-600 hover:text-emerald-600 hover:bg-emerald-50 dark:text-zinc-400 dark:hover:text-emerald-400 dark:hover:bg-emerald-900/20 rounded-lg transition-all duration-200 group/action"
                                           title="Start Receiving Items">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 9l6-3"></path>
                                            </svg>
                                        </a>
                                        @endif
                                    </div>
                                </td>                                
                            </tr>
                        @empty
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td colspan="7" class="px-6 py-4 text-center">No deliveries found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="py-4 px-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-900 dark:text-white">Per Page:</label>
                        <select wire:model.live="perPage"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div>
                        {{ $purchaseOrders->links() }}
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>