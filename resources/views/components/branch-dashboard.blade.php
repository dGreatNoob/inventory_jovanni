@props(['stats'])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($stats as $stat)
        <div class="bg-gradient-to-r {{ $stat['gradient'] }} rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">{{ $stat['label'] }}</p>
                    <p class="text-3xl font-bold mt-2">{{ $stat['value'] }}</p>
                    @if(isset($stat['change']))
                        <p class="text-sm mt-2 opacity-90">
                            <span class="{{ $stat['change'] >= 0 ? 'text-green-200' : 'text-red-200' }}">
                                {{ $stat['change'] >= 0 ? '↑' : '↓' }} {{ abs($stat['change']) }}%
                            </span>
                            vs {{ $stat['period'] }}
                        </p>
                    @endif
                </div>
                <div class="opacity-80">
                    {!! $stat['icon'] !!}
                </div>
            </div>
        </div>
    @endforeach
</div>