<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchCustomerSaleItem extends Model
{
    protected $fillable = [
        'branch_customer_sale_id',
        'product_id',
        'product_name',
        'barcode',
        'quantity',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'net_price',
        'promo_id',
        'total_amount',
        'remarks',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function branchCustomerSale()
    {
        return $this->belongsTo(BranchCustomerSale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }
}
