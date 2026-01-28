<x-slot:header>Shipment Performance Report</x-slot:header>
<x-slot:subheader>Comprehensive analysis of shipment operations, delivery performance, and logistics efficiency</x-slot:subheader>

<div class="space-y-6">
    <!-- Time Period Filter -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0 mb-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Time Period</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Select the time range for analytics</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quick Select</label>
                <select wire:model.live="timePeriod" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
            </div>
        </div>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Date</label>
                <input type="date" wire:model.live="dateFrom" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">To Date</label>
                <input type="date" wire:model.live="dateTo" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select wire:model.live="statusFilter" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="in_transit">In Transit</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="damaged">Damaged</option>
                    <option value="incomplete">Incomplete</option>
                </select>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $dashboardStats = [
                [
                    'label' => 'Total Shipments',
                    'value' => number_format($totalShipments),
                    'gradient' => 'from-blue-500 to-blue-600',
                    'icon' => 'truck'
                ],
                [
                    'label' => 'Active Shipments',
                    'value' => number_format($activeShipments),
                    'gradient' => 'from-orange-500 to-orange-600',
                    'icon' => 'clock'
                ],
                [
                    'label' => 'Completed Shipments',
                    'value' => number_format($completedShipments),
                    'gradient' => 'from-green-500 to-green-600',
                    'icon' => 'check-circle'
                ],
                [
                    'label' => 'Delivery Efficiency',
                    'value' => $totalShipments > 0 ? round(($completedShipments / $totalShipments) * 100, 1) . '%' : '0%',
                    'gradient' => 'from-purple-500 to-purple-600',
                    'icon' => 'chart-bar'
                ]
            ];
        @endphp

        @foreach($dashboardStats as $stat)
            <div class="bg-gradient-to-r {{ $stat['gradient'] }} rounded-lg p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">{{ $stat['label'] }}</p>
                        <p class="text-3xl font-bold">{{ $stat['value'] }}</p>
                    </div>
                    <div class="text-blue-100">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($stat['icon'] === 'truck')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            @elseif($stat['icon'] === 'clock')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @elseif($stat['icon'] === 'check-circle')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            @endif
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Shipment Status Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                Shipment Status Distribution
            </h3>
            <div class="relative h-48">
                <!-- Donut Chart -->
                <div class="flex items-center justify-center h-full">
                    <div class="relative w-40 h-40">
                        @php
                            $totalShipmentsNum = $totalShipments ?: 1;
                            $pendingPercentage = ($statusCounts['pending'] ?? 0) / $totalShipmentsNum * 100;
                            $approvedPercentage = ($statusCounts['approved'] ?? 0) / $totalShipmentsNum * 100;
                            $inTransitPercentage = ($statusCounts['in_transit'] ?? 0) / $totalShipmentsNum * 100;
                            $completedPercentage = ($statusCounts['completed'] ?? 0) / $totalShipmentsNum * 100;
                            $cancelledPercentage = ($statusCounts['cancelled'] ?? 0) / $totalShipmentsNum * 100;
                            $damagedPercentage = ($statusCounts['damaged'] ?? 0) / $totalShipmentsNum * 100;
                            $incompletePercentage = ($statusCounts['incomplete'] ?? 0) / $totalShipmentsNum * 100;

                            $currentAngle = 0;
                        @endphp

                        <svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 36 36">
                            <!-- Background circle -->
                            <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="4" fill="transparent" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />

                            <!-- Pending (Yellow) -->
                            <path stroke="#f59e0b" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $pendingPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @php $currentAngle += $pendingPercentage; @endphp

                            <!-- Approved (Blue) -->
                            <path stroke="#3b82f6" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $approvedPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @php $currentAngle += $approvedPercentage; @endphp

                            <!-- In Transit (Orange) -->
                            <path stroke="#f97316" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $inTransitPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @php $currentAngle += $inTransitPercentage; @endphp

                            <!-- Completed (Green) -->
                            <path stroke="#10b981" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $completedPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @php $currentAngle += $completedPercentage; @endphp

                            <!-- Cancelled (Red) -->
                            <path stroke="#ef4444" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $cancelledPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @php $currentAngle += $cancelledPercentage; @endphp

                            <!-- Damaged (Purple) -->
                            <path stroke="#8b5cf6" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $damagedPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @php $currentAngle += $damagedPercentage; @endphp

                            <!-- Incomplete (Gray) -->
                            <path stroke="#6b7280" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $incompletePercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>

                        <!-- Center text -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $totalShipmentsNum }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Shipments</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-4 grid grid-cols-2 gap-2">
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Pending: {{ $statusCounts['pending'] ?? 0 }}</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Approved: {{ $statusCounts['approved'] ?? 0 }}</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-orange-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">In Transit: {{ $statusCounts['in_transit'] ?? 0 }}</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Completed: {{ $statusCounts['completed'] ?? 0 }}</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Cancelled: {{ $statusCounts['cancelled'] ?? 0 }}</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Damaged: {{ $statusCounts['damaged'] ?? 0 }}</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-gray-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Incomplete: {{ $statusCounts['incomplete'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Monthly Shipment Trend -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Shipment Creation Trend (6 Months)
            </h3>

            <!-- Bar Chart -->
            <div class="relative h-48 flex items-end justify-between space-x-2 px-4">
                @php
                    $safeValues = $monthlyShipments ?: [0];
                    $maxValue = max($safeValues);
                @endphp
                @foreach($months as $index => $month)
                    @php
                        $val = $monthlyShipments[$index] ?? 0;
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
                <p class="text-xs text-gray-500 dark:text-gray-400">Shipments created per month</p>
            </div>
        </div>
    </div>

    <!-- Additional Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Branches by Shipments -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Top Branches by Shipment Volume
            </h3>

            <div class="space-y-4">
                @php $maxShipments = $topBranches->max('shipment_count') ?: 1; @endphp

                @forelse($topBranches as $branch)
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $branch->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $branch->shipment_count }} shipments</p>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ ($branch->shipment_count / $maxShipments) * 100 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white min-w-max">{{ $branch->shipment_count }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No branch data available</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Delivery Method Performance -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Delivery Method Distribution
            </h3>

            <div class="space-y-4">
                @php $maxMethod = $deliveryMethods->max('count') ?: 1; @endphp

                @forelse($deliveryMethods as $method)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($method->delivery_method) }}</span>
                        <div class="flex items-center space-x-2">
                            <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($method->count / $maxMethod) * 100 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $method->count }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No delivery method data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pending Shipments Alert -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            Pending Shipments Requiring Attention
        </h3>

        <div class="space-y-3 max-h-64 overflow-y-auto">
            @forelse($pendingShipments as $shipment)
                <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $shipment->shipping_plan_num }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $shipment->branchAllocation->branch->name ?? 'N/A' }} • {{ \Carbon\Carbon::parse($shipment->scheduled_ship_date)->format('M d, Y') }}</p>
                    </div>
                    <div class="text-right ml-4">
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                            {{ ucfirst(str_replace('_', ' ', $shipment->shipping_status)) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <svg class="w-12 h-12 mx-auto text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No pending shipments</p>
                </div>
            @endforelse
        </div>
    </div>
</div>