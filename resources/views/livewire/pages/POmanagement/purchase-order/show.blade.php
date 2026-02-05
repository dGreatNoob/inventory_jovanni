<x-slot:header>Purchase Order Details</x-slot:header>
<x-slot:subheader>Purchase Order #{{ $purchaseOrder->po_num }} - {{ $purchaseOrder->status->label() }}</x-slot:subheader>

<div>
    <!-- Error Message -->
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            {{ session('error') }}
        </div>
    @endif
    
    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="p-6">
        <!-- PENDING Status Section -->
        @if($purchaseOrder->status === \App\Enums\PurchaseOrderStatus::PENDING)
        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-lg border-2 border-yellow-300 dark:border-yellow-700 shadow-sm mb-6">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Pending Approval</h2>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Purchase Order #{{ $purchaseOrder->po_num }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-800">
                        Pending Approval
                    </span>
                </div>

                <!-- QR Code and Summary Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- QR Code -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4 text-center">QR Code</h3>
                        <div class="flex justify-center mb-4">
                            <div class="bg-white p-4 rounded-xl shadow-lg border-2 border-yellow-200 dark:border-yellow-700">
                                {!! QrCode::size(200)->generate($purchaseOrder->po_num) !!}
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="button" onclick="printQRCode()"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-4 focus:ring-yellow-300 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Print QR Code
                            </button>
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Purchase Order Details</h3>
                        <div class="grid gap-4">
                            <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Supplier:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Department:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->department->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Order Date:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->order_date->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Ordered By:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->orderedByUser->name ?? 'N/A' }}</span>
                            </div>
                            @if($purchaseOrder->expected_delivery_date)
                            <div class="flex justify-between items-center p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Expected Delivery:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->expected_delivery_date->format('M d, Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    
                </div>

                <!-- Items Section -->
                <div class="mb-6">
                    <h4 class="text-md font-medium text-zinc-900 dark:text-white mb-3">📋 Purchase Order Items</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
                            <thead class="text-sm text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-700 dark:text-zinc-400">
                                <tr>
                                    <th scope="col" class="px-5 py-3 text-left">Product Name</th>
                                    <th scope="col" class="px-5 py-3 text-left">SKU</th>
                                    <th scope="col" class="px-5 py-3 text-left">Supplier Code (SKU)</th>
                                    <th scope="col" class="px-5 py-3 text-left">Category</th>
                                    <th scope="col" class="px-5 py-3 text-right">Unit Price</th>
                                    <th scope="col" class="px-5 py-3 text-right">Quantity</th>
                                    <th scope="col" class="px-5 py-3 text-right">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrder->productOrders as $order)
                                    <tr class="bg-white border-b dark:bg-zinc-800 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-600">
                                        <td class="px-5 py-4">
                                            <div class="font-medium text-zinc-900 dark:text-white">{{ $order->product->remarks ?? $order->product->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm font-mono text-zinc-600 dark:text-zinc-400">{{ $order->product->sku ?? '—' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $order->product->supplier_code ?? '—' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $order->product->category->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-right">₱{{ number_format($order->unit_price, 2) }}</td>
                                        <td class="px-5 py-4 text-right">
                                            {{ number_format($order->quantity, 0) }} {{ $order->product->uom ?? 'pcs' }}
                                        </td>
                                        <td class="px-5 py-4 text-right font-semibold text-zinc-900 dark:text-white">
                                            ₱{{ number_format($order->total_price, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-zinc-800 dark:border-zinc-700">
                                        <td colspan="7" class="px-5 py-4 text-center">No items found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary -->
                <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-700">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($this->totalQuantity, 0) }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Total Quantity</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $purchaseOrder->productOrders->count() }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Items Count</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">₱{{ number_format($this->totalPrice, 2) }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Total Value</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- APPROVED Status Section -->
        @if($purchaseOrder->status === \App\Enums\PurchaseOrderStatus::APPROVED)
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border-2 border-blue-300 dark:border-blue-700 shadow-sm mb-6">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Approved Purchase Order</h2>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Purchase Order #{{ $purchaseOrder->po_num }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                        Approved
                    </span>
                </div>

                <!-- QR Code and Summary Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- QR Code -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4 text-center">QR Code</h3>
                        <div class="flex justify-center mb-4">
                            <div class="bg-white p-4 rounded-xl shadow-lg border-2 border-blue-200 dark:border-blue-700">
                                {!! QrCode::size(200)->generate($purchaseOrder->po_num) !!}
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="button" onclick="printQRCode()"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Print QR Code
                            </button>
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Purchase Order Details</h3>
                        <div class="grid gap-4">
                            <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Supplier:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Department:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->department->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Order Date:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->order_date->format('M d, Y') }}</span>
                            </div>
                            @if($purchaseOrder->expected_delivery_date)
                            <div class="flex justify-between items-center p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Expected Delivery:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->expected_delivery_date->format('M d, Y') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Approved By:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->approverInfo->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Section (read-only in Approved status) -->
                <div class="mb-6">
                    <h4 class="text-md font-medium text-zinc-900 dark:text-white mb-3">📋 Purchase Order Items</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
                            <thead class="text-sm text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-700 dark:text-zinc-400">
                                <tr>
                                    <th scope="col" class="px-5 py-3 text-left">Product Name</th>
                                    <th scope="col" class="px-5 py-3 text-left">SKU</th>
                                    <th scope="col" class="px-5 py-3 text-left">Supplier Code (SKU)</th>
                                    <th scope="col" class="px-5 py-3 text-left">Category</th>
                                    <th scope="col" class="px-5 py-3 text-left">Ordered Qty</th>
                                    <th scope="col" class="px-5 py-3 text-right">Unit Price</th>
                                    <th scope="col" class="px-5 py-3 text-right">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrder->productOrders as $order)
                                    <tr class="bg-white border-b dark:bg-zinc-800 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-600">
                                        <td class="px-5 py-4">
                                            <div class="font-medium text-zinc-900 dark:text-white">{{ $order->product->remarks ?? $order->product->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm font-mono text-zinc-600 dark:text-zinc-400">{{ $order->product->sku ?? '—' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $order->product->supplier_code ?? '—' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $order->product->category->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-left">
                                            {{ number_format($order->quantity, 0) }} {{ $order->product->uom ?? 'pcs' }}
                                        </td>
                                        <td class="px-5 py-4 text-right">₱{{ number_format($order->unit_price, 2) }}</td>
                                        <td class="px-5 py-4 text-right font-semibold text-zinc-900 dark:text-white">
                                            ₱{{ number_format($order->total_price, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-zinc-800 dark:border-zinc-700">
                                        <td colspan="7" class="px-6 py-4 text-center">No items found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary -->
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($this->totalQuantity, 0) }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Total Ordered</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $purchaseOrder->productOrders->count() }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Items Count</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">₱{{ number_format($this->totalPrice, 2) }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Total Value</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- CANCELLED Status Section -->
        @if($purchaseOrder->status === 'cancelled')
        <div class="bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 rounded-lg border-2 border-red-300 dark:border-red-700 shadow-sm mb-6">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <div>
                            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Cancelled Purchase Order</h2>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Purchase Order #{{ $purchaseOrder->po_num }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                        Cancelled
                    </span>
                </div>

                <!-- QR Code and Summary Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- QR Code -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4 text-center">QR Code</h3>
                        <div class="flex justify-center mb-4">
                            <div class="bg-white p-4 rounded-xl shadow-lg border-2 border-red-200 dark:border-red-700">
                                {!! QrCode::size(200)->generate($purchaseOrder->po_num) !!}
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="button" onclick="printQRCode()"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Print QR Code
                            </button>
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Purchase Order Details</h3>
                        <div class="grid gap-4">
                            <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Supplier:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Department:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->department->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Order Date:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->order_date->format('M d, Y') }}</span>
                            </div>
                            @if($purchaseOrder->cancellation_reason)
                            <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400 block mb-1">Cancellation Reason:</span>
                                <span class="text-sm text-zinc-900 dark:text-white">{{ $purchaseOrder->cancellation_reason }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="mb-6">
                    <h4 class="text-md font-medium text-zinc-900 dark:text-white mb-3">📋 Purchase Order Items</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
                            <thead class="text-sm text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-700 dark:text-zinc-400">
                                <tr>
                                    <th scope="col" class="px-5 py-3 text-left">Product Name</th>
                                    <th scope="col" class="px-5 py-3 text-left">SKU</th>
                                    <th scope="col" class="px-5 py-3 text-left">Supplier Code (SKU)</th>
                                    <th scope="col" class="px-5 py-3 text-left">Category</th>
                                    <th scope="col" class="px-5 py-3 text-right">Unit Price</th>
                                    <th scope="col" class="px-5 py-3 text-right">Quantity</th>
                                    <th scope="col" class="px-5 py-3 text-right">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrder->productOrders as $order)
                                    <tr class="bg-white border-b dark:bg-zinc-800 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-600">
                                        <td class="px-5 py-4">
                                            <div class="font-medium text-zinc-900 dark:text-white">{{ $order->product->remarks ?? $order->product->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm font-mono text-zinc-600 dark:text-zinc-400">{{ $order->product->sku ?? '—' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $order->product->supplier_code ?? '—' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $order->product->category->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-right">₱{{ number_format($order->unit_price, 2) }}</td>
                                        <td class="px-5 py-4 text-right">
                                            {{ number_format($order->quantity, 0) }} {{ $order->product->uom ?? 'pcs' }}
                                        </td>
                                        <td class="px-5 py-4 text-right font-semibold text-zinc-900 dark:text-white">
                                            ₱{{ number_format($order->total_price, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-zinc-800 dark:border-zinc-700">
                                        <td colspan="7" class="px-5 py-4 text-center">No items found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary -->
                <div class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($this->totalQuantity, 0) }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Total Quantity</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $purchaseOrder->productOrders->count() }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Items Count</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">₱{{ number_format($this->totalPrice, 2) }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Total Value</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- TO_RECEIVE Status Section -->
        @if($purchaseOrder->status === \App\Enums\PurchaseOrderStatus::TO_RECEIVE)
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-lg border-2 border-purple-300 dark:border-purple-700 shadow-sm mb-6">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 9l6-3"></path>
                        </svg>
                        <div>
                            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Ready for Receiving</h2>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Purchase Order #{{ $purchaseOrder->po_num }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                        To Receive
                    </span>
                </div>

                <!-- QR Code and Summary Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- QR Code -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4 text-center">QR Code for Stock-In</h3>
                        <div class="flex justify-center mb-4">
                            <div class="bg-white p-4 rounded-xl shadow-lg border-2 border-purple-200 dark:border-purple-700">
                                {!! QrCode::size(200)->generate($purchaseOrder->po_num) !!}
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="button" onclick="printQRCode()"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-4 focus:ring-purple-300 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Print QR Code
                            </button>
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Purchase Order Summary</h3>
                        <div class="grid gap-4">
                            <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Supplier:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Department:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->department->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Order Date:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->order_date->format('M d, Y') }}</span>
                            </div>
                            @if($purchaseOrder->expected_delivery_date)
                            <div class="flex justify-between items-center p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Expected Delivery:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->expected_delivery_date->format('M d, Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Delivery Progress Alert (if variance exists) -->
                @php
                    $totalOrdered = $purchaseOrder->productOrders->sum('quantity');
                    $totalExpected = $purchaseOrder->productOrders->sum('expected_qty') ?: $totalOrdered;
                    
                    // ✅ CORRECT: received_quantity already contains GOOD items only
                    $totalReceived = $purchaseOrder->productOrders->sum('received_quantity'); // Good items
                    $totalDestroyed = $purchaseOrder->productOrders->sum('destroyed_qty') ?: 0; // Damaged items
                    $totalDelivered = $totalReceived + $totalDestroyed; // Total delivered (good + damaged)
                    
                    $hasVariance = $totalExpected != $totalOrdered;
                    $fulfillmentRate = $totalOrdered > 0 ? ($totalExpected / $totalOrdered * 100) : 100;
                    
                    // ✅ Delivery rate based on total delivered (good + damaged)
                    $deliveryRate = $totalExpected > 0 ? ($totalDelivered / $totalExpected * 100) : 0;
                    
                    // ✅ Quality rate: good items / total delivered
                    $qualityRate = $totalDelivered > 0 ? ($totalReceived / $totalDelivered * 100) : 100;
                @endphp

                @if($hasVariance)
                <div class="mb-4 p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-700">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-orange-800 dark:text-orange-300">Order Variance Detected</h4>
                            <p class="text-sm text-orange-700 dark:text-orange-400 mt-1">
                                Supplier could only provide <strong>{{ number_format($totalExpected, 0) }} units</strong> 
                                out of <strong>{{ number_format($totalOrdered, 0) }} ordered</strong>
                                ({{ number_format($fulfillmentRate, 1) }}% fulfillment)
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Items Ready for Receiving Section with Batch Details -->
                <div class="mb-6">
                    <h4 class="text-md font-medium text-zinc-900 dark:text-white mb-3">📦 Items Ready for Receiving</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
                            <thead class="text-sm text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-700 dark:text-zinc-400">
                                <tr>
                                    <th scope="col" class="px-5 py-3 text-left">Product Name</th>
                                    <th scope="col" class="px-5 py-3 text-left">SKU</th>
                                    <th scope="col" class="px-5 py-3 text-left">Supplier Code (SKU)</th>
                                    <th scope="col" class="px-5 py-3 text-left">Category</th>
                                    <th scope="col" class="px-5 py-3 text-left">Ordered Qty</th>
                                    <th scope="col" class="px-5 py-3 bg-purple-50 dark:bg-purple-900/20">Expected Qty</th>
                                    <th scope="col" class="px-5 py-3 bg-amber-50 dark:bg-amber-900/20">Batch Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrder->productOrders as $order)
                                    <tr class="bg-white border-b dark:bg-zinc-800 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-600">
                                        <td class="px-5 py-4">
                                            <div class="font-medium text-zinc-900 dark:text-white">{{ $order->product->remarks ?? $order->product->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm font-mono text-zinc-600 dark:text-zinc-400">{{ $order->product->sku ?? '—' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $order->product->supplier_code ?? '—' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $order->product->category->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-left">
                                            {{ number_format($order->quantity, 0) }} {{ $order->product->uom ?? 'pcs' }}
                                        </td>
                                        <td class="px-5 py-4 bg-purple-50 dark:bg-purple-900/20 text-left">
                                            <div class="font-medium text-purple-600 dark:text-purple-400">
                                                {{ number_format($order->expected_qty ?? $order->quantity, 0) }} {{ $order->product->uom ?? 'pcs' }}
                                            </div>
                                        </td>
                                        <!-- Batch information shown via Product Batches below; no per-line batch on product orders -->
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-zinc-800 dark:border-zinc-700">
                                        <td colspan="6" class="px-6 py-4 text-center">No items found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Receiving Progress Summary -->
                <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700">
                    <div class="grid gap-4 md:grid-cols-3 mb-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($totalOrdered, 0) }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Ordered</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($totalExpected, 0) }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Expected</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold {{ $deliveryRate >= 100 ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }}">
                                {{ number_format($totalDelivered, 0) }}
                            </div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Delivered</div>
                            <div class="text-xs font-medium {{ $deliveryRate >= 100 ? 'text-green-600 dark:text-green-500' : 'text-orange-600 dark:text-orange-500' }} mt-0.5">
                                {{ number_format($deliveryRate, 1) }}% complete
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="flex justify-between text-xs text-zinc-600 dark:text-zinc-400 mb-1">
                            <span>Delivery Progress</span>
                            <span>{{ number_format($totalDelivered, 0) }} / {{ number_format($totalExpected, 0) }} units</span>
                        </div>
                        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-3">
                            @php
                                $progressWidth = min($deliveryRate, 100);
                            @endphp
                            <div class="{{ $deliveryRate >= 100 ? 'bg-green-600 dark:bg-green-500' : 'bg-orange-500 dark:bg-orange-400' }} h-3 rounded-full transition-all" 
                                 style="width: {{ $progressWidth }}%"></div>
                        </div>
                    </div>
                    <!-- Quality Breakdown -->
                    <div class="grid gap-3 md:grid-cols-{{ $totalDestroyed > 0 ? '2' : '2' }} pt-3 border-t border-green-200 dark:border-green-800">
                        <!-- Good Items -->
                        @if($totalReceived > 0)
                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Good</span>
                                </div>
                                <span class="text-lg font-bold text-green-700 dark:text-green-400">{{ number_format($totalReceived, 0) }}</span>
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                {{ number_format($qualityRate, 1) }}% quality
                            </div>
                        </div>
                        @endif

                        <!-- Damaged Items -->
                        @if($totalDestroyed > 0)
                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Damaged</span>
                                </div>
                                <span class="text-lg font-bold text-red-700 dark:text-red-400">{{ number_format($totalDestroyed, 0) }}</span>
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                {{ number_format(100 - $qualityRate, 1) }}% damage rate
                            </div>
                        </div>
                        @endif        
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- RECEIVED Status Section -->
        @if($purchaseOrder->status === \App\Enums\PurchaseOrderStatus::RECEIVED)
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg border-2 border-green-300 dark:border-green-700 shadow-sm mb-6">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Received Purchase Order</h2>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Purchase Order #{{ $purchaseOrder->po_num }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                        Received
                    </span>
                </div>
                
                <!-- QR Code and Summary Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- QR Code -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4 text-center">QR Code</h3>
                        <div class="flex justify-center mb-4">
                            <div class="bg-white p-4 rounded-xl shadow-lg border-2 border-green-200 dark:border-green-700">
                                {!! QrCode::size(200)->generate($purchaseOrder->po_num) !!}
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="button" onclick="printQRCode()"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Print QR Code
                            </button>
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Purchase Order Details</h3>
                        <div class="grid gap-4">
                            <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Supplier:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Department:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->department->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Order Date:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->order_date->format('M d, Y') }}</span>
                            </div>
                            @if($purchaseOrder->expected_delivery_date)
                            <div class="flex justify-between items-center p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Expected Delivery:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->expected_delivery_date->format('M d, Y') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Ordered By:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->orderedByUser->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Approved By:</span>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->approverInfo->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Delivery Information --}}
                @if($purchaseOrder->deliveries && $purchaseOrder->deliveries->isNotEmpty())
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-zinc-900 dark:text-white mb-3">📦 Delivery Information</h4>
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($purchaseOrder->deliveries as $delivery)
                            @php
                                // Count unique batch numbers for this delivery
                                $batchCount = \App\Models\ProductBatch::whereHas('product', function($q) use ($purchaseOrder) {
                                    $q->whereIn('id', $purchaseOrder->productOrders->pluck('product_id'));
                                })
                                ->where('notes', 'LIKE', '%DR: ' . $delivery->dr_number . '%')
                                ->count();
                            @endphp
                            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-green-200 dark:border-green-700 p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h5 class="font-semibold text-zinc-900 dark:text-white">{{ $delivery->dr_number }}</h5>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-zinc-600 dark:text-zinc-400">Delivery Date:</span>
                                        <span class="font-medium text-zinc-900 dark:text-white">
                                            {{ $delivery->delivery_date ? \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') : 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-zinc-600 dark:text-zinc-400">Batches:</span>
                                        <span class="font-medium text-zinc-900 dark:text-white">{{ $batchCount }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif



                <!-- Delivery & Receiving Progress Summary -->
                @php
                    $totalOrdered = $purchaseOrder->productOrders->sum('quantity');
                    $totalExpected = $purchaseOrder->productOrders->sum('expected_qty') ?: $totalOrdered;
                    
                    // ✅ CORRECTED: received_quantity = GOOD items, destroyed_qty = DAMAGED items
                    $totalReceived = $purchaseOrder->productOrders->sum('received_quantity'); // Good items (100)
                    $totalDestroyed = $purchaseOrder->productOrders->sum('destroyed_qty') ?: 0; // Damaged items (23)
                    $totalDelivered = $totalReceived + $totalDestroyed; // Total delivered (100 + 23 = 123)
                    
                    $hasVariance = $totalExpected != $totalOrdered;
                    $fulfillmentRate = $totalOrdered > 0 ? ($totalExpected / $totalOrdered * 100) : 100;
                    
                    // ✅ Delivery rate: total delivered / expected (123/123 = 100%)
                    $deliveryRate = $totalExpected > 0 ? ($totalDelivered / $totalExpected * 100) : 0;
                    
                    // ✅ Quality rate: good items / total delivered (100/123 = 81.3%)
                    $qualityRate = $totalDelivered > 0 ? ($totalReceived / $totalDelivered * 100) : 100;
                @endphp

                    
                <!-- Variance Alert -->
                @if($hasVariance)
                <div class="mb-4 p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-700">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-orange-800 dark:text-orange-300">Order Variance</h4>
                            <p class="text-sm text-orange-700 dark:text-orange-400 mt-1">
                                Supplier could only produce <strong>{{ number_format($totalExpected, 0) }} units</strong> 
                                out of <strong>{{ number_format($totalOrdered, 0) }} ordered</strong>
                                ({{ number_format($fulfillmentRate, 1) }}% fulfillment)
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Items Received Section with Comprehensive Details -->
                <div class="mb-6">
                    <h4 class="text-md font-medium text-zinc-900 dark:text-white mb-3">📋 Received Items with Quality Control</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
                            <thead class="text-sm text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-700 dark:text-zinc-400">
                                <tr>
                                    <th scope="col" class="px-5 py-3 text-left">Product Name</th>
                                    <th scope="col" class="px-5 py-3 text-left">SKU</th>
                                    <th scope="col" class="px-5 py-3 text-left">Supplier Code (SKU)</th>
                                    <th scope="col" class="px-5 py-3 text-left">Category</th>
                                    <th scope="col" class="px-5 py-3 text-right">Ordered Qty</th>
                                    <th scope="col" class="px-5 py-3 bg-purple-50 dark:bg-purple-900/20 text-right">Expected Qty</th>
                                    <th scope="col" class="px-5 py-3 bg-green-50 dark:bg-green-900/20 text-right">Received Qty</th>
                                    @if($totalDestroyed > 0)
                                    <th scope="col" class="px-5 py-3 bg-red-50 dark:bg-red-900/20 text-right">Destroyed Qty</th>
                                    @endif
                                    <th scope="col" class="px-5 py-3">Receiving Status</th>
                                    <th scope="col" class="px-5 py-3 text-right">Unit Price</th>
                                    <th scope="col" class="px-5 py-3 text-right">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrder->productOrders as $order)
                                    <tr class="bg-white border-b dark:bg-zinc-800 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-600">
                                        <td class="px-5 py-4">
                                            <div class="font-medium text-zinc-900 dark:text-white">{{ $order->product->remarks ?? $order->product->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm font-mono text-zinc-600 dark:text-zinc-400">{{ $order->product->sku ?? '—' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $order->product->supplier_code ?? '—' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $order->product->category->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            {{ number_format($order->quantity, 0) }} {{ $order->product->uom ?? 'pcs' }}
                                        </td>
                                        <td class="px-5 py-4 bg-purple-50 dark:bg-purple-900/20 text-right">
                                            <div class="font-medium text-purple-600 dark:text-purple-400">
                                                {{ number_format($order->expected_qty ?? $order->quantity, 0) }} {{ $order->product->uom ?? 'pcs' }}
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 bg-green-50 dark:bg-green-900/20 text-right">
                                            <div class="font-semibold text-green-600 dark:text-green-400">
                                                {{ number_format($order->received_quantity, 0) }} {{ $order->product->uom ?? 'pcs' }}
                                            </div>
                                        </td>
                                        @if($totalDestroyed > 0)
                                        <td class="px-5 py-4 bg-red-50 dark:bg-red-900/20 text-right">
                                            <div class="font-medium text-red-600 dark:text-red-400">
                                                {{ number_format($order->destroyed_qty ?? 0, 0) }} {{ $order->product->uom ?? 'pcs' }}
                                            </div>
                                        </td>
                                        @endif
                                        <td class="px-5 py-4">
                                            @php
                                                $itemReceived = $order->received_quantity ?? 0; // Good items
                                                $itemDestroyed = $order->destroyed_qty ?? 0; // Damaged items
                                                $itemDelivered = $itemReceived + $itemDestroyed; // Total delivered
                                                $itemExpected = $order->expected_qty ?? $order->quantity;
                                                $itemDeliveryRate = $itemExpected > 0 ? ($itemDelivered / $itemExpected * 100) : 0;
                                                
                                                // ✅ Determine status based on delivery completion and damage
                                                $goodReceivedRate = $itemExpected > 0 ? ($itemReceived / $itemExpected * 100) : 0;
                                                
                                                if ($goodReceivedRate >= 100) {
                                                    // All expected items received in good condition
                                                    $receivingStatus = 'complete';
                                                    $statusLabel = 'Complete';
                                                    $statusColor = 'green';
                                                    $statusIcon = '✓';
                                                } elseif ($itemDelivered >= $itemExpected && $itemDestroyed > 0) {
                                                    // Fully delivered but contains damaged items
                                                    $receivingStatus = 'damaged';
                                                    $statusLabel = 'Damaged';
                                                    $statusColor = 'orange';
                                                    $statusIcon = '⚠';
                                                } elseif ($itemReceived > 0 || $itemDestroyed > 0) {
                                                    // Partially received/delivered
                                                    $receivingStatus = 'incomplete';
                                                    $statusLabel = 'Incomplete';
                                                    $statusColor = 'yellow';
                                                    $statusIcon = '⚠';
                                                } else {
                                                    // Nothing received yet
                                                    $receivingStatus = 'pending';
                                                    $statusLabel = 'Pending';
                                                    $statusColor = 'gray';
                                                    $statusIcon = '⏳';
                                                }
                                            @endphp
                                            
                                            <!-- Delivery Progress -->
                                            <div class="mb-2">
                                                <div class="flex items-center justify-between text-xs mb-1">
                                                    <span class="text-zinc-600 dark:text-zinc-400">Delivered:</span>
                                                    <span class="font-medium {{ $itemDeliveryRate >= 100 ? 'text-blue-600 dark:text-blue-400' : 'text-orange-600 dark:text-orange-400' }}">
                                                        {{ number_format($itemDelivered, 0) }}/{{ number_format($itemExpected, 0) }}
                                                    </span>
                                                </div>
                                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 mb-2">
                                                    @php
                                                        $itemProgressWidth = min($itemDeliveryRate, 100);
                                                    @endphp
                                                    <div class="{{ $itemDeliveryRate >= 100 ? 'bg-blue-600 dark:bg-blue-500' : 'bg-orange-500 dark:bg-orange-400' }} h-1.5 rounded-full transition-all" 
                                                        style="width: {{ $itemProgressWidth }}%"></div>
                                                </div>
                                                
                                                <!-- Receiving Status Badge -->
                                                <div class="flex items-center justify-center">
                                                    @if($receivingStatus === 'complete')
                                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            {{ $statusLabel }}
                                                        </span>
                                                    @elseif($receivingStatus === 'damaged')
                                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            {{ $statusLabel }}
                                                        </span>
                                                    @elseif($receivingStatus === 'incomplete')
                                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            {{ $statusLabel }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            {{ $statusLabel }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Receiving Remarks -->
                                            @if($order->notes)
                                            <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400 italic">
                                                {{ $order->notes }}
                                            </div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-right">₱{{ number_format($order->unit_price, 2) }}</td>
                                        <td class="px-5 py-4 text-right font-semibold text-zinc-900 dark:text-white">
                                            ₱{{ number_format($order->total_price, 2) }}
                                        </td>
                                    </tr>
                                    
                                    {{-- Detailed Batch Information Display --}}
                                    {{-- ✅ UPDATED: Detailed Batch Information Display - Filter by PO --}}
                                    @php
                                        $itemBatches = \App\Models\ProductBatch::where('product_id', $order->product_id)
                                            ->where('purchase_order_id', $purchaseOrder->id) // ✅ CHANGED: Filter by PO instead of batch_number
                                            ->where('batch_number', $order->batch_number)
                                            ->orderBy('created_at', 'desc')
                                            ->get();
                                    @endphp

                                    @if($order->batch_number && $itemBatches->isNotEmpty())
                                    <tr class="bg-amber-50 dark:bg-zinc-800 border-0">
                                        <td colspan="{{ $totalDestroyed > 0 ? 11 : 10 }}" class="px-5 py-4 border-0">
                                            <div class="ml-4">
                                                <div class="flex items-center gap-2 mb-3">
                                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                    </svg>
                                                    <h5 class="text-sm font-semibold text-amber-800 dark:text-amber-200">Received Batches ({{ $itemBatches->count() }})</h5>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                                    @foreach($itemBatches as $batch)
                                                        <div style="border: 2px solid #d97706;" class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-lg p-3">
                                                            <div class="space-y-2 text-xs">
                                                                {{-- Header with batch number and status --}}
                                                                <div class="flex justify-between items-center pb-2 border-b border-amber-600">
                                                                    <span class="font-bold text-base text-amber-400">{{ $batch->batch_number }}</span>
                                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-green-600 text-white">
                                                                        Active
                                                                    </span>
                                                                </div>
                                                                
                                                                {{-- Initial and Current quantities --}}
                                                                <div class="grid grid-cols-2 gap-2">
                                                                    <div>
                                                                        <span class="text-zinc-400">Initial:</span>
                                                                        <span class="font-semibold text-white ml-1 text-base">{{ number_format($batch->initial_qty, 0) }}</span>
                                                                    </div>
                                                                    <div>
                                                                        <span class="text-zinc-400">Current:</span>
                                                                        <span class="font-semibold text-white ml-1 text-base">{{ number_format($batch->current_qty, 0) }}</span>
                                                                    </div>
                                                                </div>
                                                                
                                                                {{-- Received date --}}
                                                                @if($batch->received_date)
                                                                <div class="flex items-center gap-1.5 text-blue-400">
                                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                    <span class="text-xs">Received:</span>
                                                                    <span class="text-white font-medium">{{ \Carbon\Carbon::parse($batch->received_date)->format('M d, Y') }}</span>
                                                                </div>
                                                                @endif
                                                                
                                                                {{-- Received by user --}}
                                                                @if($batch->receivedByUser)
                                                                <div class="flex items-center gap-1.5 text-purple-400">
                                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                                    </svg>
                                                                    <span class="text-xs">By:</span>
                                                                    <span class="text-white font-medium">{{ $batch->receivedByUser->name }}</span>
                                                                </div>
                                                                @endif
                                                                
                                                                {{-- Location --}}
                                                                <div class="flex items-center gap-1.5 text-indigo-400">
                                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    </svg>
                                                                    <span class="text-xs">Location:</span>
                                                                    <span class="text-white font-medium">Warehouse</span>
                                                                </div>
                                                                
                                                                {{-- Notes section --}}
                                                                @php
                                                                    // Extract meaningful notes (skip auto-generated lines)
                                                                    $noteLines = explode("\n", $batch->notes ?? '');
                                                                    $cleanNotes = [];
                                                                    foreach ($noteLines as $line) {
                                                                        $line = trim($line);
                                                                        // Skip empty lines and auto-generated content
                                                                        if ($line && 
                                                                            !str_starts_with($line, 'Received') && 
                                                                            !str_starts_with($line, 'DR:') && 
                                                                            !str_starts_with($line, 'PO:') && 
                                                                            !str_starts_with($line, 'Date:') &&
                                                                            !str_starts_with($line, '📦') &&
                                                                            !str_starts_with($line, '✅') &&
                                                                            !str_starts_with($line, '👤')) {
                                                                            $cleanNotes[] = $line;
                                                                        }
                                                                    }
                                                                    $displayNotes = implode(' ', $cleanNotes);
                                                                @endphp
                                                                
                                                                <div class="mt-2 p-2" style="background-color: #3b1f06;" class="rounded border border-amber-900/50 text-xs">
                                                                    <span class="font-semibold text-amber-400">Notes:</span>
                                                                    <p class="text-amber-200/90 mt-1 whitespace-normal break-words">
                                                                        {{ $displayNotes ?: 'Received only ' . number_format($batch->initial_qty, 0) }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr class="bg-white border-b dark:bg-zinc-800 dark:border-zinc-700">
                                        <td colspan="{{ $totalDestroyed > 0 ? 11 : 10 }}" class="px-5 py-4 text-center">No items found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Comprehensive Summary Cards -->
                <div class="mt-6 space-y-4">
                    <!-- Main Summary Cards -->
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700">
                        <div class="grid gap-4 md:grid-cols-3 mb-4">
                            <!-- Ordered Quantity -->
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($totalOrdered, 0) }}</div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">Ordered</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-500 mt-0.5">(Purchase intent)</div>
                            </div>
                            
                            <!-- Expected Quantity -->
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($totalExpected, 0) }}</div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">Expected</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-500 mt-0.5">(Supplier commitment)</div>
                            </div>
                            
                            <!-- Delivered Quantity -->
                            <div class="text-center">
                                <div class="text-2xl font-bold {{ $deliveryRate >= 100 ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }}">
                                    {{ number_format($totalDelivered, 0) }}/{{ number_format($totalExpected, 0) }}
                                </div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">Delivered</div>
                                <div class="text-xs font-medium {{ $deliveryRate >= 100 ? 'text-green-600 dark:text-green-500' : 'text-orange-600 dark:text-orange-500' }} mt-0.5">
                                    {{ number_format($deliveryRate, 1) }}% complete
                                </div>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between text-xs text-zinc-600 dark:text-zinc-400 mb-1">
                                <span>Delivery Progress</span>
                                <span>{{ number_format($totalDelivered, 0) }} / {{ number_format($totalExpected, 0) }} units</span>
                            </div>
                            <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-3">
                                @php
                                    $progressWidth = min($deliveryRate, 100);
                                @endphp
                                <div class="{{ $deliveryRate >= 100 ? 'bg-green-600 dark:bg-green-500' : 'bg-orange-500 dark:bg-orange-400' }} h-3 rounded-full transition-all" 
                                     style="width: {{ $progressWidth }}%"></div>
                            </div>
                        </div>
                        
                        <!-- Quality Breakdown -->
                        <div class="grid gap-3 md:grid-cols-{{ $totalDestroyed > 0 ? '3' : '2' }} pt-3 border-t border-green-200 dark:border-green-800">
                            <!-- Good Items -->
                            <div class="bg-white dark:bg-zinc-800 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Good</span>
                                    </div>
                                    <span class="text-lg font-bold text-green-700 dark:text-green-400">{{ number_format($totalReceived, 0) }}</span>
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                    {{ number_format($qualityRate, 1) }}% quality
                                </div>
                            </div>
                            
                            <!-- Damaged Items -->
                            @if($totalDestroyed > 0)
                            <div class="bg-white dark:bg-zinc-800 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Damaged</span>
                                    </div>
                                    <span class="text-lg font-bold text-red-700 dark:text-red-400">{{ number_format($totalDestroyed, 0) }}</span>
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                    {{ number_format(100 - $qualityRate, 1) }}% damage rate
                                </div>
                            </div>
                            @endif
                            
                            <!-- Total Value -->
                            <div class="bg-white dark:bg-zinc-800 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Value</span>
                                    </div>
                                    <span class="text-lg font-bold text-emerald-700 dark:text-emerald-400">₱{{ number_format($this->totalPrice, 2) }}</span>
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                    Total order value
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

    <!-- Action Buttons Section -->
    <div class="flex items-center justify-between mt-6 -mx-6 px-6">
        <!-- Left Section: Back Button -->
        <div class="flex items-center">
            <a href="{{ route('pomanagement.purchaseorder') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-lg hover:bg-zinc-50 focus:outline-none focus:ring-4 focus:ring-zinc-200 dark:bg-zinc-700 dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-600 dark:focus:ring-zinc-800 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Purchase Orders
            </a>
        </div>

        <!-- Right Section: Action Buttons -->
        <div class="flex items-center space-x-3">
            @php
                use App\Enums\Enum\PermissionEnum;
            @endphp

            @can('po approve')
                @if($purchaseOrder->status === \App\Enums\PurchaseOrderStatus::PENDING || $purchaseOrder->status === 'pending')
                    <button type="button" 
                        wire:click="ApprovePurchaseOrder"
                        wire:confirm="Are you sure you want to approve Purchase Order #{{ $purchaseOrder->po_num }}?"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Approve Purchase Order
                    </button>
                    <button type="button" 
                        onclick="const rejectReason = prompt('Please enter cancellation reason:'); 
                                if(rejectReason && rejectReason.trim()) { 
                                    $wire.set('cancellation_reason', rejectReason); 
                                    $wire.call('RejectPurchaseOrder'); 
                                } else if(rejectReason !== null) {
                                    alert('Cancellation reason is required!');
                                }"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reject Purchase Order
                    </button>
                @endcan
            @elseif($purchaseOrder->status === \App\Enums\PurchaseOrderStatus::APPROVED)
                @can('po approve')
                    <button type="button" wire:click="ApprovePurchaseOrder"
                        wire:confirm="Are you sure you want to submit this information? Make sure batch information is correct."
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Complete Approval
                    </button>
                @endcan
            @elseif($purchaseOrder->status === \App\Enums\PurchaseOrderStatus::TO_RECEIVE)
                @can('receive_goods')
                    <a href="{{ route('warehousestaff.stockin') }}?po={{ $purchaseOrder->po_num }}"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-4 focus:ring-purple-300 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 9l6-3"></path>
                        </svg>
                        Start Receiving
                    </a>
                @endcan
                @can('po approve')
                    <button type="button"
                        onclick="const returnReason = prompt('Please enter reason for returning to approved status:');
                                if(returnReason && returnReason.trim()) {
                                    $wire.set('cancellation_reason', returnReason);
                                    $wire.call('ReturnToApproved');
                                } else if(returnReason !== null) {
                                    alert('Reason is required!');
                                }"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-4 focus:ring-yellow-300 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Return to Approved
                    </button>
                @endcan
            @elseif($purchaseOrder->status === \App\Enums\PurchaseOrderStatus::RECEIVED)
                @can('view_products')
                    <a href="{{ route('product-management.index') }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        View Products
                    </a>
                @endcan
            @endif
        </div>
    </div>
</div>

<script>
function printQRCode() {
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    const qrCodeHtml = `{!! QrCode::size(400)->generate($purchaseOrder->po_num) !!}`;
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Purchase Order QR Code - {{ $purchaseOrder->po_num }}</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background: white;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                }
                .print-container {
                    text-align: center;
                    max-width: 600px;
                }
                .header {
                    margin-bottom: 30px;
                }
                .company-name {
                    font-size: 24px;
                    font-weight: bold;
                    color: #1f2937;
                    margin-bottom: 10px;
                }
                .document-title {
                    font-size: 18px;
                    color: #6b7280;
                    margin-bottom: 20px;
                }
                .qr-container {
                    background: white;
                    padding: 30px;
                    border: 2px solid #e5e7eb;
                    border-radius: 12px;
                    margin: 20px 0;
                    display: inline-block;
                }
                .po-number {
                    font-size: 28px;
                    font-weight: bold;
                    color: #2563eb;
                    margin: 20px 0 10px 0;
                    font-family: monospace;
                }
                .po-label {
                    font-size: 14px;
                    color: #6b7280;
                    margin-bottom: 30px;
                }
                .po-details {
                    background: #f9fafb;
                    padding: 20px;
                    border-radius: 8px;
                    margin-top: 20px;
                    text-align: left;
                }
                .detail-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 8px;
                    padding: 5px 0;
                }
                .detail-label {
                    font-weight: 600;
                    color: #374151;
                }
                .detail-value {
                    color: #6b7280;
                }
                .footer {
                    margin-top: 30px;
                    font-size: 12px;
                    color: #9ca3af;
                }
                @media print {
                    body { margin: 0; }
                    .print-container { max-width: none; }
                }
            </style>
        </head>
        <body>
            <div class="print-container">
                <div class="header">
                    <div class="company-name">Jovanni</div>
                    <div class="document-title">Purchase Order QR Code</div>
                </div>
                
                <div class="qr-container">
                    ${qrCodeHtml}
                </div>
                
                <div class="po-number">{{ $purchaseOrder->po_num }}</div>
                <div class="po-label">Purchase Order Number</div>
                
                <div class="po-details">
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">{{ $purchaseOrder->status->label() }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Supplier:</span>
                        <span class="detail-value">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Department:</span>
                        <span class="detail-value">{{ $purchaseOrder->department->name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Order Date:</span>
                        <span class="detail-value">{{ $purchaseOrder->order_date->format('M d, Y') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Quantity:</span>
                        <span class="detail-value">${parseFloat({{ $this->totalQuantity }}).toFixed(0)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Price:</span>
                        <span class="detail-value">₱${parseFloat({{ $this->totalPrice }}).toFixed(2)}</span>
                    </div>
                </div>
                
                <div class="footer">
                    Generated on ${new Date().toLocaleString()} | Jovanni PO System
                </div>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
    };
}
</script>