<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Finance extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'reference_id',
        'supplier',
        'purchase_order',
        'customer',
        'sales_order',
        'party',
        'date',
        'category',
        'due_date',
        'amount',
        'balance',
        'payment_method',
        'status',
        'remarks',
        'branch_id',
        'agent_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}