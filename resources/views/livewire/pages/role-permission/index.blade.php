<x-slot:header>Roles & Permissions</x-slot:header>
<x-slot:subheader>Manage role access</x-slot:subheader>

<div class="space-y-6">

    {{-- Add Role Form --}}
    <x-collapsible-card title="Add Role" open="false" size="full">
        <form wire:submit.prevent="store" x-show="open" x-transition>
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <x-input type="text" wire:model="name" name="name" label="Role Name"
                        placeholder="Enter role name" />
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                

                <div class="md:col-span-2">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-semibold text-gray-800 dark:text-gray-100 tracking-wide">
                        Permissions
                    </label>

                    {{-- Select / Deselect All Toggle --}}
                    <div class="flex items-center gap-3 text-sm">
                        <button type="button"
                            wire:click="selectAllPermissions"
                            class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/40 dark:text-indigo-300 dark:hover:bg-indigo-800/60 font-medium rounded-lg transition duration-200 shadow-sm">
                            {{-- Checkmark icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Select All
                        </button>

                        <button type="button"
                            wire:click="deselectAllPermissions"
                            class="inline-flex items-center px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 dark:bg-rose-900/40 dark:text-rose-300 dark:hover:bg-rose-800/60 font-medium rounded-lg transition duration-200 shadow-sm">
                            {{-- X icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Deselect All
                        </button>
                    </div>
                </div>

                {{-- Permission Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($permissions as $permission)
                        <label
                            class="relative flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm bg-white/70 dark:bg-gray-800/70 cursor-pointer hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md transition duration-200">
                            
                            <input type="checkbox"
                                wire:model.defer="selectedPermissions"
                                value="{{ $permission->name }}"
                                class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 focus:ring-offset-1 transition" />

                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                    {{ Str::headline($permission->name) }}
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Allows access to ' . Str::lower($permission->name)) }}
                                </p>
                            </div>

                            {{-- Optional checkmark icon when selected --}}
                            @if (in_array($permission->name, $selectedPermissions))
                                <span class="absolute top-2 right-2 text-indigo-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414L9 13.414l4.707-4.707z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                            @endif
                        </label>
                    @endforeach
                </div>
            </div>

            </div>

            <div class="flex justify-end">
                <x-button type="submit" variant="primary">Submit</x-button>
            </div>
        </form>
    </x-collapsible-card>

    {{-- Roles Table --}}
    @if (session('message'))
        <x-flash-message message="{{ session('message') }}" />
    @endif

    <section>
    <div>
        <!-- Main Container -->
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            
            <!-- 🔍 Search Bar -->
            <div class="flex items-center justify-between p-4 pr-10">
                <div class="flex space-x-6">
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
                            placeholder="Search Role or Permission..."
                        >
                    </div>
                </div>
            </div>

            <!-- 📋 Data Table -->
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
                                    <div class="flex items-center justify-center gap-2">
                                        <x-button wire:click="edit({{ $role->id }})" size="sm"
                                            variant="warning">Edit</x-button>
                                        <x-button wire:click="confirmDelete({{ $role->id }})" size="sm"
                                            variant="danger">Delete</x-button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No roles
                                    found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- 📄 Pagination -->
            <div class="py-4 px-3">
                <div class="flex items-center justify-between">
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

                </div>
            </div>
        </div>
    </div>
</section>

</div>
