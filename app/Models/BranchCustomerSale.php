<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchCustomerSale extends Model
{
    protected $fillable = [
        'branch_id',
        'selling_area',
        'agent_id',
        'total_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function items()
    {
        return $this->hasMany(BranchCustomerSaleItem::class);
    }
}
