<x-slot:header>Branch Management</x-slot:header>
<x-slot:subheader>Profile</x-slot:subheader>

<div class="pt-4">
    <div class="space-y-6">
        <!-- Tabs Navigation -->
        @include('livewire.pages.branch.branch-management-tabs')
        <!-- Create New Branch Form (Collapsible) -->
         @can ('branch create')
        <section class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8" x-data="{ open: false }">
            <button type="button"
                @click="open = !open"
                class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-inset">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Create New Branch</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add a new branch to the system</p>
                </div>
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform duration-200"
                    :class="{ 'rotate-180': open }"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open"
                x-collapse
                class="border-t border-gray-200 dark:border-gray-700">
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
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                            <input type="email" id="email" wire:model="email"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="branch@example.com" />
                            @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Selling Area Information -->
                <div class="mb-10">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-6">Selling Area Information</h4>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label for="selling_area1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selling Area 1</label>
                            <input type="text" id="selling_area1" wire:model="selling_area1"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Selling Area 1" />
                            @error('selling_area1') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="selling_area2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selling Area 2</label>
                            <input type="text" id="selling_area2" wire:model="selling_area2"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Selling Area 2" />
                            @error('selling_area2') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="selling_area3" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selling Area 3</label>
                            <input type="text" id="selling_area3" wire:model="selling_area3"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Selling Area 3" />
                            @error('selling_area3') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="selling_area4" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selling Area 4</label>
                            <input type="text" id="selling_area4" wire:model="selling_area4"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Selling Area 4" />
                            @error('selling_area4') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
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
                            <label for="batchSelect" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batch</label>
                            <select id="batchSelect" wire:model.live="batchSelect"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">— No batch —</option>
                                @foreach($this->batchOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                                <option value="__new__">Add new batch…</option>
                            </select>
                            @if($batchSelect === '__new__')
                                <input type="text" wire:model="batchNew" placeholder="Enter new batch value"
                                    class="mt-2 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                                @error('batchNew') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            @endif
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

                    <!-- Row 3: Company TIN -->
                    <div class="grid gap-6 md:grid-cols-2 mb-6">
                        <div>
                            <label for="company_tin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company TIN</label>
                            <input type="text" id="company_tin" wire:model="company_tin"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Company TIN" />
                            @error('company_tin') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Subclass group: Department Code, Pull Out Address, Vendor Group -->
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                        <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Subclass</h5>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="dept_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department Code</label>
                                <input type="text" id="dept_code" wire:model="dept_code"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Department Code" />
                                @error('dept_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="pull_out_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pull Out Address</label>
                                <input type="text" id="pull_out_address" wire:model="pull_out_address"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Pull Out Address" />
                                @error('pull_out_address') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="vendor_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vendor Group</label>
                                <input type="text" id="vendor_code" wire:model="vendor_code"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Vendor Group" />
                                @error('vendor_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
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
            </div>
        </section>
        @endcan

        @if (session()->has('message'))
            <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('message') }}
            </div>
        @endif

        <!-- Branch List -->
        <section class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Branch List</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage existing branches</p>
                </div>
                <div>
                    <!-- <button type="button" 
                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors">
                        Create Branch
                    </button> -->
                </div>
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
                        <!-- Batch Filter -->
                        <div>
                            <label for="batchFilter" class="sr-only">Batch</label>
                            <select id="batchFilter" wire:model.live="batchFilter"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-40 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                                <option value="">All Batches</option>
                                @foreach($this->batchOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Address</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact / Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Manager</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Batch</th>
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
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $item->address }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div>{{ $item->contact_num ?? '—' }}</div>
                                    @if($item->email)
                                        <div class="text-gray-400 dark:text-gray-500 text-xs mt-0.5">{{ $item->email }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->manager_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->batch ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        @can('branch edit')
                                        <flux:button wire:click.prevent="edit({{ $item->id }})" variant="outline" size="sm">
                                            Edit
                                        </flux:button>
                                        @endcan

                                        @can('branch delete')
                                        <flux:button wire:click.prevent="confirmDelete({{ $item->id }})" variant="outline" size="sm" class="text-red-600 hover:text-red-700">
                                            Delete
                                        </flux:button>
                                        @endcan
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
                {{ $items->links('livewire::tailwind', ['scrollTo' => false]) }}
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
                                    <div>
                                        <label for="edit_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                                        <input type="email" wire:model="edit_email" id="edit_email" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="branch@example.com" />
                                        @error('edit_email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Selling Area Information -->
                            <div>
                                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Selling Area Information</h4>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="edit_selling_area1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selling Area 1</label>
                                        <input type="text" wire:model="edit_selling_area1" id="edit_selling_area1" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Selling Area 1" />
                                        @error('edit_selling_area1') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_selling_area2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selling Area 2</label>
                                        <input type="text" wire:model="edit_selling_area2" id="edit_selling_area2" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Selling Area 2" />
                                        @error('edit_selling_area2') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_selling_area3" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selling Area 3</label>
                                        <input type="text" wire:model="edit_selling_area3" id="edit_selling_area3" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Selling Area 3" />
                                        @error('edit_selling_area3') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_selling_area4" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selling Area 4</label>
                                        <input type="text" wire:model="edit_selling_area4" id="edit_selling_area4" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Selling Area 4" />
                                        @error('edit_selling_area4') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div>
                                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Additional Information</h4>
                                <div class="grid gap-4 md:grid-cols-2 mb-4">
                                    <div>
                                        <label for="edit_remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                                        <input type="text" wire:model="edit_remarks" id="edit_remarks" 
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            placeholder="Remarks" />
                                        @error('edit_remarks') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit_batchSelect" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batch</label>
                                        <select id="edit_batchSelect" wire:model.live="edit_batchSelect"
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="">— No batch —</option>
                                            @foreach($this->batchOptions as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                            <option value="__new__">Add new batch…</option>
                                        </select>
                                        @if($edit_batchSelect === '__new__')
                                            <input type="text" wire:model="edit_batchNew" id="edit_batchNew" placeholder="Enter new batch value"
                                                class="mt-2 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                                            @error('edit_batchNew') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        @endif
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
                                </div>
                                <!-- Subclass group -->
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Subclass</h5>
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label for="edit_dept_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department Code</label>
                                            <input type="text" wire:model="edit_dept_code" id="edit_dept_code" 
                                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                                placeholder="Department Code" />
                                            @error('edit_dept_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label for="edit_pull_out_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pull Out Address</label>
                                            <input type="text" wire:model="edit_pull_out_address" id="edit_pull_out_address" 
                                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                                placeholder="Pull Out Address" />
                                            @error('edit_pull_out_address') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label for="edit_vendor_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vendor Group</label>
                                            <input type="text" wire:model="edit_vendor_code" id="edit_vendor_code" 
                                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                                placeholder="Vendor Group" />
                                            @error('edit_vendor_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        </div>
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