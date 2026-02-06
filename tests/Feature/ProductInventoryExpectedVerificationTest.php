<?php

use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\ProductInventoryExpected;
use App\Models\PurchaseOrder;
use App\Models\ProductOrder;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Department;
use App\Support\AllocationAvailabilityHelper;
use App\Enums\PurchaseOrderStatus;
use Livewire\Livewire;
use App\Livewire\Pages\Warehousestaff\StockIn\Index as StockInIndex;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('phase 1: product_inventory_expected table and model work', function () {
    $product = Product::factory()->create();
    $supplier = Supplier::factory()->create();
    $department = Department::factory()->create();
    $po = PurchaseOrder::create([
        'po_num' => 'PO-VERIFY-' . uniqid(),
        'status' => PurchaseOrderStatus::APPROVED,
        'supplier_id' => $supplier->id,
        'order_date' => now(),
        'ordered_by' => $this->user->id,
        'del_to' => $department->id,
        'total_qty' => 100,
        'total_price' => 1000,
        'approver' => $this->user->id,
    ]);

    $expected = ProductInventoryExpected::create([
        'product_id' => $product->id,
        'purchase_order_id' => $po->id,
        'expected_quantity' => 100,
        'received_quantity' => 0,
    ]);

    expect($expected->id)->not->toBeNull();
    expect($expected->product_id)->toBe($product->id);
    expect($expected->purchase_order_id)->toBe($po->id);
    expect((float) $expected->expected_quantity)->toBe(100.0);
    expect((float) $expected->received_quantity)->toBe(0.0);
    expect($expected->product)->not->toBeNull();
    expect($expected->purchaseOrder)->not->toBeNull();
});

it('phase 2: AllocationAvailabilityHelper reads from ProductInventoryExpected', function () {
    $product = Product::factory()->create();
    ProductInventory::create([
        'product_id' => $product->id,
        'quantity' => 50,
        'reserved_quantity' => 0,
        'available_quantity' => 50,
    ]);
    $supplier = Supplier::factory()->create();
    $department = Department::factory()->create();
    $po = PurchaseOrder::create([
        'po_num' => 'PO-VERIFY-' . uniqid(),
        'status' => PurchaseOrderStatus::APPROVED,
        'supplier_id' => $supplier->id,
        'order_date' => now(),
        'ordered_by' => $this->user->id,
        'del_to' => $department->id,
        'total_qty' => 100,
        'total_price' => 1000,
        'approver' => $this->user->id,
    ]);

    // No ProductInventoryExpected: expected = 0, available = stock only
    expect(AllocationAvailabilityHelper::getExpectedQuantityFromPO($product, $po->id))->toBe(0.0);
    expect(AllocationAvailabilityHelper::getAvailableToAllocate($product, $po->id))->toBe(50.0);

    // With ProductInventoryExpected: expected = 100 - 0 = 100, available = 50 + 100 = 150
    ProductInventoryExpected::create([
        'product_id' => $product->id,
        'purchase_order_id' => $po->id,
        'expected_quantity' => 100,
        'received_quantity' => 0,
    ]);
    expect(AllocationAvailabilityHelper::getExpectedQuantityFromPO($product, $po->id))->toBe(100.0);
    expect(AllocationAvailabilityHelper::getAvailableToAllocate($product, $po->id))->toBe(150.0);

    // Partial received: expected = 100 - 30 = 70
    ProductInventoryExpected::where('product_id', $product->id)->where('purchase_order_id', $po->id)->update(['received_quantity' => 30]);
    expect(AllocationAvailabilityHelper::getExpectedQuantityFromPO($product, $po->id))->toBe(70.0);
});

it('phase 2: getExpectedQuantityFromPO returns 0 when poId is null', function () {
    $product = Product::factory()->create();
    expect(AllocationAvailabilityHelper::getExpectedQuantityFromPO($product, null))->toBe(0.0);
});

it('phase 3: real stock-in increments ProductInventoryExpected.received_quantity', function () {
    $product = Product::factory()->create();
    ProductInventory::create([
        'product_id' => $product->id,
        'quantity' => 0,
        'reserved_quantity' => 0,
        'available_quantity' => 0,
    ]);
    $supplier = Supplier::factory()->create();
    $department = Department::factory()->create();
    $po = PurchaseOrder::create([
        'po_num' => 'PO-VERIFY-' . uniqid(),
        'status' => PurchaseOrderStatus::APPROVED,
        'supplier_id' => $supplier->id,
        'order_date' => now(),
        'ordered_by' => $this->user->id,
        'del_to' => $department->id,
        'total_qty' => 100,
        'total_price' => 1000,
        'approver' => $this->user->id,
    ]);
    $productOrder = ProductOrder::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'quantity' => 100,
        'received_quantity' => 0,
        'unit_price' => 10,
        'total_price' => 1000,
    ]);

    ProductInventoryExpected::create([
        'product_id' => $product->id,
        'purchase_order_id' => $po->id,
        'expected_quantity' => 100,
        'received_quantity' => 0,
    ]);

    $this->actingAs($this->user);

    Livewire::test(StockInIndex::class)
        ->call('processScannedCode', $po->po_num)
        ->assertSet('foundPurchaseOrder.id', $po->id)
        ->set('receivedQuantities', [$productOrder->id => 30])
        ->set('destroyedQuantities', [$productOrder->id => 0])
        ->set('itemStatuses', [$productOrder->id => 'good'])
        ->set('itemRemarks', [$productOrder->id => ''])
        ->set('drNumber', 'DR-TEST-' . uniqid())
        ->call('submitStockInReport');

    $expectedRecord = ProductInventoryExpected::where('product_id', $product->id)->where('purchase_order_id', $po->id)->first();
    expect($expectedRecord)->not->toBeNull();
    expect((float) $expectedRecord->received_quantity)->toBe(30.0);
});
