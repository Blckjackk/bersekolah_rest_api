<?php

// Script to normalize mentor photo data in database to use mentor/ prefix
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Normalizing Mentor Photo Data to mentor/ format ===\n\n";

$mentors = \App\Models\Mentor::all();

foreach ($mentors as $mentor) {
    $originalPhoto = $mentor->photo;
    
    if ($originalPhoto) {
        // Extract just the filename
        $filename = basename($originalPhoto);
        
        // Check if file exists in the mentor directory
        $filePath = public_path('assets/image/mentor/' . $filename);
        
        if (file_exists($filePath)) {
            // Update database to store with mentor/ prefix for consistency
            $newPhotoPath = 'mentor/' . $filename;
            
            if ($originalPhoto !== $newPhotoPath) {
                $mentor->update(['photo' => $newPhotoPath]);
                echo "Updated mentor {$mentor->name}: '{$originalPhoto}' -> '{$newPhotoPath}'\n";
            } else {
                echo "Mentor {$mentor->name} already has correct format: {$originalPhoto}\n";
            }
        } else {
            echo "WARNING: File not found for mentor {$mentor->name}: {$filePath}\n";
            // Set to null so it will use default.jpg
            $mentor->update(['photo' => null]);
            echo "Set to null for mentor {$mentor->name} (will use default.jpg)\n";
        }
    } else {
        echo "Mentor {$mentor->name} has no photo (will use default.jpg)\n";
    }
}

echo "\n=== Data normalization complete ===\n";

// Show updated data
echo "\n=== Updated Mentor Data ===\n";
$mentors = \App\Models\Mentor::fresh();

foreach ($mentors as $mentor) {
    echo "ID: {$mentor->id} | Name: {$mentor->name} | Photo: " . ($mentor->photo ?? 'null') . " | Photo URL: {$mentor->photo_url}\n";
}
