<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Fixing testimoni paths in database...\n";

try {
    // Get all testimoni records
    $testimonials = DB::table('testimoni')->get();
    
    foreach ($testimonials as $testimonial) {
        $oldPath = $testimonial->foto_testimoni;
        
        if ($oldPath && strpos($oldPath, 'testimoni/') === 0) {
            // Remove 'testimoni/' prefix
            $newPath = substr($oldPath, 10); // Remove 'testimoni/' (10 characters)
            
            echo "Updating testimoni ID {$testimonial->id}: {$oldPath} -> {$newPath}\n";
            
            DB::table('testimoni')
                ->where('id', $testimonial->id)
                ->update(['foto_testimoni' => $newPath]);
        }
    }
    
    echo "Testimoni paths fixed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 