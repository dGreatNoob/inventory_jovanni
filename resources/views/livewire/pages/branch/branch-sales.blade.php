<x-slot:header>Branch Management</x-slot:header>
<x-slot:subheader>Sales</x-slot:subheader>

<div class="pt-4">
    <div class="space-y-6">
        @if($showCreateStepper)
            <!-- Create Branch Sales Stepper -->
            @include('livewire.pages.branch.partials.branch-sales-create-stepper')
        @else
        <!-- Branch Sales Section -->
        <section class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center gap-2">
                        <flux:icon name="banknotes" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                        Branch Sales
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        View and manage customer sales records across all branches
                    </p>
                </div>
                <flux:button wire:click="openCreateStepper" size="sm" class="flex items-center gap-2">
                    <!-- <flux:icon name="plus" class="w-4 h-4" /> -->
                    Add Customer Sales
                </flux:button>
            </div>

            @if(session()->has('success'))
                <div class="mx-6 mt-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-3 text-sm text-green-800 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Search
                    </label>
                    <input
                        type="text"
                        id="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by ref no, branch, or agent..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                </div>

                <!-- Date From -->
                <div>
                    <label for="dateFrom" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Date From
                    </label>
                    <input
                        type="date"
                        id="dateFrom"
                        wire:model.live="dateFrom"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                </div>

                <!-- Date To -->
                <div>
                    <label for="dateTo" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Date To
                    </label>
                    <input
                        type="date"
                        id="dateTo"
                        wire:model.live="dateTo"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                </div>

                <!-- Branch Filter -->
                <div>
                    <label for="branchFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Branch
                    </label>
                    <select
                        id="branchFilter"
                        wire:model.live="selectedBranchId"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Agent Filter -->
                <div>
                    <label for="agentFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Agent
                    </label>
                    <select
                        id="agentFilter"
                        wire:model.live="selectedAgentId"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">All Agents</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->agent_code }} - {{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <flux:button wire:click="clearFilters" variant="outline" size="sm">
                        Clear Filters
                    </flux:button>
                </div>
            </div>

            <!-- Sales Table -->
            <div class="overflow-hidden">
            @if($sales->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Reference No
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date & Time
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Branch
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Agent
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Selling Area
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Items
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Total Amount
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($sales as $sale)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-mono font-medium text-gray-900 dark:text-white">
                                            {{ $sale->ref_no }}
                                        </div>
                                        @if($sale->status === 'draft')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">Draft</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $sale->created_at->format('M d, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $sale->created_at->format('h:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $sale->branch->name }}
                                        </div>
                                        @if($sale->branch->code)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $sale->branch->code }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($sale->agent)
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $sale->agent->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $sale->agent->agent_code }}
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $sale->selling_area ?: '—' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $sale->items->sum('quantity') }} items
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $sale->items->count() }} products
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            ₱{{ number_format($sale->total_amount, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($sale->status === 'draft')
                                            <button type="button" wire:click="resumeDraft({{ $sale->id }})"
                                                class="px-3 py-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 border border-blue-300 dark:border-blue-600 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 focus:ring-2 focus:ring-blue-500">
                                                Resume
                                            </button>
                                        @else
                                            <flux:button wire:click="viewSaleDetails({{ $sale->id }})" variant="outline" size="sm">
                                                View Details
                                            </flux:button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $sales->links('livewire::tailwind', ['scrollTo' => false]) }}
                </div>
            @else
                <div class="mx-6 mb-6 py-12 text-center rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No sales found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Try adjusting your filters or date range
                    </p>
                </div>
            @endif
            </div>
        </section>
        @endif

        <!-- Success Modal -->
        @if($showSuccessModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-neutral-900/50" wire:click="closeSuccessModal"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Success</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">{{ $successMessage }}</p>
                    <flux:button wire:click="closeSuccessModal">OK</flux:button>
                </div>
            </div>
        @endif

        <!-- Sale Details Modal -->
    @if($showDetailsModal && $selectedSale)
    <div
        x-data="{ open: @entangle('showDetailsModal').live }"
        x-cloak
        x-on:keydown.escape.window="if (open) { open = false; $wire.closeDetailsModal(); }"
    >
        <template x-teleport="body">
            <div
                x-show="open"
                x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center"
            >
                <div
                    x-show="open"
                    x-transition.opacity
                    class="fixed inset-0 bg-neutral-900/50"
                    @click="open = false; $wire.closeDetailsModal()"
                ></div>

                <div
                    x-show="open"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="translate-y-4 opacity-0 scale-95"
                    x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="translate-y-0 opacity-100 scale-100"
                    x-transition:leave-end="translate-y-4 opacity-0 scale-95"
                    class="relative w-full max-w-4xl mx-4 bg-white dark:bg-zinc-800 rounded-lg shadow-xl max-h-[90vh] overflow-hidden flex flex-col"
                >
                    <!-- Modal Header -->
                    <div class="flex items-start justify-between px-6 py-5 border-b border-gray-200 dark:border-zinc-700">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Sale Details
                                </h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Reference: <span class="font-mono font-medium">{{ $selectedSale->ref_no }}</span>
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 dark:text-gray-500 dark:hover:bg-zinc-700 dark:hover:text-gray-200"
                            @click="open = false; $wire.closeDetailsModal()"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="flex-1 overflow-y-auto px-6 py-6">
                        <div class="space-y-6">
                            <!-- Sale Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Sale Information</h3>
                                    <dl class="space-y-2">
                                        <div>
                                            <dt class="text-xs text-gray-500 dark:text-gray-400">Date & Time</dt>
                                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $selectedSale->created_at->format('F d, Y h:i A') }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs text-gray-500 dark:text-gray-400">Branch</dt>
                                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $selectedSale->branch->name }}
                                                @if($selectedSale->branch->code)
                                                    <span class="text-xs text-gray-500">({{ $selectedSale->branch->code }})</span>
                                                @endif
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs text-gray-500 dark:text-gray-400">Selling Area</dt>
                                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $selectedSale->selling_area ?: 'Not specified' }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Agent Information</h3>
                                    @if($selectedSale->agent)
                                        <dl class="space-y-2">
                                            <div>
                                                <dt class="text-xs text-gray-500 dark:text-gray-400">Name</dt>
                                                <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $selectedSale->agent->name }}
                                                </dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs text-gray-500 dark:text-gray-400">Agent Code</dt>
                                                <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $selectedSale->agent->agent_code }}
                                                </dd>
                                            </div>
                                        </dl>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No agent assigned</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Products List -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Products Sold</h3>
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barcode</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit Price</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($selectedSale->items as $item)
                                                <tr>
                                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                        {{ $item->product_name }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">
                                                        {{ $item->barcode ?: '—' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                                        {{ $item->quantity }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                                        ₱{{ number_format($item->unit_price, 2) }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-white">
                                                        ₱{{ number_format($item->total_amount, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <td colspan="4" class="px-4 py-3 text-sm font-medium text-right text-gray-900 dark:text-white">
                                                    Total Amount:
                                                </td>
                                                <td class="px-4 py-3 text-sm font-bold text-right text-green-600 dark:text-green-400">
                                                    ₱{{ number_format($selectedSale->total_amount, 2) }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                                <div class="grid grid-cols-3 gap-4 text-center">
                                    <div>
                                        <div class="text-xs text-green-600 dark:text-green-400 font-medium">Total Items</div>
                                        <div class="text-2xl font-bold text-green-900 dark:text-green-100">
                                            {{ $selectedSale->items->sum('quantity') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-green-600 dark:text-green-400 font-medium">Total Products</div>
                                        <div class="text-2xl font-bold text-green-900 dark:text-green-100">
                                            {{ $selectedSale->items->count() }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-green-600 dark:text-green-400 font-medium">Total Amount</div>
                                        <div class="text-2xl font-bold text-green-900 dark:text-green-100">
                                            ₱{{ number_format($selectedSale->total_amount, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="border-t border-gray-200 dark:border-zinc-700 px-6 py-4">
                        <div class="flex justify-end">
                            <button
                                type="button"
                                @click="open = false; $wire.closeDetailsModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
    @endif
    </div>
</div>
