<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    protected $table = 'logs';
    protected $primaryKey = 'log_id';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'action',
        'entity',
        'reference_id',
        'message',
        'status',
        'timestamp',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
