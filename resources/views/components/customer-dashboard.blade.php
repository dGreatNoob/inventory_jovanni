@props(['stats'])

<div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
    <!-- Dashboard Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Customer Dashboard
            </h3>
            <div class="flex space-x-2">
                <button type="button" 
                    onclick="alert('Import functionality will be implemented')"
                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition-colors text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    Import Customers
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            @elseif($index === 1)
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            @elseif($index === 2)
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- Customer Growth Chart -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Customer Growth Trend
                </h4>
                
                <!-- Area Chart -->
                <div class="relative h-48">
                    <svg class="w-full h-full" viewBox="0 0 400 200">
                        @php
                            $growthData = [
                                ['month' => 'Jan', 'customers' => 42],
                                ['month' => 'Feb', 'customers' => 48],
                                ['month' => 'Mar', 'customers' => 55],
                                ['month' => 'Apr', 'customers' => 61],
                                ['month' => 'May', 'customers' => 67],
                                ['month' => 'Jun', 'customers' => 75]
                            ];
                            $maxCustomers = max(array_column($growthData, 'customers'));
                            $points = [];
                            $areaPoints = ['50,170']; // Start from bottom-left
                            foreach($growthData as $index => $data) {
                                $x = 50 + ($index * 55);
                                $y = 170 - (($data['customers'] / $maxCustomers) * 120);
                                $points[] = "$x,$y";
                                $areaPoints[] = "$x,$y";
                            }
                            $areaPoints[] = '380,170'; // End at bottom-right
                            $pathData = 'M' . implode(' L', $points);
                            $areaData = 'M' . implode(' L', $areaPoints) . ' Z';
                        @endphp
                        
                        <!-- Grid lines -->
                        <g class="text-gray-300 dark:text-gray-600" stroke="currentColor" stroke-width="0.5">
                            <line x1="50" y1="170" x2="380" y2="170" />
                            <line x1="50" y1="50" x2="50" y2="170" />
                            
                            @for($i = 0; $i <= 4; $i++)
                                <line x1="50" y1="{{ 50 + ($i * 30) }}" x2="380" y2="{{ 50 + ($i * 30) }}" opacity="0.3" />
                            @endfor
                        </g>
                        
                        <!-- Area fill -->
                        <path d="{{ $areaData }}" fill="#3b82f6" fill-opacity="0.1" />
                        
                        <!-- Data line -->
                        <path d="{{ $pathData }}" fill="none" stroke="#3b82f6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        
                        <!-- Data points -->
                        @foreach($growthData as $index => $data)
                            @php
                                $x = 50 + ($index * 55);
                                $y = 170 - (($data['customers'] / $maxCustomers) * 120);
                            @endphp
                            <circle cx="{{ $x }}" cy="{{ $y }}" r="4" fill="#3b82f6" />
                            <text x="{{ $x }}" y="{{ $y - 10 }}" text-anchor="middle" class="text-xs font-semibold fill-blue-600">
                                {{ $data['customers'] }}
                            </text>
                        @endforeach
                        
                        <!-- X-axis labels -->
                        @foreach($growthData as $index => $data)
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
                                $value = ($maxCustomers / 4) * (4 - $i);
                                $y = 50 + ($i * 30);
                            @endphp
                            <text x="40" y="{{ $y + 5 }}" text-anchor="end" class="text-xs fill-gray-600 dark:fill-gray-400">
                                {{ round($value) }}
                            </text>
                        @endfor
                    </svg>
                </div>
                
                <!-- Chart info -->
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total customers over time</p>
                </div>
            </div>

            <!-- Customer Segments -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Customer Segments
                </h4>
                
                <!-- Donut Chart -->
                <div class="flex items-center justify-center h-48 relative">
                    <div class="relative w-36 h-36">
                        @php
                            $segments = [
                                ['name' => 'Regular', 'count' => 35, 'color' => '#10b981'],
                                ['name' => 'VIP', 'count' => 12, 'color' => '#f59e0b'],
                                ['name' => 'Premium', 'count' => 8, 'color' => '#8b5cf6'],
                                ['name' => 'New', 'count' => 20, 'color' => '#3b82f6']
                            ];
                            $total = array_sum(array_column($segments, 'count'));
                            $currentAngle = 0;
                        @endphp
                        
                        <svg class="w-36 h-36 transform -rotate-90" viewBox="0 0 36 36">
                            <!-- Background circle -->
                            <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="4" fill="transparent" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            
                            @foreach($segments as $segment)
                                @php
                                    $percentage = ($segment['count'] / $total) * 100;
                                    $strokeDasharray = "$percentage 100";
                                    $strokeDashoffset = -$currentAngle;
                                    $currentAngle += $percentage;
                                @endphp
                                <path stroke="{{ $segment['color'] }}" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $strokeDasharray }}" stroke-dashoffset="{{ $strokeDashoffset }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @endforeach
                        </svg>
                        
                        <!-- Center text -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $total }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Total</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Legend -->
                <div class="mt-4 grid grid-cols-2 gap-2">
                    @foreach($segments as $segment)
                        @php
                            $percentage = ($segment['count'] / $total) * 100;
                        @endphp
                        <div class="flex items-center text-sm">
                            <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $segment['color'] }}"></div>
                            <span class="text-gray-700 dark:text-gray-300">{{ $segment['name'] }}: {{ $segment['count'] }} ({{ round($percentage, 1) }}%)</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>