<x-slot:header>Request Slip</x-slot:header>
<x-slot:subheader>Request Inbound or Outbound</x-slot:subheader>
<div>
    <div>
        <!-- New Request Button -->
        <div class="flex justify-end mb-4">
            <x-button type="button" variant="primary" wire:click="$set('showRequestSlipPanel', true)">
                + New Request
            </x-button>
        </div>

        <!-- Create Request Slip Slideover -->
        <div
            x-data="{ open: @entangle('showRequestSlipPanel').live }"
            x-cloak
            x-on:keydown.escape.window="if (open) { open = false; $wire.closeRequestSlipPanel(); }"
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
                        @click="open = false; $wire.closeRequestSlipPanel()"
                    ></div>

                    <section
                        x-show="open"
                        x-transition:enter="transform transition ease-in-out duration-300"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-300"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                        class="relative ml-auto flex h-full w-full max-w-4xl"
                    >
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 dark:bg-indigo-400"></div>

                        <div class="ml-[0.25rem] flex h-full w-full flex-col bg-white shadow-xl dark:bg-zinc-900">
                            <header class="flex items-start justify-between border-b border-gray-200 px-6 py-5 dark:border-zinc-700">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            Create Request Slip
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            Fill in the request details to create a new request slip.
                                        </p>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-gray-500 dark:hover:bg-zinc-800 dark:hover:text-gray-200"
                                    @click="open = false; $wire.closeRequestSlipPanel()"
                                    aria-label="Close request slip panel"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </header>

                            <div class="flex-1 overflow-hidden">
                                <form wire:submit.prevent="create" class="flex h-full flex-col">
                                    <div class="flex-1 overflow-y-auto px-6 py-6">
                                        <div class="space-y-8">
                                            <!-- Request Details -->
                                            <section class="space-y-4">
                                                <div>
                                                    <flux:heading size="md" class="text-gray-900 dark:text-white">Request Details</flux:heading>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Core information about the request purpose and description.</p>
                                                </div>

                                                <div class="space-y-4">
                                                    <div>
                                                        <label for="purpose" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Purpose <span class="text-red-500">*</span></label>
                                                        <select
                                                            id="purpose"
                                                            wire:model="purpose"
                                                            name="purpose"
                                                            required
                                                            class="block w-full h-11 px-3 py-2 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                        >
                                                            <option value="">Select a Purpose</option>
                                                            <option value="Stock Replenishment">Request stock replenishment for SM stores</option>
                                                            <option value="New Product Launch">Request new bag collection for store launch</option>
                                                            <option value="Store Transfer">Transfer bags between SM store locations</option>
                                                            <option value="Return/Exchange">Return or exchange defective/damaged items</option>
                                                            <option value="Quality Control">Request items for quality inspection</option>
                                                            <option value="Display Materials">Request display fixtures and marketing materials</option>
                                                            <option value="Consignment Adjustment">Adjust consignment inventory levels</option>
                                                            <option value="Inventory Audit">Request for inventory counting and audit</option>
                                                            <option value="Store Setup">Request bags for new store opening</option>
                                                            <option value="Seasonal Collection">Request seasonal bag collection</option>
                                                            <option value="Other">Other request purposes</option>
                                                        </select>
                                                        @error('purpose') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                    </div>

                                                    <div>
                                                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description <span class="text-red-500">*</span></label>
                                                        <textarea
                                                            id="description"
                                                            wire:model="description"
                                                            name="description"
                                                            rows="6"
                                                            required
                                                            placeholder="Enter request description..."
                                                            class="block w-full px-3 py-2 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 sm:text-sm placeholder:text-gray-400"
                                                        ></textarea>
                                                        @error('description') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                    </div>
                                                </div>
                                            </section>

                                            <!-- Department Information -->
                                            <section class="space-y-4">
                                                <div>
                                                    <flux:heading size="md" class="text-gray-900 dark:text-white">Department Information</flux:heading>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Sender and receiver department details.</p>
                                                </div>

                                                <div class="space-y-4">
                                                    <div>
                                                        <label for="sent_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Sender</label>
                                                        <input
                                                            type="text"
                                                            id="sent_from"
                                                            wire:model="sent_from"
                                                            name="sent_from"
                                                            readonly
                                                            class="block w-full h-11 px-3 py-2 rounded-lg border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm"
                                                        />
                                                    </div>

                                                    <div>
                                                        <label for="sent_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Receiver <span class="text-red-500">*</span></label>
                                                        <select
                                                            id="sent_to"
                                                            wire:model="sent_to"
                                                            name="sent_to"
                                                            required
                                                            class="block w-full h-11 px-3 py-2 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                        >
                                                            <option value="">Select Receiver Department</option>
                                                            @foreach($this->departments as $department)
                                                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('sent_to') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-200 bg-white px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                Review details carefully before creating a new request slip.
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <flux:button type="button" wire:click="closeRequestSlipPanel" variant="ghost">
                                                    Cancel
                                                </flux:button>

                                                <flux:button type="submit" variant="primary">
                                                    Create Request
                                                </flux:button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </template>
        </div>

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
                                    <option value="Stock Replenishment">Stock Replenishment</option>
                                    <option value="New Product Launch">New Product Launch</option>
                                    <option value="Store Transfer">Store Transfer</option>
                                    <option value="Return/Exchange">Return/Exchange</option>
                                    <option value="Quality Control">Quality Control</option>
                                    <option value="Display Materials">Display Materials</option>
                                    <option value="Consignment Adjustment">Consignment Adjustment</option>
                                    <option value="Inventory Audit">Inventory Audit</option>
                                    <option value="Store Setup">Store Setup</option>
                                    <option value="Seasonal Collection">Seasonal Collection</option>
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
