<?php

use App\Models\Product;
use App\Models\ProductPriceHistory;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\User;
use App\Models\ProductColor;
use App\Services\ProductService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Reset Carbon test time
    Carbon::setTestNow();
});

afterEach(function () {
    // Restore Carbon
    Carbon::setTestNow();
});

/**
 * Helper to create a minimal valid product for testing
 */
function createTestProduct(array $attributes = []): Product
{
    static $productCounter = 0;
    $productCounter++;
    
    $category = Category::factory()->create();
    $supplier = Supplier::factory()->create();
    $user = User::factory()->create();
    
    // Create unique product number (6 digits)
    $productNumber = str_pad((string) ($productCounter % 1000000), 6, '0', STR_PAD_LEFT);
    
    // Create unique color code for each test product
    $colorCode = str_pad((string) ($productCounter % 10000), 4, '0', STR_PAD_LEFT);
    $color = ProductColor::firstOrCreate(
        ['code' => $colorCode],
        [
            'name' => 'Test Color ' . $productCounter,
            'shortcut' => 'TC' . $productCounter,
        ]
    );

    $defaults = [
        'entity_id' => 1,
        'product_number' => $productNumber,
        'product_color_id' => $color->id,
        'product_type' => 'regular',
        'sku' => 'TEST-SKU-' . uniqid(),
        'barcode' => $productNumber . $colorCode . '000000', // 16 digits: product_number(6) + color(4) + price(6)
        'name' => 'Test Product',
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'price' => 100.00,
        'price_note' => 'REG1',
        'cost' => 50.00,
        'uom' => 'pcs',
        'disabled' => false,
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ];

    return Product::create(array_merge($defaults, $attributes));
}

test('updateProduct with future effective date stores pending only', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $product = createTestProduct([
        'price' => 100.00,
        'price_note' => 'REG1',
        'product_type' => 'regular',
    ]);

    $service = app(ProductService::class);
    $tomorrow = Carbon::today()->addDay();

    $updated = $service->updateProduct($product, [
        'price' => 899.00,
        'product_type' => 'sale',
        'price_effective_date' => $tomorrow->format('Y-m-d'),
    ]);

    $product->refresh();

    // Current price/note/type should remain unchanged
    expect($product->price)->toBe('100.00');
    expect($product->price_note)->toBe('REG1');
    expect($product->product_type)->toBe('regular');

    // Pending fields should be set
    expect($product->pending_price)->toBe('899.00');
    expect($product->pending_price_note)->not->toBeNull();
    expect($product->pending_price_note)->toStartWith('SAL'); // Should generate SAL note for sale type
    expect($product->price_effective_date->format('Y-m-d'))->toBe($tomorrow->format('Y-m-d'));

    // No price history should be created for pending change
    $historyCount = ProductPriceHistory::where('product_id', $product->id)
        ->where('new_price', 899.00)
        ->count();
    expect($historyCount)->toBe(0);
});

test('updateProduct immediate applies price and clears pending', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $product = createTestProduct([
        'price' => 100.00,
        'price_note' => 'REG1',
    ]);

    $service = app(ProductService::class);

    $updated = $service->updateProduct($product, [
        'price' => 150.00,
        // No price_effective_date
    ]);

    $product->refresh();

    // Price should be updated immediately
    expect($product->price)->toBe('150.00');
    expect($product->price_note)->not->toBe('REG1'); // Should generate next note

    // Pending fields should be cleared
    expect($product->pending_price)->toBeNull();
    expect($product->pending_price_note)->toBeNull();
    expect($product->price_effective_date)->toBeNull();

    // Price history should be created
    $history = ProductPriceHistory::where('product_id', $product->id)
        ->where('old_price', 100.00)
        ->where('new_price', 150.00)
        ->first();
    expect($history)->not->toBeNull();
});

test('updateProduct with effective date today applies immediately', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $product = createTestProduct([
        'price' => 100.00,
    ]);

    $service = app(ProductService::class);
    $today = Carbon::today();

    $updated = $service->updateProduct($product, [
        'price' => 200.00,
        'price_effective_date' => $today->format('Y-m-d'),
    ]);

    $product->refresh();

    // Should apply immediately (today is not future)
    expect($product->price)->toBe('200.00');
    expect($product->pending_price)->toBeNull();
    expect($product->pending_price_note)->toBeNull();
    expect($product->price_effective_date)->toBeNull();

    // Price history should be created
    $history = ProductPriceHistory::where('product_id', $product->id)
        ->where('new_price', 200.00)
        ->first();
    expect($history)->not->toBeNull();
});

test('applyDuePendingPriceForProduct returns false when not due', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $service = app(ProductService::class);

    // Case 1: No price_effective_date
    $product1 = createTestProduct();
    expect($service->applyDuePendingPriceForProduct($product1))->toBeFalse();

    // Case 2: Future effective date
    $product2 = createTestProduct([
        'price_effective_date' => Carbon::today()->addDay(),
        'pending_price' => 200.00,
        'pending_price_note' => 'SAL1',
    ]);
    expect($service->applyDuePendingPriceForProduct($product2))->toBeFalse();
    expect($product2->fresh()->price)->toBe('100.00'); // Unchanged

    // Case 3: No pending fields
    $product3 = createTestProduct([
        'price_effective_date' => Carbon::yesterday(),
    ]);
    expect($service->applyDuePendingPriceForProduct($product3))->toBeFalse();
});

test('applyDuePendingPriceForProduct applies when due and updates product_type', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $product = createTestProduct([
        'price' => 100.00,
        'price_note' => 'REG1',
        'product_type' => 'regular',
        'price_effective_date' => Carbon::yesterday(),
        'pending_price' => 250.00,
        'pending_price_note' => 'SAL1',
    ]);

    $service = app(ProductService::class);

    // Set test time to today so yesterday is in the past
    Carbon::setTestNow(Carbon::today());

    $result = $service->applyDuePendingPriceForProduct($product);

    expect($result)->toBeTrue();

    $product->refresh();

    // Price and note should be updated
    expect($product->price)->toBe('250.00');
    expect($product->price_note)->toBe('SAL1');
    expect($product->product_type)->toBe('sale'); // Should update from SAL note

    // Pending fields should be cleared
    expect($product->pending_price)->toBeNull();
    expect($product->pending_price_note)->toBeNull();
    expect($product->price_effective_date)->toBeNull();

    // Price history should be created
    $history = ProductPriceHistory::where('product_id', $product->id)
        ->where('old_price', 100.00)
        ->where('new_price', 250.00)
        ->where('pricing_note', 'SAL1')
        ->first();
    expect($history)->not->toBeNull();
});

test('applyDuePendingPrices returns correct count and updates only due products', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Carbon::setTestNow(Carbon::parse('2026-01-30')); // Set a known date

    // Product 1: Due (yesterday)
    $product1 = createTestProduct([
        'price' => 100.00,
        'price_effective_date' => Carbon::parse('2026-01-29'),
        'pending_price' => 150.00,
        'pending_price_note' => 'REG2',
    ]);

    // Product 2: Due (today)
    $product2 = createTestProduct([
        'price' => 200.00,
        'price_effective_date' => Carbon::parse('2026-01-30'),
        'pending_price' => 300.00,
        'pending_price_note' => 'REG2',
    ]);

    // Product 3: Not due (tomorrow)
    $product3 = createTestProduct([
        'price' => 300.00,
        'price_effective_date' => Carbon::parse('2026-01-31'),
        'pending_price' => 400.00,
        'pending_price_note' => 'REG2',
    ]);

    $service = app(ProductService::class);
    $count = $service->applyDuePendingPrices();

    expect($count)->toBe(2);

    // Product 1 should be updated
    $product1->refresh();
    expect($product1->price)->toBe('150.00');
    expect($product1->pending_price)->toBeNull();

    // Product 2 should be updated
    $product2->refresh();
    expect($product2->price)->toBe('300.00');
    expect($product2->pending_price)->toBeNull();

    // Product 3 should remain unchanged
    $product3->refresh();
    expect($product3->price)->toBe('300.00');
    expect($product3->pending_price)->toBe('400.00');
});

test('getProductDetails applies due pending price', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Carbon::setTestNow(Carbon::parse('2026-01-30'));

    $product = createTestProduct([
        'price' => 100.00,
        'price_effective_date' => Carbon::parse('2026-01-29'),
        'pending_price' => 99.00,
        'pending_price_note' => 'REG2',
    ]);

    $service = app(ProductService::class);
    $result = $service->getProductDetails($product->id);

    expect($result)->not->toBeNull();
    expect($result->price)->toBe('99.00');
    expect($result->pending_price)->toBeNull();
    expect($result->pending_price_note)->toBeNull();
    expect($result->price_effective_date)->toBeNull();

    // Price history should be created
    $history = ProductPriceHistory::where('product_id', $product->id)
        ->where('new_price', 99.00)
        ->first();
    expect($history)->not->toBeNull();
});
