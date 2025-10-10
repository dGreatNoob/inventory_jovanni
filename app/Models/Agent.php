<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = [
        'agent_code',
        'name',
        'address',
        'contact_num',
        'tin_num',
        'branch_designation',
    ];

    /**
     * Get all branch assignments (history) for this agent.
     */
    public function branchAssignments()
    {
        return $this->hasMany(AgentBranchAssignment::class);
    }

    /**
     * Get the branches where this agent is currently assigned.
     */
    public function currentBranches()
    {
        return $this->belongsToMany(Branch::class, 'agent_branch_assignments')
                    ->withPivot('assigned_at', 'released_at')
                    ->wherePivot('released_at', null);
    }
}