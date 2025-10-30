<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderBranchItem extends Model
{
    protected $fillable = [
        'sales_order_id',
        'branch_id',
        'product_id',
        'original_unit_price',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'original_unit_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
