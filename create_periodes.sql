CREATE TABLE IF NOT EXISTS `periodes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama` varchar(255) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO migrations (migration, batch) VALUES ('2026_01_14_000000_create_periodes_table', 0);
