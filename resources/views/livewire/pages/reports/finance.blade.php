<x-slot:header>Finance Report Dashboard</x-slot:header>
<x-slot:subheader>Comprehensive overview of expenses, receivables, payables, and payments</x-slot:subheader>

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
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Date</label>
                <input type="date" wire:model.live="dateFrom" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">To Date</label>
                <input type="date" wire:model.live="dateTo" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
        </div>
    </div>

    <!-- Overall Financial Position -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-indigo-100 text-sm font-medium">Net Financial Position</p>
                <p class="text-4xl font-bold">₱{{ number_format($overallStats['net_position'], 2) }}</p>
                <p class="text-xs text-indigo-100 mt-1">Receivables - Payables - Expenses + Payments</p>
            </div>
            <div class="text-indigo-100">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Module Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Expenses -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Total Expenses</p>
                    <p class="text-3xl font-bold">₱{{ number_format($expenseStats['total'], 2) }}</p>
                    <p class="text-xs text-red-100 mt-1">{{ $expenseStats['count'] }} transactions</p>
                </div>
                <svg class="w-12 h-12 text-red-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
        </div>

        <!-- Receivables -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Receivables</p>
                    <p class="text-3xl font-bold">₱{{ number_format($receivableStats['total'], 2) }}</p>
                    <p class="text-xs text-green-100 mt-1">Outstanding: ₱{{ number_format($receivableStats['balance'], 2) }}</p>
                </div>
                <svg class="w-12 h-12 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Payables -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Payables</p>
                    <p class="text-3xl font-bold">₱{{ number_format($payableStats['total'], 2) }}</p>
                    <p class="text-xs text-blue-100 mt-1">Outstanding: ₱{{ number_format($payableStats['balance'], 2) }}</p>
                </div>
                <svg class="w-12 h-12 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-6 6h6m0 0v-6m0 6H9m6 0H9m0 0v6m0-6v6"></path>
                </svg>
            </div>
        </div>

        <!-- Payments -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Payments</p>
                    <p class="text-3xl font-bold">₱{{ number_format($paymentStats['total'], 2) }}</p>
                    <p class="text-xs text-purple-100 mt-1">{{ $paymentStats['count'] }} transactions</p>
                </div>
                <svg class="w-12 h-12 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Charts and Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expense Categories -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Expense Categories Breakdown
            </h3>
            <div class="space-y-4">
                @forelse($expenseStats['categories'] as $category)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $category->category)) }}</span>
                        <div class="flex items-center space-x-2">
                            <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full" style="width: {{ $expenseStats['total'] > 0 ? ($category->total / $expenseStats['total']) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">₱{{ number_format($category->total, 2) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No expense data available</p>
                @endforelse
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Payment Methods Distribution
            </h3>
            <div class="space-y-4">
                @forelse($paymentStats['methods'] as $method)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $method->payment_method }}</span>
                        <div class="flex items-center space-x-2">
                            <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $paymentStats['total'] > 0 ? ($method->total / $paymentStats['total']) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">₱{{ number_format($method->total, 2) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No payment data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Status Distributions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Receivable Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Receivable Status Distribution</h3>
            <div class="space-y-3">
                @forelse($receivableStats['statuses'] as $status)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($status->status) }}</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status->count }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No receivable data available</p>
                @endforelse
            </div>
        </div>

        <!-- Payable Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payable Status Distribution</h3>
            <div class="space-y-3">
                @forelse($payableStats['statuses'] as $status)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($status->status) }}</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status->count }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No payable data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Monthly Trends -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Financial Trends</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Expenses Trend -->
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expenses</h4>
                <div class="space-y-2">
                    @forelse($expenseStats['monthly'] as $month)
                        <div class="flex justify-between text-xs">
                            <span>{{ $month->year }}-{{ str_pad($month->month, 2, '0', STR_PAD_LEFT) }}</span>
                            <span>₱{{ number_format($month->total, 0) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500">No data</p>
                    @endforelse
                </div>
            </div>

            <!-- Receivables Trend -->
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Receivables</h4>
                <div class="space-y-2">
                    @forelse($receivableStats['monthly'] as $month)
                        <div class="flex justify-between text-xs">
                            <span>{{ $month->year }}-{{ str_pad($month->month, 2, '0', STR_PAD_LEFT) }}</span>
                            <span>₱{{ number_format($month->total, 0) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500">No data</p>
                    @endforelse
                </div>
            </div>

            <!-- Payables Trend -->
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payables</h4>
                <div class="space-y-2">
                    @forelse($payableStats['monthly'] as $month)
                        <div class="flex justify-between text-xs">
                            <span>{{ $month->year }}-{{ str_pad($month->month, 2, '0', STR_PAD_LEFT) }}</span>
                            <span>₱{{ number_format($month->total, 0) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500">No data</p>
                    @endforelse
                </div>
            </div>

            <!-- Payments Trend -->
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payments</h4>
                <div class="space-y-2">
                    @forelse($paymentStats['monthly'] as $month)
                        <div class="flex justify-between text-xs">
                            <span>{{ $month->year }}-{{ str_pad($month->month, 2, '0', STR_PAD_LEFT) }}</span>
                            <span>₱{{ number_format($month->total, 0) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500">No data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>