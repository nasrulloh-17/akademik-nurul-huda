-- Update database untuk fitur Administrasi Keuangan.
-- Cara pakai:
-- 1. Buka phpMyAdmin.
-- 2. Klik database website terlebih dahulu.
-- 3. Buka tab SQL.
-- 4. Tempel seluruh isi file ini, lalu klik Go/Kirim.

SET @database_name = DATABASE();

CREATE TABLE IF NOT EXISTS `jenis_tagihan` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nama_tagihan` VARCHAR(255) NOT NULL,
    `keterangan` TEXT NULL,
    `aktif` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `jenis_tagihan_nama_tagihan_unique` (`nama_tagihan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `jenis_tagihan` (`nama_tagihan`, `aktif`, `created_at`, `updated_at`)
VALUES
    ('SPP dan Makan', 1, NOW(), NOW()),
    ('Kelengkapan Sekolah', 1, NOW(), NOW()),
    ('Lainnya', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `aktif` = VALUES(`aktif`),
    `updated_at` = NOW();

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `tagihan` ADD COLUMN `jenis_tagihan_id` BIGINT UNSIGNED NULL AFTER `siswa_id`',
        'SELECT "Kolom jenis_tagihan_id sudah ada"'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @database_name
        AND TABLE_NAME = 'tagihan'
        AND COLUMN_NAME = 'jenis_tagihan_id'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `tagihan` ADD COLUMN `tahun_ajaran_id` BIGINT UNSIGNED NULL AFTER `jenis_tagihan_id`',
        'SELECT "Kolom tahun_ajaran_id sudah ada"'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @database_name
        AND TABLE_NAME = 'tagihan'
        AND COLUMN_NAME = 'tahun_ajaran_id'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `tagihan` ADD COLUMN `periode` VARCHAR(255) NULL AFTER `nama_tagihan`',
        'SELECT "Kolom periode sudah ada"'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @database_name
        AND TABLE_NAME = 'tagihan'
        AND COLUMN_NAME = 'periode'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `tagihan` ADD COLUMN `keterangan` TEXT NULL AFTER `status`',
        'SELECT "Kolom keterangan sudah ada"'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @database_name
        AND TABLE_NAME = 'tagihan'
        AND COLUMN_NAME = 'keterangan'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE `tagihan`
    MODIFY `status` ENUM('belum lunas', 'sebagian', 'lunas') NOT NULL DEFAULT 'belum lunas';

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `tagihan` ADD INDEX `tagihan_jenis_tagihan_id_index` (`jenis_tagihan_id`)',
        'SELECT "Index jenis_tagihan_id sudah ada"'
    )
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = @database_name
        AND TABLE_NAME = 'tagihan'
        AND INDEX_NAME = 'tagihan_jenis_tagihan_id_index'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `tagihan` ADD INDEX `tagihan_tahun_ajaran_id_index` (`tahun_ajaran_id`)',
        'SELECT "Index tahun_ajaran_id sudah ada"'
    )
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = @database_name
        AND TABLE_NAME = 'tagihan'
        AND INDEX_NAME = 'tagihan_tahun_ajaran_id_index'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `tagihan` ADD CONSTRAINT `tagihan_jenis_tagihan_id_foreign` FOREIGN KEY (`jenis_tagihan_id`) REFERENCES `jenis_tagihan` (`id`) ON DELETE SET NULL',
        'SELECT "Foreign key jenis_tagihan_id sudah ada"'
    )
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @database_name
        AND TABLE_NAME = 'tagihan'
        AND CONSTRAINT_NAME = 'tagihan_jenis_tagihan_id_foreign'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `tagihan` ADD CONSTRAINT `tagihan_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE SET NULL',
        'SELECT "Foreign key tahun_ajaran_id sudah ada"'
    )
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @database_name
        AND TABLE_NAME = 'tagihan'
        AND CONSTRAINT_NAME = 'tagihan_tahun_ajaran_id_foreign'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE `tagihan`
JOIN `jenis_tagihan`
    ON `jenis_tagihan`.`nama_tagihan` = `tagihan`.`nama_tagihan`
SET
    `tagihan`.`jenis_tagihan_id` = `jenis_tagihan`.`id`,
    `tagihan`.`updated_at` = NOW()
WHERE `tagihan`.`jenis_tagihan_id` IS NULL;

CREATE TABLE IF NOT EXISTS `pembayaran_tagihan` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tagihan_id` BIGINT UNSIGNED NOT NULL,
    `siswa_id` BIGINT UNSIGNED NOT NULL,
    `petugas_id` BIGINT UNSIGNED NULL,
    `tanggal_bayar` DATE NOT NULL,
    `jumlah_bayar` DECIMAL(12, 2) NOT NULL,
    `metode_bayar` VARCHAR(255) NOT NULL DEFAULT 'tunai',
    `bukti_pembayaran` VARCHAR(255) NULL,
    `keterangan` TEXT NULL,
    `status` ENUM('valid', 'dibatalkan') NOT NULL DEFAULT 'valid',
    `alasan_pembatalan` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `pembayaran_tagihan_tagihan_id_index` (`tagihan_id`),
    KEY `pembayaran_tagihan_siswa_id_index` (`siswa_id`),
    KEY `pembayaran_tagihan_petugas_id_index` (`petugas_id`),
    CONSTRAINT `pembayaran_tagihan_tagihan_id_foreign`
        FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan` (`id`) ON DELETE CASCADE,
    CONSTRAINT `pembayaran_tagihan_siswa_id_foreign`
        FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
    CONSTRAINT `pembayaran_tagihan_petugas_id_foreign`
        FOREIGN KEY (`petugas_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_06_23_000001_tambah_sistem_keuangan', `batch_baru`
FROM (
    SELECT COALESCE(MAX(`batch`), 0) + 1 AS `batch_baru`
    FROM `migrations`
) AS `batch_migration`
WHERE NOT EXISTS (
    SELECT 1
    FROM `migrations`
    WHERE `migration` = '2026_06_23_000001_tambah_sistem_keuangan'
);
