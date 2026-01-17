<?php
// Direct database connection without Laravel
$conn = new mysqli('127.0.0.1', 'root', '', 'siyu');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create periodes table
$sql = "CREATE TABLE IF NOT EXISTS `periodes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama` varchar(255) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "✓ Periodes table created successfully.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

// Insert migration record
$sql2 = "INSERT IGNORE INTO migrations (migration, batch) VALUES ('2026_01_14_000000_create_periodes_table', 0)";
if ($conn->query($sql2) === TRUE) {
    echo "✓ Migration marked as completed.\n";
} else {
    echo "Error inserting migration: " . $conn->error . "\n";
}

$conn->close();
