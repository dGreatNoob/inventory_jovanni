<section class="bg-white dark:bg-gray-800 shadow rounded-lg">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
            </svg>
            Create Branch Sales
        </h3>
        <button type="button" wire:click="closeCreateStepper"
            class="rounded-full p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Stepper Bar (stock-in style) -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        @php $steps = [['label' => 'Sales Form'], ['label' => 'Add Items'], ['label' => 'Save']]; @endphp
        <nav aria-label="Progress" class="w-full">
            <div class="flex items-center justify-between bg-zinc-50 dark:bg-zinc-900 rounded-2xl shadow-md p-2 sm:p-4 border border-gray-200 dark:border-zinc-700">
                @foreach($steps as $i => $step)
                    @php $stepNum = $i + 1; @endphp
                    <div class="flex-1 flex flex-col items-center relative">
                        @if($currentStep > $stepNum)
                            <div class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-full bg-emerald-500 text-white text-sm sm:text-base shadow-md">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </div>
                        @elseif($currentStep === $stepNum)
                            <div class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-full bg-blue-600 text-white text-sm sm:text-base shadow-md">
                                <span class="font-bold">{{ $stepNum }}</span>
                            </div>
                        @else
                            <div class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-full bg-gray-300 dark:bg-zinc-700 text-gray-500 dark:text-gray-300 text-sm sm:text-base shadow-md">
                                <span class="font-bold">{{ $stepNum }}</span>
                            </div>
                        @endif
                        @if($i < count($steps) - 1)
                            <div class="absolute top-1/2 right-0 w-full h-1 z-0" style="left: 50%; width: calc(100% - 2.25rem);">
                                <div class="h-1 rounded-full transition-all duration-300
                                    @if($currentStep > $stepNum) bg-emerald-500
                                    @elseif($currentStep === $stepNum) bg-blue-600 dark:bg-blue-600
                                    @else bg-gray-300 dark:bg-zinc-700 @endif"></div>
                            </div>
                        @endif
                        <div class="mt-1 sm:mt-2 text-center">
                            <div class="text-xs sm:text-sm font-bold {{ $currentStep === $stepNum ? 'text-blue-900 dark:text-blue-100' : 'text-gray-700 dark:text-gray-200' }}">
                                {{ $step['label'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </nav>
    </div>

    @if(session()->has('error'))
        <div class="mx-6 mt-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 text-sm text-red-800 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <!-- Step Content -->
    <div class="p-6">
        {{-- Step 1: Create Branch Sales Form --}}
        @if($currentStep === 1)
            <form wire:submit.prevent="step1Next" class="space-y-6">
                <h4 class="text-md font-medium text-gray-900 dark:text-white">Step 1: Create Branch Sales Form</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="createBranchId" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Location (Branch) *</label>
                        <div class="relative" x-data x-on:click.outside="$wire.set('createBranchDropdown', false)" x-on:keydown.escape.window="$wire.set('createBranchDropdown', false)">
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <svg aria-hidden="true" class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="createBranchSearch"
                                    wire:focus="$set('createBranchDropdown', true)"
                                    placeholder="Search by branch name or code…"
                                    autocomplete="off"
                                    aria-label="Search and select branch"
                                    class="block w-full min-h-[38px] h-10 pl-10 pr-3 rounded-lg border {{ $errors->has('createBranchId') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }} bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />

                                @if($createBranchDropdown)
                                    <div class="absolute z-30 mt-2 w-full max-h-48 overflow-auto rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-lg py-1">
                                        @forelse($this->filteredCreateBranches as $branch)
                                            <button
                                                type="button"
                                                wire:click="selectCreateBranch({{ $branch->id }})"
                                                class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 {{ $createBranchId == $branch->id ? 'bg-indigo-50 dark:bg-indigo-900/30' : 'text-gray-900 dark:text-white' }}"
                                            >
                                                <span class="flex-1 min-w-0 font-medium truncate">{{ $branch->name }}</span>
                                                @if($branch->code ?? null)
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 shrink-0">{{ $branch->code }}</span>
                                                @endif
                                                @if($createBranchId == $branch->id)
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
                        @error('createBranchId')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="createTransactionDate" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Transaction Date *</label>
                        <input type="date" id="createTransactionDate" wire:model="createTransactionDate"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                        @error('createTransactionDate')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="createSellingArea" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Selling Area</label>
                        <select id="createSellingArea" wire:model="createSellingArea"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">— None —</option>
                            @foreach($sellingAreaOptions as $area)
                                @if($area)<option value="{{ $area }}">{{ $area }}</option>@endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="createAgentId" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Sales Person (Agent) *</label>
                        <div class="relative" x-data x-on:click.outside="$wire.set('createAgentDropdown', false)" x-on:keydown.escape.window="$wire.set('createAgentDropdown', false)">
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <svg aria-hidden="true" class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="createAgentSearch"
                                    wire:focus="$set('createAgentDropdown', true)"
                                    placeholder="Search by name or agent code…"
                                    autocomplete="off"
                                    aria-label="Search and select agent"
                                    class="block w-full min-h-[38px] h-10 pl-10 pr-3 rounded-lg border {{ $errors->has('createAgentId') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }} bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />

                                @if($createAgentDropdown)
                                    <div class="absolute z-30 mt-2 w-full max-h-48 overflow-auto rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-lg py-1">
                                        @forelse($this->filteredCreateAgents as $agent)
                                            <button
                                                type="button"
                                                wire:click="selectCreateAgent({{ $agent->id }})"
                                                class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 {{ $createAgentId == $agent->id ? 'bg-indigo-50 dark:bg-indigo-900/30' : 'text-gray-900 dark:text-white' }}"
                                            >
                                                <span class="flex-1 min-w-0 font-medium truncate">{{ $agent->agent_code }} - {{ $agent->name }}</span>
                                                @if($createAgentId == $agent->id)
                                                    <svg class="h-4 w-4 text-indigo-500 shrink-0" viewBox="0 0 20 20" fill="none">
                                                        <path d="M5 11.5L8.5 15L15 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                @endif
                                            </button>
                                        @empty
                                            <div class="px-3 py-3 text-xs text-gray-500 dark:text-gray-400">
                                                No agents match your search.
                                            </div>
                                        @endforelse
                                    </div>
                                @endif
                            </div>
                        </div>
                        @error('createAgentId')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="createTerm" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Term</label>
                        <select id="createTerm" wire:model="createTerm"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">— None —</option>
                            @foreach($this->termOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="createRemarks" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Remarks</label>
                        <textarea id="createRemarks" wire:model="createRemarks" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Optional remarks..."></textarea>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="closeCreateStepper"
                        class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-xl hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 dark:focus:ring-gray-400 dark:focus:border-gray-400 transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Cancel</span>
                    </button>
                    <button type="button" wire:click="saveAsDraftAndClose"
                        class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 dark:focus:ring-gray-400 dark:focus:border-gray-400 transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Save as Draft &amp; Close</span>
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors duration-200 flex items-center justify-center gap-2">
                        <span>Next: Add Items</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                </div>
            </form>
        @endif

        {{-- Step 2: Branch Sales Details (Add Items) --}}
        @if($currentStep === 2)
            {{-- Summary from Step 1 --}}
            <div class="mb-6 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Transaction Summary</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                    <div><span class="text-gray-500 dark:text-gray-400">Branch:</span> {{ (($b = collect($branches)->firstWhere('id', $createBranchId)) ? $b->name : '—') }}</div>
                    <div><span class="text-gray-500 dark:text-gray-400">Date:</span> {{ \Carbon\Carbon::parse($createTransactionDate)->format('M d, Y') }}</div>
                    <div><span class="text-gray-500 dark:text-gray-400">Agent:</span> {{ (($a = collect($agents)->firstWhere('id', $createAgentId)) ? $a->name : '—') }}</div>
                    <div><span class="text-gray-500 dark:text-gray-400">Selling Area:</span> {{ $createSellingArea ?: '—' }}</div>
                </div>
            </div>

            {{-- Add Item button --}}
            <div class="mb-6">
                <button type="button" wire:click="openAddItemModal"
                    class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Add Item
                </button>
            </div>

            {{-- Items list --}}
            @if(count($salesItems) > 0)
                <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-600 mb-6">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Barcode</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Unit Price</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                                <th class="px-4 py-3 w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($salesItems as $idx => $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $item['name'] }}</td>
                                    <td class="px-4 py-3 text-sm font-mono text-gray-500 dark:text-gray-400">{{ $item['barcode'] ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">{{ $item['quantity'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">₱{{ number_format($item['unit_price'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white">₱{{ number_format($item['total'], 2) }}</td>
                                    <td class="px-4 py-3">
                                        <button type="button" wire:click="removeSalesItem({{ $idx }})"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-sm font-medium text-right text-gray-900 dark:text-white">Total</td>
                                <td class="px-4 py-3 text-sm font-bold text-right text-indigo-600 dark:text-indigo-400">
                                    ₱{{ number_format(collect($salesItems)->sum('total'), 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">No items added yet. Click "Add Item" to add products.</p>
            @endif

            {{-- Add Item Modal --}}
            @if($showAddItemModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-on:keydown.escape.window="$wire.closeAddItemModal()">
                <div class="fixed inset-0 bg-neutral-900/50" wire:click="closeAddItemModal"></div>
                <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-lg shadow-xl max-h-[90vh] overflow-hidden flex flex-col"
                    @click.stop>
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">Add Item</h4>
                        <button type="button" wire:click="closeAddItemModal" class="rounded-full p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto p-6 space-y-4">
                        {{-- Searchable combobox with overlay dropdown --}}
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Item Search</label>
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <input type="text" wire:model.live.debounce.200ms="addItemSearch"
                                        placeholder="{{ $addItemBarcodeSearch ? 'Scan or enter barcode to auto-add' : 'Search by name, SKU, or product number (e.g. TY-209)' }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                                    {{-- Overlay dropdown (only when not barcode mode and search has content) --}}
                                    @if(!$addItemBarcodeSearch && !empty(trim($addItemSearch ?? '')))
                                    <div class="absolute left-0 right-0 top-full mt-1 z-10 max-h-48 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-lg">
                                        @forelse($this->filteredBranchProductsForAddItem as $p)
                                            <button type="button" wire:click="selectAddItemProduct({{ $p['id'] }})"
                                                class="w-full px-4 py-2 text-left text-sm hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors {{ $addItemSelectedProduct && ($addItemSelectedProduct['id'] ?? null) == $p['id'] ? 'bg-indigo-100 dark:bg-indigo-900/40' : '' }}">
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $p['name'] }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $p['barcode'] ?? $p['product_number'] ?? $p['sku'] ?? '—' }} · Qty: {{ $p['remaining_quantity'] ?? 0 }}</div>
                                            </button>
                                        @empty
                                            <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">No products found</div>
                                        @endforelse
                                    </div>
                                    @endif
                                </div>
                                <label class="flex items-center gap-2 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300 self-end pb-2">
                                    <input type="checkbox" wire:model.live="addItemBarcodeSearch" class="rounded border-gray-300 dark:border-gray-600">
                                    Barcode Search
                                </label>
                            </div>
                            @if($addItemBarcodeSearch)
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Exact barcode match will add item automatically. Pre-fill qty/price for defaults.</p>
                            @endif
                        </div>

                        {{-- All fields visible by default (editable but ignored until product selected) --}}
                        <div class="space-y-4 pt-2 border-t border-gray-200 dark:border-gray-700">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Quantity</label>
                                    <input type="number" wire:model.live="addItemQuantity" min="1" max="{{ $addItemSelectedProduct ? ($addItemSelectedProduct['remaining_quantity'] ?? 999) : 999 }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Price</label>
                                    <input type="number" step="0.01" wire:model.live="addItemUnitPrice"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Discount %</label>
                                    <input type="number" step="0.01" wire:model.live="addItemDiscountPercent" min="0" max="100"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Discount Amount (auto)</label>
                                    <input type="text" readonly
                                        value="₱{{ number_format($addItemDiscountAmount ?? 0, 2) }}"
                                        class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400">
                                </div>
                            </div>
                            @if($addItemPromoName)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Promo</label>
                                <div class="px-3 py-2 rounded-md bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-200 text-sm">{{ $addItemPromoName }}</div>
                            </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Remarks</label>
                                <input type="text" wire:model="addItemRemarks"
                                    placeholder="Optional remarks..."
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-wrap gap-2">
                        <button type="button" wire:click="closeAddItemModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 dark:text-gray-200">Close</button>
                        @if($addItemSelectedProduct)
                            <button type="button" wire:click="addItemFromModal(false)"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700">Save</button>
                            <button type="button" wire:click="addItemFromModal(true)"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-700">Save &amp; Add New</button>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" wire:click="step2Back"
                    class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-xl hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 dark:focus:ring-gray-400 dark:focus:border-gray-400 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Back</span>
                </button>
                <button type="button" wire:click="step2Next" @disabled(empty($salesItems))
                    class="flex-1 px-4 py-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-blue-600">
                    <span>Next: Save</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </button>
            </div>
        @endif

        {{-- Step 3: Save Branch Sales --}}
        @if($currentStep === 3)
            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Step 3: Save Branch Sales</h4>
            <div class="mb-6 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Transaction Summary</h5>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm mb-4">
                    <div><span class="text-gray-500 dark:text-gray-400">Branch:</span> {{ (($b = collect($branches)->firstWhere('id', $createBranchId)) ? $b->name : '—') }}</div>
                    <div><span class="text-gray-500 dark:text-gray-400">Date:</span> {{ \Carbon\Carbon::parse($createTransactionDate)->format('M d, Y') }}</div>
                    <div><span class="text-gray-500 dark:text-gray-400">Agent:</span> {{ (($a = collect($agents)->firstWhere('id', $createAgentId)) ? $a->name : '—') }}</div>
                    <div><span class="text-gray-500 dark:text-gray-400">Selling Area:</span> {{ $createSellingArea ?: '—' }}</div>
                </div>
                <div class="text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Items:</span> {{ count($salesItems) }} products, {{ collect($salesItems)->sum('quantity') }} units
                </div>
                <div class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-2">
                    Total: ₱{{ number_format(collect($salesItems)->sum('total'), 2) }}
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" wire:click="step3Back"
                    class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-xl hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 dark:focus:ring-gray-400 dark:focus:border-gray-400 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Back</span>
                </button>
                <button type="button" wire:click="saveBranchSales"
                    class="flex-1 px-4 py-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Save Branch Sales</span>
                </button>
            </div>
        @endif
    </div>
</section>
