<x-slot:header>Allocation</x-slot:header>
<x-slot:subheader>Packing / Scan</x-slot:subheader>
<x-slot:headerHref>{{ route('allocation.warehouse') }}</x-slot:headerHref>

<div class="pt-4 sm:pt-6 -mb-6 lg:-mb-8 flex flex-col h-[calc(100vh-12rem)] overflow-hidden">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 w-full flex flex-col flex-1 min-h-0">
        @if (session()->has('message'))
            <div class="shrink-0 mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-2 text-green-700 dark:text-green-300">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="shrink-0 mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-2 text-red-700 dark:text-red-300">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- 2-column layout (always): left = Select Branch + Scan; right = Branch summary + Products or empty state -->
        <div class="flex-1 min-h-0 flex flex-col overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 flex-1 min-h-0 overflow-hidden">
                {{-- Left column: Select Branch + Scan Product --}}
                <div class="flex flex-col gap-4 min-h-0 overflow-hidden">
                    {{-- Select Branch: compact card --}}
                    <div class="w-full p-4 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Select Branch</h2>
                        <div class="relative" x-data x-on:click.outside="$wire.set('showBranchDropdown', false)" x-on:keydown.escape.window="$wire.set('showBranchDropdown', false)">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <svg aria-hidden="true" class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="branchSearch"
                                    wire:focus="$set('showBranchDropdown', true)"
                                    placeholder="Search by branch name or code…"
                                    autocomplete="off"
                                    aria-label="Search and select branch"
                                    class="block w-full min-h-[38px] h-10 pl-10 pr-3 rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />

                                @if($showBranchDropdown)
                                    <div class="absolute z-30 mt-2 w-full rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-lg py-1">
                                        @forelse($this->filteredBranches as $branch)
                                            <button
                                                type="button"
                                                wire:click="selectBranch({{ $branch->id }})"
                                                class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-zinc-700 {{ $selectedBranchId === $branch->id ? 'bg-indigo-50 dark:bg-indigo-900/30' : '' }}"
                                            >
                                                <span class="flex-1 min-w-0 font-medium text-gray-900 dark:text-white truncate">{{ $branch->name }}</span>
                                                @if($branch->code ?? null)
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 shrink-0">{{ $branch->code }}</span>
                                                @endif
                                                @if($selectedBranchId === $branch->id)
                                                    <svg class="h-4 w-4 text-indigo-500 shrink-0" viewBox="0 0 20 20" fill="none">
                                                        <path d="M5 11.5L8.5 15L15 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                @endif
                                            </button>
                                        @empty
                                            <div class="px-3 py-3 text-xs text-gray-500 dark:text-gray-400">
                                                No branches match your search.
                                            </div>
                                        @endforelse
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($selectedBranchAllocationId)
                    {{-- Scan Product (when box selected) or placeholder --}}
                    @if($currentBox && $currentDr)
                        <div class="shrink-0 p-4 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm" wire:loading.class="opacity-75 pointer-events-none">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Scan Product</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Scan or enter barcode. Product must be allocated to the selected branch.</p>
                    <div class="space-y-2" x-data x-init="$wire.on('refocus-barcode-input', () => $nextTick(() => document.getElementById('scan-barcode-input')?.focus()))">
                        <input id="scan-barcode-input"
                            type="text"
                            wire:model.live.debounce.150ms="barcodeInput"
                            wire:keydown.enter.prevent="processBarcodeScanner"
                            wire:keydown.escape="clearBarcodeInput"
                            placeholder="Scan barcode or enter manually..."
                            autofocus
                            autocomplete="off"
                            aria-label="Barcode scanner input"
                            class="block w-full rounded-md border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-base py-2.5" />
                    </div>
                    <div class="mt-4 p-4 rounded-lg border transition-colors duration-150
                        {{ $scanStatus === 'success' ? 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20' : ($scanStatus === 'error' ? 'border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-700') }}"
                        aria-live="polite"
                        role="status">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $scanFeedback ?: 'Ready to scan...' }}</p>
                    </div>
                </div>
                        @else
                            <div class="shrink-0 p-4 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Scan Product</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Select or create a box in Branch summary to start scanning.</p>
                                <div class="mt-4 p-4 rounded-lg border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-700" aria-live="polite" role="status">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ready to scan...</p>
                                </div>
                            </div>
                        @endif
                    {{-- Scanned Items (when box selected) - below Scan Product --}}
                    @if($selectedBranchAllocationId && $currentBox && $currentDr)
                        <div class="flex flex-col shrink-0 overflow-hidden rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm">
                            <div class="shrink-0 p-4 pb-0">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Scanned Items ({{ $currentBox->box_number }})</h2>
                            </div>
                            <div class="overflow-auto p-4 pt-0 max-h-48">
                                <div class="rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                                        <thead class="sticky top-0 z-10 bg-gray-50 dark:bg-zinc-700">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Product</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Barcode</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                                            @forelse($this->scannedItems as $item)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $item->display_name }}</td>
                                                    <td class="px-4 py-3 text-sm font-mono text-gray-500 dark:text-gray-400">{{ $item->display_barcode }}</td>
                                                    <td class="px-4 py-3 text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ $item->scanned_quantity }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No items scanned yet</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif
                    </div>
                    {{-- Right column: Branch summary + Products (when branch) or empty state --}}
                    <div class="flex flex-col gap-4 min-h-0 overflow-hidden">
                    @if($selectedBranchAllocationId)
                        <!-- Branch Summary (50% height, table auto-scroll) -->
                        <div class="flex flex-col flex-1 min-h-0 overflow-hidden rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm">
                            <div class="shrink-0 p-4 pb-0 flex items-start justify-between gap-2">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Branch summary</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        {{ $this->branchBoxesSummary->count() }} box(es) will be shipped to <span class="font-medium text-gray-900 dark:text-white">{{ $this->selectedBranchName }}</span>
                                    </p>
                                </div>
                                <flux:button type="button" wire:click="createNewBox" size="sm" class="shrink-0">New box</flux:button>
                            </div>
                            <div class="flex-1 min-h-0 overflow-auto p-4 pt-0">
                                <div class="rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                                        <thead class="sticky top-0 z-10 bg-gray-50 dark:bg-zinc-700">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Box</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">DR number</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Items</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                                            @forelse($this->branchBoxesSummary as $row)
                                                <tr wire:click="selectBox({{ $row->id }})"
                                                    class="cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700 {{ $currentBox && $row->id === $currentBox->id ? 'bg-indigo-50 dark:bg-indigo-900/30 border-l-4 border-l-indigo-500' : '' }}">
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $row->box_number }}</td>
                                                    <td class="px-4 py-3 text-sm font-mono text-gray-500 dark:text-gray-400">{{ $row->dr_number }}</td>
                                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $row->current_count }}</td>
                                                    <td class="px-4 py-3 text-right">
                                                        <div class="flex items-center justify-end gap-1" wire:click.stop>
                                                            <flux:button
                                                                type="button"
                                                                variant="ghost"
                                                                size="xs"
                                                                icon="document-text"
                                                                wire:click="generateDrForBox({{ $row->id }})"
                                                                :label="__('Generate DR')"
                                                            />
                                                            <flux:button
                                                                type="button"
                                                                variant="ghost"
                                                                size="xs"
                                                                icon="trash"
                                                                wire:click="deleteBox({{ $row->id }})"
                                                                wire:confirm="Delete this box? Scanned items will be available for rescanning."
                                                                class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                                                :label="__('Delete')"
                                                            />
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No boxes yet. Create a box to start scanning.</td>
                                                </tr>
                                            @endforelse
                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Products for this branch (50% height, table auto-scroll) -->
                        <div class="flex flex-col flex-1 min-h-0 overflow-hidden rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm">
                            <div class="shrink-0 p-4 pb-0">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Products for this branch</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Products included in the allocation. Scan these barcodes to add items to the box.</p>
                            </div>
                            <div class="flex-1 min-h-0 overflow-auto p-4 pt-0">
                                <div class="rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                                        <thead class="sticky top-0 z-10 bg-gray-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Barcode</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Allocated</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Scanned</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Remaining</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                            @forelse($this->allocatableProducts as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</td>
                                    <td class="px-4 py-3 text-sm font-mono text-gray-500 dark:text-gray-400">{{ $item->barcode }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $item->allocated }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ $item->scanned }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($item->remaining > 0)
                                            <span class="text-amber-600 dark:text-amber-400">{{ $item->remaining }}</span>
                                        @else
                                            <span class="text-green-600 dark:text-green-400 font-medium">✓</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No products allocated to this branch yet. Add products in <a href="{{ route('allocation.warehouse') }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline" wire:navigate>Create Allocation</a> (Step 2–3) first.
                                    </td>
                                </tr>
                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Empty state when no branch selected --}}
                        <div class="flex-1 min-h-0 flex items-center justify-center overflow-auto">
                            <div class="p-8 sm:p-12 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm text-center">
                                <div class="mx-auto flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-zinc-700 text-gray-400 dark:text-zinc-400 mb-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                </div>
                                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-2">Select a branch to start scanning</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto mb-4">
                                    Choose a branch with an active allocation. Then select or create a box and scan products to add them.
                                </p>
                                <a href="{{ route('allocation.warehouse') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:navigate>
                                    Go to Create Allocation
                                </a>
                            </div>
                        </div>
                    @endif
                    </div>
                </div>
            </div>
    </div>
</div>
