<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchAllocationItem extends Model
    {
        protected $fillable = [
            'branch_allocation_id',
            'product_id',
            'quantity',
            'scanned_quantity',
            'sold_quantity',
            'unit_price',
            'box_id',
            'delivery_receipt_id',
            // Product snapshot fields for historical data integrity
            'product_snapshot_name',
            'product_snapshot_sku',
            'product_snapshot_barcode',
            'product_snapshot_specs',
            'product_snapshot_price',
            'product_snapshot_uom',
            'product_snapshot_created_at',
        ];

    protected $casts = [
        'quantity' => 'integer',
        'scanned_quantity' => 'integer',
        'sold_quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'product_snapshot_specs' => 'array',
        'product_snapshot_price' => 'decimal:2',
        'product_snapshot_created_at' => 'datetime',
    ];

    public function branchAllocation()
    {
        return $this->belongsTo(BranchAllocation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function deliveryReceipt()
    {
        return $this->belongsTo(DeliveryReceipt::class);
    }

    /**
     * Boot the model and set up event listeners
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->createProductSnapshot();
        });
    }

    /**
     * Create a snapshot of the product data at the time of allocation
     */
    public function createProductSnapshot()
    {
        if ($this->product) {
            $this->product_snapshot_name = $this->product->name;
            $this->product_snapshot_sku = $this->product->sku;
            $this->product_snapshot_barcode = $this->product->barcode;
            $this->product_snapshot_specs = $this->product->specs;
            $this->product_snapshot_price = $this->product->price ?? $this->product->selling_price;
            $this->product_snapshot_uom = $this->product->uom;
            $this->product_snapshot_created_at = $this->product->created_at;
        }
    }

    /**
     * Get the product name (snapshot if available, otherwise current)
     */
    public function getDisplayNameAttribute()
    {
        return $this->product_snapshot_name ?: ($this->product ? $this->product->name : 'Unknown Product');
    }

    /**
     * Get the product SKU (snapshot if available, otherwise current)
     */
    public function getDisplaySkuAttribute()
    {
        return $this->product_snapshot_sku ?: ($this->product ? $this->product->sku : 'N/A');
    }

    /**
     * Get the product barcode (snapshot if available, otherwise current)
     */
    public function getDisplayBarcodeAttribute()
    {
        return $this->product_snapshot_barcode ?: ($this->product ? $this->product->barcode : 'N/A');
    }

    /**
     * Get the product price (snapshot if available, otherwise current)
     */
    public function getDisplayPriceAttribute()
    {
        return $this->product_snapshot_price ?: ($this->product ? ($this->product->price ?? $this->product->selling_price ?? 0) : 0);
    }
}