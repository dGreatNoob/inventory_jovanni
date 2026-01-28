<?php

use App\Models\Branch;
use App\Models\BranchAllocation;
use App\Models\BranchAllocationItem;
use App\Models\Product;
use App\Models\Shipment;
use Spatie\Activitylog\Models\Activity;

test('inventory audit can detect missing items', function () {
    $branch = Branch::factory()->create();
    $product = Product::factory()->create(['barcode' => 'TEST-BARCODE-001']);
    
    // Create allocation with product
    $allocation = BranchAllocation::factory()->create(['branch_id' => $branch->id]);
    $allocationItem = BranchAllocationItem::factory()->create([
        'branch_allocation_id' => $allocation->id,
        'product_id' => $product->id,
        'quantity' => 10,
        'product_snapshot_barcode' => 'TEST-BARCODE-001',
    ]);
    
    // Create completed shipment
    $shipment = Shipment::factory()->create([
        'branch_allocation_id' => $allocation->id,
        'shipping_status' => 'completed',
    ]);

    // Simulate audit: no barcodes scanned (product is missing)
    $scannedBarcodes = [];
    
    $allocatedItems = BranchAllocationItem::with('product')
        ->whereHas('branchAllocation', fn($q) => $q->where('branch_id', $branch->id))
        ->whereHas('branchAllocation.shipments', fn($q) => $q->where('shipping_status', 'completed'))
        ->where('box_id', null)
        ->get();

    $allocatedProducts = [];
    foreach ($allocatedItems as $item) {
        $barcode = $item->product_snapshot_barcode ?? $item->product->barcode;
        if (!empty($barcode) && $barcode !== 'N/A') {
            if (!isset($allocatedProducts[$barcode])) {
                $allocatedProducts[$barcode] = ['allocated_quantity' => 0];
            }
            $allocatedProducts[$barcode]['allocated_quantity'] += $item->quantity;
        }
    }

    $scannedCount = array_count_values($scannedBarcodes);
    $missingItems = [];
    
    foreach ($allocatedProducts as $barcode => $product) {
        $scanned = $scannedCount[$barcode] ?? 0;
        if ($scanned == 0) {
            $missingItems[] = [
                'barcode' => $barcode,
                'allocated_quantity' => $product['allocated_quantity'],
            ];
        }
    }

    expect(count($missingItems))->toBeGreaterThan(0);
    expect($missingItems[0]['barcode'])->toBe('TEST-BARCODE-001');
});

test('inventory audit can detect extra items', function () {
    $branch = Branch::factory()->create();
    
    // Simulate audit: scanned barcode that doesn't exist in allocations
    $scannedBarcodes = ['UNKNOWN-BARCODE-999'];
    
    $allocatedItems = BranchAllocationItem::with('product')
        ->whereHas('branchAllocation', fn($q) => $q->where('branch_id', $branch->id))
        ->whereHas('branchAllocation.shipments', fn($q) => $q->where('shipping_status', 'completed'))
        ->where('box_id', null)
        ->get();

    $allocatedProducts = [];
    foreach ($allocatedItems as $item) {
        $barcode = $item->product_snapshot_barcode ?? $item->product->barcode;
        if (!empty($barcode) && $barcode !== 'N/A') {
            $allocatedProducts[$barcode] = true;
        }
    }

    $scannedCount = array_count_values($scannedBarcodes);
    $extraItems = [];
    
    foreach ($scannedCount as $barcode => $count) {
        if (!isset($allocatedProducts[$barcode])) {
            $extraItems[] = [
                'barcode' => $barcode,
                'scanned_quantity' => $count,
            ];
        }
    }

    expect(count($extraItems))->toBeGreaterThan(0);
    expect($extraItems[0]['barcode'])->toBe('UNKNOWN-BARCODE-999');
});

test('inventory audit can detect quantity variances', function () {
    $branch = Branch::factory()->create();
    $product = Product::factory()->create(['barcode' => 'TEST-BARCODE-002']);
    
    $allocation = BranchAllocation::factory()->create(['branch_id' => $branch->id]);
    $allocationItem = BranchAllocationItem::factory()->create([
        'branch_allocation_id' => $allocation->id,
        'product_id' => $product->id,
        'quantity' => 10,
        'product_snapshot_barcode' => 'TEST-BARCODE-002',
    ]);
    
    $shipment = Shipment::factory()->create([
        'branch_allocation_id' => $allocation->id,
        'shipping_status' => 'completed',
    ]);

    // Simulate audit: scanned 7 items but allocated 10
    $scannedBarcodes = array_fill(0, 7, 'TEST-BARCODE-002');
    
    $allocatedItems = BranchAllocationItem::with('product')
        ->whereHas('branchAllocation', fn($q) => $q->where('branch_id', $branch->id))
        ->whereHas('branchAllocation.shipments', fn($q) => $q->where('shipping_status', 'completed'))
        ->where('box_id', null)
        ->get();

    $allocatedProducts = [];
    foreach ($allocatedItems as $item) {
        $barcode = $item->product_snapshot_barcode ?? $item->product->barcode;
        if (!empty($barcode) && $barcode !== 'N/A') {
            if (!isset($allocatedProducts[$barcode])) {
                $allocatedProducts[$barcode] = ['allocated_quantity' => 0];
            }
            $allocatedProducts[$barcode]['allocated_quantity'] += $item->quantity;
        }
    }

    $scannedCount = array_count_values($scannedBarcodes);
    $quantityVariances = [];
    
    foreach ($allocatedProducts as $barcode => $product) {
        $scanned = $scannedCount[$barcode] ?? 0;
        if ($scanned > 0 && $scanned != $product['allocated_quantity']) {
            $quantityVariances[] = [
                'barcode' => $barcode,
                'allocated_quantity' => $product['allocated_quantity'],
                'scanned_quantity' => $scanned,
                'variance' => $scanned - $product['allocated_quantity'],
            ];
        }
    }

    expect(count($quantityVariances))->toBeGreaterThan(0);
    expect($quantityVariances[0]['variance'])->toBe(-3); // 7 scanned - 10 allocated
});

test('inventory audit can be saved to activity logs', function () {
    $branch = Branch::factory()->create();
    
    $auditData = [
        'audit_date' => now()->toDateTimeString(),
        'total_scanned' => 5,
        'total_allocated' => 10,
        'missing_items' => [],
        'extra_items' => [],
        'quantity_variances' => [],
    ];

    $activity = Activity::create([
        'log_name' => 'inventory_audit',
        'description' => "Inventory audit for branch {$branch->id}",
        'subject_type' => Branch::class,
        'subject_id' => $branch->id,
        'causer_type' => null,
        'causer_id' => null,
        'properties' => $auditData,
    ]);

    expect($activity)->toBeInstanceOf(Activity::class);
    expect($activity->log_name)->toBe('inventory_audit');
    expect($activity->subject_id)->toBe($branch->id);
    expect($activity->properties['total_scanned'])->toBe(5);
});
