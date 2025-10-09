<?php

// Add this to routes/web.php temporarily for testing
Route::get('/test-product-management', function () {
    try {
        $component = app(\App\Livewire\Pages\ProductManagement\Index::class);
        $products = $component->products;
        $stats = $component->stats;
        
        return response()->json([
            'success' => true,
            'products_type' => get_class($products),
            'products_count' => $products->count(),
            'stats' => $stats,
            'message' => 'Product Management system is working correctly!'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});
