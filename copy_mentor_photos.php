<?php

// Script to copy existing mentor photos to bersekolah_website
$sourceDir = __DIR__ . '/public/assets/image/mentor';
$destDir = dirname(__DIR__) . '/bersekolah_website/public/assets/image/mentor';

echo "=== Copying Mentor Photos ===\n";
echo "Source: $sourceDir\n";
echo "Destination: $destDir\n\n";

// Create destination directory if it doesn't exist
if (!file_exists($destDir)) {
    mkdir($destDir, 0755, true);
    echo "Created destination directory\n";
}

// List of files to copy (based on current database)
$filesToCopy = [
    'Azzam.png',
    'Ghifari.png', 
    'julian.png',
    'Rhea.png',
    'Erin.png',
    'Dinal.png',
    'Fathir.png',
    'Dedi.png',
    'kakRifat.png',
    'kakAndrian.png',
    'KakSantika.png',
    'KakTyas.png',
    'kakPebi.png',
    '1751007719_tsalitsa.png',
    'default.jpg'
];

foreach ($filesToCopy as $filename) {
    $sourcePath = $sourceDir . '/' . $filename;
    $destPath = $destDir . '/' . $filename;
    
    if (file_exists($sourcePath)) {
        if (copy($sourcePath, $destPath)) {
            echo "Copied: $filename\n";
        } else {
            echo "Failed to copy: $filename\n";
        }
    } else {
        echo "Source file not found: $filename\n";
        
        // Check if we can find it with different casing or name
        $files = glob($sourceDir . '/*');
        foreach ($files as $file) {
            $basename = basename($file);
            if (strtolower($basename) === strtolower($filename)) {
                if (copy($file, $destPath)) {
                    echo "Copied (case different): $basename -> $filename\n";
                    break;
                }
            }
        }
    }
}

echo "\n=== Copy operation completed ===\n";

// List destination directory contents
echo "\nFiles in destination directory:\n";
$destFiles = glob($destDir . '/*');
foreach ($destFiles as $file) {
    echo "- " . basename($file) . "\n";
}
