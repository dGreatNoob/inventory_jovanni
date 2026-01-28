<?php

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\User;

test('product can be created', function () {
    $category = Category::factory()->create();
    $supplier = Supplier::factory()->create();
    $user = User::factory()->create();

    $product = Product::create([
        'name' => 'Test Product',
        'sku' => 'TEST-SKU-001',
        'barcode' => '1234567890123',
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'price' => 100.00,
        'cost' => 50.00,
        'created_by' => $user->id,
    ]);

    expect($product)->toBeInstanceOf(Product::class);
    expect($product->name)->toBe('Test Product');
    expect($product->sku)->toBe('TEST-SKU-001');
    expect($product->price)->toBe('100.00');
});

test('product can be updated', function () {
    $category = Category::factory()->create();
    $supplier = Supplier::factory()->create();
    $user = User::factory()->create();

    $product = Product::create([
        'name' => 'Test Product',
        'sku' => 'TEST-SKU-001',
        'barcode' => '1234567890123',
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'price' => 100.00,
        'created_by' => $user->id,
    ]);

    $product->update([
        'name' => 'Updated Product',
        'price' => 150.00,
        'updated_by' => $user->id,
    ]);

    expect($product->fresh()->name)->toBe('Updated Product');
    expect($product->fresh()->price)->toBe('150.00');
});

test('product can be retrieved', function () {
    $category = Category::factory()->create();
    $supplier = Supplier::factory()->create();
    $user = User::factory()->create();

    $product = Product::create([
        'name' => 'Test Product',
        'sku' => 'TEST-SKU-002',
        'barcode' => '1234567890124',
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'price' => 100.00,
        'created_by' => $user->id,
    ]);

    $found = Product::find($product->id);

    expect($found)->not->toBeNull();
    expect($found->name)->toBe('Test Product');
});

test('product can be soft deleted', function () {
    $category = Category::factory()->create();
    $supplier = Supplier::factory()->create();
    $user = User::factory()->create();

    $product = Product::create([
        'name' => 'Test Product',
        'sku' => 'TEST-SKU-003',
        'barcode' => '1234567890125',
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'price' => 100.00,
        'created_by' => $user->id,
    ]);

    $productId = $product->id;
    $product->delete();

    expect(Product::find($productId))->toBeNull();
    expect(Product::withTrashed()->find($productId))->not->toBeNull();
});
