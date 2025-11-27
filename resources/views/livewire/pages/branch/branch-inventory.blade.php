<x-slot:header>Branch Management</x-slot:header>
<x-slot:subheader>Inventory</x-slot:subheader>
<div class="pt-4">
    <div class="space-y-6">
        @include('livewire.pages.branch.branch-management-tabs')
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
            
            <h5 class="font-medium mb-3">Branch Inventory</h5>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Current stock levels for each branch and product. This view is read-only.
            </p>
            

            @if($branches->count() && $products->count())
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800 border-collapse border border-gray-300 dark:border-gray-600">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700">Branch</th>
                            @foreach($products as $product)
                                <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase min-w-[120px] bg-gray-50 dark:bg-gray-700">
                                    {{ $product->name }}<br>
                                    <span class="text-xs text-gray-400">₱{{ number_format($product->price ?? $product->selling_price ?? 0, 2) }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branches as $branch)
                            <tr>
                                <td class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700">
                                    {{ $branch->name }}
                                </td>
                                @foreach($products as $product)
                                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left bg-white dark:bg-gray-800">
                                        @php
                                            // Show stock from mapping or relationship
                                            $stock = $branchProductStocks[$branch->id][$product->id] ?? 0;
                                        @endphp
                                        <div class="flex items-center justify-between">
                                            <span class="flex-1 text-center font-semibold text-gray-900 dark:text-white">{{ $stock }}</span>
                                            <button wire:click="openModal({{ $branch->id }}, {{ $product->id }})" class="px-2 py-1 text-xs bg-blue-500 hover:bg-blue-600 text-white rounded ml-2" title="View Shipment Details">
                                                Details
                                            </button>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No branches or products available.</p>
            @endif
        </div>
    </div>

    <!-- Modal for batch details -->
    @if($showModal)
        <div class="fixed inset-0 flex items-center justify-center z-50" wire:click="closeModal">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-lg w-full mx-4 max-h-96 overflow-y-auto" @click.stop>
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Batch Details</h3>
                @php
                    $batches = collect($branchProductBatches[$selectedBranchId][$selectedProductId] ?? []);
                    $totalBatches = $batches->count();
                    $totalPages = ceil($totalBatches / $perPage);
                    $paginatedBatches = $batches->forPage($currentPage, $perPage);
                @endphp
                <div class="space-y-2">
                    @if($totalBatches > 0)
                        @foreach($paginatedBatches as $batch)
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded text-sm">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $batch['reference'] }}</div>
                                <div class="text-gray-600 dark:text-gray-400">Date: {{ $batch['date'] }}</div>
                                <div class="text-gray-600 dark:text-gray-400">Quantity: {{ $batch['quantity'] }}</div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-600 dark:text-gray-400 text-center">No shipment details available for this product in this branch.</p>
                    @endif
                </div>
                @if($totalPages > 1)
                    <div class="flex justify-between items-center mt-4">
                        <button wire:click="prevPage" @if($currentPage <= 1) disabled @endif class="px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded {{ $currentPage <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-300 dark:hover:bg-gray-500' }}">
                            Previous
                        </button>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Page {{ $currentPage }} of {{ $totalPages }}
                        </span>
                        <button wire:click="nextPage" @if($currentPage >= $totalPages) disabled @endif class="px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded {{ $currentPage >= $totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-300 dark:hover:bg-gray-500' }}">
                            Next
                        </button>
                    </div>
                @endif
                <div class="mt-6 flex justify-end">
                    <button wire:click="closeModal" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>