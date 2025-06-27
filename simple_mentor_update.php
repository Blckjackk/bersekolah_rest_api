<?php

// Simple script to update mentor photos using raw SQL
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Starting mentor photo update script...\n";

$host = 'localhost';
$dbname = 'bersekolah_db';
$username = 'root';
$password = '';

try {
    echo "Connecting to database...\n";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!\n";
    
    echo "=== Manual Mentor Photo Update ===\n\n";
    
    // Manual mapping based on available files and mentor names
    $updates = [
        1 => 'mentor/Azzam.png', // Ahmad Izuddin Azzam
        2 => 'mentor/Ghifari.png', // Abdurrahman Alghifari  
        3 => 'mentor/julian.png', // Julian Dwi
        4 => 'mentor/Rhea.png', // Arya Jagadhita
        5 => 'mentor/Erin.png', // Erin Armaida
        6 => 'mentor/Dinal.png', // Dinal Azmi
        7 => 'mentor/Fathir.png', // Fathir vandarvelis
        8 => 'mentor/Dedi.png', // Dedi
        9 => 'mentor/kakRifat.png', // Rifat Syafaat
        10 => 'mentor/kakAndrian.png', // Andrian Fauzi
        11 => 'mentor/KakSantika.png', // Shantika
        12 => 'mentor/KakTyas.png', // Tyas Ningrum
        13 => 'mentor/kakPebi.png', // Pebi Sukamdani
        17 => 'mentor/1751006443_tsalitsa.png' // tsalitsa (keep current)
    ];
    
    foreach ($updates as $id => $photoPath) {
        echo "Updating mentor ID $id to $photoPath\n";
        $stmt = $pdo->prepare("UPDATE mentors SET photo = ? WHERE id = ?");
        $result = $stmt->execute([$photoPath, $id]);
        echo "Result: " . ($result ? "Success" : "Failed") . "\n";
    }
    
    echo "\n=== Update complete ===\n";
    
    // Show final results
    echo "\n=== Final Mentor Data ===\n";
    $stmt = $pdo->query("SELECT id, name, email, photo FROM mentors ORDER BY id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']} | Name: {$row['name']} | Photo: {$row['photo']}\n";
    }
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}

echo "Script completed.\n";
