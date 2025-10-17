@props([
    'barcode',
    'product' => null,
    'size' => 'md', // sm, md, lg
    'showLabel' => true,
    'showText' => true
])

@php
    $sizes = [
        'sm' => ['width' => 1, 'height' => 30],
        'md' => ['width' => 2, 'height' => 50],
        'lg' => ['width' => 3, 'height' => 70]
    ];
    
    $sizeConfig = $sizes[$size] ?? $sizes['md'];
    
    $barcodeService = app(\App\Services\BarcodeService::class);
    $barcodeImage = $barcodeService->generateBarcodePNG($barcode, $sizeConfig['width'], $sizeConfig['height']);
@endphp

<div class="barcode-display flex flex-col items-center space-y-1">
    @if($showLabel && $product)
        <div class="text-xs text-gray-600 dark:text-gray-400 font-medium">
            {{ $product->name }}
        </div>
    @endif
    
    <div class="bg-white p-2 rounded border border-gray-200 dark:border-gray-600">
        @if($barcodeImage)
            <img src="{{ $barcodeImage }}" 
                 alt="Barcode: {{ $barcode }}"
                 class="max-w-full h-auto"
                 style="image-rendering: pixelated;">
        @else
            <div class="text-xs text-red-500">
                Unable to generate barcode
            </div>
        @endif
    </div>
    
    @if($showText)
        <div class="text-xs font-mono text-gray-700 dark:text-gray-300 tracking-wider">
            {{ $barcode }}
        </div>
    @endif
</div>

