@props([
    'type' => 'success', // success, error, warning, info
    'message' => session('message'),
])

@php
    $colors = [
        'success' => 'bg-green-100 text-green-800 dark:bg-green-200 dark:text-green-900',
        'error' => 'bg-red-100 text-red-800 dark:bg-red-200 dark:text-red-900',
        'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-200 dark:text-yellow-900',
        'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-200 dark:text-blue-900',
    ];
@endphp

@if ($message)
    <div 
        class="flex items-center p-4 mb-4 text-sm rounded-lg {{ $colors[$type] }}" 
        role="alert"
    >
        <svg class="flex-shrink-0 w-4 h-4 mr-3" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
            @if ($type === 'success')
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            @elseif ($type === 'error')
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5a1 1 0 112 0 1 1 0 01-2 0zm1-8a1 1 0 00-1 1v4a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            @elseif ($type === 'warning')
                <path d="M8.257 3.099c.763-1.36 2.683-1.36 3.446 0l6.857 12.224A1.75 1.75 0 0116.857 18H3.143a1.75 1.75 0 01-1.703-2.677L8.257 3.1zM9 13h2v2H9v-2zm0-6h2v4H9V7z"/>
            @else
                <path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zM9 7a1 1 0 100 2 1 1 0 000-2zm0 4a1 1 0 012 0v1a1 1 0 01-2 0v-1z" clip-rule="evenodd"/>
            @endif
        </svg>
        <span class="sr-only">{{ ucfirst($type) }}</span>
        <div>{{ $message }}</div>
    </div>
@endif
