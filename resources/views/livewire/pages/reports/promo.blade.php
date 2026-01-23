<x-slot:header>Promo Performance Dashboard</x-slot:header>
<x-slot:subheader>Comprehensive analysis of promotional campaigns and pricing strategies</x-slot:subheader>

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
                    <option value="active">Active</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Promos -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Promos</p>
                    <p class="text-3xl font-bold">{{ $totalPromos }}</p>
                    <p class="text-xs text-blue-100 mt-1">All promotional campaigns</p>
                </div>
                <svg class="w-12 h-12 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
            </div>
        </div>

        <!-- Active Promos -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Active Promos</p>
                    <p class="text-3xl font-bold">{{ $activePromos }}</p>
                    <p class="text-xs text-green-100 mt-1">Currently running</p>
                </div>
                <svg class="w-12 h-12 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Total Discount Value -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Discount Value</p>
                    <p class="text-3xl font-bold">₱{{ number_format($totalDiscountValue, 2) }}</p>
                    <p class="text-xs text-purple-100 mt-1">Potential savings</p>
                </div>
                <svg class="w-12 h-12 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
        </div>

        <!-- Average Discount -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Average Discount</p>
                    <p class="text-3xl font-bold">{{ number_format($averageDiscount, 1) }}%</p>
                    <p class="text-xs text-orange-100 mt-1">Across all promos</p>
                </div>
                <svg class="w-12 h-12 text-orange-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Unique Visualization: Price Comparison Matrix -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Price Impact Analysis
        </h3>

        <div class="space-y-6">
            @forelse($promos as $promo)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $promo->name }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $promo->code }} • {{ ucfirst($promo->type) }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                            Active
                        </span>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @forelse($promo->products as $product)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-2">{{ $product->name }}</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500 dark:text-gray-400">Current Price</span>
                                        <span class="font-medium text-gray-900 dark:text-white">₱{{ number_format($product->current_price, 2) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: 100%"></div>
                                    </div>

                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500 dark:text-gray-400">Discounted Price</span>
                                        <span class="font-medium text-green-600 dark:text-green-400">₱{{ number_format($product->discounted_price, 2) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($product->discounted_price / $product->current_price) * 100 }}%"></div>
                                    </div>

                                    <div class="flex justify-between text-xs font-medium text-red-600 dark:text-red-400">
                                        <span>Savings</span>
                                        <span>₱{{ number_format($product->current_price - $product->discounted_price, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">No products in this promo</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">No promos found</p>
                </div>
            @endforelse
        </div>
    </div>
</div>