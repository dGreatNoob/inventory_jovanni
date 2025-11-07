<div>
    <x-slot:header>Deliveries</x-slot:header>
    <x-slot:subheader>Manage delivery orders and track shipments</x-slot:subheader>

    <div class="p-6">
        <!-- Search and Filter Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <!-- Search Input -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-zinc-500 dark:text-zinc-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search by PO # or supplier..." 
                        class="block w-full sm:w-64 p-2.5 ps-10 text-sm text-zinc-900 border border-zinc-300 rounded-lg bg-zinc-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:placeholder-zinc-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors"
                    >
                </div>
                
                <!-- Status Filter -->
                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-zinc-900 dark:text-white whitespace-nowrap">Filter by:</label>
                    <div class="relative">
                        <select wire:model.live="statusFilter" 
                            class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 py-2.5 pl-3 pr-10 appearance-none dark:bg-zinc-700 dark:border-zinc-600 dark:text-white dark:focus:ring-blue-500 transition-colors">
                            <option value="">All Deliveries</option>
                            <option value="complete">Complete</option>
                            <option value="partial">Partial</option>
                            <option value="pending">Pending</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Count -->
            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                Showing {{ $purchaseOrders->count() }} of {{ $purchaseOrders->total() }} deliveries
            </div>
        </div>

        <!-- Deliveries Table -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase bg-zinc-50/80 dark:bg-zinc-700/80 backdrop-blur-sm sticky top-0 z-10">
                        <tr class="border-b border-zinc-200 dark:border-zinc-600">
                            <th class="px-6 py-4 font-semibold text-left text-zinc-900 dark:text-zinc-100 tracking-wide">PO Number</th> 
                            <th class="px-6 py-4 font-semibold text-left text-zinc-900 dark:text-zinc-100 tracking-wide">Supplier</th>
                            <th class="px-6 py-4 font-semibold text-left text-zinc-900 dark:text-zinc-100 tracking-wide">Order Date</th>
                            <th class="px-6 py-4 font-semibold text-left text-zinc-900 dark:text-zinc-100 tracking-wide">Expected Delivery</th>
                            <th class="px-6 py-4 font-semibold text-left text-zinc-900 dark:text-zinc-100 tracking-wide">Progress</th>
                            <th class="px-6 py-4 font-semibold text-left text-zinc-900 dark:text-zinc-100 tracking-wide">Status</th>
                            <th class="px-6 py-4 font-semibold text-center text-zinc-900 dark:text-zinc-100 tracking-wide">Actions</th>                                                 
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
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
                            <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-all duration-200">                              
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-sm font-semibold text-blue-600 dark:text-blue-400">
                                            {{ $po->po_num }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $po->supplier->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $po->order_date->format('M d, Y') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                        {{ $po->expected_delivery_date ? $po->expected_delivery_date->format('M d, Y') : 'Not set' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3 min-w-[140px]">
                                        <div class="flex-1">
                                            <div class="flex justify-between text-xs text-zinc-600 dark:text-zinc-400 mb-1">
                                                <span>{{ number_format($totalDelivered, 0) }}/{{ number_format($denominator, 0) }}</span>
                                                <span>{{ number_format($progressPercentage, 0) }}%</span>
                                            </div>
                                            <div class="w-full bg-zinc-200 dark:bg-zinc-600 rounded-full h-2">
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm">{{ $statusIcon }}</span>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold 
                                            @if($status === 'complete') bg-emerald-100 text-emerald-300 dark:bg-emerald-900/30 text-emerald-100
                                            @elseif($status === 'partial') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                                            @else bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300 @endif">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- View Button -->
                                        <a href="{{ route('pomanagement.purchaseorder.show', ['Id' => $po->id]) }}"
                                           class="inline-flex items-center justify-center w-9 h-9 text-zinc-600 hover:text-blue-600 hover:bg-blue-50 dark:text-zinc-400 dark:hover:text-blue-400 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-200 group/action"
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
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-zinc-500 dark:text-zinc-400">
                                        <svg class="w-16 h-16 mb-4 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-zinc-600 dark:text-zinc-300 mb-2">No deliveries found</h3>
                                        <p class="text-sm max-w-sm">No delivery orders match your current search criteria. Try adjusting your filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($purchaseOrders->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Show:</span>
                        <select wire:model.live="perPage"
                            class="bg-white dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-1.5 transition-colors">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">per page</span>
                    </div>
                    <div class="flex justify-center sm:justify-end">
                        {{ $purchaseOrders->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>