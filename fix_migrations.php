<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

// Get the database connection
$db = DB::connection();

// Check if migration already exists
$exists = $db->table('migrations')
    ->where('migration', '2026_01_12_000000_create_yudisium_sidings_table')
    ->exists();

if (!$exists) {
    // Insert the migration record
    $db->table('migrations')->insert([
        'migration' => '2026_01_12_000000_create_yudisium_sidings_table',
        'batch' => 1
    ]);
    echo "✓ Migration '2026_01_12_000000_create_yudisium_sidings_table' marked as completed.\n";
} else {
    echo "✓ Migration already marked as completed.\n";
}

// Show status
$migrations = $db->table('migrations')->pluck('migration');
echo "\nCompleted migrations:\n";
foreach ($migrations as $migration) {
    echo "  - $migration\n";
}
