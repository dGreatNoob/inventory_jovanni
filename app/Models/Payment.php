<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_ref',
        'amount',
        'payment_date',
        'payment_method',
        'finance_id',
        'status',
        'remarks',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the finance record (payable or receivable) this payment is linked to
     */
    public function finance(): BelongsTo
    {
        return $this->belongsTo(Finance::class);
    }

    /**
     * Scope to filter by payment method
     */
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check if payment is valid (amount <= balance due)
     */
    public function isValidAmount(): bool
    {
        if (!$this->finance) {
            return false;
        }

        return $this->amount <= $this->finance->balance;
    }
}
