<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentVehicle extends Model
{
    protected $fillable = [
        'shipment_id',
        'plate_number',
        'delivery_receipt_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function deliveryReceipt()
    {
        return $this->belongsTo(DeliveryReceipt::class);
    }
}
