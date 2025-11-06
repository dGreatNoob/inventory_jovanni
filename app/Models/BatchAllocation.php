<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchAllocation extends Model
{
    protected $fillable = [
        'ref_no',
        'transaction_date',
        'remarks',
        'status',
    ];

    public function branchAllocations()
    {
        return $this->hasMany(BranchAllocation::class);
    }
}