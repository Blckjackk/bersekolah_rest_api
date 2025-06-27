<?php

// Simple script to clean mentor photo paths in database
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Starting database cleanup script...\n";

$host = 'localhost';
$dbname = 'bersekolah_db';
$username = 'root';
$password = '';

try {
    echo "Connecting to database...\n";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!\n";
    
    echo "=== Cleaning Mentor Photo Paths ===\n\n";
    
    // Get all mentors
    $stmt = $pdo->query("SELECT id, name, photo FROM mentors WHERE photo IS NOT NULL AND photo != ''");
    $mentors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($mentors as $mentor) {
        $originalPhoto = $mentor['photo'];
        
        // Remove mentor/ prefix if it exists and get just the filename
        $cleanFilename = str_replace('mentor/', '', $originalPhoto);
        $cleanFilename = basename($cleanFilename); // Get just the filename part
        
        if ($originalPhoto !== $cleanFilename) {
            $updateStmt = $pdo->prepare("UPDATE mentors SET photo = ? WHERE id = ?");
            $result = $updateStmt->execute([$cleanFilename, $mentor['id']]);
            echo "Updated mentor ID {$mentor['id']} ({$mentor['name']}): '{$originalPhoto}' -> '{$cleanFilename}'\n";
        } else {
            echo "Mentor ID {$mentor['id']} ({$mentor['name']}) already clean: '{$originalPhoto}'\n";
        }
    }
    
    echo "\n=== Cleanup complete ===\n";
    
    // Show final results
    echo "\n=== Final Mentor Data ===\n";
    $stmt = $pdo->query("SELECT id, name, email, photo FROM mentors ORDER BY id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']} | Name: {$row['name']} | Photo: " . ($row['photo'] ?: 'null') . "\n";
    }
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}

echo "Script completed.\n";
