@props([
    'products' => collect(),
    'selectedIds' => [],
    'emptySearchMessage' => 'Type to search products',
    'noResultsMessage' => 'No products match your search',
    'hasSearchQuery' => false,
    'loadingTarget' => 'addProductsModalSearch',
    'showPrice' => false,
])

<div
    class="h-[400px] min-h-[400px] flex-1 min-h-0 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-600 scroll-smooth relative"
    wire:loading.class="opacity-60 pointer-events-none"
    wire:target="{{ $loadingTarget }}"
>
    {{-- Loading skeleton --}}
    <div wire:loading wire:target="{{ $loadingTarget }}" class="absolute inset-0 z-10 flex flex-col p-2 bg-white dark:bg-gray-800 rounded-lg" aria-hidden="true">
        @foreach(range(1, 5) as $i)
            <div class="flex items-center gap-3 px-4 py-3 animate-pulse">
                <div class="h-4 w-4 rounded bg-gray-200 dark:bg-gray-600"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 w-3/4 rounded bg-gray-200 dark:bg-gray-600"></div>
                    <div class="h-3 w-1/2 rounded bg-gray-100 dark:bg-gray-700"></div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Product rows --}}
    <div class="relative" role="listbox" aria-label="Product list">
        @forelse($products as $product)
            <x-product-row
                wire:key="product-row-{{ $product->id }}"
                :product="$product"
                :selected="in_array($product->id, $selectedIds)"
                :show-price="$showPrice"
            />
        @empty
            <div class="flex flex-col items-center justify-center h-full px-4 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ $hasSearchQuery ? $noResultsMessage : $emptySearchMessage }}
                </p>
            </div>
        @endforelse
    </div>
</div>
