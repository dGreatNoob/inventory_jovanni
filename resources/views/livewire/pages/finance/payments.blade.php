
<div class="">

    <div class="">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Payments Management</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Record and track payment transactions</p>
                </div>
                <div class="flex flex-row items-center space-x-3">
                    <flux:button
                        wire:click="openCreatePanel"
                        variant="primary"
                        class="flex items-center gap-2 whitespace-nowrap min-w-fit"
                        type="button"
                    >
                        <svg class="inline w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Record Payment</span>
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

        <!-- Record Payment Slide-in Panel -->
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
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500 dark:bg-green-400"></div>

                        <div class="ml-[0.25rem] flex h-full w-full flex-col bg-white shadow-xl dark:bg-zinc-900">
                            <header class="flex items-start justify-between border-b border-gray-200 px-6 py-5 dark:border-zinc-700">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            {{ $editingPaymentId ? 'Edit Payment' : 'Record New Payment' }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $editingPaymentId ? 'Update payment details.' : 'Record a new payment transaction.' }}
                                        </p>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 dark:text-gray-500 dark:hover:bg-zinc-800 dark:hover:text-gray-200"
                                    @click="open = false; $wire.closeCreatePanel()"
                                    aria-label="Close payment panel"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </header>

                            <div class="flex-1 overflow-hidden">
                                <form wire:submit.prevent="{{ $editingPaymentId ? 'update' : 'save' }}" class="flex h-full flex-col">
                                    <div class="flex-1 overflow-y-auto px-6 py-6">
                                        <div class="space-y-8">
                                            <!-- Payment Details -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Details</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Enter the payment information below.</p>
                                                </div>

                                                <div class="space-y-4">
                                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <label for="payment_ref" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Payment Reference
                                                            </label>
                                                            <input
                                                                type="text"
                                                                id="payment_ref"
                                                                wire:model="payment_ref"
                                                                class="block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white cursor-not-allowed"
                                                                readonly
                                                            />
                                                            @error('payment_ref') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>

                                                        <div>
                                                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Payment Amount
                                                            </label>
                                                            <input
                                                                type="number"
                                                                step="0.01"
                                                                id="amount"
                                                                wire:model="amount"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                                placeholder="0.00"
                                                            />
                                                            @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <label for="payment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Payment Date
                                                            </label>
                                                            <input
                                                                type="date"
                                                                id="payment_date"
                                                                wire:model="payment_date"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                            />
                                                            @error('payment_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>

                                                        <div>
                                                            <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Payment Method
                                                            </label>
                                                            <select
                                                                id="payment_method"
                                                                wire:model="payment_method"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                            >
                                                                <option value="">Select payment method</option>
                                                                <option value="Cash">Cash</option>
                                                                <option value="Bank Transfer">Bank Transfer</option>
                                                                <option value="Credit Card">Credit Card</option>
                                                                <option value="Check">Check</option>
                                                                <option value="GCash">GCash</option>
                                                                <option value="Maya">Maya</option>
                                                                <option value="Others">Others</option>
                                                            </select>
                                                            @error('payment_method') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Type
                                                        </label>
                                                        <select
                                                            id="type"
                                                            wire:model="type"
                                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                        >
                                                            <option value="">Select Type</option>
                                                            <option value="Payable">Payable</option>
                                                            <option value="Receivable">Receivable</option>
                                                        </select>
                                                        @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div>
                                                        <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Remarks
                                                        </label>
                                                        <textarea
                                                            id="remarks"
                                                            wire:model="remarks"
                                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm resize-y"
                                                            style="min-height: 80px;"
                                                            placeholder="Additional notes or details"
                                                        ></textarea>
                                                        @error('remarks') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-200 bg-white px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                Review details before {{ $editingPaymentId ? 'updating' : 'recording' }} the payment.
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
                                                    {{ $editingPaymentId ? 'Update Payment' : 'Record Payment' }}
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

        <x-collapsible-card title="Payments List" open="true" size="full">
            <div class="flex items-center justify-between p-4 pr-10">
                <div class="flex space-x-6">
                    <div class="relative">
                        <x-input type="text" wire:model.live="search" name="search" label="Search" placeholder="Search payments..." />
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="grid gap-4 mb-4 md:grid-cols-4 px-4">
                <div>
                    <x-select wire:model.live="filterPaymentMethod" name="filterPaymentMethod" label="Filter by Payment Method">
                        <option value="">All Methods</option>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Check">Check</option>
                        <option value="GCash">GCash</option>
                        <option value="Maya">Maya</option>
                        <option value="Others">Others</option>
                    </x-select>
                </div>
                <div>
                    <x-select wire:model.live="filterStatus" name="filterStatus" label="Filter by Status">
                        <option value="">All Statuses</option>
                        @foreach($paymentStatuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <x-input type="date" wire:model.live="filterDateFrom" name="filterDateFrom" label="Date From" />
                </div>
                <div>
                    <x-input type="date" wire:model.live="filterDateTo" name="filterDateTo" label="Date To" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-2 py-2 whitespace-nowrap">Payment Ref</th>
                            <th class="px-2 py-2 whitespace-nowrap">Amount</th>
                            <th class="px-2 py-2 whitespace-nowrap">Payment Date</th>
                            <th class="px-2 py-2 whitespace-nowrap">Payment Method</th>
                            <th class="px-2 py-2 whitespace-nowrap">Remarks</th>
                            <th class="px-2 py-2 whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200 text-xs">
                            <td class="px-2 py-2 whitespace-nowrap">{{ $payment->payment_ref }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $payment->payment_method }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $payment->remarks ?? '-' }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <x-button type="button" wire:click="edit({{ $payment->id }})" variant="warning" size="sm">Edit</x-button>
                                    <x-button type="button" wire:click="confirmDelete({{ $payment->id }})" variant="danger" size="sm">Delete</x-button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-2 py-2 text-center text-gray-500 whitespace-nowrap">No payments found.</td>
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
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </x-collapsible-card>


        <!-- Delete Modal -->
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
                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this payment? This will restore the balance to the linked record.</h3>
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
