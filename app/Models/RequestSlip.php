<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class RequestSlip extends Model
{
    /** @use HasFactory<\Database\Factories\RequestSlipFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'status',
        'purpose',
        'description',
        'request_date',
        'sent_from',
        'sent_to',
        'requested_by',
        'approver',
    ];
    protected $casts = [
        'request_date' => 'date',
    ];

    public function scopeSearch($query, $search)
    {
        return $query->where('purpose', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver');
    }

    public function sentFrom(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'sent_from');
    }
    public function sentTo(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'sent_to');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'purpose', 'description', 'request_date', 'sent_from', 'sent_to', 'requested_by', 'approver'])
            ->logOnlyDirty()
            ->useLogName('request_slip')
            ->setDescriptionForEvent(function(string $eventName) {
                switch ($eventName) {
                    case 'created':
                        return 'Request Slip created';
                    case 'deleted':
                        return 'Request Slip deleted';
                    case 'updated':
                        if ($this->status === 'approved') {
                            return 'Request Slip approved';
                        } elseif ($this->status === 'rejected') {
                            return 'Request Slip rejected';
                        }
                        return 'Request Slip updated';
                    default:
                        return "Request Slip {$eventName}";
                }
            });
    }
}
