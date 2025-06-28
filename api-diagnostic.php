#!/usr/bin/env php
<?php

/**
 * API Diagnostic Tool for Bersekolah REST API
 * 
 * This script helps diagnose and fix common API issues:
 * - CORS configuration issues
 * - Authentication middleware issues
 * - Route configuration problems
 * 
 * How to use:
 * 1. Copy this file to the root of your Laravel project
 * 2. Make it executable: chmod +x api-diagnostic.php
 * 3. Run it: php api-diagnostic.php
 */

$projectRoot = __DIR__;

// Check if we're in a Laravel project
if (!file_exists($projectRoot . '/artisan')) {
    echo "Error: This script must be run from the root of a Laravel project.\n";
    exit(1);
}

echo "=================================================================\n";
echo "Bersekolah REST API Diagnostic Tool\n";
echo "=================================================================\n\n";

// Check CORS configuration
echo "Checking CORS configuration...\n";
$corsConfig = file_get_contents($projectRoot . '/config/cors.php');

if (strpos($corsConfig, "'allowed_origins' => ['*']") !== false) {
    echo "✅ CORS is configured to allow all origins\n";
} else {
    echo "⚠️ CORS may not be configured to allow all origins\n";
    echo "   Recommended fix: set 'allowed_origins' => ['*'] in config/cors.php\n";
}

if (strpos($corsConfig, "'supports_credentials' => false") !== false) {
    echo "✅ CORS is configured to not require credentials\n";
} else {
    echo "⚠️ CORS may be requiring credentials\n";
    echo "   Recommended fix: set 'supports_credentials' => false in config/cors.php\n";
}

// Check API routes
echo "\nChecking API routes...\n";

$apiRoutes = file_get_contents($projectRoot . '/routes/api.php');

if (strpos($apiRoutes, "'/announcements', [AnnouncementController::class") !== false) {
    echo "✅ Found announcements route\n";
    
    if (strpos($apiRoutes, "withoutMiddleware(['auth', 'auth:sanctum']") !== false) {
        echo "✅ Announcements route has authentication middleware explicitly disabled\n";
    } else {
        echo "⚠️ Announcements route may be subject to authentication middleware\n";
        echo "   Recommended fix: add ->withoutMiddleware(['auth', 'auth:sanctum']) to the route\n";
    }
} else {
    echo "❌ Could not find announcements route\n";
}

// Check debug routes
if (strpos($apiRoutes, "'/debug/") !== false) {
    echo "✅ Debug routes are available\n";
} else {
    echo "⚠️ No debug routes found\n";
    echo "   Recommended fix: Add diagnostic endpoints as shown in the documentation\n";
}

// Check AnnouncementController
echo "\nChecking AnnouncementController...\n";
$controllerPath = $projectRoot . '/app/Http/Controllers/AnnouncementController.php';

if (file_exists($controllerPath)) {
    $controller = file_get_contents($controllerPath);
    
    if (strpos($controller, "getPublishedAnnouncements") !== false) {
        echo "✅ Found getPublishedAnnouncements method\n";
        
        if (strpos($controller, "withHeaders") !== false) {
            echo "✅ Controller is setting CORS headers explicitly\n";
        } else {
            echo "⚠️ Controller is not setting CORS headers explicitly\n";
            echo "   Recommended fix: Add CORS headers to the response\n";
        }
    } else {
        echo "❌ Could not find getPublishedAnnouncements method\n";
    }
} else {
    echo "❌ Could not find AnnouncementController.php\n";
}

// Test the API endpoint
echo "\nWould you like to test the API endpoint? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if (strtolower($line) === 'y') {
    echo "Testing API endpoint...\n";
    
    $curl = curl_init("http://localhost:8000/api/announcements");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    echo "Response Code: " . $httpCode . "\n";
    
    if ($httpCode >= 200 && $httpCode < 300) {
        echo "✅ API endpoint returned success!\n";
        echo "Response preview: " . substr($response, 0, 100) . "...\n";
    } else if ($httpCode === 401) {
        echo "❌ API endpoint returned 401 Unauthorized\n";
        echo "   This confirms the authentication issue. Check that you've applied the recommended fixes.\n";
    } else {
        echo "❌ API endpoint returned error: " . $httpCode . "\n";
        echo "Response: " . $response . "\n";
    }
    
    curl_close($curl);
    
    // Also try the debug endpoint
    echo "\nTesting debug endpoint...\n";
    
    $curl = curl_init("http://localhost:8000/api/debug/announcements");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    echo "Debug Response Code: " . $httpCode . "\n";
    
    if ($httpCode >= 200 && $httpCode < 300) {
        echo "✅ Debug endpoint returned success!\n";
        echo "Response preview: " . substr($response, 0, 100) . "...\n";
    } else {
        echo "❌ Debug endpoint returned error: " . $httpCode . "\n";
        echo "Response: " . $response . "\n";
    }
    
    curl_close($curl);
}

echo "\n=================================================================\n";
echo "Summary:\n";
echo "=================================================================\n";
echo "If you're experiencing 401 Unauthorized errors with your API, here's what to do:\n\n";
echo "1. Make sure CORS is configured correctly:\n";
echo "   - Set 'allowed_origins' => ['*']\n";
echo "   - Set 'supports_credentials' => false\n\n";
echo "2. Ensure the announcements route is not protected by auth middleware:\n";
echo "   - Add ->withoutMiddleware(['auth', 'auth:sanctum'])\n\n";
echo "3. Add explicit CORS headers to your controller responses\n\n";
echo "4. Restart your Laravel server after making changes:\n";
echo "   php artisan serve\n\n";
echo "5. Test from the frontend using the diagnostic page:\n";
echo "   /debug/api-test\n\n";
echo "=================================================================\n";
