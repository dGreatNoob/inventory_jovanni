<div class="space-y-6">
    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            Report Filters
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                <input type="date" wire:model.live="dateFrom"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                <input type="date" wire:model.live="dateTo"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <!-- Supplier Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier</label>
                <select wire:model.live="selectedSupplier"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Report Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Report Type</label>
                <select wire:model.live="reportType"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="summary">PO Summary</option>
                    <option value="outstanding">Outstanding POs</option>
                    <option value="supplier_history">Supplier History</option>
                    <option value="lead_time">Lead Time Analysis</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button wire:click="exportReport" 
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Report
            </button>
        </div>
    </div>

    <!-- Report Content -->
    @if($reportType === 'summary')
        <!-- Purchase Order Summary Report -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Purchase Order Summary</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Period: {{ Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
            </p>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-300">Total Purchase Orders</p>
                            <p class="text-2xl font-bold text-blue-900 dark:text-white">{{ number_format($data['summary']['total_pos']) }}</p>
                        </div>
                        <svg class="w-10 h-10 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600 dark:text-green-300">Total Amount</p>
                            <p class="text-2xl font-bold text-green-900 dark:text-white">₱{{ number_format($data['summary']['total_amount'], 2) }}</p>
                        </div>
                        <svg class="w-10 h-10 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-600 dark:text-yellow-300">Total Quantity</p>
                            <p class="text-2xl font-bold text-yellow-900 dark:text-white">{{ number_format($data['summary']['total_qty']) }}</p>
                        </div>
                        <svg class="w-10 h-10 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Status Breakdown -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Count</th>
                            <th class="px-6 py-3">Total Amount</th>
                            <th class="px-6 py-3">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['summary']['by_status'] as $status)
                            @php
                                $percentage = $data['summary']['total_amount'] > 0 
                                    ? round(($status->total / $data['summary']['total_amount']) * 100, 1) 
                                    : 0;
                            @endphp
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if ($status->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($status->status === 'approved') bg-cyan-100 text-cyan-800
                                        @elseif($status->status === 'for_delivery') bg-blue-100 text-blue-800
                                        @elseif($status->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($status->status === 'received') bg-purple-100 text-purple-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $status->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ number_format($status->count) }}</td>
                                <td class="px-6 py-4">₱{{ number_format($status->total, 2) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-xs">{{ $percentage }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center">No data available for selected period</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($reportType === 'outstanding')
        <!-- Outstanding POs Report -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Outstanding Purchase Orders</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Purchase orders that are pending, approved, for delivery, or delivered (not yet received)
            </p>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">PO #</th>
                            <th class="px-6 py-3">Supplier</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Order Date</th>
                            <th class="px-6 py-3">Expected Delivery</th>
                            <th class="px-6 py-3">Total Amount</th>
                            <th class="px-6 py-3">Days Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['outstanding'] as $po)
                            @php
                                $daysOutstanding = $po->order_date ? $po->order_date->diffInDays(now()) : 0;
                            @endphp
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium">{{ $po->po_num }}</td>
                                <td class="px-6 py-4">{{ $po->supplier->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if ($po->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($po->status === 'approved') bg-cyan-100 text-cyan-800
                                        @elseif($po->status === 'for_delivery') bg-blue-100 text-blue-800
                                        @elseif($po->status === 'delivered') bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $po->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ $po->order_date ? $po->order_date->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-6 py-4">₱{{ number_format($po->total_price, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($daysOutstanding > 30) bg-red-100 text-red-800
                                        @elseif($daysOutstanding > 14) bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ $daysOutstanding }} days
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center">No outstanding purchase orders</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($reportType === 'supplier_history')
        <!-- Supplier Purchase History Report -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Supplier Purchase History</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Period: {{ Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
            </p>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Supplier</th>
                            <th class="px-6 py-3">Total Orders</th>
                            <th class="px-6 py-3">Total Items</th>
                            <th class="px-6 py-3">Total Spent</th>
                            <th class="px-6 py-3">Avg Order Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['supplier_history'] as $supplier)
                            @php
                                $avgOrderValue = $supplier->total_orders > 0 
                                    ? $supplier->total_spent / $supplier->total_orders 
                                    : 0;
                            @endphp
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium">{{ $supplier->supplier->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ number_format($supplier->total_orders) }}</td>
                                <td class="px-6 py-4">{{ number_format($supplier->total_items) }}</td>
                                <td class="px-6 py-4 font-semibold text-green-600">₱{{ number_format($supplier->total_spent, 2) }}</td>
                                <td class="px-6 py-4">₱{{ number_format($avgOrderValue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center">No supplier data for selected period</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($reportType === 'lead_time')
        <!-- Lead Time Analysis Report -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">PO vs Delivery Lead Time Analysis</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Period: {{ Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
            </p>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">PO #</th>
                            <th class="px-6 py-3">Supplier</th>
                            <th class="px-6 py-3">Order Date</th>
                            <th class="px-6 py-3">Expected Delivery</th>
                            <th class="px-6 py-3">Actual Delivery</th>
                            <th class="px-6 py-3">Expected Lead Time</th>
                            <th class="px-6 py-3">Actual Lead Time</th>
                            <th class="px-6 py-3">Variance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['lead_time'] as $item)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium">{{ $item['po_num'] }}</td>
                                <td class="px-6 py-4">{{ $item['supplier'] }}</td>
                                <td class="px-6 py-4">{{ $item['order_date']?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $item['expected_delivery']?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $item['actual_delivery']?->format('M d, Y H:i') ?? 'Pending' }}</td>
                                <td class="px-6 py-4">{{ $item['expected_lead_time'] ?? 'N/A' }} days</td>
                                <td class="px-6 py-4">{{ $item['actual_lead_time'] ?? 'N/A' }} days</td>
                                <td class="px-6 py-4">
                                    @if($item['difference'] !== null)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($item['difference'] > 0) bg-red-100 text-red-800
                                            @elseif($item['difference'] < 0) bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $item['difference'] > 0 ? '+' : '' }}{{ $item['difference'] }} days
                                        </span>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center">No completed deliveries for lead time analysis</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>