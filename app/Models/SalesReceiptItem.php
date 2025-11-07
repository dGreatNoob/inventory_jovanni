<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReceiptItem extends Model
{
    protected $fillable = [
        'sales_receipt_id',
        'product_id',
        'allocated_qty',
        'received_qty',
        'damaged_qty',
        'missing_qty',
        'sold_qty',
        'status',
        'remarks',
        'sold_at',
        'sold_by',
    ];

    public function salesReceipt()
    {
        return $this->belongsTo(SalesReceipt::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}