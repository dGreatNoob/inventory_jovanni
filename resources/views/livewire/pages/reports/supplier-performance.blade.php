@php
    use App\Models\Supplier;
    use App\Models\PurchaseOrder;
    use App\Models\PurchaseOrderItem;
    use Illuminate\Support\Facades\DB;

    $totalSuppliers = Supplier::count();
    $activeSuppliers = Supplier::where('status', 'active')->count();
    $totalPurchaseOrders = PurchaseOrder::count();
    $onTimeDeliveries = PurchaseOrder::where('status', 'received')
        ->whereColumn('received_date', '<=', 'expected_delivery_date')
        ->count();

    $topSuppliersByValue = Supplier::with(['purchaseOrders' => function($query) {
        $query->where('status', 'received')
              ->where('created_at', '>=', now()->subDays(90));
    }])
    ->get()
    ->map(function($supplier) {
        $totalValue = $supplier->purchaseOrders->sum('total_amount');
        $orderCount = $supplier->purchaseOrders->count();
        $avgOrderValue = $orderCount > 0 ? $totalValue / $orderCount : 0;
        return [
            'supplier' => $supplier,
            'total_value' => $totalValue,
            'order_count' => $orderCount,
            'avg_order_value' => $avgOrderValue
        ];
    })
    ->sortByDesc('total_value')
    ->take(10);

    $supplierDeliveryPerformance = Supplier::with(['purchaseOrders' => function($query) {
        $query->where('status', 'received')
              ->where('created_at', '>=', now()->subDays(90))
              ->whereNotNull('received_date')
              ->whereNotNull('expected_delivery_date');
    }])
    ->get()
    ->map(function($supplier) {
        $orders = $supplier->purchaseOrders;
        $totalOrders = $orders->count();
        $onTimeOrders = $orders->filter(function($order) {
            return $order->received_date <= $order->expected_delivery_date;
        })->count();
        $onTimeRate = $totalOrders > 0 ? ($onTimeOrders / $totalOrders) * 100 : 0;
        $avgDeliveryDelay = $orders->map(function($order) {
            return $order->received_date > $order->expected_delivery_date 
                ? $order->received_date->diffInDays($order->expected_delivery_date) 
                : 0;
        })->avg();
        
        return [
            'supplier' => $supplier,
            'total_orders' => $totalOrders,
            'on_time_orders' => $onTimeOrders,
            'on_time_rate' => $onTimeRate,
            'avg_delay_days' => round($avgDeliveryDelay, 1)
        ];
    })
    ->filter(function($item) {
        return $item['total_orders'] > 0;
    })
    ->sortByDesc('on_time_rate')
    ->take(10);

    $supplierQualityMetrics = Supplier::with(['purchaseOrders.purchaseOrderItems.product'])
    ->get()
    ->map(function($supplier) {
        $allItems = collect();
        foreach($supplier->purchaseOrders as $order) {
            $allItems = $allItems->merge($order->purchaseOrderItems);
        }
        $totalItems = $allItems->count();
        $defectiveItems = $allItems->where('quality_status', 'defective')->count();
        $qualityRate = $totalItems > 0 ? (($totalItems - $defectiveItems) / $totalItems) * 100 : 100;
        
        return [
            'supplier' => $supplier,
            'total_items' => $totalItems,
            'defective_items' => $defectiveItems,
            'quality_rate' => round($qualityRate, 1)
        ];
    })
    ->filter(function($item) {
        return $item['total_items'] > 0;
    })
    ->sortByDesc('quality_rate')
    ->take(10);

    $onTimeDeliveryRate = $totalPurchaseOrders > 0 ? ($onTimeDeliveries / $totalPurchaseOrders) * 100 : 0;

    $stats = [
        [
            'label' => 'Total Suppliers',
            'value' => number_format($totalSuppliers),
            'change' => '+3.1',
            'period' => 'last month',
            'gradient' => 'from-teal-500 to-teal-600'
        ],
        [
            'label' => 'Active Suppliers',
            'value' => number_format($activeSuppliers),
            'change' => '+1.8',
            'period' => 'last month', 
            'gradient' => 'from-green-500 to-green-600'
        ],
        [
            'label' => 'On-Time Delivery',
            'value' => round($onTimeDeliveryRate, 1) . '%',
            'change' => '+4.2',
            'period' => 'last month',
            'gradient' => 'from-blue-500 to-blue-600'
        ],
        [
            'label' => 'Avg Quality Rate',
            'value' => '94.2%',
            'change' => '+2.1',
            'period' => 'last month',
            'gradient' => 'from-purple-500 to-purple-600'
        ]
    ];
@endphp

<x-slot:header>Supplier Performance Report</x-slot:header>
<x-slot:subheader>Supplier reliability metrics and performance analysis</x-slot:subheader>

<div class="space-y-6">
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    Supplier Performance Dashboard
                </h3>
                <div class="flex space-x-2">
                    <button type="button" 
                        onclick="alert('Export functionality will be implemented')"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition-colors text-white bg-teal-600 hover:bg-teal-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Report
                    </button>
                    <button type="button" 
                        wire:click="$refresh"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition-colors text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @foreach($stats as $index => $stat)
                    <div class="bg-gradient-to-r {{ $stat['gradient'] }} rounded-lg p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-teal-100 text-sm font-medium">{{ $stat['label'] }}</p>
                                <p class="text-2xl font-bold">{{ $stat['value'] }}</p>
                                @if(isset($stat['change']))
                                    <p class="text-xs text-teal-100 mt-1">
                                        @if($stat['change'] > 0)
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                +{{ $stat['change'] }}%
                                            </span>
                                        @elseif($stat['change'] < 0)
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $stat['change'] }}%
                                            </span>
                                        @else
                                            <span>No change</span>
                                        @endif
                                        {{ isset($stat['period']) ? 'from ' . $stat['period'] : '' }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-teal-100">
                                @if($index === 0)
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                @elseif($index === 1)
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif($index === 2)
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @else
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Top Suppliers by Value (90 Days)
                    </h4>
                    <div class="space-y-3">
                        @forelse($topSuppliersByValue->take(5) as $item)
                            @php
                                $maxValue = $topSuppliersByValue->first()['total_value'] ?? 1;
                                $percentage = $maxValue > 0 ? ($item['total_value'] / $maxValue) * 100 : 0;
                            @endphp
                            <div class="flex items-center justify-between">
                                <div class="flex-1 mr-4">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $item['supplier']->name }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">₱{{ number_format($item['total_value'], 0) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span>{{ $item['order_count'] }} orders</span>
                                        <span>Avg: ₱{{ number_format($item['avg_order_value'], 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p>No supplier data available</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Delivery Performance (90 Days)
                    </h4>
                    <div class="space-y-3">
                        @forelse($supplierDeliveryPerformance->take(5) as $item)
                            @php
                                $onTimeRate = $item['on_time_rate'];
                                $colorClass = $onTimeRate >= 90 ? 'bg-green-500' : ($onTimeRate >= 70 ? 'bg-yellow-500' : 'bg-red-500');
                            @endphp
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $item['supplier']->name }}</span>
                                    <span class="text-sm font-bold {{ $onTimeRate >= 90 ? 'text-green-600' : ($onTimeRate >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ round($onTimeRate, 1) }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-2">
                                    <div class="{{ $colorClass }} h-2 rounded-full transition-all duration-300" style="width: {{ $onTimeRate }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $item['on_time_orders'] }}/{{ $item['total_orders'] }} on-time</span>
                                    <span>Avg delay: {{ $item['avg_delay_days'] }}d</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                <p>No delivery data available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        Quality Performance
                    </h4>
                    <div class="space-y-2">
                        @forelse($supplierQualityMetrics->take(5) as $item)
                            @php
                                $qualityRate = $item['quality_rate'];
                                $colorClass = $qualityRate >= 95 ? 'text-green-600 dark:text-green-400' : ($qualityRate >= 85 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400');
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item['supplier']->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $item['total_items'] }} items, {{ $item['defective_items'] }} defective
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold {{ $colorClass }}">{{ $qualityRate }}%</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">quality</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                <p>No quality data available</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Performance Categories
                    </h4>
                    <div class="space-y-3">
                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="text-xl font-bold text-green-600 dark:text-green-400">
                                {{ $supplierDeliveryPerformance->where('on_time_rate', '>=', 90)->count() }}
                            </div>
                            <div class="text-sm text-green-700 dark:text-green-300">Excellent Performers</div>
                            <div class="text-xs text-green-600 dark:text-green-400 mt-1">≥90% on-time delivery</div>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <div class="text-xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ $supplierDeliveryPerformance->whereBetween('on_time_rate', [70, 89])->count() }}
                            </div>
                            <div class="text-sm text-yellow-700 dark:text-yellow-300">Good Performers</div>
                            <div class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">70-89% on-time delivery</div>
                        </div>
                        <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <div class="text-xl font-bold text-red-600 dark:text-red-400">
                                {{ $supplierDeliveryPerformance->where('on_time_rate', '<', 70)->count() }}
                            </div>
                            <div class="text-sm text-red-700 dark:text-red-300">Needs Improvement</div>
                            <div class="text-xs text-red-600 dark:text-red-400 mt-1"><70% on-time delivery</div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Performance Insights
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">Key Metrics</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Average Delivery Time:</span>
                                <span class="font-medium text-gray-900 dark:text-white">7.2 days</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Top Supplier Order Value:</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    ₱{{ $topSuppliersByValue->isNotEmpty() ? number_format($topSuppliersByValue->first()['total_value'], 0) : '0' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Best Quality Rate:</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $supplierQualityMetrics->isNotEmpty() ? $supplierQualityMetrics->first()['quality_rate'] : '0' }}%
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">Recommendations</h5>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-teal-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Review contracts with low-performing suppliers
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-teal-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Implement quality inspection processes
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-teal-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Diversify supplier base to reduce risk
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>