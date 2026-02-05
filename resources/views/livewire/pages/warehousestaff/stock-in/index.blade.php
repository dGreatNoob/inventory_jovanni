<div class="min-h-[70vh] flex flex-col justify-center items-center py-2 sm:py-4 px-2 sm:px-4">
    @php
        $steps = [
            ['label' => 'Scan QR'],
            ['label' => 'Review'],
            ['label' => 'Submit'],
            ['label' => 'Finish'],
        ];
    @endphp
    <!-- Stepper Bar -->
    <div class="w-full max-w-sm sm:max-w-md mx-auto mb-3 sticky top-0">
        <div class="flex items-center justify-between bg-zinc-50 dark:bg-zinc-900 rounded-2xl shadow-2xl p-2 sm:p-4 border-b border-gray-200 dark:border-zinc-700 z-20">
            @foreach($steps as $i => $step)
                <div class="flex-1 flex flex-col items-center relative">
                    <!-- Step Circle (uniform size, color as indicator) -->
                    @if($currentStep > $i)
                        <div class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-full bg-emerald-500 text-white text-sm sm:text-base shadow-md">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </div>
                    @elseif($currentStep === $i)
                        <div class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-full bg-blue-600 text-white text-sm sm:text-base shadow-md">
                            <span class="font-bold">{{ $i + 1 }}</span>
                        </div>
                    @else
                        <div class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-full bg-gray-300 dark:bg-zinc-700 text-gray-500 dark:text-gray-300 text-sm sm:text-base shadow-md">
                            <span class="font-bold">{{ $i + 1 }}</span>
                        </div>
                    @endif
                    <!-- Connecting Line -->
                    @if($i < count($steps) - 1)
                        <div class="absolute top-1/2 right-0 w-full h-1 z-0" style="left: 50%; width: calc(100% - 2.25rem);">
                            <div class="h-1 rounded-full transition-all duration-300
                                @if($currentStep > $i) bg-emerald-500
                                @elseif($currentStep === $i) bg-blue-600 dark:bg-blue-600
                                @else bg-gray-300 dark:bg-zinc-700 @endif"></div>
                        </div>
                    @endif
                    <!-- Step Label -->
                    <div class="mt-1 sm:mt-2 text-center">
                        <div class="text-xs sm:text-sm font-bold {{ $currentStep === $i ? 'text-blue-900 dark:text-blue-100' : 'text-gray-700 dark:text-gray-200' }}">
                            {{ $step['label'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Error Status Bar -->
    @if($messageType === 'error' && $message)
    <div class="w-full max-w-sm sm:max-w-md mx-auto mb-2 px-2 sm:px-4">
        <div class="rounded-lg p-2 sm:p-3 mb-2 text-sm font-mono shadow border bg-red-100 text-red-800 border-red-300 dark:bg-red-900 dark:text-red-100 dark:border-red-700">
            <div><strong>Error:</strong> {{ $message }}</div>
        </div>
    </div>
    @endif

    <!-- Step 1: Scan QR -->
    @if($currentStep === 0)
        
    <!-- Camera Status Toast -->
    <div id="camera-status-toast" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm sm:max-w-md px-4 hidden">
        <div id="camera-status-toast-content" class="flex items-start gap-3 p-4 rounded-xl shadow-xl border bg-blue-50 border-blue-300 text-blue-900 dark:bg-blue-900 dark:border-blue-700 dark:text-blue-100">
            <div class="pt-1">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>
            </div>
            <div class="flex-1">
                <div id="camera-status-toast-message" class="text-sm"></div>
            </div>
            <button onclick="document.getElementById('camera-status-toast').classList.add('hidden')" class="ml-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>
    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-2xl shadow-lg p-2 sm:p-4 flex flex-col items-center w-full max-w-sm sm:max-w-md mx-auto space-y-2 sm:space-y-4">
        <div id="qr-reader"
            class="w-full aspect-square bg-gray-100 dark:bg-zinc-700 border-2 border-dashed border-gray-300 dark:border-zinc-600 rounded-2xl overflow-hidden flex items-center justify-center relative">
            <div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
                <div class="text-center">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V6a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1zm12 0h2a1 1 0 001-1V6a1 1 0 00-1-1h-2a1 1 0 00-1 1v1a1 1 0 001 1zM5 20h2a1 1 0 001-1v-1a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1z"></path>
                    </svg>
                    <p class="text-sm sm:text-base">Camera will appear here</p>
                </div>
            </div>
            <div id="qr-loading" class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-zinc-900/80 z-10 hidden">
                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
            </div>
        </div>
        <div id="qr-result" class="hidden w-full mt-2 p-2 sm:p-3 rounded-lg bg-green-50 border border-green-400 text-green-700 text-center font-semibold flex flex-col items-center justify-center">
            <span class="text-xl sm:text-2xl mb-1">✅</span>
            <span id="qr-value" class="font-mono text-sm sm:text-base break-words"></span>
        </div>
    </div>

    <!-- Manual PO Input Section -->
    <div class="w-full max-w-sm sm:max-w-md mx-auto mt-3 px-2 sm:px-4">
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-2 sm:p-4 border border-gray-200 dark:border-zinc-700">
            <div class="flex flex-col items-center mb-3 gap-2">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white text-center">Input PO #</h3>
                <button 
                    wire:click="toggleManualInput"
                    class="text-xs sm:text-sm px-3 py-1.5 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:ring-2 focus:ring-blue-500 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-700 dark:hover:bg-blue-900/30 transition-colors">
                    {{ $showManualInput ? 'Hide Input' : 'Show Input' }}
                </button>
            </div>
            
            @if($showManualInput)
                <div class="space-y-2 sm:space-y-3">
                    <!-- Manual PO Input -->
                    <div>
                        <label for="manual-po" class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Enter PO Number
                        </label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="flex-1 relative">
                                <input 
                                    type="text" 
                                    id="manual-po"
                                    wire:model="manualPONumber"
                                    wire:keydown.enter="processManualPO"
                                    class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-400 dark:focus:border-blue-400 font-mono"
                                    placeholder="Enter PO number (e.g., PO-2025-1001)"
                                    autocomplete="off"
                                    style="text-transform: uppercase;">
                            </div>
                            <div class="flex gap-2">
                                <button 
                                    wire:click="resetPOInput"
                                    class="px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors"
                                    title="Reset to {{ $poPrefix }}">
                                    Reset
                                </button>
                                <button 
                                    wire:click="processManualPO"
                                    class="px-4 py-2 text-xs sm:text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors">
                                    Process
                                </button>
                            </div>
                        </div>
                        
                        <!-- Format examples -->
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <p class="mb-1"><strong>PO Format:</strong></p>
                            <div class="grid grid-cols-1 gap-1 font-mono">
                                <div><span class="text-blue-600">{{ $poPrefix }}</span><span class="text-gray-400">XXXX</span></div>
                                <div class="text-gray-400">Example: {{ $poPrefix }}1001</div>
                            </div>
                            <p class="mt-1 text-gray-400">Field is pre-filled with "{{ $poPrefix }}" - just add the number</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Step 2: Items Review -->
    <!-- Step 2: Items Review -->
    @if($currentStep === 1 && $foundPurchaseOrder)
    <div class="w-full max-w-md mx-auto space-y-4" wire:init="ensureCorrectPO">
        
        <!-- Purchase Order Header -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Purchase Order Details</h2>
                <button wire:click="goBackToStep1" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">PO Number:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $foundPurchaseOrder->po_num }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Supplier:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $foundPurchaseOrder->supplier->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Order Date:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $foundPurchaseOrder->order_date->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Delivery To:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $foundPurchaseOrder->department->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Items:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $foundPurchaseOrder->productOrders->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Delivery Number Input -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Delivery Information</h3>
            <div>
                <label for="delivery-number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    DR # (Delivery Receipt Number): <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="delivery-number"
                    wire:model="drNumber"
                    class="w-full px-3 py-2 text-sm border {{ empty(trim($drNumber)) && $messageType === 'error' ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }} rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-400 dark:focus:border-blue-400"
                    placeholder="Enter DR # (e.g., DR-2024-001)"
                    autocomplete="off"
                    style="text-transform: uppercase;">
                
                @if(empty(trim($drNumber)) && $messageType === 'error' && str_contains($message, 'Delivery Receipt'))
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                        <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @endif
            </div>
        </div>

        <!-- Items Review Section -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Review Items</h3>
            
            <div class="space-y-4">
                @foreach($foundPurchaseOrder->productOrders as $productOrder)
                @php
                    $orderedQty = $productOrder->quantity;
                    $expectedQty = $productOrder->expected_qty ?? $productOrder->quantity;
                    $receivedQty = $productOrder->received_quantity ?? 0;
                    $destroyedQty = $productOrder->destroyed_qty ?? 0;
                    $totalDelivered = $receivedQty + $destroyedQty;
                    $remainingQty = $productOrder->remaining_quantity; // ✅ Uses the model accessor
                    $isFullyReceived = $totalDelivered >= $expectedQty;
                    
                    $orderedUomDisplay = $productOrder->product->uom ?? 'pcs';
                    $remainingUomDisplay = $orderedUomDisplay;
                @endphp
                
                <div class="border border-gray-200 dark:border-zinc-600 rounded-xl p-4 space-y-3">
                    <!-- Product Name -->
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white text-sm">
                            {{ $productOrder->product->name }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            SKU: {{ $productOrder->product->sku }}
                        </div>
                    </div>

                    <!-- Batch information is managed via Product Batches; no per-line batch number from PO -->

                    <!-- Received Qty input field -->
                    <div class="space-y-2">
                        @php
                            $uomName = $productOrder->product->uom ?? 'Unit';
                            $remainingUomDisplay = $remainingQty == 1 ? $uomName : \Illuminate\Support\Str::plural($uomName);
                            $orderedUomDisplay = $orderedQty == 1 ? $uomName : \Illuminate\Support\Str::plural($uomName);
                            $receivedUomDisplay = $receivedQty == 1 ? $uomName : \Illuminate\Support\Str::plural($uomName);
                        @endphp
                        <label for="received-qty-{{ $productOrder->id }}" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            @if($isFullyReceived)
                                Received Quantity:
                            @elseif($receivedQty > 0)
                                Additional Received Quantity / [{{ number_format($remainingQty, 0) }} {{ $remainingUomDisplay }} remaining]:
                            @else
                                Received Quantity / [{{ number_format($orderedQty, 0) }} {{ $orderedUomDisplay }} expected]:
                            @endif
                        </label>
                        @if($isFullyReceived)
                            <div class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-600 border border-gray-200 dark:border-gray-500 rounded-lg text-gray-700 dark:text-gray-300">
                                {{ number_format($receivedQty, 0) }} / {{ number_format($orderedQty, 0) }} {{ $orderedUomDisplay }}
                            </div>
                        @else
                            <input 
                                type="number"
                                min="0"
                                step="1"
                                max="{{ $remainingQty }}"
                                id="received-qty-{{ $productOrder->id }}"
                                wire:model.live="receivedQuantities.{{ $productOrder->id }}"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Enter quantity in {{ \Illuminate\Support\Str::plural($uomName) }}">
                            
                            @if($receivedQty > 0)
                                <p class="text-xs text-gray-500 dark:text-gray-400">Previously received: {{ number_format($receivedQty, 0) }} {{ $receivedUomDisplay }}</p>
                            @endif
                        @endif
                    </div>

                    <!-- Item Condition (good condition checkbox) -->
                    <div class="space-y-2">
                        @if($isFullyReceived)
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-200 dark:text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Fully Received
                                </span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">({{ number_format($receivedQty, 0) }} / {{ number_format($orderedQty, 0) }} {{ $uomName }})</span>
                            </div>
                        @else
                            @php
                                $isGoodCondition = !isset($itemStatuses[$productOrder->id]) || $itemStatuses[$productOrder->id] === 'good';
                            @endphp
                            
                            <!-- Good Condition Checkbox -->
                            <label class="flex items-center p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg cursor-pointer hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                                <input type="checkbox" 
                                    x-data="{ checked: {{ $isGoodCondition ? 'true' : 'false' }} }"
                                    x-model="checked"
                                    @change="$wire.set('itemStatuses.{{ $productOrder->id }}', checked ? 'good' : 'destroyed')"
                                    class="w-5 h-5 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <div class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-green-700 dark:text-green-300">✓ All items received in good condition</span>
                                    <p class="text-xs text-green-600 dark:text-green-400 mt-0.5">Uncheck if there are damaged/destroyed items</p>
                                </div>
                            </label>
                            
                            <!-- Destroyed Quantity Input (shown when checkbox is unchecked) -->
                            @if(!$isGoodCondition)
                            <div class="mt-3 p-4 bg-red-50 dark:bg-red-900/20 border-2 border-red-300 dark:border-red-700 rounded-lg">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-red-700 dark:text-red-300">Damaged/Destroyed Items</span>
                                </div>
                                
                                <label for="destroyed-qty-{{ $productOrder->id }}" class="text-sm font-medium text-red-700 dark:text-red-300 mb-2 block">
                                    How many items were damaged or destroyed?
                                </label>
                                <input 
                                    type="number"
                                    min="0"
                                    step="1"
                                    max="{{ $remainingQty }}"
                                    id="destroyed-qty-{{ $productOrder->id }}"
                                    wire:model.live="destroyedQuantities.{{ $productOrder->id }}"
                                    class="w-full px-3 py-2 text-sm border-2 border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-red-600 dark:text-white dark:focus:ring-red-400 dark:focus:border-red-400 font-semibold"
                                    placeholder="Enter destroyed quantity">
                                
                                <p class="text-xs text-red-600 dark:text-red-400 mt-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>These items will NOT be added to inventory and will be marked as destroyed in the report.</span>
                                </p>
                            </div>
                            @endif
                        @endif
                    </div>

                    <!-- Remarks -->
                    <div class="space-y-2 pt-3 border-t border-gray-200 dark:border-zinc-600">
                        <label for="remarks-{{ $productOrder->id }}" class="text-sm font-medium text-gray-700 dark:text-gray-300">Remarks:</label>
                        @if($isFullyReceived)
                            <div class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-600 border border-gray-200 dark:border-gray-500 rounded-lg text-gray-500 dark:text-gray-400">
                                @if($productOrder->receiving_remarks)
                                    {{ $productOrder->receiving_remarks }}
                                @else
                                    No remarks provided
                                @endif
                            </div>
                        @else
                            <textarea 
                                id="remarks-{{ $productOrder->id }}"
                                wire:model="itemRemarks.{{ $productOrder->id }}"
                                rows="2"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-400 dark:focus:border-blue-400"
                                placeholder="Add any comments about this item..."></textarea>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- General Remarks -->
            <div class="mt-8 space-y-3">
                <label for="general-remarks" class="text-base font-medium text-gray-700 dark:text-gray-300">General Remarks:</label>
                <textarea 
                    id="general-remarks"
                    wire:model="generalRemarks"
                    rows="4"
                    class="w-full px-4 py-3 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-400 dark:focus:border-blue-400 resize-none"
                    placeholder="Add any general comments about the delivery..."></textarea>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 mt-6">
                <button 
                    wire:click="goBackToStep1"
                    class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-xl hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 dark:focus:ring-gray-400 dark:focus:border-gray-400 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Scan</span>
                </button>
                <button 
                    wire:click="goToStep3"
                    class="flex-1 px-4 py-3 text-sm font-medium text-white {{ empty(trim($drNumber)) ? 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500 dark:bg-orange-600 dark:hover:bg-orange-700 dark:focus:ring-orange-400' : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-400' }} border border-transparent rounded-xl focus:ring-2 focus:border-transparent transition-colors duration-200 flex items-center justify-center gap-2">
                    @if(empty(trim($drNumber)))
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <span class="truncate">Enter DR # to Continue</span>
                    @else
                        <span>Continue to Report</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    @endif
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Step 3: Report Review -->
    @if($currentStep === 2 && $foundPurchaseOrder)
    <div class="w-full max-w-md mx-auto space-y-4">
        <!-- Report Header -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Stock-In Report</h2>
                <button wire:click="goBackToStep2" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </button>
            </div>
            
            <!-- PO Summary -->
            <div class="bg-blue-100 dark:bg-zinc-800 rounded-xl p-4 mb-6 border border-blue-200 dark:border-zinc-700">
                <h3 class="text-lg font-bold text-blue-900 dark:text-white mb-3">Purchase Order Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="font-bold text-blue-900 dark:text-white">PO Number:</span>
                        <span class="font-bold text-blue-900 dark:text-white">{{ $foundPurchaseOrder->po_num }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-bold text-blue-900 dark:text-white">Supplier:</span>
                        <span class="font-bold text-blue-900 dark:text-white">{{ $foundPurchaseOrder->supplier->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-bold text-blue-900 dark:text-white">Total Items:</span>
                        <span class="font-bold text-blue-900 dark:text-white">{{ $foundPurchaseOrder->productOrders->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-bold text-blue-900 dark:text-white">DR #:</span>
                        <span class="font-bold text-blue-900 dark:text-white">{{ $drNumber ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Items Review Summary -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Items Review Summary</h3>
                
                @foreach($foundPurchaseOrder->productOrders as $productOrder)
                <div class="border border-gray-200 dark:border-zinc-700 rounded-xl p-4 space-y-3 bg-white dark:bg-zinc-800">
                    <!-- Product Name -->
                    <div>
                        <div class="font-bold text-gray-900 dark:text-white text-sm">
                            {{ $productOrder->product->name }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            SKU: {{ $productOrder->product->sku }}
                        </div>
                    </div>

                    <!-- Status with Quantity Details -->
                    <div class="space-y-2">
                        @php
                            // Livewire can send empty strings for blank inputs; normalize to numeric
                            $rawAdditionalReceived = $receivedQuantities[$productOrder->id] ?? 0;
                            $rawAdditionalDestroyed = $destroyedQuantities[$productOrder->id] ?? 0;

                            $additionalReceivedQty = is_numeric($rawAdditionalReceived) ? (float) $rawAdditionalReceived : 0.0;
                            $additionalDestroyedQty = is_numeric($rawAdditionalDestroyed) ? (float) $rawAdditionalDestroyed : 0.0;

                            $currentReceivedQty = (float) ($productOrder->received_quantity ?? 0);
                            $totalReceivedQty = $currentReceivedQty + $additionalReceivedQty;
                            $itemStatus = $itemStatuses[$productOrder->id] ?? 'good';
                            $orderedQty = (float) $productOrder->quantity;
                            $isFullyReceived = $currentReceivedQty >= $orderedQty;
                            $uomName = $productOrder->product->uom ?? 'Units';
                        @endphp
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Status:</span>
                            @if($itemStatus === 'good')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-200 dark:text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Good Condition
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-200 dark:text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Has Damaged Items
                                </span>
                            @endif
                        </div>

                        <!-- Quantity Details -->
                        <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-3 space-y-2">
                            @if($additionalReceivedQty > 0)
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-1 text-green-700 dark:text-green-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-medium">Received:</span>
                                </span>
                                <div class="text-right">
                                    @php
                                        $receivedUom = $additionalReceivedQty == 1 ? $uomName : \Illuminate\Support\Str::plural($uomName);
                                    @endphp
                                    <div class="font-bold text-green-700 dark:text-green-400">
                                        {{ number_format($additionalReceivedQty, 0) }} {{ $receivedUom }}
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($additionalDestroyedQty > 0)
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-1 text-red-700 dark:text-red-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-medium">Destroyed:</span>
                                </span>
                                <div class="text-right">
                                    @php
                                        $destroyedUom = $additionalDestroyedQty == 1 ? $uomName : \Illuminate\Support\Str::plural($uomName);
                                    @endphp
                                    <div class="font-bold text-red-700 dark:text-red-400">
                                        {{ number_format($additionalDestroyedQty, 0) }} {{ $destroyedUom }}
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($additionalReceivedQty == 0 && $additionalDestroyedQty == 0)
                            <div class="text-sm text-gray-500 dark:text-gray-400 text-center italic">
                                No items processed in this session
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Batch Information -->
                    @if(isset($batchNumbers[$productOrder->id]) && !empty($batchNumbers[$productOrder->id]))
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Batch #:</span>
                            <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $batchNumbers[$productOrder->id] }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Remarks -->
                    @if(isset($itemRemarks[$productOrder->id]) && !empty($itemRemarks[$productOrder->id]))
                    <div class="pt-3 border-t border-gray-200 dark:border-zinc-700">
                        <p class="text-sm font-bold text-gray-900 dark:text-white mb-1">Remarks:</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 italic">{{ $itemRemarks[$productOrder->id] }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- General Remarks -->
            @if(!empty($generalRemarks))
            <div class="mt-6 bg-gray-100 dark:bg-zinc-900 rounded-xl p-4 border border-gray-200 dark:border-zinc-700">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">General Remarks:</h4>
                <p class="text-sm text-gray-900 dark:text-white">{{ $generalRemarks }}</p>
            </div>
            @endif

            <!-- Navigation Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 mt-6">
                <button 
                    wire:click="goBackToStep2"
                    class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-xl hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 dark:focus:ring-gray-400 dark:focus:border-gray-400 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Review</span>
                </button>
                <button 
                    wire:click="submitStockInReport"
                    class="flex-1 px-4 py-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Submit Report</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Step 4: Completion -->
    @if($currentStep === 3)
    <div class="w-full max-w-md mx-auto space-y-4">
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-8 border border-gray-200 dark:border-zinc-700 text-center">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900/50 mb-6">
                <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <!-- Success Message -->
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Report Submitted!</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Your stock-in report has been successfully submitted and processed.
            </p>

            <!-- Scanner and Date Info -->
            <div class="mb-6 flex flex-col items-center gap-1">
                <div class="text-base font-bold text-gray-900 dark:text-white">Scanned by:
                    <span class="font-semibold">{{ auth()->user()->name }}</span>
                </div>
                <div class="text-base font-bold text-gray-900 dark:text-white">DR #:
                    <span class="font-semibold">{{ $drNumber ?: 'N/A' }}</span>
                </div>
                <div class="text-base font-bold text-gray-900 dark:text-white">Received Date:
                    <span class="font-semibold">{{ $foundPurchaseOrder->updated_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button 
                    wire:click="goBackToStep1"
                    class="flex-1 px-4 py-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>New Stock-In</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>

<!-- Include html5-qrcode library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    let html5Qr = null;
    let isInitialized = false;
    let camerasList = [];

    // Show status message
    function showStatus(message, type = 'info') {
        const toast = document.getElementById('camera-status-toast');
        const toastContent = document.getElementById('camera-status-toast-content');
        const toastMsg = document.getElementById('camera-status-toast-message');
        if (!toast || !toastContent || !toastMsg) return;
        toastMsg.textContent = message;
        toast.classList.remove('hidden');
        // Set color/icon
        if (type === 'error') {
            toastContent.className = 'flex items-start gap-3 p-4 rounded-xl shadow-xl border bg-red-50 border-red-300 text-red-900 dark:bg-red-900 dark:border-red-700 dark:text-red-100';
            toastContent.querySelector('svg').className = 'w-6 h-6 text-red-500';
        } else if (type === 'success') {
            toastContent.className = 'flex items-start gap-3 p-4 rounded-xl shadow-xl border bg-green-50 border-green-300 text-green-900 dark:bg-green-900 dark:border-green-700 dark:text-green-100';
            toastContent.querySelector('svg').className = 'w-6 h-6 text-green-500';
        } else {
            toastContent.className = 'flex items-start gap-3 p-4 rounded-xl shadow-xl border bg-blue-50 border-blue-300 text-blue-900 dark:bg-blue-900 dark:border-blue-700 dark:text-blue-100';
            toastContent.querySelector('svg').className = 'w-6 h-6 text-blue-500';
        }
        // Auto-dismiss after 4s for info/success, stay for error
        if (type === 'error') return;
        setTimeout(() => { toast.classList.add('hidden'); }, 4000);
    }

    // Direct html5-qrcode implementation
    async function startQRScanner() {
        try {
            // Check if Html5Qrcode is available
            if (typeof Html5Qrcode === 'undefined') {
                console.error('Html5Qrcode library not loaded');
                showStatus('QR Scanner library not loaded. Please check if html5-qrcode is properly included.', 'error');
                return false;
            }

            console.log('Html5Qrcode library found:', typeof Html5Qrcode);
            showStatus('Initializing QR scanner...', 'info');

            const qrRegionId = "qr-reader";
            const html5QrCode = new Html5Qrcode(qrRegionId);
            
            // Get available cameras
            const devices = await Html5Qrcode.getCameras();
            console.log('Available cameras:', devices);
            
            if (devices && devices.length) {
                // Prefer back camera
                let selectedCameraId = devices[0].id;
                for (const device of devices) {
                    if (device.label.toLowerCase().includes('back') || 
                        device.label.toLowerCase().includes('environment')) {
                        selectedCameraId = device.id;
                        break;
                    }
                }

                console.log('Selected camera:', selectedCameraId);

                // Start scanning
                await html5QrCode.start(
                    selectedCameraId,
                    {
                        fps: 10,
                        qrbox: { width: 200, height: 200 }
                    },
                    (decodedText, decodedResult) => {
                        // QR Code detected
                        console.log("QR Code detected:", decodedText);
                        
                        // Update UI
                        document.getElementById("qr-value").textContent = decodedText;
                        document.getElementById("qr-result").classList.remove("hidden");
                        
                        // Play sound if available
                        const scanSound = document.getElementById("scanSound");
                        if (scanSound) {
                            scanSound.currentTime = 0;
                            scanSound.play().catch(err => {
                                console.warn("Sound playback failed:", err);
                            });
                        }

                        // Check Livewire availability
                        console.log('Checking Livewire availability...');
                        console.log('window.Livewire:', window.Livewire);
                        console.log('typeof window.Livewire.emit:', typeof window.Livewire?.emit);
                        console.log('window.$wire:', window.$wire);
                        console.log('document.querySelector("[wire\\:id]")', document.querySelector('[wire\\:id]'));

                        // Try multiple methods to communicate with Livewire
                        let eventEmitted = false;
                        
                        // Method 1: Try window.Livewire.emit
                        if (window.Livewire && typeof window.Livewire.emit === 'function') {
                            console.log("Method 1: Using window.Livewire.emit");
                            window.Livewire.emit('qrScanned', decodedText);
                            eventEmitted = true;
                        }
                        // Method 2: Try window.$wire
                        else if (window.$wire && typeof window.$wire.handleQrScanned === 'function') {
                            console.log("Method 2: Using window.$wire.handleQrScanned");
                            window.$wire.handleQrScanned(decodedText);
                            eventEmitted = true;
                        }
                        // Method 3: Try Livewire.find
                        else if (window.Livewire && typeof window.Livewire.find === 'function') {
                            console.log("Method 3: Using window.Livewire.find");
                            const component = window.Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id'));
                            if (component) {
                                component.call('handleQrScanned', decodedText);
                                eventEmitted = true;
                            }
                        }
                        // Method 4: Try dispatching a custom event
                        else {
                            console.log("Method 4: Dispatching custom event");
                            const event = new CustomEvent('qrScanned', { detail: decodedText });
                            document.dispatchEvent(event);
                            eventEmitted = true;
                        }

                        if (!eventEmitted) {
                            console.error("No Livewire communication method available");
                            console.log("Available window objects:", Object.keys(window).filter(key => key.toLowerCase().includes('livewire')));
                            showStatus('Livewire not available for QR processing', 'error');
                        } else {
                            console.log("Event emitted successfully");
                            showStatus('Processing QR code...', 'info');
                        }

                        // Stop scanner
                        html5QrCode.stop().then(() => {
                            console.log("QR Scanner stopped");
                        }).catch(err => {
                            console.error("Error stopping scanner:", err);
                        });
                    },
                    (errorMessage) => {
                        // Ignore errors during scanning
                        console.log('Scanning error (ignored):', errorMessage);
                    }
                );

                html5Qr = html5QrCode;
                isInitialized = true;
                showStatus('QR Scanner started successfully', 'success');
                return true;

            } else {
                showStatus('No cameras found', 'error');
                return false;
            }

        } catch (err) {
            console.error('QR Scanner error:', err);
            showStatus('Failed to start QR scanner: ' + err.message, 'error');
            return false;
        }
    }

    // Manual trigger function (for testing)
    function triggerQRScanned() {
        const qrValue = "7000"; // Use a real PO number for testing
        document.getElementById("qr-value").textContent = qrValue;
        document.getElementById("qr-result").classList.remove("hidden");
        
        // Emit to Livewire
        if (window.Livewire && typeof window.Livewire.emit === 'function') {
            console.log("Emitting qrScanned event from main context (manual trigger)");
            window.Livewire.emit('qrScanned', qrValue);
        } else {
            console.error("Livewire still not available in main context (manual trigger)");
            showStatus('Livewire not available', 'error');
        }
    }

    // Initialize QR Scanner
    document.addEventListener('DOMContentLoaded', function() {
        // Add custom event listener for QR scanning
        document.addEventListener('qrScanned', function(event) {
            console.log('Custom qrScanned event received:', event.detail);
            // Try to call the Livewire method directly
            if (window.Livewire) {
                window.Livewire.emit('qrScanned', event.detail);
            }
        });

        let html5QrCode = null;
        
        // Show initial status
        showStatus('Initializing QR scanner...', 'info');

        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            // QR Code detected
            console.log("QR Code detected:", decodedText);
            
            // Show success notification
            showStatus(`QR Code detected: ${decodedText}`, 'success');
            
            // Update UI
            document.getElementById("qr-value").textContent = decodedText;
            document.getElementById("qr-result").classList.remove("hidden");
            
            // Play sound if available
            const scanSound = document.getElementById("scanSound");
            if (scanSound) {
                scanSound.currentTime = 0;
                scanSound.play().catch(err => {
                    console.warn("Sound playback failed:", err);
                });
            }

            // Check Livewire availability
            console.log('Checking Livewire availability...');
            console.log('window.Livewire:', window.Livewire);
            console.log('typeof window.Livewire.emit:', typeof window.Livewire?.emit);
            console.log('window.$wire:', window.$wire);
            console.log('document.querySelector("[wire\\:id]")', document.querySelector('[wire\\:id]'));

            // Try multiple methods to communicate with Livewire
            let eventEmitted = false;
            
            // Method 1: Try window.Livewire.emit
            if (window.Livewire && typeof window.Livewire.emit === 'function') {
                console.log("Method 1: Using window.Livewire.emit");
                window.Livewire.emit('qrScanned', decodedText);
                eventEmitted = true;
            }
            // Method 2: Try window.$wire
            else if (window.$wire && typeof window.$wire.handleQrScanned === 'function') {
                console.log("Method 2: Using window.$wire.handleQrScanned");
                window.$wire.handleQrScanned(decodedText);
                eventEmitted = true;
            }
            // Method 3: Try Livewire.find
            else if (window.Livewire && typeof window.Livewire.find === 'function') {
                console.log("Method 3: Using window.Livewire.find");
                const component = window.Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id'));
                if (component) {
                    component.call('handleQrScanned', decodedText);
                    eventEmitted = true;
                }
            }
            // Method 4: Try dispatching a custom event
            else {
                console.log("Method 4: Dispatching custom event");
                const event = new CustomEvent('qrScanned', { detail: decodedText });
                document.dispatchEvent(event);
                eventEmitted = true;
            }

            if (!eventEmitted) {
                console.error("No Livewire communication method available");
                console.log("Available window objects:", Object.keys(window).filter(key => key.toLowerCase().includes('livewire')));
                showStatus('Livewire not available for QR processing', 'error');
            } else {
                console.log("Event emitted successfully");
            }

            // Stop scanner
            showStatus('Stopping QR scanner...', 'info');
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    console.log("QR Scanner stopped");
                    showStatus('QR Scanner stopped', 'info');
                }).catch(err => {
                    console.error("Error stopping scanner:", err);
                    showStatus('Error stopping scanner: ' + err.message, 'error');
                });
            }
        };

        const qrCodeErrorCallback = (errorMessage) => {
            // Ignore errors during scanning
            console.log('Scanning error (ignored):', errorMessage);
        };

        // Initialize scanner
        html5QrCode = new Html5Qrcode("qr-reader");

        // Get available cameras
        showStatus('Checking for available cameras...', 'info');
        
        Html5Qrcode.getCameras().then(devices => {
            console.log('Available cameras:', devices);
            if (devices && devices.length) {
                showStatus(`Found ${devices.length} camera(s). Selecting best camera...`, 'info');
                
                // Prefer back camera
                let selectedCameraId = devices[0].id;
                let selectedCameraName = devices[0].label || 'Camera 1';
                
                for (const device of devices) {
                    if (device.label.toLowerCase().includes('back') || 
                        device.label.toLowerCase().includes('environment')) {
                        selectedCameraId = device.id;
                        selectedCameraName = device.label || 'Back Camera';
                        break;
                    }
                }
                
                console.log('Selected camera:', selectedCameraId);
                showStatus(`Starting camera: ${selectedCameraName}`, 'info');
                
                html5QrCode.start(selectedCameraId, { 
                    fps: 10,
                    qrbox: { width: 200, height: 200 }
                }, qrCodeSuccessCallback, qrCodeErrorCallback).then(() => {
                    showStatus('QR Scanner ready! Point camera at QR code', 'success');
                }).catch(err => {
                    console.error("Error starting scanner:", err);
                    if (err.message.includes('Permission')) {
                        showStatus('Camera permission denied. Please allow camera access and refresh the page.', 'error');
                    } else if (err.message.includes('NotFound')) {
                        showStatus('Camera not found or in use by another application.', 'error');
                    } else {
                        showStatus('Failed to start QR scanner: ' + err.message, 'error');
                    }
                });
            } else {
                showStatus('No cameras found on this device. Please check your camera connection.', 'error');
            }
        }).catch(err => {
            console.error("Error getting cameras:", err);
            if (err.message.includes('Permission')) {
                showStatus('Camera permission denied. Please allow camera access and refresh the page.', 'error');
            } else if (err.message.includes('NotAllowed')) {
                showStatus('Camera access blocked. Please check your browser settings and allow camera permissions.', 'error');
            } else {
                showStatus('Failed to access cameras: ' + err.message, 'error');
            }
        });
    });
</script>