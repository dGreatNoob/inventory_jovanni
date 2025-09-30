@props(['stats'])

<div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
    <!-- Dashboard Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Purchase Orders Dashboard
            </h3>
            <div class="flex space-x-2">
                <button type="button" 
                    onclick="alert('Export functionality will be implemented')"
                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition-colors text-white bg-blue-600 hover:bg-blue-700">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
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
            <!-- Order Status Chart -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                    Order Status Distribution
                </h4>
                <div class="relative h-48">
                    <!-- Simple CSS-based pie chart representation -->
                    <div class="flex items-center justify-center h-full">
                        <div class="relative w-32 h-32">
                            @php
                                $totalOrders = collect($stats)->sum(fn($stat) => is_numeric($stat['value']) ? $stat['value'] : 0);
                                $pendingOrders = isset($stats[1]) ? (int)str_replace(',', '', $stats[1]['value']) : 0;
                                $toReceiveOrders = isset($stats[2]) ? (int)str_replace(',', '', $stats[2]['value']) : 0;
                                $totalOrdersNum = isset($stats[0]) ? (int)str_replace(',', '', $stats[0]['value']) : 0;
                                
                                $pendingPercentage = $totalOrdersNum > 0 ? ($pendingOrders / $totalOrdersNum) * 100 : 0;
                                $toReceivePercentage = $totalOrdersNum > 0 ? ($toReceiveOrders / $totalOrdersNum) * 100 : 0;
                                $completedPercentage = 100 - $pendingPercentage - $toReceivePercentage;
                            @endphp
                            
                            <!-- Circular progress visualization -->
                            <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                                <!-- Background circle -->
                                <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                
                                <!-- Pending (Yellow) -->
                                <path class="text-yellow-500" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="{{ $pendingPercentage }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                
                                <!-- To Receive (Blue) -->
                                <path class="text-blue-500" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="{{ $toReceivePercentage }}, 100" stroke-dashoffset="-{{ $pendingPercentage }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                
                                <!-- Completed (Green) -->
                                <path class="text-green-500" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="{{ $completedPercentage }}, 100" stroke-dashoffset="-{{ $pendingPercentage + $toReceivePercentage }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            
                            <!-- Center text -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $totalOrdersNum }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Orders</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Legend -->
                <div class="mt-4 space-y-2">
                    <div class="flex items-center text-sm">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        <span class="text-gray-700 dark:text-gray-300">Pending: {{ $pendingOrders }} ({{ round($pendingPercentage, 1) }}%)</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span class="text-gray-700 dark:text-gray-300">To Receive: {{ $toReceiveOrders }} ({{ round($toReceivePercentage, 1) }}%)</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-gray-700 dark:text-gray-300">Completed: {{ round($completedPercentage, 1) }}%</span>
                    </div>
                </div>
            </div>

            <!-- Monthly Trend Chart -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Purchase Trend (6 Months)
                </h4>
                
                <!-- Simple Bar Chart -->
                <div class="relative h-48 flex items-end justify-between space-x-2 px-4">
                    @php
                        // Generate sample trend data for demonstration
                        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                        $values = [85, 92, 78, 95, 88, 96]; // Sample values
                        $maxValue = max($values);
                    @endphp
                    
                    @foreach($months as $index => $month)
                        @php
                            $height = ($values[$index] / $maxValue) * 100;
                        @endphp
                        <div class="flex flex-col items-center">
                            <div class="relative bg-blue-500 rounded-t-sm hover:bg-blue-600 transition-colors" style="height: {{ $height * 1.6 }}px; width: 24px;">
                                <!-- Value tooltip on hover -->
                                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{ $values[$index] }}
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $month }}</div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Chart info -->
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Purchase orders created per month</p>
                </div>
            </div>
        </div>
    </div>
</div>