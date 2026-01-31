<section>
    <div class="mb-5 max-w-xlg p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <h3 class="text-lg font-semibold mb-2">Deployment History</h3>
        <div class="flex items-center justify-between p-4 pr-10">
            <div class="flex space-x-2">
                <input 
                    type="text" 
                    wire:model.live.debounce.500ms="search" 
                    placeholder="Search agent..." 
                    class="border p-2 rounded-lg"
                />
                <select wire:model.live.debounce.500ms="perPage"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-2">AGENT CODE</th>
                        <th class="px-4 py-2">AGENT NAME</th>
                        <th class="px-4 py-2">BRANCH</th>
                        <th class="px-4 py-2">SELLING AREA</th>
                        <th class="px-4 py-2">ASSIGNED AT</th>
                        <th class="px-4 py-2">RELEASED AT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <td class="px-4 py-2">{{ $assignment->agent->agent_code ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $assignment->agent->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $assignment->branch->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $assignment->selling_area ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $assignment->assigned_at }}</td>
                            <td class="px-4 py-2">{{ $assignment->released_at ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-2 text-center text-gray-400">No deployment history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>
</section>