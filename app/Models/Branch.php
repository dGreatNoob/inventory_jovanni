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
}
