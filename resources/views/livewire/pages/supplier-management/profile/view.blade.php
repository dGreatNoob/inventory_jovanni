<x-slot:header>Supplier Profile</x-slot:header>
<div class="pt-4">
    <div class="">
        <div class="mb-4">
            <a href="{{ route('supplier.profile') }}"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-md 
                        text-base font-bold tracking-wide 
                        text-gray-700 dark:text-gray-300 
                        bg-gray-50 dark:bg-gray-700
                        hover:bg-gray-100 dark:hover:bg-gray-600 
                        transition-colors duration-200">
                <svg class="w-[28px] h-[28px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4"/>
            </svg>
                Back
            </a>
    </div>

        <section class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap poly6">
                
                <!-- Total Orders -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-[28px] h-[28px] text-blue-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 3v4a1 1 0 0 1-1 1H5m4 8h6m-6-4h6m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Orders</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $totalOrders }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Spent -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-[28px] h-[28px] text-green-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 14a3 3 0 0 1 3-3h4a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-4a3 3 0 0 1-3-3Zm3-1a1 1 0 1 0 0 2h4v-2h-4Z" clip-rule="evenodd"/>
                                    <path fill-rule="evenodd" d="M12.293 3.293a1 1 0 0 1 1.414 0L16.414 6h-2.828l-1.293-1.293a1 1 0 0 1 0-1.414ZM12.414 6 9.707 3.293a1 1 0 0 0-1.414 0L5.586 6h6.828ZM4.586 7l-.056.055A2 2 0 0 0 3 9v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2h-4a5 5 0 0 1 0-10h4a2 2 0 0 0-1.53-1.945L17.414 7H4.586Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Ordered Value</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">₱{{ number_format($totalSpent, 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Products -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-[30px] h-[30px] text-violet-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M9.98189 4.50602c1.24881-.67469 2.78741-.67469 4.03621 0l3.9638 2.14148c.3634.19632.6862.44109.9612.72273l-6.9288 3.60207L5.20654 7.225c.2403-.22108.51215-.41573.81157-.5775l3.96378-2.14148ZM4.16678 8.84364C4.05757 9.18783 4 9.5493 4 9.91844v4.28296c0 1.3494.7693 2.5963 2.01811 3.2709l3.96378 2.1415c.32051.1732.66011.3019 1.00901.3862v-7.4L4.16678 8.84364ZM13.009 20c.3489-.0843.6886-.213 1.0091-.3862l3.9638-2.1415C19.2307 16.7977 20 15.5508 20 14.2014V9.91844c0-.30001-.038-.59496-.1109-.87967L13.009 12.6155V20Z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Products</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $activeProductCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Performance -->
                {{--<div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-[28px] h-[28px] text-{{ $deliveryPerformanceColor }}-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M2.586 4.586A2 2 0 0 1 4 4h8a2 2 0 0 1 2 2h5a1 1 0 0 1 .894.553l2 4c.07.139.106.292.106.447v4a1 1 0 0 1-1 1h-.535a3.5 3.5 0 1 1-6.93 0h-3.07a3.5 3.5 0 1 1-6.93 0H3a1 1 0 0 1-1-1V6a2 2 0 0 1 .586-1.414ZM18.208 15.61a1.497 1.497 0 0 0-2.416 0 1.5 1.5 0 1 0 2.416 0Zm-10 0a1.498 1.498 0 0 0-2.416 0 1.5 1.5 0 1 0 2.416 0Zm5.79-7.612v2.02h5.396l-1-2.02h-4.396ZM9 8.667a1 1 0 1 0-2 0V10a1 1 0 0 0 .293.707l1.5 1.5a1 1 0 0 0 1.414-1.414L9 9.586v-.92Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Delivery Performance</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($deliveryPerformance, 1) }}%</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>--}}
            </div>
        </section>

        <!-- Supplier Profile Information -->
        <section class="mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Company Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Company Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h12M6 4v16M6 4H5m13 0v16m0-16h1m-1 16H6m12 0h1M6 20H5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <div class="flex-1">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Supplier Name</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">{{ $supplier_name ?? 'Not provided' }}</dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                            <div class="flex-1">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Supplier Code</dt>
                                <dd class="text-base font-mono font-medium text-gray-900 dark:text-white">{{ $supplier_code ?? 'Not provided' }}</dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Address</dt>
                                <dd class="text-base text-gray-900 dark:text-white">{{ $supplier_address ?? 'Not provided' }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Contact Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div class="flex-1">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Contact Person</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">{{ $contact_person ?? 'Not provided' }}</dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <div class="flex-1">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Contact Number</dt>
                                <dd class="text-base font-mono font-medium text-gray-900 dark:text-white">{{ $contact_num ?? 'Not provided' }}</dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div class="flex-1">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Email Address</dt>
                                <dd class="text-base text-gray-900 dark:text-white">{{ $email ?? 'Not provided' }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Status
                        </h3>
                    </div>
                    <div class="p-6">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Account Status</dt>
                            <dd>
                                @if($status === 'active')
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-800/50">
                                    <span class="w-2 h-2 rounded-full bg-green-500 dark:bg-green-400"></span>
                                    Active
                                </span>
                                @elseif($status === 'inactive')
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300 border border-red-200 dark:border-red-800/50">
                                    <span class="w-2 h-2 rounded-full bg-red-500 dark:bg-red-400"></span>
                                    Inactive
                                </span>
                                @else
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-800/50">
                                    <span class="w-2 h-2 rounded-full bg-yellow-500 dark:bg-yellow-400"></span>
                                    Pending
                                </span>
                                @endif
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </section>




        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
            <!-- Header with View All -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-white">Recent Purchase Orders</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Latest orders and delivery status</p>
                    </div>
                </div>
                
                <a href="{{ route('pomanagement.purchaseorder') }}" 
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-200">
                    <span>View All</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            <x-collapsible-card title="Recent Purchase Orders" open="false" size="full">
                <div class="overflow-x-auto">
                    @if($purchaseOrders && $purchaseOrders->count() > 0)
                        <table class="w-full text-sm text-left rtl:text-right text-zinc-500 dark:text-zinc-400">
                            <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-700 dark:text-zinc-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">PO Number</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Total Price</th>
                                    <th scope="col" class="px-6 py-3">Order Date</th>
                                    <th scope="col" class="px-6 py-3">
                                        <div class="flex flex-col">
                                            <span>Delivery Date</span>
                                            <span class="text-[10px] font-normal text-zinc-500 dark:text-zinc-400 normal-case">(Actual/Expected)</span>
                                        </div>
                                    </th>
                                    {{-- <th scope="col" class="px-6 py-3">Variance</th>--}}
                                    <th scope="col" class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrders as $purchaseOrder)
                                    @php
                                        // ✅ Calculate variance (Total Delivered vs Expected)
                                        $totalExpected = (float) $purchaseOrder->productOrders->sum(function($order) {
                                            return $order->expected_qty ?? $order->quantity;
                                        });
                                        $totalReceived = (float) $purchaseOrder->productOrders->sum('received_quantity');
                                        $totalDestroyed = (float) ($purchaseOrder->productOrders->sum('destroyed_qty') ?? 0);
                                        
                                        // ✅ Total Delivered = Good + Damaged
                                        $totalDelivered = $totalReceived + $totalDestroyed;
                                        
                                        // ✅ Variance = Total Delivered - Expected
                                        $variance = $totalDelivered - $totalExpected;
                                        $variancePercentage = $totalExpected > 0 ? (($variance / $totalExpected) * 100) : 0;
                                        
                                        // Get latest delivery date
                                        $latestDelivery = $purchaseOrder->deliveries()->latest('delivery_date')->first();
                                        $actualDeliveryDate = $latestDelivery ? $latestDelivery->delivery_date : null;
                                    @endphp
                                    
                                    <tr class="bg-white border-b dark:bg-zinc-800 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                        <!-- PO Number -->
                                        <td class="px-6 py-4 font-medium text-zinc-900 whitespace-nowrap dark:text-white">
                                            <span class="font-mono text-sm font-semibold text-blue-600 dark:text-blue-400">
                                                {{ $purchaseOrder->po_num }}
                                            </span>
                                        </td>
                                        
                                        <!-- Status -->
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                @if($purchaseOrder->status->value === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                @elseif($purchaseOrder->status->value === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                @elseif($purchaseOrder->status->value === 'to_receive') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300
                                                @elseif($purchaseOrder->status->value === 'received') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                                @elseif($purchaseOrder->status->value === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                @else bg-zinc-100 text-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $purchaseOrder->status->value)) }}
                                            </span>
                                        </td>
                                        
                                        <!-- Total Price -->
                                        <td class="px-6 py-4">
                                            <span class="font-medium text-zinc-900 dark:text-white">
                                                ₱{{ number_format($purchaseOrder->total_price ?? 0, 2) }}
                                            </span>
                                        </td>
                                        
                                        <!-- Order Date -->
                                        <td class="px-6 py-4">
                                            <span class="text-zinc-600 dark:text-zinc-400">
                                                {{ $purchaseOrder->order_date ? $purchaseOrder->order_date->format('M d, Y') : 'N/A' }}
                                            </span>
                                        </td>
                                        
                                        <!-- Delivery Date (Actual/Expected) -->
                                        <td class="px-6 py-4">
                                            @php
                                                // ✅ Check if PO is fully delivered (total delivered >= expected for ALL items)
                                                $isFullyDelivered = true;
                                                foreach ($purchaseOrder->productOrders as $order) {
                                                    $expectedQty = $order->expected_qty ?? $order->quantity;
                                                    $receivedQty = $order->received_quantity ?? 0;
                                                    $destroyedQty = $order->destroyed_qty ?? 0;
                                                    $totalDeliveredQty = $receivedQty + $destroyedQty;
                                                    
                                                    if ($totalDeliveredQty < $expectedQty) {
                                                        $isFullyDelivered = false;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            
                                            @if($actualDeliveryDate)
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-medium text-zinc-900 dark:text-white">
                                                        {{ \Carbon\Carbon::parse($actualDeliveryDate)->format('M d, Y') }}
                                                    </span>
                                                    @if($isFullyDelivered)
                                                        <span class="text-xs text-green-600 dark:text-green-400">
                                                            ✓ Delivered
                                                        </span>
                                                    @else
                                                        <span class="text-xs text-blue-600 dark:text-blue-400">
                                                            Partial Delivery
                                                        </span>
                                                    @endif
                                                </div>
                                            @elseif($purchaseOrder->expected_delivery_date)
                                                <div class="flex flex-col">
                                                    <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                                        {{ $purchaseOrder->expected_delivery_date->format('M d, Y') }}
                                                    </span>
                                                    <span class="text-xs text-zinc-400 dark:text-zinc-500">
                                                        Expected
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-sm text-zinc-400 dark:text-zinc-500">N/A</span>
                                            @endif
                                        </td>
                                        
                                        <!-- Variance (Total Delivered vs Expected) -->
                                        {{-- <td class="px-6 py-4">
                                            @if($totalExpected > 0)
                                                <div class="flex flex-col items-start">
                                                    <span class="text-sm font-semibold {{ $variance < 0 ? 'text-red-600 dark:text-red-400' : ($variance > 0 ? 'text-green-600 dark:text-green-400' : 'text-zinc-500 dark:text-zinc-400') }}">
                                                        {{ $variance > 0 ? '+' : '' }}{{ number_format($variance, 0) }} units
                                                    </span>
                                                    <span class="text-xs font-medium {{ $variance < 0 ? 'text-red-500 dark:text-red-400' : ($variance > 0 ? 'text-green-500 dark:text-green-400' : 'text-zinc-400') }}">
                                                        ({{ $variance > 0 ? '+' : '' }}{{ number_format($variancePercentage, 1) }}%)
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-xs text-zinc-400 dark:text-zinc-500">No items</span>
                                            @endif
                                        </td>--}}
                                        
                                        <!-- Action -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-1">
                                                <!-- View Button -->
                                                <a href="{{ route('pomanagement.purchaseorder.show', ['Id' => $purchaseOrder->id]) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 text-zinc-600 hover:text-blue-600 hover:bg-blue-50 dark:text-zinc-400 dark:hover:text-blue-400 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-200"
                                                title="View Purchase Order">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                
                                                <!-- Start Receiving Button (if TO_RECEIVE status) -->
                                                @if($purchaseOrder->status->value === 'to_receive')
                                                    <a href="{{ route('warehousestaff.stockin') }}?po={{ $purchaseOrder->po_num }}"
                                                    target="_blank"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-zinc-600 hover:text-emerald-600 hover:bg-emerald-50 dark:text-zinc-400 dark:hover:text-emerald-400 dark:hover:bg-emerald-900/20 rounded-lg transition-all duration-200"
                                                    title="Start Receiving Items">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 9l6-3"></path>
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-6 text-center text-zinc-500 dark:text-zinc-400">
                            <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2">No purchase orders found.</p>
                        </div>
                    @endif
                </div>
            </x-collapsible-card>
        </div>
        @if (session()->has('message'))
            <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('message') }}
            </div>
        @endif

        <!-- DataTables Section -->
            <x-collapsible-card title="Supplier Products" open="false" size="full">
                <section class="mb-6">
                            <!-- Data Table -->
                            <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Product</th>
                                        <th scope="col" class="px-6 py-3">SKU</th>
                                        <th scope="col" class="px-6 py-3">Price</th>
                                        {{-- <th scope="col" class="px-6 py-3">Min Order</th>
                                        <th scope="col" class="px-6 py-3">Lead time</th>--}}
                                        {{--<th scope="col" class="px-6 py-3">Status</th>--}}
                                        <th scope="col" class="px-6 py-3">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($items as $item)
                                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item->name }}</td>
                                            <td class="px-6 py-4">{{ $item->sku }}</td>
                                            <td class="px-6 py-4">₱ {{ number_format($item->price, 2) }}</td>
                                            {{--<td class="px-6 py-4">{{ $item->min_order_quantity }}</td>
                                            <td class="px-6 py-4">{{ $item->lead_time }} days</td> --}}
                                            {{--  <td class="px-6 py-4">
                                                @if(!$item->disabled)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[12px] font-semibold bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-300">
                                                        <span class="w-2 h-2 rounded-full bg-green-500 dark:bg-green-400"></span>
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[12px] font-semibold bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-300">
                                                        <span class="w-2 h-2 rounded-full bg-red-500 dark:bg-red-400"></span>
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>--}}
                                            <td class="px-6 py-4">
                                                <flux:button wire:click.prevent="edit({{ $item->id }})" variant="outline" size="sm">Edit</flux:button>
                                                <flux:button wire:click.prevent="confirmDelete({{ $item->id }})" variant="outline" size="sm">Delete</flux:button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                            <!-- Pagination -->
                            <div class="py-4 px-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <label class="text-sm font-medium text-gray-900 dark:text-white">Per Page:</label>
                                        <select wire:model.live="perPage"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                    <div>
                                        {{ $items->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Action Button -->
                <section>
                    <div x-data="{ show: @entangle('showEditModal').live }" x-show="show" x-cloak
                        class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                        <div class="relative w-full max-w-2xl max-h-full">
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Product Status</h3>
                                    <button type="button" wire:click="cancel"
                                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>

                                <div class="p-6">
                                    <div>
                                        <label for="edit_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                                        <select wire:model="edit_status" id="edit_status" 
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                                                focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                                dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                                dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                            <option value="">Select status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                        @error('edit_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                                    <flux:button wire:click="update">
                                        Save changes
                                    </flux:button>
                                    <flux:button wire:click="cancel" variant="outline">
                                        Cancel
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($showDeleteModal)
                        <div class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                            <div class="relative w-full max-w-md max-h-full">
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                    <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" wire:click="cancel">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                    <div class="p-6 text-center">
                                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                        </svg>
                                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this product?</h3>
                                        <flux:button wire:click="delete" class="mr-2 bg-red-600 hover:bg-red-700 text-white">
                                            Yes, I'm sure
                                        </flux:button>
                                        <flux:button wire:click="cancel" variant="outline">
                                            No, cancel
                                        </flux:button>
                                    </div>
                                </div>
                            </div>
                    @endif
                </section>
        </x-collapsible-card>
    </div>
</div>
</div>