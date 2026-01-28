
<div class="">
    <div class="">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Receivables Management</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Record and track receivable entries</p>
                </div>
                <div class="flex flex-row items-center space-x-3">
                    <flux:button
                        wire:click="openCreatePanel"
                        variant="primary"
                        class="flex items-center gap-2 whitespace-nowrap min-w-fit"
                        type="button"
                    >
                        <svg class="inline w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Add Receivable</span>
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Flash Message -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded dark:bg-green-900 dark:border-green-600 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        <!-- Add/Edit Receivable Slide-in Panel -->
        <div
            x-data="{ open: @entangle('showCreatePanel').live }"
            x-cloak
            x-on:keydown.escape.window="if (open) { open = false; $wire.closeCreatePanel(); }"
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
                        @click="open = false; $wire.closeCreatePanel()"
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
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 dark:bg-blue-400"></div>

                        <div class="ml-[0.25rem] flex h-full w-full flex-col bg-white shadow-xl dark:bg-zinc-900">
                            <header class="flex items-start justify-between border-b border-gray-200 px-6 py-5 dark:border-zinc-700">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            {{ $editingReceivableId ? 'Edit Receivable' : 'Add New Receivable' }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $editingReceivableId ? 'Update receivable details.' : 'Record a new receivable entry.' }}
                                        </p>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-gray-500 dark:hover:bg-zinc-800 dark:hover:text-gray-200"
                                    @click="open = false; $wire.closeCreatePanel()"
                                    aria-label="Close receivable panel"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </header>

                            <div class="flex-1 overflow-hidden">
                                <form wire:submit.prevent="{{ $editingReceivableId ? 'update' : 'save' }}" class="flex h-full flex-col">
                                    <div class="flex-1 overflow-y-auto px-6 py-6">
                                        <div class="space-y-8">
                                            <!-- Receivable Details -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Receivable Details</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Basic information about the receivable.</p>
                                                </div>

                                                <div class="space-y-4">
                                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <label for="reference_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Reference No
                                                            </label>
                                                            <input
                                                                type="text"
                                                                id="reference_id"
                                                                wire:model="reference_id"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                                placeholder="Enter reference number"
                                                            />
                                                            @error('reference_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>

                                                        <div>
                                                            <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Branch
                                                            </label>
                                                            <select
                                                                id="branch_id"
                                                                wire:model.live="branch_id"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                            >
                                                                <option value="">Select Branch</option>
                                                                @foreach(\App\Models\Branch::all() as $branch)
                                                                    @php
                                                                        $agents = $branch->currentAgents;
                                                                        $agentText = $agents->count() > 0 ? ' - ' . $agents->pluck('name')->join(', ') : ' - No agents assigned';
                                                                    @endphp
                                                                    <option value="{{ $branch->id }}">{{ $branch->name }}{{ $agentText }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('branch_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <label for="amount_due" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Amount Due
                                                            </label>
                                                            <input
                                                                type="number"
                                                                step="0.01"
                                                                id="amount_due"
                                                                wire:model="amount_due"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                                placeholder="0.00"
                                                            />
                                                            @error('amount_due') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>

                                                        <div>
                                                            <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Due Date
                                                            </label>
                                                            <input
                                                                type="date"
                                                                id="due_date"
                                                                wire:model="due_date"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                            />
                                                            @error('due_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>

                                            <!-- Payment Information -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Information</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Payment status and details.</p>
                                                </div>

                                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                    <div>
                                                        <label for="payment_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Payment Status
                                                        </label>
                                                        <select
                                                            id="payment_status"
                                                            wire:model="payment_status"
                                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                        >
                                                            <option value="pending">Pending</option>
                                                            <option value="paid">Paid</option>
                                                            <option value="overdue">Overdue</option>
                                                            <option value="cancelled">Cancelled</option>
                                                        </select>
                                                        @error('payment_status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </section>

                                            <!-- Description & Remarks -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Description & Remarks</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Additional details about this receivable.</p>
                                                </div>

                                                <div>
                                                    <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Remarks
                                                    </label>
                                                    <textarea
                                                        id="remarks"
                                                        wire:model="remarks"
                                                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm resize-y"
                                                        style="min-height: 80px;"
                                                        placeholder="Additional notes or details"
                                                    ></textarea>
                                                    @error('remarks') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                            </section>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-200 bg-white px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                Review details before {{ $editingReceivableId ? 'updating' : 'recording' }} the receivable.
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <flux:button
                                                    type="button"
                                                    variant="ghost"
                                                    wire:click="cancel"
                                                >
                                                    Cancel
                                                </flux:button>

                                                <flux:button
                                                    type="submit"
                                                    variant="primary"
                                                >
                                                    {{ $editingReceivableId ? 'Update Receivable' : 'Record Receivable' }}
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

        @if (session('success'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 4000)"
                class="transition duration-500 ease-in-out"
                x-transition
            >
                <x-flash-message />
            </div>
        @endif

        <x-collapsible-card title="Receivables List" open="true" size="full">
            <div class="flex items-center justify-between p-4 pr-10">
                <div class="flex space-x-6">
                    <div class="relative">
                        <x-input type="text" wire:model.live="search" name="search" label="Search" placeholder="Search receivables..." />
                    </div>
                    <div class="relative">
                        <x-select wire:model.live="filter_status" name="filter_status" label="Status">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="overdue">Overdue</option>
                            <option value="cancelled">Cancelled</option>
                        </x-select>
                    </div>
                    <div class="relative">
                        <x-input type="date" wire:model.live="filter_due_date_from" name="filter_due_date_from" label="Due Date From" />
                    </div>
                    <div class="relative">
                        <x-input type="date" wire:model.live="filter_due_date_to" name="filter_due_date_to" label="Due Date To" />
                    </div>
                    <div class="relative">
                        <x-select wire:model.live="filter_branch" name="filter_branch" label="Branch">
                            <option value="">All Branches</option>
                            @foreach(\App\Models\Branch::all() as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <div class="relative">
                        <x-select wire:model.live="filter_agent" name="filter_agent" label="Agent">
                            <option value="">All Agents</option>
                            @foreach(\App\Models\Agent::all() as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </x-select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-2 py-2 whitespace-nowrap">Reference No</th>
                            <th class="px-2 py-2 whitespace-nowrap">Branch</th>
                            <th class="px-2 py-2 whitespace-nowrap">Agent</th>
                            <th class="px-2 py-2 whitespace-nowrap">Amount Due</th>
                            <th class="px-2 py-2 whitespace-nowrap">Due Date</th>
                            <th class="px-2 py-2 whitespace-nowrap">Payment Status</th>
                            <th class="px-2 py-2 whitespace-nowrap">Remarks</th>
                            <th class="px-2 py-2 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receivables as $receivable)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200 text-xs">
                            <td class="px-2 py-2 whitespace-nowrap">{{ $receivable->reference_id }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $receivable->branch?->name ?? '-' }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $receivable->agent?->name ?? '-' }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ number_format($receivable->amount, 2) }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $receivable->due_date ? \Carbon\Carbon::parse($receivable->due_date)->format('M d, Y') : '-' }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">
                                @php
                                    $currentStatus = $statusUpdates[$receivable->id] ?? $receivable->status;
                                    $statusColor = match($currentStatus) {
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'paid' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'overdue' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <select wire:model.live="statusUpdates.{{$receivable->id}}" wire:change="updateReceivableStatus({{$receivable->id}})" class="px-2 py-1 rounded text-xs font-semibold border-0 {{ $statusColor }}">
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="overdue">Overdue</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ Str::limit($receivable->remarks, 30) }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <x-button type="button" wire:click="edit({{ $receivable->id }})" variant="warning" size="sm">Edit</x-button>
                                    <x-button type="button" wire:click="confirmDelete({{ $receivable->id }})" variant="danger" size="sm">Delete</x-button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-2 py-2 text-center text-gray-500 whitespace-nowrap">No receivables found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="py-4 px-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-900 dark:text-white">Per Page:</label>
                        <x-dropdown wire:model.live="perPage" name="perPage" :options="[
                            '5' => '5',
                            '10' => '10',
                            '25' => '25',
                            '50' => '50',
                            '100' => '100'
                        ]" />
                    </div>
                    <div>
                        {{ $receivables->links() }}
                    </div>
                </div>
            </div>
        </x-collapsible-card>


        <div x-data="{ show: @entangle('showDeleteModal') }" x-show="show" x-cloak class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
            <div class="relative w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" wire:click="closeDeleteModal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                    <div class="p-6 text-center">
                        <div class="mb-2">
                            <span class="font-semibold">Invoice Number:</span>
                            <span class="text-gray-700">{{ $deleteReferenceId }}</span>
                        </div>
                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this receivable?</h3>
                        <div class="flex justify-center space-x-2">
                            <x-button type="button" wire:click="delete" variant="danger">Yes, I'm sure</x-button>
                            <x-button type="button" wire:click="closeDeleteModal" variant="secondary">No, cancel</x-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 