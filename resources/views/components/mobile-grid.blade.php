@props([
    'data' => [],
    'columns' => 1,
    'mobileColumns' => 1,
    'gap' => 'gap-4',
    'class' => ''
])

<div class="grid grid-cols-{{ $mobileColumns }} sm:grid-cols-{{ $columns }} {{ $gap }} {{ $class }}">
    @foreach($data as $item)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow duration-200">
            {{ $slot($item) }}
        </div>
    @endforeach
</div>

@if(count($data) === 0)
    <div class="col-span-full text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No items found</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filters.</p>
    </div>
@endif
