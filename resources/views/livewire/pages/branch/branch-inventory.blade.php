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
                                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center bg-white dark:bg-gray-800">
                                        @php
                                            // Show stock from mapping or relationship
                                            $stock = $branchProductStocks[$branch->id][$product->id] ?? 0;
                                        @endphp
                                        <span class="inline-block px-3 py-2 rounded font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                            {{ $stock }}
                                        </span>
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
</div>