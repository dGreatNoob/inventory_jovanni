<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        
        <div class="mb-4">
            <nav class="flex items-center gap-2 text-2xl font-semibold text-gray-800 dark:text-white" aria-label="Breadcrumb">
                @if(isset($header) && isset($subheader))
                    @if(isset($headerHref))
                        <a href="{{ $headerHref }}" class="hover:underline hover:text-gray-600 dark:hover:text-gray-300" wire:navigate>{{ $header }}</a>
                        <span class="text-gray-400 dark:text-gray-500 font-normal" aria-hidden="true">&gt;</span>
                        <span>{{ $subheader }}</span>
                    @else
                        {{ $header }} <span class="text-gray-400 dark:text-gray-500 font-normal">&gt;</span> {{ $subheader }}
                    @endif
                @else
                    {{ $header ?? $subheader ?? null }}
                @endif
            </nav>
        </div>
@if (session()->has('success'))
    <div
        class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg animate-fade-in z-50 pointer-events-none"
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)">
        {{ session('success') }}
    </div>
@endif

        {{-- <livewire:delivery-history /> --}}

        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
