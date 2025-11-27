<div class="pt-4">
    <div class="">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Promo Details</h1>
                        <!-- Status Badge -->
                        @php
                            $now = \Carbon\Carbon::now();
                            $startDate = \Carbon\Carbon::parse($promo->startDate);
                            $endDate = \Carbon\Carbon::parse($promo->endDate);
                        @endphp
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">View promotion information and details</p>
                </div>
                <div class="flex flex-row items-center space-x-3">
                    <flux:button 
                        wire:click="goBack"
                        variant="outline" 
                        class="flex items-center gap-2 whitespace-nowrap min-w-fit"
                    >
                        <svg class="inline w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Back to List</span>
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 mb-6">
            <!-- Promo Header -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-500 text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $name }}</h2>
                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                            @if($code)
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2.5 py-1 rounded-full border border-indigo-200 dark:border-indigo-700">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                    {{ $code }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-full border border-gray-200 dark:border-gray-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                {{ $type }}
                            </span>
                            @if($now->between($startDate, $endDate))
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 dark:text-emerald-200 bg-emerald-100 dark:bg-emerald-900/40 px-2.5 py-1 rounded-full border border-emerald-200 dark:border-emerald-600">
                                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                                    Active
                                </span>
                            @elseif($now->lt($startDate))
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 dark:text-amber-200 bg-amber-100 dark:bg-amber-900/40 px-2.5 py-1 rounded-full border border-amber-200 dark:border-amber-600">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Upcoming
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 dark:text-red-200 bg-red-100 dark:bg-red-900/40 px-2.5 py-1 rounded-full border border-red-200 dark:border-red-600">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    Expired
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promo Details -->
            <div class="p-5 space-y-6">
                <!-- Date Range -->
                <section class="space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-5 bg-green-500 rounded-full"></div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Date Range</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Promotion active period</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="p-3 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg border border-blue-200 dark:border-blue-700">
                            <label class="block text-xs font-medium text-blue-700 dark:text-blue-300 mb-1">Start Date</label>
                            <p class="text-blue-900 dark:text-blue-100 font-semibold text-base">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}</p>
                            <p class="text-blue-600 dark:text-blue-400 text-xs mt-0.5">{{ \Carbon\Carbon::parse($startDate)->format('l') }}</p>
                        </div>
                        
                        <div class="p-3 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg border border-purple-200 dark:border-purple-700">
                            <label class="block text-xs font-medium text-purple-700 dark:text-purple-300 mb-1">End Date</label>
                            <p class="text-purple-900 dark:text-purple-100 font-semibold text-base">{{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
                            <p class="text-purple-600 dark:text-purple-400 text-xs mt-0.5">{{ \Carbon\Carbon::parse($endDate)->format('l') }}</p>
                        </div>
                    </div>
                </section>

                <!-- Batches -->
                <section class="space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-5 bg-indigo-500 rounded-full"></div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Batches</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Applicable batches for this promotion</p>
                        </div>
                    </div>

                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($branch_names as $branchName)
                                <span class="inline-flex items-center bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-1 rounded-full dark:bg-indigo-900/30 dark:text-indigo-300">
                                    <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $branchName }}
                                </span>
                            @empty
                                <span class="text-gray-400 text-xs">No branches selected</span>
                            @endforelse
                        </div>
                    </div>
                </section>

                <!-- Description -->
                @if($description)
                <section class="space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-5 bg-gray-500 rounded-full"></div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Description</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Additional promotion details</p>
                        </div>
                    </div>

                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">{{ $description }}</p>
                    </div>
                </section>
                @endif

                <!-- Additional Information -->
                <section class="space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-5 bg-amber-500 rounded-full"></div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Additional Information</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Promo creation and update details</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Created</label>
                            <p class="text-gray-900 dark:text-white text-sm">{{ $promo->created_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>

                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Last Updated</label>
                            <p class="text-gray-900 dark:text-white text-sm">{{ $promo->updated_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <!-- Connected Products Data Table Section -->
        <section class="mb-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <!-- Search Bar -->
                <div class="flex items-center justify-between p-4 pr-10">
                    <div class="flex space-x-6">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.500ms="productSearch"
                            class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search products...">
                        </div>
                    </div>
                </div>
                <!-- Data Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Product</th>
                                <th class="px-6 py-3">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($connectedProducts as $product)
                                <tr wire:key="product-{{ $product->id }}" 
                                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    
                                    <!-- Product with Image -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                           <!-- Larger Product Image -->
                                        <div class="flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                            @if($product->primary_image)
                                                <img src="{{ asset('storage/photos/' . $product->primary_image) }}" 
                                                    alt="{{ $product->name }}" 
                                                    class="w-12 h-12 object-cover"
                                                    onerror="this.src='{{ asset('images/placeholder.png') }}'; this.onerror=null;">
                                            @else
                                                <div class="w-12 h-12 flex items-center justify-center text-gray-400 dark:text-gray-500">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                            
                                            <!-- Product Details -->
                                            <div class="flex flex-col space-y-1">
                                                <div class="inline-flex items-center space-x-2 text-sm">
                                                    <span class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</span>
                                                    <span class="text-gray-400 dark:text-gray-500">|</span>
                                                    <span class="text-gray-500 dark:text-gray-300">{{ $product->sku ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Action -->
                                    <td class="px-6 py-4 space-x-2">
                                        <flux:modal.trigger name="product-details">
                                            <flux:button 
                                                wire:click="viewProduct({{ $product->id }})"
                                                variant="outline" 
                                                size="sm"
                                                type="button">
                                                View
                                            </flux:button>
                                        </flux:modal.trigger>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No products found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="py-4 px-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Per Page:</label>
                            <select wire:model.live="productsPerPage"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div>
                            {{ $connectedProducts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <flux:modal name="product-details" class="w-[1100px] max-w-full">
            @if($viewingProduct)
                <div class="space-y-6 pr-2">
                    <div class="flex flex-col lg:flex-row gap-6">
                        <!-- Image Gallery Section -->
                        <div class="w-full lg:w-2/5 space-y-4">
                            <!-- Product Header (removed the small picture) -->
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <flux:heading size="lg" class="text-gray-900 dark:text-white leading-snug">
                                        {{ $viewingProduct->name }}
                                    </flux:heading>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        SKU: {{ $viewingProduct->sku }}
                                    </p>
                                </div>
                            </div>

                            <!-- Big Image Display -->
                            <div class="relative h-64 rounded-2xl border border-gray-200 bg-white dark:bg-gray-900 dark:border-gray-700 shadow-sm flex items-center justify-center">
                                @if($viewingProduct->primary_image)
                                    <img src="{{ asset('storage/photos/' . $viewingProduct->primary_image) }}" 
                                        alt="{{ $viewingProduct->name }}" 
                                        class="h-full w-full object-contain"
                                        onerror="this.src='{{ asset('images/placeholder.png') }}'; this.onerror=null;">
                                @else
                                    <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                        <svg class="h-10 w-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-sm">No product image available</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Description -->
                            @if($viewingProduct->remarks)
                                <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                                        </svg>
                                        Description
                                    </h4>
                                    <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ $viewingProduct->remarks }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Details Section (Right Side) -->
                        <div class="w-full lg:flex-1 space-y-6">
                            <!-- Updated Time -->
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Updated {{ optional($viewingProduct->updated_at)->diffForHumans() ?? '—' }}
                            </p>

                            <!-- Pricing Cards -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Selling Price</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">₱{{ number_format($viewingProduct->price, 2) }}</p>
                                </div>
                                <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Cost</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">₱{{ number_format($viewingProduct->cost, 2) }}</p>
                                </div>
                                <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Promo Type</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $type }}</p>
                                </div>
                            </div>

                            <!-- Detailed Information Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Basic Information -->
                                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Basic Information
                                    </h4>
                                    <dl class="space-y-2 text-sm">
                                        <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">SKU</dt><dd class="text-gray-900 dark:text-white text-right">{{ $viewingProduct->sku ?: '—' }}</dd></div>
                                        <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Barcode</dt><dd class="text-gray-900 dark:text-white text-right">{{ $viewingProduct->barcode ?: '—' }}</dd></div>
                                        <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Category</dt><dd class="text-gray-900 dark:text-white text-right">{{ $viewingProduct->category->name ?? 'N/A' }}</dd></div>
                                        <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Supplier</dt><dd class="text-gray-900 dark:text-white text-right">{{ $viewingProduct->supplier->name ?? 'N/A' }}</dd></div>
                                    </dl>
                                </div>

                                <!-- Inventory Information -->
                                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        Inventory
                                    </h4>
                                    <dl class="space-y-2 text-sm">
                                        <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Stock On Hand</dt><dd class="text-gray-900 dark:text-white text-right">{{ number_format($this->getProductStockInBranches($viewingProduct->id)) }} {{ $viewingProduct->uom }}</dd></div>
                                        <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Status</dt><dd>
                                            @php
                                                $stockStatus = $this->getProductStockStatusInBranches($viewingProduct->id);
                                                $colorClasses = [
                                                    'red' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200',
                                                    'yellow' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200',
                                                    'green' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200'
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $colorClasses[$stockStatus['color']] ?? $colorClasses['green'] }}">
                                                {{ $stockStatus['label'] }}
                                            </span>
                                        </dd></div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </flux:modal>
    </div>
</div>