<?php

// Simple test to check if export is working
echo "Testing export functionality...\n";

// Test if we can access the BeswanDocument model
try {
    require_once 'bootstrap/app.php';
    
    $app = require_once 'bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    // Test database connection
    $applications = App\Models\BeasiswaApplication::with('beswan.user')->get();
    echo "Found {$applications->count()} applications\n";
    
    // Test document retrieval
    $documents = App\Models\BeswanDocument::all();
    echo "Found {$documents->count()} documents\n";
    
    // Test storage path
    $storagePath = storage_path('app/public');
    echo "Storage path: {$storagePath}\n";
    echo "Storage exists: " . (file_exists($storagePath) ? 'Yes' : 'No') . "\n";
    
    // List files in dokumen-wajib
    $wajibPath = storage_path('app/public/dokumen-wajib/identity_proof');
    if (file_exists($wajibPath)) {
        $files = scandir($wajibPath);
        echo "Files in dokumen-wajib/identity_proof: " . count($files) . "\n";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "  - {$file}\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
