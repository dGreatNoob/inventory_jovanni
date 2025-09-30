<x-slot:header>Request Slip</x-slot:header>
<x-slot:subheader>Request Inbound or Outbound</x-slot:subheader>
<div>
    <div>
        <!-- New Request Button -->
        <div class="flex justify-end mb-4">
            <x-button type="button" variant="primary" wire:click="$set('showCreateModal', true)">
                + New Request
            </x-button>
        </div>

        <!-- Create Request Slip Modal -->
        <x-modal wire:model="showCreateModal" class="max-h-[80vh]">
            <x-slot name="title">Create Request Slip</x-slot>
            <form wire:submit.prevent="create">
                <x-dropdown wire:model="purpose" name="purpose" label="Purpose" :options="[
                    'Pet Food' => 'Request Pet Food Supplies',
                    'Pet Toys' => 'Request Pet Toys & Accessories',
                    'Pet Care' => 'Request Pet Care Products',
                    'Pet Health' => 'Request Pet Health & Medical Supplies',
                    'Pet Grooming' => 'Request Pet Grooming Supplies',
                    'Pet Bedding' => 'Request Pet Bedding & Comfort Items',
                    'Pet Training' => 'Request Pet Training Supplies',
                    'Pet Safety' => 'Request Pet Safety & Security Items',
                    'Office Supplies' => 'Request Office Supplies',
                    'Packaging' => 'Request Packaging Materials',
                    'Equipment' => 'Request Equipment & Tools',
                    'Other' => 'Request Other Items',
                ]"
                    placeholder="Select a Purpose" />
                <div class="grid gap-6 mb-2 md:grid-cols-2">
                    <x-input type="text" wire:model="sent_from" name="sent_from" label="Sender" readonly />
                    <x-dropdown name="sent_to" wire:model="sent_to" label="Receiver" :options="$this->departments->pluck('name', 'id')->toArray()" />
                </div>
                <x-input type="textarea" wire:model="description" rows="8" name="description" label="Description" />
                <div class="flex justify-end mt-4 space-x-2">
                    <x-button type="button" variant="secondary" wire:click="$set('showCreateModal', false)">Cancel</x-button>
                    <x-button type="submit" variant="primary">Save</x-button>
                </div>
            </form>
        </x-modal>

        <x-flash-message />

        <section>
            <div>
                <!-- Start coding here -->
                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <div class="flex items-center justify-between p-4 pr-10">
                        <div class="flex space-x-6">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" wire:model.live.debounce.300ms="search"
                                    class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search Request..." required="">
                            </div>
                            <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium text-gray-900 dark:text-white">Purpose:</label>
                                <select wire:model.live="purposeFilter"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">All Purposes</option>
                                    <option value="Pet Food">Pet Food</option>
                                    <option value="Pet Toys">Pet Toys</option>
                                    <option value="Pet Care">Pet Care</option>
                                    <option value="Pet Health">Pet Health</option>
                                    <option value="Pet Grooming">Pet Grooming</option>
                                    <option value="Pet Bedding">Pet Bedding</option>
                                    <option value="Pet Training">Pet Training</option>
                                    <option value="Pet Safety">Pet Safety</option>
                                    <option value="Office Supplies">Office Supplies</option>
                                    <option value="Packaging">Packaging</option>
                                    <option value="Equipment">Equipment</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Sent From
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Purpose
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Description
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Requested Date
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Requested By
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    use App\Enums\Enum\PermissionEnum;
                                @endphp

                                @forelse ($request_slips as $request_slip)
                                    <tr wire:key
                                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                        <td class="px-6 py-4">
                                            <span
                                                class="
                                                                        px-2 py-1 rounded-full text-white text-xs font-semibold
                                                                        @if ($request_slip->status === 'pending') bg-yellow-500
                                                                        @elseif ($request_slip->status === 'approved') bg-green-600
                                                                        @elseif ($request_slip->status === 'rejected') bg-red-600
                                                                            @else bg-gray-500 @endif">

                                                {{ ucfirst($request_slip->status) }}
                                            </span>
                                        </td>
                                        <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $request_slip->sentFrom->name ?? 'N/A' }}
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ $request_slip->purpose }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ Str::limit($request_slip->description, 25) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $request_slip->created_at }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $request_slip->requestedBy->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('requisition.requestslip.view', $request_slip->id) }}"
                                                class="font-medium px-1 text-blue-600 dark:text-blue-500 hover:underline">View</a>
                                            @can(PermissionEnum::DELETE_REQUEST_SLIP->value)
                                                <a href="#" wire:click='delete({{ $request_slip->id }})'
                                                    class="font-medium px-1 text-red-600 dark:text-red-500 hover:underline">Delete</a>
                                            @endcan
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            No request slips found.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>

                    <div class="py-4 px-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Per Page Selection -->
                            <div class="flex items-center space-x-4">
                                <label for="perPage" class="text-sm font-medium text-gray-900 dark:text-white">Per
                                    Page</label>
                                <select id="perPage" wire:model.live="perPage"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>

                            <!-- Pagination Links -->
                            <div>
                                {{ $request_slips->links() }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
    </div>
    </section>
</div>
</div>

</div>
