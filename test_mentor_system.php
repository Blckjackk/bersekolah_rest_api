<?php

// Load Laravel application
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Mentor;

// Test the mentor system
echo "=== Testing Mentor System ===\n\n";

// 1. Test retrieving all mentors
echo "1. Testing mentor retrieval:\n";
$mentors = Mentor::all();
echo "Found " . $mentors->count() . " mentors\n\n";

// 2. Test each mentor's photo URL
echo "2. Testing photo URLs:\n";
foreach ($mentors as $mentor) {
    echo "Mentor: {$mentor->name}\n";
    echo "  - Photo field: " . ($mentor->photo ?: 'null') . "\n";
    echo "  - Photo URL: {$mentor->photo_url}\n";
    
    // Check if file actually exists
    $filename = str_replace('mentor/', '', $mentor->photo ?: '');
    $fullPath = dirname(__DIR__, 1) . '/bersekolah_website/public/assets/image/mentor/' . $filename;
    $exists = file_exists($fullPath);
    echo "  - File exists: " . ($exists ? 'YES' : 'NO') . "\n";
    
    if (!$exists && $filename && $filename !== 'default.jpg') {
        echo "  - WARNING: Photo file missing for {$mentor->name}\n";
    }
    echo "\n";
}

// 3. Test default.jpg exists
echo "3. Testing default.jpg:\n";
$defaultPath = dirname(__DIR__, 1) . '/bersekolah_website/public/assets/image/mentor/default.jpg';
echo "Default image exists: " . (file_exists($defaultPath) ? 'YES' : 'NO') . "\n";

// 4. List all files in mentor directory
echo "\n4. Files in mentor directory:\n";
$mentorDir = dirname(__DIR__, 1) . '/bersekolah_website/public/assets/image/mentor';
if (is_dir($mentorDir)) {
    $files = scandir($mentorDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "  - {$file}\n";
        }
    }
} else {
    echo "  - Directory does not exist!\n";
}

echo "\n=== Test Complete ===\n";
