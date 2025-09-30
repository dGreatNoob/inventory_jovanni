<div class="fixed top-4 right-4 px-4 py-2 rounded shadow text-white
            {{ $type === 'success' ? 'bg-green-500' : 'bg-red-500' }}" x-data x-init="
            window.addEventListener('auto-hide-flash', () => {
                setTimeout(() => $wire.set('visible', false), 4000);
            })
        ">
    @if($visible)
        {{ $message }}
    @endif
</div>