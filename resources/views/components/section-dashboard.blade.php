@props([
    'title' => 'Dashboard',
    'stats' => [],
    'charts' => false,
    'actions' => []
])

<div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
    <!-- Dashboard Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                {{ $title }}
            </h3>
            <!-- Dashboard Actions -->
            @if(!empty($actions))
                <div class="flex space-x-2">
                    @foreach($actions as $action)
                        <button type="button" 
                            @if(isset($action['click'])) 
                                onclick="{{ $action['click'] }}"
                            @endif
                            @if(isset($action['wire:click'])) 
                                wire:click="{{ $action['wire:click'] }}"
                            @endif
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $action['class'] ?? 'text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                            @if(isset($action['icon']))
                                {!! $action['icon'] !!}
                            @endif
                            {{ $action['label'] }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="p-6">
        @if(!empty($stats))
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 {{ $charts ? 'mb-6' : '' }}">
                @foreach($stats as $stat)
                    <div class="bg-gradient-to-r {{ $stat['gradient'] ?? 'from-blue-500 to-blue-600' }} rounded-lg p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">{{ $stat['label'] }}</p>
                                <p class="text-2xl font-bold">{{ $stat['value'] }}</p>
                                @if(isset($stat['change']))
                                    <p class="text-xs text-blue-100 mt-1">
                                        @if($stat['change'] > 0)
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                +{{ $stat['change'] }}%
                                            </span>
                                        @elseif($stat['change'] < 0)
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $stat['change'] }}%
                                            </span>
                                        @else
                                            <span>No change</span>
                                        @endif
                                        {{ isset($stat['period']) ? 'from ' . $stat['period'] : '' }}
                                    </p>
                                @endif
                            </div>
                            @if(isset($stat['icon']))
                                <div class="text-blue-100">
                                    {!! $stat['icon'] !!}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($charts)
            <!-- Charts Section -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <div class="text-center text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p>Charts will be implemented here</p>
                </div>
            </div>
        @endif

        <!-- Custom Content Slot -->
        {{ $slot }}
    </div>
</div>