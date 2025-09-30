<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    /**
     * A role belongs to a department (team).
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
