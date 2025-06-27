<?php

// Load Laravel application
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Mentor;

echo "=== Debugging File Paths ===\n\n";

// Test path construction
echo "Current directory: " . __DIR__ . "\n";
echo "dirname(__DIR__, 4): " . dirname(__DIR__, 4) . "\n";

$testPath1 = dirname(__DIR__, 4) . '/bersekolah_website/public/assets/image/mentor/';
echo "Test path 1: {$testPath1}\n";
echo "Test path 1 exists: " . (is_dir($testPath1) ? 'YES' : 'NO') . "\n";

$testPath2 = dirname(__DIR__, 1) . '/bersekolah_website/public/assets/image/mentor/';
echo "Test path 2: {$testPath2}\n";
echo "Test path 2 exists: " . (is_dir($testPath2) ? 'YES' : 'NO') . "\n";

// Test a specific file
$filename = 'Azzam.png';
$fullPath1 = dirname(__DIR__, 4) . '/bersekolah_website/public/assets/image/mentor/' . $filename;
$fullPath2 = dirname(__DIR__, 1) . '/bersekolah_website/public/assets/image/mentor/' . $filename;

echo "\nTesting file: {$filename}\n";
echo "Full path 1: {$fullPath1}\n";
echo "File exists 1: " . (file_exists($fullPath1) ? 'YES' : 'NO') . "\n";
echo "Full path 2: {$fullPath2}\n";
echo "File exists 2: " . (file_exists($fullPath2) ? 'YES' : 'NO') . "\n";

// Test mentor
$mentor = Mentor::where('name', 'Ahmad Izuddin Azzam')->first();
if ($mentor) {
    echo "\nMentor test:\n";
    echo "Photo field: {$mentor->photo}\n";
    echo "Photo URL: {$mentor->photo_url}\n";
}

echo "\n=== Debug Complete ===\n";
