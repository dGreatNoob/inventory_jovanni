<x-slot:header>Sales Profiles</x-slot:header>
<x-slot:subheader>Sales Profile Management</x-slot:subheader>
<div class="">
    <!-- Under Revision Notice -->
    <div class="mb-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">
                    Module Under Revision
                </h3>
                <div class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                    <p>The Sales Management module is currently under revision and may not be fully functional. Some features may be incomplete or unavailable. Please use the Product Management module for core inventory operations.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="">
        <x-collapsible-card title="Create Sales Profile" open="false" size="full">
            <div x-show="open" x-transition>
                <form wire:submit.prevent="create">
                    <div class="grid gap-6 mb-6 md:grid-cols-2">
                        <div>
                            <x-input type="date" wire:model="sales_date" name="sales_date" label="Sales Date" />
                            @error('sales_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <x-dropdown wire:model="branch_ids" name="branch_ids" label="Branch" :options="$branches->pluck('name', 'id')" placeholder="Select Branch" multiselect />
                            @error('branch_ids') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <x-dropdown wire:model="agent_id" name="agent_id" label="Agent" :options="$agents->pluck('name', 'id')" placeholder="Select Agent" />
                            @error('agent_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <x-input type="text" wire:model="remarks" name="remarks" label="Remarks" placeholder="Enter remarks" />
                            @error('remarks') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-4">Sales Items</h4>

                        <!-- Add Item Form -->
                        <div class="grid gap-4 mb-4 md:grid-cols-4">
                            <div>
                                <x-dropdown wire:model="selectedProduct" name="selectedProduct" label="Product" :options="$products->pluck('name', 'id')" placeholder="Select Product" />
                            </div>
                            <div>
                                <x-input type="number" wire:model="quantity" name="quantity" label="Quantity" min="1" />
                            </div>
                            <div>
                                <x-input type="number" wire:model="unitPrice" name="unitPrice" label="Unit Price" step="0.01" min="0" />
                            </div>
                            <div class="flex items-end mb-4">
                                <x-button type="button" wire:click="addItem" variant="secondary" size="sm">Add Item</x-button>
                            </div>
                        </div>

                        <!-- Items List -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Product</th>
                                        <th scope="col" class="px-6 py-3">Quantity</th>
                                        <th scope="col" class="px-6 py-3">Unit Price</th>
                                        <th scope="col" class="px-6 py-3">Total</th>
                                        <th scope="col" class="px-6 py-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $index => $item)
                                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                        <td class="px-6 py-4">{{ $item['product_name'] }}</td>
                                        <td class="px-6 py-4">{{ $item['quantity'] }}</td>
                                        <td class="px-6 py-4">₱{{ number_format($item['unit_price'], 2) }}</td>
                                        <td class="px-6 py-4">₱{{ number_format($item['total_price'], 2) }}</td>
                                        <td class="px-6 py-4">
                                            <x-button type="button" wire:click="removeItem({{ $index }})" variant="danger" size="sm">Remove</x-button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-100 dark:bg-gray-800">
                                        <td colspan="3" class="px-6 py-4 font-semibold">Total Amount:</td>
                                        <td class="px-6 py-4 font-semibold">₱{{ number_format(collect($items)->sum('total_price'), 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-button type="submit" variant="primary">Create Sales Profile</x-button>
                    </div>
                </form>
            </div>
        </x-collapsible-card>

        @if (session('message'))
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

        @if (session('error'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 4000)"
                class="transition duration-500 ease-in-out"
                x-transition
            >
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <x-collapsible-card title="Sales Profiles List" open="true" size="full">
            <div class="flex items-center justify-between p-4 pr-10">
                <div class="flex space-x-6">
                    <div class="relative">
                        <x-input type="text" wire:model.live="search" name="search" label="Search" placeholder="Search sales profiles..." />
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Sales Number</th>
                            <th scope="col" class="px-6 py-3">Date</th>
                            <th scope="col" class="px-6 py-3">Branch</th>
                            <th scope="col" class="px-6 py-3">Agent</th>
                            <th scope="col" class="px-6 py-3">Total Amount</th>
                            <th scope="col" class="px-6 py-3">Items Count</th>
                            <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesProfiles as $profile)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <td class="px-6 py-4 font-medium">{{ $profile->sales_number }}</td>
                            <td class="px-6 py-4">{{ $profile->sales_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4">{{ $profile->branches->count() }}</td>
                            <td class="px-6 py-4">{{ $profile->agent->name }}</td>
                            <td class="px-6 py-4">₱{{ number_format($profile->total_amount, 2) }}</td>
                            <td class="px-6 py-4">{{ $profile->items->count() }}</td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <x-button type="button" wire:click="view({{ $profile->id }})" variant="info" size="sm">View</x-button>
                                    <x-button type="button" wire:click="edit({{ $profile->id }})" variant="warning" size="sm">Edit</x-button>
                                    <x-button type="button" wire:click="confirmDelete({{ $profile->id }})" variant="danger" size="sm">Delete</x-button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Edit Modal -->
            <div x-data="{ show: @entangle('showEditModal') }" x-show="show" x-cloak class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                <div class="relative w-full max-w-4xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Edit Sales Profile
                            </h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" wire:click="cancel">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <div class="p-6 space-y-6 max-h-96 overflow-y-auto">
                            <form wire:submit.prevent="update">
                                <div class="grid gap-6 mb-6 md:grid-cols-2">
                                    <div>
                                        <x-input type="date" wire:model="sales_date" name="sales_date" label="Sales Date" />
                                        @error('sales_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <x-dropdown wire:model="branch_ids" name="branch_ids" label="Branch" :options="$branches->pluck('name', 'id')" multiselect />
                                        @error('branch_ids') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <x-dropdown wire:model="agent_id" name="agent_id" label="Agent" :options="$agents->pluck('name', 'id')" />
                                        @error('agent_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <x-input type="text" wire:model="remarks" name="remarks" label="Remarks" />
                                        @error('remarks') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Items Section for Edit -->
                                <div class="mb-6">
                                    <h4 class="text-lg font-semibold mb-4">Sales Items</h4>

                                    <!-- Add Item Form -->
                                    <div class="grid gap-4 mb-4 md:grid-cols-4">
                                        <div>
                                            <x-dropdown wire:model="selectedProduct" name="selectedProduct" label="Product" :options="$products->pluck('name', 'id')" placeholder="Select Product" />
                                        </div>
                                        <div>
                                            <x-input type="number" wire:model="quantity" name="quantity" label="Quantity" min="1" />
                                        </div>
                                        <div>
                                            <x-input type="number" wire:model="unitPrice" name="unitPrice" label="Unit Price" step="0.01" min="0" />
                                        </div>
                                        <div class="flex items-end mb-4">
                                            <x-button type="button" wire:click="addItem" variant="secondary" size="sm">Add Item</x-button>
                                        </div>
                                    </div>

                                    <!-- Items List -->
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                            <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3">Product</th>
                                                    <th scope="col" class="px-6 py-3">Quantity</th>
                                                    <th scope="col" class="px-6 py-3">Unit Price</th>
                                                    <th scope="col" class="px-6 py-3">Total</th>
                                                    <th scope="col" class="px-6 py-3">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($items as $index => $item)
                                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                                    <td class="px-6 py-4">{{ $item['product_name'] }}</td>
                                                    <td class="px-6 py-4">{{ $item['quantity'] }}</td>
                                                    <td class="px-6 py-4">₱{{ number_format($item['unit_price'], 2) }}</td>
                                                    <td class="px-6 py-4">₱{{ number_format($item['total_price'], 2) }}</td>
                                                    <td class="px-6 py-4">
                                                        <x-button type="button" wire:click="removeItem({{ $index }})" variant="danger" size="sm">Remove</x-button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-gray-100 dark:bg-gray-800">
                                                    <td colspan="3" class="px-6 py-4 font-semibold">Total Amount:</td>
                                                    <td class="px-6 py-4 font-semibold">₱{{ number_format(collect($items)->sum('total_price'), 2) }}</td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
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
            </div>

            <!-- View Modal -->
            <div x-data="{ show: @entangle('showViewModal') }" x-show="show" x-cloak class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                <div class="relative w-full max-w-4xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                View Sales Profile
                            </h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" wire:click="cancel">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <div class="p-6 space-y-6 max-h-96 overflow-y-auto">
                            <div class="grid gap-6 mb-6 md:grid-cols-2">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sales Number</label>
                                    <p class="text-gray-700 dark:text-gray-300">{{ $viewingSalesProfile->sales_number ?? '' }}</p>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sales Date</label>
                                    <p class="text-gray-700 dark:text-gray-300">{{ $viewingSalesProfile && $viewingSalesProfile->sales_date ? $viewingSalesProfile->sales_date->format('M d, Y') : '' }}</p>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Branches</label>
                                    <p class="text-gray-700 dark:text-gray-300">{{ $viewingSalesProfile ? $viewingSalesProfile->branches->pluck('name')->join(', ') : '' }}</p>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Agent</label>
                                    <p class="text-gray-700 dark:text-gray-300">{{ $viewingSalesProfile && $viewingSalesProfile->agent ? $viewingSalesProfile->agent->name : '' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Remarks</label>
                                    <p class="text-gray-700 dark:text-gray-300">{{ $viewingSalesProfile ? ($viewingSalesProfile->remarks ?? 'No remarks') : '' }}</p>
                                </div>
                            </div>

                            <!-- Items Section for View -->
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold mb-4">Sales Items</h4>

                                <!-- Items List -->
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-6 py-3">Product</th>
                                                <th scope="col" class="px-6 py-3">Quantity</th>
                                                <th scope="col" class="px-6 py-3">Unit Price</th>
                                                <th scope="col" class="px-6 py-3">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($viewingSalesProfile && $viewingSalesProfile->items)
                                                @foreach($viewingSalesProfile->items as $item)
                                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                                    <td class="px-6 py-4">{{ $item->product ? $item->product->name : 'Unknown Product' }}</td>
                                                    <td class="px-6 py-4">{{ $item->quantity }}</td>
                                                    <td class="px-6 py-4">₱{{ number_format($item->unit_price, 2) }}</td>
                                                    <td class="px-6 py-4">₱{{ number_format($item->total_price, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-gray-100 dark:bg-gray-800">
                                                <td colspan="3" class="px-6 py-4 font-semibold">Total Amount:</td>
                                                <td class="px-6 py-4 font-semibold">₱{{ number_format($viewingSalesProfile ? ($viewingSalesProfile->total_amount ?? 0) : 0, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                                <x-button type="button" wire:click="cancel" variant="secondary">Close</x-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Modal -->
            <div x-data="{ show: @entangle('showDeleteModal') }" x-show="show" x-cloak class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                <div class="relative w-full max-w-md max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" wire:click="cancel">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                        <div class="p-6 text-center">
                            <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this sales profile?</h3>
                            <div class="flex justify-center space-x-2">
                                <x-button type="button" wire:click="delete" variant="danger">Yes, I'm sure</x-button>
                                <x-button type="button" wire:click="cancel" variant="secondary">No, cancel</x-button>
                            </div>
                        </div>
                    </div>
                </div>
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
                        {{ $salesProfiles->links() }}
                    </div>
                </div>
            </div>
        </x-collapsible-card>
    </div>
</div>
