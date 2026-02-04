<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing S3 File Upload...\n\n";

try {
    $disk = \Storage::disk('s3');
    
    // Test 1: Upload a string
    echo "Test 1: Uploading text file...\n";
    $textPath = $disk->put('test/text.txt', 'Hello World');
    echo "Text file path: " . ($textPath ?: 'EMPTY') . "\n";
    
    if ($textPath) {
        $url = $disk->url($textPath);
        echo "Text file URL: " . $url . "\n";
        $disk->delete($textPath);
        echo "✓ Text upload successful\n\n";
    } else {
        echo "✗ Text upload failed - path is empty\n\n";
    }
    
    // Test 2: Create a temporary file and upload it
    echo "Test 2: Uploading file...\n";
    $tmpFile = tempnam(sys_get_temp_dir(), 'test');
    file_put_contents($tmpFile, 'Test content');
    
    $uploadedFile = new \Illuminate\Http\UploadedFile($tmpFile, 'test.txt', 'text/plain', null, true);
    
    $filePath = $disk->putFileAs('test', $uploadedFile, 'uploaded-test.txt', 'public');
    echo "Uploaded file path: " . ($filePath ?: 'EMPTY') . "\n";
    
    if ($filePath) {
        $url = $disk->url($filePath);
        echo "Uploaded file URL: " . $url . "\n";
        $disk->delete($filePath);
        echo "✓ File upload successful\n\n";
    } else {
        echo "✗ File upload failed - path is empty\n\n";
    }
    
    unlink($tmpFile);
    
    echo "S3 Configuration:\n";
    echo "Bucket: " . config('filesystems.disks.s3.bucket') . "\n";
    echo "Region: " . config('filesystems.disks.s3.region') . "\n";
    echo "Key: " . (config('filesystems.disks.s3.key') ? 'SET' : 'NOT SET') . "\n";
    echo "Secret: " . (config('filesystems.disks.s3.secret') ? 'SET' : 'NOT SET') . "\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
