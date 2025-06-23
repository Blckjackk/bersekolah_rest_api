<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'auth/*'],
    
    'allowed_origins' => ['*'], // Allow all origins to connect to the API
    
    'allowed_methods' => ['*'],
    
    'allowed_headers' => ['*'],
    
    'exposed_headers' => [],
    
    'max_age' => 0,
    
    // Set to false since these are public routes that don't need cookies/credentials
    'supports_credentials' => false,
];