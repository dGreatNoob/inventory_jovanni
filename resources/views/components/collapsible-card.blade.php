@props([
    'title' => 'Card Title',
    'open' => true,
    'size' => 'w-full',
])

@php
    $size = match ($size) {
        'sm' => 'w-1/4',
        'md' => 'w-1/2',
        'lg' => 'w-3/4',
        'full' => 'w-full',
        default => 'w-full',
    };
@endphp
<section 
    x-data="{ open: {{ $open ? 'true' : 'false' }} }" 
    {{ $attributes->merge([
        'class' => trim("{$size} mb-5 p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700")
    ]) }}>
    <!-- Header -->
    <div class="flex justify-between items-center cursor-pointer mb-4" @click="open = !open">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>

        <!-- Toggle Icons -->
        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
        </svg>
        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </div>

    <!-- Content Slot -->
    <div x-show="open" x-transition>
        {{ $slot }}
    </div>
</section>
