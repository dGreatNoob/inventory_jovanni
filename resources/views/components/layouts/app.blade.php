<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        
        <div class="mb-4">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white"> {{ $header ?? null }} </h1>
            <p class="text-sm text-gray-600 dark:text-neutral-300">{{ $subheader ?? null}}</p>
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
