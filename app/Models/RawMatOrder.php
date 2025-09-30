<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMatOrder extends Model
{
    /** @use HasFactory<\Database\Factories\RawMatOrderFactory> */
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'raw_mat_profile_id',
        'order_qty',

    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function rawMatProfile(): BelongsTo
    {
        return $this->belongsTo(RawMatProfile::class);
    }

    public function rawMatInvs(): HasMany
    {
        return $this->hasMany(RawMatInv::class);
    }
}
