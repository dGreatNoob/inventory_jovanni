<x-slot:header>Stock Available Report</x-slot:header>
<x-slot:subheader>Current inventory levels and availability status</x-slot:subheader>

<div class="space-y-6">
    <!-- Stock Overview KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $totalProducts = \App\Models\SupplyProfile::count();
            $totalQuantity = \App\Models\SupplyProfile::sum('supply_qty');
            $lowStockItems = \App\Models\SupplyProfile::where('supply_qty', '<', 50)->count();
            $outOfStockItems = \App\Models\SupplyProfile::where('supply_qty', '=', 0)->count();
        @endphp
        
        <!-- Total Products -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Products</p>
                    <p class="text-3xl font-bold">{{ number_format($totalProducts) }}</p>
                    <p class="text-xs text-blue-100 mt-1">Active inventory items</p>
                </div>
                <svg class="w-12 h-12 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </div>

        <!-- Total Stock Quantity -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Stock Quantity</p>
                    <p class="text-3xl font-bold">{{ number_format($totalQuantity) }}</p>
                    <p class="text-xs text-green-100 mt-1">Units in inventory</p>
                </div>
                <svg class="w-12 h-12 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Low Stock Items</p>
                    <p class="text-3xl font-bold">{{ number_format($lowStockItems) }}</p>
                    <p class="text-xs text-yellow-100 mt-1">Below 50 units</p>
                </div>
                <svg class="w-12 h-12 text-yellow-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
        </div>

        <!-- Out of Stock -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Out of Stock</p>
                    <p class="text-3xl font-bold">{{ number_format($outOfStockItems) }}</p>
                    <p class="text-xs text-red-100 mt-1">Zero quantity items</p>
                </div>
                <svg class="w-12 h-12 text-red-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Stock Level Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Stock Distribution Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Stock Level Distribution
            </h3>
            
            <!-- Donut Chart -->
            <div class="flex items-center justify-center h-48 relative">
                <div class="relative w-40 h-40">
                    @php
                        $wellStockedItems = \App\Models\SupplyProfile::where('supply_qty', '>', 100)->count();
                        $moderateStockItems = \App\Models\SupplyProfile::whereBetween('supply_qty', [50, 100])->count();
                        $lowStockCount = $lowStockItems;
                        $outOfStockCount = $outOfStockItems;
                        
                        $totalItems = $totalProducts ?: 1;
                        $wellStockedPercentage = ($wellStockedItems / $totalItems) * 100;
                        $moderateStockPercentage = ($moderateStockItems / $totalItems) * 100;
                        $lowStockPercentage = ($lowStockCount / $totalItems) * 100;
                        $outOfStockPercentage = ($outOfStockCount / $totalItems) * 100;
                        
                        $currentAngle = 0;
                    @endphp
                    
                    <svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 36 36">
                        <!-- Background circle -->
                        <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="4" fill="transparent" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        
                        <!-- Well Stocked (Green) -->
                        <path stroke="#10b981" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $wellStockedPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        @php $currentAngle += $wellStockedPercentage; @endphp
                        
                        <!-- Moderate Stock (Blue) -->
                        <path stroke="#3b82f6" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $moderateStockPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        @php $currentAngle += $moderateStockPercentage; @endphp
                        
                        <!-- Low Stock (Yellow) -->
                        <path stroke="#f59e0b" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $lowStockPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        @php $currentAngle += $lowStockPercentage; @endphp
                        
                        <!-- Out of Stock (Red) -->
                        <path stroke="#ef4444" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $outOfStockPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    
                    <!-- Center text -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $totalItems }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Items</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Legend -->
            <div class="mt-4 grid grid-cols-2 gap-2">
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Well Stocked: {{ $wellStockedItems }}</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Moderate: {{ $moderateStockItems }}</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Low Stock: {{ $lowStockCount }}</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Out of Stock: {{ $outOfStockCount }}</span>
                </div>
            </div>
        </div>

        <!-- Top Stock Items -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Items with Highest Stock
            </h3>
            
            <div class="space-y-4">
                @php
                    $topStockItems = \App\Models\SupplyProfile::orderBy('supply_qty', 'desc')->limit(5)->get();
                    $maxStock = $topStockItems->max('supply_qty') ?: 1;
                @endphp
                
                @forelse($topStockItems as $item)
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item->supply_description }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $item->supply_sku }} • {{ $item->supply_uom }}</p>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: {{ ($item->supply_qty / $maxStock) * 100 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white min-w-max">{{ number_format($item->supply_qty) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No inventory items found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Critical Stock Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Alert -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                Low Stock Alerts
            </h3>
            
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @php
                    $lowStockProducts = \App\Models\SupplyProfile::where('supply_qty', '<', 50)->where('supply_qty', '>', 0)->orderBy('supply_qty')->limit(8)->get();
                @endphp
                
                @forelse($lowStockProducts as $product)
                    <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $product->supply_description }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->supply_sku }}</p>
                        </div>
                        <div class="text-right ml-4">
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                {{ number_format($product->supply_qty) }} {{ $product->supply_uom }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <svg class="w-12 h-12 mx-auto text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No low stock items</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Out of Stock Alert -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                </svg>
                Out of Stock Items
            </h3>
            
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @php
                    $outOfStockProducts = \App\Models\SupplyProfile::where('supply_qty', '=', 0)->limit(8)->get();
                @endphp
                
                @forelse($outOfStockProducts as $product)
                    <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $product->supply_description }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->supply_sku }}</p>
                        </div>
                        <div class="text-right ml-4">
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                OUT OF STOCK
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <svg class="w-12 h-12 mx-auto text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No out of stock items</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>