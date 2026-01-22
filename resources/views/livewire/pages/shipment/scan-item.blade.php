

<div class="min-h-[70vh] flex flex-col justify-center items-center py-4">
    @php
        $steps = [
            ['label' => 'Select'],
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

    <!-- Step 1: Select Shipment -->
    @if($currentStep === 0)
    <div class="w-full max-w-md mx-auto space-y-6">
        <!-- Shipment Selection Section -->
        <div class="bg-zinc-50 dark:bg-zinc-900 rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 text-center">Select Approved Shipment</h3>

            <div class="space-y-4">
                <div>
                    <label for="shipment-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Choose from Available Shipments
                    </label>
                    <select
                        id="shipment-select"
                        wire:model.live="selectedShipmentId"
                        class="w-full px-4 py-3 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-400 dark:focus:border-blue-400">
                        <option value="">Select a shipment...</option>
                        @foreach($availableShipments as $shipment)
                            <option value="{{ $shipment->id }}">
                                {{ $shipment->shipping_plan_num }} - {{ $shipment->branchAllocation->branch->name ?? 'Branch' }} ({{ \Carbon\Carbon::parse($shipment->scheduled_ship_date)->format('M d, Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Manual Input Section -->
        <div class="bg-zinc-50 dark:bg-zinc-900 rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 text-center">Or Enter Manually</h3>

            <div class="space-y-4">
                <div>
                    <label for="manual-shipment-ref" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Shipment Reference Number
                    </label>
                    <input
                        type="text"
                        id="manual-shipment-ref"
                        wire:model.live.debounce.300ms="manualShipmentRef"
                        class="w-full px-4 py-3 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-400 dark:focus:border-blue-400"
                        placeholder="Enter shipment reference number..."
                    />
                </div>

                <button
                    wire:click="selectShipment"
                    wire:loading.attr="disabled"
                    class="w-full px-6 py-3 text-base font-medium text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span wire:loading.remove>Select Shipment</span>
                    <svg class="animate-spin h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" wire:loading>
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    

    <!-- Step 2: Items Review -->
        @if($currentStep === 1 && $foundShipment)
    <div class="w-full max-w-md mx-auto space-y-4">
        
        <!-- Purchase Order Header -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Shipment Details</h2>
                <button wire:click="goBackToStep1" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Shipping Plan Number:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $foundShipment->shipping_plan_num }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Shipment Created Date:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $foundShipment->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Items:</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $foundShipment->branchAllocation ? $foundShipment->branchAllocation->items->count() : 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Items Review Section -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Review Items</h3>
            
            <div class="space-y-4">
                @if($foundShipment->branchAllocation)
                @foreach($foundShipment->branchAllocation->items->where('box_id', null) as $item)
                <div class="border border-gray-200 dark:border-zinc-600 rounded-xl p-4 space-y-3">
                    <!-- Item Header -->
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $item->product->supply_description }}</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400">SKU: {{ $item->product->sku }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Product Name: {{ $item->product->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->quantity }} {{ $item->product->supply_uom }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Allocated</p>
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
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p>No order items found for this shipment.</p>
                    </div>
                @endif
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
    @if($currentStep === 2 && $foundShipment)
    <div class="w-full max-w-md mx-auto space-y-4">
        <!-- Report Header -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Shipment Report</h2>
                <button wire:click="goBackToStep2" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </button>
            </div>
            
            <!-- PO Summary -->
            <div class="bg-blue-100 dark:bg-zinc-800 rounded-xl p-4 mb-6 border border-blue-200 dark:border-zinc-700">
                <h3 class="text-lg font-bold text-blue-900 dark:text-white mb-3">Shipping Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="font-bold text-blue-900 dark:text-white">Shipping Plan Number:</span>
                        <span class="font-bold text-blue-900 dark:text-white">{{ $foundShipment->shipping_plan_num }}</span>
                    </div>                  
                    <div class="flex justify-between">
                        <span class="font-bold text-blue-900 dark:text-white">Total Items:</span>
                        <span class="font-bold text-blue-900 dark:text-white">{{ $foundShipment->branchAllocation ? $foundShipment->branchAllocation->items->count() : 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Items Review Summary -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Items Review Summary</h3>

                @if($foundShipment->branchAllocation)
                @foreach($foundShipment->branchAllocation->items->where('box_id', null) as $item)
                <div class="border border-gray-200 dark:border-zinc-700 rounded-xl p-4 space-y-3 bg-white dark:bg-zinc-800">
                    <!-- Item Header -->
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ $item->product->supply_description }}</h4>
                            <p class="text-xs text-gray-700 dark:text-gray-200">SKU: {{ $item->product->sku }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Product Name: {{ $item->product->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->quantity }} {{ $item->product->supply_uom }}</p>
                            <p class="text-xs text-gray-700 dark:text-gray-200">Allocated</p>
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
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p>No order items found for this shipment.</p>
                    </div>
                @endif
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
                Your shipment report has been successfully submitted and processed.
            </p>

            <!-- Scanner and Date Info -->
            <div class="mb-6 flex flex-col items-center gap-1">
                <div class="text-base font-bold text-gray-900 dark:text-white">Scanned by:
                    <span class="font-semibold">{{ auth()->user()->name }}</span>
                </div>
                <div class="text-base font-bold text-gray-900 dark:text-white">Received Date:
                    <span class="font-semibold">{{ $foundShipment->updated_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-center">
                <button 
                    wire:click="goBackToStep1"
                    class="flex-1 sm:flex-none px-6 py-4 text-base font-medium text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Select New Shipment</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>

