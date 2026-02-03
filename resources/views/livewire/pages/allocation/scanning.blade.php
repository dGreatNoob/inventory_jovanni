<div class="pt-4">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Packing / Scan</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Verify products for the correct branch. Select branch and box, then scan.</p>
        </div>

        <!-- Selection: Branch → Box -->
        <div class="space-y-6 mb-8">
            <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Context</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                        <select wire:model.live="selectedBranchId"
                            class="block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">— Select branch —</option>
                            @foreach($this->branchesWithAllocations as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Box</label>
                        <div class="flex gap-2">
                            <select wire:model.live="selectedBoxId"
                                class="block flex-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                @if(!$selectedBranchId) disabled @endif>
                                <option value="">— Select box —</option>
                                @foreach($availableBoxes as $box)
                                    <option value="{{ $box->id }}">{{ $box->box_number }}</option>
                                @endforeach
                            </select>
                            @if($selectedBranchId)
                                <button type="button" wire:click="createNewBox"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    New
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($selectedBranchAllocationId)
            <!-- Branch Summary (boxes + DRs for this branch) -->
            <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Branch summary</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    {{ $this->branchBoxesSummary->count() }} box(es) will be shipped to <span class="font-medium text-gray-900 dark:text-white">{{ $this->selectedBranchName }}</span>
                </p>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Box</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">DR number</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Items</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($this->branchBoxesSummary as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
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

            <!-- Barcode Scanner -->
            <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Scan Product</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Scan or enter barcode. Product must be allocated to the selected branch.</p>
                <div class="space-y-2" x-data x-init="$wire.on('refocus-barcode-input', () => $nextTick(() => document.getElementById('scan-barcode-input')?.focus()))">
                    <input id="scan-barcode-input"
                        type="text"
                        wire:model.live="barcodeInput"
                        wire:keydown.enter.prevent="processBarcodeScanner"
                        placeholder="Scan barcode or enter manually..."
                        autofocus
                        class="block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                </div>

                <div class="mt-4 p-4 rounded-lg border {{ str_contains($scanFeedback ?? '', 'COMPLETE') || str_contains($scanFeedback ?? '', '/') ? 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20' : (str_contains($scanFeedback ?? '', 'not') || str_contains($scanFeedback ?? '', 'allocated to') ? 'border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700') }}">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $scanFeedback ?: 'Ready to scan...' }}</p>
                </div>
            </div>

            <!-- Products that can be scanned -->
            <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Products for this branch</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Products included in the allocation. Scan these barcodes to add items to the box.</p>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden max-h-64 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Barcode</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Allocated</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Scanned</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Remaining</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($this->allocatableProducts as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
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
                <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Scanned Items ({{ $currentBox->box_number }})</h2>
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Barcode</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quantity</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @php
                                    $scannedItems = \App\Models\BranchAllocationItem::where('box_id', $currentBox->id)
                                        ->where('scanned_quantity', '>', 0)
                                        ->get();
                                @endphp
                                @forelse($scannedItems as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
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
            <div class="p-6 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-center text-gray-500 dark:text-gray-400">
                Select a branch to start scanning.
            </div>
        @endif
    </div>
</div>
