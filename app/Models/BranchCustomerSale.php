<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchCustomerSale extends Model
{
    protected $fillable = [
        'ref_no',
        'branch_id',
        'selling_area',
        'agent_id',
        'total_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Boot the model and generate reference number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (empty($sale->ref_no)) {
                $sale->ref_no = static::generateReferenceNumber();
            }
        });
    }

    /**
     * Generate a unique reference number for the sale
     * Format: CS-YYYYMMDD-XXXX (CS = Customer Sale)
     */
    public static function generateReferenceNumber()
    {
        $date = now()->format('Ymd');
        $prefix = "CS-{$date}-";

        // Get the last sale for today
        $lastSale = static::where('ref_no', 'like', $prefix . '%')
            ->orderBy('ref_no', 'desc')
            ->first();

        if ($lastSale) {
            // Extract the sequence number and increment
            $lastSequence = intval(substr($lastSale->ref_no, -4));
            $newSequence = $lastSequence + 1;
        } else {
            // First sale of the day
            $newSequence = 1;
        }

        return $prefix . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function items()
    {
        return $this->hasMany(BranchCustomerSaleItem::class);
    }
}
