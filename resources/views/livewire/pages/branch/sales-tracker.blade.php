<div>
    <x-slot:header>Branch Management</x-slot:header>
    <x-slot:subheader>Sales Tracker</x-slot:subheader>
    <div class="pt-4">
        <div class="space-y-6">
            <!-- Header Section with Add Sales Button -->
            <div class="mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sales Tracker</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Track and record sales performance across branches and products</p>
                    </div>
                    <div class="flex flex-row items-center space-x-3">
                        @can('product view')
                        <flux:modal.trigger name="add-sales">
                            <flux:button
                                wire:click="openAddSalesModal"
                                variant="primary"
                                class="flex items-center gap-2 whitespace-nowrap min-w-fit"
                            >
                                <svg class="inline w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Add Sales</span>
                            </flux:button>
                        </flux:modal.trigger>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">

                <h5 class="font-medium mb-3">Branch Sales Tracker</h5>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Track sales performance across branches and products. This view shows total quantity sold and revenue generated.
                </p>

                <!-- Date Range Filters -->
                <div class="mb-4 flex flex-wrap gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                        <input
                            type="date"
                            wire:model.live="startDate"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                        <input
                            type="date"
                            wire:model.live="endDate"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                    </div>
                </div>

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
                                                $sale = $branchProductSales[$branch->id][$product->id] ?? ['quantity' => 0, 'revenue' => 0];
                                            @endphp
                                            <span class="inline-block px-3 py-2 rounded font-semibold text-green-700 dark:text-green-300">
                                                {{ $sale['quantity'] }}<br>
                                                <small>₱{{ number_format($sale['revenue'], 2) }}</small>
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

    <!-- Modals -->
    <!-- Add Sales Modal -->
    <flux:modal name="add-sales" class="md:w-4/5">
        <div class="space-y-6">
            <!-- Modal Header -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    Add Sales Entry
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Select a completed shipment and scan products to record sales.
                </p>
            </div>

            <!-- Step 1: Select Shipment -->
            @if(!$selectedShipmentId)
            <div class="space-y-4">
                <div>
                    <label for="shipment-select" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Select Completed Shipment
                    </label>
                    <select wire:model.live="selectedShipmentId"
                            id="shipment-select"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400">
                        <option value="">Choose a completed shipment...</option>
                        @foreach($completedShipments as $shipment)
                            <option value="{{ $shipment->id }}">
                                {{ $shipment->shipping_plan_num }} - {{ $shipment->customer_name ?? 'N/A' }}
                                ({{ $shipment->batchAllocation->ref_no ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('selectedShipmentId')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                @if($selectedShipmentId)
                    <!-- Shipment Details -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Shipment Details</h4>
                        @php
                            $selectedShipment = $completedShipments->find($selectedShipmentId);
                        @endphp
                        @if($selectedShipment)
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-blue-700 dark:text-blue-300">Reference:</span>
                                    <div class="font-medium text-blue-900 dark:text-blue-100">{{ $selectedShipment->shipping_plan_num }}</div>
                                </div>
                                <div>
                                    <span class="text-blue-700 dark:text-blue-300">Customer:</span>
                                    <div class="font-medium text-blue-900 dark:text-blue-100">{{ $selectedShipment->customer_name ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <span class="text-blue-700 dark:text-blue-300">Batch:</span>
                                    <div class="font-medium text-blue-900 dark:text-blue-100">{{ $selectedShipment->batchAllocation->ref_no ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <span class="text-blue-700 dark:text-blue-300">Status:</span>
                                    <div class="font-medium text-blue-900 dark:text-blue-100">{{ ucfirst($selectedShipment->shipping_status) }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            @else
            <!-- Step 2: Product Scanning -->
            <div class="space-y-4">
                <!-- Selected Shipment Info -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">Scanning Products</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Shipment: {{ $completedShipments->find($selectedShipmentId)->shipping_plan_num ?? 'N/A' }}
                            </p>
                        </div>
                        <button wire:click="changeShipment" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                            Change Shipment
                        </button>
                    </div>
                </div>

                <!-- Barcode Scanner Input -->
                <div>
                    <label for="barcode-input" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Barcode Scanner Input
                    </label>
                    <input type="text"
                           wire:model.live="barcodeInput"
                           wire:keydown.enter="processBarcode"
                           id="barcode-input"
                           autofocus
                           placeholder="Scan barcode or enter manually..."
                           class="w-full px-4 py-3 text-lg font-mono border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Scan product barcodes or enter them manually to record sales.
                    </p>
                </div>

                <!-- Scan Feedback -->
                @if($scanFeedback)
                    <div class="p-4 rounded-lg border-2
                        @if(str_contains($scanFeedback, '✅')) bg-green-50 border-green-500 text-green-800 dark:bg-green-900/20 dark:text-green-300
                        @elseif(str_contains($scanFeedback, '❌')) bg-red-50 border-red-500 text-red-800 dark:bg-red-900/20 dark:text-red-300
                        @elseif(str_contains($scanFeedback, '⚠️')) bg-yellow-50 border-yellow-500 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300
                        @else bg-blue-50 border-blue-500 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300 @endif">
                        <div class="flex items-center">
                            <div class="text-2xl mr-3">
                                @if(str_contains($scanFeedback, '✅')) ✅
                                @elseif(str_contains($scanFeedback, '❌')) ❌
                                @elseif(str_contains($scanFeedback, '⚠️')) ⚠️
                                @else ℹ️ @endif
                            </div>
                            <div class="text-lg font-semibold">{{ $scanFeedback }}</div>
                        </div>
                    </div>
                @endif

                <!-- Products from Selected Shipment -->
                @if($selectedShipmentId && $shipmentProducts)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h5 class="font-medium text-gray-900 dark:text-white">Products in Shipment</h5>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Barcode</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Allocated Qty</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Scanned Qty</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($shipmentProducts as $product)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $product['name'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono">
                                                {{ $product['barcode'] ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $product['allocated_qty'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 dark:text-blue-400 font-bold">
                                                {{ $product['scanned_qty'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($product['scanned_qty'] >= $product['allocated_qty'])
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                        ✓ Complete
                                                    </span>
                                                @elseif($product['scanned_qty'] > 0)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                                        {{ $product['allocated_qty'] - $product['scanned_qty'] }} remaining
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                                        Not Scanned
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            <!-- Debug Information (Remove in production) -->
            @if($selectedShipmentId)
            <div class="mt-4 p-3 bg-gray-100 dark:bg-gray-800 rounded text-xs">
                <strong>Debug Info:</strong><br>
                Selected Shipment: {{ $selectedShipmentId }}<br>
                All Products Scanned: {{ $this->allProductsScanned ? 'Yes' : 'No' }}<br>
                Scanned Quantities: {{ json_encode($scannedQuantities) }}<br>
                Shipment Products Count: {{ count($shipmentProducts) }}<br>
                Button Enabled: {{ $selectedShipmentId && $this->allProductsScanned ? 'Yes' : 'No' }}
            </div>
            @endif

        </div>
    </flux:modal>
</div>