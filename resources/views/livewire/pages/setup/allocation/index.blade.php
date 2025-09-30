<x-slot:header>Allocations</x-slot:header>
<x-slot:subheader>Allocation Management</x-slot:subheader>
<div class="">
    <div class="">
        <x-collapsible-card title="Create Allocation" open="false" size="full">
            <form wire:submit.prevent="create" x-show="open" x-transition>
                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <x-input type="text" wire:model="name" name="name" label="Allocation Name" placeholder="Enter allocation name" />
                    </div>
                    <div>
                        <x-input type="text" wire:model="description" name="description" label="Description" placeholder="Enter description" />
                    </div>
                </div>
                <div class="flex justify-end">
                    <x-button type="submit" variant="primary">Submit</x-button>
                </div>
            </form>
        </x-collapsible-card>

        @if (session('message'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 4000)"
                class="transition duration-500 ease-in-out"
                x-transition
            >
                <x-flash-message />
            </div>
        @endif

        <x-collapsible-card title="Allocations List" open="true" size="full">
            <div class="flex items-center justify-between p-4 pr-10">
                <div class="flex space-x-6">
                    <div class="relative">
                        <x-input type="text" wire:model.live="search" name="search" label="Search" placeholder="Search allocations..." />
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Allocation Name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Description
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allocations as $allocation)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <td class="px-6 py-4">
                                {{ $allocation->name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $allocation->description ?: 'No Description' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <x-button type="button" wire:click="edit({{ $allocation->id }})" variant="warning" size="sm">Edit</x-button>
                                    <x-button type="button" wire:click="confirmDelete({{ $allocation->id }})" variant="danger" size="sm">Delete</x-button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div x-data="{ show: @entangle('showEditModal') }" x-show="show" x-cloak class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                <div class="relative w-full max-w-2xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Edit Allocation
                            </h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" wire:click="cancel">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid gap-6 mb-6 md:grid-cols-2">
                                <div>
                                    <x-input type="text" wire:model="name" name="name" label="Allocation Name" placeholder="Enter allocation name" />
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <x-input type="text" wire:model="description" name="description" label="Description" placeholder="Enter description" />
                                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <x-button type="button" wire:click="{{ $editingAllocationId ? 'update' : 'create' }}" variant="primary">{{ $editingAllocationId ? 'Save changes' : 'Submit' }}</x-button>
                            <x-button type="button" wire:click="cancel" variant="secondary">Cancel</x-button>
                        </div>
                    </div>
                </div>
            </div>

            <div x-data="{ show: @entangle('showDeleteModal') }" x-show="show" x-cloak class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
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
                            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this allocation?</h3>
                            <div class="flex justify-center space-x-2">
                                <x-button type="button" wire:click="delete" variant="danger">Yes, I'm sure</x-button>
                                <x-button type="button" wire:click="cancel" variant="secondary">No, cancel</x-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-4 px-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-900 dark:text-white">Per Page:</label>
                        <x-dropdown wire:model.live="perPage" name="perPage" :options="[
                            '5' => '5',
                            '10' => '10',
                            '25' => '25',
                            '50' => '50',
                            '100' => '100'
                        ]" />
                    </div>
                    <div>
                        {{ $allocations->links() }}
                    </div>
                </div>
            </div>
        </x-collapsible-card>
    </div>
</div>
