<x-slot:header>Branch Management</x-slot:header>
<x-slot:subheader>Profile</x-slot:subheader>

<div class="pt-4">
    <div class="space-y-6">
        <!-- Create New Branch Form -->
        <section class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Create New Branch</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add a new branch to the system</p>
            </div>
            <form wire:submit.prevent="submit" class="p-6">
                <!-- Basic Information -->
                <div class="mb-10">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-6">Basic Information</h4>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch Name</label>
                            <input type="text" id="name" wire:model="name"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Main Branch" required />
                            @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code</label>
                            <input type="text" id="code" wire:model="code"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="BR-001" required />
                            @error('code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>
                    <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                            <input type="text" id="category" wire:model="category"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Retail" required />
                            @error('category') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                        <input type="text" id="address" wire:model="address"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="123 Main Street, City" required />
                            @error('address') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                            <label for="contact_num" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Number</label>
                        <input type="text" id="contact_num" wire:model="contact_num"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="+1 234 567 8900" />
                            @error('contact_num') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                            <label for="manager_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Manager Name</label>
                        <input type="text" id="manager_name" wire:model="manager_name"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="John Doe" />
                            @error('manager_name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Subclass Information -->
                <div class="mb-10">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-6">Subclass Information</h4>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                            <label for="subclass1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subclass 1</label>
                                <input type="text" id="subclass1" wire:model="subclass1"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Subclass 1" />
                            @error('subclass1') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        <div>
                            <label for="subclass2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subclass 2</label>
                                <input type="text" id="subclass2" wire:model="subclass2"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Subclass 2" />
                            @error('subclass2') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        <div>
                            <label for="subclass3" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subclass 3</label>
                                <input type="text" id="subclass3" wire:model="subclass3"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Subclass 3" />
                            @error('subclass3') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        <div>
                            <label for="subclass4" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subclass 4</label>
                                <input type="text" id="subclass4" wire:model="subclass4"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Subclass 4" />
                            @error('subclass4') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mb-10">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-6">Additional Information</h4>
                    
                    <!-- Row 1: Remarks and Batch -->
                    <div class="grid gap-6 md:grid-cols-2 mb-6">
                        <div>
                            <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                            <input type="text" id="remarks" wire:model="remarks"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Additional remarks" />
                            @error('remarks') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    <div>
                            <label for="batch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batch</label>
                        <input type="text" id="batch" wire:model="batch"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Batch number" />
                            @error('batch') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Row 2: Branch Code and Company Name -->
                    <div class="grid gap-6 md:grid-cols-2 mb-6">
                    <div>
                            <label for="branch_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch Code</label>
                        <input type="text" id="branch_code" wire:model="branch_code"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Branch Code" />
                            @error('branch_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company Name</label>
                        <input type="text" id="company_name" wire:model="company_name"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Company Name" />
                            @error('company_name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Row 3: Company TIN and Department Code -->
                    <div class="grid gap-6 md:grid-cols-2 mb-6">
                    <div>
                            <label for="company_tin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company TIN</label>
                        <input type="text" id="company_tin" wire:model="company_tin"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Company TIN" />
                            @error('company_tin') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                            <label for="dept_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department Code</label>
                        <input type="text" id="dept_code" wire:model="dept_code"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Department Code" />
                            @error('dept_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Row 4: Pull Out Address and Vendor Code -->
                    <div class="grid gap-6 md:grid-cols-2">
                    <div>
                            <label for="pull_out_addresse" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pull Out Address</label>
                        <input type="text" id="pull_out_addresse" wire:model="pull_out_addresse"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Pull Out Address" />
                            @error('pull_out_addresse') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                            <label for="vendor_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vendor Code</label>
                        <input type="text" id="vendor_code" wire:model="vendor_code"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Vendor Code" />
                            @error('vendor_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-end">
                        <flux:button type="submit">
                            Add Branch
                        </flux:button>
                    </div>
                </div>
            </form>
        </section>

        @if (session()->has('message'))
            <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('message') }}
            </div>
        @endif

        <!-- Branch List -->
        <section class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Branch List</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage existing branches</p>
            </div>
            
            <!-- Search and Filter Section -->
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                        <!-- Search Input -->
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
                                class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                                placeholder="Search branches...">
                        </div>
                    </div>

                    <!-- Per Page Selector -->
                    <div class="flex items-center space-x-3">
                        <label class="text-sm font-medium text-gray-900 dark:text-white whitespace-nowrap">
                            Per Page
                        </label>
                        <select wire:model.live="perPage"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-24 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                                    <option value="5">5</option>
                            <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>

            <!-- Results Info -->
            <div class="px-6 py-3 text-sm text-gray-700 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50">
                @if($items->total() > 0)
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $items->total() }}</span> branches found
                @else
                    <span class="font-semibold text-gray-900 dark:text-white">No branches found</span>
                @endif
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Branch Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Address</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Manager</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($items as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $item->code }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->category }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $item->address }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->contact_num ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->manager_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <flux:button wire:click.prevent="edit({{ $item->id }})" variant="outline" size="sm">
                                            Edit
                                        </flux:button>
                                        <flux:button wire:click.prevent="confirmDelete({{ $item->id }})" variant="outline" size="sm" class="text-red-600 hover:text-red-700">
                                            Delete
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                        <svg class="w-16 h-16 mb-4 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            No branches found
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Try adjusting your search criteria
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $items->links() }}
            </div>
        </section>

        <!-- Edit Modal -->
        @if($showEditModal)
            <div class="fixed inset-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto flex items-center justify-center">
                <div class="relative w-full max-w-2xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <!-- Modal Header -->
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Branch</h3>
                            <button type="button" wire:click="cancel"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-6 space-y-6 max-h-96 overflow-y-auto">
                            <!-- Basic Information -->
                            <div>
                                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h4>
                                <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                        <label for="edit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch Name</label>
                                    <input type="text" wire:model="edit_name" id="edit_name"
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Branch Name" required />
                                        @error('edit_name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code</label>
                                        <input type="text" wire:model="edit_code" id="edit_code" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Code" required />
                                        @error('edit_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                        <input type="text" wire:model="edit_category" id="edit_category" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Category" required />
                                        @error('edit_category') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                        <label for="edit_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                                    <input type="text" wire:model="edit_address" id="edit_address"
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Address" required />
                                        @error('edit_address') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                        <label for="edit_contact_num" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Number</label>
                                    <input type="text" wire:model="edit_contact_num" id="edit_contact_num"
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Contact Number" />
                                        @error('edit_contact_num') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_manager_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Manager Name</label>
                                        <input type="text" wire:model="edit_manager_name" id="edit_manager_name" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Manager Name" />
                                        @error('edit_manager_name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Subclass Information -->
                            <div>
                                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Subclass Information</h4>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="edit_subclass1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subclass 1</label>
                                        <input type="text" wire:model="edit_subclass1" id="edit_subclass1" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Subclass 1" />
                                        @error('edit_subclass1') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_subclass2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subclass 2</label>
                                        <input type="text" wire:model="edit_subclass2" id="edit_subclass2" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Subclass 2" />
                                        @error('edit_subclass2') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_subclass3" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subclass 3</label>
                                        <input type="text" wire:model="edit_subclass3" id="edit_subclass3" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Subclass 3" />
                                        @error('edit_subclass3') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                        <label for="edit_subclass4" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subclass 4</label>
                                        <input type="text" wire:model="edit_subclass4" id="edit_subclass4" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Subclass 4" />
                                        @error('edit_subclass4') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div>
                                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Additional Information</h4>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="edit_remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                                        <input type="text" wire:model="edit_remarks" id="edit_remarks" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Remarks" />
                                        @error('edit_remarks') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_batch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batch</label>
                                        <input type="text" wire:model="edit_batch" id="edit_batch" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Batch" />
                                        @error('edit_batch') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_branch_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch Code</label>
                                        <input type="text" wire:model="edit_branch_code" id="edit_branch_code" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Branch Code" />
                                        @error('edit_branch_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company Name</label>
                                        <input type="text" wire:model="edit_company_name" id="edit_company_name" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Company Name" />
                                        @error('edit_company_name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_company_tin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company TIN</label>
                                        <input type="text" wire:model="edit_company_tin" id="edit_company_tin" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Company TIN" />
                                        @error('edit_company_tin') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_dept_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department Code</label>
                                        <input type="text" wire:model="edit_dept_code" id="edit_dept_code" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Department Code" />
                                        @error('edit_dept_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_pull_out_addresse" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pull Out Address</label>
                                        <input type="text" wire:model="edit_pull_out_addresse" id="edit_pull_out_addresse" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Pull Out Address" />
                                        @error('edit_pull_out_addresse') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_vendor_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vendor Code</label>
                                        <input type="text" wire:model="edit_vendor_code" id="edit_vendor_code" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Vendor Code" />
                                        @error('edit_vendor_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <flux:button wire:click="update">
                                Save changes
                            </flux:button>
                            <flux:button wire:click="cancel" variant="outline">
                                Cancel
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete Confirmation Modal -->
        @if($showDeleteModal)
            <div class="fixed inset-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto flex items-center justify-center bg-black/30">
                <div class="relative w-full max-w-md max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <div class="p-6 text-center">
                            <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this branch?</h3>
                            <div class="flex justify-center space-x-3">
                                <flux:button wire:click="delete" class="bg-red-600 hover:bg-red-700 text-white">
                                    Yes, Delete
                                </flux:button>
                                <flux:button wire:click="cancel" variant="outline">
                                Cancel
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>