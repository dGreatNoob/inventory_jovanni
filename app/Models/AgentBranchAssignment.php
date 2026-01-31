<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentBranchAssignment extends Model
{
    protected $fillable = [
        'agent_id',
        'branch_id',
        'selling_area',
        'assigned_at',
        'released_at',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}