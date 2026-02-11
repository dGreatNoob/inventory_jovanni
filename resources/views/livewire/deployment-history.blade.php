<div>
{{-- Deployment History --}}
<div class="bg-white dark:bg-gray-800 shadow rounded-lg">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Deployment History</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Agent branch assignments and release history</p>
    </div>

    {{-- Search and Per Page (controls this table) --}}
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="relative flex-1 max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search agent or branch..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm">
            </div>
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-200 whitespace-nowrap">Per Page</label>
                <select wire:model.live="perPage"
                    class="block w-24 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Results Info --}}
    <div class="px-6 py-3 text-sm text-gray-700 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
        @if($assignments->total() > 0)
            <span class="font-semibold text-gray-900 dark:text-white">{{ $assignments->total() }}</span> records found
        @else
            <span class="font-semibold text-gray-900 dark:text-white">No deployment history found</span>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Agent Code</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Agent Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Branch</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Selling Area</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Assigned At</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Released At</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($assignments as $assignment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $assignment->agent->agent_code ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $assignment->agent->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $assignment->branch->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $assignment->selling_area ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $assignment->assigned_at?->format('M j, Y H:i') ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $assignment->released_at?->format('M j, Y H:i') ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12">
                            <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                <svg class="w-16 h-16 mb-4 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    No deployment history found
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Try adjusting your search or assign agents to branches
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $assignments->links('livewire::tailwind', ['scrollTo' => false]) }}
    </div>
</div>
</div>
