<x-slot:header>Sales Price Setup</x-slot:header>
<x-slot:subheader>Manage sales price configurations with description and percentage settings.</x-slot:subheader>

<div>
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
                    <p>The Sales Management module is currently under revision and may not be fully functional. Some features may be incomplete or unavailable. Please use the Product Management module for core inventory operations.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Flash Message -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="transition duration-500 ease-in-out mb-4">
            <x-flash-message />
        </div>
    @endif

    <!-- Create Modal -->
    <x-modal wire:model="showCreateModal" class="max-h-[80vh]">
        <h2 class="text-xl font-bold mb-4">Create Sales Price</h2>
        <form wire:submit.prevent="create" class="space-y-4">
            <div>
                <x-input
                    type="text"
                    wire:model="description"
                    name="description"
                    label="Description"
                    placeholder="Enter description"
                    class="w-full"
                />
                @error('description')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Less (%)
                </label>
                <input
                    type="number"
                    wire:model="less_percentage"
                    name="less_percentage"
                    min="0"
                    max="100"
                    step="0.01"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Enter percentage"
                >
                @error('less_percentage')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end space-x-2 pt-4">
                <x-button type="button" wire:click="closeCreateModal" variant="secondary">Cancel</x-button>
                <x-button type="submit" variant="primary">Create</x-button>
            </div>
        </form>
    </x-modal>

    <!-- Edit Modal -->
    <x-modal wire:model="showEditModal" class="max-h-[80vh]">
        <h2 class="text-xl font-bold mb-4">Edit Sales Price</h2>
        <form wire:submit.prevent="update" class="space-y-4">
            <div>
                <x-input
                    type="text"
                    wire:model="description"
                    name="description"
                    label="Description"
                    placeholder="Enter description"
                    class="w-full"
                />
                @error('description')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Less (%)
                </label>
                <input
                    type="number"
                    wire:model="less_percentage"
                    name="less_percentage"
                    min="0"
                    max="100"
                    step="0.01"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Enter percentage"
                >
                @error('less_percentage')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pricing Note
                </label>
                <textarea
                    wire:model="pricing_note"
                    name="pricing_note"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Enter pricing note (optional)"
                ></textarea>
                @error('pricing_note')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end space-x-2 pt-4">
                <x-button type="button" wire:click="closeEditModal" variant="secondary">Cancel</x-button>
                <x-button type="submit" variant="primary">Update</x-button>
            </div>
        </form>
    </x-modal>

    <!-- Header with Create Button -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <!-- <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sales Price Setup</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage sales price configurations</p> -->
        </div>
        <x-button wire:click="openCreateModal" variant="primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Sales Price
        </x-button>
    </div>

    <!-- Search Bar -->
    <div class="mb-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Search sales prices..."
            >
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Description</th>
                        <th scope="col" class="px-6 py-3">Less (%)</th>
                        <th scope="col" class="px-6 py-3">Pricing Note</th>
                        <th scope="col" class="px-6 py-3">Created At</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($salesPrices as $salesPrice)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $salesPrice->description }}
                            </td>
                            <td class="px-6 py-4">
                                {{ number_format($salesPrice->less_percentage, 2) }}%
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs truncate" title="{{ $salesPrice->pricing_note }}">
                                    {{ $salesPrice->pricing_note ?: '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                {{ $salesPrice->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <button
                                        wire:click="openEditModal({{ $salesPrice->id }})"
                                        class="text-blue-600 hover:text-blue-800 dark:hover:text-blue-400"
                                        title="Edit"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="delete({{ $salesPrice->id }})"
                                        wire:confirm="Are you sure you want to delete this sales price?"
                                        class="text-red-600 hover:text-red-800 dark:hover:text-red-400"
                                        title="Delete"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400">No sales prices found.</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Get started by creating your first sales price.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="py-4 px-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Per Page Selection -->
                <div class="flex items-center space-x-4">
                    <label for="perPage" class="text-sm font-medium text-gray-900 dark:text-white">Per Page</label>
                    <select
                        id="perPage"
                        wire:model.live="perPage"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <!-- Pagination Links -->
                <div>
                    {{ $salesPrices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
