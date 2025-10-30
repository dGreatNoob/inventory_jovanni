<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class SalesOrderItem extends Model
{
    use HasFactory,LogsActivity;
    protected $table = 'sales_order_items';

    protected $fillable = [
        'sales_order_id',
        'product_id',    
        'quantity',
        'unit_price',
        'subtotal',
    ];

    /**
     * Relationship: belongs to SalesOrder
     */
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    /**
     * Relationship: belongs to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

   
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->useLogName('sales_order_item')
            ->setDescriptionForEvent(function (string $eventName) {
                // Load relations safely
                $this->loadMissing(['product', 'salesOrder']);

                $fields = [
                    'Sales Order' => $this->salesOrder->sales_order_number ?? 'N/A',
                    'Product' => $this->product->name ?? 'N/A',
                    'Description' => $this->description ?? 'N/A',
                    'Quantity' => $this->quantity ?? 'N/A',
                    'Unit Price' => $this->unit_price ?? 'N/A',
                    'Subtotal' => $this->subtotal ?? 'N/A',
                ];

                return collect($fields)
                    ->map(fn($v, $k) => "<strong>$k</strong>: $v")
                    ->implode('<br>');
            });
    }
}
