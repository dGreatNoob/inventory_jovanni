<x-slot:header>Warehouse Allocation Report</x-slot:header>
<x-slot:subheader>Overview of warehouse batch allocations and performance metrics</x-slot:subheader>

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
                    <option value="draft">Draft</option>
                    <option value="pending">Pending</option>
                    <option value="dispatched">Dispatched</option>
                </select>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $dashboardStats = [
                [
                    'label' => 'Total Batches',
                    'value' => number_format($totalBatches),
                    'gradient' => 'from-blue-500 to-blue-600',
                    'icon' => 'cube'
                ],
                [
                    'label' => 'Total Allocations',
                    'value' => number_format($totalAllocations),
                    'gradient' => 'from-green-500 to-green-600',
                    'icon' => 'building-storefront'
                ],
                [
                    'label' => 'Total Products',
                    'value' => number_format($totalProducts),
                    'gradient' => 'from-purple-500 to-purple-600',
                    'icon' => 'calculator'
                ],
                [
                    'label' => 'Total Value',
                    'value' => '₱' . number_format($totalValue, 0),
                    'gradient' => 'from-orange-500 to-orange-600',
                    'icon' => 'banknotes'
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
                            @if($stat['icon'] === 'cube')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            @elseif($stat['icon'] === 'building-storefront')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            @elseif($stat['icon'] === 'calculator')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            @endif
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Distribution Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                Batch Status Distribution
            </h3>
            <div class="relative h-48">
                <!-- Donut Chart -->
                <div class="flex items-center justify-center h-full">
                    <div class="relative w-40 h-40">
                        @php
                            $totalBatchesNum = $totalBatches ?: 1;
                            $draftPercentage = ($draftBatches / $totalBatchesNum) * 100;
                            $pendingPercentage = ($pendingBatches / $totalBatchesNum) * 100;
                            $dispatchedPercentage = ($dispatchedBatches / $totalBatchesNum) * 100;

                            $currentAngle = 0;
                        @endphp

                        <svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 36 36">
                            <!-- Background circle -->
                            <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="4" fill="transparent" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />

                            <!-- Draft (Gray) -->
                            <path stroke="#6b7280" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $draftPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />

                            @php $currentAngle += $draftPercentage; @endphp

                            <!-- Pending (Yellow) -->
                            <path stroke="#f59e0b" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $pendingPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />

                            @php $currentAngle += $pendingPercentage; @endphp

                            <!-- Dispatched (Green) -->
                            <path stroke="#10b981" stroke-width="4" fill="transparent" stroke-linecap="round" stroke-dasharray="{{ $dispatchedPercentage }} 100" stroke-dashoffset="{{ -$currentAngle }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>

                        <!-- Center text -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $totalBatchesNum }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Batches</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-4 space-y-2">
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-gray-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Draft: {{ $draftBatches }} ({{ round($draftPercentage, 1) }}%)</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Pending: {{ $pendingBatches }} ({{ round($pendingPercentage, 1) }}%)</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Dispatched: {{ $dispatchedBatches }} ({{ round($dispatchedPercentage, 1) }}%)</span>
                </div>
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Batch Creation Trend (6 Months)
            </h3>

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
                <p class="text-xs text-gray-500 dark:text-gray-400">Batches created per month</p>
            </div>
        </div>
    </div>

    <!-- Recent Batches -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Recent Warehouse Batches
        </h3>

        <div class="space-y-3 max-h-64 overflow-y-auto">
            @forelse($recentBatches as $batch)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $batch->ref_no }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $batch->batch_number }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $batch->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="text-right">
                        @php
                            $statusColor = $batch->status === 'dispatched' ? 'green' : ($batch->status === 'pending' ? 'yellow' : 'gray');
                            $badge = "inline-block px-2 py-1 text-xs font-semibold rounded-full bg-{$statusColor}-100 text-{$statusColor}-800";
                        @endphp
                        <span class="{{ $badge }}">
                            {{ ucfirst($batch->status) }}
                        </span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $batch->branchAllocations->count() }} branches</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No warehouse batches found</p>
                </div>
            @endforelse
        </div>
    </div>
</div>