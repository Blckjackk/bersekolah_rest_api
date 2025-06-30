<?php

// Test file untuk memverifikasi upload gambar
require_once 'vendor/autoload.php';

use Illuminate\Support\Str;

// Test path
$destination = storage_path('app/public/artikel');
echo "Destination path: " . $destination . "\n";
echo "Path exists: " . (file_exists($destination) ? 'YES' : 'NO') . "\n";
echo "Path is writable: " . (is_writable($destination) ? 'YES' : 'NO') . "\n";

// Test slug generation
$title = "Test Artikel Upload";
$slug = Str::slug($title);
echo "Title: " . $title . "\n";
echo "Generated slug: " . $slug . "\n";

// List existing files
echo "\nExisting files in directory:\n";
if (file_exists($destination)) {
    $files = scandir($destination);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "- " . $file . "\n";
        }
    }
} 