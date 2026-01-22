<?php
/**
 * Database Table Usage Verification Script
 * 
 * This script verifies which tables from migrations are actually used in the codebase.
 * Run: php verify_unused_tables.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Documented active tables (from module analysis)
$documentedTables = [
    // Core Tables
    'users', 'products', 'categories', 'suppliers', 'branches', 'departments', 'activity_log',
    
    // Purchase Order Tables
    'purchase_orders', 'product_orders', 'purchase_order_approval_logs', 'purchase_order_deliveries',
    
    // Product Tables
    'product_colors', 'product_images', 'product_price_histories', 'product_inventory',
    'inventory_movements', 'inventory_locations', 'product_batches', 'branch_product',
    
    // Allocation Tables
    'batch_allocations', 'branch_allocations', 'branch_allocation_items',
    
    // Sales Tables
    'sales_receipts', 'sales_receipt_items', 'sales_orders', 'promos',
    
    // Finance Tables
    'finances', 'payments',
    
    // Agent & Branch Tables
    'agents', 'agent_branch_assignments',
    
    // Shipment Tables
    'shipments', 'shipment_status_logs', 'customers',
    
    // Permission Tables
    'roles', 'permissions', 'role_has_permissions', 'model_has_roles', 'model_has_permissions',
];

// System/Infrastructure tables (keep)
$systemTables = [
    'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs',
    'sessions', 'password_reset_tokens', 'migrations',
];

// Tables already cleaned up
$cleanedUpTables = [
    'item_classes', 'stock_batches', 'logs',
];

// Potential unused tables to verify
$tablesToVerify = [
    'raw_mat_profiles',
    'raw_mat_orders',
    'raw_mat_invs',
    'request_slips',
    'sales_returns',
    'sales_return_items',
    'sales_order_branch_items',
    'sales_prices',
    'supply_profiles',
    'supply_orders',
    'supply_batches',
    'notifications',
    'item_types',
    'purchase_order_items',
];

echo "=== Database Table Usage Verification ===\n\n";

// Check each table
$results = [];

foreach ($tablesToVerify as $table) {
    echo "Checking: $table\n";
    echo str_repeat('-', 50) . "\n";
    
    $result = [
        'table' => $table,
        'model_exists' => false,
        'model_path' => null,
        'codebase_references' => 0,
        'database_exists' => false,
        'record_count' => 0,
        'has_foreign_keys' => false,
        'referenced_by' => [],
    ];
    
    // Check if model exists
    $modelName = str_replace('_', '', ucwords($table, '_'));
    $modelPath = "app/Models/$modelName.php";
    
    if (file_exists($modelPath)) {
        $result['model_exists'] = true;
        $result['model_path'] = $modelPath;
        echo "  ✓ Model exists: $modelPath\n";
    } else {
        echo "  ✗ Model not found\n";
    }
    
    // Check codebase usage
    $grepCommand = "grep -r '$table' app/ database/ resources/ 2>/dev/null | grep -v 'Binary' | wc -l";
    $grepResult = trim(shell_exec($grepCommand) ?: '0');
    $result['codebase_references'] = (int)$grepResult;
    echo "  Codebase references: {$result['codebase_references']}\n";
    
    // Check database
    try {
        $exists = \Illuminate\Support\Facades\Schema::hasTable($table);
        $result['database_exists'] = $exists;
        
        if ($exists) {
            $count = \Illuminate\Support\Facades\DB::table($table)->count();
            $result['record_count'] = $count;
            echo "  ✓ Table exists in database\n";
            echo "  Records: $count\n";
            
            // Check for foreign keys
            $fks = \Illuminate\Support\Facades\DB::select("
                SELECT 
                    TABLE_NAME,
                    COLUMN_NAME,
                    CONSTRAINT_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$table]);
            
            if (!empty($fks)) {
                $result['has_foreign_keys'] = true;
                echo "  Foreign keys: " . count($fks) . "\n";
            }
            
            // Check what tables reference this table
            $referencedBy = \Illuminate\Support\Facades\DB::select("
                SELECT 
                    TABLE_NAME,
                    COLUMN_NAME,
                    CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND REFERENCED_TABLE_NAME = ?
            ", [$table]);
            
            if (!empty($referencedBy)) {
                $result['referenced_by'] = array_map(function($ref) {
                    return $ref->TABLE_NAME;
                }, $referencedBy);
                echo "  Referenced by: " . implode(', ', $result['referenced_by']) . "\n";
            }
        } else {
            echo "  ✗ Table does not exist in database\n";
        }
    } catch (\Exception $e) {
        echo "  ✗ Error checking database: " . $e->getMessage() . "\n";
    }
    
    $results[] = $result;
    echo "\n";
}

// Summary
echo "\n=== SUMMARY ===\n\n";

$unusedTables = [];
$usedTables = [];
$needsReview = [];

foreach ($results as $result) {
    $isUnused = !$result['model_exists'] 
        && $result['codebase_references'] == 0 
        && $result['record_count'] == 0
        && empty($result['referenced_by']);
    
    $isUsed = $result['model_exists'] 
        || $result['codebase_references'] > 0 
        || $result['record_count'] > 0
        || !empty($result['referenced_by']);
    
    if ($isUnused) {
        $unusedTables[] = $result['table'];
    } elseif ($isUsed) {
        $usedTables[] = $result['table'];
    } else {
        $needsReview[] = $result['table'];
    }
}

echo "Unused Tables (Safe to Remove):\n";
if (empty($unusedTables)) {
    echo "  None found\n";
} else {
    foreach ($unusedTables as $table) {
        echo "  - $table\n";
    }
}

echo "\nUsed Tables (Keep):\n";
if (empty($usedTables)) {
    echo "  None found\n";
} else {
    foreach ($usedTables as $table) {
        echo "  - $table\n";
    }
}

echo "\nNeeds Review:\n";
if (empty($needsReview)) {
    echo "  None\n";
} else {
    foreach ($needsReview as $table) {
        echo "  - $table\n";
    }
}

// Save results to JSON
file_put_contents('docs/db_cleanup/verification_results.json', json_encode($results, JSON_PRETTY_PRINT));
echo "\n✓ Results saved to docs/db_cleanup/verification_results.json\n";

