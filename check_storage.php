<?php
/**
 * Storage Diagnostic Script
 * Run this to check if storage is properly configured
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Storage Configuration Check ===\n\n";

// Check symlink
$symlinkPath = public_path('storage');
$targetPath = storage_path('app/public');

echo "1. Symlink Check:\n";
if (is_link($symlinkPath)) {
    echo "   ✓ Symlink exists: $symlinkPath\n";
    echo "   → Points to: " . readlink($symlinkPath) . "\n";
    if (readlink($symlinkPath) === $targetPath) {
        echo "   ✓ Symlink target is correct\n";
    } else {
        echo "   ✗ Symlink target is WRONG! Should point to: $targetPath\n";
    }
} else {
    echo "   ✗ Symlink does NOT exist at: $symlinkPath\n";
    echo "   Run: php artisan storage:link\n";
}

// Check storage directory
echo "\n2. Storage Directory:\n";
$photosDir = storage_path('app/public/photos');
if (is_dir($photosDir)) {
    echo "   ✓ Photos directory exists: $photosDir\n";
    $files = glob($photosDir . '/*');
    echo "   → Found " . count($files) . " files\n";
    if (count($files) > 0) {
        $sampleFile = basename($files[0]);
        echo "   → Sample file: $sampleFile\n";
    }
} else {
    echo "   ✗ Photos directory does NOT exist: $photosDir\n";
}

// Check permissions
echo "\n3. Permissions:\n";
if (is_dir($photosDir)) {
    $perms = substr(sprintf('%o', fileperms($photosDir)), -4);
    echo "   Photos directory permissions: $perms\n";
    if ($perms >= '0755') {
        echo "   ✓ Permissions are OK\n";
    } else {
        echo "   ⚠ Permissions might be too restrictive (should be 755 or 775)\n";
    }
}

// Check URL generation
echo "\n4. URL Generation:\n";
$appUrl = config('app.url');
echo "   APP_URL: $appUrl\n";

if (!empty($files)) {
    $testFile = basename($files[0]);
    $storageUrl = Storage::disk('public')->url('photos/' . $testFile);
    $assetUrl = asset('storage/photos/' . $testFile);
    
    echo "   Storage::url(): $storageUrl\n";
    echo "   asset(): $assetUrl\n";
    
    if (strpos($storageUrl, $appUrl) !== false) {
        echo "   ✓ Storage URL includes APP_URL\n";
    } else {
        echo "   ⚠ Storage URL does not match APP_URL\n";
    }
}

// Check if files are accessible
echo "\n5. File Accessibility:\n";
if (!empty($files)) {
    $testFile = $files[0];
    if (is_readable($testFile)) {
        echo "   ✓ Sample file is readable: " . basename($testFile) . "\n";
    } else {
        echo "   ✗ Sample file is NOT readable: " . basename($testFile) . "\n";
    }
}

echo "\n=== Recommendations ===\n";
if (!is_link($symlinkPath)) {
    echo "1. Create storage symlink: php artisan storage:link\n";
}
if ($appUrl === 'http://localhost:8000') {
    echo "2. Update APP_URL in .env to your production domain\n";
}
echo "3. Ensure web server (nginx/apache) has read access to storage/app/public\n";
echo "4. Clear config cache: php artisan config:clear\n";
echo "5. Clear route cache: php artisan route:clear\n";
echo "6. Clear view cache: php artisan view:clear\n";

