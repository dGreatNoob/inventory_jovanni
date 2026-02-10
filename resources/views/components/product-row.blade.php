@props([
    'product',
    'selected' => false,
    'showPrice' => false,
    'wireModel' => 'temporarySelectedProducts',
])

<label
    data-product-row
    class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors border-b border-gray-100 dark:border-gray-700 last:border-b-0 {{ $selected ? 'bg-indigo-50 dark:bg-indigo-900/20' : '' }}"
>
    <input
        type="checkbox"
        wire:model.live="{{ $wireModel }}"
        value="{{ $product->id }}"
        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0 dark:border-gray-600 dark:bg-gray-700"
    />
    <div class="min-w-0 flex-1">
        <div class="text-sm font-medium text-gray-900 dark:text-white">
            {{ $product->product_number ?? '—' }} | {{ $product->name ?? $product->remarks ?? '—' }} - {{ $product->color ? ($product->color->code ?? $product->color->name ?? '—') : '—' }}
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
            {{ $product->sku ?? '—' }} - {{ $product->supplier_code ?? '—' }}
        </div>
    </div>
    @if($showPrice && isset($product->price))
        <div class="text-sm font-medium text-gray-600 dark:text-gray-300 shrink-0">
            ₱{{ number_format($product->price, 2) }}
        </div>
    @endif
</label>
