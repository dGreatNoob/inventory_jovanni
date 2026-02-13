<x-slot:header>Allocation</x-slot:header>
<x-slot:subheader>Packing / Scan</x-slot:subheader>
<x-slot:headerHref>{{ route('allocation.warehouse') }}</x-slot:headerHref>

<div class="pb-6">
    <div class="w-full flex flex-col gap-4">
        @if (session()->has('message'))
            <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-2 text-green-700 dark:text-green-300">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-2 text-red-700 dark:text-red-300">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Select Branch: compact card (overflow-visible so dropdown is not clipped by parent) --}}
        <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-visible">
            <div class="px-6 py-4">
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
                                    class="block w-full min-h-[38px] h-10 pl-10 pr-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />

                                @if($showBranchDropdown)
                                    <div class="absolute left-0 right-0 z-50 mt-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-xl py-1 max-h-64 overflow-y-auto">
                                        @forelse($this->filteredBranches as $branch)
                                            <button
                                                type="button"
                                                wire:click="selectBranch({{ $branch->id }})"
                                                class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white {{ $selectedBranchId === $branch->id ? 'bg-blue-50 dark:bg-blue-900/30' : '' }}"
                                            >
                                                <span class="flex-1 min-w-0 font-medium text-gray-900 dark:text-white truncate">{{ $branch->name }}</span>
                                                @if($branch->code ?? null)
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 shrink-0">{{ $branch->code }}</span>
                                                @endif
                                                @if($selectedBranchId === $branch->id)
                                                    <svg class="h-4 w-4 text-blue-500 shrink-0" viewBox="0 0 20 20" fill="none">
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
        </div>

        @if($selectedBranchAllocationId)
            {{-- Branch Summary (boxes table) --}}
            <div class="flex flex-col overflow-hidden bg-white dark:bg-gray-800 shadow-md sm:rounded-lg" style="max-height: 400px;">
                <div class="shrink-0 px-6 py-4 pb-0 flex items-start justify-between gap-2">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Branch summary</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            {{ $this->branchBoxesSummary->count() }} box(es) will be shipped to <span class="font-medium text-gray-900 dark:text-white">{{ $this->selectedBranchName }}</span>
                        </p>
                    </div>
                    <flux:button type="button" wire:click="createNewBox" size="sm" class="shrink-0">New box</flux:button>
                </div>
                <div class="flex-1 min-h-0 overflow-auto px-6 py-4 pt-0">
                    <div class="overflow-x-auto border-t border-gray-200 dark:border-gray-700">
                                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 min-w-full">
                                    <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-10">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Box</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">DR number</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Items</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($this->branchBoxesSummary as $row)
                                            <tr wire:click="selectBox({{ $row->id }})"
                                                class="cursor-pointer odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $currentBox && $row->id === $currentBox->id ? 'bg-blue-50 dark:bg-blue-900/30 border-l-4 border-l-blue-500' : '' }}">
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $row->box_number }}</td>
                                                <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">{{ $row->dr_number }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">{{ $row->current_count }}</td>
                                                <td class="px-6 py-4 text-right">
                                                    <div class="flex items-center justify-end gap-2" wire:click.stop style="min-width: 140px;">
                                                        <button type="button"
                                                            wire:click="generateDrForBox({{ $row->id }})"
                                                            class="px-3 py-1.5 text-xs font-medium text-white rounded bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                                                            Generate DR
                                                        </button>
                                                        <button type="button"
                                                            wire:click="deleteBox({{ $row->id }})"
                                                            wire:confirm="Delete this box? Scanned items will be available for rescanning."
                                                            class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                                            Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No boxes yet. Create a box to start scanning.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

            {{-- Scan Product (when box selected) or placeholder --}}
            @if($currentBox && $currentDr)
                <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-hidden" wire:loading.class="opacity-75 pointer-events-none">
                    <div class="px-6 py-4">
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
                                class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-base py-2.5" />
                        </div>
                        <div class="mt-4 p-4 rounded-lg border transition-colors duration-150
                            {{ $scanStatus === 'success' ? 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20' : ($scanStatus === 'error' ? 'border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700') }}"
                            aria-live="polite"
                            role="status">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $scanFeedback ?: 'Ready to scan...' }}</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Scan Product</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Select or create a box in Branch summary to start scanning.</p>
                        <div class="mt-4 p-4 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700" aria-live="polite" role="status">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ready to scan...</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Scanned Items (when box selected) - below Scan Product --}}
            @if($currentBox && $currentDr)
                <div class="flex flex-col overflow-hidden bg-white dark:bg-gray-800 shadow-md sm:rounded-lg" style="max-height: 350px;">
                    <div class="shrink-0 px-6 py-4 pb-0">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Scanned Items ({{ $currentBox->box_number }})</h2>
                    </div>
                    <div class="flex-1 min-h-0 overflow-auto px-6 py-4 pt-0">
                        <div class="overflow-x-auto border-t border-gray-200 dark:border-gray-700">
                                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 min-w-full">
                                        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-10">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barcode</th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                            @forelse($this->scannedItems as $item)
                                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $item->display_name }}</td>
                                                    <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">{{ $item->display_barcode }}</td>
                                                    <td class="px-6 py-4 text-sm text-right font-semibold text-blue-600 dark:text-blue-400">{{ $item->scanned_quantity }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No items scanned yet</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Products for this branch --}}
                    <div class="flex flex-col overflow-hidden bg-white dark:bg-gray-800 shadow-md sm:rounded-lg shrink-0" style="max-height: 400px;">
                            <div class="shrink-0 px-6 py-4 pb-0">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Products for this branch</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Products included in the allocation. Scan these barcodes to add items to the box.</p>
                            </div>
                            <div class="flex-1 min-h-0 overflow-auto px-6 py-4 pt-0">
                                <div class="overflow-x-auto border-t border-gray-200 dark:border-gray-700">
                                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 min-w-full">
                                        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barcode</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Allocated</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Scanned</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Remaining</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($this->allocatableProducts as $item)
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</td>
                                    <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">{{ $item->barcode }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">{{ $item->allocated }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $item->scanned }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @if($item->remaining > 0)
                                            <span class="text-amber-600 dark:text-amber-400">{{ $item->remaining }}</span>
                                        @else
                                            <span class="text-green-600 dark:text-green-400 font-medium">✓</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No products allocated to this branch yet. Add products in <a href="{{ route('allocation.warehouse') }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline" wire:navigate>Create Allocation</a> (Step 2–3) first.
                                    </td>
                                </tr>
                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
            </div>

            {{-- Ready to dispatch section (shown whenever there are boxes) --}}
            @if($this->branchBoxesSummary->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg">
                    <div class="px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ $this->isFullyScanned ? 'Scanning complete' : 'Ready to dispatch' }}</h3>
                        @if(!$this->isFullyScanned)
                            <p class="text-sm text-amber-700 dark:text-amber-300 mb-3">Some items not yet fully scanned. You can dispatch with current scans or continue scanning.</p>
                        @endif
                        @if($this->summaryDrNumber)
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Summary DR: <span class="font-mono font-medium">{{ $this->summaryDrNumber }}</span></p>
                        @endif
                        <div class="flex flex-wrap items-center gap-3">
                            <button type="button"
                                wire:click="exportDeliverySummary"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                Export Delivery Summary
                            </button>
                            <button type="button"
                                wire:click="dispatchForShipment"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                Dispatch for shipment
                            </button>
                            @if($this->summaryDrId)
                                <a href="{{ route('shipment.index') }}?summary_dr_id={{ $this->summaryDrId }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                   wire:navigate>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" /></svg>
                                    Create shipment
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @else
            {{-- Empty state when no branch selected --}}
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg">
                <div class="px-6 py-8 text-center">
                    <div class="mx-auto flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-2">Select a branch to start scanning</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto mb-4">
                        Choose a branch with an active allocation. Then select or create a box and scan products to add them.
                    </p>
                    <a href="{{ route('allocation.warehouse') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" wire:navigate>
                        Go to Create Allocation
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Dispatch Date Modal --}}
    @if($showDispatchModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="cancelDispatch"></div>

                {{-- Center modal --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Set Dispatch Date & Time
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Enter the date and time when this shipment will be dispatched.
                                    </p>

                                    @if($this->summaryDrNumber)
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                                <span class="font-medium">Summary DR:</span>
                                                <span class="font-mono">{{ $this->summaryDrNumber }}</span>
                                            </p>
                                        </div>
                                    @endif

                                    <div>
                                        <label for="dispatchDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Dispatch Date
                                        </label>
                                        <input type="date"
                                            id="dispatchDate"
                                            wire:model="dispatchDate"
                                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2.5"
                                            required>
                                        @error('dispatchDate')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="dispatchTime" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Dispatch Time
                                        </label>
                                        <input type="time"
                                            id="dispatchTime"
                                            wire:model="dispatchTime"
                                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2.5"
                                            required>
                                        @error('dispatchTime')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                        <button type="button"
                            wire:click="confirmDispatch"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirm Dispatch
                        </button>
                        <button type="button"
                            wire:click="cancelDispatch"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    Livewire.on('download-delivery-receipt', (event) => {
        const payload = event?.detail ?? event;
        const url = payload?.url;
        if (url) window.open(url, '_blank');
    });
</script>
@endscript
