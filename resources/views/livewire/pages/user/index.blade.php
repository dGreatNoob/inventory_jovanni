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
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <x-input type="email" wire:model="email" name="email" label="User Email"
                            placeholder="Enter user email" />
                        @error('email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <x-dropdown name="department_id" wire:model="department_id" label="Department"
                            :options="$this->departments->pluck('name', 'id')->toArray()" />
                    </div>
                    <div>
                        <x-dropdown label="Role(s)" name="roles" wire:model="selectedRole" :options="$roles->pluck('name', 'id')->toArray()"
                            multiselect />
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
                <div class="flex justify-end">
                    <x-button type="submit" variant="primary">Submit</x-button>
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
            <div class="flex items-center justify-between p-4 pr-10">
                <div class="flex space-x-6">
                    <div class="relative">
                        <x-input type="text" wire:model.live="search" name="search" label="Search"
                            placeholder="Search users..." />
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
                                    <div class="flex space-x-2">
                                        <x-button type="button" wire:click="edit({{ $user->id }})"
                                            variant="warning" size="sm">Edit</x-button>
                                        <x-button type="button" wire:click="confirmDelete({{ $user->id }})"
                                            variant="danger" size="sm">Delete</x-button>
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

                        <div class="flex items-center justify-end p-6 space-x-2 border-t">
                            <x-button type="button" wire:click="update" variant="primary">
                                Save Changes
                            </x-button>
                            <x-button type="button" wire:click="cancel" variant="secondary">Cancel</x-button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Delete Modal -->
            <div x-data="{ show: @entangle('showDeleteModal') }" x-show="show" x-cloak
                class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center w-full p-4 overflow-x-hidden overflow-y-auto h-[calc(100%-1rem)] max-h-full">
                <div class="relative w-full max-w-md max-h-full">
                    <div class="bg-white rounded-lg shadow dark:bg-gray-700 p-6 text-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01M4.93 4.93a10 10 0 0114.14 0M4.93 19.07a10 10 0 0014.14 0" />
                        </svg>
                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                            Are you sure you want to delete this user?
                        </h3>
                        <div class="flex justify-center space-x-2">
                            <x-button type="button" wire:click="delete" variant="danger">Yes, I'm sure</x-button>
                            <x-button type="button" wire:click="cancel" variant="secondary">No, cancel</x-button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination and Per Page -->
            <div class="py-4 px-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-900 dark:text-white">Per Page:</label>
                        <x-dropdown wire:model.live="perPage" name="perPage" :options="[
                            '5' => '5',
                            '10' => '10',
                            '25' => '25',
                            '50' => '50',
                            '100' => '100',
                        ]" />
                    </div>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </x-collapsible-card>
    </div>
</div>
