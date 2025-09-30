@props(['stats'])

<div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
    <!-- Dashboard Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Expenses Dashboard
            </h3>
            <div class="flex space-x-2">
                <button type="button" 
                    onclick="alert('Export functionality will be implemented')"
                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition-colors text-white bg-green-600 hover:bg-green-700">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export
                </button>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($stats as $index => $stat)
                <div class="bg-gradient-to-r {{ $stat['gradient'] }} rounded-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">{{ $stat['label'] }}</p>
                            <p class="text-2xl font-bold">{{ $stat['value'] }}</p>
                            @if(isset($stat['change']))
                                <p class="text-xs text-blue-100 mt-1">
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
                        <div class="text-blue-100">
                            @if($index === 0)
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            @elseif($index === 1)
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @elseif($index === 2)
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- Monthly Expenses Chart -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Monthly Expense Trend
                </h4>
                
                <!-- Line Chart -->
                <div class="relative h-48">
                    <svg class="w-full h-full" viewBox="0 0 400 200">
                        @php
                            $monthlyData = [
                                ['month' => 'Jan', 'amount' => 45000],
                                ['month' => 'Feb', 'amount' => 52000],
                                ['month' => 'Mar', 'amount' => 38000],
                                ['month' => 'Apr', 'amount' => 67000],
                                ['month' => 'May', 'amount' => 55000],
                                ['month' => 'Jun', 'amount' => 72000]
                            ];
                            $maxAmount = max(array_column($monthlyData, 'amount'));
                            $points = [];
                            foreach($monthlyData as $index => $data) {
                                $x = 50 + ($index * 55);
                                $y = 170 - (($data['amount'] / $maxAmount) * 120);
                                $points[] = "$x,$y";
                            }
                            $pathData = 'M' . implode(' L', $points);
                        @endphp
                        
                        <!-- Grid lines -->
                        <g class="text-gray-300 dark:text-gray-600" stroke="currentColor" stroke-width="0.5">
                            <line x1="50" y1="170" x2="380" y2="170" />
                            <line x1="50" y1="50" x2="50" y2="170" />
                            
                            @for($i = 0; $i <= 4; $i++)
                                <line x1="50" y1="{{ 50 + ($i * 30) }}" x2="380" y2="{{ 50 + ($i * 30) }}" opacity="0.3" />
                            @endfor
                        </g>
                        
                        <!-- Data line -->
                        <path d="{{ $pathData }}" fill="none" stroke="#ef4444" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        
                        <!-- Data points -->
                        @foreach($monthlyData as $index => $data)
                            @php
                                $x = 50 + ($index * 55);
                                $y = 170 - (($data['amount'] / $maxAmount) * 120);
                            @endphp
                            <circle cx="{{ $x }}" cy="{{ $y }}" r="4" fill="#ef4444" />
                        @endforeach
                        
                        <!-- X-axis labels -->
                        @foreach($monthlyData as $index => $data)
                            @php
                                $x = 50 + ($index * 55);
                            @endphp
                            <text x="{{ $x }}" y="185" text-anchor="middle" class="text-xs fill-gray-600 dark:fill-gray-400">
                                {{ $data['month'] }}
                            </text>
                        @endforeach
                        
                        <!-- Y-axis labels -->
                        @for($i = 0; $i <= 4; $i++)
                            @php
                                $value = ($maxAmount / 4) * (4 - $i);
                                $y = 50 + ($i * 30);
                            @endphp
                            <text x="40" y="{{ $y + 5 }}" text-anchor="end" class="text-xs fill-gray-600 dark:fill-gray-400">
                                ₱{{ number_format($value / 1000) }}k
                            </text>
                        @endfor
                    </svg>
                </div>
                
                <!-- Chart info -->
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Monthly expense amounts (₱)</p>
                </div>
            </div>

            <!-- Expense Categories Chart -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                    Expense Categories
                </h4>
                
                <!-- Horizontal Bar Chart -->
                <div class="space-y-4">
                    @php
                        $categories = [
                            ['name' => 'Office Supplies', 'amount' => 35000, 'color' => 'bg-blue-500'],
                            ['name' => 'Utilities', 'amount' => 28000, 'color' => 'bg-green-500'],
                            ['name' => 'Transportation', 'amount' => 22000, 'color' => 'bg-yellow-500'],
                            ['name' => 'Marketing', 'amount' => 18000, 'color' => 'bg-purple-500'],
                            ['name' => 'Maintenance', 'amount' => 15000, 'color' => 'bg-red-500']
                        ];
                        $maxCategory = max(array_column($categories, 'amount'));
                    @endphp
                    
                    @foreach($categories as $category)
                        @php
                            $percentage = ($category['amount'] / $maxCategory) * 100;
                        @endphp
                        <div class="flex items-center">
                            <div class="w-24 text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $category['name'] }}
                            </div>
                            <div class="flex-1 mx-3">
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 relative">
                                    <div class="{{ $category['color'] }} h-4 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                    <span class="absolute inset-0 flex items-center justify-end pr-2 text-xs font-medium text-white">
                                        {{ $percentage > 20 ? '₱' . number_format($category['amount']) : '' }}
                                    </span>
                                </div>
                            </div>
                            <div class="w-16 text-sm text-gray-600 dark:text-gray-400 text-right">
                                {{ $percentage < 20 ? '₱' . number_format($category['amount']) : '' }}
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Total -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Total Categories:</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">₱{{ number_format(array_sum(array_column($categories, 'amount'))) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>