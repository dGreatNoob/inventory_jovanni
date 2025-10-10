<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'subclass1',
        'subclass2',
        'subclass3',
        'subclass4',
        'code',
        'category',
        'address',
        'remarks',
        'batch',
        'branch_code',
        'company_name',
        'company_tin',
        'dept_code',
        'pull_out_addresse',
        'vendor_code',
    ];

    /**
     * Get the agents currently assigned to this branch.
     */
    public function currentAgents()
    {
        return $this->belongsToMany(Agent::class, 'agent_branch_assignments')
                    ->withPivot('assigned_at', 'released_at')
                    ->wherePivot('released_at', null);
    }

    /**
     * Get all agent assignments (history) for this branch.
     */
    public function agentAssignments()
    {
        return $this->hasMany(AgentBranchAssignment::class);
    }
}