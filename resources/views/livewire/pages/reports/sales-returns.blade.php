@php
    // Dummy data for Sales Returns report
    $totalReturns = 245;
    $totalReturnValue = 125650.00;
    $returnsThisMonth = 38;
    $avgReturnValue = 512.45;
    
    $returnReasons = [
        ['reason' => 'Defective Product', 'count' => 95, 'percentage' => 38.8],
        ['reason' => 'Wrong Item Delivered', 'count' => 58, 'percentage' => 23.7],
        ['reason' => 'Customer Changed Mind', 'count' => 42, 'percentage' => 17.1],
        ['reason' => 'Damaged in Transit', 'count' => 32, 'percentage' => 13.1],
        ['reason' => 'Late Delivery', 'count' => 18, 'percentage' => 7.3]
    ];
    
    $recentReturns = [
        ['id' => 'SR-2024-001', 'customer' => 'ABC Corporation', 'amount' => 1250.00, 'reason' => 'Defective Product', 'date' => now()->subDays(2), 'status' => 'Approved'],
        ['id' => 'SR-2024-002', 'customer' => 'XYZ Industries', 'amount' => 850.50, 'reason' => 'Wrong Item', 'date' => now()->subDays(3), 'status' => 'Pending'],
        ['id' => 'SR-2024-003', 'customer' => 'Tech Solutions Ltd', 'amount' => 2100.75, 'reason' => 'Damaged', 'date' => now()->subDays(5), 'status' => 'Approved'],
        ['id' => 'SR-2024-004', 'customer' => 'Global Enterprises', 'amount' => 675.25, 'reason' => 'Changed Mind', 'date' => now()->subDays(7), 'status' => 'Rejected'],
        ['id' => 'SR-2024-005', 'customer' => 'Mega Corp', 'amount' => 1800.00, 'reason' => 'Late Delivery', 'date' => now()->subDays(8), 'status' => 'Processing']
    ];
    
    $topCustomerReturns = [
        ['customer' => 'ABC Corporation', 'returns' => 12, 'total_value' => 8750.50],
        ['customer' => 'XYZ Industries', 'returns' => 8, 'total_value' => 6240.75],
        ['customer' => 'Tech Solutions Ltd', 'returns' => 6, 'total_value' => 4850.25],
        ['customer' => 'Global Enterprises', 'returns' => 5, 'total_value' => 3920.00],
        ['customer' => 'Mega Corp', 'returns' => 4, 'total_value' => 2650.80]
    ];
    
    $monthlyTrend = [
        ['month' => 'Jan', 'returns' => 42, 'value' => 18500],
        ['month' => 'Feb', 'returns' => 38, 'value' => 16750],
        ['month' => 'Mar', 'returns' => 45, 'value' => 21200],
        ['month' => 'Apr', 'returns' => 52, 'value' => 24800],
        ['month' => 'May', 'returns' => 35, 'value' => 15600],
        ['month' => 'Jun', 'returns' => 33, 'value' => 14900]
    ];
    
    $stats = [
        [
            'label' => 'Total Returns',
            'value' => number_format($totalReturns),
            'change' => '-8.5',
            'period' => 'last month',
            'gradient' => 'from-red-500 to-red-600'
        ],
        [
            'label' => 'Return Value',
            'value' => '₱' . number_format($totalReturnValue, 2),
            'change' => '-12.3',
            'period' => 'last month', 
            'gradient' => 'from-orange-500 to-orange-600'
        ],
        [
            'label' => 'Returns This Month',
            'value' => number_format($returnsThisMonth),
            'change' => '+5.2',
            'period' => 'vs last month',
            'gradient' => 'from-yellow-500 to-yellow-600'
        ],
        [
            'label' => 'Avg Return Value',
            'value' => '₱' . number_format($avgReturnValue, 2),
            'change' => '+2.8',
            'period' => 'last 30 days',
            'gradient' => 'from-purple-500 to-purple-600'
        ]
    ];
@endphp

<x-slot:header>Sales Returns Report</x-slot:header>
<x-slot:subheader>Returns analysis and refund tracking</x-slot:subheader>

<div class="space-y-6">
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m5 14v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"></path>
                    </svg>
                    Sales Returns Dashboard
                </h3>
                <div class="flex space-x-2">
                    <button type="button" 
                        onclick="alert('Export functionality will be implemented')"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition-colors text-white bg-red-600 hover:bg-red-700">
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
                                <p class="text-red-100 text-sm font-medium">{{ $stat['label'] }}</p>
                                <p class="text-2xl font-bold">{{ $stat['value'] }}</p>
                                @if(isset($stat['change']))
                                    <p class="text-xs text-red-100 mt-1">
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
                            <div class="text-red-100">
                                @if($index === 0)
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m5 14v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"></path>
                                    </svg>
                                @elseif($index === 1)
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                @elseif($index === 2)
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-8 0h8m-8 0H6a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
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
                                        <div class="bg-red-500 h-2 rounded-full transition-all duration-300" style="width: {{ $reason['percentage'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <div class="relative bg-orange-500 rounded-t-sm hover:bg-orange-600 transition-colors" style="height: {{ $height * 1.6 }}px; width: 24px;">
                                    <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        {{ $month['returns'] }} returns<br>₱{{ number_format($month['value']) }}
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $month['month'] }}</div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Return volume and value by month</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Top Customers by Returns
                    </h4>
                    <div class="space-y-2">
                        @foreach($topCustomerReturns as $customer)
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $customer['customer'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $customer['returns'] }} returns</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-blue-600 dark:text-blue-400">₱{{ number_format($customer['total_value'], 2) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">total value</p>
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
                        Recent Returns
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
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $return['customer'] }} • {{ $return['reason'] }}</p>
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

            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Returns Analysis Summary
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">Key Metrics</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Return Rate:</span>
                                <span class="font-medium text-gray-900 dark:text-white">4.8%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Avg Processing Time:</span>
                                <span class="font-medium text-gray-900 dark:text-white">3.2 days</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Approval Rate:</span>
                                <span class="font-medium text-gray-900 dark:text-white">78.5%</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">Recommendations</h5>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Improve quality control to reduce defective products
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Review order fulfillment process for accuracy
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Enhance packaging to prevent transit damage
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>