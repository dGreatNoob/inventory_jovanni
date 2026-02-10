@props([
    'placeholder' => 'Search products...',
])

<div class="relative">
    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>
    <input
        {{ $attributes->merge([
            'type' => 'text',
            'class' => 'block w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20',
        ]) }}
        placeholder="{{ $placeholder }}"
        aria-label="Search products"
    />
</div>
