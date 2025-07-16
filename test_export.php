<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\ExportDataController;

// Test basic export functionality
try {
    $controller = new ExportDataController();
    
    // Create a mock request
    $request = new Request([
        'tables' => ['dokumen_beswan'],
        'format' => 'zip',
        'dateRange' => 'all'
    ]);
    
    echo "Testing export functionality...\n";
    $response = $controller->export($request);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response headers: " . json_encode($response->headers->all()) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
