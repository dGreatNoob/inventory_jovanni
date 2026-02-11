{{-- Shared PO header: QR code + key metadata. Pass $purchaseOrder and $currencySymbol. Optional $qrBorderColor (e.g. border-zinc-200). --}}
@php
    $currencySymbol = $currencySymbol ?? ($purchaseOrder->currency?->symbol ?? '₱');
    $qrBorderColor = $qrBorderColor ?? 'border-zinc-200 dark:border-zinc-700';
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- QR Code -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4 text-center">QR Code</h3>
        <div class="flex justify-center mb-4">
            <div class="bg-white p-4 rounded-xl shadow-lg border-2 {{ $qrBorderColor }}">
                {!! QrCode::size(200)->generate($purchaseOrder->po_num) !!}
            </div>
        </div>
        <div class="text-center">
            <button type="button" onclick="printQRCode()"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-zinc-600 border border-transparent rounded-lg hover:bg-zinc-700 focus:outline-none focus:ring-4 focus:ring-zinc-300 dark:bg-zinc-600 dark:hover:bg-zinc-700 dark:focus:ring-zinc-800 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print QR Code
            </button>
        </div>
    </div>

    <!-- Purchase Order Details -->
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
            @if($purchaseOrder->approverInfo ?? null)
            <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Approved By:</span>
                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $purchaseOrder->approverInfo->name ?? 'N/A' }}</span>
            </div>
            @endif
            @if(!empty($purchaseOrder->cancellation_reason))
            <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400 block mb-1">Cancellation Reason:</span>
                <span class="text-sm text-zinc-900 dark:text-white">{{ $purchaseOrder->cancellation_reason }}</span>
            </div>
            @endif
        </div>
    </div>
</div>
