<x-slot:header>Purchase Orders Report</x-slot:header>
<x-slot:subheader>Analysis of purchase orders and supplier performance</x-slot:subheader>

<div class="space-y-6">
    <!-- Filters -->
    <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-800/50 grid gap-4 md:grid-cols-4">
        <div>
            <label for="dateFrom" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">From Date</label>
            <input type="date" id="dateFrom" wire:model.live="dateFrom" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        <div>
            <label for="dateTo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">To Date</label>
            <input type="date" id="dateTo" wire:model.live="dateTo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        <div>
            <label for="selectedSupplier" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Supplier</label>
            <select id="selectedSupplier" wire:model.live="selectedSupplier" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="statusFilter" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
            <select id="statusFilter" wire:model.live="statusFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="to_receive">To Receive</option>
                <option value="received">Received</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $dashboardStats = [
                [
                    'label' => 'Total Purchase Orders',
                    'value' => number_format($totalPOs),
                    'gradient' => 'from-blue-500 to-blue-600',
                    'change' => null,
                    'period' => null
                ],
                [
                    'label' => 'Pending Approval',
                    'value' => number_format($pendingPOs),
                    'gradient' => 'from-yellow-500 to-yellow-600',
                    'change' => null,
                    'period' => null
                ],
                [
                    'label' => 'To Receive',
                    'value' => number_format($toReceivePOs),
                    'gradient' => 'from-orange-500 to-orange-600',
                    'change' => null,
                    'period' => null
                ],
                [
                    'label' => 'Total Value',
                    'value' => '₱' . number_format($totalValue, 0),
                    'gradient' => 'from-green-500 to-green-600',
                    'change' => null,
                    'period' => null
                ]
            ];
        @endphp
        
        @foreach($dashboardStats as $index => $stat)
            <div class="bg-gradient-to-r {{ $stat['gradient'] }} rounded-lg p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">{{ $stat['label'] }}</p>
                        <p class="text-3xl font-bold">{{ $stat['value'] }}</p>
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
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        @elseif($index === 1)
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @elseif($index === 2)
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        @else
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Order Status Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                Order Status Distribution
            </h4>
            <div class="relative h-48">
                <!-- Donut Chart -->
                <div class="flex items-center justify-center h-full">
                    <div class="relative w-40 h-40">
                        @php
                            $totalOrdersNum = $totalPOs ?: 1;
                            $pendingPercentage = ($pendingPOs / $totalOrdersNum) * 100;
                            $toReceivePercentage = ($toReceivePOs / $totalOrdersNum) * 100;
                            $receivedPercentage = ($receivedPOs / $totalOrdersNum) * 100;
                            $currentAngle = 0;
                        @endphp
                        
                        <svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 36 36">
                            <!-- Background circle -->
                            <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="4" fill="transparent" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            
                            <!-- Pending (Yellow) -->
                            <path stroke="#f59e0b" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $pendingPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            
                            @php $currentAngle += $pendingPercentage; @endphp
                            
                            <!-- To Receive (Blue) -->
                            <path stroke="#3b82f6" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $toReceivePercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            
                            @php $currentAngle += $toReceivePercentage; @endphp
                            
                            <!-- Received (Green) -->
                            <path stroke="#10b981" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $receivedPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
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
                    <span class="text-gray-700 dark:text-gray-300">Pending: {{ $pendingPOs }} ({{ round($pendingPercentage, 1) }}%)</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">To Receive: {{ $toReceivePOs }} ({{ round($toReceivePercentage, 1) }}%)</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Received: {{ $receivedPOs }} ({{ round($receivedPercentage, 1) }}%)</span>
                </div>
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Purchase Trend (6 Months)
            </h4>
            
            <!-- Bar Chart -->
            <div class="relative h-48 flex items-end justify-between space-x-2 px-4">
                @php
                    $safeValues = $values ?: [0];
                    $maxValue = max($safeValues);
                @endphp
                @foreach($months as $index => $month)
                    @php
                        $val = $values[$index] ?? 0;
                        $height = $maxValue > 0 ? ($val / $maxValue) * 140 : 0;
                    @endphp
                    <div class="flex flex-col items-center group">
                        <div class="relative bg-blue-500 rounded-t-sm hover:bg-blue-600 transition-colors" style="height: {{ $height }}px; width: 32px;">
                            <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                {{ $val }}
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

    <!-- Supplier Performance and Recent Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Suppliers -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Top Suppliers by Orders
            </h3>
            
            <div class="space-y-4">
                @php $maxSupplierOrders = $topSuppliers->max('order_count') ?: 1; @endphp
                
                @forelse($topSuppliers as $supplierData)
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $supplierData->supplier->name ?? 'Unknown Supplier' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $supplierData->order_count }} orders • ₱{{ number_format($supplierData->total_value ?: 0) }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ ($supplierData->order_count / $maxSupplierOrders) * 100 }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-6">{{ $supplierData->order_count }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No supplier data available</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Purchase Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Recent Purchase Orders
            </h3>
            
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse($recentOrders as $order)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->po_num }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->supplier->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->order_date ? $order->order_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            @php
                                $color = $order->display_status_color; // yellow/blue/purple/green/red/orange
                                $badge = "inline-block px-2 py-1 text-xs font-semibold rounded-full bg-{$color}-100 text-{$color}-800";
                            @endphp
                            <span class="{{ $badge }}">
                                {{ $order->display_status }}
                            </span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">₱{{ number_format($order->total_price, 2) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No purchase orders found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>