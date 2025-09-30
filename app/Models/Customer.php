<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $fillable = [
        'name',
        'address',
        'contact_num',
        'tin_num'
    ];

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
