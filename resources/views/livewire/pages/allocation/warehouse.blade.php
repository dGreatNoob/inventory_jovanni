<x-slot:header>Allocation Management</x-slot:header>
<x-slot:subheader>Create Allocation</x-slot:subheader>

<div class="pb-6">
        <!-- StePPER WORKFLOW (Inline, not modal) -->
        @if ($showStepper)
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg mb-6">
                <!-- Stepper Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">New Allocation</h3>
                        <button wire:click="closeStepper"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Stepper Navigation -->
                <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                    <nav aria-label="Progress">
                        <ol class="flex items-center w-full space-x-4">
                            <li
                                class="flex flex-col w-full items-center flex-1">
                                <div class="flex w-full items-center after:content-[''] after:w-full after:h-1 {{ $currentStep > 1 ? 'after:border-gray-300' : 'after:border-gray-200 dark:after:border-gray-600' }} after:border-4 after:inline-block after:ms-4 after:rounded-full">
                                    <span
                                        class="flex items-center justify-center w-10 h-10 {{ $currentStep > 1 ? 'bg-gray-100' : ($currentStep == 1 ? 'bg-gray-100' : 'bg-gray-100 dark:bg-gray-700') }} rounded-full lg:h-12 lg:w-12 shrink-0">
                                        @if ($currentStep > 1)
                                            <svg class="w-5 h-5 text-gray-600" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 {{ $currentStep == 1 ? 'text-gray-600' : 'text-gray-400' }}"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                                height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        @endif
                                    </span>
                                </div>
                                <span class="mt-2 text-xs font-medium {{ $currentStep >= 1 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500' }}">Batch</span>
                            </li>
                            <li
                                class="flex flex-col w-full items-center flex-1">
                                <div class="flex w-full items-center after:content-[''] after:w-full after:h-1 {{ $currentStep > 2 ? 'after:border-gray-300' : 'after:border-gray-200 dark:after:border-gray-600' }} after:border-4 after:inline-block after:ms-4 after:rounded-full">
                                    <span
                                        class="flex items-center justify-center w-10 h-10 {{ $currentStep > 2 ? 'bg-gray-100' : ($currentStep == 2 ? 'bg-gray-100' : 'bg-gray-100 dark:bg-gray-700') }} rounded-full lg:h-12 lg:w-12 shrink-0">
                                        <svg class="w-5 h-5 {{ $currentStep > 2 ? 'text-gray-600' : ($currentStep == 2 ? 'text-gray-600' : 'text-gray-400') }}"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 9h3m-3 3h3m-3 3h3m-6 1c-.306-.613-.933-1-1.618-1H7.618c-.685 0-1.312.387-1.618 1M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm7 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z" />
                                        </svg>
                                    </span>
                                </div>
                                <span class="mt-2 text-xs font-medium {{ $currentStep >= 2 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500' }}">Branches</span>
                            </li>
                            <li
                                class="flex flex-col w-full items-center flex-1">
                                <div class="flex w-full items-center after:content-[''] after:w-full after:h-1 {{ $currentStep > 3 ? 'after:border-gray-300' : 'after:border-gray-200 dark:after:border-gray-600' }} after:border-4 after:inline-block after:ms-4 after:rounded-full">
                                    <span
                                        class="flex items-center justify-center w-10 h-10 {{ $currentStep > 3 ? 'bg-gray-100' : ($currentStep == 3 ? 'bg-gray-100' : 'bg-gray-100 dark:bg-gray-700') }} rounded-full lg:h-12 lg:w-12 shrink-0">
                                        <svg class="w-5 h-5 {{ $currentStep > 3 ? 'text-gray-600' : ($currentStep == 3 ? 'text-gray-600' : 'text-gray-400') }}"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M20 7h-3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h3m-5 4H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2Z" />
                                        </svg>
                                    </span>
                                </div>
                                <span class="mt-2 text-xs font-medium {{ $currentStep >= 3 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500' }}">Products</span>
                            </li>
                            <li class="flex flex-col w-full items-center flex-1">
                                <div class="flex w-full items-center">
                                    <span
                                        class="flex items-center justify-center w-10 h-10 {{ $currentStep == 4 ? 'bg-gray-100' : 'bg-gray-100 dark:bg-gray-700' }} rounded-full lg:h-12 lg:w-12 shrink-0">
                                        <svg class="w-5 h-5 {{ $currentStep == 4 ? 'text-gray-600' : 'text-gray-400' }}"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8.5 8.5v.01M8.5 12v.01M8.5 15.5v.01M12 8.5v.01M12 12v.01M12 15.5v.01M15.5 8.5v.01M15.5 12v.01M15.5 15.5v.01" />
                                        </svg>
                                    </span>
                                </div>
                                <span class="mt-2 text-xs font-medium {{ $currentStep >= 4 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500' }}">Review</span>
                            </li>
                        </ol>
                    </nav>
                </div>

                <!-- Step Content -->
                <div class="p-6">
                    <!-- STEP 1: CREATE BATCH -->
                    @if ($currentStep === 1)
                        <div>
                            <h4 class="text-md font-medium mb-4">
                                @if ($isEditing)
                                    Step 1: Edit Batch Details
                                @else
                                    Step 1: Create New Batch
                                @endif
                            </h4>
                            <form wire:submit="createBatch" class="space-y-5">
                                <!-- Primary: Batch selection (full width) -->
                                <div>
                                    <label for="batch_number" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                        Batch Numbers *
                                    </label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                        Select which batches to include. Branches from these batches will be added to the allocation.
                                    </p>
                                    @if (count($availableBatchNumbers) > 3)
                                        <div class="flex gap-2 mb-2">
                                            <button type="button" wire:click="selectAllBatches"
                                                class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                                Select All
                                            </button>
                                            <span class="text-gray-400 dark:text-gray-500">|</span>
                                            <button type="button" wire:click="clearBatchSelection"
                                                class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                                Clear
                                            </button>
                                        </div>
                                    @endif
                                    <div class="border border-gray-300 rounded-md shadow-sm p-2 max-h-48 overflow-y-auto dark:bg-gray-600 dark:border-gray-500">
                                        @forelse ($availableBatchNumbers as $batchNum)
                                            <label class="flex items-center p-2 hover:bg-gray-100 dark:hover:bg-gray-500 cursor-pointer rounded">
                                                <input type="checkbox" wire:model.live="selectedBatchNumbers"
                                                    value="{{ $batchNum }}"
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ $batchNum }}</span>
                                            </label>
                                        @empty
                                            <div class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                                No batches found. Ensure branches have batch numbers assigned in <a href="{{ route('branch.profile') }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline" wire:navigate>Branch Management</a>.
                                            </div>
                                        @endforelse
                                    </div>
                                    @error('selectedBatchNumbers')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Optional: Link to Purchase Order (searchable, prefix-segment) -->
                                <div
                                    x-data="{
                                        open: false,
                                        search: '',
                                        selectedId: @entangle('batchPurchaseOrderId').live,
                                        options: [{ id: null, po_num: '', label: 'No PO linked' }, ...@js($this->availablePurchaseOrders->map(fn($po) => [
                                            'id' => $po->id,
                                            'po_num' => $po->po_num,
                                            'label' => $po->po_num . ' — ' . ($po->supplier?->name ?? 'No supplier') . ($po->expected_delivery_date ? ' (' . $po->expected_delivery_date->format('M d, Y') . ')' : '')
                                        ])->values()->toArray())],
                                        matchesSegmentPrefix(query, value) {
                                            const qSeg = (query || '').toLowerCase().split(/[\s\-_]+/).filter(Boolean);
                                            const vSeg = (value || '').toLowerCase().split(/[\s\-_]+/).filter(Boolean);
                                            if (qSeg.length === 0) return true;
                                            if (qSeg.length > vSeg.length) return false;
                                            return qSeg.every((q, i) => (vSeg[i] || '').startsWith(q));
                                        },
                                        get filtered() {
                                            if (!this.search.trim()) return this.options;
                                            return this.options.filter(o => o.po_num === '' || this.matchesSegmentPrefix(this.search, o.po_num));
                                        },
                                        selectedLabel() {
                                            const found = this.options.find(o => o.id === this.selectedId);
                                            return found ? found.label : '';
                                        },
                                        select(option) {
                                            this.selectedId = option.id;
                                            this.search = '';
                                            this.open = false;
                                            $wire.set('batchPurchaseOrderId', option.id);
                                        }
                                    }"
                                    @click.away="open = false"
                                >
                                    <label for="batch_purchase_order" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                        Purchase Order (Optional)
                                    </label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                        Link this allocation to a PO to filter products and show expected quantities.
                                    </p>
                                    <div class="relative">
                                        <input
                                            type="text"
                                            id="batch_purchase_order"
                                            x-model="search"
                                            @focus="open = true"
                                            :placeholder="selectedLabel() || 'Search PO (e.g. P-2-60004)...'"
                                            class="block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm pl-3 pr-10 py-2"
                                        />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                        <div
                                            x-show="open"
                                            x-transition
                                            class="absolute z-20 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                                        >
                                            <template x-if="filtered.length === 0">
                                                <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                                                    No purchase orders found
                                                </div>
                                            </template>
                                            <template x-for="option in filtered" :key="option.id ?? 'none'">
                                                <button
                                                    type="button"
                                                    class="w-full text-left px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-between text-gray-900 dark:text-white"
                                                    @click="select(option)"
                                                >
                                                    <span x-text="option.label"></span>
                                                    <span
                                                        x-show="option.id === selectedId"
                                                        class="text-xs text-blue-600 dark:text-blue-400"
                                                    >
                                                        Selected
                                                    </span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Secondary: Remarks -->
                                <div>
                                    <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                        Remarks (Optional)
                                    </label>
                                    <textarea id="remarks" wire:model="remarks" rows="2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                        placeholder="e.g., Dispatched by Mark, For VisMin route"></textarea>
                                    @error('remarks')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Metadata: Ref + Status inline -->
                                <div class="text-sm text-gray-500 dark:text-gray-400 pt-2 border-t border-gray-200 dark:border-gray-600">
                                    <span>Ref: <span class="font-mono text-gray-700 dark:text-gray-300">{{ $ref_no }}</span></span>
                                    <span class="mx-2">·</span>
                                    <span>Status: Draft</span>
                                </div>

                                <div class="flex justify-end space-x-3 pt-2">
                                    <flux:button type="button" wire:click="closeStepper" variant="outline">
                                        Cancel
                                    </flux:button>
                                    <flux:button type="submit">
                                        {{ $isEditing ? 'Save & Continue' : 'Create & Continue' }}
                                    </flux:button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- STEP 2: REVIEW BRANCHES -->
                    @if ($currentStep === 2)
                        <div>
                            <h4 class="text-md font-medium mb-2">Step 2: Review Branches
                                <span class="text-gray-500 dark:text-gray-400 font-normal">(Batches: {{ implode(', ', $selectedBatchNumbers) }})</span>
                                @if ($currentBatch?->purchaseOrder)
                                    <span class="text-gray-400 dark:text-gray-500">·</span>
                                    <span class="text-blue-600 dark:text-blue-400 font-medium">Linked PO: {{ $currentBatch->purchaseOrder->po_num }}</span>
                                @endif
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                {{ count($filteredBranchesByBatch) }} {{ Str::plural('branch', count($filteredBranchesByBatch)) }} from the selected batches have been automatically added.
                            </p>

                            @if (empty($filteredBranchesByBatch))
                                <div class="text-center py-8 border border-gray-200 dark:border-gray-600 rounded-lg mb-6">
                                    <p class="text-gray-500 dark:text-gray-400">No branches found for batches: {{ implode(', ', $selectedBatchNumbers) }}</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Ensure branches have batch numbers assigned in <a href="{{ route('branch.profile') }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline" wire:navigate>Branch Management</a>.</p>
                                </div>
                            @else
                                <div class="mb-4">
                                    <input type="text"
                                        wire:model.live.debounce.200ms="branchSearch"
                                        placeholder="Search branches by name, code, or address..."
                                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:placeholder-gray-400">
                                </div>

                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden mb-6 max-h-[32rem] overflow-y-auto">
                                    @php
                                        $groupedBranches = collect($this->filteredBranchesForReview)->groupBy('batch');
                                    @endphp
                                    @if ($this->filteredBranchesForReview->isEmpty())
                                        <div class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No branches match "{{ $branchSearch }}"
                                        </div>
                                    @else
                                        @foreach ($groupedBranches as $batchName => $branches)
                                            <div class="border-b border-gray-200 dark:border-gray-600 last:border-b-0" x-data="{ expanded: true }">
                                                <button type="button"
                                                    @click="expanded = !expanded"
                                                    class="w-full flex items-center justify-between px-4 py-3 text-left bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $batchName }}</span>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $branches->count() }} {{ Str::plural('branch', $branches->count()) }}</span>
                                                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                                <div x-show="expanded" x-collapse class="overflow-hidden">
                                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                                        <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                                            <tr>
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Code</th>
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase hidden sm:table-cell">Address</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                                            @foreach ($branches as $branch)
                                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $branch['name'] }}</td>
                                                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $branch['code'] ?? '—' }}</td>
                                                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 hidden sm:table-cell">{{ $branch['address'] ?: '—' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endif

                            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                                <button type="button" wire:click="previousStep"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                                    Back
                                </button>
                                <button type="button" wire:click="nextStep"
                                    @if (empty($filteredBranchesByBatch)) disabled @endif
                                    class="px-4 py-2 text-sm font-medium text-white bg-gray-600 border border-transparent rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:bg-gray-400">
                                    Continue to Products
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- STEP 3: ADD PRODUCTS (Applied to all branches) -->
                    @if ($currentStep === 3)
                        <div>
                            <h4 class="text-md font-medium mb-2">Step 3: Add Products to All Branches</h4>

                            @php
                                $filteredProductsForMatrix = $currentBatch && !empty($selectedProductIdsForAllocation)
                                    ? $this->availableProductsForBatch->whereIn('id', $selectedProductIdsForAllocation)
                                    : collect();
                                $hasMatrixData = $currentBatch && $filteredProductsForMatrix->count() > 0;
                            @endphp

                            {{-- Product Allocation Matrix Section: Header | Matrix | Footer --}}
                            <div class="flex flex-col max-h-[calc(100vh-12rem)] rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 overflow-hidden">
                                {{-- Header: Title / Subheading ---- Add Products --}}
                                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 flex-shrink-0">
                                    <div>
                                        <h5 class="font-medium text-gray-900 dark:text-white">Product Allocation Matrix</h5>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Enter quantities for each product and branch combination.
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            @if ($currentBatch?->purchaseOrder)
                                                <span class="text-blue-600 dark:text-blue-400 font-medium">Linked PO: {{ $currentBatch->purchaseOrder->po_num }}</span>
                                                @if (!empty($selectedProductIdsForAllocation))
                                                    <span class="text-gray-400 dark:text-gray-500 mx-1">|</span>
                                                @endif
                                            @endif
                                            @if (!empty($selectedProductIdsForAllocation))
                                                <strong>{{ count($selectedProductIdsForAllocation) }} products</strong> in allocation.
                                            @else
                                                Leave blank or 0 for no allocation.
                                            @endif
                                        </p>
                                    </div>
                                    <flux:button type="button" wire:click="openAddProductsModal" icon="plus">
                                        Add Products
                                    </flux:button>
                                </div>

                                {{-- Validation errors (strict mode - allocation exceeds available) --}}
                                @if (!empty($allocationValidationErrors) && !$showOverAllocationConfirm)
                                    <div class="flex-shrink-0 px-4 py-3 bg-red-50 dark:bg-red-900/20 border-b border-red-200 dark:border-red-800">
                                        <p class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">Allocation exceeds available quantity:</p>
                                        <ul class="text-xs text-red-700 dark:text-red-300 list-disc list-inside space-y-1">
                                            @foreach ($allocationValidationErrors as $err)
                                                <li>{{ $err }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Amber Warning (when matrix shown and unsaved) --}}
                                @if ($hasMatrixData && !$matrixSavedInSession)
                                    <div class="flex-shrink-0 px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800 flex items-center gap-3">
                                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <p class="text-sm text-amber-800 dark:text-amber-200">You have unsaved allocation quantities. Save before going to Packing / Scan.</p>
                                    </div>
                                @endif

                                {{-- Matrix Scroll Area --}}
                                <div class="flex-1 min-h-0 overflow-auto">
                                    @if ($hasMatrixData)
                                        <table class="w-full table-fixed bg-white dark:bg-gray-800 border-collapse border border-gray-300 dark:border-gray-600" style="min-width: max-content;">
                                            <thead>
                                                <tr>
                                                    <th class="sticky top-0 left-0 z-20 w-[180px] min-w-[180px] px-4 py-2 border border-gray-300 dark:border-gray-600 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase bg-gray-100 dark:bg-gray-700">Branch</th>
                                                    @foreach ($filteredProductsForMatrix as $product)
                                                        <th class="sticky top-0 z-10 w-[140px] min-w-[140px] px-3 py-2 border border-gray-300 dark:border-gray-600 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase bg-gray-100 dark:bg-gray-700">
                                                            <div class="flex flex-col items-center space-y-1.5">
                                                                <button type="button" wire:click="removeProductFromAllocation({{ $product->id }})" class="text-red-500 hover:text-red-700 self-end mb-1" title="Remove this product from allocation">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                </button>
                                                                @php
                                                                    $stockQty = \App\Support\AllocationAvailabilityHelper::getStockQuantity($product);
                                                                    $expectedQty = \App\Support\AllocationAvailabilityHelper::getExpectedQuantityFromPO($product, $selectedPurchaseOrderId);
                                                                    $totalAvailable = \App\Support\AllocationAvailabilityHelper::getAvailableToAllocate($product, $selectedPurchaseOrderId);
                                                                @endphp
                                                                <div class="text-xs font-mono text-gray-700 dark:text-gray-300">{{ $product->product_number ?? '—' }}</div>
                                                                <div class="text-xs text-gray-600 dark:text-gray-400">{{ $product->name ?? '—' }}</div>
                                                                <div class="text-xs text-gray-600 dark:text-gray-400">{{ $product->supplier_code ?? '—' }}</div>
                                                                @if(!empty(trim($product->remarks ?? '')))
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $product->remarks }}</div>
                                                                @endif
                                                                <div class="text-xs text-gray-500 dark:text-gray-500">Stock: {{ (int) $stockQty }}</div>
                                                                <div class="text-xs text-gray-500 dark:text-gray-500">Expected: {{ (int) $expectedQty }}</div>
                                                                <div class="text-xs font-medium text-gray-700 dark:text-gray-300">Total Available: {{ (int) $totalAvailable }}</div>
                                                            </div>
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($currentBatch->branchAllocations as $branchAllocation)
                                                    <tr class="bg-white dark:bg-gray-800 even:bg-gray-50 dark:even:bg-gray-800">
                                                        <td class="sticky left-0 z-10 w-[180px] min-w-[180px] px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700">{{ $branchAllocation->branch->name }}</td>
                                                        @foreach ($filteredProductsForMatrix as $product)
                                                            <td class="w-[140px] min-w-[140px] px-4 py-2 border border-gray-300 dark:border-gray-600 text-center bg-white dark:bg-gray-800">
                                                                <input type="number" wire:model.blur="matrixQuantities.{{ $branchAllocation->id }}.{{ $product->id }}" value="{{ $matrixQuantities[$branchAllocation->id][$product->id] ?? '' }}" min="0" placeholder="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white text-center">
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                @if (!empty($selectedProductIdsForAllocation) && !$currentBatch)
                                                    Ensure branches are selected (Step 1) to display the allocation matrix.
                                                @else
                                                    Click Add Products to add items to allocate.
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Over-allocation warning (warn mode) --}}
                                @if ($showOverAllocationConfirm && !empty($allocationValidationErrors))
                                    <div class="flex-shrink-0 px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border-t border-amber-200 dark:border-amber-800">
                                        <p class="text-sm font-medium text-amber-800 dark:text-amber-200 mb-2">Allocation exceeds available quantity:</p>
                                        <ul class="text-xs text-amber-700 dark:text-amber-300 list-disc list-inside mb-3 space-y-1">
                                            @foreach ($allocationValidationErrors as $err)
                                                <li>{{ $err }}</li>
                                            @endforeach
                                        </ul>
                                        <div class="flex gap-2">
                                            <flux:button wire:click="saveMatrixAllocationsAnyway" class="bg-amber-600 hover:bg-amber-700">Save anyway</flux:button>
                                            <flux:button wire:click="dismissOverAllocationConfirm" variant="ghost">Cancel</flux:button>
                                        </div>
                                    </div>
                                @endif

                                {{-- Footer: Save | Back | Continue --}}
                                <div class="flex-shrink-0 flex items-center justify-between px-4 py-3 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
                                    @if ($hasMatrixData)
                                        <div class="flex flex-col items-start gap-1">
                                            @if (!empty($allocationValidationErrors) && $showOverAllocationConfirm)
                                                <p class="text-xs text-amber-600 dark:text-amber-400">Review over-allocation above</p>
                                            @else
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Save before using Packing / Scan</p>
                                                <flux:button wire:click="saveMatrixAllocations" class="bg-green-600 hover:bg-green-700">Save All Allocations</flux:button>
                                            @endif
                                        </div>
                                    @else
                                        <div></div>
                                    @endif
                                    <div class="flex gap-3">
                                        <button type="button" wire:click="previousStep"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                                            Back
                                        </button>
                                        <button type="button" wire:click="nextStep"
                                            class="px-4 py-2 text-sm font-medium text-white bg-gray-600 border border-transparent rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                            Continue to Dispatch
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Products Modal -->
                            <x-product-selection-modal wire:model.live="showAddProductsModal">
                                <x-slot:search>
                                    <div class="space-y-3">
                                        <div class="space-y-2">
                                            <label for="add-products-po-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filter by PO</label>
                                            <select id="add-products-po-filter" wire:model.live="selectedPurchaseOrderId"
                                                class="block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm pl-3 pr-10 py-2">
                                                <option value="">All products</option>
                                                @foreach ($this->availablePurchaseOrders as $po)
                                                    <option value="{{ $po->id }}">{{ $po->po_num }} — {{ $po->supplier?->name ?? 'No supplier' }}@if($po->expected_delivery_date) ({{ $po->expected_delivery_date->format('M d, Y') }})@endif</option>
                                                @endforeach
                                            </select>
                                            @if($selectedPurchaseOrderId)
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Showing products from selected PO.</p>
                                            @endif
                                        </div>
                                        <x-product-search-bar
                                            wire:model.live.debounce.300ms="addProductsModalSearch"
                                            placeholder="Search by product number (e.g. LD-127 matches LD2505-127)..."
                                        />
                                    </div>
                                </x-slot:search>

                                <x-product-list
                                    :products="$this->addProductsModalResults"
                                    :selected-ids="$temporarySelectedProducts ?? []"
                                    :has-search-query="filled($addProductsModalSearch)"
                                    empty-search-message="Type to search products"
                                    no-results-message="No products match your search"
                                    loading-target="addProductsModalSearch"
                                    :show-price="false"
                                />

                                <x-slot:footer>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ count($temporarySelectedProducts) }} selected
                                        </span>
                                        <div class="flex gap-2">
                                            <flux:button type="button" variant="ghost" wire:click="closeAddProductsModal">
                                                Cancel
                                            </flux:button>
                                            <flux:button type="button" wire:click="addSelectedProductsAndCloseModal" :disabled="count($temporarySelectedProducts) === 0">
                                                Confirm Selection
                                            </flux:button>
                                        </div>
                                    </div>
                                </x-slot:footer>
                            </x-product-selection-modal>
                        </div>
                    @endif

                    <!-- STEP 4: DISPATCH -->
                    @if ($currentStep === 4)
                        <div>
                            <!-- Export Allocation Matrix and Go to Scanning -->
                            <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
                                <a href="{{ route('allocation.scan') }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    wire:navigate>
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                    </svg>
                                    Packing / Scan
                                </a>
                                <button wire:click="exportAllocationToPDF"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export Allocation Matrix to PDF
                                </button>
                            </div>

                            <!-- Branch Summary (scanning moved to Packing / Scan page) -->
                            <div
                                class="mb-6 p-4 bg-white dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700 rounded-lg">
                                <h4 class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-3">
                                    Branches
                                </h4>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                                    Use <a href="{{ route('allocation.scan') }}" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline" wire:navigate>Packing / Scan</a> to verify products for each branch and box.
                                </p>
                                <div
                                    class="overflow-x-auto max-h-96 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <table class="min-w-full bg-white dark:bg-gray-800">
                                        <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                    Status</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                    Branch Name</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                    Products</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach ($currentBatch->branchAllocations as $branchAllocation)
                                                @php $isComplete = $this->isBranchComplete($branchAllocation->id); @endphp
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                        @if ($isComplete)
                                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">Complete</span>
                                                        @else
                                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $branchAllocation->branch->name }}</td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $branchAllocation->items()->whereNull('box_id')->count() }} products</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                                <div class="space-y-4">
                                    <h3 class="text-lg font-medium mb-4">Step 4: Review & Dispatch</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                        Review your allocation. Use <a href="{{ route('allocation.scan') }}" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline" wire:navigate>Packing / Scan</a> to verify products per branch and box.
                                    </p>

                                    @if ($currentBatch)
                                        <!-- Product Allocation by Branch -->
                                        @if ($activeBranchId)
                                            {{-- Show only the active branch table --}}
                                            @php
                                                $activeBranchAllocation = $currentBatch->branchAllocations->find(
                                                    $activeBranchId,
                                                );
                                            @endphp

                                            @if ($activeBranchAllocation)
                                                <div
                                                    class="bg-white dark:bg-gray-800 border-2 border-blue-500 shadow-lg rounded-lg overflow-hidden mb-6">
                                                    <div
                                                        class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-500 to-blue-600">
                                                        <div class="flex items-center justify-between">
                                                            <div>
                                                                <h5 class="font-bold text-lg text-white">
                                                                    {{ $activeBranchAllocation->branch->name }}
                                                                    <span
                                                                        class="ml-2 px-2 py-1 bg-white text-blue-600 rounded text-xs">ACTIVE</span>
                                                                </h5>
                                                                <p class="text-sm text-blue-100 mt-1">
                                                                    {{ $activeBranchAllocation->items()->whereNull('box_id')->count() }}
                                                                    products
                                                                    allocated
                                                                </p>
                                                            </div>
                                                            <div class="text-right">
                                                                @php
                                                                    $branchScannedCount = 0;
                                                                    $branchTotalProducts = $activeBranchAllocation->items()->whereNull('box_id')->count();
                                                                    $originalItems = $activeBranchAllocation->items()->whereNull('box_id')->get();
                                                                    foreach ($originalItems as $item) {
                                                                        $scannedQty =
                                                                            $scannedQuantities[
                                                                                $activeBranchAllocation->id
                                                                            ][$item->product_id] ?? 0;
                                                                        if ($scannedQty >= $item->quantity) {
                                                                            $branchScannedCount++;
                                                                        }
                                                                    }
                                                                    $branchProgress =
                                                                        $branchTotalProducts > 0
                                                                            ? round(
                                                                                ($branchScannedCount /
                                                                                    $branchTotalProducts) *
                                                                                    100,
                                                                            )
                                                                            : 0;
                                                                @endphp
                                                                <div class="text-2xl font-bold text-white">
                                                                    {{ $branchProgress }}%
                                                                </div>
                                                                <div class="text-xs text-blue-100">
                                                                    {{ $branchScannedCount }} /
                                                                    {{ $branchTotalProducts }}
                                                                    complete
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Container -->
<div class="border border-gray-200 dark:border-gray-700 rounded-lg">

    <!-- Buttons above table -->
    {{-- <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-2">
        <button type="button"
            wire:click="generateDeliveryReceipt({{ $activeBranchAllocation->id }})"
            class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-green-600 rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            Generate Delivery Receipt
        </button>

        <button type="button"
            wire:click="resetScannedQuantities({{ $activeBranchAllocation->branch->name }})"
            wire:confirm="Are you sure you want to reset all scanned quantities for {{ $activeBranchAllocation->branch->name }}? This cannot be undone."
            class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-orange-600 rounded hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
            🔄 Reset Branch Scans
        </button>
    </div> --}}

    <!-- Table -->
    <table class="min-w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="w-16 px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                <th class="w-48 px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                <th class="w-36 px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barcode</th>
                <th class="w-24 px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Allocated Qty</th>
                <th class="w-24 px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Scanned Qty</th>
                <th class="w-36 px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
            </tr>
        </thead>
    </table>

    <!-- Scrollable tbody -->
    <div class="max-h-64 overflow-y-auto">
        <table class="min-w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @php
                    // Filter to show only original allocation items (not scanned tracking records)
                    $originalItems = $activeBranchAllocation->items->where('box_id', null);
                @endphp
                @if ($originalItems->count() > 0)
                    @foreach ($originalItems as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 w-16">
                                @if ($item->product->primary_image)
                                    <img src="{{ asset('storage/' . $item->product->primary_image) }}" alt="{{ $item->product->name }}" class="w-12 h-12 rounded object-cover">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 w-48 text-sm">
                                <div class="space-y-1">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $item->product ? ($item->product->remarks ?? $item->product->name) : $item->display_name }}
                                        @if ($item->product && $item->product->color)
                                            <span class="text-gray-500 dark:text-gray-400"> · {{ $item->product->color->name ?? $item->product->color->code }}</span>
                                        @endif
                                    </div>
                                    <div class="text-xs font-mono text-gray-600 dark:text-gray-400">
                                        SKU: {{ $item->product->sku ?? '—' }}
                                    </div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                        Supplier Code: {{ $item->product->supplier_code ?? '—' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 w-36 text-sm font-mono text-gray-500 dark:text-gray-400 truncate" title="{{ $item->display_barcode }}">
                                {{ $item->display_barcode }}
                            </td>
                            <td class="px-6 py-4 w-24 text-sm font-semibold text-lg text-gray-900 dark:text-white">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 w-24 text-3xl font-bold text-blue-600 dark:text-blue-400">
                                @php
                                    // Calculate actual scanned quantity from box-specific records
                                    $actualScannedQty = \App\Models\BranchAllocationItem::where('branch_allocation_id', $activeBranchAllocation->id)
                                        ->where('product_id', $item->product_id)
                                        ->whereNotNull('box_id')
                                        ->sum('scanned_quantity');

                                    // Check if this product has been quantity edited AFTER the batch was created
                                    $hasBeenEdited = \Spatie\Activitylog\Models\Activity::where('log_name', 'branch_inventory')
                                        ->where('properties->barcode', $item->product->barcode ?? '')
                                        ->where('properties->branch_id', $activeBranchAllocation->branch_id)
                                        ->where('description', 'like', 'Updated allocated quantity%')
                                        ->where('created_at', '>', $currentBatch->created_at)
                                        ->exists();

                                    // If edited after batch creation, consider it fully scanned
                                    $totalScannedQty = $hasBeenEdited ? $item->quantity : $actualScannedQty;
                                @endphp
                                {{ $totalScannedQty }}
                            </td>
                            <td class="px-6 py-4 w-36 text-sm truncate">
                                @php
                                    $scannedQty = $totalScannedQty;
                                    $allocatedQty = $item->quantity;
                                @endphp
                                @if ($scannedQty == 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">Not Scanned</span>
                                @elseif($scannedQty >= $allocatedQty)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">✓ Complete</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">{{ $allocatedQty - $scannedQty }} remaining</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center text-sm text-gray-500 dark:text-gray-400">
                            No products allocated to this branch.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

                                                </div>
                                            @endif
                                        @else
                                            <div class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-700 rounded-lg p-6 mb-6 text-center">
                                                <p class="text-blue-800 dark:text-blue-200">Use the <a href="{{ route('allocation.scan') }}" class="font-semibold underline" wire:navigate>Packing / Scan</a> page to verify products for each branch.</p>
                                            </div>
                                        @endif

                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Batch Summary -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
        <h4 class="font-medium mb-3">Batch Summary</h4>
        <div class="flex flex-wrap gap-6 text-sm">
            <div>
                <span class="text-gray-600 dark:text-gray-400">Reference:</span>
                <div class="font-medium">{{ $currentBatch->ref_no }}</div>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">Branches:</span>
                <div class="font-medium">{{ $currentBatch->branchAllocations->count() }}</div>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">Boxes:</span>
                <div class="font-medium">{{ $this->getTotalBoxesCount() }}</div>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">Total Quantities:</span>
                <div class="font-medium">{{ $this->getTotalQuantitiesCount() }}</div>
            </div>
        </div>
    </div>

    <!-- Overall Scan Summary -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800
                rounded-lg p-4 mb-6">
        <h5 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Overall Scan Summary</h5>
        <div class="flex flex-wrap gap-6 text-sm">
            <div>
                <span class="text-blue-700 dark:text-blue-300">Total Quantities:</span>
                <div class="font-medium text-blue-900 dark:text-blue-100 text-xl">
                    {{ $this->getTotalQuantitiesCount() }}
                </div>
            </div>
            <div>
                <span class="text-blue-700 dark:text-blue-300">Scanned Quantities:</span>
                <div class="font-medium text-blue-900 dark:text-blue-100 text-xl">
                    {{ $this->getTotalScannedQuantitiesCount() }} /
                    {{ $this->getTotalQuantitiesCount() }}
                </div>
            </div>
            <div>
                <span class="text-blue-700 dark:text-blue-300">Pending:</span>
                <div class="font-medium text-blue-900 dark:text-blue-100 text-xl">
                    {{ $this->getPendingItemsCount() }}
                </div>
            </div>
        </div>
    </div>

</div>


                               
                </div>
                 <div
                                    class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                                    <div class="flex">
                                        <svg class="h-5 w-5 text-yellow-400 mt-0.5" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                                Ready
                                                to Dispatch</h3>
                                            <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                                Once dispatched, delivery receipts will be generated for all
                                                branches
                                                and this batch cannot be modified.
                                                You can dispatch based on scanned items (partial dispatch allowed).
                                            </p>
                                        </div>
                                    </div>
                                </div>
                    @endif

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" wire:click="previousStep"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                        Back
                    </button>
                    <button type="button" wire:click="dispatchBatchFromStepper"
                        wire:confirm="Are you sure you want to {{ $currentBatch->status === 'dispatched' ? 'update' : 'dispatch' }} this batch? This action cannot be undone."
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        ✓ {{ $currentBatch->status === 'dispatched' ? 'Update Dispatch' : 'Dispatch Batch' }}
                    </button>
                </div>
            </div>
            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const barcodeInput = document.getElementById('barcode-scanner-input');
                        let lastScrollPosition = 0;

                        if (barcodeInput) {
                            // Auto-focus on load
                            barcodeInput.focus();

                            // Save scroll position before Livewire update
                            window.addEventListener('livewire:update', function() {
                                lastScrollPosition = window.scrollY || window.pageYOffset;
                            });

                            // Restore scroll position after Livewire update
                            window.addEventListener('livewire:updated', function() {
                                window.scrollTo(0, lastScrollPosition);
                                // Refocus the input after update
                                setTimeout(() => {
                                    if (barcodeInput) {
                                        barcodeInput.focus();
                                    }
                                }, 50);
                            });

                            // Prevent scroll on focus
                            barcodeInput.addEventListener('focus', function(e) {
                                e.preventDefault();
                            });

                            // Refocus when clicking anywhere on the page (except buttons)
                            document.addEventListener('click', function(e) {
                                if (e.target.tagName !== 'BUTTON' && !e.target.closest('button')) {
                                    setTimeout(() => barcodeInput.focus(), 50);
                                }
                            });
                        }
                    });

                    // Additional Livewire hook to prevent scroll
                    document.addEventListener('livewire:initialized', () => {
                        let scrollPosition = 0;

                        Livewire.hook('morph.updating', ({
                            component,
                            cleanup
                        }) => {
                            scrollPosition = window.scrollY || window.pageYOffset;
                        });

                        Livewire.hook('morph.updated', ({
                            component
                        }) => {
                            window.scrollTo(0, scrollPosition);

                            // Refocus barcode input
                            const barcodeInput = document.getElementById('barcode-scanner-input');
                            if (barcodeInput) {
                                setTimeout(() => barcodeInput.focus(), 100);
                            }
                        });
                    });
                </script>
            @endpush
        @endif

    <!-- Barcode Scanner Side Panel -->
    <div
        x-data="{ open: @entangle('showBarcodeScannerModal').live }"
        x-cloak
        x-on:keydown.escape.window="if (open) { open = false; $wire.closeBarcodeScannerModal(); }"
    >
        <template x-teleport="body">
            <div
                x-show="open"
                x-transition.opacity
                class="fixed inset-0 z-50 flex"
            >
                <div
                    x-show="open"
                    x-transition.opacity
                    class="fixed inset-0 bg-neutral-900/30 dark:bg-neutral-900/50"
                    @click="open = false; $wire.closeBarcodeScannerModal()"
                ></div>

                <section
                    x-show="open"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="relative ml-auto flex h-full w-full max-w-2xl"
                >
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 dark:bg-blue-400"></div>

                    <div class="ml-[0.25rem] flex h-full w-full flex-col bg-white shadow-xl dark:bg-gray-900">
                        <header class="flex items-start justify-between border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        Barcode Scanner
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        @if ($activeBranchId && $currentBox && $currentDr)
                                            {{ $currentBatch->branchAllocations->find($activeBranchId)->branch->name }}
                                            • Box: {{ $currentBox->box_number }} • DR: {{ $currentDr->dr_number }}
                                        @else
                                            Select a box to begin scanning
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <button
                                type="button"
                                class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                                @click="open = false; $wire.closeBarcodeScannerModal()"
                                aria-label="Close scanner panel"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </header>

                        <div class="flex-1 overflow-hidden">
                            <div class="flex h-full flex-col">
                                <div class="flex-1 overflow-y-auto px-6 py-6">
                                    <div class="space-y-6">
                                        <!-- Barcode Input -->
                                        <section class="space-y-4">
                                            <div>
                                                <flux:heading size="md" class="text-gray-900 dark:text-white">Scan Product</flux:heading>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Scan product barcodes to allocate items to the current box.</p>
                                            </div>

                                            <div class="space-y-4">
                                                <div>
                                                    <label for="barcode-input" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                                        Barcode
                                                    </label>
                                                    <input
                                                        id="barcode-input"
                                                        type="text"
                                                        wire:model.live="barcodeInput"
                                                        wire:keydown.enter="processBarcodeScanner"
                                                        placeholder="Scan barcode or enter manually..."
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                        x-ref="barcodeInput"
                                                        x-init="$nextTick(() => { if ($el) $el.focus(); })"
                                                        autofocus
                                                    />
                                                </div>
                                            </div>
                                        </section>

                                        <!-- Scan Feedback -->
                                        <section class="space-y-4">
                                            <div>
                                                <flux:heading size="md" class="text-gray-900 dark:text-white">Scan Status</flux:heading>
                                            </div>

                                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        @if(str_contains($scanFeedback, '✅'))
                                                            <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        @elseif(str_contains($scanFeedback, '❌'))
                                                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        @elseif(str_contains($scanFeedback, '⚠️'))
                                                            <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <div class="ml-3 flex-1">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $scanFeedback ?: 'Ready to scan products...' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                        <!-- Scanned Items Table -->
                                        @if ($currentBox && $currentDr)
                                        <section class="space-y-4">
                                            <div>
                                                <flux:heading size="md" class="text-gray-900 dark:text-white">Scanned Items</flux:heading>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Items scanned into this box.</p>
                                            </div>

                                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                                        <tr>
                                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barcode</th>
                                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                        @php
                                                            $scannedItems = \App\Models\BranchAllocationItem::where('box_id', $currentBox->id)
                                                                ->with('product')
                                                                ->where('scanned_quantity', '>', 0)
                                                                ->get();
                                                        @endphp
                                                        @if ($scannedItems->count() > 0)
                                                            @foreach ($scannedItems as $item)
                                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                                    <td class="px-4 py-3">
                                                                        @if ($item->product && $item->product->primary_image)
                                                                            <img src="{{ asset('storage/' . $item->product->primary_image) }}" alt="{{ $item->display_name }}" class="w-12 h-12 rounded object-cover">
                                                                        @else
                                                                            <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z"></path>
                                                                                </svg>
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                                                        {{ $item->display_name }}
                                                                        @if ($item->product && $item->product->color)
                                                                            <span class="text-xs text-gray-500 dark:text-gray-400">({{ $item->product->color->name }})</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm font-mono text-gray-500 dark:text-gray-400">
                                                                        {{ $item->display_barcode }}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm font-semibold text-blue-600 dark:text-blue-400">
                                                                        {{ $item->scanned_quantity }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                                    No items scanned yet
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </section>
                                        @endif

                                        <!-- Current Status -->
                                        <section class="space-y-4">
                                            <div>
                                                <flux:heading size="md" class="text-gray-900 dark:text-white">Current Status</flux:heading>
                                            </div>

                                            <div class="space-y-4">
                                                <!-- Box Status -->
                                                @if ($currentBox)
                                                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                                                        <h5 class="font-medium text-blue-900 dark:text-blue-100 mb-3 flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                            </svg>
                                                            Box Status
                                                        </h5>
                                                        <div class="space-y-2 text-sm">
                                                            <div class="flex justify-between">
                                                                <span class="text-blue-700 dark:text-blue-300">Box Number:</span>
                                                                <span class="font-medium text-blue-900 dark:text-blue-100">{{ $currentBox->box_number }}</span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-blue-700 dark:text-blue-300">Items in box:</span>
                                                                <span class="font-medium text-blue-900 dark:text-blue-100">{{ $currentBox->current_count }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- DR Status -->
                                                @if ($currentDr)
                                                    <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                                                        <h5 class="font-medium text-green-900 dark:text-green-100 mb-3 flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            Delivery Receipt
                                                        </h5>
                                                        <div class="space-y-2 text-sm">
                                                            <div class="flex justify-between">
                                                                <span class="text-green-700 dark:text-green-300">DR Number:</span>
                                                                <span class="font-medium text-green-900 dark:text-green-100">{{ $currentDr->dr_number }}</span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-green-700 dark:text-green-300">Type:</span>
                                                                <span class="font-medium text-green-900 dark:text-green-100">{{ ucfirst($currentDr->type) }}</span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-green-700 dark:text-green-300">Items Scanned:</span>
                                                                <span class="font-medium text-green-900 dark:text-green-100">{{ $currentDr->scanned_items }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Branch Progress -->
                                                @if ($activeBranchId && $currentBatch)
                                                    @php
                                                        $branchAllocation = $currentBatch->branchAllocations->find($activeBranchId);
                                                        $totalItems = $branchAllocation ? $branchAllocation->items()->whereNull('box_id')->count() : 0;
                                                        $scannedItems = 0;
                                                        if ($branchAllocation) {
                                                            $originalItems = $branchAllocation->items()->whereNull('box_id')->get();
                                                            foreach ($originalItems as $item) {
                                                                // Calculate actual scanned quantity for this product
                                                                $productScannedQty = \App\Models\BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
                                                                    ->where('product_id', $item->product_id)
                                                                    ->whereNotNull('box_id')
                                                                    ->sum('scanned_quantity');
                                                                if ($productScannedQty >= $item->quantity) {
                                                                    $scannedItems++;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="rounded-lg border border-purple-200 bg-purple-50 p-4 dark:border-purple-800 dark:bg-purple-900/20">
                                                        <h5 class="font-medium text-purple-900 dark:text-purple-100 mb-3 flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                            </svg>
                                                            Branch Progress
                                                        </h5>
                                                        <div class="space-y-3 text-sm">
                                                            <div class="flex justify-between">
                                                                <span class="text-purple-700 dark:text-purple-300">Items Scanned:</span>
                                                                <span class="font-medium text-purple-900 dark:text-purple-100">{{ $scannedItems }} / {{ $totalItems }}</span>
                                                            </div>
                                                            <div class="w-full bg-purple-200 rounded-full h-2 dark:bg-purple-700">
                                                                <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ $totalItems > 0 ? ($scannedItems / $totalItems) * 100 : 0 }}%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </section>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 bg-white px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            @if($currentBox && $currentDr)
                                                Scanning active • Box: {{ $currentBox->box_number }} • DR: {{ $currentDr->dr_number }}
                                            @else
                                                Select a box to begin scanning
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            @if($currentBox && $currentDr)
                                                <flux:button type="button" wire:click="declareBoxFull" class="bg-green-600 hover:bg-green-700">
                                                    Declare as Full
                                                </flux:button>
                                            @endif
                                            <flux:button type="button" wire:click="closeBarcodeScannerModal" variant="ghost">
                                                Close Scanner
                                            </flux:button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </template>
    </div>

    </div>
    @endif

    @if (!$showStepper || $currentStep >= 3)
    <!-- Table All (hidden on step 1 and 2 to reduce lag) -->
    <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
    <div class="flex items-center justify-between p-4 pr-10">
        <div class="flex space-x-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" id="search" wire:model.live="search"
                    placeholder="Search ref, remarks, branch..."
                    class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-900 dark:text-white">Date From</label>
                <input type="date" id="dateFrom" wire:model.live="dateFrom"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-900 dark:text-white">Date To</label>
                <input type="date" id="dateTo" wire:model.live="dateTo"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <button type="button" wire:click="clearFilters"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                Clear
            </button>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600 dark:text-gray-400">
                Showing {{ $batches->count() }} of {{ $batches->total() }} allocations
            </span>
            @if (!$showStepper)
                <flux:button wire:click="openStepper" class="inline-flex items-center justify-center gap-2">
                    <span>New Allocation</span>
                </flux:button>
            @endif
        </div>
    </div>
    <div class="overflow-x-auto">
    @php
        $steps = [
            1 => 'Step 1: Select Batches',
            2 => 'Step 2: Review Branches',
            3 => 'Step 3: Adding Products',
            4 => 'Step 4: Dispatch Scanning',
        ];
    @endphp

    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 min-w-full">
        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    <button wire:click="sortBy('ref_no')" class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-400">
                        <span>Reference Number</span>
                        <div class="flex flex-col">
                            <svg class="w-3 h-3 {{ $sortField === 'ref_no' && $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <svg class="w-3 h-3 {{ $sortField === 'ref_no' && $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </button>
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    <button wire:click="sortBy('created_at')" class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-400">
                        <span>Date Created</span>
                        <div class="flex flex-col">
                            <svg class="w-3 h-3 {{ $sortField === 'created_at' && $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <svg class="w-3 h-3 {{ $sortField === 'created_at' && $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </button>
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Current Step
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Scan Progress
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    <button wire:click="sortBy('status')" class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-400">
                        <span>Status</span>
                        <div class="flex flex-col">
                            <svg class="w-3 h-3 {{ $sortField === 'status' && $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <svg class="w-3 h-3 {{ $sortField === 'status' && $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </button>
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    <button wire:click="sortBy('batch_number')" class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-400">
                        <span>Batch</span>
                        <div class="flex flex-col">
                            <svg class="w-3 h-3 {{ $sortField === 'batch_number' && $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <svg class="w-3 h-3 {{ $sortField === 'batch_number' && $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </button>
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody wire:loading.class="opacity-60">
            @forelse ($batches as $record)
                <tr wire:key="batch-row-{{ $record->id }}" class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <!-- Reference Number -->
                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                        {{ $record->ref_no }}
                    </td>

                    <!-- Date Created -->
                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                        {{ $record->created_at->format('M d, Y') }}
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $record->created_at->format('h:i A') }}
                        </div>
                    </td>

                    <!-- Current Step -->
                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                        @php
                            $batchStep = $batchSteps[$record->id] ?? 1;
                            $stepLabels = [
                                1 => 'Step 1: Select Batches',
                                2 => 'Step 2: Review Branches',
                                3 => 'Step 3: Adding Products',
                                4 => 'Step 4: Dispatch Scanning',
                            ];
                        @endphp
                        <div class="flex items-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if ($batchStep == 4 && $record->status === 'dispatched') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                                    @elseif($batchStep == 4) bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300
                                    @elseif($batchStep == 3) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300 @endif">
                                {{ $batchStep == 4 && $record->status === 'dispatched' ? 'Completed' : $stepLabels[$batchStep] ?? 'Not Started' }}
                            </span>
                        </div>
                    </td>

                    <!-- Scan Progress (pre-computed in backend) -->
                    <td class="px-6 py-4 text-sm text-blue-900 dark:text-blue-200">
                        @php
                            $progress = $scanProgress[$record->id] ?? ['scanned' => 0, 'allocated' => 0, 'allScanned' => false];
                            $scannedQty = $progress['scanned'];
                            $allocatedQty = $progress['allocated'];
                        @endphp
                        @if ($allocatedQty > 0)
                            <span class="font-semibold">{{ $scannedQty }}/{{ $allocatedQty }}</span>
                            <span
                                class="ml-2 px-2 py-0.5 rounded text-xs font-medium
                                    @if ($scannedQty >= $allocatedQty && $allocatedQty > 0) bg-green-100 text-green-800 dark:bg-green-900/10 dark:text-green-200
                                    @elseif($scannedQty == 0)
                                        bg-gray-100 text-gray-500 dark:bg-gray-900/10 dark:text-gray-400
                                    @else
                                        bg-yellow-100 text-yellow-800 dark:bg-yellow-900/10 dark:text-yellow-300 @endif">
                                @if ($scannedQty >= $allocatedQty && $allocatedQty > 0)
                                    All scanned
                                @elseif($scannedQty == 0)
                                    Not Started
                                @else
                                    {{ $allocatedQty - $scannedQty }} left
                                @endif
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">No items</span>
                        @endif
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4 text-sm capitalize text-gray-800 dark:text-gray-200">
                        {{ $record->status }}
                    </td>

                    <!-- Batches -->
                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                        @if ($record->batch_number)
                            {{ $record->batch_number }}
                        @else
                            <span class="text-gray-400">No batches</span>
                        @endif
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center justify-center space-x-2"
                            style="min-width: 150px; min-height:42px;">
                            @if ($record->status === 'dispatched' && !(($scanProgress[$record->id] ?? [])['fullyScanned'] ?? false))
                                <button wire:click="editRecord({{ $record->id }})"
                                    class="px-3 py-1 text-xs font-medium text-white rounded bg-green-600 hover:bg-green-700">
                                    Continue Scanning
                                </button>
                            @else
                                <button wire:click="editRecord({{ $record->id }})"
                                    class="px-3 py-1 text-xs font-medium text-white rounded bg-blue-600 hover:bg-blue-700">
                                    View
                                </button>
                            @endif
                            <button wire:click="removeBatch({{ $record->id }})"
                                wire:confirm="Are you sure you want to delete this allocation? This action cannot be undone."
                                class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <td colspan="7" class="px-6 py-4 text-center">No allocations found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="py-4 px-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-900 dark:text-white">Per Page:</label>
                <select wire:model.live="perPage"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div>
                {{ $batches->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@script
    <script>
        // VDR CSV Download Handler
        window.addEventListener('download-vdr', (event) => {
            const {
                content,
                filename
            } = event.detail;

            // Create a blob with the CSV content
            const blob = new Blob([content], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = window.URL.createObjectURL(blob);

            // Create a temporary link and trigger download
            const link = document.createElement('a');
            if (link.download !== undefined) {
                link.href = url;
                link.download = filename;
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            // Clean up
            window.URL.revokeObjectURL(url);
        });

        // VDR Print Handler
        window.addEventListener('open-vdr-print', (event) => {
            const {
                batchId
            } = event.detail;

            // Open print window with the VDR content
            const printUrl = `/allocation/vdr/print/${batchId}`;
            window.open(printUrl, '_blank', 'width=800,height=600');
        });

        // VDR Excel Download Handler
        window.addEventListener('open-excel-download', (event) => {
            const {
                url
            } = event.detail;

            // Open the Excel export URL which will trigger a download
            window.open(url, '_blank');
        });

        // PDF Download Handler
        window.addEventListener('open-pdf-download', (event) => {
            const {
                url
            } = event.detail;

            // Open the PDF export URL which will trigger a download
            window.open(url, '_blank');
        });

        // Delivery Receipt Download Handler
        window.addEventListener('download-delivery-receipt', (event) => {
            const {
                url,
                filename
            } = event.detail;

            // Open the delivery receipt URL which will trigger a download
            window.open(url, '_blank');
        });

        // Swal popup handlers (Livewire events)
        document.addEventListener('DOMContentLoaded', function() {
            // Success popup handler
            window.Livewire.on('show-success-popup', (data) => {
                const {
                    title,
                    message
                } = data[0];

                Swal.fire({
                    icon: 'success',
                    title: title,
                    text: message,
                    timer: 3000,
                    showConfirmButton: false
                });
            });

            // Info popup handler
            window.Livewire.on('show-info-popup', (data) => {
                const {
                    title,
                    message
                } = data[0];

                Swal.fire({
                    icon: 'info',
                    title: title,
                    text: message,
                    confirmButtonText: 'OK'
                });
            });
        });
    </script>
@endscript
