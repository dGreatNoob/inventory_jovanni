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
        'transaction_date',
        'remarks',
        'status',
        'workflow_step', // Add this
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'workflow_step' => 'integer',
    ];

    public function branchAllocations()
    {
        return $this->hasMany(BranchAllocation::class);
    }
}