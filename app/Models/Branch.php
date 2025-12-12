<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    /**
     * Subclass configuration constants
     */
    const SUBCLASS_FIELDS = ['subclass1', 'subclass2', 'subclass3', 'subclass4'];
    const MAX_SUBCLASSES = 4;

    protected $fillable = [
        'name',
        'subclass1',
        'subclass2',
        'subclass3',
        'subclass4',
        'code',
        'category',
        'address',
        'contact_num',
        'manager_name',
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
                    ->withPivot('assigned_at', 'released_at', 'subclass')
                    ->wherePivot('released_at', null);
    }

    /**
     * Get all agent assignments (history) for this branch.
     */
    public function agentAssignments()
    {
        return $this->hasMany(AgentBranchAssignment::class);
    }

    /**
     * Get all branch assignments (for relationship).
     */
    public function branchAssignments()
    {
        return $this->hasMany(AgentBranchAssignment::class);
    }

    /**
     * Get active agent assignments with agent details.
     */
    public function activeAgents()
    {
        return $this->hasMany(AgentBranchAssignment::class)
            ->whereNull('released_at')
            ->with('agent');
    }

    /**
     * Get all subclass values as an array.
     *
     * @return array
     */
    public function getSubclasses(): array
    {
        return array_filter([
            $this->subclass1,
            $this->subclass2,
            $this->subclass3,
            $this->subclass4,
        ]);
    }

    /**
     * Get subclass by index (1-4).
     *
     * @param int $index
     * @return string|null
     */
    public function getSubclass(int $index): ?string
    {
        if ($index < 1 || $index > self::MAX_SUBCLASSES) {
            return null;
        }

        return $this->{"subclass{$index}"};
    }

    /**
     * Check if branch has any subclasses defined.
     *
     * @return bool
     */
    public function hasSubclasses(): bool
    {
        return !empty($this->getSubclasses());
    }

    /**
     * Get subclass count.
     *
     * @return int
     */
    public function getSubclassCount(): int
    {
        return count($this->getSubclasses());
    }

    /**
     * Get all available subclass fields.
     *
     * @return array
     */
    public static function getSubclassFields(): array
    {
        return self::SUBCLASS_FIELDS;
    }

    /**
     * Get subclass options for dropdown/select.
     *
     * @return array
     */
    public function getSubclassOptions(): array
    {
        $options = [];
        
        foreach (self::SUBCLASS_FIELDS as $field) {
            if (!empty($this->$field)) {
                $options[$field] = $this->$field;
            }
        }
        
        return $options;
    }

    /**
     * Scope to filter branches with specific subclass.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $subclass
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithSubclass($query, string $subclass)
    {
        return $query->where(function($q) use ($subclass) {
            foreach (self::SUBCLASS_FIELDS as $field) {
                $q->orWhere($field, $subclass);
            }
        });
    }

    /**
     * Scope to filter branches that have any subclass defined.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasSubclasses($query)
    {
        return $query->where(function($q) {
            foreach (self::SUBCLASS_FIELDS as $field) {
                $q->orWhereNotNull($field);
            }
        });
    }
    public function products()
    {
        // If you have a pivot table "branch_product"
        return $this->belongsToMany(Product::class, 'branch_product')
                    ->withPivot('stock'); // Add other fields from pivot if any
    }

    /**
     * Get all branch allocations for this branch.
     */
    public function branchAllocations()
    {
        return $this->hasMany(BranchAllocation::class);
    }

    /**
     * Get all shipments for this branch through branch allocations.
     */
    public function shipments()
    {
        return $this->hasManyThrough(Shipment::class, BranchAllocation::class, 'branch_id', 'branch_allocation_id');
    }
}