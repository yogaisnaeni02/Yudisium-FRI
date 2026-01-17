<?php
require 'vendor/autoload.php';

$pdo = new PDO('mysql:host=127.0.0.1;dbname=siyu', 'root', '');
$stmt = $pdo->query('DESC users');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Users Table Structure:\n";
echo str_repeat("-", 50) . "\n";
foreach($columns as $col) {
    echo $col['Field'] . " - " . $col['Type'] . "\n";
}
echo str_repeat("-", 50) . "\n";

// Check if foto column exists
$fotoExists = false;
foreach($columns as $col) {
    if($col['Field'] === 'foto') {
        $fotoExists = true;
        break;
    }
}

echo "Foto column exists: " . ($fotoExists ? "YES ✓" : "NO ✗") . "\n";
?>
