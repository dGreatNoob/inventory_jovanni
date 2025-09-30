<x-slot:header>Payables</x-slot:header>
<x-slot:subheader>Payable Management</x-slot:subheader>
<div class="">
    <div class="">
        <x-collapsible-card title="Add Payable" open="false" size="full">
            <form wire:submit.prevent="save" x-show="open" x-transition>
                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <x-input type="text" wire:model.defer="reference_id" name="reference_id" label="Invoice Number" placeholder="Enter invoice number" readonly class="bg-gray-100 cursor-not-allowed"/>
                        @error('reference_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                    <x-select wire:model.defer="supplier" name="supplier" label="Supplier">
                            <option value="">Select Supplier</option>
                            <option value="Supplier 1">Supplier 1</option>
                            <option value="Supplier 2">Supplier 2</option>
                            <option value="Supplier 3">Supplier 3</option>
                            <option value="Supplier 4">Supplier 4</option>
                            <option value="Supplier 5">Supplier 5</option>
                            <option value="Supplier 6">Supplier 6</option>
                            <option value="Supplier 7">Supplier 7</option>
                        </x-select>
                        @error('supplier') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                    <x-select wire:model.defer="purchase_order" name="purchase_oder" label="Purchase Order">
                            <option value="">Select Purchase Order</option>
                            <option value="PO #1">PO #1</option>
                            <option value="PO #2">PO #2</option>
                            <option value="PO #3">PO #3</option>
                            <option value="PO #4">PO #4</option>
                            <option value="PO #5">PO #5</option>
                            <option value="PO #6">PO #6</option>
                            <option value="PO #7">PO #7</option>
                        </x-select>
                        @error('purchase_order') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input type="text" wire:model.defer="party" name="party" label="Description" placeholder="Enter Description" />
                        @error('party') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input type="date" wire:model.defer="date" name="date" label="Date" />
                        @error('date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input type="date" wire:model.defer="due_date" name="due_date" label="Due Date" />
                        @error('due_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input type="number" step="0.01" wire:model.defer="amount" name="amount" label="Amount" placeholder="Enter amount" />
                        @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-select wire:model.defer="payment_method" name="payment_method" label="Payment Method">
                            <option value="">Select payment method</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Check">Check</option>
                            <option value="GCash">GCash</option>
                            <option value="Maya">Maya</option>
                            <option value="Others">Others</option>
                        </x-select>
                        @error('payment_method') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <x-input type="textarea" wire:model.defer="remarks" name="remarks" label="Remarks" placeholder="Enter remarks" />
                        @error('remarks') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex justify-end">
                    <x-button type="submit" variant="primary">Submit</x-button>
                </div>
            </form>
        </x-collapsible-card>

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

        <x-collapsible-card title="Payables List" open="true" size="full">
            <div class="flex items-center justify-between p-4 pr-10">
                <div class="flex space-x-6">
                    <div class="relative">
                        <x-input type="text" wire:model.live="search" name="search" label="Search" placeholder="Search payables..." />
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-2 py-2 whitespace-nowrap">Invoice #</th>
                            <th class="px-2 py-2 whitespace-nowrap">Supplier</th>
                            <th class="px-2 py-2 whitespace-nowrap">PO</th>
                            <th class="px-2 py-2 whitespace-nowrap">Description</th>
                            <th class="px-2 py-2 whitespace-nowrap">Date</th>
                            <th class="px-2 py-2 whitespace-nowrap">Due Date</th>
                            <th class="px-2 py-2 whitespace-nowrap">Amount</th>
                            <th class="px-2 py-2 whitespace-nowrap">Balance</th>
                            <th class="px-2 py-2 whitespace-nowrap">Payment Method</th>
                            <th class="px-2 py-2 whitespace-nowrap">Status</th>
                            <th class="px-2 py-2 whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payables as $payable)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200 text-xs">
                            <td class="px-2 py-2 whitespace-nowrap">{{ $payable->reference_id }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $payable->supplier }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $payable->purchase_order }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $payable->party }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $payable->date }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $payable->due_date }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ number_format($payable->amount, 2) }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ number_format($payable->balance, 2) }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $payable->payment_method }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">
                                @php
                                    $statusColor = match($payable->status) {
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'paid' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'partial' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusColor }}">
                                    {{ ucfirst($payable->status) }}
                                </span>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <x-button type="button" wire:click="edit({{ $payable->id }})" variant="warning" size="sm">Edit</x-button>
                                    <x-button type="button" wire:click="confirmDelete({{ $payable->id }})" variant="danger" size="sm">Delete</x-button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="px-2 py-2 text-center text-gray-500 whitespace-nowrap">No payables found.</td>
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
                        {{ $payables->links() }}
                    </div>
                </div>
            </div>
        </x-collapsible-card>

        <div x-data="{ show: @entangle('showEditModal') }" x-show="show" x-cloak class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
            <div class="relative w-full max-w-2xl max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Edit Payable
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" wire:click="cancel">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="update">
                        <div class="p-6 space-y-6">
                            <div class="grid gap-6 mb-6 md:grid-cols-2">
                                <div>
                                    <x-input type="text" wire:model.defer="reference_id" name="reference_id" label="Invoice Number" placeholder="Enter Invoice Number" />
                                    @error('reference_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <x-select wire:model.defer="supplier" name="supplier" label="Supplier">
                                        <option value="">Select Supplier</option>
                                        <option value="Supplier 1">Supplier 1</option>
                                        <option value="Supplier 2">Supplier 2</option>
                                        <option value="Supplier 3">Supplier 3</option>
                                        <option value="Supplier 4">Supplier 4</option>
                                        <option value="Supplier 5">Supplier 5</option>
                                        <option value="Supplier 6">Supplier 6</option>
                                        <option value="Supplier 7">Supplier 7</option>
                                    </x-select>
                                    @error('supplier') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <x-select wire:model.defer="purchase_order" name="purchase_order" label="Purchase Order">
                                        <option value="">Select Purchase Order</option>
                                        <option value="PO #1">PO #1</option>
                                        <option value="PO #2">PO #2</option>
                                        <option value="PO #3">PO #3</option>
                                        <option value="PO #4">PO #4</option>
                                        <option value="PO #5">PO #5</option>
                                        <option value="PO #6">PO #6</option>
                                        <option value="PO #7">PO #7</option>
                                    </x-select>
                                    @error('purchase_order') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <x-input type="text" wire:model.defer="party" name="party" label="Party" placeholder="Enter party" />
                                    @error('party') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <x-input type="date" wire:model.defer="date" name="date" label="Date" />
                                    @error('date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <x-input type="date" wire:model.defer="due_date" name="due_date" label="Due Date" />
                                    @error('due_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <x-input type="number" step="0.01" wire:model.defer="amount" name="amount" label="Amount" placeholder="Enter amount" />
                                    @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <x-input type="number" step="0.01" wire:model.defer="balance" name="balance" label="Balance" placeholder="Enter balance" />
                                    @error('balance') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    @if($balance == 0)
                                        <x-input type="text" name="status" label="Status" value="Paid" readonly class="bg-gray-100 cursor-not-allowed" />
                                    @else
                                        <x-select wire:model.defer="status" name="status" label="Status">
                                            <option value="pending" @if($balance == $amount) selected @endif>Pending</option>
                                            <option value="partial" @if($balance < $amount && $balance > 0) selected @endif>Partial</option>
                                            <option value="cancelled" @if($status == 'cancelled') selected @endif>Cancelled</option>
                                        </x-select>
                                    @endif
                                    @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <x-input type="textarea" wire:model.defer="remarks" name="remarks" label="Remarks" placeholder="Enter remarks" />
                                    @error('remarks') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <x-button type="submit" variant="primary">Save changes</x-button>
                            <x-button type="button" wire:click="cancel" variant="secondary">Cancel</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

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
                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this payable?</h3>
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