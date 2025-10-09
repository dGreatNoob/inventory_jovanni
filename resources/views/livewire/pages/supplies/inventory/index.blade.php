<x-slot:header>Product Inventory</x-slot:header>
<x-slot:subheader>Manage Your Product Catalog</x-slot:subheader>

<div class="pt-4">
    <!-- Products Table with Integrated Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header Section with Button -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('supplies.inventory.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Product Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Search and Filters Section -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Search Bar -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live="search" 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="Search products...">
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-4">
                    <!-- Storage Filter -->
                    <div class="min-w-0 flex-1 lg:min-w-48">
                        <select wire:model.live="storageFilter" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Storage</option>
                            <option value="warehouse">Warehouse</option>
                            <option value="store">Store</option>
                            <option value="display">Display</option>
                        </select>
                    </div>

                    <!-- Stock Level Filter -->
                    <div class="min-w-0 flex-1 lg:min-w-48">
                        <select wire:model.live="stockFilter" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Stock Levels</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>

                    <!-- Item Class Filter -->
                    <div class="min-w-0 flex-1 lg:min-w-48">
                        <select wire:model.live="itemClassFilter" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Classes</option>
                            <option value="consumable">Consumable</option>
                            <option value="accessories">Accessories</option>
                        </select>
        </div>
    </div>
</div>

        <!-- Products Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Product
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Class
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Stock Level
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Pricing
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800">
                        @forelse($supplies as $supply)
                            <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <!-- Product Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <!-- QR Code Placeholder -->
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <div class="h-12 w-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" clip-rule="evenodd"></path>
                                                        </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $supply->supply_description ?: 'N/A N/A N/A' }}
                                    </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ Str::limit($supply->supply_description, 50) }}
                                        </div>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                                    SKU: {{ $supply->supply_sku }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    WAREHOUSE
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Type Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($supply->supply_item_class === 'consumable')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            FOOD
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            NON FOOD
                                        </span>
                                    @endif
                                </td>

                                <!-- Class Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                        {{ ucfirst($supply->supply_item_class) }}
                                    </span>
                                </td>

                                <!-- Stock Level Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ number_format($supply->supply_qty) }} {{ $supply->supply_uom }}
                                        </div>
                                        @if($supply->supply_qty > 0)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                ~{{ number_format($supply->supply_qty * 12) }} pcs
                                            </div>
                                        @endif
                                        <div class="flex items-center mt-1">
                                            @if($supply->supply_qty <= 0)
                                                <div class="flex items-center">
                                                    <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
                                                    <span class="text-xs text-red-600 dark:text-red-400">Out of Stock</span>
                                                </div>
                                            @elseif($supply->supply_qty <= ($supply->supply_qty * $supply->low_stock_threshold_percentage / 100))
                                                <div class="flex items-center">
                                                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></div>
                                                    <span class="text-xs text-yellow-600 dark:text-yellow-400">Low Stock</span>
                                                </div>
                                            @else
                                                <div class="flex items-center">
                                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                                                    <span class="text-xs text-green-600 dark:text-green-400">In Stock</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Threshold: {{ $supply->low_stock_threshold_percentage }}%
                                        </div>
                                    </div>
                                </td>

                                <!-- Pricing Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                ₱{{ number_format($supply->supply_price1, 2) }}
                                            </div>
                                    @if($supply->unit_cost > 0)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Cost: ₱{{ number_format($supply->unit_cost, 2) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Actions Column -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <!-- Batches Button -->
                                        <button class="inline-flex items-center px-3 py-1 border border-blue-300 text-blue-700 text-xs font-medium rounded hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-blue-600 dark:text-blue-400 dark:hover:bg-blue-900">
                                            Batches (0)
                                            </button>
                                        
                                        <!-- Edit Button -->
                                        <button wire:click="edit({{ $supply->id }})" 
                                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            
                                        <!-- Delete Button -->
                                        <button wire:click="delete({{ $supply->id }})" 
                                                wire:confirm="Are you sure you want to delete this product?"
                                                class="text-gray-400 hover:text-red-600 dark:hover:text-red-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No products found</h3>
                                        <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by creating your first product.</p>
                                        <a href="{{ route('supplies.inventory.create') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Create Product Profile
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($supplies->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-sm text-gray-700 dark:text-gray-300 mr-2">Per Page:</span>
                            <select wire:model.live="perPage" 
                                    class="px-2 py-1 text-sm border border-gray-300 rounded-md bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                                        <div>
                            {{ $supplies->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if (session()->has('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
        {{ session('success') }}
    </div>
@endif

@if (session()->has('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
        {{ session('error') }}
    </div>
@endif