<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    protected $fillable = [
        'sales_return_number',
        'branch_id',
        'shipment_id',
        'status',
        'reason',
        'created_by',
        'completed_by',
        'completed_at',
    ];

    protected static function booted()
    {
        static::creating(function ($salesReturn) {
            $date = now()->format('Ymd');
            $latest = self::count() + 1;
            $salesReturn->sales_return_number = 'SR-' . $date . '-' . str_pad($latest, 4, '0', STR_PAD_LEFT);
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function items()
    {
        return $this->hasMany(SalesReturnItem::class);
    }
}
