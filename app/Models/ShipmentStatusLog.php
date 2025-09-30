<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShipmentStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'status',
        'changed_at',
        'changed_by',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    // Relationship to shipment
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    // Optional: Relationship to user who changed it
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
