<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\ArtikelController;
use App\Models\Artikel;

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== TESTING ARTIKEL SYSTEM ===\n\n";
    
    // Test 1: Check if Artikel model can connect to database
    echo "1. Testing database connection...\n";
    $artikelModel = new Artikel();
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('konten_bersekolah');
    
    if ($tableExists) {
        echo "✓ Table 'konten_bersekolah' exists\n";
        
        // Check table structure
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('konten_bersekolah');
        echo "✓ Table columns: " . implode(', ', $columns) . "\n";
        
        // Count existing records
        $count = $artikelModel->count();
        echo "✓ Current artikel count: {$count}\n";
    } else {
        echo "✗ Table 'konten_bersekolah' does not exist\n";
        echo "Run: php artisan migrate\n";
    }
    
    echo "\n2. Testing ArtikelController methods...\n";
    
    // Test controller instantiation
    $controller = new ArtikelController();
    echo "✓ ArtikelController instantiated\n";
    
    // Test index method
    $request = Request::create('/api/artikels', 'GET');
    $response = $controller->index($request);
    $responseData = json_decode($response->getContent(), true);
    
    if ($response->getStatusCode() === 200) {
        echo "✓ Index method works - Status: {$response->getStatusCode()}\n";
        echo "✓ Response structure: " . (isset($responseData['data']) ? 'OK' : 'Missing data key') . "\n";
    } else {
        echo "✗ Index method failed - Status: {$response->getStatusCode()}\n";
        echo "Error: " . $response->getContent() . "\n";
    }
    
    echo "\n3. Testing file upload directory...\n";
    
    $uploadPath = public_path('storage/uploads/artikel');
    if (is_dir($uploadPath)) {
        echo "✓ Upload directory exists: {$uploadPath}\n";
        echo "✓ Directory is writable: " . (is_writable($uploadPath) ? 'Yes' : 'No') . "\n";
    } else {
        echo "✗ Upload directory does not exist: {$uploadPath}\n";
        echo "Creating directory...\n";
        if (mkdir($uploadPath, 0755, true)) {
            echo "✓ Directory created successfully\n";
        } else {
            echo "✗ Failed to create directory\n";
        }
    }
    
    echo "\n4. Testing frontend image directory...\n";
    
    $frontendImagePath = __DIR__ . '/../bersekolah_website/public/assets/image/artikel';
    if (is_dir($frontendImagePath)) {
        echo "✓ Frontend image directory exists: {$frontendImagePath}\n";
        
        $defaultImage = $frontendImagePath . '/default.jpg';
        if (file_exists($defaultImage)) {
            echo "✓ Default image exists\n";
        } else {
            echo "✗ Default image missing\n";
        }
    } else {
        echo "✗ Frontend image directory does not exist: {$frontendImagePath}\n";
    }
    
    echo "\n=== TEST COMPLETED ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
