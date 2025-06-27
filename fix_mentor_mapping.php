<?php

// Script to manually fix mentor photo mapping
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Manual Mentor Photo Mapping ===\n\n";

// Manual mapping based on available files and mentor names
$manualMapping = [
    1 => 'Azzam.png', // Ahmad Izuddin Azzam
    2 => 'Ghifari.png', // Abdurrahman Alghifari  
    3 => 'julian.png', // Julian Dwi
    4 => 'Rhea.png', // Arya Jagadhita
    5 => 'Erin.png', // Erin Armaida
    6 => 'Dinal.png', // Dinal Azmi
    7 => 'Fathir.png', // Fathir vandarvelis
    8 => 'Dedi.png', // Dedi
    9 => 'kakRifat.png', // Rifat Syafaat
    10 => 'kakAndrian.png', // Andrian Fauzi
    11 => 'KakSantika.png', // Shantika
    12 => 'KakTyas.png', // Tyas Ningrum
    13 => 'kakPebi.png', // Pebi Sukamdani
    // ID 17 will keep its current photo
];

$mentors = \App\Models\Mentor::all();

foreach ($mentors as $mentor) {
    if (isset($manualMapping[$mentor->id])) {
        $filename = $manualMapping[$mentor->id];
        $filePath = public_path('assets/image/mentor/' . $filename);
        
        if (file_exists($filePath)) {
            $newPhotoPath = 'mentor/' . $filename;
            $mentor->update(['photo' => $newPhotoPath]);
            echo "Updated mentor {$mentor->name} (ID: {$mentor->id}): 'mentor/{$filename}'\n";
        } else {
            echo "WARNING: File not found for mentor {$mentor->name}: {$filePath}\n";
        }
    } else {
        // Keep existing photo for new entries (like ID 17)
        if ($mentor->photo && !str_starts_with($mentor->photo, 'mentor/')) {
            $filename = basename($mentor->photo);
            $mentor->update(['photo' => 'mentor/' . $filename]);
            echo "Updated mentor {$mentor->name} (ID: {$mentor->id}): 'mentor/{$filename}'\n";
        } else {
            echo "Mentor {$mentor->name} (ID: {$mentor->id}) already has correct format or no photo\n";
        }
    }
}

echo "\n=== Manual mapping complete ===\n";

// Show final data
echo "\n=== Final Mentor Data ===\n";
$mentors = \App\Models\Mentor::fresh();

foreach ($mentors as $mentor) {
    echo "ID: {$mentor->id} | Name: {$mentor->name} | Photo: " . ($mentor->photo ?? 'null') . " | Photo URL: {$mentor->photo_url}\n";
}
