@props(['class' => ''])

<div x-data="{ show: @entangle($attributes->wire('model')) }"
     x-show="show"
     x-cloak
     class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-transparent"
     style="display: none;">
    <div class="relative w-full {{ $class }}">
        <div class="relative bg-white dark:bg-zinc-900 rounded-lg shadow border border-zinc-200 dark:border-zinc-700">
            <!-- Modal header -->
            @if(isset($title))
                <div class="flex items-start justify-between p-4 border-b border-zinc-200 dark:border-zinc-700 rounded-t">
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-white">
                        {{ $title }}
                    </h3>
                    <button type="button" 
                            @click="show = false"
                            class="text-zinc-400 bg-transparent hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-700 dark:hover:text-white rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center transition-colors">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
            @endif
            
            <!-- Modal body -->
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div> 