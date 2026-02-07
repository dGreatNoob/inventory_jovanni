<div>
    <x-slot:header>Branch Management</x-slot:header>
    <x-slot:subheader>Inventory - {{ $branch->name }}</x-slot:subheader>
    <div class="pt-4">
        <!-- Branch Products -->
        <section class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center gap-2">
                            <flux:icon name="building-storefront" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                            {{ $branch->name }} - Branch Products
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ count($branchProducts) }} products from completed shipments
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <flux:button wire:click="openUploadModal" size="sm" class="flex items-center gap-2">
                            <!-- <flux:icon name="document-text" class="w-4 h-4" /> -->
                            Upload Text File
                        </flux:button>
                        <flux:button wire:click="openCustomerSalesModal" variant="outline" size="sm" class="flex items-center gap-2">
                            <!-- <flux:icon name="banknotes" class="w-4 h-4" /> -->
                            Add Customer Sales
                        </flux:button>
                        <a href="{{ route('branch.inventory') }}" wire:navigate>
                            <flux:button variant="outline" size="sm">Back to Branches</flux:button>
                        </a>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                Search Shipments
                            </label>
                            <input type="text" id="search" wire:model.live.debounce.300ms="search"
                                placeholder="Shipment number..."
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="dateFrom" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                Date From
                            </label>
                            <input type="date" id="dateFrom" wire:model.live="dateFrom"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="dateTo" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                Date To
                            </label>
                            <input type="date" id="dateTo" wire:model.live="dateTo"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <div class="flex items-end">
                            <flux:button wire:click="clearFilters" variant="outline" size="sm">Clear Filters</flux:button>
                        </div>
                    </div>
                </div>

                @if(!empty($branchProducts))
                <!-- Summary Header -->
                    <div class="mx-6 mb-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Branch Summary</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ count($branchProducts) }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Products</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $branchProducts->sum('total_quantity') }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Quantity</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $branchProducts->sum('total_sold') }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Sold</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    ₱{{ number_format($branchProducts->sum('total_value'), 2) }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Value</div>
                            </div>
                        </div>
                    </div>

                <!-- Products List -->
                <div class="mx-6 mb-6 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product ID</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product Name</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKU</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supplier Code (SKU)</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barcode</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Quantity</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Sold</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Remaining</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit Price</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Value</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Active Promos</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($branchProducts as $product)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 text-sm">
                                            @if(isset($product['image_url']))
                                                <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}" class="w-12 h-12 object-cover rounded">
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">No image</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm font-mono text-gray-500 dark:text-gray-400">
                                            {{ $product['product_number'] ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $product['name'] }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-mono text-gray-500 dark:text-gray-400">
                                            {{ $product['sku'] }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $product['supplier_code'] ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-mono text-gray-500 dark:text-gray-400">
                                            {{ $product['barcode'] ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white text-center">
                                            {{ $product['total_quantity'] }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white text-center">
                                            {{ $product['total_sold'] }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white text-center">
                                            {{ $product['remaining_quantity'] }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            ₱{{ number_format($product['unit_price'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                            ₱{{ number_format($product['total_value'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            {{ $product['active_promos_count'] }} Active Promos
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <flux:button wire:click="viewProductDetails({{ $product['id'] }})" variant="outline" size="sm">
                                                View Details
                                            </flux:button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="mx-6 mb-6 py-12 text-center rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                    <p class="text-gray-500 dark:text-gray-400">No products from completed shipments yet. Adjust date filters or wait for shipments to complete.</p>
                </div>
                @endif
            </section>
    </div>

    <!-- Product View Modal -->
    @if($showProductViewModal && $selectedProductDetails)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-neutral-900/30 dark:bg-neutral-900/50" wire:click="closeProductViewModal"></div>
            <div class="relative flex w-full max-w-4xl max-h-[90vh] flex-col bg-white shadow-xl dark:bg-zinc-900 rounded-lg">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 dark:bg-indigo-400 rounded-l-lg"></div>
                <!-- Header -->
                <div class="flex-shrink-0 flex justify-between items-center px-6 py-5 ml-1 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Product Details: {{ $selectedProductDetails['name'] }}</h3>
                    <button wire:click="closeProductViewModal" class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-gray-500 dark:hover:bg-zinc-800 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Scrollable Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    <!-- Product Summary -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Barcode</div>
                                <div class="font-mono text-gray-900 dark:text-white">{{ $selectedProductDetails['barcode'] ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">SKU</div>
                                <div class="font-mono text-gray-900 dark:text-white">{{ $selectedProductDetails['sku'] }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Quantity</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $selectedProductDetails['total_quantity'] }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Sold</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $selectedProductDetails['total_sold'] }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- History Tabs -->
                    <div class="mb-6">
                        <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                            <nav class="-mb-px flex space-x-8">
                                <button wire:click="setActiveHistoryTab('upload_history')"
                                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeHistoryTab == 'upload_history' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                                    Upload History
                                </button>
                                <button wire:click="setActiveHistoryTab('quantity_edits')"
                                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeHistoryTab == 'quantity_edits' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                                    Quantity Edits
                                </button>
                                <button wire:click="setActiveHistoryTab('synced_similar')"
                                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeHistoryTab == 'synced_similar' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                                    Synced Similar Barcodes
                                </button>
                            </nav>
                        </div>

                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @php
                                $currentBarcode = $selectedProductDetails['barcode'];
                            @endphp

                            @if($activeHistoryTab == 'upload_history')
                                @php
                                    $histories = \Spatie\Activitylog\Models\Activity::where('log_name', 'branch_inventory')
                                        ->where('properties->barcode', $currentBarcode)
                                        ->where('properties->branch_id', $branch->id)
                                        ->where('properties->uploaded_file', true)
                                        ->orderBy('created_at', 'desc')
                                        ->limit(10)
                                        ->get();

                                    $processedHistories = [];

                                    foreach ($histories as $history) {
                                        $properties = $history->properties;
                                        $quantitySold = $properties['quantity_sold'] ?? 1;
                                        $isDuplicate = $quantitySold > 1;

                                        $processedHistories[] = [
                                            'quantity' => $quantitySold,
                                            'time' => $history->created_at,
                                            'is_duplicate' => $isDuplicate
                                        ];
                                    }
                                @endphp
                                @forelse($processedHistories as $historyItem)
                                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                                        <div class="text-sm text-blue-900 dark:text-blue-100">
                                            @if($historyItem['is_duplicate'])
                                                {{ $historyItem['quantity'] }} barcodes scanned to this product and {{ $historyItem['quantity'] }} sold of this upload
                                            @else
                                                Scanned 1 barcode for this product 1 sold of this upload
                                            @endif
                                        </div>
                                        <div class="text-xs text-blue-600 dark:text-blue-300 mt-1">
                                            {{ $historyItem['time']->format('M d, Y H:i') }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-500 dark:text-gray-400">No upload history found.</div>
                                @endforelse

                            @elseif($activeHistoryTab == 'quantity_edits')
                                @php
                                    $quantityEdits = \Spatie\Activitylog\Models\Activity::where('log_name', 'branch_inventory')
                                        ->where('properties->barcode', $currentBarcode)
                                        ->where('properties->branch_id', $branch->id)
                                        ->where('description', 'like', 'Updated allocated quantity%')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(10)
                                        ->get();

                                    $processedEdits = [];

                                    foreach ($quantityEdits as $edit) {
                                        $properties = $edit->properties;
                                        $oldQty = $properties['old_quantity'] ?? 0;
                                        $newQty = $properties['new_quantity'] ?? 0;
                                        $shipmentNum = $properties['shipment_id'] ? \App\Models\Shipment::find($properties['shipment_id'])->shipping_plan_num ?? 'Unknown' : 'Unknown';

                                        $processedEdits[] = [
                                            'old_quantity' => $oldQty,
                                            'new_quantity' => $newQty,
                                            'shipment' => $shipmentNum,
                                            'time' => $edit->created_at,
                                            'change' => $newQty - $oldQty
                                        ];
                                    }
                                @endphp
                                @forelse($processedEdits as $editItem)
                                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-3">
                                        <div class="text-sm text-orange-900 dark:text-orange-100">
                                            Previous quantity: {{ $editItem['old_quantity'] }}, Quantity added/changed: {{ $editItem['change'] > 0 ? '+' : '' }}{{ $editItem['change'] }} (new total: {{ $editItem['new_quantity'] }}) in shipment {{ $editItem['shipment'] }}
                                        </div>
                                        <div class="text-xs text-orange-600 dark:text-orange-300 mt-1">
                                            {{ $editItem['time']->format('M d, Y H:i') }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-500 dark:text-gray-400">No quantity edit history found.</div>
                                @endforelse

                            @elseif($activeHistoryTab == 'synced_similar')
                                @php
                                    $syncedHistories = \Spatie\Activitylog\Models\Activity::where('log_name', 'branch_inventory')
                                        ->where('properties->barcode', $currentBarcode)
                                        ->where('properties->branch_id', $branch->id)
                                        ->where('properties->synced_similar', true)
                                        ->orderBy('created_at', 'desc')
                                        ->limit(10)
                                        ->get();

                                    $processedSynced = [];

                                    foreach ($syncedHistories as $sync) {
                                        $properties = $sync->properties;
                                        $uploadedBarcode = $properties['uploaded_barcode'] ?? 'Unknown';
                                        $quantitySold = $properties['quantity_sold'] ?? 1;

                                        $processedSynced[] = [
                                            'uploaded_barcode' => $uploadedBarcode,
                                            'quantity' => $quantitySold,
                                            'time' => $sync->created_at
                                        ];
                                    }
                                @endphp
                                @forelse($processedSynced as $syncItem)
                                    <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-3">
                                        <div class="text-sm text-purple-900 dark:text-purple-100">
                                            Synced {{ $syncItem['quantity'] }} quantity from uploaded barcode {{ $syncItem['uploaded_barcode'] }} to this product
                                        </div>
                                        <div class="text-xs text-purple-600 dark:text-purple-300 mt-1">
                                            {{ $syncItem['time']->format('M d, Y H:i') }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-500 dark:text-gray-400">No synced similar barcodes history found.</div>
                                @endforelse
                            @endif
                        </div>
                    </div>

                    <!-- Shipments List -->
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Shipments Containing This Product</h4>
                        <div class="max-h-96 overflow-y-auto">
                            @foreach($selectedProductDetails['shipments'] as $shipment)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4 mb-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-green-100 dark:bg-green-900/20 p-2 rounded-lg">
                                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h5 class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $shipment['shipping_plan_num'] }}
                                                </h5>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    Shipped: {{ $shipment['shipment_date'] }} • {{ $shipment['carrier_name'] }} • {{ $shipment['delivery_method'] }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                                ₱{{ number_format($shipment['total'], 2) }}
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400 flex items-center justify-end space-x-2">
                                                @if($editingShipmentId == $shipment['id'])
                                                    <input type="number" wire:model="editingAllocatedQuantity" min="0"
                                                           class="w-16 px-2 py-1 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                                    <button wire:click="saveAllocatedQuantity"
                                                            class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded focus:outline-none focus:ring-1 focus:ring-green-500">
                                                        ✓
                                                    </button>
                                                    <button wire:click="cancelEditingQuantity"
                                                            class="px-2 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs rounded focus:outline-none focus:ring-1 focus:ring-gray-500">
                                                        ✕
                                                    </button>
                                                @else
                                                    {{ $shipment['allocated_quantity'] }} allocated • {{ $shipment['sold_quantity'] }} sold
                                                    <button wire:click="startEditingQuantity({{ $shipment['id'] }}, {{ $shipment['allocated_quantity'] }})"
                                                            class="ml-2 px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                            title="Edit allocated quantity">
                                                        ✏️
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        Allocation: {{ $shipment['allocation_reference'] }} • Barcode: {{ $shipment['barcode'] }}
                                    </div>
                                    @php
                                        $productId = $selectedProductDetails['id'];
                                        $batchAllocationId = $shipment['batch_allocation_id'];
                                        $promo = \App\Models\Promo::where('product', 'like', '%' . (string)$productId . '%')
                                            ->where('branch', 'like', '%' . (string)$batchAllocationId . '%')
                                            ->where('startDate', '<=', now())
                                            ->where('endDate', '>=', now())
                                            ->first();
                                        if ($promo) {
                                            $discount = 0;
                                            if($promo->type == 'Buy one Take one') {
                                                $discount = 0.5;
                                            } elseif($promo->type == '70% Discount') {
                                                $discount = 0.7;
                                            } elseif($promo->type == '60% Discount') {
                                                $discount = 0.6;
                                            } elseif($promo->type == '50% Discount') {
                                                $discount = 0.5;
                                            }
                                            $discounted_price = $shipment['price'] * (1 - $discount);
                                            $total_discounted_value = $discounted_price * $shipment['allocated_quantity'];
                                        }
                                    @endphp
                                    @if($promo)
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                            <strong>Discounted Price:</strong> ₱{{ number_format($discounted_price, 2) }} |
                                            <strong>Total Discounted Value:</strong> ₱{{ number_format($total_discounted_value, 2) }} |
                                            <strong>Promo Type:</strong> {{ $promo->name }} ({{ $promo->type }})
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex-shrink-0 flex justify-end p-6 border-t border-gray-200 dark:border-gray-700">
                    <button wire:click="closeProductViewModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Upload Text File Modal -->
    @if($showUploadModal)
        <div class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upload Text File</h3>
                    <button wire:click="closeUploadModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Upload a text file containing scanned barcodes (one barcode per line) for inventory audit. The system will compare these barcodes with allocated products in the current branch to detect variances (missing items, extra items, quantity mismatches).
                    </p>
                </div>

                <div class="mb-4">
                    <label for="textFile" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Choose Text File (.txt)
                    </label>
                    <input type="file" id="textFile" wire:model="textFile" accept=".txt"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    @error('textFile')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="closeUploadModal" type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Cancel
                    </button>
                    <button wire:click="processTextFile" type="button"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Process File
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Inventory Audit Results Modal -->
    @if($showResultsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 dark:bg-black/60" wire:click="closeResultsModal"></div>
            <div class="relative w-full max-w-5xl max-h-[90vh] bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden flex flex-col">
                <div class="p-6 flex-shrink-0 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Inventory Audit Results</h3>
                        <button wire:click="closeResultsModal" class="rounded-full p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-6">
                    <div class="mb-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $auditResults['total_scanned'] ?? $uploadedBarcodeCount }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Scanned</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $auditResults['total_allocated'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Allocated</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ count($missingItems) }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Missing Items</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ count($quantityVariances) + count($extraItems) }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Variances</div>
                            </div>
                        </div>
                    </div>

                    @if(!empty($missingItems))
                        <div class="mb-6">
                            <h4 class="font-medium text-red-600 dark:text-red-400 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Missing Items ({{ count($missingItems) }}): Allocated but not scanned
                            </h4>
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-red-200 dark:divide-red-700">
                                        <thead class="bg-red-100 dark:bg-red-900/40">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Product ID</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Product Name</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">SKU</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Supplier Code (SKU)</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Barcode</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Allocated Qty</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Scanned Qty</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Variance</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-red-50 dark:bg-red-900/20 divide-y divide-red-200 dark:divide-red-700">
                                            @foreach($missingItems as $item)
                                                <tr class="hover:bg-red-100 dark:hover:bg-red-900/30">
                                                    <td class="px-4 py-3 text-sm font-mono text-red-900 dark:text-red-100">{{ $item['product_number'] ?? '—' }}</td>
                                                    <td class="px-4 py-3 text-sm text-red-900 dark:text-red-100">{{ $item['product_name'] }}</td>
                                                    <td class="px-4 py-3 text-sm font-mono text-red-700 dark:text-red-300">{{ $item['sku'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-red-900 dark:text-red-100">{{ $item['supplier_code'] ?? '—' }}</td>
                                                    <td class="px-4 py-3 text-sm font-mono text-red-900 dark:text-red-100">{{ $item['barcode'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-red-900 dark:text-red-100 text-center">{{ $item['allocated_quantity'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-red-900 dark:text-red-100 text-center">0</td>
                                                    <td class="px-4 py-3 text-sm font-semibold text-red-600 dark:text-red-400 text-center">{{ $item['variance'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!empty($extraItems))
                        <div class="mb-6">
                            <h4 class="font-medium text-orange-600 dark:text-orange-400 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Extra Items ({{ count($extraItems) }}): Scanned but not allocated to this branch
                            </h4>
                            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-orange-200 dark:divide-orange-700">
                                        <thead class="bg-orange-100 dark:bg-orange-900/40">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-orange-700 dark:text-orange-300 uppercase">Barcode</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-orange-700 dark:text-orange-300 uppercase">Scanned Qty</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-orange-700 dark:text-orange-300 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-orange-50 dark:bg-orange-900/20 divide-y divide-orange-200 dark:divide-orange-700">
                                            @foreach($extraItems as $item)
                                                <tr class="hover:bg-orange-100 dark:hover:bg-orange-900/30">
                                                    <td class="px-4 py-3 text-sm font-mono text-orange-900 dark:text-orange-100">{{ $item['barcode'] }}</td>
                                                    <td class="px-4 py-3 text-sm font-semibold text-orange-600 dark:text-orange-400 text-center">{{ $item['scanned_quantity'] }}</td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300">
                                                            Not Allocated
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!empty($quantityVariances))
                        <div class="mb-6">
                            <h4 class="font-medium text-yellow-600 dark:text-yellow-400 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Quantity Variances ({{ count($quantityVariances) }}): Scanned count doesn't match allocated
                            </h4>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-yellow-200 dark:divide-yellow-700">
                                        <thead class="bg-yellow-100 dark:bg-yellow-900/40">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Product ID</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Product Name</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">SKU</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Supplier Code (SKU)</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Barcode</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Allocated Qty</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Scanned Qty</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Variance</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-yellow-50 dark:bg-yellow-900/20 divide-y divide-yellow-200 dark:divide-yellow-700">
                                            @foreach($quantityVariances as $item)
                                                <tr class="hover:bg-yellow-100 dark:hover:bg-yellow-900/30">
                                                    <td class="px-4 py-3 text-sm font-mono text-yellow-900 dark:text-yellow-100">{{ $item['product_number'] ?? '—' }}</td>
                                                    <td class="px-4 py-3 text-sm text-yellow-900 dark:text-yellow-100">{{ $item['product_name'] }}</td>
                                                    <td class="px-4 py-3 text-sm font-mono text-yellow-700 dark:text-yellow-300">{{ $item['sku'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-yellow-900 dark:text-yellow-100">{{ $item['supplier_code'] ?? '—' }}</td>
                                                    <td class="px-4 py-3 text-sm font-mono text-yellow-900 dark:text-yellow-100">{{ $item['barcode'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-yellow-900 dark:text-yellow-100 text-center">{{ $item['allocated_quantity'] }}</td>
                                                    <td class="px-4 py-3 text-sm text-yellow-900 dark:text-yellow-100 text-center">{{ $item['scanned_quantity'] }}</td>
                                                    <td class="px-4 py-3 text-sm font-semibold {{ $item['variance'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} text-center">
                                                        {{ $item['variance'] > 0 ? '+' : '' }}{{ $item['variance'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(empty($missingItems) && empty($extraItems) && empty($quantityVariances))
                        <div class="mb-6">
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 text-center">
                                <svg class="mx-auto h-12 w-12 text-green-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h4 class="text-lg font-medium text-green-800 dark:text-green-200 mb-2">No Variances Found</h4>
                                <p class="text-sm text-green-600 dark:text-green-300">All scanned barcodes match the allocated products. Inventory is accurate.</p>
                            </div>
                        </div>
                    @endif

                    @if($errors->has('audit') || !empty($existingAuditIdForDay))
                        <div class="mb-4">
                            <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="h-5 w-5 text-amber-700 dark:text-amber-300 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-amber-900 dark:text-amber-100">
                                            Audit already saved today
                                        </div>
                                        <div class="mt-1 text-sm text-amber-800 dark:text-amber-200">
                                            {{ $errors->first('audit') }}
                                        </div>
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <button wire:click="viewTodaysAudit"
                                                class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                                View Today’s Audit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex-shrink-0 flex justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <button wire:click="closeResultsModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Close
                    </button>
                    <button wire:click="saveAuditResults"
                            @if(!empty($existingAuditIdForDay)) disabled @endif
                            class="px-4 py-2 text-sm font-medium text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                {{ !empty($existingAuditIdForDay) ? 'bg-blue-300 dark:bg-blue-900/40 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700' }}">
                        Save Audit Results
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Modal -->
    @if($showSuccessModal)
        <div class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Success</h3>
                    <button wire:click="closeSuccessModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $successMessage }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button wire:click="closeSuccessModal"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        OK
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Customer Sales Modal -->
    <div
        x-data="{ open: @entangle('showCustomerSalesModal').live }"
        x-cloak
        x-on:keydown.escape.window="if (open) { open = false; $wire.closeCustomerSalesModal(); }"
    >
        <template x-teleport="body">
            <div
                x-show="open"
                x-transition.opacity
                class="fixed inset-0 z-50 flex"
            >
                <div
                    x-show="open"
                    x-transition.opacity
                    class="fixed inset-0 bg-neutral-900/30 dark:bg-neutral-900/50"
                    @click="open = false; $wire.closeCustomerSalesModal()"
                ></div>

                <section
                    x-show="open"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="relative ml-auto flex h-full w-full max-w-2xl bg-white shadow-xl dark:bg-zinc-900"
                >
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500 dark:bg-green-400"></div>

                    <div class="ml-[0.25rem] flex h-full w-full flex-col bg-white shadow-xl dark:bg-zinc-900">
                        <header class="flex items-start justify-between border-b border-gray-200 px-6 py-5 dark:border-zinc-700">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        Add Customer Sales
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        Record customer sales for {{ $branch->name }}
                                    </p>
                                </div>
                            </div>

                            <button
                                type="button"
                                class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 dark:text-gray-500 dark:hover:bg-zinc-800 dark:hover:text-gray-200"
                                @click="open = false; $wire.closeCustomerSalesModal()"
                                aria-label="Close sales modal"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </header>

                        <div class="flex-1 overflow-hidden">
                            <div class="flex h-full flex-col">
                                <div class="flex-1 overflow-y-auto px-6 py-6">
                                    <div class="space-y-6">
                                        <!-- Agent and Selling Area Selection -->
                                        <section class="space-y-4">
                                            <div>
                                                <flux:heading size="md" class="text-gray-900 dark:text-white">Sales Information</flux:heading>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Select agent and selling area before scanning products.</p>
                                            </div>

                                            <div class="space-y-4">
                                                <!-- Agent Selection (Required) -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                                        Agent <span class="text-red-500">*</span>
                                                    </label>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Select the agent responsible for this sale</p>
                                                    <div class="relative" wire:click.outside="$set('agentDropdown', false)">
                                                        <div wire:click="toggleAgentDropdown"
                                                            class="block w-full rounded-md border {{ $selectedAgentId ? 'border-gray-300' : 'border-red-300' }} bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm cursor-pointer flex justify-between items-center min-h-[42px]">
                                                            <span class="{{ $selectedAgentId ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                                                                @if($selectedAgentId && $availableAgents)
                                                                    @php $selectedAgent = collect($availableAgents)->firstWhere('id', $selectedAgentId); @endphp
                                                                    @if($selectedAgent)
                                                                        {{ $selectedAgent->agent_code }} - {{ $selectedAgent->name }}
                                                                    @else
                                                                        Select an agent...
                                                                    @endif
                                                                @else
                                                                    Select an agent...
                                                                @endif
                                                            </span>
                                                            <svg class="w-4 h-4 ml-2 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </div>
                                                        @if($agentDropdown)
                                                            <div class="absolute z-20 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg dark:bg-gray-700 dark:border-gray-600">
                                                                <div class="p-2 border-b border-gray-200 dark:border-gray-600 sticky top-0 bg-white dark:bg-gray-700">
                                                                    <input type="text"
                                                                        wire:model.live.debounce.200ms="agentSearch"
                                                                        placeholder="Search by name or agent code..."
                                                                        onclick="event.stopPropagation()"
                                                                        class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-600 dark:text-white dark:placeholder-gray-400" />
                                                                </div>
                                                                <div class="max-h-48 overflow-auto">
                                                                    <button type="button"
                                                                            wire:click="selectAgent()"
                                                                            class="w-full flex items-center px-3 py-2 text-left text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600">
                                                                        <span class="text-gray-400">None</span>
                                                                    </button>
                                                                    @foreach($this->filteredAvailableAgents as $agent)
                                                                        <button type="button"
                                                                                wire:click="selectAgent({{ $agent->id }})"
                                                                                class="w-full flex items-center justify-between px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600 last:border-b-0 {{ $selectedAgentId == $agent->id ? 'bg-green-50 dark:bg-green-900/20 text-green-900 dark:text-green-100' : 'text-gray-900 dark:text-white' }}">
                                                                            <span>{{ $agent->agent_code }} - {{ $agent->name }}</span>
                                                                            @if($selectedAgentId == $agent->id)
                                                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                                </svg>
                                                                            @endif
                                                                        </button>
                                                                    @endforeach
                                                                    @if($this->filteredAvailableAgents->isEmpty())
                                                                        <div class="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                                            @if($agentSearch)
                                                                                No agents match "{{ $agentSearch }}"
                                                                            @else
                                                                                No agents available
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @if(!$selectedAgentId)
                                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">Please select an agent before scanning products</p>
                                                    @endif
                                                </div>

                                                <!-- Selling Area Selection (Optional) -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                                        Selling Area <span class="text-gray-400 text-xs">(Optional)</span>
                                                    </label>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Where in the branch was this item sold?</p>
                                                    <div class="relative" wire:click.outside="$set('sellingAreaDropdown', false)">
                                                        <div wire:click="toggleSellingAreaDropdown"
                                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm flex justify-between items-center min-h-[42px] {{ !empty($sellingAreaOptions) ? 'cursor-pointer' : 'cursor-not-allowed opacity-75' }}">
                                                            <span class="{{ $selectedSellingArea ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                                                                @if($selectedSellingArea)
                                                                    {{ $selectedSellingArea }}
                                                                @else
                                                                    {{ empty($sellingAreaOptions) ? 'No selling areas configured' : 'Select selling area...' }}
                                                                @endif
                                                            </span>
                                                            <svg class="w-4 h-4 ml-2 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </div>
                                                        @if($sellingAreaDropdown && !empty($sellingAreaOptions))
                                                            <div class="absolute z-20 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg dark:bg-gray-700 dark:border-gray-600">
                                                                <div class="p-2 border-b border-gray-200 dark:border-gray-600 sticky top-0 bg-white dark:bg-gray-700">
                                                                    <input type="text"
                                                                        wire:model.live.debounce.200ms="sellingAreaSearch"
                                                                        placeholder="Search selling areas..."
                                                                        onclick="event.stopPropagation()"
                                                                        class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-600 dark:text-white dark:placeholder-gray-400" />
                                                                </div>
                                                                <div class="max-h-48 overflow-auto">
                                                                    <button type="button"
                                                                            wire:click="selectSellingArea()"
                                                                            class="w-full flex items-center px-3 py-2 text-left text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600">
                                                                        <span class="text-gray-400">None</span>
                                                                    </button>
                                                                    @foreach($this->filteredSellingAreaOptions as $option)
                                                                        <button type="button"
                                                                                wire:click="selectSellingArea({{ json_encode($option) }})"
                                                                                class="w-full flex items-center justify-between px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600 last:border-b-0 {{ $selectedSellingArea == $option ? 'bg-green-50 dark:bg-green-900/20 text-green-900 dark:text-green-100' : 'text-gray-900 dark:text-white' }}">
                                                                            <span>{{ $option }}</span>
                                                                            @if($selectedSellingArea == $option)
                                                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                                </svg>
                                                                            @endif
                                                                        </button>
                                                                    @endforeach
                                                                    @if($this->filteredSellingAreaOptions->isEmpty())
                                                                        <div class="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                                            @if($sellingAreaSearch)
                                                                                No selling areas match "{{ $sellingAreaSearch }}"
                                                                            @else
                                                                                No selling areas available
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                        <!-- Product Scanning Section -->
                                        @if($selectedAgentId)
                                        <section class="space-y-4">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <flux:heading size="md" class="text-gray-900 dark:text-white">Scan Products</flux:heading>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Scan barcode to automatically add products to the sale.</p>
                                                </div>
                                                @if(!empty($salesItems))
                                                    <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                        </svg>
                                                        <span class="text-sm font-semibold text-blue-900 dark:text-blue-100">{{ count($salesItems) }} scanned</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="space-y-4" x-data x-init="$wire.on('refocus-sales-barcode', () => $nextTick(() => document.getElementById('sales-barcode-input')?.focus()))">
                                                <div>
                                                    <label for="sales-barcode-input" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                                        Barcode
                                                    </label>
                                                    <input
                                                        id="sales-barcode-input"
                                                        type="text"
                                                        wire:model.live.debounce.400ms="salesBarcodeInput"
                                                        wire:keydown.enter.prevent="processSalesBarcode"
                                                        wire:keydown.escape="clearSalesBarcodeInput"
                                                        placeholder="Scan barcode - auto-adds to sale..."
                                                        autocomplete="off"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                        x-ref="salesBarcodeInput"
                                                        x-init="$nextTick(() => { if ($el) $el.focus(); })"
                                                        autofocus
                                                    />
                                                </div>

                                                <!-- Last Added Item Feedback -->
                                                @if($lastAddedItem)
                                                    <div class="rounded-lg border border-green-200 bg-green-50 p-3 dark:border-green-800 dark:bg-green-900/20 animate-pulse">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            <div class="flex-1 min-w-0">
                                                                <div class="text-sm font-medium text-green-900 dark:text-green-100">Added to sale!</div>
                                                                <div class="text-xs text-green-700 dark:text-green-300 truncate">
                                                                    {{ $lastAddedItem['name'] }} - {{ $lastAddedItem['barcode'] }}
                                                                </div>
                                                            </div>
                                                            <div class="text-sm font-semibold text-green-900 dark:text-green-100">
                                                                ₱{{ number_format($lastAddedItem['unit_price'], 2) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Scan barcode to automatically add 1 unit to the sales list below. Each scan is instantly recorded.
                                                </p>
                                            </div>
                                        </section>
                                        @else
                                        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20">
                                            <div class="flex items-start gap-3">
                                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                </svg>
                                                <div>
                                                    <h4 class="text-sm font-medium text-yellow-900 dark:text-yellow-100">Agent Selection Required</h4>
                                                    <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">Please select an agent above before you can start scanning products.</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Sales Items List -->
                                        @if (!empty($salesItems))
                                        <section class="space-y-4">
                                            <div>
                                                <flux:heading size="md" class="text-gray-900 dark:text-white">Sales Items</flux:heading>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Items added to this sales transaction.</p>
                                            </div>

                                            <!-- Sales Summary Info -->
                                            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <div class="text-xs text-blue-600 dark:text-blue-400 font-medium mb-1">Agent</div>
                                                        <div class="font-medium text-blue-900 dark:text-blue-100">
                                                            @if($selectedAgentId && $availableAgents)
                                                                @php $selectedAgent = collect($availableAgents)->firstWhere('id', $selectedAgentId); @endphp
                                                                @if($selectedAgent)
                                                                    {{ $selectedAgent->agent_code }} - {{ $selectedAgent->name }}
                                                                @else
                                                                    —
                                                                @endif
                                                            @else
                                                                —
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="text-xs text-blue-600 dark:text-blue-400 font-medium mb-1">Selling Area</div>
                                                        <div class="font-medium text-blue-900 dark:text-blue-100">
                                                            {{ $selectedSellingArea ?: 'Not specified' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                                        <tr>
                                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit Price</th>
                                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                        @foreach($salesItems as $index => $item)
                                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                                <td class="px-4 py-3 text-sm">
                                                                    <div class="font-medium text-gray-900 dark:text-white">{{ $item['name'] }}</div>
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                                        <span class="font-mono">SKU: {{ $item['sku'] ?? '—' }}</span>
                                                                        @if(!empty($item['supplier_code']))
                                                                            <span class="mx-1">·</span>
                                                                            <span>Supplier Code (SKU): {{ $item['supplier_code'] }}</span>
                                                                        @endif
                                                                    </div>
                                                                    @if(!empty($item['product_number']))
                                                                        <div class="text-xs text-gray-400 dark:text-gray-500 font-mono mt-0.5">ID: {{ $item['product_number'] }}</div>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                                    {{ $item['quantity'] }}
                                                                </td>
                                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                                    ₱{{ number_format($item['unit_price'], 2) }}
                                                                </td>
                                                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                                                                    ₱{{ number_format($item['total'], 2) }}
                                                                </td>
                                                                <td class="px-4 py-3 text-sm">
                                                                    <button wire:click="removeSalesItem({{ $index }})"
                                                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                        </svg>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Sales Summary -->
                                            <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium text-green-900 dark:text-green-100">Total Sales Amount:</span>
                                                    <span class="text-xl font-bold text-green-900 dark:text-green-100">₱{{ number_format(collect($salesItems)->sum('total'), 2) }}</span>
                                                </div>
                                            </div>
                                        </section>
                                        @endif
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 bg-white px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900">
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            @if(empty($salesItems))
                                                Add products to create a sales transaction
                                            @else
                                                {{ count($salesItems) }} items • Total: ₱{{ number_format(collect($salesItems)->sum('total'), 2) }}
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <button type="button" wire:click="clearSalesItems"
                                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500"
                                                    @if(empty($salesItems)) disabled @endif>
                                                Clear All
                                            </button>
                                            <button type="button" wire:click="saveCustomerSales"
                                                    class="px-4 py-2 text-sm font-medium text-white border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 {{ !$selectedAgentId || empty($salesItems) ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700' }}"
                                                    @if(!$selectedAgentId || empty($salesItems)) disabled @endif>
                                                Save Sales
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </template>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const salesBarcodeInput = document.getElementById('sales-barcode-input');
            let lastScrollPosition = 0;

            if (salesBarcodeInput) {
                // Auto-focus on load
                salesBarcodeInput.focus();

                // Save scroll position before Livewire update
                window.addEventListener('livewire:update', function() {
                    lastScrollPosition = window.scrollY || window.pageYOffset;
                });

                // Restore scroll position after Livewire update
                window.addEventListener('livewire:updated', function() {
                    window.scrollTo(0, lastScrollPosition);
                    // Refocus the input after update
                    setTimeout(() => {
                        const input = document.getElementById('sales-barcode-input');
                        if (input) {
                            input.focus();
                        }
                    }, 50);
                });

                // Prevent scroll on focus
                salesBarcodeInput.addEventListener('focus', function(e) {
                    e.preventDefault();
                });

                // Refocus when clicking anywhere on the page (except buttons)
                document.addEventListener('click', function(e) {
                    if (e.target.tagName !== 'BUTTON' && !e.target.closest('button') && !e.target.closest('input')) {
                        setTimeout(() => {
                            const input = document.getElementById('sales-barcode-input');
                            if (input) input.focus();
                        }, 50);
                    }
                });
            }
        });

        // Additional Livewire hook to prevent scroll
        document.addEventListener('livewire:initialized', () => {
            let scrollPosition = 0;

            Livewire.hook('morph.updating', ({
                component,
                cleanup
            }) => {
                scrollPosition = window.scrollY || window.pageYOffset;
            });

            Livewire.hook('morph.updated', ({
                component
            }) => {
                window.scrollTo(0, scrollPosition);

                // Refocus sales barcode input
                const salesBarcodeInput = document.getElementById('sales-barcode-input');
                if (salesBarcodeInput) {
                    setTimeout(() => salesBarcodeInput.focus(), 100);
                }
            });
        });
    </script>
    @endpush
    </div>
</div>