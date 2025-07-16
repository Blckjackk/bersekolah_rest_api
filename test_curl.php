<?php

// Test cURL untuk endpoint export
$url = 'http://localhost:8000/api/export';
$params = [
    'tables' => 'dokumen_beswan',
    'format' => 'zip',
    'dateRange' => 'all'
];

// Build query string
$queryString = http_build_query($params);
$fullUrl = $url . '?' . $queryString;

echo "Testing URL: $fullUrl\n";

// Initialize cURL
$ch = curl_init();

// cURL options
curl_setopt($ch, CURLOPT_URL, $fullUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer test-token' // You'll need to replace with actual token
]);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

// Show results
echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";
if ($error) {
    echo "cURL Error: $error\n";
}
