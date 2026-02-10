
<x-modal {{ $attributes->merge(['class' => 'max-w-3xl max-h-[90vh] flex flex-col']) }}>
    <x-slot:title>Select Products</x-slot:title>
    <div
        data-product-selection-modal
        x-data="{
            focusedIndex: -1,
            onKeydown(e) {
                if (!document.body.contains($el) || $el.offsetParent === null) return;
                if (e.target.tagName === 'INPUT' && e.target.type === 'text') return;
                const rows = $refs.list?.querySelectorAll('[data-product-row]');
                if (!rows?.length) return;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    this.focusedIndex = Math.min(this.focusedIndex + 1, rows.length - 1);
                    rows[this.focusedIndex]?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    rows[this.focusedIndex]?.querySelector('input[type=checkbox]')?.focus();
                }
                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    this.focusedIndex = Math.max(this.focusedIndex - 1, 0);
                    rows[this.focusedIndex]?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    rows[this.focusedIndex]?.querySelector('input[type=checkbox]')?.focus();
                }
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const row = rows[this.focusedIndex];
                    if (row) row.querySelector('input[type=checkbox]')?.click();
                }
            }
        }"
        x-init="focusedIndex = -1"
        @keydown.window="onKeydown($event)"
        class="flex flex-col gap-4 min-h-0 max-h-[calc(90vh-8rem)] overflow-hidden"
    >
        {{-- Search bar - sticky, outside scroll container --}}
        <div class="flex-shrink-0">
            {{ $search ?? '' }}
        </div>

        {{-- Product list - fixed height, scrollable --}}
        <div x-ref="list" class="flex-1 min-h-0 flex flex-col">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-600 pt-4">
            {{ $footer ?? '' }}
        </div>
    </div>
</x-modal>
