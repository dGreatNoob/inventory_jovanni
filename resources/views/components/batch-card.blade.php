@props([
    'title' => 'Batch Title',
    'batchId' => null,
    'open' => false,
])

<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 mb-4 overflow-hidden" 
     x-data="{ open: {{ $open ? 'true' : 'false' }} }">
    <!-- Header -->
    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 cursor-pointer" 
         @click="open = !open">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
            <div class="flex items-center space-x-2">
                <!-- Toggle Icons -->
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div x-show="open" x-transition class="p-6">
        {{ $slot }}
    </div>
</div>