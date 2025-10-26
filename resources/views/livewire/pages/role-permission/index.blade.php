<x-slot:header>Roles & Permissions</x-slot:header>
<x-slot:subheader>Manage role access</x-slot:subheader>

<div class="space-y-6">

    {{-- Add / Edit Role Form --}}
    <x-collapsible-card title="Add Role" open="false" size="full">
        <form wire:submit.prevent="store" x-show="open" x-transition>
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                {{-- Role Name --}}
                <div>
                    <x-input 
                        type="text" 
                        wire:model="name" 
                        name="name" 
                        label="Role Name"
                        placeholder="Enter role name" />
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Permissions --}}
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-m font-semibold text-gray-800 dark:text-gray-100 tracking-wide">
                            Permissions
                        </label>

                        {{-- Select / Deselect All --}}
                        <div class="flex items-center gap-3 text-sm">
                            <flux:button 
                                wire:click="selectAllPermissions" 
                                size="sm" 
                                variant="outline">
                                Select All
                            </flux:button>

                            <flux:button 
                                wire:click="deselectAllPermissions" 
                                size="sm" 
                                variant="outline" 
                                class="text-red-600 hover:text-red-700">
                                Deselect All
                            </flux:button>
                        </div>
                    </div>

                    {{-- Permissions Grid --}}
                    <div class="@error('selectedPermissions') border border-red-500 rounded @enderror">
                        @php
                            $groupedPermissions = collect(\App\Enums\Enum\PermissionEnum::cases())
                                ->groupBy(fn($perm) => $perm->category());
                        @endphp

                        @foreach ($groupedPermissions as $category => $permissionsGroup)
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2">
                                    {{ $category }}
                                </h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($permissionsGroup as $permission)
                                        <label
                                            class="relative flex items-start p-3 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm 
                                                bg-white/70 dark:bg-gray-800/70 cursor-pointer hover:border-indigo-400 
                                                dark:hover:border-indigo-500 hover:shadow-md transition duration-200 select-none">

                                            <input 
                                                type="checkbox"
                                                wire:model.defer="selectedPermissions"
                                                value="{{ $permission->value }}"
                                                id="perm-{{ Str::slug($permission->value) }}"
                                                class="form-checkbox shrink-0 mt-0.5 h-5 w-5 text-indigo-600 border-gray-300 rounded 
                                                    focus:ring-indigo-500 cursor-pointer align-middle" />

                                            <div class="ml-3 leading-tight">
                                                <label for="perm-{{ Str::slug($permission->value) }}" class="cursor-pointer">
                                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                                        {{ $permission->label() }}
                                                    </span>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ __('Allows access to ' . Str::lower($permission->label())) }}
                                                    </p>
                                                </label>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Validation Error --}}
                    @error('selectedPermissions')
                        <div class="mt-2 text-red-500 text-sm">
                            {{ $message }}
                        </div>
                    @enderror
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

    {{-- Flash Message --}}
    @if (session('message'))
        <x-flash-message message="{{ session('message') }}" />
    @endif

    {{-- Roles Table --}}
    <section>
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">

            {{-- Search Bar --}}
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
                    <input type="text" 
                        wire:model.live.debounce.500ms="search"
                        class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg 
                               bg-gray-50 focus:ring-blue-500 focus:border-blue-500 
                               dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 
                               dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Search Role or Permission...">
                </div>
            </div>

            {{-- Data Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Role</th>
                            <th scope="col" class="px-6 py-3">Permissions</th>
                            <th scope="col" class="px-6 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr class="border-b dark:border-gray-600">
                                <td class="px-6 py-4 font-medium">{{ $role->name }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($role->permissions as $permission)
                                            <span class="inline-block px-2 py-1 text-xs bg-gray-200 rounded dark:bg-gray-600">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <flux:button wire:click="edit({{ $role->id }})" size="sm" variant="outline">Edit</flux:button>
                                        <flux:button wire:click="confirmDelete({{ $role->id }})" size="sm" variant="outline" class="text-red-600 hover:text-red-700">Delete</flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No roles found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
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
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </section>

    {{-- Delete Confirmation Modal --}}
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
                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this role?</h3>
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

</div>

