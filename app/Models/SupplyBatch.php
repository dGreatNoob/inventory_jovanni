<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class SupplyBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'supply_profile_id',
        'supply_order_id',
        'batch_number',
        'expiration_date',
        'manufactured_date',
        'initial_qty',
        'current_qty',
        'location',
        'notes',
        'status',
        'received_date',
        'received_by',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'manufactured_date' => 'date', 
        'received_date' => 'date',
        'initial_qty' => 'decimal:2',
        'current_qty' => 'decimal:2',
    ];

    // Relationships
    // supplyProfile() relationship removed - SupplyProfile module has been removed
    // Use Product model instead if needed

    public function supplyOrder(): BelongsTo
    {
        return $this->belongsTo(SupplyOrder::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')->where('current_qty', '>', 0);
    }

    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->where('expiration_date', '<=', Carbon::now()->addDays($days))
                    ->where('expiration_date', '>', Carbon::now())
                    ->where('status', 'active');
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expiration_date', '<', Carbon::now())
                    ->where('status', 'active');
    }

    // scopeBySupplyProfile() removed - SupplyProfile module has been removed
    // Use product_id instead if needed

    // FIFO ordering (First In, First Out) - earliest expiration first
    public function scopeFifo(Builder $query): Builder
    {
        return $query->orderBy('expiration_date', 'asc')
                    ->orderBy('received_date', 'asc');
    }

    // Methods
    public function isExpired(): bool
    {
        return $this->expiration_date && $this->expiration_date->lt(Carbon::now());
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiration_date && 
               $this->expiration_date->gte(Carbon::now()) &&
               $this->expiration_date->lte(Carbon::now()->addDays($days));
    }

    public function getDaysUntilExpiry(): ?int
    {
        if (!$this->expiration_date) {
            return null;
        }
        
        return Carbon::now()->diffInDays($this->expiration_date, false);
    }

    public function canDeduct(float $quantity): bool
    {
        return $this->status === 'active' && 
               $this->current_qty >= $quantity && 
               !$this->isExpired();
    }

    public function deductQuantity(float $quantity): bool
    {
        if (!$this->canDeduct($quantity)) {
            return false;
        }

        $this->current_qty -= $quantity;
        
        // Update status if depleted
        if ($this->current_qty <= 0) {
            $this->status = 'depleted';
            $this->current_qty = 0;
        }

        return $this->save();
    }

    public function addQuantity(float $quantity): bool
    {
        $this->current_qty += $quantity;
        
        // Reactivate if was depleted but now has stock
        if ($this->status === 'depleted' && $this->current_qty > 0 && !$this->isExpired()) {
            $this->status = 'active';
        }

        return $this->save();
    }

    // generateBatchNumber() removed - SupplyProfile module has been removed
    // Use Product model instead if needed
    
    // getOldestAvailableBatch() removed - SupplyProfile module has been removed
    // Use Product model instead if needed
    
    // getBatchesForFifoDeduction() removed - SupplyProfile module has been removed
    // Use Product model instead if needed

        $selectedBatches = [];
        $remainingQty = $totalQtyNeeded;

        foreach ($batches as $batch) {
            if ($remainingQty <= 0) break;

            $qtyToTake = min($batch->current_qty, $remainingQty);
            $selectedBatches[] = [
                'batch' => $batch,
                'quantity' => $qtyToTake
            ];
            $remainingQty -= $qtyToTake;
        }

        return [
            'batches' => $selectedBatches,
            'total_available' => $totalQtyNeeded - $remainingQty,
            'shortage' => max(0, $remainingQty)
        ];
    }
}
