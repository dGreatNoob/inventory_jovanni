<x-slot:header>Inventory Valuation Report</x-slot:header>
<x-slot:subheader>Financial valuation and cost analysis of current inventory</x-slot:subheader>

<div class="space-y-6">
    <!-- Financial Overview KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            // Calculate inventory values
            $totalInventoryValue = \App\Models\SupplyProfile::selectRaw('SUM(supply_qty * supply_unit_cost) as total_value')->first()->total_value ?? 0;
            $avgUnitCost = \App\Models\SupplyProfile::avg('supply_unit_cost') ?? 0;
            $highValueItems = \App\Models\SupplyProfile::whereRaw('supply_qty * supply_unit_cost > 10000')->count();
            $totalItems = \App\Models\SupplyProfile::count();
        @endphp
        
        <!-- Total Inventory Value -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Inventory Value</p>
                    <p class="text-3xl font-bold">₱{{ number_format($totalInventoryValue, 2) }}</p>
                    <p class="text-xs text-green-100 mt-1">Current market value</p>
                </div>
                <svg class="w-12 h-12 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
        </div>

        <!-- Average Unit Cost -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Average Unit Cost</p>
                    <p class="text-3xl font-bold">₱{{ number_format($avgUnitCost, 2) }}</p>
                    <p class="text-xs text-blue-100 mt-1">Per unit average</p>
                </div>
                <svg class="w-12 h-12 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>

        <!-- High Value Items -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">High Value Items</p>
                    <p class="text-3xl font-bold">{{ number_format($highValueItems) }}</p>
                    <p class="text-xs text-purple-100 mt-1">Above ₱10,000 value</p>
                </div>
                <svg class="w-12 h-12 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
        </div>

        <!-- Total Items Valued -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Total Items Valued</p>
                    <p class="text-3xl font-bold">{{ number_format($totalItems) }}</p>
                    <p class="text-xs text-orange-100 mt-1">Products in valuation</p>
                </div>
                <svg class="w-12 h-12 text-orange-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Valuation Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Value Distribution Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                </svg>
                Inventory Value Distribution
            </h3>
            
            <!-- Donut Chart -->
            <div class="flex items-center justify-center h-48 relative">
                <div class="relative w-40 h-40">
                    @php
                        $lowValueItems = \App\Models\SupplyProfile::whereRaw('supply_qty * supply_unit_cost < 1000')->count();
                        $mediumValueItems = \App\Models\SupplyProfile::whereRaw('supply_qty * supply_unit_cost BETWEEN 1000 AND 5000')->count();
                        $highValueItems = \App\Models\SupplyProfile::whereRaw('supply_qty * supply_unit_cost BETWEEN 5000 AND 10000')->count();
                        $premiumValueItems = \App\Models\SupplyProfile::whereRaw('supply_qty * supply_unit_cost > 10000')->count();
                        
                        $totalItemsForChart = $totalItems ?: 1;
                        $lowPercentage = ($lowValueItems / $totalItemsForChart) * 100;
                        $mediumPercentage = ($mediumValueItems / $totalItemsForChart) * 100;
                        $highPercentage = ($highValueItems / $totalItemsForChart) * 100;
                        $premiumPercentage = ($premiumValueItems / $totalItemsForChart) * 100;
                        
                        $currentAngle = 0;
                    @endphp
                    
                    <svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 36 36">
                        <!-- Background circle -->
                        <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="4" fill="transparent" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        
                        <!-- Low Value (Green) -->
                        <path stroke="#10b981" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $lowPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        @php $currentAngle += $lowPercentage; @endphp
                        
                        <!-- Medium Value (Blue) -->
                        <path stroke="#3b82f6" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $mediumPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        @php $currentAngle += $mediumPercentage; @endphp
                        
                        <!-- High Value (Orange) -->
                        <path stroke="#f97316" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $highPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        @php $currentAngle += $highPercentage; @endphp
                        
                        <!-- Premium Value (Purple) -->
                        <path stroke="#8b5cf6" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $premiumPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    
                    <!-- Center text -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $totalItemsForChart }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Items</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Legend -->
            <div class="mt-4 grid grid-cols-2 gap-2">
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Low (<₱1K): {{ $lowValueItems }}</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Medium (₱1-5K): {{ $mediumValueItems }}</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-orange-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">High (₱5-10K): {{ $highValueItems }}</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Premium (>₱10K): {{ $premiumValueItems }}</span>
                </div>
            </div>
        </div>

        <!-- Most Valuable Items -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Most Valuable Items
            </h3>
            
            <div class="space-y-4">
                @php
                    $mostValuableItems = \App\Models\SupplyProfile::selectRaw('*, (supply_qty * supply_unit_cost) as total_value')
                        ->orderBy('total_value', 'desc')
                        ->limit(5)
                        ->get();
                    $maxValue = $mostValuableItems->max('total_value') ?: 1;
                @endphp
                
                @forelse($mostValuableItems as $item)
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item->supply_description }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->supply_qty }} units × ₱{{ number_format($item->supply_unit_cost, 2) }}</p>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full transition-all duration-300" style="width: {{ ($item->total_value / $maxValue) * 100 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white min-w-max">₱{{ number_format($item->total_value, 0) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No inventory items found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Cost Analysis and Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cost Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Cost Analysis
            </h3>
            
            <div class="space-y-4">
                @php
                    $costRanges = [
                        ['range' => 'Under ₱100', 'count' => \App\Models\SupplyProfile::where('supply_unit_cost', '<', 100)->count(), 'color' => 'bg-green-500'],
                        ['range' => '₱100 - ₱500', 'count' => \App\Models\SupplyProfile::whereBetween('supply_unit_cost', [100, 500])->count(), 'color' => 'bg-blue-500'],
                        ['range' => '₱500 - ₱1,000', 'count' => \App\Models\SupplyProfile::whereBetween('supply_unit_cost', [500, 1000])->count(), 'color' => 'bg-yellow-500'],
                        ['range' => 'Over ₱1,000', 'count' => \App\Models\SupplyProfile::where('supply_unit_cost', '>', 1000)->count(), 'color' => 'bg-red-500'],
                    ];
                    $maxCount = max(array_column($costRanges, 'count')) ?: 1;
                @endphp
                
                @foreach($costRanges as $range)
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $range['range'] }}</p>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                <div class="{{ $range['color'] }} h-2 rounded-full transition-all duration-300" style="width: {{ ($range['count'] / $maxCount) * 100 }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white ml-4">{{ $range['count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Valuation Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Financial Summary
            </h3>
            
            <div class="space-y-4">
                @php
                    $lowValueTotal = \App\Models\SupplyProfile::whereRaw('supply_qty * supply_unit_cost < 1000')->selectRaw('SUM(supply_qty * supply_unit_cost) as total')->first()->total ?? 0;
                    $mediumValueTotal = \App\Models\SupplyProfile::whereRaw('supply_qty * supply_unit_cost BETWEEN 1000 AND 5000')->selectRaw('SUM(supply_qty * supply_unit_cost) as total')->first()->total ?? 0;
                    $highValueTotal = \App\Models\SupplyProfile::whereRaw('supply_qty * supply_unit_cost BETWEEN 5000 AND 10000')->selectRaw('SUM(supply_qty * supply_unit_cost) as total')->first()->total ?? 0;
                    $premiumValueTotal = \App\Models\SupplyProfile::whereRaw('supply_qty * supply_unit_cost > 10000')->selectRaw('SUM(supply_qty * supply_unit_cost) as total')->first()->total ?? 0;
                @endphp
                
                <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-green-700 dark:text-green-300">Low Value Items</span>
                        <span class="font-semibold text-green-800 dark:text-green-200">₱{{ number_format($lowValueTotal, 2) }}</span>
                    </div>
                </div>
                
                <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-blue-700 dark:text-blue-300">Medium Value Items</span>
                        <span class="font-semibold text-blue-800 dark:text-blue-200">₱{{ number_format($mediumValueTotal, 2) }}</span>
                    </div>
                </div>
                
                <div class="bg-orange-50 dark:bg-orange-900/20 p-3 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-orange-700 dark:text-orange-300">High Value Items</span>
                        <span class="font-semibold text-orange-800 dark:text-orange-200">₱{{ number_format($highValueTotal, 2) }}</span>
                    </div>
                </div>
                
                <div class="bg-purple-50 dark:bg-purple-900/20 p-3 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-purple-700 dark:text-purple-300">Premium Items</span>
                        <span class="font-semibold text-purple-800 dark:text-purple-200">₱{{ number_format($premiumValueTotal, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Assessment -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                Risk Assessment
            </h3>
            
            <div class="space-y-4">
                @php
                    $highValueLowStock = \App\Models\SupplyProfile::whereRaw('supply_qty * supply_unit_cost > 5000 AND supply_qty < 10')->count();
                    $zeroValueItems = \App\Models\SupplyProfile::where('supply_unit_cost', '=', 0)->count();
                    $overvaluedItems = \App\Models\SupplyProfile::whereRaw('supply_qty * supply_unit_cost > 50000')->count();
                @endphp
                
                <div class="border-l-4 border-red-500 pl-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">High Value, Low Stock</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Critical inventory risk</p>
                        </div>
                        <span class="text-lg font-bold text-red-600">{{ $highValueLowStock }}</span>
                    </div>
                </div>
                
                <div class="border-l-4 border-yellow-500 pl-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Zero Cost Items</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Need cost assignment</p>
                        </div>
                        <span class="text-lg font-bold text-yellow-600">{{ $zeroValueItems }}</span>
                    </div>
                </div>
                
                <div class="border-l-4 border-orange-500 pl-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Overvalued Stock</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Above ₱50K value</p>
                        </div>
                        <span class="text-lg font-bold text-orange-600">{{ $overvaluedItems }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>