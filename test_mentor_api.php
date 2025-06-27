<?php

// Test script to check mentor API response
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;

// Create a simple test to check mentor model behavior
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test Mentor model directly
$mentors = \App\Models\Mentor::all();

echo "=== Testing Mentor Model ===\n";
echo "Total mentors: " . $mentors->count() . "\n\n";

foreach ($mentors as $mentor) {
    echo "ID: " . $mentor->id . "\n";
    echo "Name: " . $mentor->name . "\n";
    echo "Email: " . $mentor->email . "\n";
    echo "Photo (DB): " . ($mentor->photo ?? 'null') . "\n";
    echo "Photo URL (accessor): " . $mentor->photo_url . "\n";
    echo "JSON representation:\n";
    echo json_encode($mentor->toArray(), JSON_PRETTY_PRINT) . "\n";
    echo "---\n\n";
}
