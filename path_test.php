<?php

// Test ABSOLUTE path (final approach)
echo "=== ABSOLUTE Path Test ===\n";

$destinationPath = 'C:\Users\mp2k5\Documents\GitHub\Project_Prokon\bersekolah_website\public\assets\image\mentor';

echo "Destination path: $destinationPath\n";
echo "Path exists: " . (file_exists($destinationPath) ? 'YES' : 'NO') . "\n";
echo "Path writable: " . (is_writable($destinationPath) ? 'YES' : 'NO') . "\n";

// Test file creation
if (file_exists($destinationPath)) {
    $testFile = $destinationPath . '\test_upload.txt';
    $testContent = 'Test file creation: ' . date('Y-m-d H:i:s');
    
    $writeResult = file_put_contents($testFile, $testContent);
    echo "Test file write: " . ($writeResult !== false ? 'SUCCESS' : 'FAILED') . "\n";
    
    if ($writeResult !== false && file_exists($testFile)) {
        unlink($testFile); // Clean up test file
        echo "Test file cleanup: SUCCESS\n";
    }
    
    echo "\nREADY FOR UPLOAD TESTING!\n";
}

echo "\n=== End Test ===\n";
