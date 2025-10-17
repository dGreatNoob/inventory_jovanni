<x-slot:header>Branch Management</x-slot:header>
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

            <!-- Pagination -->
            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                {{ $items->links() }}
            </div>
        </section>

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