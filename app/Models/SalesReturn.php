<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'sales_order_id',
        'return_date',
        'return_type',
        'reason',
        'return_reference',
        'total_refund',
        'processed_by'
    ];

    public function scopeSearch($query, $search)
    {
        $full = strtolower(trim($search));
        $term = "%{$search}%";       
       
        return $query->where(function ($q) use ($term,$full) {
            $q->where('return_reference', 'like', $term)
            ->orWhere('status', 'like', $term)
            ->orWhere('return_date', 'like', $term)
            ->orWhereHas('salesOrder', function ($q) use ($term) {
                $q->where('sales_order_number', 'like', $term)
                ->orWhereHas('customers', function ($q) use ($term) {
                    $q->where('name', 'like', $term);
                });
            });

            // Match "full" or "partial" to is_full_return
            if ($full == 'full') {
                $q->orWhere('is_full_return', 1);
            } elseif ($full == 'partial') {
                $q->orWhere('is_full_return', 0);
            }

        });
    }

    public function salesOrder()
    {
       return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function items()
    {
        return $this->hasMany(SalesReturnItem::class);
    }
    
}
