<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Sales Order QR Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-container { box-shadow: none !important; margin: 0 !important; }
        }
        .modern-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Sales Order QR Code</h1>
            <p class="text-gray-600">Scan the QR code to access order details</p>
        </div>

        <!-- Main Card -->
        <div class="modern-card p-8 print-container">
            <div class="glass-effect rounded-xl p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- QR Code Section -->
                    <div class="flex flex-col items-center justify-center space-y-4">
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            {!! QrCode::size(200)->generate($salesOrder->sales_order_number) !!}
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-600">Order Number</p>
                            <p class="text-lg font-mono font-bold text-gray-800">{{ $salesOrder->sales_order_number }}</p>
                        </div>
                    </div>

                    <!-- Order Details Section -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Order Information
                            </h3>
                            <div class="grid grid-cols-1 gap-3">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-sm font-medium text-gray-600">Branch:</span>
                                    <span class="text-sm font-semibold text-gray-800">{{ $salesOrder->customers->pluck('name')->join(', ') }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-sm font-medium text-gray-600">Agent:</span>
                                    <span class="text-sm font-semibold text-gray-800">{{ $salesOrder->agents->pluck('name')->join(', ') }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-sm font-medium text-gray-600">Status:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($salesOrder->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($salesOrder->status === 'approved') bg-green-100 text-green-800
                                        @elseif($salesOrder->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ str_replace('_', ' ', ucfirst($salesOrder->status)) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-sm font-medium text-gray-600">Order Date:</span>
                                    <span class="text-sm font-semibold text-gray-800">{{ $salesOrder->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-sm font-medium text-gray-600">Delivery Date:</span>
                                    <span class="text-sm font-semibold text-gray-800">{{ $salesOrder->delivery_date ? $salesOrder->delivery_date->format('M d, Y') : 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-sm font-medium text-gray-600">Contact:</span>
                                    <span class="text-sm font-semibold text-gray-800">{{ $salesOrder->contact_person_name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-sm font-medium text-gray-600">Total Quantity:</span>
                                    <span class="text-sm font-semibold text-gray-800">{{ number_format($salesOrder->items->sum('quantity'), 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-sm font-medium text-gray-600">Total Amount:</span>
                                    <span class="text-lg font-bold text-green-600">₱{{ number_format($salesOrder->items->sum('subtotal'), 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items Section -->
                @if($salesOrder->items->count() > 0)
                <div class="mt-8">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Order Items
                    </h4>
                    <div class="overflow-x-auto">
                        <table class="w-full bg-white rounded-lg overflow-hidden shadow-sm">
                            <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">SKU</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Unit Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($salesOrder->items as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $item->product->sku ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->product->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">₱{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">Grand Total:</td>
                                    <td class="px-6 py-4 text-lg font-bold text-green-600">₱{{ number_format($salesOrder->items->sum('subtotal'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Print Button -->
        <div class="text-center mt-8 no-print">
            <button onclick="window.print()"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg shadow-lg hover:from-blue-700 hover:to-blue-800 transform hover:scale-105 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print QR Code
            </button>
        </div>
    </div>
</body>
</html>