@php
    // Dummy data for Purchase Returns report
    $totalReturns = 189;
    $totalReturnValue = 89750.00;
    $returnsThisMonth = 28;
    $avgReturnValue = 475.13;
    
    $returnReasons = [
        ['reason' => 'Quality Issues', 'count' => 75, 'percentage' => 39.7],
        ['reason' => 'Wrong Specifications', 'count' => 48, 'percentage' => 25.4],
        ['reason' => 'Delivery Delays', 'count' => 32, 'percentage' => 16.9],
        ['reason' => 'Damaged Goods', 'count' => 22, 'percentage' => 11.6],
        ['reason' => 'Order Cancellation', 'count' => 12, 'percentage' => 6.4]
    ];
    
    $recentReturns = [
        ['id' => 'PR-2024-001', 'supplier' => 'Global Supplies Inc', 'amount' => 2450.00, 'reason' => 'Quality Issues', 'date' => now()->subDays(1), 'status' => 'Approved'],
        ['id' => 'PR-2024-002', 'supplier' => 'Tech Hardware Ltd', 'amount' => 1850.75, 'reason' => 'Wrong Specs', 'date' => now()->subDays(2), 'status' => 'Pending'],
        ['id' => 'PR-2024-003', 'supplier' => 'Premium Materials Co', 'amount' => 3200.50, 'reason' => 'Damaged', 'date' => now()->subDays(4), 'status' => 'Processing'],
        ['id' => 'PR-2024-004', 'supplier' => 'Swift Logistics', 'amount' => 975.25, 'reason' => 'Late Delivery', 'date' => now()->subDays(6), 'status' => 'Approved'],
        ['id' => 'PR-2024-005', 'supplier' => 'Quality Goods Inc', 'amount' => 2100.00, 'reason' => 'Order Cancelled', 'date' => now()->subDays(8), 'status' => 'Rejected']
    ];
    
    $topSupplierReturns = [
        ['supplier' => 'Global Supplies Inc', 'returns' => 15, 'total_value' => 12750.50, 'return_rate' => 8.2],
        ['supplier' => 'Tech Hardware Ltd', 'returns' => 12, 'total_value' => 9240.75, 'return_rate' => 6.5],
        ['supplier' => 'Premium Materials Co', 'returns' => 9, 'total_value' => 7850.25, 'return_rate' => 5.8],
        ['supplier' => 'Swift Logistics', 'returns' => 7, 'total_value' => 5920.00, 'return_rate' => 4.1],
        ['supplier' => 'Quality Goods Inc', 'returns' => 6, 'total_value' => 4650.80, 'return_rate' => 3.9]
    ];
    
    $monthlyTrend = [
        ['month' => 'Jan', 'returns' => 32, 'value' => 15500],
        ['month' => 'Feb', 'returns' => 28, 'value' => 12750],
        ['month' => 'Mar', 'returns' => 35, 'value' => 18200],
        ['month' => 'Apr', 'returns' => 42, 'value' => 21800],
        ['month' => 'May', 'returns' => 25, 'value' => 11600],
        ['month' => 'Jun', 'returns' => 27, 'value' => 13900]
    ];
    
    $processingStats = [
        ['status' => 'Approved', 'count' => 142, 'percentage' => 75.1],
        ['status' => 'Pending', 'count' => 28, 'percentage' => 14.8],
        ['status' => 'Processing', 'count' => 12, 'percentage' => 6.4],
        ['status' => 'Rejected', 'count' => 7, 'percentage' => 3.7]
    ];
    
    $stats = [
        [
            'label' => 'Total Returns',
            'value' => number_format($totalReturns),
            'change' => '-6.2',
            'period' => 'last month',
            'gradient' => 'from-red-500 to-red-600'
        ],
        [
            'label' => 'Return Value',
            'value' => '₱' . number_format($totalReturnValue, 2),
            'change' => '-9.8',
            'period' => 'last month', 
            'gradient' => 'from-orange-500 to-orange-600'
        ],
        [
            'label' => 'Returns This Month',
            'value' => number_format($returnsThisMonth),
            'change' => '+3.7',
            'period' => 'vs last month',
            'gradient' => 'from-yellow-500 to-yellow-600'
        ],
        [
            'label' => 'Avg Return Value',
            'value' => '₱' . number_format($avgReturnValue, 2),
            'change' => '+1.5',
            'period' => 'last 30 days',
            'gradient' => 'from-purple-500 to-purple-600'
        ]
    ];
@endphp

<x-slot:header>Purchase Returns Report</x-slot:header>
<x-slot:subheader>Supplier returns analysis and procurement tracking</x-slot:subheader>

<div class="space-y-6">
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4h-4m0 0l-3 3m3-3l-3-3m5 14v-1a4 4 0 00-4-4h-4m0 0l-3 3m3-3l-3-3"></path>
                    </svg>
                    Purchase Returns Dashboard
                </h3>
                <div class="flex space-x-2">
                    <button type="button" 
                        onclick="alert('Export functionality will be implemented')"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition-colors text-white bg-orange-600 hover:bg-orange-700">
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
                                <p class="text-orange-100 text-sm font-medium">{{ $stat['label'] }}</p>
                                <p class="text-2xl font-bold">{{ $stat['value'] }}</p>
                                @if(isset($stat['change']))
                                    <p class="text-xs text-orange-100 mt-1">
                                        @if(str_starts_with($stat['change'], '-'))
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $stat['change'] }}%
                                            </span>
                                        @elseif(str_starts_with($stat['change'], '+'))
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
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
                            <div class="text-orange-100">
                                @if($index === 0)
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4h-4m0 0l-3 3m3-3l-3-3m5 14v-1a4 4 0 00-4-4h-4m0 0l-3 3m3-3l-3-3"></path>
                                    </svg>
                                @elseif($index === 1)
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                @elseif($index === 2)
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
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
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Return Reasons
                    </h4>
                    <div class="space-y-3">
                        @foreach($returnReasons as $reason)
                            <div class="flex items-center justify-between">
                                <div class="flex-1 mr-4">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $reason['reason'] }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $reason['count'] }} ({{ $reason['percentage'] }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-orange-500 h-2 rounded-full transition-all duration-300" style="width: {{ $reason['percentage'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Processing Status Distribution
                    </h4>
                    
                    <div class="relative h-48 flex items-center justify-center">
                        <div class="relative w-32 h-32">
                            @php
                                $totalCount = collect($processingStats)->sum('count');
                                $cumulativePercentage = 0;
                            @endphp
                            
                            <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                
                                @foreach($processingStats as $index => $stat)
                                    @php
                                        $colors = ['text-green-500', 'text-yellow-500', 'text-blue-500', 'text-red-500'];
                                        $color = $colors[$index] ?? 'text-gray-500';
                                    @endphp
                                    <path class="{{ $color }}" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" 
                                          stroke-dasharray="{{ $stat['percentage'] }}, 100" 
                                          stroke-dashoffset="-{{ $cumulativePercentage }}" 
                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    @php
                                        $cumulativePercentage += $stat['percentage'];
                                    @endphp
                                @endforeach
                            </svg>
                            
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $totalCount }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Returns</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 space-y-2">
                        @foreach($processingStats as $index => $stat)
                            @php
                                $colors = [
                                    ['bg' => 'bg-green-500', 'text' => 'text-green-700 dark:text-green-300'],
                                    ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-700 dark:text-yellow-300'],
                                    ['bg' => 'bg-blue-500', 'text' => 'text-blue-700 dark:text-blue-300'],
                                    ['bg' => 'bg-red-500', 'text' => 'text-red-700 dark:text-red-300']
                                ];
                                $color = $colors[$index] ?? ['bg' => 'bg-gray-500', 'text' => 'text-gray-700 dark:text-gray-300'];
                            @endphp
                            <div class="flex items-center text-sm">
                                <div class="w-3 h-3 {{ $color['bg'] }} rounded-full mr-2"></div>
                                <span class="{{ $color['text'] }}">{{ $stat['status'] }}: {{ $stat['count'] }} ({{ $stat['percentage'] }}%)</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Top Suppliers by Returns
                    </h4>
                    <div class="space-y-3">
                        @foreach($topSupplierReturns as $supplier)
                            @php
                                $rateColor = $supplier['return_rate'] > 6 ? 'text-red-600' : ($supplier['return_rate'] > 4 ? 'text-yellow-600' : 'text-green-600');
                            @endphp
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $supplier['supplier'] }}</span>
                                    <span class="text-xs font-medium {{ $rateColor }}">{{ $supplier['return_rate'] }}% rate</span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $supplier['returns'] }} returns</span>
                                    <span>₱{{ number_format($supplier['total_value'], 2) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Recent Purchase Returns
                    </h4>
                    <div class="space-y-2">
                        @foreach($recentReturns as $return)
                            @php
                                $statusColors = [
                                    'Approved' => 'text-green-600 bg-green-100',
                                    'Pending' => 'text-yellow-600 bg-yellow-100', 
                                    'Processing' => 'text-blue-600 bg-blue-100',
                                    'Rejected' => 'text-red-600 bg-red-100'
                                ];
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $return['id'] }}</p>
                                        <span class="text-xs px-2 py-1 rounded {{ $statusColors[$return['status']] }}">{{ $return['status'] }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $return['supplier'] }} • {{ $return['reason'] }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $return['date']->diffForHumans() }}</p>
                                </div>
                                <div class="text-right ml-2">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">₱{{ number_format($return['amount'], 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Monthly Return Trend (6 Months)
                    </h4>
                    
                    <div class="relative h-48 flex items-end justify-between space-x-2 px-4">
                        @php
                            $maxValue = collect($monthlyTrend)->max('returns');
                        @endphp
                        
                        @foreach($monthlyTrend as $month)
                            @php
                                $height = ($month['returns'] / $maxValue) * 100;
                            @endphp
                            <div class="flex flex-col items-center group">
                                <div class="relative bg-purple-500 rounded-t-sm hover:bg-purple-600 transition-colors" style="height: {{ $height * 1.6 }}px; width: 24px;">
                                    <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        {{ $month['returns'] }} returns<br>₱{{ number_format($month['value']) }}
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $month['month'] }}</div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Purchase return volume and value by month</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Returns Impact Analysis
                    </h4>
                    <div class="space-y-4">
                        <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">5.2%</div>
                            <div class="text-sm text-red-700 dark:text-red-300">Overall Return Rate</div>
                            <div class="text-xs text-red-600 dark:text-red-400 mt-1">vs industry avg 3.8%</div>
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Cost Impact:</span>
                                <span class="font-medium text-gray-900 dark:text-white">₱{{ number_format($totalReturnValue + ($totalReturnValue * 0.15), 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Avg Processing Time:</span>
                                <span class="font-medium text-gray-900 dark:text-white">4.5 days</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Recovery Rate:</span>
                                <span class="font-medium text-gray-900 dark:text-white">82.3%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Procurement Recommendations
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">Key Actions</h5>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Implement stricter quality inspection protocols
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Review specifications clarity with suppliers
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Consider supplier diversification for high-risk items
                            </li>
                        </ul>
                    </div>
                    <div class="space-y-3">
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">Risk Mitigation</h5>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Establish penalty clauses for quality failures
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Create supplier scorecards with return metrics
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Implement pre-delivery inspection processes
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>