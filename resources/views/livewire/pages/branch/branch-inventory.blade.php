<x-slot:header>Branch Management</x-slot:header>
<x-slot:subheader>Inventory</x-slot:subheader>
<div class="pt-4">
    <div class="space-y-6">
        @include('livewire.pages.branch.branch-management-tabs')

        <!-- Batch Selection -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
            <h5 class="font-medium mb-3">Branch Inventory Management</h5>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Select a batch to view branches and their completed shipments.
            </p>

            <!-- Batch Selection Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                @foreach($batches as $batch)
                    <button wire:click="selectBatch('{{ $batch['name'] }}')"
                            class="p-6 bg-white dark:bg-gray-800 border-2 {{ $selectedBatch == $batch['name'] ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600' }} rounded-lg hover:border-blue-500 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                                    📦 {{ $batch['name'] }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $batch['branch_count'] }} branches with shipments</p>
                            </div>
                            <div class="bg-blue-100 dark:bg-blue-900/20 p-3 rounded-lg">
                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h10a2 2 0 012 2v2"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">TOTAL SHIPMENTS</span>
                                <span class="bg-green-100 dark:bg-green-900/20 px-2 py-1 rounded-full text-xs font-medium text-green-800 dark:text-green-300">
                                    {{ $batch['total_shipments'] }} shipments
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                @if($batch['last_shipment_date'])
                                    Last shipment: {{ $batch['last_shipment_date'] }}
                                @else
                                    No shipments yet
                                @endif
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>

            @if(empty($batches))
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">📦</div>
                    <h4 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">No Batches with Shipments</h4>
                    <p class="text-gray-600 dark:text-gray-400">No batches have completed shipments yet.</p>
                </div>
            @endif
        </div>

        <!-- Selected Batch - Branch Selection -->
        @if($selectedBatch && !empty($batchBranches))
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 p-6 mb-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            📦 Batch: {{ $selectedBatch }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ count($batchBranches) }} branches with completed shipments
                        </p>
                    </div>

                    <div class="flex space-x-2">
                        <button wire:click="refreshData"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Refresh
                        </button>
                        <button wire:click="clearBatchSelection"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Back to Batches
                        </button>
                    </div>
                </div>

                <!-- Branches in Selected Batch -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    @foreach($batchBranches as $branch)
                        <button wire:click="selectBranch({{ $branch['id'] }})"
                                class="p-6 bg-gray-50 dark:bg-gray-700 border-2 {{ $selectedBranchId == $branch['id'] ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600' }} rounded-lg hover:border-green-500 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                                        📍 {{ $branch['name'] }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $branch['address'] ?? 'No address' }}</p>
                                </div>
                                <div class="bg-green-100 dark:bg-green-900/20 p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">COMPLETED SHIPMENTS</span>
                                    <span class="bg-green-100 dark:bg-green-900/20 px-2 py-1 rounded-full text-xs font-medium text-green-800 dark:text-green-300">
                                        {{ $branch['completed_shipments_count'] }} shipments
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    @if($branch['last_shipment_date'])
                                        Last shipment: {{ $branch['last_shipment_date'] }}
                                    @else
                                        No shipments yet
                                    @endif
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Selected Branch Products -->
        @if($selectedBranchId && !empty($branchProducts))
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            📍 {{ collect($batchBranches)->firstWhere('id', $selectedBranchId)['name'] ?? 'Branch' }} - Branch Products
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ count($branchProducts) }} products from completed shipments
                        </p>
                    </div>

                    <div class="flex space-x-2">
                        <button wire:click="openUploadModal"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            📄 Upload Text File
                        </button>
                        <button wire:click="clearBranchSelection"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Back to Branches
                        </button>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                Search Shipments
                            </label>
                            <input type="text" id="search" wire:model.live.debounce.300ms="search"
                                placeholder="Shipment number..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        </div>

                        <div>
                            <label for="dateFrom" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                Date From
                            </label>
                            <input type="date" id="dateFrom" wire:model.live="dateFrom"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        </div>

                        <div>
                            <label for="dateTo" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                Date To
                            </label>
                            <input type="date" id="dateTo" wire:model.live="dateTo"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        </div>

                        <div class="flex items-end">
                            <button wire:click="clearFilters"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Header -->
                @if(!empty($branchProducts))
                    <div class="mt-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Branch Summary</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ count($branchProducts) }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Products</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $branchProducts->sum('total_quantity') }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Quantity</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                    {{ $branchProducts->sum('total_sold') }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Sold</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                    ₱{{ number_format($branchProducts->sum('total_value'), 2) }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Value</div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Products List -->
                <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Image</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Barcode</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">SKU</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total Sold</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Remaining</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Unit Price</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total Value</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($branchProducts as $product)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3 text-sm">
                                            @if(isset($product['image_url']))
                                                <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}" class="w-12 h-12 object-cover rounded">
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">No image</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $product['name'] }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-mono text-gray-500 dark:text-gray-400">
                                            {{ $product['barcode'] ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-mono text-gray-500 dark:text-gray-400">
                                            {{ $product['sku'] }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white text-center">
                                            {{ $product['total_quantity'] }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white text-center">
                                            {{ $product['total_sold'] }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white text-center">
                                            {{ $product['remaining_quantity'] }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                            ₱{{ number_format($product['unit_price'], 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                                            ₱{{ number_format($product['total_value'], 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <button wire:click="viewProductDetails({{ $product['id'] }})"
                                                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Product View Modal -->
    @if($showProductViewModal && $selectedProductDetails)
        <div class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-4xl max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Product Details: {{ $selectedProductDetails['name'] }}</h3>
                        <button wire:click="closeProductViewModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Product Summary -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Barcode</div>
                                <div class="font-mono text-gray-900 dark:text-white">{{ $selectedProductDetails['barcode'] ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">SKU</div>
                                <div class="font-mono text-gray-900 dark:text-white">{{ $selectedProductDetails['sku'] }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Quantity</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $selectedProductDetails['total_quantity'] }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Sold</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $selectedProductDetails['total_sold'] }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- History -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Upload History</h4>
                        <div class="space-y-2">
                            @php
                                $histories = \Spatie\Activitylog\Models\Activity::where('log_name', 'branch_inventory')
                                    ->whereJsonContains('properties->barcode', $selectedProductDetails['barcode'])
                                    ->whereJsonContains('properties->branch_id', $selectedBranchId)
                                    ->orderBy('created_at', 'desc')
                                    ->limit(10)
                                    ->get();
                            @endphp
                            @forelse($histories as $history)
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                                    <div class="text-sm text-blue-900 dark:text-blue-100">
                                        {{ $history->description }}
                                    </div>
                                    <div class="text-xs text-blue-600 dark:text-blue-300 mt-1">
                                        {{ $history->created_at->format('M d, Y H:i') }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-sm text-gray-500 dark:text-gray-400">No upload history found.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Shipments List -->
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Shipments Containing This Product</h4>
                        @foreach($selectedProductDetails['shipments'] as $shipment)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-green-100 dark:bg-green-900/20 p-2 rounded-lg">
                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h5 class="font-semibold text-gray-900 dark:text-white">
                                                {{ $shipment['shipping_plan_num'] }}
                                            </h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Shipped: {{ $shipment['shipment_date'] }} • {{ $shipment['carrier_name'] }} • {{ $shipment['delivery_method'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                            ₱{{ number_format($shipment['total'], 2) }}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $shipment['allocated_quantity'] }} allocated • {{ $shipment['sold_quantity'] }} sold
                                        </div>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    Allocation: {{ $shipment['allocation_reference'] }} • Barcode: {{ $shipment['barcode'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end mt-6">
                        <button wire:click="closeProductViewModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Upload Text File Modal -->
    @if($showUploadModal)
        <div class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upload Text File</h3>
                    <button wire:click="closeUploadModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Upload a text file containing barcodes (one barcode per line). The system will compare these barcodes with products in the current branch and update the Quantity Sold column.
                    </p>
                </div>

                <div class="mb-4">
                    <label for="textFile" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Choose Text File (.txt)
                    </label>
                    <input type="file" id="textFile" wire:model="textFile" accept=".txt"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    @error('textFile')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="closeUploadModal" type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Cancel
                    </button>
                    <button wire:click="processTextFile" type="button"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Process File
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Results Slider -->
    @if($showResultsModal)
        <div class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-opacity-50" wire:click="closeResultsModal"></div>
            <div class="absolute right-0 top-0 h-full w-full max-w-2xl bg-white dark:bg-gray-800 shadow-xl transform transition-transform duration-300 {{ $showResultsModal ? 'translate-x-0' : 'translate-x-full' }}">
                <div class="p-6 h-full overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Barcode Comparison Results</h3>
                        <button wire:click="closeResultsModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mb-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $uploadedBarcodeCount }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Barcodes Uploaded</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $matchedBarcodeCount }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Matching Products Found</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $uploadedBarcodeCount - $matchedBarcodeCount }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Unmatched Barcodes</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                    {{ array_sum(array_column($barcodeMatches, 'quantity_sold')) }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Quantity Sold</div>
                            </div>
                        </div>
                    </div>

                    @if(!empty($validBarcodes))
                        <div class="mb-6">
                            <h4 class="font-medium text-green-600 dark:text-green-400 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Will Be Saved ({{ count($validBarcodes) }}):
                            </h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Barcode</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product Name</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">SKU</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quantity Sold</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Available Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($validBarcodes as $barcode => $result)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">{{ $barcode }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $result['product_name'] }}</td>
                                                <td class="px-4 py-3 text-sm font-mono text-gray-500 dark:text-gray-400">{{ $result['sku'] }}</td>
                                                <td class="px-4 py-3 text-sm font-semibold text-green-600 dark:text-green-400 text-center">{{ $result['quantity_sold'] }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-center">{{ $result['available_quantity'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if(!empty($invalidBarcodes))
                        <div class="mb-6">
                            <h4 class="font-medium text-red-600 dark:text-red-400 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Will Be Skipped ({{ count($invalidBarcodes) }}):
                            </h4>
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-red-200 dark:divide-red-700">
                                        <thead class="bg-red-100 dark:bg-red-900/40">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Barcode</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Product Name</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">SKU</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Attempted Qty</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Remaining</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Already Sold</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-red-50 dark:bg-red-900/20 divide-y divide-red-200 dark:divide-red-700">
                                            @foreach($invalidBarcodes as $barcode => $result)
                                                <tr class="hover:bg-red-100 dark:hover:bg-red-900/30">
                                                    <td class="px-4 py-3 text-sm font-mono text-red-900 dark:text-red-100">{{ $barcode }}</td>
                                                    <td class="px-4 py-3 text-sm text-red-900 dark:text-red-100">{{ $result['product_name'] }}</td>
                                                    <td class="px-4 py-3 text-sm font-mono text-red-700 dark:text-red-300">{{ $result['sku'] }}</td>
                                                    <td class="px-4 py-3 text-sm font-semibold text-red-600 dark:text-red-400 text-center">{{ $result['quantity_sold'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-red-900 dark:text-red-100 text-center">{{ $result['available_quantity'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-red-900 dark:text-red-100 text-center">{{ $result['already_sold'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!empty($unmatchedBarcodes))
                        <div class="mb-6">
                            <h4 class="font-medium text-red-600 dark:text-red-400 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Unmatched Barcodes (Errors):
                            </h4>
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-red-200 dark:divide-red-700">
                                        <thead class="bg-red-100 dark:bg-red-900/40">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Barcode</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Count</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-red-50 dark:bg-red-900/20 divide-y divide-red-200 dark:divide-red-700">
                                            @foreach($unmatchedBarcodes as $barcode => $count)
                                                <tr class="hover:bg-red-100 dark:hover:bg-red-900/30">
                                                    <td class="px-4 py-3 text-sm font-mono text-red-900 dark:text-red-100">{{ $barcode }}</td>
                                                    <td class="px-4 py-3 text-sm font-semibold text-red-600 dark:text-red-400 text-center">{{ $count }}</td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                                            ✗ Not Found
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!empty($similarBarcodes))
                        <div class="mb-6">
                            <h4 class="font-medium text-yellow-600 dark:text-yellow-400 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Similar Barcodes:
                            </h4>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-yellow-200 dark:divide-yellow-700">
                                        <thead class="bg-yellow-100 dark:bg-yellow-900/40">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Uploaded Barcode</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Existing Barcode</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Product Name</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">SKU</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Quantity Sold</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-yellow-50 dark:bg-yellow-900/20 divide-y divide-yellow-200 dark:divide-yellow-700">
                                            @foreach($similarBarcodes as $item)
                                                <tr class="hover:bg-yellow-100 dark:hover:bg-yellow-900/30">
                                                    <td class="px-4 py-3 text-sm font-mono text-yellow-900 dark:text-yellow-100">{{ $item['uploaded_barcode'] }}</td>
                                                    <td class="px-4 py-3 text-sm font-mono text-yellow-900 dark:text-yellow-100">{{ $item['existing_barcode'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-yellow-900 dark:text-yellow-100">{{ $item['product_name'] }}</td>
                                                    <td class="px-4 py-3 text-sm font-mono text-yellow-700 dark:text-yellow-300">{{ $item['sku'] }}</td>
                                                    <td class="px-4 py-3 text-sm font-semibold text-yellow-600 dark:text-yellow-400 text-center">{{ $item['quantity_sold'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <button wire:click="syncSimilarBarcodes"
                                            class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                                        Sync Similar Barcodes
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end space-x-3">
                        <button wire:click="closeResultsModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Cancel
                        </button>
                        <button wire:click="saveMatchedBarcodesToDatabase"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            Update Quantity Sold
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Modal -->
    @if($showSuccessModal)
        <div class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Success</h3>
                    <button wire:click="closeSuccessModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $successMessage }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button wire:click="closeSuccessModal"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        OK
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
</div>