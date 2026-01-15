<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'siyu');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mark the migration as completed
$sql = "INSERT IGNORE INTO migrations (migration, batch) VALUES ('2026_01_15_000000_add_periode_to_submissions_table', 3)";
if ($conn->query($sql) === TRUE) {
    echo "✓ Migration 'add_periode_to_submissions_table' marked as completed.\n";
} else {
    echo "Error: " . $conn->error . "\n";
}

$conn->close();
