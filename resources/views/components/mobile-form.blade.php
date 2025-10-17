@props([
    'method' => 'POST',
    'action' => '',
    'class' => '',
    'submitText' => 'Save',
    'cancelText' => 'Cancel',
    'showCancel' => true,
    'submitColor' => 'blue',
    'cancelColor' => 'gray'
])

<form method="{{ $method }}" action="{{ $action }}" class="space-y-6 {{ $class }}">
    @csrf
    @if($method !== 'GET' && $method !== 'POST')
        @method($method)
    @endif

    <div class="space-y-4">
        {{ $slot }}
    </div>

    <!-- Mobile Form Actions -->
    <div class="lg:hidden">
        <div class="flex flex-col space-y-3">
            <button type="submit" 
                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-{{ $submitColor }}-600 hover:bg-{{ $submitColor }}-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $submitColor }}-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ $submitText }}
            </button>
            
            @if($showCancel)
                <button type="button" 
                        onclick="history.back()"
                        class="w-full inline-flex justify-center items-center px-4 py-3 border border-{{ $cancelColor }}-300 text-base font-medium rounded-md text-{{ $cancelColor }}-700 bg-white hover:bg-{{ $cancelColor }}-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $cancelColor }}-500 dark:bg-gray-700 dark:border-gray-600 dark:text-{{ $cancelColor }}-300 dark:hover:bg-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ $cancelText }}
                </button>
            @endif
        </div>
    </div>

    <!-- Desktop Form Actions -->
    <div class="hidden lg:flex lg:justify-end lg:space-x-3">
        @if($showCancel)
            <button type="button" 
                    onclick="history.back()"
                    class="inline-flex items-center px-4 py-2 border border-{{ $cancelColor }}-300 shadow-sm text-sm font-medium rounded-md text-{{ $cancelColor }}-700 bg-white hover:bg-{{ $cancelColor }}-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $cancelColor }}-500 dark:bg-gray-700 dark:border-gray-600 dark:text-{{ $cancelColor }}-300 dark:hover:bg-gray-600">
                {{ $cancelText }}
            </button>
        @endif
        
        <button type="submit" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-{{ $submitColor }}-600 hover:bg-{{ $submitColor }}-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $submitColor }}-500">
            {{ $submitText }}
        </button>
    </div>
</form>
