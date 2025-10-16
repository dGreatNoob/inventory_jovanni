<x-slot:header>Agent Management</x-slot:header>
<x-slot:subheader>Profile</x-slot:subheader>

<div class="pt-4">
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
                    <p>The Agent Management module is currently under revision and may not be fully functional. Some features may be incomplete or unavailable. Please use the Product Management module for core inventory operations.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="">
        <section
            class="mb-5 max-w-xlg p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <form wire:submit.prevent="submit">
                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label for="agent_code"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Agent Code</label>
                        <input type="text" id="agent_code" wire:model="agent_code"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="AGT-001" required />
                    </div>
                    <div>
                        <label for="name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                        <input type="text" id="name" wire:model="name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Zach Agbalo" required />
                    </div>
                    <div>
                        <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                        <input type="text" id="address" wire:model="address"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="P24 Lawa-an St., Bayugan City" required />
                    </div>
                    <div>
                        <label for="contact_num"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contact Number</label>
                        <input type="tel" id="contact_num" wire:model="contact_num"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="+639474720112" required />
                    </div>
                    <div>
                        <label for="tin_num" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tin Number</label>
                        <input type="number" id="tin_num" wire:model="tin_num"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="123456789" required />
                    </div>
                </div>

                <div class="h-4"></div> <!-- Space between assign branch and submit -->
                <div class="flex justify-end">
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Submit
                    </button>
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
            <div class="mb-5 max-w-xlg p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
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
                            <input type="text" wire:model.lazy="search"
                                class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search Agent...">
                        </div>

                        <!-- Status Filter Dropdown -->
                        <select wire:model.live="statusFilter"
                            class="px-4 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="all">All Status</option>
                            <option value="deployed">Deployed</option>
                            <option value="active">Active</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Agent Code</th>
                                <th scope="col" class="px-6 py-3">Name</th>
                                <th scope="col" class="px-6 py-3">Address</th>
                                <th scope="col" class="px-6 py-3">Contact Number</th>
                                <th scope="col" class="px-6 py-3">Tin Number</th>
                                <th scope="col" class="px-6 py-3 text-center">Status</th>
                                <th scope="col" class="px-6 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                @php
                                    $isDeployed = $item->branchAssignments->where('released_at', null)->count() > 0;
                                    $createdRecent = $item->created_at && now()->diffInSeconds($item->created_at) <= 5;
                                @endphp
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item->agent_code }}</td>
                                    <td class="px-6 py-4">{{ $item->name }}</td>
                                    <td class="px-6 py-4">{{ $item->address }}</td>
                                    <td class="px-6 py-4">{{ $item->contact_num }}</td>
                                    <td class="px-6 py-4">{{ $item->tin_num }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($isDeployed)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Deployed</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Active</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <x-button href="#" wire:click.prevent="toggleDeployment({{ $item->id }})" variant="info">
                                            {{ in_array($item->id, $deployedAgentIds ?? []) ? 'Release' : 'Deploy' }}
                                        </x-button>
                                        <x-button href="#" wire:click.prevent="edit({{ $item->id }})" variant="warning">Edit</x-button>
                                        <x-button href="#" wire:click.prevent="confirmDelete({{ $item->id }})" variant="danger">Delete</x-button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="py-4 px-3">
                    <div class="flex">
                        <div class="flex space-x-4 items-center mb-3">
                            <label class="w-32 text-sm font-medium text-gray-900 dark:text-white">Per Page</label>
                            <select wire:model="perPage"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
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
                                    <label for="edit_agent_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Agent Code</label>
                                    <input type="text" wire:model="edit_agent_code" id="edit_agent_code" 
                                        class="w-full p-2 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white" placeholder="AGT-001" />
                                    @error('edit_agent_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="edit_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                                    <input type="text" wire:model="edit_name" id="edit_name" 
                                        class="w-full p-2 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white" placeholder="Zach Agbalo" />
                                    @error('edit_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="edit_address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                                    <input type="text" wire:model="edit_address" id="edit_address" 
                                        class="w-full p-2 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white" placeholder="Address" />
                                    @error('edit_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="edit_contact_num" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contact Number</label>
                                    <input type="text" wire:model="edit_contact_num" id="edit_contact_num" 
                                        class="w-full p-2 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white" placeholder="+639474720112" />
                                    @error('edit_contact_num') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="edit_tin_num" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">TIN Number</label>
                                    <input type="text" wire:model="edit_tin_num" id="edit_tin_num" 
                                        class="w-full p-2 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white" placeholder="123456789" />
                                    @error('edit_tin_num') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button type="button" wire:click="update"
                                    class="text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-lg">
                                Save changes
                            </button>
                            <button type="button" wire:click="cancel"
                                    class="text-gray-500 bg-white hover:bg-gray-100 px-5 py-2.5 rounded-lg border border-gray-200">
                                Cancel
                            </button>
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
                                <button wire:click="delete" class="bg-red-600 text-white px-5 py-2.5 rounded-lg hover:bg-red-700">
                                    Yes, Delete
                                </button>
                                <button wire:click="cancel" class="bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-300">
                                    Cancel
                                </button>
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
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Branch</label>
                                <select x-model="branchId" @change="updateSubclassOptions()" wire:model="assign_branch_id"
                                    class="w-full p-2 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select a branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                @error('assign_branch_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Subclass {{ empty($subclassOptions) ? '(none)' : '' }}
                                </label>
                                <select wire:model="assign_subclass"
                                    class="w-full p-2 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white"
                                    @disabled(empty($subclassOptions))>
                                    <option value="">{{ empty($subclassOptions) ? 'No subclasses' : 'Select a subclass' }}</option>
                                    @foreach($subclassOptions as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                                @error('assign_subclass') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button type="button" wire:click="assignToBranch"
                                class="text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-lg">
                                Assign
                            </button>
                            <button type="button" wire:click="cancel"
                                class="text-gray-500 bg-white hover:bg-gray-100 px-5 py-2.5 rounded-lg border border-gray-200">
                                Cancel
                            </button>
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
