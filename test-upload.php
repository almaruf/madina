<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing image upload functionality...\n\n";

try {
    $product = \App\Models\Product::where('slug', 'test-egg')->first();
    
    if (!$product) {
        echo "ERROR: Product 'test-egg' not found\n";
        exit(1);
    }
    
    echo "Product found: {$product->name} (ID: {$product->id})\n";
    echo "Current images: " . $product->images()->count() . "\n\n";
    
    // Test S3 connection
    echo "Testing S3 connection...\n";
    $disk = \Storage::disk('s3');
    $testFile = 'test-connection-' . time() . '.txt';
    
    try {
        $disk->put($testFile, 'test content');
        echo "✓ S3 upload successful\n";
        
        $exists = $disk->exists($testFile);
        echo "✓ File exists in S3: " . ($exists ? 'YES' : 'NO') . "\n";
        
        $disk->delete($testFile);
        echo "✓ S3 delete successful\n\n";
        
    } catch (\Exception $e) {
        echo "✗ S3 ERROR: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        exit(1);
    }
    
    echo "S3 is working correctly!\n";
    echo "\nThe upload endpoint should work now.\n";
    echo "Try uploading an image through the browser.\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
