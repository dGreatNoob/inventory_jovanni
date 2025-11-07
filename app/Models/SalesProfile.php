<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesProfile extends Model
{
    protected $fillable = [
        'sales_number',
        'sales_date',
        'branch_id',
        'agent_id',
        'total_amount',
        'remarks'
    ];

    protected $casts = [
        'sales_date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesProfileItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->sales_number)) {
                $model->sales_number = self::generateSalesNumber();
            }
        });
    }

    public static function generateSalesNumber(): string
    {
        $date = now()->format('Ym');
        $lastRecord = self::where('sales_number', 'like', "SLS-{$date}-%")
                         ->orderBy('id', 'desc')
                         ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->sales_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "SLS-{$date}-{$newNumber}";
    }
}
