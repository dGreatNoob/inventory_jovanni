<x-slot:header>Branch Management</x-slot:header>
<x-slot:subheader>Profile</x-slot:subheader>
<div class="pt-4">
    <div class="">
        <!-- Dashboard Section - Commented out as requested -->
     {{-- <x-branch-dashboard :stats="$this->dashboardStats" /> 

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
                    <p>The Branch Management module is currently under revision and may not be fully functional. Some features may be incomplete or unavailable. Please use the Product Management module for core inventory operations.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="">
        <!-- Branch Management Form -->
        <section class="mb-5 max-w-xlg p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Branch Information</h2>
            <form wire:submit.prevent="submit">
                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Branch Name</label>
                        <input type="text" id="name" wire:model="name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Main Branch" required />
                    </div>
                    <div>
                        <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                        <input type="text" id="address" wire:model="address"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="123 Main Street, City" required />
                    </div>
                    <div>
                        <label for="contact_num" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contact Number</label>
                        <input type="text" id="contact_num" wire:model="contact_num"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="+1 234 567 8900" />
                    </div>
                    <div>
                        <label for="manager_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Manager Name</label>
                        <input type="text" id="manager_name" wire:model="manager_name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="John Doe" />
                    </div>
                </div>
                
                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Add Branch
                </button>
            </form>
        </section>

        <!-- Branch List -->
        <section class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Branch List</h3>
                
                <!-- Search -->
                <div class="mb-4">
                    <input type="text" wire:model.live.debounce.300ms="search" 
                        placeholder="Search branches..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Branch Name</th>
                            <th class="px-6 py-3">Address</th>
                            <th class="px-6 py-3">Contact</th>
                            <th class="px-6 py-3">Manager</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $branch)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $branch->name }}</td>
                                <td class="px-6 py-4">{{ $branch->address }}</td>
                                <td class="px-6 py-4">{{ $branch->contact_num ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $branch->manager_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <button wire:click="edit({{ $branch->id }})" 
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-2">
                                        Edit
                                    </button>
                                    <button wire:click="confirmDelete({{ $branch->id }})" 
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No branches found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Your existing branch table --}}

        <section
            x-data="{ open: true }"
            class="mb-5 max-w-xlg p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Basic Information</h2>
                <button type="button" @click="open = !open"
                    class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                    <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            <form wire:submit.prevent="submit" x-show="open" x-transition>
                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label for="name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Branch Name</label>
                        <input type="text" id="name" wire:model="name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Branch" required />

                        <!-- Inline inputs below Branch Name -->
                        <div class="flex gap-2 mt-4">
                            <div class="flex-1">
                                <label for="subclass1" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">Subclass Branch</label>
                                <input type="text" id="subclass1" wire:model="subclass1"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Subclass 1" required />
                            </div>
                            <div class="flex-1">
                                <label for="subclass2" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">Subclass Branch</label>
                                <input type="text" id="subclass2" wire:model="subclass2"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Subclass 2" />
                            </div>
                            <div class="flex-1">
                                <label for="subclass3" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">Subclass Branch</label>
                                <input type="text" id="subclass3" wire:model="subclass3"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Subclass 3" />
                            </div>
                            <div class="flex-1">
                                <label for="subclass4" class="block mb-1 text-xs font-medium text-gray-900 dark:text-white">Subclass Branch</label>
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
                            placeholder="Unit No, Street Name., City" required />
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
                                <input type="text" wire:model.live.debounce.500ms="search"
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

        <!-- Edit Modal -->
        @if($showEditModal)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Edit Branch</h3>
                        <form wire:submit.prevent="update">
                            <div class="space-y-4">
                                <div>
                                    <label for="edit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Branch Name</label>
                                    <input type="text" wire:model="edit_name" id="edit_name"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        required />
                                </div>
                                <div>
                                    <label for="edit_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                                    <input type="text" wire:model="edit_address" id="edit_address"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        required />
                                </div>
                                <div>
                                    <label for="edit_contact_num" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Number</label>
                                    <input type="text" wire:model="edit_contact_num" id="edit_contact_num"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                </div>
                                <div>
                                    <label for="edit_manager_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Manager Name</label>
                                    <input type="text" wire:model="edit_manager_name" id="edit_manager_name"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                </div>
                            </div>
                            <div class="flex justify-end space-x-2 mt-6">
                                <button type="button" wire:click="$set('showEditModal', false)"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete Confirmation Modal -->
        @if($showDeleteModal)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Confirm Delete</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Are you sure you want to delete this branch? This action cannot be undone.</p>
                        <div class="flex justify-end space-x-2">
                            <button wire:click="$set('showDeleteModal', false)"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                                Cancel
                            </button>
                            <button wire:click="delete"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('message'))
            <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50">
                {{ session('message') }}
            </div>
        @endif
    </div>
</div>