<x-slot:header>Branch Management</x-slot:header>
<x-slot:subheader>Inventory</x-slot:subheader>
<div class="pt-4">
    <div class="space-y-6">
        @include('livewire.pages.branch.branch-management-tabs')
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">

            <h5 class="font-medium mb-3">Branch Inventory</h5>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Batch-based inventory showing branches with their assigned batch numbers. Select a batch to view branches.
            </p>

            <!-- Batch Selection Buttons -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <!-- Batch 1 Button -->
                <button wire:click="selectBatch(1)"
                        class="p-6 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Batch 1</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Branches in Batch 1</p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900/20 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h10a2 2 0 012 2v2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">BRANCHES</span>
                            <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full text-xs font-medium text-gray-600 dark:text-gray-300">
                                {{ $batch1BranchesCount ?? 0 }} branches
                            </span>
                        </div>
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            View branches assigned to Batch 1
                        </div>
                    </div>
                </button>

                <!-- Batch 2 Button -->
                <button wire:click="selectBatch(2)"
                        class="p-6 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-green-500 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Batch 2</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Branches in Batch 2</p>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900/20 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h10a2 2 0 012 2v2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">BRANCHES</span>
                            <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full text-xs font-medium text-gray-600 dark:text-gray-300">
                                {{ $batch2BranchesCount ?? 0 }} branches
                            </span>
                        </div>
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            View branches assigned to Batch 2
                        </div>
                    </div>
                </button>

                <!-- Batch 3 Button -->
                <button wire:click="selectBatch(3)"
                        class="p-6 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-purple-500 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Batch 3</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Branches in Batch 3</p>
                        </div>
                        <div class="bg-purple-100 dark:bg-purple-900/20 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h10a2 2 0 012 2v2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">BRANCHES</span>
                            <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full text-xs font-medium text-gray-600 dark:text-gray-300">
                                {{ $batch3BranchesCount ?? 0 }} branches
                            </span>
                        </div>
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            View branches assigned to Batch 3
                        </div>
                    </div>
                </button>
            </div>

            <!-- Selected Batch Content -->
            @if($selectedBatch)
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Batch {{ $selectedBatch }} - Branches</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Branches assigned to Batch {{ $selectedBatch }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="refreshBatchData"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Refresh
                            </button>
                            <button wire:click="clearSelection"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Batch Branches Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Branch Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Batch Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($batchBranches as $branch)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $branch['name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 dark:text-blue-400 font-medium">
                                            {{ $branch['batch_number'] ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $branch['reference'] ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <button wire:click="viewBranchDetails({{ $branch['id'] }})"
                                                    class="px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                                Details
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                                <svg class="w-16 h-16 mb-4 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                                <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    No branches found
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    No branches are assigned to Batch {{ $selectedBatch }}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Batch Summary -->
                    @if(count($batchBranches) > 0)
                        <div class="mt-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Batch {{ $selectedBatch }} Summary</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Total Branches:</span>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ count($batchBranches) }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Batch Number:</span>
                                    <div class="font-medium text-blue-600 dark:text-blue-400">{{ $selectedBatch }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Branch Details Modal -->
    @if($showBranchDetailsModal)
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50" wire:click="closeBranchDetailsModal">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto" @click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Branch Details</h3>
                    <button wire:click="closeBranchDetailsModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                @if($selectedBranchDetails)
                    <div class="space-y-4">
                        <!-- Branch Info -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Branch Information</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Name:</span>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $selectedBranchDetails['name'] }}</div>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Code:</span>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $selectedBranchDetails['code'] ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Address:</span>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $selectedBranchDetails['address'] ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Batch Number:</span>
                                    <div class="font-medium text-blue-600 dark:text-blue-400">{{ $selectedBranchDetails['batch_number'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Allocation Info -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Allocation Summary</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Reference:</span>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $selectedBranchDetails['reference'] ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Allocation ID:</span>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $selectedBranchDetails['allocation_id'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                            <button wire:click="closeBranchDetailsModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Close
                            </button>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">Loading branch details...</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>