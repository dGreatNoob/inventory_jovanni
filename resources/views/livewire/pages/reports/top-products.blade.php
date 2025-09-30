@php
    use App\Models\SupplyProfile;
    use App\Models\StockBatch;
    use App\Models\SupplyOrder;
    use App\Models\SalesOrderItem;
    use Illuminate\Support\Facades\DB;

    $totalProducts = SupplyProfile::count();
    $activeProducts = SupplyProfile::where('supply_status', 'active')->count();
    // Dummy data for demonstration
    $topSellingProducts = collect([
        (object)[ 'name' => 'Product A', 'sales_order_items_count' => 25 ],
        (object)[ 'name' => 'Product B', 'sales_order_items_count' => 18 ],
        (object)[ 'name' => 'Product C', 'sales_order_items_count' => 12 ],
    ]);

    $topPurchasedProducts = collect([
        (object)[ 'name' => 'Product X', 'purchase_order_items_count' => 30 ],
        (object)[ 'name' => 'Product Y', 'purchase_order_items_count' => 22 ],
        (object)[ 'name' => 'Product Z', 'purchase_order_items_count' => 15 ],
    ]);

    $mostMovedProducts = collect([
        [ 'product' => (object)['name' => 'Product M'], 'movements' => 10, 'total_quantity' => 100 ],
        [ 'product' => (object)['name' => 'Product N'], 'movements' => 7, 'total_quantity' => 70 ],
        [ 'product' => (object)['name' => 'Product O'], 'movements' => 5, 'total_quantity' => 50 ],
    ]);

    $lowMovementProducts = collect([
        [ 'product' => (object)['name' => 'Product P'], 'movements' => 2, 'total_quantity' => 20 ],
        [ 'product' => (object)['name' => 'Product Q'], 'movements' => 1, 'total_quantity' => 10 ],
    ]);

    $stats = [
        [
            'label' => 'Total Products',
            'value' => number_format($totalProducts),
            'change' => '+5.2',
            'period' => 'last month',
            'gradient' => 'from-blue-500 to-blue-600'
        ],
        [
            'label' => 'Active Products',
            'value' => number_format($activeProducts),
            'change' => '+2.1',
            'period' => 'last month', 
            'gradient' => 'from-green-500 to-green-600'
        ],
        [
            'label' => 'Fast Moving',
            'value' => number_format($mostMovedProducts->where('movements', '>', 10)->count()),
            'change' => '+12.5',
            'period' => 'last month',
            'gradient' => 'from-orange-500 to-orange-600'
        ],
        [
            'label' => 'Slow Moving',
            'value' => number_format($lowMovementProducts->count()),
            'change' => '-8.3',
            'period' => 'last month',
            'gradient' => 'from-red-500 to-red-600'
        ]
    ];
@endphp

<x-slot:header>Top Products Report</x-slot:header>
<x-slot:subheader>Product performance analytics and usage patterns</x-slot:subheader>

<div class="space-y-6">
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Top Products Dashboard
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
                            <div class="text-orange-100">
                                @if($index === 0)
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                @elseif($index === 1)
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif($index === 2)
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                @else
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        Top Selling Products (30 Days)
                    </h4>
                    <div class="space-y-3">
                        @forelse($topSellingProducts->take(5) as $product)
                            @php
                                $maxSales = $topSellingProducts->first()->sales_order_items_count ?? 1;
                                $percentage = $maxSales > 0 ? ($product->sales_order_items_count / $maxSales) * 100 : 0;
                            @endphp
                            <div class="flex items-center justify-between">
                                <div class="flex-1 mr-4">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $product->name }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $product->sales_order_items_count }} sales</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p>No sales data available</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                        Most Active Products (30 Days)
                    </h4>
                    <div class="space-y-3">
                        @forelse($mostMovedProducts->take(5) as $item)
                            @php
                                $maxMovements = $mostMovedProducts->first()['movements'] ?? 1;
                                $percentage = $maxMovements > 0 ? ($item['movements'] / $maxMovements) * 100 : 0;
                            @endphp
                            <div class="flex items-center justify-between">
                                <div class="flex-1 mr-4">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $item['product']->name }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $item['movements'] }} moves</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                </svg>
                                <p>No movement data available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Top Purchased Products
                    </h4>
                    <div class="space-y-2">
                        @forelse($topPurchasedProducts->take(5) as $product)
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->sku ?? 'N/A' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-purple-600 dark:text-purple-400">{{ $product->purchase_order_items_count }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">purchases</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                <p>No purchase data available</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Slow Moving Products
                    </h4>
                    <div class="space-y-2">
                        @forelse($lowMovementProducts as $item)
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item['product']->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        No recent movements
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-red-600 dark:text-red-400">{{ $item['movements'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">movements</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                <p>All products are active</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Performance Summary
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $mostMovedProducts->where('movements', '>', 10)->count() }}</div>
                        <div class="text-sm text-green-700 dark:text-green-300">High Performers</div>
                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">>10 movements/month</div>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $mostMovedProducts->whereBetween('movements', [3, 10])->count() }}</div>
                        <div class="text-sm text-yellow-700 dark:text-yellow-300">Average Performers</div>
                        <div class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">3-10 movements/month</div>
                    </div>
                    <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $lowMovementProducts->count() }}</div>
                        <div class="text-sm text-red-700 dark:text-red-300">Low Performers</div>
                        <div class="text-xs text-red-600 dark:text-red-400 mt-1">≤2 movements/month</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>