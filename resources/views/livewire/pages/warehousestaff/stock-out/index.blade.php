

<div class="min-h-[70vh] flex flex-col justify-center items-center py-4">
    @php
        $steps = [
            ['label' => 'Scan QR'],
            ['label' => 'Review'],
            ['label' => 'Submit'],
            ['label' => 'Finish'],
        ];
    @endphp
    <!-- Stepper Bar -->
    <div class="w-full max-w-xs mx-auto mb-4 sticky top-0">
        <div class="flex items-center justify-between bg-zinc-50 dark:bg-zinc-900 rounded-2xl shadow-2xl p-4 border-b border-gray-200 dark:border-zinc-700 z-20">
            @foreach($steps as $i => $step)
                <div class="flex-1 flex flex-col items-center relative">
                    <!-- Step Circle (uniform size, color as indicator) -->
                    @if($currentStep > $i)
                        <div class="w-9 h-9 flex items-center justify-center rounded-full bg-emerald-500 text-white text-base shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </div>
                    @elseif($currentStep === $i)
                        <div class="w-9 h-9 flex items-center justify-center rounded-full bg-blue-600 text-white text-base shadow-md">
                            <span class="font-bold">{{ $i + 1 }}</span>
                        </div>
                    @else
                        <div class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-300 dark:bg-zinc-700 text-gray-500 dark:text-gray-300 text-base shadow-md">
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
                    <div class="mt-2 text-center">
                        <div class="text-sm font-bold {{ $currentStep === $i ? 'text-blue-900 dark:text-blue-100' : 'text-gray-700 dark:text-gray-200' }}">
                            {{ $step['label'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Error Status Bar -->
    @if($messageType === 'error' && $message)
    <div class="w-full max-w-md mx-auto mb-2">
        <div class="rounded-lg p-3 mb-2 text-sm font-mono shadow border bg-red-100 text-red-800 border-red-300 dark:bg-red-900 dark:text-red-100 dark:border-red-700">
            <div><strong>Error:</strong> {{ $message }}</div>
        </div>
    </div>
    @endif

    <!-- Step 1: Scan QR -->
    @if($currentStep === 0)
        
    <!-- Camera Status Toast -->
    <div id="camera-status-toast" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-xs px-4 hidden">
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
    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-2xl shadow-lg p-4 flex flex-col items-center w-full max-w-xs mx-auto space-y-4">
        <div id="qr-reader"
            class="w-full aspect-square bg-gray-100 dark:bg-zinc-700 border-2 border-dashed border-gray-300 dark:border-zinc-600 rounded-2xl overflow-hidden flex items-center justify-center relative">
            <div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V6a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1zm12 0h2a1 1 0 001-1V6a1 1 0 00-1-1h-2a1 1 0 00-1 1v1a1 1 0 001 1zM5 20h2a1 1 0 001-1v-1a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1z"></path>
                    </svg>
                    <p class="text-base">Camera will appear here</p>
                </div>
            </div>
            <div id="qr-loading" class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-zinc-900/80 z-10 hidden">
                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
            </div>
        </div>
        <div id="qr-result" class="hidden w-full mt-2 p-3 rounded-lg bg-green-50 border border-green-400 text-green-700 text-center font-semibold flex flex-col items-center justify-center">
            <span class="text-2xl mb-1">✅</span>
            <span id="qr-value" class="font-mono text-base break-words"></span>
        </div>
    </div>
    @endif

    

    <!-- Step 2: Items Review -->
        @if($currentStep === 1 && $foundSalesOrder)
    <div class="w-full max-w-md mx-auto space-y-4" wire:init="ensureCorrectPO">
        
        <!-- Purchase Order Header -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Sales Order Details</h2>
                <button wire:click="goBackToStep1" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">SO Number:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $foundSalesOrder->sales_order_number }}</span>
                </div>
                {{-- <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Supplier:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $foundSalesOrder->supplier->name ?? 'N/A' }}</span>
                </div> --}}
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Order Date:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $foundSalesOrder->created_at->format('M d, Y') }}</span>
                </div>
                {{-- <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Delivery To:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $foundSalesOrder->department->name ?? 'N/A' }}</span>
                </div> --}}
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Items:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $foundSalesOrder->items->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Items Review Section -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Review Items</h3>
            
            <div class="space-y-4">
                @foreach($foundSalesOrder->items as $item)
                <div class="border border-gray-200 dark:border-zinc-600 rounded-xl p-4 space-y-3">
                    <!-- Item Header -->
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $item->product->supply_description }}</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400">SKU: {{ $item->product->supply_sku }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->order_qty }} {{ $item->product->supply_uom }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Ordered</p>
                        </div>
                    </div>

                    <!-- Status Selection -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Item Status:</label>
                        <div class="flex space-x-2">
                            <label class="flex items-center">
                                <input type="radio" 
                                       wire:model="itemStatuses.{{ $item->id }}" 
                                       value="good" 
                                       class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Good</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" 
                                       wire:model="itemStatuses.{{ $item->id }}" 
                                       value="incomplete" 
                                       class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 focus:ring-yellow-500 dark:focus:ring-yellow-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Incomplete</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" 
                                       wire:model="itemStatuses.{{ $item->id }}" 
                                       value="destroyed" 
                                       class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Destroyed</span>
                            </label>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="space-y-2">
                        <label for="remarks-{{ $item->id }}" class="text-xs font-medium text-gray-700 dark:text-gray-300">Remarks:</label>
                        <textarea 
                            id="remarks-{{ $item->id }}"
                            wire:model="itemRemarks.{{ $item->id }}"
                            rows="2"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-400 dark:focus:border-blue-400"
                            placeholder="Add any comments about this item..."></textarea>
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
            <div class="flex flex-col sm:flex-row gap-3 mt-8">
                <button 
                    wire:click="goBackToStep1"
                    class="flex-1 sm:flex-none mt-4 px-6 py-4 text-base font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-xl hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 dark:focus:ring-gray-400 dark:focus:border-gray-400 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Scan</span>
                </button>
                <button 
                    wire:click="goToStep3"
                    class="flex-1 sm:flex-none px-6 py-4 text-base font-medium text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors duration-200 flex items-center justify-center gap-2">
                    <span>Continue to Report</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Step 3: Report Review -->
    @if($currentStep === 2 && $foundSalesOrder)
    <div class="w-full max-w-md mx-auto space-y-4">
        <!-- Report Header -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Stock-Out Report</h2>
                <button wire:click="goBackToStep2" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </button>
            </div>
            
            <!-- PO Summary -->
            <div class="bg-blue-100 dark:bg-zinc-800 rounded-xl p-4 mb-6 border border-blue-200 dark:border-zinc-700">
                <h3 class="text-lg font-bold text-blue-900 dark:text-white mb-3">Sales Order Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="font-bold text-blue-900 dark:text-white">SO Number:</span>
                        <span class="font-bold text-blue-900 dark:text-white">{{ $foundSalesOrder->sales_order_number }}</span>
                    </div>
                    {{-- <div class="flex justify-between">
                        <span class="font-bold text-blue-900 dark:text-white">Supplier:</span>
                        <span class="font-bold text-blue-900 dark:text-white">{{ $foundSalesOrder->supplier->name ?? 'N/A' }}</span>
                    </div> --}}
                    <div class="flex justify-between">
                        <span class="font-bold text-blue-900 dark:text-white">Total Items:</span>
                        <span class="font-bold text-blue-900 dark:text-white">{{ $foundSalesOrder->items->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Items Review Summary -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Items Review Summary</h3>
                
                @foreach($foundSalesOrder->items as $item)
                <div class="border border-gray-200 dark:border-zinc-700 rounded-xl p-4 space-y-3 bg-white dark:bg-zinc-800">
                    <!-- Item Header -->
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ $item->product->supply_description }}</h4>
                            <p class="text-xs text-gray-700 dark:text-gray-200">SKU: {{ $item->product->supply_sku }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->order_qty }} {{ $item->product->supply_uom }}</p>
                            <p class="text-xs text-gray-700 dark:text-gray-200">Ordered</p>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-900 dark:text-white">Status:</span>
                        @if(isset($itemStatuses[$item->id]))
                            @if($itemStatuses[$item->id] === 'good')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-600 text-white dark:bg-green-400 dark:text-green-900 shadow">
                                    <svg class="w-3 h-3 mr-1 text-white dark:text-green-900" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Good
                                </span>
                            @elseif($itemStatuses[$item->id] === 'incomplete')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-500 text-yellow-900 dark:bg-yellow-300 dark:text-yellow-900 shadow">
                                    <svg class="w-3 h-3 mr-1 text-yellow-900" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Incomplete
                                </span>
                            @elseif($itemStatuses[$item->id] === 'destroyed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-600 text-white dark:bg-red-400 dark:text-red-900 shadow">
                                    <svg class="w-3 h-3 mr-1 text-white dark:text-red-900" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Destroyed
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-600 text-white dark:bg-gray-400 dark:text-gray-900 shadow">
                                Not Set
                            </span>
                        @endif
                    </div>

                    <!-- Remarks -->
                    @if(isset($itemRemarks[$item->id]) && !empty($itemRemarks[$item->id]))
                    <div class="bg-gray-100 dark:bg-zinc-900 rounded-lg p-3 border border-gray-200 dark:border-zinc-700">
                        <p class="text-xs text-gray-900 dark:text-white mb-1">Remarks:</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $itemRemarks[$item->id] }}</p>
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
            <div class="flex flex-col sm:flex-row gap-3 mt-8">
                <button 
                    wire:click="goBackToStep2"
                    class="flex-1 sm:flex-none mt-6 px-6 py-4 text-base font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-xl hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 dark:focus:ring-gray-400 dark:focus:border-gray-400 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Review</span>
                </button>
                <button 
                    wire:click="submitStockInReport"
                    class="flex-1 sm:flex-none px-6 py-4 text-base font-medium text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                Your stock-out report has been successfully submitted and processed.
            </p>

            <!-- Scanner and Date Info -->
            <div class="mb-6 flex flex-col items-center gap-1">
                <div class="text-base font-bold text-gray-900 dark:text-white">Scanned by:
                    <span class="font-semibold">{{ auth()->user()->name }}</span>
                </div>
                <div class="text-base font-bold text-gray-900 dark:text-white">Received Date:
                    <span class="font-semibold">{{ $foundSalesOrder->updated_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button 
                    wire:click="goBackToStep1"
                    class="flex-1 sm:flex-none px-6 py-4 text-base font-medium text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>New Stock-Out</span>
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
                        qrbox: { width: 250, height: 250 }
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
                    qrbox: { width: 250, height: 250 }
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
