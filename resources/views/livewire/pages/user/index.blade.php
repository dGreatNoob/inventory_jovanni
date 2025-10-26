<x-slot:header>User</x-slot:header>
<x-slot:subheader>User Management</x-slot:subheader>

<div class="">
    <div class="">
        <x-collapsible-card title="Add User" open="false" size="full">
            <form wire:submit.prevent="create" x-show="open" x-transition>
                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <x-input type="text" wire:model="name" name="name" label="User Name"
                            placeholder="Enter user name" />
                    </div>
                    <div>
                        <x-input type="email" wire:model="email" name="email" label="User Email"
                            placeholder="Enter user email" />
                    </div>
                    <div>
                        <x-dropdown name="department_id" wire:model="department_id" label="Department"
                            :options="$this->departments->pluck('name', 'id')->toArray()" />
                    </div>
                    <div>
                        <x-dropdown label="Role(s)" name="roles" wire:model="selectedRole" :options="$roles->pluck('name', 'id')->toArray()"  multiselect
                            class="{{ $errors->has('selectedRole') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}"
                        />
                        @error('selectedRole')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <x-input type="password" wire:model="password" name="password" label="Password"
                            placeholder="Enter password" />
                        @error('password')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <x-input type="password" wire:model="password_confirmation" name="password_confirmation"
                            label="Confirm Password" placeholder="Confirm password" />
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                <flux:button type="submit" wire:loading.attr="disabled" wire:target="store">
                    <span wire:loading.remove wire:target="store">Submit</span>
                    <span wire:loading wire:target="store">Saving...</span>
                </flux:button>
            </div>
            </form>
        </x-collapsible-card>

        @if (session('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="transition duration-500 ease-in-out" x-transition>
                <x-flash-message />
            </div>
        @endif

        <x-collapsible-card title="Users List" open="true" size="full">
            <div class="w-full">
                <div class="flex items-center justify-start pl-6 pr-0 py-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>

                        <input 
                            type="text" 
                            wire:model.live.debounce.500ms="search"
                            class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg 
                                bg-gray-50 focus:ring-blue-500 focus:border-blue-500 
                                dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 
                                dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search User...">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                    <thead class="text-xs text-gray-600 uppercase bg-gray-100 dark:bg-gray-800 dark:text-gray-300">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Roles</th>
                            <th class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr
                                class="bg-white dark:bg-gray-900 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4">{{ $user->name }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    {{ $user->department ? $user->department->name : 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ \Spatie\Permission\Models\Role::find(
                                        DB::table('model_has_roles')->where('model_id', $user->id)->where('model_type', 'App\\Models\\User')->value('role_id'),
                                    )?->name ?? 'No Role' }}
                                </td>


                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <flux:button wire:click="edit({{ $user->id }})" size="sm" variant="outline"> Edit </flux:button>
                                        <flux:button wire:click="confirmDelete({{ $user->id }})" size="sm" variant="outline" class="text-red-600 hover:text-red-700"> Delete </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


            <!-- Edit Modal -->
            <div x-data="{ show: @entangle('showEditModal') }" x-show="show" x-cloak
                class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center w-full p-4 overflow-x-hidden overflow-y-auto h-[calc(100%-1rem)] max-h-full">
                <div class="relative w-full max-w-2xl max-h-full">
                    <div class="bg-white rounded-lg shadow dark:bg-gray-700">
                        <div class="flex justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit User</h3>
                            <button type="button" wire:click="cancel"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 dark:hover:bg-gray-600 dark:hover:text-white">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-6">
                            <div class="grid gap-6 mb-6 md:grid-cols-2">
                                <div>
                                    <x-input type="text" wire:model="name" name="name" label="User Name" />
                                    @error('name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <x-input type="email" wire:model="email" name="email" label="User Email" />
                                    @error('email')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <x-dropdown label="Role(s)" name="roles" wire:model="selectedRole"
                                        :options="$roles->pluck('name', 'id')->toArray()" multiselect />
                                    @error('selectedRole')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <x-input type="password" wire:model="password" name="password" label="Password"
                                        placeholder="Enter new password (optional)" />
                                    @error('password')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <x-input type="password" wire:model="password_confirmation"
                                        name="password_confirmation" label="Confirm Password"
                                        placeholder="Confirm new password" />
                                </div>
                            </div>
                        </div>

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


            <!-- Delete Modal -->
            @if($showDeleteModal)
                <div class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                    <div class="relative w-full max-w-md max-h-full">
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" wire:click="cancelDelete">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                            <div class="p-6 text-center">
                                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>
                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this user?</h3>
                                <flux:button wire:click="delete" class="mr-2 bg-red-600 hover:bg-red-700 text-white">
                                    Yes, I'm sure
                                </flux:button>
                                <flux:button wire:click="cancelDelete" variant="outline">
                                    No, cancel
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Pagination and Per Page -->
            <div class="py-4 px-3 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-900 dark:text-white">Per Page:</label>
                    <select wire:model.live="perPage"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                               focus:ring-blue-500 focus:border-blue-500 block p-2.5 
                               dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 
                               dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </x-collapsible-card>
    </div>
</div>
