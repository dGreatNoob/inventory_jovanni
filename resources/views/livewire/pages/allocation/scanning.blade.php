<x-slot:header>Allocation</x-slot:header>
<x-slot:subheader>Packing / Scan</x-slot:subheader>

<div class="py-4 sm:py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="mb-6 flex flex-col gap-3">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <a href="{{ route('allocation.warehouse') }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200" wire:navigate>
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full border border-gray-300 dark:border-zinc-600 text-gray-500 dark:text-gray-300 bg-white dark:bg-zinc-800">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M12.5 15L7.5 10L12.5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="font-medium">Back to Allocation</span>
                </a>
                <span class="text-gray-400 dark:text-gray-600">/</span>
                <span>Packing / Scan</span>
            </div>
        </div>

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

        <!-- Selection: Branch → Box -->
        <div class="space-y-6 mb-8">
            <div class="p-4 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Branch & Box</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Branch: searchable dropdown --}}
                    <div class="relative" x-data x-on:click.outside="$wire.set('showBranchDropdown', false)">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                        <div class="relative">
                            <button
                                type="button"
                                wire:click="$toggle('showBranchDropdown')"
                                class="inline-flex w-full min-h-[38px] h-10 items-center justify-between gap-2 rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-left text-sm text-gray-900 dark:text-white shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <span class="flex-1 min-w-0 truncate">
                                    @if($this->selectedBranchName)
                                        <span class="font-medium">{{ $this->selectedBranchName }}</span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">Search and select a branch…</span>
                                    @endif
                                </span>
                                <span class="flex items-center gap-2 shrink-0 text-gray-400 dark:text-gray-300">
                                    <svg aria-hidden="true" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                    <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 20 20">
                                        <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </button>

                            @if($showBranchDropdown)
                                <div class="absolute z-30 mt-2 w-full rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-lg">
                                    <div class="px-3 pt-3 pb-2 border-b border-gray-100 dark:border-zinc-700">
                                        <div class="relative">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <svg aria-hidden="true" class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <input
                                                type="text"
                                                wire:model.debounce.300ms="branchSearch"
                                                wire:click.stop
                                                class="block w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 pl-9 pr-3 py-1.5 text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-indigo-500 focus:border-indigo-500"
                                                placeholder="Search by branch name or code…"
                                            />
                                        </div>
                                    </div>

                                    <div class="max-h-72 overflow-y-auto py-1 text-sm">
                                        @forelse($this->filteredBranches as $branch)
                                            <button
                                                type="button"
                                                wire:click="selectBranch({{ $branch->id }})"
                                                class="flex w-full items-center gap-2 px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-zinc-700 {{ $selectedBranchId === $branch->id ? 'bg-indigo-50 dark:bg-indigo-900/30' : '' }}"
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
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Box: select with uniform height --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Box</label>
                        <div class="flex gap-2">
                            <select wire:model.live="selectedBoxId"
                                class="block flex-1 min-h-[38px] h-10 rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                @if(!$selectedBranchId) disabled @endif>
                                <option value="">— Select box —</option>
                                @foreach($availableBoxes as $box)
                                    <option value="{{ $box->id }}">{{ $box->box_number }}</option>
                                @endforeach
                            </select>
                            @if($selectedBranchId)
                                <flux:button type="button" wire:click="createNewBox" size="sm" class="!min-h-[38px] !h-10 shrink-0">
                                    New
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($selectedBranchAllocationId)
            @if($currentBox && $currentDr)
                <!-- Scan Product (primary focus when box selected - above Branch summary) -->
                <div class="p-4 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm mb-6" wire:loading.class="opacity-75 pointer-events-none">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Scan Product</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Scan or enter barcode. Product must be allocated to the selected branch.</p>
                    <div class="space-y-2" x-data x-init="$wire.on('refocus-barcode-input', () => $nextTick(() => document.getElementById('scan-barcode-input')?.focus()))">
                        <input id="scan-barcode-input"
                            type="text"
                            wire:model.live="barcodeInput"
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
            @endif

            <!-- Branch Summary (boxes + DRs for this branch) -->
            <div class="p-4 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Branch summary</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    {{ $this->branchBoxesSummary->count() }} box(es) will be shipped to <span class="font-medium text-gray-900 dark:text-white">{{ $this->selectedBranchName }}</span>
                </p>
                <div class="rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                        <thead class="bg-gray-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Box</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">DR number</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Items</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                            @forelse($this->branchBoxesSummary as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700 {{ $currentBox && $row->id === $currentBox->id ? 'bg-indigo-50 dark:bg-indigo-900/30 border-l-4 border-l-indigo-500' : '' }}">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $row->box_number }}</td>
                                    <td class="px-4 py-3 text-sm font-mono text-gray-500 dark:text-gray-400">{{ $row->dr_number }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $row->current_count }}</td>
                                    <td class="px-4 py-3">
                                        @if($row->status === 'full')
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">Full</span>
                                        @elseif($row->status === 'open')
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">Open</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">{{ $row->status }}</span>
                                        @endif
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

            @if(!$currentBox || !$currentDr)
                <!-- Scan Product (when no box selected - prompts to select box) -->
                <div class="p-4 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Scan Product</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Select or create a box above to start scanning.</p>
                    <div class="mt-4 p-4 rounded-lg border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-700" aria-live="polite" role="status">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ready to scan...</p>
                    </div>
                </div>
            @endif

            <!-- Products that can be scanned -->
            <div class="p-4 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Products for this branch</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Products included in the allocation. Scan these barcodes to add items to the box.</p>
                <div class="rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden max-h-64 overflow-y-auto overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                        <thead class="bg-gray-50 dark:bg-zinc-700 sticky top-0">
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

            <!-- Scanned Items (when box selected) -->
            @if($currentBox && $currentDr)
                <div class="p-4 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Scanned Items ({{ $currentBox->box_number }})</h2>
                    <div class="rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                            <thead class="bg-gray-50 dark:bg-zinc-700">
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
            @endif
        @else
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
        @endif
    </div>
</div>
