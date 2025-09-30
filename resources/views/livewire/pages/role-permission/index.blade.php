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
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Permissions</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($permissions as $permission)
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model.defer="selectedPermissions"
                                    value="{{ $permission->name }}"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring focus:ring-indigo-200" />
                                <span class="ml-2 text-sm text-gray-700 dark:text-white">{{ $permission->name }}</span>
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

    <div class="mt-6 overflow-x-auto">
        <table
            class="w-full text-sm text-left text-gray-700 bg-white border border-gray-200 rounded shadow dark:bg-gray-800 dark:text-gray-200">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3">Role</th>
                    <th class="px-6 py-3">Permissions</th>
                    <th class="px-6 py-3 text-center">Action</th>
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

        <div class="mt-4">
            {{ $roles->links() }}
        </div>
    </div>
</div>
