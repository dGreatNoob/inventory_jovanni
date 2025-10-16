<x-slot:header>Branch Management</x-slot:header>
<x-slot:subheader>Profile</x-slot:subheader>
<div class="pt-4">
    <div class="">
        <!-- Dashboard Section - Commented out as requested -->
    <x-branch-dashboard :stats="$this->dashboardStats" />


        {{-- Agent Per Branch Dashboard --}}
        <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Agents Per Branch</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                        <p class="text-sm text-blue-600 dark:text-blue-400">Total Branches</p>
                        <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $this->agentPerBranchStats->count() }}</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                        <p class="text-sm text-green-600 dark:text-green-400">Total Agents</p>
                        <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $totalAgents }}</p>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                        <p class="text-sm text-purple-600 dark:text-purple-400">Active Agents</p>
                        <p class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ $totalActiveAgents }}</p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-3 mb-4">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="dashboardSearch" 
                        placeholder="Search branch name or code..." 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    
                    <select 
                        wire:model.live="statusFilter"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="all">All Statuses</option>
                        <option value="covered">Covered (Has Agents)</option>
                        <option value="no_agent">No Agent</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" wire:click="sortByColumn('name')">
                                Branch Name @if($sortBy === 'name') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                            </th>
                            <th class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" wire:click="sortByColumn('code')">
                                Branch Code @if($sortBy === 'code') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                            </th>
                            <th class="px-6 py-3">Address</th>
                            <th class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" wire:click="sortByColumn('agent_count')">
                                Active Agents @if($sortBy === 'agent_count') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                            </th>
                            <th class="px-6 py-3">Agent Details</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->agentPerBranchStats as $branch)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $branch->name }}</td>
                                <td class="px-6 py-4">{{ $branch->code }}</td>
                                <td class="px-6 py-4">{{ $branch->address }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-lg font-semibold rounded-full {{ $branch->agent_count > 0 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                        {{ $branch->agent_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($branch->activeAgents && $branch->activeAgents->count() > 0)
                                        <div class="space-y-1">
                                            @foreach($branch->activeAgents as $assignment)
                                                <div class="text-xs">
                                                    <span class="font-semibold text-gray-900 dark:text-white">
                                                        {{ $assignment->agent->agent_code }}
                                                    </span>
                                                    - {{ $assignment->agent->name }}
                                                    @if($assignment->subclass)
                                                        <span class="text-gray-500 dark:text-gray-400">({{ $assignment->subclass }})</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs italic">No agents assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($branch->agent_count > 0)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Covered</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">No Agent</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No branches found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Your existing branch table --}}

        <section
            class="mb-5 max-w-xlg p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
            <form wire:submit.prevent="submit">
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="name"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Branch Name</label>
                    <input type="text" id="name" wire:model="name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Main Branch" required />

                    <!-- Inline inputs below Branch Name -->
                    <div class="flex gap-2 mt-4">
                        <div class="flex-1">
                            <label for="subclass1" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">Subclass 1</label>
                            <input type="text" id="subclass1" wire:model="subclass1"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Subclass 1" />
                        </div>
                        <div class="flex-1">
                            <label for="subclass2" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">Subclass 2</label>
                            <input type="text" id="subclass2" wire:model="subclass2"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Subclass 2" />
                        </div>
                        <div class="flex-1">
                            <label for="subclass3" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">Subclass 3</label>
                            <input type="text" id="subclass3" wire:model="subclass3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Subclass 3" />
                        </div>
                        <div class="flex-1">
                            <label for="subclass4" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">Subclass 4</label>
                            <input type="text" id="subclass4" wire:model="subclass4"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Subclass 4" />
                        </div>
                    </div>
                </div>
                <div>
                    <label for="code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Code</label>
                    <input type="text" id="code" wire:model="code"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Branch Code" required />
                </div>
                <div>
                    <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category</label>
                    <input type="text" id="category" wire:model="category"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Category" required />
                </div>
                <div>
                    <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                    <input type="text" id="address" wire:model="address"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="P24 lawaan st. bayugan city" required />
                </div>
                <div>
                    <label for="remarks" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Remarks</label>
                    <input type="text" id="remarks" wire:model="remarks"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Remarks" />
                </div>
            </div>

            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Additional Information</h2>
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="batch" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Batch</label>
                    <input type="text" id="batch" wire:model="batch"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Batch" />
                </div>
                <div>
                    <label for="branch_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Branch Code</label>
                    <input type="text" id="branch_code" wire:model="branch_code"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Branch Code" />
                </div>
                <div>
                    <label for="company_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company Name</label>
                    <input type="text" id="company_name" wire:model="company_name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Company Name" />
                </div>
                <div>
                    <label for="company_tin" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company TIN</label>
                    <input type="text" id="company_tin" wire:model="company_tin"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Company TIN" />
                </div>
                <div>
                    <label for="dept_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">DeptCode</label>
                    <input type="text" id="dept_code" wire:model="dept_code"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="DeptCode" />
                </div>
                <div>
                    <label for="pull_out_addresse" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pull Out Addresse</label>
                    <input type="text" id="pull_out_addresse" wire:model="pull_out_addresse"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Pull Out Addresse" />
                </div>
                <div>
                    <label for="vendor_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Vendor Code</label>
                    <input type="text" id="vendor_code" wire:model="vendor_code"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Vendor Code" />
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Submit
                </button>
            </div>

            </form>
        </section>
        @if (session()->has('message'))
            <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('message') }}
            </div>
        @endif
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
                                <input type="text" wire:model.debounce.500ms="search"
                                    class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search Branch...">
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Code
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Branch Name
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Category
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Address
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $item->code }}
                                        </th>
                                        <td class="px-6 py-4">{{ $item->name }}</td>
                                        <td class="px-6 py-4">{{ $item->category }}</td>
                                        <td class="px-6 py-4">{{ $item->address }}</td>
                                        <td class="px-6 py-4">
                                            <x-button href="#" wire:click.prevent="edit({{ $item->id }})"
                                               variant="warning">Edit</x-button>
                                            <x-button href="#" wire:click.prevent="confirmDelete({{ $item->id }})"
                                                variant="danger">Delete</x-button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="py-4 px-3">
                        <div class="flex ">
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
            </div>
        </section>
        <section>
            <div x-data="{ show: @entangle('showEditModal').live }" x-show="show" x-cloak
                class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                <div class="relative w-full max-w-2xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Branch</h3>
                            <button type="button" wire:click="cancel"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <div class="p-6 space-y-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                            <div class="grid gap-6 mb-6 md:grid-cols-2">
                                <div>
                                    <label for="edit_name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Branch Name</label>
                                    <input type="text" wire:model="edit_name" id="edit_name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500
                                        block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                        dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Enter Branch Name" required />
                                    @error('edit_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                                    <!-- Inline inputs below Branch Name -->
                                    <div class="flex gap-2 mt-4">
                                        <div class="flex-1">
                                            <label for="edit_subclass1" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">Subclass 1</label>
                                            <input type="text" id="edit_subclass1" wire:model="edit_subclass1"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Subclass 1" />
                                        </div>
                                        <div class="flex-1">
                                            <label for="edit_subclass2" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">Subclass 2</label>
                                            <input type="text" id="edit_subclass2" wire:model="edit_subclass2"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Subclass 2" />
                                        </div>
                                        <div class="flex-1">
                                            <label for="edit_subclass3" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">Subclass 3</label>
                                            <input type="text" id="edit_subclass3" wire:model="edit_subclass3"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Subclass 3" />
                                        </div>
                                        <div class="flex-1">
                                            <label for="edit_subclass4" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">Subclass 4</label>
                                            <input type="text" id="edit_subclass4" wire:model="edit_subclass4"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Subclass 4" />
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="edit_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Code</label>
                                    <input type="text" wire:model="edit_code" id="edit_code"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500
                                        block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                        dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Branch Code" required />
                                </div>
                                <div>
                                    <label for="edit_category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category</label>
                                    <input type="text" wire:model="edit_category" id="edit_category"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Category" required />
                                </div>
                                <div>
                                    <label for="edit_address"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >Address</label>
                                    <input type="text" wire:model="edit_address" id="edit_address"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500
                                        block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                        dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter address" />
                                    @error('edit_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="edit_remarks" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Remarks</label>
                                    <input type="text" wire:model="edit_remarks" id="edit_remarks"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Remarks" />
                                </div>
                            </div>

                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Additional Information</h2>
                            <div class="grid gap-6 mb-6 md:grid-cols-2">
                                <div>
                                    <label for="edit_batch" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Batch</label>
                                    <input type="text" wire:model="edit_batch" id="edit_batch"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Batch" />
                                </div>
                                <div>
                                    <label for="edit_branch_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Branch Code</label>
                                    <input type="text" wire:model="edit_branch_code" id="edit_branch_code"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Branch Code" />
                                </div>
                                <div>
                                    <label for="edit_company_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company Name</label>
                                    <input type="text" wire:model="edit_company_name" id="edit_company_name"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Company Name" />
                                </div>
                                <div>
                                    <label for="edit_company_tin" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company TIN</label>
                                    <input type="text" wire:model="edit_company_tin" id="edit_company_tin"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Company TIN" />
                                </div>
                                <div>
                                    <label for="edit_dept_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">DeptCode</label>
                                    <input type="text" wire:model="edit_dept_code" id="edit_dept_code"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="DeptCode" />
                                </div>
                                <div>
                                    <label for="edit_pull_out_addresse" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pull Out Addresse</label>
                                    <input type="text" wire:model="edit_pull_out_addresse" id="edit_pull_out_addresse"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Pull Out Addresse" />
                                </div>
                                <div>
                                    <label for="edit_vendor_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Vendor Code</label>
                                    <input type="text" wire:model="edit_vendor_code" id="edit_vendor_code"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Vendor Code" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button type="button" wire:click="update"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Save changes
                            </button>
                            <button type="button" wire:click="cancel"
                                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @if($showDeleteModal)
                <div class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
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
                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this branch profile?</h3>
                                <button type="button" wire:click="delete" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                    Yes, I'm sure
                                </button>
                                <button type="button" wire:click="cancel" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No, cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </div>
</div>