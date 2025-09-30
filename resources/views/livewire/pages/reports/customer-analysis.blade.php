@php
    use App\Models\Customer;
    use App\Models\SalesOrder;
    use App\Models\SalesOrderItem;
    use Illuminate\Support\Facades\DB;

    $totalCustomers = Customer::count();
    $activeCustomers = Customer::whereHas('salesOrders', function($query) {
        $query->where('created_at', '>=', now()->subDays(90));
    })->count();

    $totalRevenue = SalesOrder::where('status', 'completed')
        ->where('created_at', '>=', now()->subDays(90))
        ->sum('total_amount');

    $avgOrderValue = SalesOrder::where('status', 'completed')
        ->where('created_at', '>=', now()->subDays(90))
        ->avg('total_amount');

    $topCustomersByRevenue = Customer::with(['salesOrders' => function($query) {
        $query->where('status', 'completed')
              ->where('created_at', '>=', now()->subDays(90));
    }])
    ->get()
    ->map(function($customer) {
        $totalRevenue = $customer->salesOrders->sum('total_amount');
        $orderCount = $customer->salesOrders->count();
        $avgOrderValue = $orderCount > 0 ? $totalRevenue / $orderCount : 0;
        $lastOrderDate = $customer->salesOrders->sortByDesc('created_at')->first()?->created_at;
        return [
            'customer' => $customer,
            'total_revenue' => $totalRevenue,
            'order_count' => $orderCount,
            'avg_order_value' => $avgOrderValue,
            'last_order' => $lastOrderDate
        ];
    })
    ->sortByDesc('total_revenue')
    ->take(10);

    $customerFrequency = Customer::with(['salesOrders' => function($query) {
        $query->where('created_at', '>=', now()->subDays(90));
    }])
    ->get()
    ->map(function($customer) {
        $orderCount = $customer->salesOrders->count();
        $daysSinceFirstOrder = $customer->salesOrders->sortBy('created_at')->first()?->created_at->diffInDays(now()) ?: 1;
        $purchaseFrequency = $orderCount > 0 ? $daysSinceFirstOrder / $orderCount : 0;
        return [
            'customer' => $customer,
            'order_count' => $orderCount,
            'days_since_first' => $daysSinceFirstOrder,
            'purchase_frequency' => round($purchaseFrequency, 1)
        ];
    })
    ->filter(function($item) {
        return $item['order_count'] > 0;
    })
    ->sortBy('purchase_frequency')
    ->take(10);

    $customerSegmentation = [
        'vip' => $topCustomersByRevenue->where('total_revenue', '>', 50000)->count(),
        'regular' => $topCustomersByRevenue->whereBetween('total_revenue', [10000, 50000])->count(),
        'occasional' => $topCustomersByRevenue->whereBetween('total_revenue', [1000, 10000])->count(),
        'new' => $topCustomersByRevenue->where('total_revenue', '<', 1000)->count()
    ];

    $recentCustomers = Customer::with('salesOrders')
        ->whereHas('salesOrders')
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get()
        ->map(function($customer) {
            return [
                'customer' => $customer,
                'first_order' => $customer->salesOrders->sortBy('created_at')->first(),
                'total_orders' => $customer->salesOrders->count(),
                'total_spent' => $customer->salesOrders->where('status', 'completed')->sum('total_amount')
            ];
        });

    $inactiveCustomers = Customer::with(['salesOrders' => function($query) {
        $query->orderBy('created_at', 'desc');
    }])
    ->get()
    ->filter(function($customer) {
        $lastOrder = $customer->salesOrders->first();
        return $lastOrder && $lastOrder->created_at->lt(now()->subDays(60));
    })
    ->sortByDesc(function($customer) {
        return $customer->salesOrders->first()?->created_at;
    })
    ->take(10)
    ->map(function($customer) {
        $lastOrder = $customer->salesOrders->first();
        return [
            'customer' => $customer,
            'last_order_date' => $lastOrder?->created_at,
            'days_inactive' => $lastOrder ? $lastOrder->created_at->diffInDays(now()) : null,
            'total_orders' => $customer->salesOrders->count(),
            'total_spent' => $customer->salesOrders->where('status', 'completed')->sum('total_amount')
        ];
    });

    $stats = [
        [
            'label' => 'Total Customers',
            'value' => number_format($totalCustomers),
            'change' => '+8.3',
            'period' => 'last month',
            'gradient' => 'from-rose-500 to-rose-600'
        ],
        [
            'label' => 'Active Customers',
            'value' => number_format($activeCustomers),
            'change' => '+12.1',
            'period' => 'last 90 days', 
            'gradient' => 'from-green-500 to-green-600'
        ],
        [
            'label' => 'Total Revenue',
            'value' => '₱' . number_format($totalRevenue, 0),
            'change' => '+15.7',
            'period' => 'last 90 days',
            'gradient' => 'from-blue-500 to-blue-600'
        ],
        [
            'label' => 'Avg Order Value',
            'value' => '₱' . number_format($avgOrderValue, 0),
            'change' => '+3.2',
            'period' => 'last 90 days',
            'gradient' => 'from-purple-500 to-purple-600'
        ]
    ];
@endphp

<x-slot:header>Customer Report</x-slot:header>
<x-slot:subheader>Customer behavior patterns and purchasing analytics</x-slot:subheader>

<div class="space-y-6">
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Customer Report Dashboard
                </h3>
                <div class="flex space-x-2">
                    <button type="button" 
                        onclick="alert('Export functionality will be implemented')"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition-colors text-white bg-rose-600 hover:bg-rose-700">
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
                                <p class="text-rose-100 text-sm font-medium">{{ $stat['label'] }}</p>
                                <p class="text-2xl font-bold">{{ $stat['value'] }}</p>
                                @if(isset($stat['change']))
                                    <p class="text-xs text-rose-100 mt-1">
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
                            <div class="text-rose-100">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                @else
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
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
                        Top Customers by Revenue (90 Days)
                    </h4>
                    <div class="space-y-3">
                        @forelse($topCustomersByRevenue->take(5) as $item)
                            @php
                                $maxRevenue = $topCustomersByRevenue->first()['total_revenue'] ?? 1;
                                $percentage = $maxRevenue > 0 ? ($item['total_revenue'] / $maxRevenue) * 100 : 0;
                            @endphp
                            <div class="flex items-center justify-between">
                                <div class="flex-1 mr-4">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $item['customer']->name }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">₱{{ number_format($item['total_revenue'], 0) }}</span>
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
                                <p>No customer data available</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Most Frequent Customers
                    </h4>
                    <div class="space-y-3">
                        @forelse($customerFrequency->take(5) as $item)
                            @php
                                $frequency = $item['purchase_frequency'];
                                $colorClass = $frequency <= 7 ? 'text-green-600 dark:text-green-400' : ($frequency <= 14 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-600 dark:text-gray-400');
                            @endphp
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $item['customer']->name }}</span>
                                    <span class="text-xs {{ $colorClass }} font-medium">
                                        Every {{ $frequency }}d
                                    </span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $item['order_count'] }} orders</span>
                                    <span>{{ $item['days_since_first'] }}d customer</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                <p>No frequency data available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Recent Customers
                    </h4>
                    <div class="space-y-2">
                        @forelse($recentCustomers as $item)
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item['customer']->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Joined {{ $item['customer']->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $item['total_orders'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">orders</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                <p>No recent customers</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Inactive Customers (60+ Days)
                    </h4>
                    <div class="space-y-2">
                        @forelse($inactiveCustomers as $item)
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item['customer']->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Last order: {{ $item['last_order_date']?->diffForHumans() ?? 'Never' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-orange-600 dark:text-orange-400">{{ $item['days_inactive'] }}d</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">inactive</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                <p>All customers are active!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Customer Segmentation
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $customerSegmentation['vip'] }}</div>
                        <div class="text-sm text-yellow-700 dark:text-yellow-300">VIP Customers</div>
                        <div class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">>₱50,000 spent</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $customerSegmentation['regular'] }}</div>
                        <div class="text-sm text-green-700 dark:text-green-300">Regular Customers</div>
                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">₱10K-₱50K spent</div>
                    </div>
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $customerSegmentation['occasional'] }}</div>
                        <div class="text-sm text-blue-700 dark:text-blue-300">Occasional Customers</div>
                        <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">₱1K-₱10K spent</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $customerSegmentation['new'] }}</div>
                        <div class="text-sm text-gray-700 dark:text-gray-300">New Customers</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1"><₱1K spent</div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">Key Insights</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Customer Retention Rate:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $totalCustomers > 0 ? round(($activeCustomers / $totalCustomers) * 100, 1) : 0 }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Top Customer Revenue:</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    ₱{{ $topCustomersByRevenue->isNotEmpty() ? number_format($topCustomersByRevenue->first()['total_revenue'], 0) : '0' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Most Frequent Customer:</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    Every {{ $customerFrequency->isNotEmpty() ? $customerFrequency->first()['purchase_frequency'] : '0' }} days
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">Recommendations</h5>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Launch re-engagement campaigns for inactive customers
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Create VIP loyalty programs for high-value customers
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Focus on converting occasional customers to regulars
                            </li>
                        </ul>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>