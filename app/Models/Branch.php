<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    /**
     * Selling area configuration constants
     */
    const SELLING_AREA_FIELDS = ['selling_area1', 'selling_area2', 'selling_area3', 'selling_area4'];
    const MAX_SELLING_AREAS = 4;

    protected $fillable = [
        'name',
        'selling_area1',
        'selling_area2',
        'selling_area3',
        'selling_area4',
        'code',
        'category',
        'address',
        'contact_num',
        'manager_name',
        'email',
        'remarks',
        'batch',
        'branch_code',
        'company_name',
        'company_tin',
        'dept_code',
        'pull_out_address',
        'vendor_code',
    ];

    /**
     * Get the agents currently assigned to this branch.
     */
    public function currentAgents()
    {
        return $this->belongsToMany(Agent::class, 'agent_branch_assignments')
                    ->withPivot('assigned_at', 'released_at', 'selling_area')
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
     * Get all selling area values as an array.
     *
     * @return array
     */
    public function getSellingAreas(): array
    {
        return array_filter([
            $this->selling_area1,
            $this->selling_area2,
            $this->selling_area3,
            $this->selling_area4,
        ]);
    }

    /**
     * Get selling area by index (1-4).
     *
     * @param int $index
     * @return string|null
     */
    public function getSellingArea(int $index): ?string
    {
        if ($index < 1 || $index > self::MAX_SELLING_AREAS) {
            return null;
        }

        return $this->{"selling_area{$index}"};
    }

    /**
     * Check if branch has any selling areas defined.
     *
     * @return bool
     */
    public function hasSellingAreas(): bool
    {
        return !empty($this->getSellingAreas());
    }

    /**
     * Get selling area count.
     *
     * @return int
     */
    public function getSellingAreaCount(): int
    {
        return count($this->getSellingAreas());
    }

    /**
     * Get all available selling area fields.
     *
     * @return array
     */
    public static function getSellingAreaFields(): array
    {
        return self::SELLING_AREA_FIELDS;
    }

    /**
     * Get selling area options for dropdown/select.
     *
     * @return array
     */
    public function getSellingAreaOptions(): array
    {
        $options = [];
        
        foreach (self::SELLING_AREA_FIELDS as $field) {
            if (!empty($this->$field)) {
                $options[$field] = $this->$field;
            }
        }
        
        return $options;
    }

    /**
     * Scope to filter branches with specific selling area.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $sellingArea
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithSellingArea($query, string $sellingArea)
    {
        return $query->where(function($q) use ($sellingArea) {
            foreach (self::SELLING_AREA_FIELDS as $field) {
                $q->orWhere($field, $sellingArea);
            }
        });
    }

    /**
     * Scope to filter branches that have any selling area defined.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasSellingAreas($query)
    {
        return $query->where(function($q) {
            foreach (self::SELLING_AREA_FIELDS as $field) {
                $q->orWhereNotNull($field);
            }
        });
    }

    /**
     * @deprecated Use getSellingAreas() instead
     */
    public function getSubclasses(): array
    {
        return $this->getSellingAreas();
    }

    /**
     * @deprecated Use getSellingAreaOptions() instead
     */
    public function getSubclassOptions(): array
    {
        return $this->getSellingAreaOptions();
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