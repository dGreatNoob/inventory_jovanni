@props([
    'data' => [],
    'columns' => [],
    'mobileColumns' => [],
    'actions' => [],
    'emptyMessage' => 'No data found',
    'class' => ''
])

<div class="overflow-hidden {{ $class }}">
    <!-- Desktop Table -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    @foreach($columns as $column)
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ $column['label'] }}
                        </th>
                    @endforeach
                    @if(count($actions) > 0)
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($data as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        @foreach($columns as $column)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                @if(isset($column['slot']))
                                    {{ $column['slot']($item) }}
                                @else
                                    {{ data_get($item, $column['key']) }}
                                @endif
                            </td>
                        @endforeach
                        @if(count($actions) > 0)
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-2">
                                    @foreach($actions as $action)
                                        @if(isset($action['condition']) && !$action['condition']($item))
                                            @continue
                                        @endif
                                        <button wire:click="{{ $action['method'] }}({{ $item->id }})" 
                                                class="text-{{ $action['color'] ?? 'blue' }}-600 hover:text-{{ $action['color'] ?? 'blue' }}-900 dark:text-{{ $action['color'] ?? 'blue' }}-400 dark:hover:text-{{ $action['color'] ?? 'blue' }}-300">
                                            {{ $action['label'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + (count($actions) > 0 ? 1 : 0) }}" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ $emptyMessage }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="lg:hidden space-y-4">
        @forelse($data as $item)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="space-y-3">
                    @foreach($mobileColumns as $column)
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ $column['label'] }}
                                </p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                    @if(isset($column['slot']))
                                        {{ $column['slot']($item) }}
                                    @else
                                        {{ data_get($item, $column['key']) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                    
                    @if(count($actions) > 0)
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex space-x-2">
                                @foreach($actions as $action)
                                    @if(isset($action['condition']) && !$action['condition']($item))
                                        @continue
                                    @endif
                                    <button wire:click="{{ $action['method'] }}({{ $item->id }})" 
                                            class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-{{ $action['color'] ?? 'blue' }}-300 shadow-sm text-sm leading-4 font-medium rounded-md text-{{ $action['color'] ?? 'blue' }}-700 bg-white hover:bg-{{ $action['color'] ?? 'blue' }}-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $action['color'] ?? 'blue' }}-500 dark:bg-gray-700 dark:border-gray-600 dark:text-{{ $action['color'] ?? 'blue' }}-300 dark:hover:bg-gray-600">
                                        {{ $action['label'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No data found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $emptyMessage }}</p>
            </div>
        @endforelse
    </div>
</div>
