@props([
    'barcode',
    'product' => null,
    'size' => 'md', // sm, md, lg
    'showLabel' => true,
    'showText' => true
])

@php
    use Illuminate\Support\Str;

    $sizes = [
        'sm' => ['width' => 1, 'height' => 30],
        'md' => ['width' => 2, 'height' => 50],
        'lg' => ['width' => 3, 'height' => 70]
    ];
    
    $sizeConfig = $sizes[$size] ?? $sizes['md'];
    
    $barcodeService = app(\App\Services\BarcodeService::class);
    $barcodeImage = $barcodeService->generateBarcodePNG($barcode, $sizeConfig['width'], $sizeConfig['height']);

    $isSale = false;
    if ($product) {
        $priceNote = Str::upper((string) data_get($product, 'price_note', ''));
        $isSale = Str::startsWith($priceNote, 'SAL') || data_get($product, 'product_type') === 'sale';
    }
@endphp

<div class="barcode-display flex flex-col items-center space-y-1">
    @if($showLabel && $product)
        <div class="text-xs text-gray-600 dark:text-gray-400 font-medium">
            {{ $product->name }}
        </div>
    @endif
    
    <div @class([
            'p-2 rounded border transition-colors duration-150',
            'bg-white border-gray-200 dark:border-gray-600' => !$isSale,
            'bg-red-600 border-red-600 shadow-lg' => $isSale,
        ])
    >
        @if($barcodeImage)
            <img src="{{ $barcodeImage }}" 
                 alt="Barcode: {{ $barcode }}"
                 class="max-w-full h-auto {{ $isSale ? 'bg-white p-1 rounded' : '' }}"
                 style="image-rendering: pixelated;">
        @else
            <div class="text-xs text-red-500">
                Unable to generate barcode
            </div>
        @endif
    </div>
    
    @if($showText)
        <div class="text-xs font-mono tracking-wider {{ $isSale ? 'text-red-700 dark:text-red-300' : 'text-gray-700 dark:text-gray-300' }}">
            {{ $barcode }}
        </div>
    @endif
</div>

