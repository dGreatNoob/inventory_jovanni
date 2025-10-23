<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_num',
        'status',
        'po_type',
        'supplier_id',
        'order_date',
        'expected_delivery_date',
        'ordered_by',
        'del_to',
        'del_on',
        'payment_terms',
        'quotation',
        'total_qty',
        'total_price',
        'total_est_weight',
        'approver',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'del_on' => 'datetime',
        'total_qty' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'del_to');
    }

    public function orderedByUser()
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    // ✅ ADD THIS METHOD
    public function approverInfo()
    {
        return $this->belongsTo(User::class, 'approver');
    }

    public function productOrders()
    {
        return $this->hasMany(ProductOrder::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, ProductOrder::class);
    }
}