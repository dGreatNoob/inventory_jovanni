<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref_no',
        'batch_number',
        'purchase_order_id',
        'transaction_date',
        'remarks',
        'status',
        'workflow_step',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'workflow_step' => 'integer',
    ];

    public function branchAllocations()
    {
        return $this->hasMany(BranchAllocation::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}