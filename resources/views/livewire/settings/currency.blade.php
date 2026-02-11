<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout
        :heading="__('Exchange Rates')"
        :subheading="__('Set manual conversion rates for purchase orders and stock-in. These rates may differ from market rates.')"
    >
        <form wire:submit="save" class="mt-6 space-y-6">
            <div class="space-y-4">
                <flux:input
                    wire:model="exchangeRateCnyToPhp"
                    :label="__('CNY → PHP (1 Chinese Yuan = how many Philippine Peso)')"
                    type="number"
                    step="0.0001"
                    min="0.0001"
                    placeholder="8.25"
                />
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Used when converting supplier prices from Chinese Yuan to PHP for costing and reports.') }}
                </p>
            </div>

            @if (session()->has('message'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300">
                    {{ session('message') }}
                </div>
            @endif

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </x-settings.layout>
</section>
