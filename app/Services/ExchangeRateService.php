<?php

namespace App\Services;

use App\Models\Setting;

class ExchangeRateService
{
    /**
     * Get the exchange rate from one currency to another.
     * Reads from manual settings (e.g. exchange_rate_cny_to_php).
     * Returns 1.0 for same currency or PHP to PHP.
     */
    public function getRate(string $from, string $to): float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return 1.0;
        }

        $key = "exchange_rate_{$from}_to_{$to}";
        $value = Setting::get($key);

        if ($value !== null && is_numeric($value)) {
            return (float) $value;
        }

        // Inverse rate (e.g. PHP to CNY if CNY to PHP is set)
        $inverseKey = "exchange_rate_{$to}_to_{$from}";
        $inverseValue = Setting::get($inverseKey);

        if ($inverseValue !== null && is_numeric($inverseValue) && (float) $inverseValue > 0) {
            return 1.0 / (float) $inverseValue;
        }

        return 1.0;
    }

    /**
     * Convert an amount from one currency to another.
     */
    public function convert(float $amount, string $from, string $to): float
    {
        $rate = $this->getRate($from, $to);

        return round($amount * $rate, 2);
    }
}
