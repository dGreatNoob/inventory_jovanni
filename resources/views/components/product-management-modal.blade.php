@props([
    'name',
    'title',
    'description' => null,
    'size' => 'lg',
    'variant' => 'default',
    'icon' => null,
    'iconColor' => 'indigo',
    'closeable' => true,
    'persistent' => false
])

@php
    $sizeClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md', 
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
        'full' => 'max-w-full'
    ];
    
    $iconColors = [
        'indigo' => 'text-indigo-600 bg-indigo-100 dark:bg-indigo-900/20',
        'red' => 'text-red-600 bg-red-100 dark:bg-red-900/20',
        'green' => 'text-green-600 bg-green-100 dark:bg-green-900/20',
        'yellow' => 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900/20',
        'blue' => 'text-blue-600 bg-blue-100 dark:bg-blue-900/20',
        'purple' => 'text-purple-600 bg-purple-100 dark:bg-purple-900/20',
        'gray' => 'text-gray-600 bg-gray-100 dark:bg-gray-900/20'
    ];
@endphp

<flux:modal 
    name="{{ $name }}" 
    class="{{ $sizeClasses[$size] ?? 'max-w-lg' }}"
    :dismissible="!$persistent"
    :closable="$closeable"
>
    <div class="space-y-6">
        @if($title || $icon)
            <div class="flex items-start space-x-4">
                @if($icon)
                    <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full {{ $iconColors[$iconColor] ?? $iconColors['indigo'] }}">
                        @if($icon === 'warning')
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        @elseif($icon === 'info')
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @elseif($icon === 'success')
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @elseif($icon === 'delete')
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        @elseif($icon === 'edit')
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        @elseif($icon === 'bulk')
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        @elseif($icon === 'upload')
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        @else
                            {{ $icon }}
                        @endif
                    </div>
                @endif
                
                <div class="flex-1">
                    @if($title)
                        <flux:heading size="lg" class="text-gray-900 dark:text-white">{{ $title }}</flux:heading>
                    @endif
                    
                    @if($description)
                        <flux:text class="mt-2 text-gray-600 dark:text-gray-400">{{ $description }}</flux:text>
                    @endif
                </div>
            </div>
        @endif

        <!-- Modal Content -->
        <div class="space-y-4">
            {{ $slot }}
        </div>

        <!-- Modal Actions -->
        @if(isset($actions))
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                {{ $actions }}
            </div>
        @endif
    </div>
</flux:modal>
