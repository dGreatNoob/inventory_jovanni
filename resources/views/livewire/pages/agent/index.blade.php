<x-slot:header>Agent Management</x-slot:header>
<x-slot:subheader>Profile</x-slot:subheader>

<div class="pt-4">
    <div class="">
        <section class="mb-6 bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Create New Agent</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add a new agent to the system</p>
            </div>
            <form wire:submit.prevent="submit" class="p-6">
                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label for="agent_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Agent Code</label>
                        <input type="text" id="agent_code" wire:model="agent_code"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="AGT-001" required />
                        @error('agent_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                        <input type="text" id="name" wire:model="name"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Full Name" required />
                        @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                        <input type="text" id="address" wire:model="address"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Unit No, Street Name, City" required />
                        @error('address') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="contact_num" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Number</label>
                        <input type="tel" id="contact_num" wire:model="contact_num"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="+639474720112" required />
                        @error('contact_num') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="tin_num" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">TIN Number</label>
                        <input type="text" id="tin_num" wire:model="tin_num"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="123456789" required />
                        @error('tin_num') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="h-4"></div> <!-- Space between assign branch and submit -->
                <div class="flex justify-end">
                    <flux:button type="submit">
                        Submit
                    </flux:button>
                </div>
                </div>
            </form>
        </section>

        @if (session()->has('message'))
            <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('message') }}
            </div>
        @endif

        <section>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                {{-- Search and Filter Section --}}
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                            {{-- Search Input --}}
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
                                    placeholder="Search Agent...">
                            </div>

                            {{-- Status Filter Dropdown --}}
                            <select wire:model.live="statusFilter"
                                class="px-4 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                                <option value="all">All Status</option>
                                <option value="deployed">Deployed</option>
                                <option value="active">Active</option>
                            </select>
                        </div>

                        {{-- Per Page Selector --}}
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

                {{-- Results Info --}}
                <div class="px-6 py-3 text-sm text-gray-700 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50">
                    @if($items->total() > 0)
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $items->total() }}</span> agents found
                    @else
                        <span class="font-semibold text-gray-900 dark:text-white">No agents found</span>
                    @endif
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Agent Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Address</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact Number</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">TIN Number</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($items as $item)
                                @php
                                    $isDeployed = $item->branchAssignments->where('released_at', null)->count() > 0;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $item->agent_code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $item->address }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->contact_num }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->tin_num }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($isDeployed)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400">
                                                Deployed
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                                Active
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <flux:button wire:click.prevent="toggleDeployment({{ $item->id }})" variant="outline" size="sm">
                                                {{ $isDeployed ? 'Release' : 'Deploy' }}
                                            </flux:button>
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
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                No agents found
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Try adjusting your search or filter criteria
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
                    {{ $items->links() }}
                </div>
            </div>
        </section>
        <section>
            <!-- Agent Edit Modal -->
            <div x-data="{ show: @entangle('showEditModal').live }" 
                x-show="show" x-cloak
                class="fixed inset-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto flex items-center justify-center">
                
                <div class="relative w-full max-w-2xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        
                        <!-- Modal Header -->
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Agent</h3>
                            <button type="button" wire:click="cancel"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-6 space-y-6">
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label for="edit_agent_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Agent Code</label>
                                    <input type="text" wire:model="edit_agent_code" id="edit_agent_code" 
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                        placeholder="AGT-001" />
                                    @error('edit_agent_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="edit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                                    <input type="text" wire:model="edit_name" id="edit_name" 
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                        placeholder="Full Name" />
                                    @error('edit_name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="edit_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                                    <input type="text" wire:model="edit_address" id="edit_address" 
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                        placeholder="Unit No, Street Name, City" />
                                    @error('edit_address') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="edit_contact_num" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Number</label>
                                    <input type="text" wire:model="edit_contact_num" id="edit_contact_num" 
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                        placeholder="+639474720112" />
                                    @error('edit_contact_num') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="edit_tin_num" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">TIN Number</label>
                                    <input type="text" wire:model="edit_tin_num" id="edit_tin_num" 
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                        placeholder="123456789" />
                                    @error('edit_tin_num') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
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
            @if($showDeleteModal)
                <div class="fixed inset-0 z-50 w-full p-4 flex items-center justify-center bg-gray-900 bg-opacity-50">
                    <div class="relative w-full max-w-md max-h-full">
                        <div class="bg-white rounded-lg shadow dark:bg-gray-700 p-6 text-center">
                            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                                Are you sure you want to delete this agent profile?
                            </h3>
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
            @endif
        </section>


        <section>
            <div x-data="{
                    show: @entangle('showAssignModal').live,
                    branchId: @entangle('assign_branch_id').defer,
                    subclassOptions: @js($subclassOptions),
                    updateSubclassOptions() {
                        this.$wire.set('assign_branch_id', this.branchId);
                        this.$nextTick(() => {
                            this.subclassOptions = @js($subclassOptions);
                        });
                    }
                }"
                x-show="show" x-cloak
                class="fixed inset-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto flex items-center justify-center bg-black/30">
                <div class="relative w-full max-w-lg max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <!-- Modal Header -->
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Assign to Branch</h3>
                            <button type="button" wire:click="cancel"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-6 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                                <select x-model="branchId" @change="updateSubclassOptions()" wire:model="assign_branch_id"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select a branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                @error('assign_branch_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Subclass {{ empty($subclassOptions) ? '(none)' : '' }}
                                </label>
                                <select wire:model="assign_subclass"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-500"
                                    @disabled(empty($subclassOptions))>
                                    <option value="">{{ empty($subclassOptions) ? 'No subclasses' : 'Select a subclass' }}</option>
                                    @foreach($subclassOptions as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                                @error('assign_subclass') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <flux:button wire:click="assignToBranch">
                                Assign
                            </flux:button>
                            <flux:button wire:click="cancel" variant="outline">
                                Cancel
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Deployment History: add a Subclass column -->
        <section>
            <div >
                {{-- Add the Livewire deployment history component --}}
                <livewire:deployment-history />
            </div>
        </section>



    </div>
</div>
