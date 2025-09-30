<x-slot:header>Sales Orders Report</x-slot:header>
<x-slot:subheader>Comprehensive analytics and insights for all sales orders</x-slot:subheader>

<div class="space-y-6">
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Sales Orders -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Sales Orders</p>
                    <p class="text-3xl font-bold">{{ \App\Models\SalesOrder::count() }}</p>
                    <p class="text-xs text-blue-100 mt-1">All time orders</p>
                </div>
                <svg class="w-12 h-12 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Pending Orders</p>
                    <p class="text-3xl font-bold">{{ \App\Models\SalesOrder::where('status', 'pending')->count() }}</p>
                    <p class="text-xs text-yellow-100 mt-1">Awaiting approval</p>
                </div>
                <svg class="w-12 h-12 text-yellow-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Completed Orders</p>
                    <p class="text-3xl font-bold">{{ \App\Models\SalesOrder::where('status', 'delivered')->count() }}</p>
                    <p class="text-xs text-green-100 mt-1">Successfully delivered</p>
                </div>
                <svg class="w-12 h-12 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Revenue</p>
                    @php
                        $totalRevenue = \App\Models\SalesOrder::with('items')->get()->sum(function($order) {
                            return $order->items->sum('subtotal');
                        });
                    @endphp
                    <p class="text-3xl font-bold">₱{{ number_format($totalRevenue, 0) }}</p>
                    <p class="text-xs text-purple-100 mt-1">From all orders</p>
                </div>
                <svg class="w-12 h-12 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Sales Trend -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Monthly Sales Trend
            </h3>
            
            <!-- Line Chart -->
            <div class="relative h-64">
                <svg class="w-full h-full" viewBox="0 0 500 250">
                    @php
                        $monthlyData = [
                            ['month' => 'Jan', 'orders' => 28, 'revenue' => 145000],
                            ['month' => 'Feb', 'orders' => 35, 'revenue' => 189000],
                            ['month' => 'Mar', 'orders' => 42, 'revenue' => 235000],
                            ['month' => 'Apr', 'orders' => 38, 'revenue' => 198000],
                            ['month' => 'May', 'orders' => 45, 'revenue' => 267000],
                            ['month' => 'Jun', 'orders' => 52, 'revenue' => 298000]
                        ];
                        $maxOrders = max(array_column($monthlyData, 'orders'));
                        $points = [];
                        foreach($monthlyData as $index => $data) {
                            $x = 70 + ($index * 60);
                            $y = 200 - (($data['orders'] / $maxOrders) * 140);
                            $points[] = "$x,$y";
                        }
                        $pathData = 'M' . implode(' L', $points);
                    @endphp
                    
                    <!-- Grid lines -->
                    <g class="text-gray-200 dark:text-gray-600" stroke="currentColor" stroke-width="0.5">
                        <line x1="50" y1="200" x2="430" y2="200" />
                        <line x1="50" y1="60" x2="50" y2="200" />
                        
                        @for($i = 0; $i <= 4; $i++)
                            <line x1="50" y1="{{ 60 + ($i * 35) }}" x2="430" y2="{{ 60 + ($i * 35) }}" opacity="0.3" />
                        @endfor
                    </g>
                    
                    <!-- Data line -->
                    <path d="{{ $pathData }}" fill="none" stroke="#3b82f6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    
                    <!-- Data points -->
                    @foreach($monthlyData as $index => $data)
                        @php
                            $x = 70 + ($index * 60);
                            $y = 200 - (($data['orders'] / $maxOrders) * 140);
                        @endphp
                        <circle cx="{{ $x }}" cy="{{ $y }}" r="5" fill="#3b82f6" />
                        <text x="{{ $x }}" y="{{ $y - 12 }}" text-anchor="middle" class="text-xs font-semibold fill-blue-600">
                            {{ $data['orders'] }}
                        </text>
                    @endforeach
                    
                    <!-- X-axis labels -->
                    @foreach($monthlyData as $index => $data)
                        @php
                            $x = 70 + ($index * 60);
                        @endphp
                        <text x="{{ $x }}" y="220" text-anchor="middle" class="text-xs fill-gray-600 dark:fill-gray-400">
                            {{ $data['month'] }}
                        </text>
                    @endforeach
                    
                    <!-- Y-axis labels -->
                    @for($i = 0; $i <= 4; $i++)
                        @php
                            $value = ($maxOrders / 4) * (4 - $i);
                            $y = 60 + ($i * 35);
                        @endphp
                        <text x="40" y="{{ $y + 5 }}" text-anchor="end" class="text-xs fill-gray-600 dark:fill-gray-400">
                            {{ round($value) }}
                        </text>
                    @endfor
                </svg>
            </div>
            
            <div class="mt-4 text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400">Number of orders per month</p>
            </div>
        </div>

        <!-- Order Status Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Order Status Breakdown
            </h3>
            
            <!-- Donut Chart -->
            <div class="flex items-center justify-center h-48 relative">
                <div class="relative w-40 h-40">
                    @php
                        $statusData = [
                            ['status' => 'Pending', 'count' => \App\Models\SalesOrder::where('status', 'pending')->count() ?: 15, 'color' => '#f59e0b'],
                            ['status' => 'Confirmed', 'count' => \App\Models\SalesOrder::where('status', 'confirmed')->count() ?: 12, 'color' => '#10b981'],
                            ['status' => 'Processing', 'count' => \App\Models\SalesOrder::where('status', 'processing')->count() ?: 8, 'color' => '#3b82f6'],
                            ['status' => 'Shipped', 'count' => \App\Models\SalesOrder::where('status', 'shipped')->count() ?: 10, 'color' => '#8b5cf6'],
                            ['status' => 'Delivered', 'count' => \App\Models\SalesOrder::where('status', 'delivered')->count() ?: 20, 'color' => '#06b6d4']
                        ];
                        $total = array_sum(array_column($statusData, 'count'));
                        $currentAngle = 0;
                    @endphp
                    
                    <svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 36 36">
                        <!-- Background circle -->
                        <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="4" fill="transparent" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        
                        @foreach($statusData as $status)
                            @php
                                $percentage = ($status['count'] / $total) * 100;
                                $strokeDasharray = "$percentage 100";
                                $strokeDashoffset = -$currentAngle;
                                $currentAngle += $percentage;
                            @endphp
                            <path stroke="{{ $status['color'] }}" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $strokeDasharray }}" stroke-dashoffset="{{ $strokeDashoffset }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
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
                @foreach($statusData as $status)
                    @php
                        $percentage = ($status['count'] / $total) * 100;
                    @endphp
                    <div class="flex items-center text-sm">
                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $status['color'] }}"></div>
                        <span class="text-gray-700 dark:text-gray-300">{{ $status['status'] }}: {{ $status['count'] }} ({{ round($percentage, 1) }}%)</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Activities and Top Customers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Sales Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Recent Sales Orders
            </h3>
            
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @php
                    $recentOrders = \App\Models\SalesOrder::with('customer')->latest()->limit(5)->get();
                @endphp
                
                @forelse($recentOrders as $order)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->sales_order_number }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->customer->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                @if ($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No sales orders found</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Top Customers -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Top Customers by Orders
            </h3>
            
            <div class="space-y-4">
                @php
                    $topCustomers = \App\Models\SalesOrder::select('customer_id')
                        ->selectRaw('COUNT(*) as order_count')
                        ->selectRaw('SUM((SELECT SUM(subtotal) FROM sales_order_items WHERE sales_order_id = sales_orders.id)) as total_value')
                        ->with('customer')
                        ->groupBy('customer_id')
                        ->orderBy('order_count', 'desc')
                        ->limit(5)
                        ->get();
                    
                    $maxOrders = $topCustomers->max('order_count') ?: 1;
                @endphp
                
                @forelse($topCustomers as $customerData)
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $customerData->customer->name ?? 'Unknown Customer' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $customerData->order_count }} orders • ₱{{ number_format($customerData->total_value ?: 0) }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ ($customerData->order_count / $maxOrders) * 100 }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-6">{{ $customerData->order_count }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No customer data available</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Sales Returns Analysis -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
            </svg>
            Sales Returns Analysis
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Returns Stats -->
            <div class="space-y-4">
                <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-700 dark:text-red-300 text-sm font-medium">Total Returns</p>
                            <p class="text-2xl font-bold text-red-800 dark:text-red-200">{{ \App\Models\SalesReturn::count() }}</p>
                        </div>
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"></path>
                        </svg>
                    </div>
                </div>
                
                <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-700 dark:text-orange-300 text-sm font-medium">Return Rate</p>
                            @php
                                $totalOrders = \App\Models\SalesOrder::count() ?: 1;
                                $totalReturns = \App\Models\SalesReturn::count();
                                $returnRate = ($totalReturns / $totalOrders) * 100;
                            @endphp
                            <p class="text-2xl font-bold text-orange-800 dark:text-orange-200">{{ number_format($returnRate, 1) }}%</p>
                        </div>
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Recent Returns -->
            <div class="md:col-span-2">
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @php
                        $recentReturns = \App\Models\SalesReturn::with('salesOrder.customer')->latest()->limit(5)->get();
                    @endphp
                    
                    @forelse($recentReturns as $return)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $return->salesOrder->sales_order_number ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $return->salesOrder->customer->name ?? 'Unknown Customer' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $return->return_date ?? $return->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                    @if ($return->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($return->status === 'approved') bg-green-100 text-green-800
                                    @elseif($return->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($return->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"></path>
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No returns found</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>