-- ============================================================
-- Migrasi: Tabel top_news
-- Menyimpan konfigurasi 5 berita utama banner halaman depan
-- Jalankan sekali di database: ihsanul-web
-- ============================================================

CREATE TABLE IF NOT EXISTS `top_news` (
  `posisi`      TINYINT(1)   NOT NULL COMMENT 'Posisi banner 1-5 (1=Tengah Besar, 2=Kiri Atas, 3=Kiri Bawah, 4=Kanan Atas, 5=Kanan Bawah)',
  `id_artikel`  INT(11)      NOT NULL COMMENT 'ID artikel yang dipilih dari tabel artikel',
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Waktu terakhir diperbarui',
  PRIMARY KEY (`posisi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Konfigurasi 5 berita utama banner halaman depan';
