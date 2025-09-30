<x-slot:header>Financial Summary</x-slot:header>
<x-slot:subheader>Financial Performance Report</x-slot:subheader>

<div class="space-y-6">
    <!-- Period Selection -->
    <x-collapsible-card title="Report Period" open="true" size="full">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-select wire:model.live="selectedPeriod" name="selectedPeriod" label="Quick Period">
                    <option value="current_month">Current Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="current_quarter">Current Quarter</option>
                    <option value="current_year">Current Year</option>
                </x-select>
            </div>
            <div>
                <x-input type="date" wire:model.live="dateFrom" name="dateFrom" label="From Date" />
            </div>
            <div>
                <x-input type="date" wire:model.live="dateTo" name="dateTo" label="To Date" />
            </div>
        </div>
    </x-collapsible-card>

    <!-- Report Header with Export/Refresh Buttons -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Profit & Loss Analysis
                </h3>
                <div class="flex space-x-2">
                    <button type="button" 
                        onclick="alert('Export functionality will be implemented')"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition-colors text-white bg-green-600 hover:bg-green-700">
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
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-lg shadow text-center text-white">
            <div class="text-3xl font-bold">₱{{ number_format($financialData['profit'], 2) }}</div>
            <div class="mt-2 opacity-90">Total Profit</div>
            <div class="text-sm mt-1 opacity-75">
                @if($financialData['profit'] > 0)
                    <span class="text-green-200">↗ Positive</span>
                @elseif($financialData['profit'] < 0)
                    <span class="text-red-200">↘ Loss</span>
                @else
                    <span class="text-gray-200">→ Break Even</span>
                @endif
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-lg shadow text-center text-white">
            <div class="text-3xl font-bold">₱{{ number_format($financialData['receivables']['total'], 2) }}</div>
            <div class="mt-2 opacity-90">Total Receivables</div>
            <div class="text-sm mt-1 opacity-75">
                {{ $financialData['receivables']['count'] }} transactions
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 rounded-lg shadow text-center text-white">
            <div class="text-3xl font-bold">₱{{ number_format($financialData['payables']['total'], 2) }}</div>
            <div class="mt-2 opacity-90">Total Payables</div>
            <div class="text-sm mt-1 opacity-75">
                {{ $financialData['payables']['count'] }} transactions
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-6 rounded-lg shadow text-center text-white">
            <div class="text-3xl font-bold">₱{{ number_format($financialData['expenses']['total'], 2) }}</div>
            <div class="mt-2 opacity-90">Total Expenses</div>
            <div class="text-sm mt-1 opacity-75">
                {{ $financialData['expenses']['count'] }} transactions
            </div>
        </div>
    </div>

    <!-- Detailed Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Receivables Breakdown -->
        <x-collapsible-card title="Receivables Breakdown" open="true" size="full">
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <span class="font-medium text-gray-900 dark:text-gray-100">Total Receivables</span>
                    <span class="font-bold text-blue-900 dark:text-blue-100">₱{{ number_format($financialData['receivables']['total'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <span class="font-medium text-gray-900 dark:text-gray-100">Paid</span>
                    <span class="font-bold text-green-900 dark:text-green-100">₱{{ number_format($financialData['receivables']['paid'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <span class="font-medium text-gray-900 dark:text-gray-100">Pending</span>
                    <span class="font-bold text-yellow-900 dark:text-yellow-100">₱{{ number_format($financialData['receivables']['pending'], 2) }}</span>
                </div>
            </div>
        </x-collapsible-card>

        <!-- Payables Breakdown -->
        <x-collapsible-card title="Payables Breakdown" open="true" size="full">
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                    <span class="font-medium text-gray-900 dark:text-gray-100">Total Payables</span>
                    <span class="font-bold text-red-900 dark:text-red-100">₱{{ number_format($financialData['payables']['total'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <span class="font-medium text-gray-900 dark:text-gray-100">Paid</span>
                    <span class="font-bold text-green-900 dark:text-green-100">₱{{ number_format($financialData['payables']['paid'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <span class="font-medium text-gray-900 dark:text-gray-100">Pending</span>
                    <span class="font-bold text-yellow-900 dark:text-yellow-100">₱{{ number_format($financialData['payables']['pending'], 2) }}</span>
                </div>
            </div>
        </x-collapsible-card>
    </div>

    <!-- Monthly Trend Chart -->
    {{-- <x-collapsible-card title="6-Month Financial Trend" open="true" size="full">
        <div class="h-64 flex items-end justify-between space-x-2 p-4">
            @foreach($monthlyTrend['months'] as $index => $month)
                <div class="flex flex-col items-center space-y-2 flex-1">
                    <div class="w-full space-y-1">
                        <!-- Receivables Bar -->
                        <div class="bg-blue-500 rounded-t" style="height: {{ ($monthlyTrend['receivables'][$index] / max($monthlyTrend['receivables'])) * 150 }}px;"></div>
                        <!-- Payables Bar -->
                        <div class="bg-red-500" style="height: {{ ($monthlyTrend['payables'][$index] / max($monthlyTrend['payables'])) * 150 }}px;"></div>
                        <!-- Expenses Bar -->
                        <div class="bg-yellow-500" style="height: {{ ($monthlyTrend['expenses'][$index] / max($monthlyTrend['expenses'])) * 150 }}px;"></div>
                        <!-- Profit Bar -->
                        <div class="bg-green-500 rounded-b" style="height: {{ abs($monthlyTrend['profit'][$index]) / max(array_map('abs', $monthlyTrend['profit'])) * 150 }}px;"></div>
                    </div>
                    <div class="text-xs text-center">
                        <div class="font-medium">{{ $month }}</div>
                        <div class="text-green-600">₱{{ number_format($monthlyTrend['profit'][$index], 0) }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="flex justify-center space-x-6 mt-4">
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-blue-500 rounded"></div>
                <span class="text-sm">Receivables</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-red-500 rounded"></div>
                <span class="text-sm">Payables</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                <span class="text-sm">Expenses</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-green-500 rounded"></div>
                <span class="text-sm">Profit</span>
            </div>
        </div>
    </x-collapsible-card>

    <!-- Top Expense Categories -->
    <x-collapsible-card title="Top Expense Categories" open="true" size="full">
        <div class="space-y-3">
            @foreach($topExpenseCategories as $category)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $category['category'] }}</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-32 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $category['percentage'] }}%"></div>
                        </div>
                        <span class="font-bold text-blue-700 dark:text-blue-300">₱{{ number_format($category['amount'], 2) }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $category['percentage'] }}%</span>
                    </div>
                </div>
            @endforeach
        </div>
    </x-collapsible-card> --}}

    <!-- Financial Summary -->
    <x-collapsible-card title="Financial Summary" open="true" size="full">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Revenue Analysis</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-700 dark:text-gray-300">Total Receivables:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">₱{{ number_format($financialData['receivables']['total'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700 dark:text-gray-300">Paid Receivables:</span>
                        <span class="font-medium text-green-600 dark:text-green-400">₱{{ number_format($financialData['receivables']['paid'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700 dark:text-gray-300">Pending Receivables:</span>
                        <span class="font-medium text-yellow-600 dark:text-yellow-400">₱{{ number_format($financialData['receivables']['pending'], 2) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Cost Analysis</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-700 dark:text-gray-300">Total Payables:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">₱{{ number_format($financialData['payables']['total'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700 dark:text-gray-300">Total Expenses:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">₱{{ number_format($financialData['expenses']['total'], 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-2">
                        <span class="font-semibold text-gray-800 dark:text-gray-200">Net Profit:</span>
                        <span class="font-bold {{ $financialData['profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            ₱{{ number_format($financialData['profit'], 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </x-collapsible-card>
</div>
