<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Product Image Gallery</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage product images and uploads</p>
            @if (session()->has('message'))
                <div class="mt-3 rounded-md bg-green-50 dark:bg-green-900/20 p-3 text-sm text-green-700 dark:text-green-300">
                    {{ session('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="mt-3 rounded-md bg-red-50 dark:bg-red-900/20 p-3 text-sm text-red-700 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <flux:button 
                wire:click="toggleViewMode" 
                variant="outline"
                class="flex items-center gap-2 whitespace-nowrap min-w-fit"
            >
                @if($viewMode === 'grid')
                    <svg class="inline w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    <span>List View</span>
                @else
                    <svg class="inline w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span>Grid View</span>
                @endif
            </flux:button>

            <flux:modal.trigger name="upload-images">
                @can('image create')
                <flux:button variant="primary" class="whitespace-nowrap min-w-fit flex items-center">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Upload Images</span>
                </flux:button>
                @endcan
            </flux:modal.trigger>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Images</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $stats['total_images'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Products with Images</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $stats['products_with_images'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Products without Images</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $stats['products_without_images'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v18a1 1 0 01-1 1H4a1 1 0 01-1-1V1a1 1 0 011-1h2a1 1 0 011 1v3m0 0h8M7 4h8"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Storage Used</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $stats['total_size_mb'] ?? 0 }} MB</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <!-- Search -->
                <div class="flex-1 max-w-lg">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" 
                               type="text" 
                               placeholder="Search by product name, SKU, or alt text..." 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white dark:bg-gray-700 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm dark:text-white">
                    </div>
                </div>

                <!-- Filter Toggle -->
                <div class="flex items-center space-x-4">
                    <button wire:click="toggleFilters" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600 dark:focus:ring-gray-400">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filters
                    </button>

                    @if(!empty($selectedImages))
                        <flux:modal.trigger name="bulk-actions-image">
                            <flux:button variant="primary">
                                Bulk Actions ({{ count($selectedImages) }})
                            </flux:button>
                        </flux:modal.trigger>
                    @endif
                </div>
            </div>

            <!-- Advanced Filters -->
            @if($showFilters)
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                        <select wire:model.live="categoryFilter"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button wire:click="clearFilters"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600 dark:focus:ring-gray-400">
                            Clear Filters
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Images Display -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 pb-6 sm:p-6 sm:pb-8">
            @if(($viewMode === 'grid' && count($productCards) > 0) || ($viewMode !== 'grid' && count($images) > 0))
                @if($viewMode === 'grid')
                    <!-- Grid View: group by product (one card per product, show primary/first image) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                        @foreach($productCards as $product)
                            @php $cover = $product->images->first(); @endphp
                            <div class="group relative bg-white dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 overflow-hidden">
                                <!-- Selection Checkbox -->
                                <div class="absolute top-2 left-2 z-10">
                                    <input type="checkbox" 
                                           wire:click="toggleImageSelection({{ $cover->id ?? 0 }})"
                                           @if($cover && in_array($cover->id, $selectedImages)) checked @endif
                                           class="h-4 w-4 text-gray-600 focus:ring-gray-500 dark:focus:ring-gray-400 border-gray-300 rounded">
                                </div>

                                <!-- Primary Badge -->
                                @if($cover && $cover->is_primary)
                                    <div class="absolute top-2 right-2 z-10">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                            Primary
                                        </span>
                                    </div>
                                @endif

                                <!-- Image -->
                                <flux:modal.trigger name="image-viewer">
                                    <div class="aspect-square bg-gray-100 dark:bg-gray-600 overflow-hidden cursor-pointer"
                                         wire:click="openProductViewer({{ $product->id }}, {{ $cover->id ?? 'null' }})">
                                        @if($cover && $cover->filename)
                                            <img src="{{ asset('storage/photos/' . $cover->filename) }}" 
                                                 alt="{{ $product->name }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                                                 onerror="this.src='{{ asset('images/placeholder.png') }}'; this.onerror=null;">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">No Image</div>
                                        @endif
                                    </div>
                                </flux:modal.trigger>

                                <!-- Image Info -->
                                <div class="p-3">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $product->name }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $product->sku }}
                                    </p>
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $product->images->count() }} {{ Str::plural('image', $product->images->count()) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- List View: one row per product -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left">
                                        <input type="checkbox" 
                                               wire:click="selectAllCurrentProducts" 
                                               class="h-4 w-4 text-gray-600 focus:ring-gray-500 dark:focus:ring-gray-400 border-gray-300 rounded">
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Image</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Product</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Images</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($productCards as $product)
                                    @php $cover = $product->images->first(); @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($cover)
                                            <input type="checkbox" 
                                                   wire:click="toggleImageSelection({{ $cover->id }})"
                                                   @if(in_array($cover->id, $selectedImages)) checked @endif
                                                   class="h-4 w-4 text-gray-600 focus:ring-gray-500 dark:focus:ring-gray-400 border-gray-300 rounded">
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex-shrink-0 h-16 w-16">
                                                @if($cover && $cover->filename)
                                                    <img class="h-16 w-16 rounded-lg object-cover" 
                                                         src="{{ asset('storage/photos/' . $cover->filename) }}" 
                                                         alt="{{ $product->name }}"
                                                         onerror="this.src='{{ asset('images/placeholder.png') }}'; this.onerror=null;">
                                                @else
                                                    <div class="h-16 w-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">—</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $product->sku }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $product->images->count() }} {{ Str::plural('image', $product->images->count()) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <flux:modal.trigger name="image-viewer">
                                                    <flux:button 
                                                        wire:click="openProductViewer({{ $product->id }}, {{ $cover->id ?? 'null' }})" 
                                                        variant="ghost" 
                                                        size="sm"
                                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                                    >
                                                        View
                                                    </flux:button>
                                                </flux:modal.trigger>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- Pagination (consistent with Product Management / Categories) -->
                @if($productCards->count() > 0)
                    <div class="mt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="bg-white dark:bg-gray-800 px-4 py-4 sm:px-6 sm:py-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
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
                                    {{ $productCards->links('livewire::tailwind', ['scrollTo' => false]) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No images found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by uploading some product images.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modals -->
    @include('livewire.pages.product-management.modals.upload-images')
    @include('livewire.pages.product-management.modals.edit-image')
    @include('livewire.pages.product-management.modals.delete-image')
    @include('livewire.pages.product-management.modals.bulk-actions-image')
    @include('livewire.pages.product-management.modals.image-viewer')
</div>
