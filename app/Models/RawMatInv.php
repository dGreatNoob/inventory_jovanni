<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class RawMatInv extends Model
{
    /** @use HasFactory<\Database\Factories\RawMatInvFactory> */
    use HasFactory;

    protected $fillable = [
        'spc_num',
        'supplier_num',
        'weight',
        'rem_weight',
        'remarks',
        'comment',
        'raw_mat_order_id',
        'date_delivered',
        'status'
    ];

    public function rawMatOrder(): BelongsTo
    {
        return $this->belongsTo(RawMatOrder::class);
    }

    public function purchaseOrder()
    {
        return $this->hasOneThrough(
            \App\Models\PurchaseOrder::class,
            \App\Models\RawMatOrder::class,
            'id', // Foreign key on RawMatOrder
            'id', // Foreign key on PurchaseOrder
            'raw_mat_order_id', // Local key on RawMatInv
            'purchase_order_id' // Local key on RawMatOrder
        );
    }

}
