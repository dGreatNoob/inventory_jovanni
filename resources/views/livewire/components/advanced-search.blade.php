<div class="bg-white dark:bg-gray-800 shadow rounded-lg">
    <div class="p-6">
        <!-- Search Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <!-- Main Search Bar -->
            <div class="flex-1 max-w-2xl">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" 
                           type="text" 
                           placeholder="Search products..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white dark:bg-gray-700 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:text-white">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-3">
                <!-- Search Fields Toggle -->
                <button wire:click="toggleSearchFields" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Fields
                </button>

                <!-- Advanced Filters Toggle -->
                <button wire:click="toggleAdvancedFilters" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filters
                    @if($this->getActiveFiltersCount() > 0)
                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                            {{ $this->getActiveFiltersCount() }}
                        </span>
                    @endif
                </button>

                <!-- Clear Filters -->
                @if($this->getActiveFiltersCount() > 0 || $search)
                    <button wire:click="clearFilters" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear
                    </button>
                @endif
            </div>
        </div>

        <!-- Search Fields Selection -->
        @if($showSearchFields)
            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Search in these fields:</h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($availableSearchFields as $field => $label)
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   wire:click="toggleSearchField('{{ $field }}')"
                                   @if(in_array($field, $searchFields)) checked @endif
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Advanced Filters -->
        @if($showAdvancedFilters)
            <div class="mt-6 space-y-6">
                <!-- Basic Filters Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                        <select wire:model.live="categoryFilter" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Supplier Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                        <select wire:model.live="supplierFilter" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                        <select wire:model.live="locationFilter" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">All Locations</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select wire:model.live="statusFilter" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Price Range Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Price Min -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Min Price</label>
                        <input type="number" 
                               wire:model.live.debounce.300ms="priceMin" 
                               step="0.01"
                               min="0"
                               class="mt-1 block w-full pl-3 pr-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="0.00">
                    </div>

                    <!-- Price Max -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Price</label>
                        <input type="number" 
                               wire:model.live.debounce.300ms="priceMax" 
                               step="0.01"
                               min="0"
                               class="mt-1 block w-full pl-3 pr-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="999.99">
                    </div>

                    <!-- Cost Min -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Min Cost</label>
                        <input type="number" 
                               wire:model.live.debounce.300ms="costMin" 
                               step="0.01"
                               min="0"
                               class="mt-1 block w-full pl-3 pr-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="0.00">
                    </div>

                    <!-- Cost Max -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Cost</label>
                        <input type="number" 
                               wire:model.live.debounce.300ms="costMax" 
                               step="0.01"
                               min="0"
                               class="mt-1 block w-full pl-3 pr-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="999.99">
                    </div>
                </div>

                <!-- Stock Level and Date Range Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Stock Level Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock Level</label>
                        <select wire:model.live="stockLevelFilter" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">All Stock Levels</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock (< 10)</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created From</label>
                        <input type="date" 
                               wire:model.live="dateFrom" 
                               class="mt-1 block w-full pl-3 pr-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created To</label>
                        <input type="date" 
                               wire:model.live="dateTo" 
                               class="mt-1 block w-full pl-3 pr-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Per Page -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Per Page</label>
                        <select wire:model.live="perPage" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <!-- Advanced Options Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Sort By -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort By</label>
                        <select wire:model.live="sortBy" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="created_at">Created Date</option>
                            <option value="name">Name</option>
                            <option value="sku">SKU</option>
                            <option value="price">Price</option>
                            <option value="cost">Cost</option>
                            <option value="updated_at">Updated Date</option>
                        </select>
                    </div>

                    <!-- Sort Direction -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Direction</label>
                        <select wire:model.live="sortDirection" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>
                        </select>
                    </div>

                    <!-- Checkboxes -->
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   wire:model.live="exactMatch" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Exact Match</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   wire:model.live="includeInactive" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Include Inactive</span>
                        </label>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
