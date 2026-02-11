<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'decimal_places',
        'is_base',
    ];

    protected $casts = [
        'is_base' => 'boolean',
    ];

    /**
     * Get the base currency.
     */
    public static function base(): ?self
    {
        return static::where('is_base', true)->first();
    }

    /**
     * Get currency by code.
     */
    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }

    /**
     * Format amount with currency symbol.
     */
    public function format(float $amount): string
    {
        return $this->symbol . number_format($amount, $this->decimal_places);
    }
}
