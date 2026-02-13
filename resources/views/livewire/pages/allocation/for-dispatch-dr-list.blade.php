<x-slot:header>Allocation</x-slot:header>
<x-slot:subheader>For Dispatch DRs</x-slot:subheader>
<x-slot:headerHref>{{ route('allocation.warehouse') }}</x-slot:headerHref>

<div class="pb-6">
    <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
        {{-- Filter bar: same layout as warehouse allocation page --}}
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex space-x-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="search" wire:model.live.debounce.300ms="search"
                        placeholder="Search DR number, branch, batch..."
                        class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                <div class="flex items-center space-x-2">
                    <label for="statusFilter" class="text-sm font-medium text-gray-900 dark:text-white">Status</label>
                    <select id="statusFilter" wire:model.live="statusFilter"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="all">All</option>
                        <option value="not_dispatched">Not dispatched</option>
                        <option value="dispatched">Dispatched</option>
                        <option value="in_shipment">In shipment</option>
                    </select>
                </div>
                <button type="button" wire:click="clearFilters"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                    Clear
                </button>
            </div>
            <span class="text-sm text-gray-600 dark:text-gray-400">
                Showing {{ $summaryDRs->count() }} of {{ $summaryDRs->total() }} DR{{ $summaryDRs->total() !== 1 ? 's' : '' }}
            </span>
        </div>

        <div class="overflow-x-auto border-t border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 min-w-full">
                <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">DR number</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Dispatch Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Branch</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Batch ref</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Boxes</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody wire:loading.class="opacity-60">
                    @forelse($summaryDRs as $row)
                        <tr wire:key="dr-row-{{ $row->id }}" class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 text-sm font-mono font-medium text-gray-900 dark:text-white">{{ $row->dr_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                @if($row->dispatched_at)
                                    {{ $row->dispatched_at->format('M d, Y') }}
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $row->dispatched_at->format('h:i A') }}</div>
                                @else
                                    {{ $row->created_at?->format('M d, Y') ?? '—' }}
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $row->created_at?->format('h:i A') ?? '' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">{{ $row->branch_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">{{ $row->batch_ref }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">{{ $row->box_count }}</td>
                            <td class="px-6 py-4">
                                @if($row->status === 'in_shipment')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">In shipment</span>
                                @elseif($row->status === 'dispatched')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Dispatched</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Not dispatched</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('allocation.for-dispatch.view', ['summaryDr' => $row->id]) }}"
                                    class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline"
                                    wire:navigate>
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No summary DRs found. Create boxes in <a href="{{ route('allocation.scan') }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline" wire:navigate>Packing / Scan</a> to see them here.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination controls: same layout as warehouse allocation page --}}
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
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
                    {{ $summaryDRs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
