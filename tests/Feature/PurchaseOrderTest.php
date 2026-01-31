<?php

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\User;
use App\Models\Department;
use App\Enums\PurchaseOrderStatus;

test('purchase order can be created', function () {
    $supplier = Supplier::factory()->create();
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $po = PurchaseOrder::create([
        'po_num' => 'PO-TEST-001',
        'status' => PurchaseOrderStatus::PENDING,
        'supplier_id' => $supplier->id,
        'order_date' => now(),
        'ordered_by' => $user->id,
        'del_to' => $department->id,
        'total_qty' => 100,
        'total_price' => 10000.00,
        'approver' => $user->id,
    ]);

    expect($po)->toBeInstanceOf(PurchaseOrder::class);
    expect($po->po_num)->toBe('PO-TEST-001');
    expect($po->status)->toBe(PurchaseOrderStatus::PENDING);
    expect($po->total_price)->toBe('10000.00');
});

test('purchase order can have items', function () {
    $supplier = Supplier::factory()->create();
    $user = User::factory()->create();
    $department = Department::factory()->create();
    $product = Product::factory()->create();

    $po = PurchaseOrder::create([
        'po_num' => 'PO-TEST-002',
        'status' => PurchaseOrderStatus::PENDING,
        'supplier_id' => $supplier->id,
        'order_date' => now(),
        'ordered_by' => $user->id,
        'del_to' => $department->id,
        'total_qty' => 50,
        'total_price' => 5000.00,
        'approver' => $user->id,
    ]);

    $poItem = PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'quantity' => 50,
        'unit_price' => 100.00,
    ]);

    expect($poItem->purchase_order_id)->toBe($po->id);
    expect($poItem->product_id)->toBe($product->id);
    expect($poItem->quantity)->toBe(50);
    expect($po->items()->count())->toBe(1);
});

test('purchase order status can be updated', function () {
    $supplier = Supplier::factory()->create();
    $user = User::factory()->create();
    $department = Department::factory()->create();

    $po = PurchaseOrder::create([
        'po_num' => 'PO-TEST-003',
        'status' => PurchaseOrderStatus::PENDING,
        'supplier_id' => $supplier->id,
        'order_date' => now(),
        'ordered_by' => $user->id,
        'del_to' => $department->id,
        'total_qty' => 100,
        'total_price' => 10000.00,
        'approver' => $user->id,
    ]);

    $po->update([
        'status' => PurchaseOrderStatus::APPROVED,
        'approved_at' => now(),
        'approved_by' => $user->id,
    ]);

    expect($po->fresh()->status)->toBe(PurchaseOrderStatus::APPROVED);
    expect($po->fresh()->approved_at)->not->toBeNull();
});
