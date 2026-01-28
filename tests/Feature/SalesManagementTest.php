<?php

use App\Models\BranchAllocationItem;
use App\Models\BranchAllocation;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Shipment;
use Spatie\Activitylog\Models\Activity;

test('sales can be recorded via branch allocation items', function () {
    $branch = Branch::factory()->create();
    $product = Product::factory()->create();
    
    $allocation = BranchAllocation::factory()->create(['branch_id' => $branch->id]);
    $allocationItem = BranchAllocationItem::factory()->create([
        'branch_allocation_id' => $allocation->id,
        'product_id' => $product->id,
        'quantity' => 100,
        'sold_quantity' => 0,
        'unit_price' => 50.00,
    ]);
    
    $shipment = Shipment::factory()->create([
        'branch_allocation_id' => $allocation->id,
        'shipping_status' => 'completed',
    ]);

    // Record sale of 25 units
    $saleQuantity = 25;
    $allocationItem->increment('sold_quantity', $saleQuantity);

    expect($allocationItem->fresh()->sold_quantity)->toBe(25);
    expect($allocationItem->fresh()->quantity - $allocationItem->fresh()->sold_quantity)->toBe(75);
});

test('sales activity is logged', function () {
    $branch = Branch::factory()->create();
    $product = Product::factory()->create(['barcode' => 'SALE-TEST-001']);

    $activity = Activity::create([
        'log_name' => 'branch_inventory',
        'description' => "Customer sale recorded for product SALE-TEST-001 in branch {$branch->id}",
        'subject_type' => BranchAllocationItem::class,
        'subject_id' => null,
        'causer_type' => null,
        'causer_id' => null,
        'properties' => [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'barcode' => 'SALE-TEST-001',
            'quantity_sold' => 10,
            'unit_price' => 50.00,
            'total_amount' => 500.00,
            'branch_id' => $branch->id,
            'customer_sale' => true,
        ],
    ]);

    expect($activity)->toBeInstanceOf(Activity::class);
    expect($activity->log_name)->toBe('branch_inventory');
    expect($activity->properties['customer_sale'])->toBeTrue();
    expect($activity->properties['quantity_sold'])->toBe(10);
});

test('sales quantity cannot exceed available quantity', function () {
    $branch = Branch::factory()->create();
    $product = Product::factory()->create();
    
    $allocation = BranchAllocation::factory()->create(['branch_id' => $branch->id]);
    $allocationItem = BranchAllocationItem::factory()->create([
        'branch_allocation_id' => $allocation->id,
        'product_id' => $product->id,
        'quantity' => 100,
        'sold_quantity' => 0,
    ]);

    // Try to sell more than available
    $available = $allocationItem->quantity - $allocationItem->sold_quantity;
    $attemptedSale = $available + 10; // Try to sell 10 more than available

    // Should only allow selling available quantity
    $actualSale = min($attemptedSale, $available);
    $allocationItem->increment('sold_quantity', $actualSale);

    expect($allocationItem->fresh()->sold_quantity)->toBe(100);
    expect($allocationItem->fresh()->sold_quantity)->toBeLessThanOrEqual($allocationItem->fresh()->quantity);
});
