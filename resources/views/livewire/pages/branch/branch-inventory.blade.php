<x-slot:header>Branch Management</x-slot:header>
<x-slot:subheader>Inventory</x-slot:subheader>

<div class="pt-4">
    <div class="space-y-6">
        @if(session()->has('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-3 text-sm text-green-800 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        <!-- Search and Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                    <div class="flex-1 max-w-lg">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input wire:model.live.debounce.300ms="search"
                                   type="text"
                                   placeholder="Search branches..."
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm">
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <button wire:click="toggleFilters"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                            </svg>
                            Filters
                        </button>
                    </div>
                </div>

                @if($showFilters)
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Batch</label>
                                <select wire:model.live="batchFilter"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm">
                                    <option value="">All Batches</option>
                                    @foreach($this->allBatchesForFilter as $batch)
                                        <option value="{{ $batch['name'] }}">
                                            {{ $batch['name'] }}{{ ($batch['has_completed_shipments'] ?? true) ? '' : ' — no completed shipments' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end pb-2">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox"
                                           wire:model.live="completedShipmentOnly"
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Completed Shipment</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Per Page</label>
                                <select wire:model.live="perPage"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button wire:click="clearFilters"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-gray-400">
                                Clear Filters
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Branches List -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-3 text-sm text-gray-700 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                @if($items->total() > 0)
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $items->total() }}</span> branches found
                @else
                    <span class="font-semibold text-gray-900 dark:text-white">No branches found</span>
                @endif
            </div>

            <div class="overflow-hidden">
                @if($items->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Branch</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Batch</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Address</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Completed Shipments</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Last Shipment</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($items as $branch)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $branch['name'] }}
                                            </div>
                                            @if($branch['code'] ?? null)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $branch['code'] }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $branch['batch'] ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $branch['address'] ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                {{ $branch['completed_shipments_count'] }} shipments
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $branch['last_shipment_date'] ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('branch.inventory.products', $branch['id']) }}" wire:navigate>
                                                <flux:button variant="outline" size="sm">View Products</flux:button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $items->links('livewire::tailwind', ['scrollTo' => false]) }}
                    </div>
                @else
                    <div class="mx-6 mb-6 py-12 text-center rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                        @if(empty($batches) && empty($batchesWithoutCompletedShipments))
                            <div class="bg-gray-100 dark:bg-gray-800/50 w-32 h-32 rounded-2xl flex items-center justify-center mx-auto mb-8">
                                <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <h4 class="text-xl font-medium text-gray-900 dark:text-white mb-3">No Batches with Completed Shipments</h4>
                            <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-8 leading-relaxed">
                                To see branch inventory here, first assign batches to branches in Branch Management, then create allocations and complete shipments.
                            </p>
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                                <flux:button href="{{ route('branch.profile') }}" wire:navigate>
                                    Assign batches to branches
                                </flux:button>
                                <a href="{{ route('allocation.warehouse') }}" wire:navigate
                                    class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                    Go to Allocation Management
                                </a>
                            </div>
                        @else
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No branches found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Try adjusting your filters or batch selection
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
