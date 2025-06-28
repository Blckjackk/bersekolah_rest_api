<?php

header('Content-Type: text/plain');

// Simple API test
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

use Illuminate\Http\Request;

echo "=== TESTING API ENDPOINTS ===\n";

try {
    // Test GET /api/artikels
    $request = Request::create('/api/artikels', 'GET');
    $request->headers->set('Accept', 'application/json');
    
    $response = $kernel->handle($request);
    $statusCode = $response->getStatusCode();
    $content = $response->getContent();
    
    echo "GET /api/artikels - Status: {$statusCode}\n";
    
    if ($statusCode === 200) {
        $data = json_decode($content, true);
        if (isset($data['data'])) {
            echo "✓ Response has data key\n";
            echo "✓ Articles count: " . count($data['data']) . "\n";
        } else {
            echo "✗ Response missing data key\n";
        }
    } else {
        echo "✗ Request failed\n";
        echo "Response: " . substr($content, 0, 200) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

$kernel->terminate($request, $response);
