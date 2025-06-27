<?php

// Load Laravel application
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Mentor;

echo "=== API Response Test ===\n\n";

// Simulate API response for mentors
$mentors = Mentor::all();

echo "API Response (JSON):\n";
echo json_encode([
    'success' => true,
    'data' => $mentors->toArray()
], JSON_PRETTY_PRINT);

echo "\n\n=== Individual Mentor Test ===\n";

// Test first mentor
$mentor = $mentors->first();
if ($mentor) {
    echo "Single Mentor Response:\n";
    echo json_encode([
        'success' => true,
        'data' => $mentor->toArray()
    ], JSON_PRETTY_PRINT);
}

echo "\n=== Test Complete ===\n";
