<x-slot:header>Branch Management</x-slot:header>
<x-slot:subheader>Inventory</x-slot:subheader>

<div class="pt-4">
    <div class="space-y-6">
        <!-- Branch Inventory Section -->
        <section class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <!-- Filters Section (collapsible) - same pattern as branch-sales -->
            <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30" x-data="{ filtersExpanded: true }">
                <button type="button" @click="filtersExpanded = !filtersExpanded"
                    class="w-full px-6 py-4 flex items-center justify-between gap-2 text-left hover:bg-gray-100/50 dark:hover:bg-gray-800/30 transition-colors">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Filters</span>
                    <svg class="w-5 h-5 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': filtersExpanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="filtersExpanded" x-collapse class="overflow-hidden">
                    <div class="px-6 pb-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div class="lg:col-span-2">
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                    Search
                                </label>
                                <input
                                    type="text"
                                    id="search"
                                    wire:model.live.debounce.300ms="search"
                                    placeholder="Search by branch name or code..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                            </div>

                            <!-- Batch Filter -->
                            <div>
                                <label for="batchFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                    Batch
                                </label>
                                <select
                                    id="batchFilter"
                                    wire:model.live="batchFilter"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                                    <option value="">All Batches</option>
                                    @foreach($batches as $batch)
                                        <option value="{{ $batch['name'] }}">{{ $batch['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Per Page -->
                            <div class="flex items-end">
                                <div class="flex items-center gap-3">
                                    <label for="perPage" class="text-sm font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        Per Page
                                    </label>
                                    <select id="perPage" wire:model.live="perPage"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-24 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <flux:button wire:click="clearFilters" variant="outline" size="sm">
                                Clear Filters
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>

            @if(session()->has('success'))
                <div class="mx-6 mt-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-3 text-sm text-green-800 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4 bg-white dark:bg-gray-800">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center gap-2">
                    <flux:icon name="building-storefront" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    Branch Inventory
                </h3>
            </div>

            <!-- Results Info -->
            <div class="px-6 py-3 text-sm text-gray-700 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50">
                @if($items->total() > 0)
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $items->total() }}</span> branches found
                @else
                    <span class="font-semibold text-gray-900 dark:text-white">No branches found</span>
                @endif
            </div>

            <!-- Branches Table -->
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
                        @if(empty($batches))
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

            @if(!empty($batchesWithoutCompletedShipments))
                <div class="mt-8 px-6 pb-6 border-t border-gray-200 dark:border-gray-700 pt-6" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                        class="flex items-center justify-between w-full text-left text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-inset rounded">
                        <span>Batches without completed shipments ({{ count($batchesWithoutCompletedShipments) }})</span>
                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-4">
                        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Batch</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Branches</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($batchesWithoutCompletedShipments as $excluded)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $excluded['name'] }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $excluded['branch_count'] }} {{ Str::plural('branch', $excluded['branch_count']) }}</td>
                                            <td class="px-6 py-4">
                                                @if($excluded['status'] === 'no_allocations')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">No allocations</span>
                                                @elseif($excluded['status'] === 'no_shipments')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">No shipments</span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">Pending shipments</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            Assign batches in <a href="{{ route('branch.profile') }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline" wire:navigate>Branch Management</a>.
                            Create allocations in <a href="{{ route('allocation.warehouse') }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline" wire:navigate>Allocation Management</a>.
                        </p>
                    </div>
                </div>
            @endif
        </section>
    </div>
</div>
