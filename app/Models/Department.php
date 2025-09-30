<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'del_to');
    }
    public function requestSlipsSentFrom()
    {
        return $this->hasMany(RequestSlip::class, 'sent_from');
    }
    public function requestSlipsSentTo()
    {
        return $this->hasMany(RequestSlip::class, 'sent_to');
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
   
    
    
    

}

