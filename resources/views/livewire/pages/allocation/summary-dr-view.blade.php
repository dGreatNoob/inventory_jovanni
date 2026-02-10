<x-slot:header>Allocation</x-slot:header>
<x-slot:subheader>Summary DR</x-slot:subheader>
<x-slot:headerHref>{{ route('allocation.for-dispatch') }}</x-slot:headerHref>

<div class="pt-4 sm:pt-6 -mb-6 lg:-mb-8">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 w-full">
        @if(!$this->motherDr)
            <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm p-8 text-center">
                <p class="text-gray-600 dark:text-gray-400 mb-4">Summary DR not found or invalid.</p>
                <a href="{{ route('allocation.for-dispatch') }}" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 font-medium hover:underline" wire:navigate>Back to For Dispatch DRs</a>
            </div>
            @return
        @endif

        @php
            $mother = $this->motherDr;
            $branchAllocationId = $mother->branchAllocation?->id;
        @endphp

        {{-- Summary DR header --}}
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm p-6 mb-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Summary DR: <span class="font-mono">{{ $mother->dr_number }}</span></h1>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Branch</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ $mother->branchAllocation?->branch?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Batch ref</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ $mother->branchAllocation?->batchAllocation?->ref_no ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Status</dt>
                            <dd>
                                @if($this->statusLabel === 'In shipment')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">In shipment</span>
                                @elseif($this->statusLabel === 'Dispatched')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Dispatched</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Not dispatched</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Boxes</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ $this->boxesWithItems->count() }}</dd>
                        </div>
                    </dl>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($branchAllocationId)
                        <a href="{{ route('allocation.scan') }}?branch_allocation_id={{ $branchAllocationId }}"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-zinc-600"
                           wire:navigate>
                            Open in Packing / Scan
                        </a>
                    @endif
                    <a href="{{ route('allocation.for-dispatch') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:underline" wire:navigate>Back to list</a>
                </div>
            </div>
        </div>

        {{-- Boxes and their DRs + items --}}
        <div class="space-y-6">
            @foreach($this->boxesWithItems as $row)
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-700/50">
                        <div class="flex flex-wrap items-center gap-4">
                            <span class="font-medium text-gray-900 dark:text-white">Box: {{ $row->box->box_number }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">DR: <span class="font-mono text-gray-700 dark:text-gray-300">{{ $row->dr_number }}</span></span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $row->items->count() }} item(s)</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                            <thead class="bg-gray-50 dark:bg-zinc-700">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Product</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Barcode</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Qty</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                                @forelse($row->items as $item)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $item->display_name }}</td>
                                        <td class="px-4 py-2 text-sm font-mono text-gray-500 dark:text-gray-400">{{ $item->display_barcode }}</td>
                                        <td class="px-4 py-2 text-sm text-right font-medium text-gray-900 dark:text-white">{{ $item->scanned_quantity }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">No items scanned in this box.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
