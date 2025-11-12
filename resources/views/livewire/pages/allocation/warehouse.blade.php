<x-slot:header>Allocation - Warehouse</x-slot:header>
<x-slot:subheader>Master control panel for preparing and sending goods from warehouse to branches through delivery batches.</x-slot:subheader>

@script
<script>
// VDR CSV Download Handler
window.addEventListener('download-vdr', (event) => {
    const { content, filename } = event.detail;
    
    // Create a blob with the CSV content
    const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' });
    const url = window.URL.createObjectURL(blob);
    
    // Create a temporary link and trigger download
    const link = document.createElement('a');
    if (link.download !== undefined) {
        link.href = url;
        link.download = filename;
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    // Clean up
    window.URL.revokeObjectURL(url);
});

// VDR Print Handler
window.addEventListener('open-vdr-print', (event) => {
    const { batchId, preparedBy } = event.detail;
    
    // Open print window with the VDR content
    const printUrl = preparedBy ? `/allocation/vdr/print/${batchId}?prepared_by=${encodeURIComponent(preparedBy)}` : `/allocation/vdr/print/${batchId}`;
    window.open(printUrl, '_blank', 'width=800,height=600');
});

// VDR Excel Download Handler
window.addEventListener('open-excel-download', (event) => {
    const { url } = event.detail;
    
    // Open the Excel export URL which will trigger a download
    window.open(url, '_blank');
});

// Alternative: Listen for Livewire events
document.addEventListener('DOMContentLoaded', function() {
    // VDR CSV Download
    window.Livewire.on('download-vdr', (data) => {
        const { content, filename } = data[0];
        
        const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        
        const link = document.createElement('a');
        if (link.download !== undefined) {
            link.href = url;
            link.download = filename;
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        window.URL.revokeObjectURL(url);
    });

    // VDR Print
    window.Livewire.on('open-vdr-print', (data) => {
        const { batchId, preparedBy } = data[0];
        
        const printUrl = preparedBy ? `/allocation/vdr/print/${batchId}?prepared_by=${encodeURIComponent(preparedBy)}` : `/allocation/vdr/print/${batchId}`;
        window.open(printUrl, '_blank', 'width=800,height=600');
    });

    // VDR Excel Download
    window.Livewire.on('open-excel-download', (data) => {
        const { url } = data[0];
        
        // Open the Excel export URL which will trigger a download
        window.open(url, '_blank');
    });
});
</script>
@endscript

<div>
    <!-- Success/Error Messages -->
    @if (session()->has('message'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="ml-2 text-green-700 dark:text-green-300">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span class="ml-2 text-red-700 dark:text-red-300">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- STEP 1: CREATE BATCH ALLOCATION -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Warehouse Batch Allocations</h2>
            <button wire:click="openCreateBatchModal" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create New Batch
            </button>
        </div>

        <!-- Search and Date Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search Filter -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Search Batches
                    </label>
                    <div class="relative">
                        <input type="text"
                               id="search"
                               wire:model.live="search"
                               placeholder="Search by reference no, remarks, or branch name..."
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Date From Filter -->
                <div>
                    <label for="dateFrom" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Date From
                    </label>
                    <input type="date"
                           id="dateFrom"
                           wire:model.live="dateFrom"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                </div>

                <!-- Date To Filter -->
                <div>
                    <label for="dateTo" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Date To
                    </label>
                    <div class="flex space-x-2">
                        <input type="date"
                               id="dateTo"
                               wire:model.live="dateTo"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <button wire:click="clearFilters"
                                class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Filters Display -->
            @if($search || $dateFrom || $dateTo)
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Active filters:</span>
                    @if(isset($search) && $search)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                            Search: "{{ $search }}"
                            <button wire:click="$set('search', '')" class="ml-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if(isset($dateFrom) && $dateFrom)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                            From: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}
                            <button wire:click="$set('dateFrom', '')" class="ml-1 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if(isset($dateTo) && $dateTo)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                            To: {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                            <button wire:click="$set('dateTo', '')" class="ml-1 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Batch Allocations List -->
        @forelse($batchAllocations as $batch)
            <x-batch-card
                :title="$batch->ref_no"
                :batch-id="$batch->id"
                :open="$openBatches[$batch->id] ?? false"
            >
                <!-- Batch Header Content -->
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Delivery Date: {{ \Carbon\Carbon::parse($batch->transaction_date)->format('M d, Y') }}
                        </p>
                        @if($batch->remarks)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Remarks: {{ $batch->remarks }}</p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 text-xs font-medium rounded-full
                            @if($batch->status === 'draft') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300
                            @elseif($batch->status === 'dispatched') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300 @endif">
                            {{ ucfirst($batch->status) }}
                        </span>
                        @if($batch->status === 'draft')
                            <button wire:click="openAddBranchesModal({{ $batch->id }})" class="bg-green-600 hover:bg-green-700 text-white text-sm py-1 px-3 rounded">
                                Add Branches
                            </button>
                            <button wire:click="dispatchBatch({{ $batch->id }})"
                                    wire:confirm="Are you sure you want to dispatch this batch? This action cannot be undone."
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm py-1 px-3 rounded">
                                Dispatch Batch
                            </button>
                        @endif
                        @if($batch->branchAllocations->count() > 0)
                            <button wire:click="openVDRPreview({{ $batch->id }})" class="bg-purple-600 hover:bg-purple-700 text-white text-sm py-1 px-3 rounded">
                                Export to Excel
                            </button>
                        @endif
                    </div>
                </div>

                <!-- STEP 2 & 3: BRANCHES AND ITEMS -->
                <div class="space-y-4">
                    @forelse($batch->branchAllocations as $branchAllocation)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 dark:text-white">{{ $branchAllocation->branch->name }}</h4>
                                    @if($branchAllocation->remarks)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Remarks: {{ $branchAllocation->remarks }}</p>
                                    @endif
                                    <span class="inline-block mt-1 px-2 py-1 text-xs font-medium rounded-full
                                        @if($branchAllocation->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300
                                        @elseif($branchAllocation->status === 'allocated') bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300
                                        @elseif($branchAllocation->status === 'received') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300 @endif">
                                        {{ ucfirst($branchAllocation->status) }}
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    @if($batch->status === 'draft')
                                        <button wire:click="openAddItemsModal({{ $branchAllocation->id }})" class="bg-green-600 hover:bg-green-700 text-white text-sm py-1 px-3 rounded">
                                            Add Items
                                        </button>
                                        <button wire:click="removeBranch({{ $branchAllocation->id }})"
                                                wire:confirm="Are you sure you want to remove this branch from the batch?"
                                                class="bg-red-600 hover:bg-red-700 text-white text-sm py-1 px-3 rounded">
                                            Remove
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Items Table -->
                            <div class="space-y-2">
                                @forelse($branchAllocation->items as $item)
                                    <div class="bg-white dark:bg-gray-800 rounded p-3 border border-gray-200 dark:border-gray-600">
                                        <div class="flex justify-between items-center">
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400 space-x-4">
                                                    <span>Qty: {{ $item->quantity }}</span>
                                                    @if($item->product->price ?? $item->product->selling_price ?? null)
                                                        <span>Selling: ₱{{ number_format($item->product->price ?? $item->product->selling_price ?? 0, 2) }}</span>
                                                    @endif
                                                    @if($item->unit_price)
                                                        <span>Unit: ₱{{ number_format($item->unit_price, 2) }}</span>
                                                        <span class="font-medium text-blue-600 dark:text-blue-400">Total: ₱{{ number_format($item->quantity * $item->unit_price, 2) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($batch->status === 'draft')
                                                <div class="flex space-x-2">
                                                    <button wire:click="openEditItemModal({{ $item->id }})"
                                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                    <button wire:click="removeItem({{ $item->id }})"
                                                            wire:confirm="Are you sure you want to remove this item?"
                                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                                        <p class="text-sm text-yellow-700 dark:text-yellow-300">No items assigned to this branch.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                            <p class="text-gray-500 dark:text-gray-400">No branches added to this batch yet.</p>
                            @if($batch->status === 'draft')
                                <button wire:click="openAddBranchesModal({{ $batch->id }})" class="mt-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    Add branches to this batch
                                </button>
                            @endif
                        </div>
                    @endforelse
                </div>
            </x-batch-card>
        @empty
            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No batch allocations</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first batch allocation.</p>
            </div>
        @endforelse
    </div>

    <!-- STEP 1: CREATE BATCH MODAL -->
    <x-modal wire:model="showCreateBatchModal" class="max-w-lg">
        <h2 class="text-xl font-bold mb-4">Create New Batch Allocation</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Start a new delivery wave. Example: "Batch 1 – Delivery for Big Branches on Nov 6, 2025"
        </p>
        
        <form wire:submit="createBatch" class="space-y-4">
            <div>
                <label for="ref_no" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Reference Number
                </label>
                <input type="text"
                       id="ref_no"
                       wire:model="ref_no"
                       readonly
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 dark:bg-gray-500 dark:border-gray-500 dark:text-white">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Auto-generated reference number</p>
            </div>

            <div>
                <label for="transaction_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Delivery Date *
                </label>
                <input type="date"
                       id="transaction_date"
                       wire:model="transaction_date"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                       required>
                @error('transaction_date')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Remarks (Optional)
                </label>
                <textarea id="remarks"
                          wire:model="remarks"
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                          placeholder="e.g., 'Dispatched by Mark', 'For VisMin route'"></textarea>
                @error('remarks')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Status
                </label>
                <select id="status"
                        wire:model="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    <option value="draft">Draft</option>
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Status is automatically set to draft</p>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button type="button"
                        wire:click="closeCreateBatchModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create Batch
                </button>
            </div>
        </form>
    </x-modal>

    <!-- STEP 2: ADD BRANCHES MODAL -->
    <x-modal wire:model="showAddBranchesModal" class="max-w-2xl">
        <h2 class="text-xl font-bold mb-4">Add Branches to Batch: {{ $selectedBatch?->ref_no ?? '' }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Select one or multiple branches to receive products in this batch.
        </p>
        
        <form wire:submit="addBranchesToBatch" class="space-y-4">
            @forelse($availableBranches as $branch)
                <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <input type="checkbox"
                           id="branch_{{ $branch->id }}"
                           wire:model="selectedBranchIds"
                           value="{{ $branch->id }}"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="branch_{{ $branch->id }}" class="ml-3 flex-1">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $branch->name }}</div>
                        @if($branch->address)
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $branch->address }}</div>
                        @endif
                    </label>
                </div>
                @if(in_array($branch->id, $selectedBranchIds))
                    <div class="ml-6">
                        <label for="branch_remarks_{{ $branch->id }}" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Remarks for {{ $branch->name }} (Optional)
                        </label>
                        <input type="text"
                               id="branch_remarks_{{ $branch->id }}"
                               wire:model="branchRemarks.{{ $branch->id }}"
                               placeholder="e.g., 'Handled by Agent A'"
                               class="w-full px-2 py-1 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    </div>
                @endif
            @empty
                <div class="text-center py-4">
                    <p class="text-gray-500 dark:text-gray-400">No additional branches available to add.</p>
                </div>
            @endforelse

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button type="button"
                        wire:click="closeAddBranchesModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                    Cancel
                </button>
                <button type="submit"
                        @if(count($availableBranches) === 0) disabled @endif
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-400">
                    Add Selected Branches
                </button>
            </div>
        </form>
    </x-modal>

    <!-- STEP 3: ADD ITEMS MODAL -->
    <x-modal wire:model="showAddItemsModal" class="max-w-2xl">
        <h2 class="text-xl font-bold mb-4">Add Items to Branch</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            {{ $selectedBranchAllocation?->branch->name ?? 'Selected Branch' }}
        </p>
        
        <!-- Existing Items Display -->
        @if($selectedBranchAllocation && $selectedBranchAllocation->items->count() > 0)
            <div class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2">Current Items in this Branch:</h4>
                <div class="space-y-1">
                    @foreach($selectedBranchAllocation->items as $existingItem)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-yellow-700 dark:text-yellow-300">{{ $existingItem->product->name }}</span>
                            <span class="text-yellow-600 dark:text-yellow-400">Qty: {{ $existingItem->quantity }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <form wire:submit="addItemToBranch" class="space-y-4">
            <div>
                <label for="selectedProductId" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Product * @if($selectedBranchAllocation && $selectedBranchAllocation->items->count() > 0)<span class="text-xs text-orange-600 dark:text-orange-400">(Already added products are disabled)</span>@endif
                </label>
                <select id="selectedProductId"
                        wire:model="selectedProductId"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    <option value="">Select a product</option>
                    @foreach($availableProducts as $product)
                        @php
                            $isExisting = $selectedBranchAllocation && $selectedBranchAllocation->items->where('product_id', $product->id)->isNotEmpty();
                            $productPrice = $product->price ?? $product->selling_price ?? null;
                        @endphp
                        <option value="{{ $product->id }}" @disabled($isExisting)>
                            {{ $product->name }} @if($productPrice) (₱{{ number_format($productPrice, 2) }}) @endif @if($isExisting) (Already Added) @endif
                        </option>
                    @endforeach
                </select>
                @error('selectedProductId')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="productQuantity" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Quantity *
                </label>
                <input type="number"
                       id="productQuantity"
                       wire:model="productQuantity"
                       min="1"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                @error('productQuantity')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="productUnitPrice" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Unit Price (Optional)
                </label>
                <input type="number"
                       id="productUnitPrice"
                       wire:model="productUnitPrice"
                       min="0"
                       step="0.01"
                       placeholder="0.00"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                @error('productUnitPrice')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button type="button"
                        wire:click="closeAddItemsModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Add Item
                </button>
            </div>
        </form>
    </x-modal>

    <!-- STEP 4: EDIT ITEM MODAL -->
    <x-modal wire:model="showEditItemModal" class="max-w-2xl">
        <h2 class="text-xl font-bold mb-4">Edit Item</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            {{ $selectedEditItem?->branchAllocation?->branch->name ?? 'Selected Branch' }} - {{ $selectedEditItem?->product->name ?? 'Selected Product' }}
        </p>
        
        @if($selectedEditItem)
            <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Product Information:</h4>
                <div class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                    <div>Product: {{ $selectedEditItem->product->name }}</div>
                    @if($selectedEditItem->product->price ?? $selectedEditItem->product->selling_price ?? null)
                        <div>Selling Price: ₱{{ number_format($selectedEditItem->product->price ?? $selectedEditItem->product->selling_price ?? 0, 2) }}</div>
                    @endif
                    <div class="font-medium">Current Allocation: Qty: {{ $selectedEditItem->quantity }} @if($selectedEditItem->unit_price) at ₱{{ number_format($selectedEditItem->unit_price, 2) }} = ₱{{ number_format($selectedEditItem->quantity * $selectedEditItem->unit_price, 2) }} @endif</div>
                </div>
            </div>
        @endif
        
        <form wire:submit="updateItem" class="space-y-4">
            <div>
                <label for="editProductQuantity" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Quantity *
                </label>
                <input type="number"
                       id="editProductQuantity"
                       wire:model="editProductQuantity"
                       min="1"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                @error('editProductQuantity')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="editProductUnitPrice" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Unit Price (Optional)
                </label>
                <input type="number"
                       id="editProductUnitPrice"
                       wire:model="editProductUnitPrice"
                       min="0"
                       step="0.01"
                       placeholder="0.00"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                @error('editProductUnitPrice')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            @if($editProductQuantity && $editProductUnitPrice)
                <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <div class="text-sm text-green-700 dark:text-green-300">
                        <div>New Total: Qty {{ $editProductQuantity }} × ₱{{ number_format($editProductUnitPrice, 2) }} = <span class="font-bold">₱{{ number_format($editProductQuantity * $editProductUnitPrice, 2) }}</span></div>
                        @if($selectedEditItem && ($selectedEditItem->product->price ?? $selectedEditItem->product->selling_price ?? null))
                            <div>Difference: ₱{{ number_format(($editProductQuantity * $editProductUnitPrice) - ($selectedEditItem->quantity * $selectedEditItem->unit_price), 2) }}</div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button type="button"
                        wire:click="closeEditItemModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Item
                </button>
            </div>
        </form>
    </x-modal>

    <!-- VDR PREVIEW MODAL -->
    <x-modal wire:model="showVDRPreviewModal" class="max-w-4xl">
        <h2 class="text-xl font-bold mb-4">VDR Preview & Export</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            Validate Delivery Receipt for Batch: {{ $selectedBatchForVDR?->ref_no ?? '' }}
        </p>

        @if($selectedBatchForVDR)
            <!-- Vendor Information Form -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Vendor Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="vendorCode" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Vendor Code *
                        </label>
                        <input type="text"
                               id="vendorCode"
                               wire:model="vendorCode"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                               placeholder="104148">
                        @error('vendorCode')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="vendorName" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Vendor Name *
                        </label>
                        <input type="text"
                               id="vendorName"
                               wire:model="vendorName"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                               placeholder="JKF CORP.">
                        @error('vendorName')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="preparedBy" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Prepared By *
                        </label>
                        <input type="text"
                               id="preparedBy"
                               wire:model="preparedBy"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                               placeholder="Your name">
                        @error('preparedBy')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- VDR Preview Table -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">VDR Preview</h3>
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">DR#</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Store Code</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Store Name</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Exp. Date</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">SKU #</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">SKU Description</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @php
                                    $totalQty = 0;
                                    $totalBoxes = 0;
                                    $uniqueSkus = collect();
                                @endphp
                                
                                @foreach($selectedBatchForVDR->branchAllocations as $branchAllocation)
                                    @foreach($branchAllocation->items as $item)
                                        @php
                                            $totalQty += $item->quantity;
                                            $uniqueSkus->push($item->product->sku ?? $item->product->id);
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $selectedBatchForVDR->ref_no }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $branchAllocation->branch->code ?? '' }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $branchAllocation->branch->name ?? '' }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($selectedBatchForVDR->transaction_date)->format('m/d/y') }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $item->product->sku ?? $item->product->id }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $item->product->name ?? '' }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-white font-medium">{{ $item->quantity }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                
                                <!-- Summary Row -->
                                <tr class="bg-gray-50 dark:bg-gray-700 font-medium">
                                    <td colspan="6" class="px-3 py-2 text-sm text-gray-900 dark:text-white">TOTAL QTY:</td>
                                    <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $totalQty }}</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-700 font-medium">
                                    <td colspan="6" class="px-3 py-2 text-sm text-gray-900 dark:text-white">TOTAL BOXES:</td>
                                    <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $totalBoxes }}</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-700 font-medium">
                                    <td colspan="6" class="px-3 py-2 text-sm text-gray-900 dark:text-white">TOTAL SKU/S:</td>
                                    <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $uniqueSkus->unique()->count() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                <div class="flex space-x-3">
                    <button type="button"
                            wire:click="printVDR"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print VDR
                    </button>
                    <button type="button"
                            wire:click="exportVDRToExcel"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export to Excel
                    </button>
                </div>
                <div>
                    <button type="button"
                            wire:click="closeVDRPreview"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                        Close
                    </button>
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-gray-500 dark:text-gray-400">No batch selected for VDR preview.</p>
            </div>
        @endif
    </x-modal>
</div>