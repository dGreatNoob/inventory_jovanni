<div class="space-y-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex-1">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Product Inventory</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-neutral-300">Real-time inventory analytics and insights</p>
            </div>
            <div class="flex flex-row items-center space-x-3">
            <!-- Auto Refresh Toggle -->
            <div class="flex items-center">
                <input type="checkbox" 
                       wire:model.live="autoRefresh" 
                       id="autoRefresh"
                       class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded dark:border-gray-600 dark:focus:ring-gray-400">
                <label for="autoRefresh" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                    Auto Refresh
                </label>
            </div>
            
            <!-- Manual Refresh Button -->
            <flux:button 
                wire:click="refreshData" 
                variant="outline"
                class="flex items-center gap-2 whitespace-nowrap min-w-fit"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Refresh</span>
            </flux:button>
            
            @if($lastRefresh)
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Last updated: {{ $lastRefresh->diffForHumans() }}
                </span>
            @endif
        </div>
    </div>

    <!-- Alerts (placed right after header for priority visibility) -->
    @if(count($alerts) > 0)
        <div class="space-y-3 mb-6" aria-label="Inventory alerts">
            @foreach($alerts as $alert)
                <div class="rounded-md p-4 
                    @if($alert['type'] === 'error') bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800
                    @elseif($alert['type'] === 'warning') bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800
                    @else bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800
                    @endif">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 
                                @if($alert['type'] === 'error') text-red-400
                                @elseif($alert['type'] === 'warning') text-yellow-400
                                @else text-blue-400
                                @endif" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold 
                                @if($alert['type'] === 'error') text-red-800 dark:text-red-200
                                @elseif($alert['type'] === 'warning') text-yellow-800 dark:text-yellow-200
                                @else text-blue-800 dark:text-blue-200
                                @endif">
                                {{ $alert['title'] }}
                            </h3>
                            <p class="mt-1 text-sm 
                                @if($alert['type'] === 'error') text-red-700 dark:text-red-300
                                @elseif($alert['type'] === 'warning') text-yellow-700 dark:text-yellow-300
                                @else text-blue-700 dark:text-blue-300
                                @endif">{{ $alert['message'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Time Period Filter -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Time Period</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Select the time range for analytics</p>
            </div>
            <div class="flex flex-wrap gap-4 items-end" aria-label="Filters">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quick Select</label>
                    <select wire:model.live="timePeriod" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">Last year</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Date</label>
                    <input type="date" 
                           wire:model.live="dateFrom" 
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">To Date</label>
                    <input type="date" 
                           wire:model.live="dateTo" 
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="w-56">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                    <input type="text" 
                           placeholder="SKU or Product name" 
                           wire:model.live="search" 
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="w-56">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                    <select wire:model.live="categoryId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All</option>
                        @foreach($this->categoryOptions as $opt)
                            <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-56">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                    <select wire:model.live="supplierId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All</option>
                        @foreach($this->supplierOptions as $opt)
                            <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-56">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                    <select wire:model.live="locationId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All</option>
                        @foreach($this->locationOptions as $opt)
                            <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Balances Table (moved directly under Time Period) -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Inventory Balances</h3>
                <div class="flex items-center gap-3">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Shows on-hand, available, on-order and valuation</div>
                    <div class="flex items-center gap-2">
                        <flux:button size="sm" variant="ghost" wire:click="exportCsv">Export CSV</flux:button>
                        <flux:button size="sm" variant="ghost" wire:click="exportXlsx">Export XLSX</flux:button>
                        <flux:button size="sm" variant="ghost" wire:click="exportPdf">Export PDF</flux:button>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="flex flex-wrap items-center gap-3" aria-label="Column visibility">
                    @php($cols = $visibleColumns)
                    @foreach($cols as $col => $visible)
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" wire:model.live="visibleColumns.{{ $col }}" class="rounded border-gray-300 dark:border-gray-600">
                            <span>{{ ucwords(str_replace('_', ' ', $col)) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" aria-describedby="inventory-balances-caption">
                    <caption id="inventory-balances-caption" class="sr-only">Inventory balances with stock and valuation</caption>
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            @if($visibleColumns['sku'])
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">SKU</th>
                            @endif
                            @if($visibleColumns['name'])
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                <button type="button" class="inline-flex items-center gap-1" wire:click="sortBy('name')" aria-label="Sort by Product">
                                    Product
                                    @if($sortField==='name')
                                        <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path d="M5 8l5-5 5 5H5zm0 4h10l-5 5-5-5z"/></svg>
                                    @endif
                                </button>
                            </th>
                            @endif
                            @if($visibleColumns['category'])
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Category</th>
                            @endif
                            @if($visibleColumns['supplier'])
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Supplier</th>
                            @endif
                            @if($visibleColumns['on_hand'])
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                <button type="button" class="inline-flex items-center gap-1" wire:click="sortBy('on_hand')" aria-label="Sort by On-hand">On-hand
                                    @if($sortField==='on_hand')
                                        <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path d="M5 8l5-5 5 5H5zm0 4h10l-5 5-5-5z"/></svg>
                                    @endif
                                </button>
                            </th>
                            @endif
                            @if($visibleColumns['allocated'])
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Allocated</th>
                            @endif
                            @if($visibleColumns['available'])
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Available</th>
                            @endif
                            @if($visibleColumns['on_order'])
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">On-order</th>
                            @endif
                            @if($visibleColumns['unit_cost'])
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Unit Cost</th>
                            @endif
                            @if($visibleColumns['ext_value'])
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Ext. Value</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($inventoryBalances as $row)
                            <tr>
                                @if($visibleColumns['sku'])
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ $row['sku'] ?? '—' }}</td>
                                @endif
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('product-management.index', ['search' => $row['sku']]) }}" class="hover:underline">
                                            {{ $row['name'] ?? 'N/A' }}
                                        </a>
                                    </div>
                                </td>
                                @if($visibleColumns['category'])
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ $row['category'] ?? '—' }}</td>
                                @endif
                                @if($visibleColumns['supplier'])
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ $row['supplier'] ?? '—' }}</td>
                                @endif
                                @if($visibleColumns['on_hand'])
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">{{ number_format($row['on_hand']) }}</td>
                                @endif
                                @if($visibleColumns['allocated'])
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">{{ number_format($row['allocated']) }}</td>
                                @endif
                                @if($visibleColumns['available'])
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-emerald-700 dark:text-emerald-300">{{ number_format($row['available']) }}</td>
                                @endif
                                @if($visibleColumns['on_order'])
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">{{ number_format($row['on_order']) }}</td>
                                @endif
                                @if($visibleColumns['unit_cost'])
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">₱{{ number_format($row['unit_cost'], 2) }}</td>
                                @endif
                                @if($visibleColumns['ext_value'])
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium text-gray-900 dark:text-white">₱{{ number_format($row['ext_value'], 2) }}</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No inventory balances found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $inventoryBalances->links() }}</div>
            </div>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Totals</h4>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-300">On-hand</span>
                        <span class="text-gray-900 dark:text-white">{{ number_format($totals['on_hand']) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-300">Valuation</span>
                        <span class="text-gray-900 dark:text-white">₱{{ number_format($totals['value'], 2) }}</span>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">By Category</h4>
                    <div class="space-y-1 max-h-48 overflow-auto">
                        @foreach($categorySubtotals as $c)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-300">{{ $c->name }}</span>
                                <span class="text-gray-900 dark:text-white">{{ number_format($c->on_hand_sum) }} • ₱{{ number_format($c->value_sum, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">By Supplier</h4>
                    <div class="space-y-1 max-h-48 overflow-auto">
                        @foreach($supplierSubtotals as $s)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-300">{{ $s->name }}</span>
                                <span class="text-gray-900 dark:text-white">{{ number_format($s->on_hand_sum) }} • ₱{{ number_format($s->value_sum, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6" aria-label="Key performance indicators">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Products</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($overviewStats['total_products']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Products</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($overviewStats['active_products']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Low Stock</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($overviewStats['low_stock_products']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Inventory Value</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">₱{{ number_format($overviewStats['inventory_value'] ?? 0, 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Movement Stats (compact summary) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6" aria-label="Movement summary">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Movements</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($inventoryMovements['total_movements']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Inbound</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($inventoryMovements['inbound_movements']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Outbound</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($inventoryMovements['outbound_movements']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Adjustments</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($inventoryMovements['adjustment_movements']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6" aria-label="Insights">
        <!-- Top Products -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Top Products by Movement</h3>
                <div class="space-y-3">
                    @forelse($topProducts as $product)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->sku ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($product->total_movements ?? 0) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">movements</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No product movements found</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Low Stock Products</h3>
                <div class="space-y-3">
                    @forelse($lowStockProducts as $inventory)
                        <div class="flex items-center justify-between p-3 bg-rose-50 dark:bg-rose-900/20 rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventory->product->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $inventory->product->sku ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-rose-600 dark:text-rose-400">{{ $inventory->quantity }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">in stock</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No low stock products</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Movements (detailed ledger) -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6" aria-label="Recent inventory movements">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Recent Inventory Movements</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentMovements as $movement)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $movement->product->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $movement->product->sku ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($movement->movement_type === 'in') bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300
                                        @elseif($movement->movement_type === 'out') bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-300
                                        @else bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300
                                        @endif">
                                        {{ ucfirst($movement->movement_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ number_format($movement->quantity) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $movement->location->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $movement->creator->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $movement->created_at->format('M j, Y g:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No recent movements found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Subtotals & Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg" aria-label="Category distribution">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Products by Category</h3>
                <div class="space-y-3">
                    @forelse($categoryDistribution as $category)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->products_count }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">products</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No categories found</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Auto Refresh Script -->
@if($autoRefresh)
    <script>
        setInterval(function() {
            @this.call('refreshData');
        }, {{ $refreshInterval * 1000 }});
    </script>
@endif
