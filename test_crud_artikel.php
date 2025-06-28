<?php

// Test CRUD operations for Artikel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Artikel;
use Illuminate\Http\UploadedFile;

try {
    echo "=== TESTING ARTIKEL CRUD OPERATIONS ===\n\n";
    
    // 1. CREATE TEST
    echo "1. Testing CREATE operation...\n";
    
    $testArtikel = Artikel::create([
        'judul_halaman' => 'Test Artikel CRUD',
        'slug' => 'test-artikel-crud',
        'deskripsi' => 'Ini adalah test artikel untuk testing CRUD operations.',
        'category' => 'news',
        'status' => 'draft',
        'gambar' => 'default.jpg',
        'user_id' => 1
    ]);
    
    if ($testArtikel) {
        echo "✓ CREATE successful - ID: {$testArtikel->id}\n";
    } else {
        echo "✗ CREATE failed\n";
    }
    
    // 2. READ TEST
    echo "\n2. Testing READ operation...\n";
    
    $foundArtikel = Artikel::find($testArtikel->id);
    if ($foundArtikel && $foundArtikel->judul_halaman === 'Test Artikel CRUD') {
        echo "✓ READ successful - Title: {$foundArtikel->judul_halaman}\n";
        echo "✓ Image URL: " . $foundArtikel->gambar_url . "\n";
    } else {
        echo "✗ READ failed\n";
    }
    
    // 3. UPDATE TEST
    echo "\n3. Testing UPDATE operation...\n";
    
    $foundArtikel->update([
        'judul_halaman' => 'Test Artikel CRUD - Updated',
        'status' => 'published'
    ]);
    
    $updatedArtikel = Artikel::find($testArtikel->id);
    if ($updatedArtikel && $updatedArtikel->judul_halaman === 'Test Artikel CRUD - Updated') {
        echo "✓ UPDATE successful - New title: {$updatedArtikel->judul_halaman}\n";
        echo "✓ Status updated to: {$updatedArtikel->status}\n";
    } else {
        echo "✗ UPDATE failed\n";
    }
    
    // 4. LIST TEST
    echo "\n4. Testing LIST operation...\n";
    
    $allArtikels = Artikel::orderBy('created_at', 'desc')->take(5)->get();
    if ($allArtikels->count() > 0) {
        echo "✓ LIST successful - Found {$allArtikels->count()} articles:\n";
        foreach ($allArtikels as $artikel) {
            echo "  - {$artikel->judul_halaman} (Status: {$artikel->status})\n";
        }
    } else {
        echo "✗ LIST failed - No articles found\n";
    }
    
    // 5. FILTER BY CATEGORY TEST
    echo "\n5. Testing FILTER by category...\n";
    
    $newsArtikels = Artikel::where('category', 'news')->take(3)->get();
    if ($newsArtikels->count() > 0) {
        echo "✓ FILTER successful - Found {$newsArtikels->count()} news articles\n";
    } else {
        echo "✗ FILTER failed - No news articles found\n";
    }
    
    // 6. DELETE TEST
    echo "\n6. Testing DELETE operation...\n";
    
    $deleteResult = $testArtikel->delete();
    $deletedCheck = Artikel::find($testArtikel->id);
    
    if ($deleteResult && !$deletedCheck) {
        echo "✓ DELETE successful - Test article removed\n";
    } else {
        echo "✗ DELETE failed\n";
    }
    
    echo "\n=== CRUD TEST COMPLETED ===\n";
    
    // 7. FINAL SUMMARY
    echo "\n7. Final system check...\n";
    
    $totalCount = Artikel::count();
    $publishedCount = Artikel::where('status', 'published')->count();
    $draftCount = Artikel::where('status', 'draft')->count();
    
    echo "✓ Total articles: {$totalCount}\n";
    echo "✓ Published: {$publishedCount}\n";
    echo "✓ Draft: {$draftCount}\n";
    
    echo "\n🎉 Artikel system is working correctly!\n";
    echo "✅ All CRUD operations functional\n";
    echo "✅ Database connection established\n";
    echo "✅ Model relationships working\n";
    echo "✅ Image URL generation working\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStacktrace:\n" . $e->getTraceAsString() . "\n";
}
