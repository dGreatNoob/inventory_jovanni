<x-slot:header>Allocation</x-slot:header>
<x-slot:subheader>For Dispatch DRs</x-slot:subheader>
<x-slot:headerHref>{{ route('allocation.warehouse') }}</x-slot:headerHref>

<div class="pt-4 sm:pt-6 -mb-6 lg:-mb-8">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 w-full">
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-zinc-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Summary DRs (for dispatch)</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Mother DRs from Packing/Scan. View details or open in Packing/Scan to continue scanning.</p>
                <div class="flex flex-wrap gap-4">
                    <div class="min-w-[200px] flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                        <input id="search" type="text" wire:model.live.debounce.300ms="search"
                               placeholder="DR number, branch, batch..."
                               class="block w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                    </div>
                    <div class="min-w-[180px]">
                        <label for="statusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select id="statusFilter" wire:model.live="statusFilter"
                                class="block w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="all">All</option>
                            <option value="not_dispatched">Not dispatched</option>
                            <option value="dispatched">Dispatched</option>
                            <option value="in_shipment">In shipment</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">DR number</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Branch</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Batch ref</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Boxes</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                        @forelse($summaryDRs as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="px-4 py-3 text-sm font-mono font-medium text-gray-900 dark:text-white">{{ $row->dr_number }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $row->branch_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $row->batch_ref }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $row->box_count }}</td>
                                <td class="px-4 py-3">
                                    @if($row->status === 'in_shipment')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">In shipment</span>
                                    @elseif($row->status === 'dispatched')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Dispatched</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Not dispatched</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('allocation.for-dispatch.view', ['summaryDr' => $row->id]) }}"
                                       class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline"
                                       wire:navigate>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No summary DRs found. Create boxes in <a href="{{ route('allocation.scan') }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline" wire:navigate>Packing / Scan</a> to see them here.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
