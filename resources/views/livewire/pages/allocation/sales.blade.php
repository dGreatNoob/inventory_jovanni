<x-slot:header>Allocation - Sales</x-slot:header>
<x-slot:subheader>Branch receipt management system for incoming deliveries from warehouse.</x-slot:subheader>

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

    <!-- STEP 1: SALES ALLOCATION LANDING PAGE -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Sales Allocation Management</h2>

        <!-- Batch Selector -->
        @if(count($availableBatches) > 0)
            <div class="mb-6">
                <label for="selectedBatchId" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Select Batch
                </label>
                <select id="selectedBatchId"
                        wire:model="selectedBatchId"
                        wire:change="loadBatchReceipts"
                        class="w-full max-w-md px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    <option value="">Select a dispatched batch...</option>
                    @foreach($availableBatches as $batch)
                        <option value="{{ $batch->id }}">{{ $batch->ref_no }} - {{ \Carbon\Carbon::parse($batch->transaction_date)->format('M d, Y') }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <!-- Branches Table -->
        @if($selectedBatchId && count($batchReceipts) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Branches in Batch
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Branch Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Total Items
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Total Qty
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($batchReceipts as $receipt)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $receipt->branch->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $receipt->total_items }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $receipt->total_received_qty }} / {{ $receipt->total_allocated_qty }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $this->getStatusBadgeClass($receipt->status) }}">
                                            {{ ucfirst($receipt->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="viewReceiptDetails({{ $receipt->id }})"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            View Allocation
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif($selectedBatchId)
            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No branch receipts</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This batch has no branch receipts yet.</p>
            </div>
        @else
            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m10-3h2m-3 3h2m-4 3h2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No batches available</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No dispatched batches available for receipt management.</p>
            </div>
        @endif
    </div>

    <!-- STEP 2: VIEW A BRANCH'S ALLOCATION (Receipt Details Modal) -->
    <x-modal wire:model.live="showReceiptDetails" class="max-w-6xl">
        <x-slot:title>
            @if($selectedReceipt)
                Receipt: {{ $selectedReceipt->branch->name }}
            @else
                Loading...
            @endif
        </x-slot:title>
        @if($selectedReceipt)
            <div class="space-y-6">
                <!-- Receipt Header -->
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                            Batch: {{ $selectedReceipt->batchAllocation->ref_no }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Transaction Date: {{ \Carbon\Carbon::parse($selectedReceipt->batchAllocation->transaction_date)->format('M d, Y') }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Received From: Warehouse
                        </p>
                        @if($selectedReceipt->date_received)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Date Received: {{ \Carbon\Carbon::parse($selectedReceipt->date_received)->format('M d, Y') }}
                            </p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $this->getStatusBadgeClass($selectedReceipt->status) }}">
                            {{ ucfirst($selectedReceipt->status) }}
                        </span>
                        @if($selectedReceipt->status === 'pending')
                            <button wire:click="openConfirmModal({{ $selectedReceipt->id }})"
                                    class="bg-green-600 hover:bg-green-700 text-white text-sm py-1 px-3 rounded">
                                Confirm Receipt
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Product
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Allocated Qty
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Received Qty
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Sold Qty
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Damaged
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Missing
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($selectedReceipt->items as $item)
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</div>
                                        @if($item->remarks)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $item->remarks }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $item->allocated_qty }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $item->received_qty }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $item->sold_qty }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $item->damaged_qty }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $item->missing_qty }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $this->getStatusBadgeClass($item->status) }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex justify-end space-x-2">
                                            @if($selectedReceipt->status === 'pending')
                                                <button wire:click="openEditItemModal({{ $item->id }})"
                                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                                    Edit
                                                </button>
                                            @endif
                                            @if($selectedReceipt->status === 'received' && $item->sold_qty < $item->received_qty)
                                                <button wire:click="markAsSold({{ $item->id }}, 1)"
                                                        class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm">
                                                    Mark Sold
                                                </button>
                                            @endif
                                            @if($item->sold_at)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Sold: {{ \Carbon\Carbon::parse($item->sold_at)->format('M d, Y H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-500 dark:text-gray-400">Loading receipt details...</div>
            </div>
        @endif
    </x-modal>

    <!-- STEP 3: CONFIRM RECEIPT MODAL -->
    @if($showConfirmModal && $selectedReceipt && count($receiptItems) > 0)
        <x-modal wire:model.live="showConfirmModal" class="max-w-4xl">
            <x-slot:title>
                Confirm Receipt of Goods
            </x-slot:title>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                {{ $selectedReceipt->branch->name }} - Batch: {{ $selectedReceipt->batchAllocation->ref_no }}
            </p>
            <p class="text-sm text-yellow-600 dark:text-yellow-400 mb-4">
                <strong>Note:</strong> After confirming receipt, warehouse will be locked and no further edits will be allowed.
            </p>

            <div class="space-y-4">
                @foreach($receiptItems as $index => $item)
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ $item['product_name'] }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Received Quantity *
                                </label>
                                <input type="number"
                                       wire:model.live="receiptItems.{{ $index }}.received_qty"
                                       min="0"
                                       max="{{ $item['allocated_qty'] }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Damaged Quantity
                                </label>
                                <input type="number"
                                       wire:model.live="receiptItems.{{ $index }}.damaged_qty"
                                       min="0"
                                       max="{{ $item['allocated_qty'] }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Missing Quantity
                                </label>
                                <div class="px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 dark:bg-gray-500 dark:border-gray-500 text-gray-500 dark:text-gray-400">
                                    {{ $item['allocated_qty'] - ($item['received_qty'] + $item['damaged_qty']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600 mt-6">
                <button type="button"
                        wire:click="closeConfirmModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                    Cancel
                </button>
                <button type="button"
                        wire:click="confirmReceipt"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Confirm Receipt
                </button>
            </div>
        </x-modal>
    @endif

    <!-- STEP 6: EDIT ITEM MODAL -->
    @if($showEditItemModal && $itemId)
        <x-modal wire:model.live="showEditItemModal" class="max-w-lg">
            <x-slot:title>
                Edit Item Details
            </x-slot:title>
            
            <div class="space-y-4">
                <div>
                    <label for="received_qty" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Received Quantity *
                    </label>
                    <input type="number"
                           id="received_qty"
                           wire:model.live="received_qty"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    @error('received_qty')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="damaged_qty" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Damaged Quantity
                    </label>
                    <input type="number"
                           id="damaged_qty"
                           wire:model.live="damaged_qty"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    @error('damaged_qty')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="missing_qty" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Missing Quantity (Auto-calculated)
                    </label>
                    <div class="px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 dark:bg-gray-500 dark:border-gray-500 text-gray-500 dark:text-gray-400">
                        {{ $missing_qty ?? 0 }}
                    </div>
                </div>

                <div>
                    <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Remarks
                    </label>
                    <textarea id="remarks"
                              wire:model.live="remarks"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                              placeholder="Any notes about this item..."></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600 mt-6">
                <button type="button"
                        wire:click="closeEditItemModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                    Cancel
                </button>
                <button type="button"
                        wire:click="updateItem"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Item
                </button>
            </div>
        </x-modal>
    @endif
</div>