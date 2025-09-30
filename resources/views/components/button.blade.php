@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'loading' => false,
    'disabled' => false,
    'href' => null,
])

@php
    $base = 'inline-flex items-center font-medium focus:ring-4 focus:outline-none rounded-lg text-center transition-all duration-200';

    $variants = [
        'primary' => 'text-white bg-gray-800 hover:bg-gray-900 focus:ring-gray-400 dark:bg-gray-700 dark:hover:bg-gray-800 dark:focus:ring-gray-900',
        'secondary' => 'text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700',
        'danger' => 'text-white bg-red-600 hover:bg-red-700 focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-800',
        'success' => 'text-white bg-green-600 hover:bg-green-700 focus:ring-green-300 dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800',
        'warning' => 'text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-300 dark:focus:ring-yellow-500',
    ];

    $sizes = [
        'sm' => 'text-sm px-3 py-2',
        'md' => 'text-sm px-5 py-2.5',
        'lg' => 'text-base px-6 py-3',
    ];

    $isDisabled = $disabled || $loading;
    $finalClasses = "$base {$variants[$variant]} {$sizes[$size]} " . ($isDisabled ? 'opacity-70 cursor-not-allowed' : '');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $finalClasses]) }}>
        @if ($loading)
            <svg aria-hidden="true" role="status" class="inline w-4 h-4 mr-2 text-white animate-spin" viewBox="0 0 100 101" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5A50 50 0 1150 .5a50 50 0 0150 50z" fill="#E5E7EB"/>
                <path d="M93.967 39.04a46 46 0 00-81.93-15.29 46 46 0 0064.262 64.262A46 46 0 0093.967 39.04z" fill="currentColor"/>
            </svg>
        @elseif ($icon)
            <i class="{{ $icon }} mr-2"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" @if ($isDisabled) disabled @endif
        {{ $attributes->merge(['class' => $finalClasses]) }}>
        @if ($loading)
            <svg aria-hidden="true" role="status" class="inline w-4 h-4 mr-2 text-white animate-spin" viewBox="0 0 100 101" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5A50 50 0 1150 .5a50 50 0 0150 50z" fill="#E5E7EB"/>
                <path d="M93.967 39.04a46 46 0 00-81.93-15.29 46 46 0 0064.262 64.262A46 46 0 0093.967 39.04z" fill="currentColor"/>
            </svg>
        @elseif ($icon)
            <i class="{{ $icon }} mr-2"></i>
        @endif
        {{ $slot }}
    </button>
@endif
