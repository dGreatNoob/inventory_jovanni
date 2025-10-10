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
}