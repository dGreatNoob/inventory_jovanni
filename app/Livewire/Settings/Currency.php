<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;

class Currency extends Component
{
    public string $exchangeRateCnyToPhp = '';

    public function mount(): void
    {
        $value = Setting::get('exchange_rate_cny_to_php');
        $this->exchangeRateCnyToPhp = $value !== null ? (string) $value : '';
    }

    public function save(): void
    {
        $this->validate([
            'exchangeRateCnyToPhp' => ['required', 'numeric', 'min:0.0001', 'max:9999.9999'],
        ]);

        Setting::set('exchange_rate_cny_to_php', $this->exchangeRateCnyToPhp);

        session()->flash('message', __('Exchange rate saved successfully.'));
    }

    public function render()
    {
        return view('livewire.settings.currency');
    }
}
