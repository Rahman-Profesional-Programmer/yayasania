-- ============================================================
-- CREATE DATABASE
-- ============================================================
-- CREATE DATABASE IF NOT EXISTS `ihsanul-web`
--   DEFAULT CHARACTER SET utf8mb4
--   COLLATE utf8mb4_general_ci;

-- USE `ihsanul-web`;

-- ============================================================
-- TABEL 1: users
-- ============================================================
-- Sumber kolom:
--   admin/proses_login.php      → email, pass, name_show, enable
--   interface/artikel-show.php  → email, name-show, foto, diskripsi, facebook, instagram
--   admin/koneksi_user.php      → email (penulis default)
-- ============================================================
CREATE TABLE `users` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `email`       VARCHAR(255) NOT NULL UNIQUE,
  `pass`        VARCHAR(255) NOT NULL,
  `name_show`   VARCHAR(255) DEFAULT NULL       COMMENT 'Nama tampil. Di artikel-show.php dibaca sebagai name-show',
  `foto`        VARCHAR(500) DEFAULT NULL       COMMENT 'Path foto profil penulis',
  `diskripsi`   TEXT         DEFAULT NULL        COMMENT 'Bio / deskripsi penulis',
  `facebook`    VARCHAR(255) DEFAULT NULL,
  `instagram`   VARCHAR(255) DEFAULT NULL,
  `role`        ENUM('admin','user') NOT NULL DEFAULT 'user' COMMENT 'Level akses panel admin',
  `enable`      TINYINT(1)   NOT NULL DEFAULT 1  COMMENT '1=aktif, 0=nonaktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL 2: menu_utama
-- ============================================================
-- Sumber kolom:
--   admin/crud-add-menu-proses.php   → urutan, nama_menu, link_menu, link_menu_active, enable
--   admin/crud-edit-menu-proses.php  → id, urutan, nama_menu, link_menu
--   admin/crud-enable-menu-proses.php  → enable = 1
--   admin/crud-disable-menu-proses.php → enable = 0
--   admin/crud-del-menu-proses.php     → DELETE by id
--   interface/home.php, artikel-list.php, artikel-show.php → SELECT WHERE enable=1 ORDER BY urutan
-- ============================================================
CREATE TABLE `menu_utama` (
  `id`               INT AUTO_INCREMENT PRIMARY KEY,
  `urutan`           INT          NOT NULL DEFAULT 0   COMMENT 'Urutan tampil menu',
  `nama_menu`        VARCHAR(255) NOT NULL,
  `link_menu`        VARCHAR(500) DEFAULT NULL,
  `link_menu_active` VARCHAR(10)  DEFAULT '1',
  `enable`           TINYINT(1)   NOT NULL DEFAULT 1   COMMENT '1=tampil, 0=sembunyikan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL 3: sub_menu
-- ============================================================
-- Sumber kolom:
--   admin/crud-add-sub-menu-proses.php   → urutan, nama_menu, link_menu, link_menu_active, menu_utama, enable
--   admin/crud-edit-sub-menu-proses.php  → id, urutan, nama_menu, link_menu, menu_utama
--   admin/crud-enable-sub-menu-proses.php  → enable = 1
--   admin/crud-disable-sub-menu-proses.php → enable = 0
--   admin/crud-del-sub-menu-proses.php     → DELETE by id
-- ============================================================
CREATE TABLE `sub_menu` (
  `id`               INT AUTO_INCREMENT PRIMARY KEY,
  `urutan`           INT          NOT NULL DEFAULT 0,
  `nama_menu`        VARCHAR(255) NOT NULL,
  `link_menu`        VARCHAR(500) DEFAULT NULL,
  `link_menu_active` VARCHAR(10)  DEFAULT '1',
  `menu_utama`       VARCHAR(255) NOT NULL              COMMENT 'Merujuk ke menu_utama.nama_menu',
  `enable`           TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL 4: artikel
-- ============================================================
-- Sumber kolom:
--   admin/crud_artikel_proses.php       → INSERT: judul_artikel, konten_artikel, gambar, penulis, kategori, tanggal_update, viewer, enable, hapus
--   admin/crud-artikel-ena-proses.php   → UPDATE enable = 1
--   admin/crud-artikel-dis-proses.php   → UPDATE enable = 0
--   admin/crud-artikel-del-proses.php   → UPDATE hapus = 0 (soft delete)
--   admin/crud-artikel.php              → SELECT WHERE hapus = 1
--   interface/artikel-list.php          → SELECT WHERE disable = 1 AND hapus = 1
--   interface/artikel-search.php        → SELECT WHERE disable = 1 AND hapus = 1; juga MONTH/YEAR(tanggal_update)
--   interface/artikel-show.php          → SELECT WHERE id_artikel = ?
--
-- CATATAN PENTING:
--   - Admin menggunakan kolom `enable` (1=tampil, 0=sembunyi)
--   - Interface menggunakan kolom `disable` (1=tampil, 0=sembunyi) — logikanya SAMA
--   - Solusi: buat KEDUA kolom + trigger sinkronisasi
-- ============================================================
CREATE TABLE `artikel` (
  `id_artikel`      INT AUTO_INCREMENT PRIMARY KEY,
  `judul_artikel`   VARCHAR(500) NOT NULL,
  `konten_artikel`  LONGTEXT     DEFAULT NULL,
  `gambar`          VARCHAR(500) DEFAULT NULL           COMMENT 'Path relatif: admin/upload_foto/timestamp.ext',
  `penulis`         VARCHAR(255) DEFAULT NULL           COMMENT 'Email penulis, merujuk ke users.email',
  `kategori`        VARCHAR(255) DEFAULT NULL,
  `tanggal_update`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `viewer`          INT          NOT NULL DEFAULT 0,
  `enable`          TINYINT(1)   NOT NULL DEFAULT 1     COMMENT 'Dipakai admin: 1=aktif, 0=nonaktif',
  `disable`         TINYINT(1)   NOT NULL DEFAULT 1     COMMENT 'Dipakai interface: 1=aktif (sama dgn enable)',
  `hapus`           TINYINT(1)   NOT NULL DEFAULT 1     COMMENT '1=ada, 0=soft-deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL 5: artikel_tag
-- ============================================================
-- Sumber kolom:
--   admin/crud_artikel_proses.php   → INSERT: id_artikel, tag
--   interface/artikel-search.php    → JOIN artikel ON id_artikel; SELECT DISTINCT tag
-- ============================================================
CREATE TABLE `artikel_tag` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `id_artikel`  INT          NOT NULL,
  `tag`         VARCHAR(255) NOT NULL,
  INDEX `idx_id_artikel` (`id_artikel`),
  INDEX `idx_tag` (`tag`),
  CONSTRAINT `fk_artikel_tag_artikel`
    FOREIGN KEY (`id_artikel`) REFERENCES `artikel`(`id_artikel`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TRIGGER: sinkronisasi kolom enable ↔ disable pada artikel
-- ============================================================
DELIMITER $$

CREATE TRIGGER trg_artikel_before_insert
BEFORE INSERT ON `artikel`
FOR EACH ROW
BEGIN
  SET NEW.`disable` = NEW.`enable`;
END$$

CREATE TRIGGER trg_artikel_before_update
BEFORE UPDATE ON `artikel`
FOR EACH ROW
BEGIN
  IF NEW.`enable` != OLD.`enable` THEN
    SET NEW.`disable` = NEW.`enable`;
  ELSEIF NEW.`disable` != OLD.`disable` THEN
    SET NEW.`enable` = NEW.`disable`;
  END IF;
END$$

DELIMITER ;

-- ============================================================
-- DATA AWAL (contoh agar web bisa langsung jalan)
-- ============================================================

-- User admin default (password asli: admin123 — sudah disimpan dalam bentuk bcrypt hash)
INSERT INTO `users` (`email`, `pass`, `name_show`, `foto`, `diskripsi`, `facebook`, `instagram`, `role`, `enable`)
VALUES ('ermasmpit@gmail.com', '$2y$10$1gdLVYp4OMFWF66OvYkR7uz.T1DegnEnfQ4p1jwQQca.X3x.frnQm', 'Admin Yayasan', NULL, 'Administrator Yayasan Ihsanul Amal', NULL, NULL, 'admin', 1);

-- Menu utama
INSERT INTO `menu_utama` (`urutan`, `nama_menu`, `link_menu`, `link_menu_active`, `enable`) VALUES
(1, 'Beranda',   'home.php',         '1', 1),
(2, 'Profil',    '#',                '1', 1),
(3, 'Berita',    'artikel-list.php', '1', 1),
(4, 'Kontak',    'kontak.php',       '1', 1);

-- Sub menu contoh
INSERT INTO `sub_menu` (`urutan`, `nama_menu`, `link_menu`, `link_menu_active`, `menu_utama`, `enable`) VALUES
(1, 'Visi Misi',        'visi-misi.php',        '1', 'Profil', 1),
(2, 'Sejarah',          'sejarah.php',           '1', 'Profil', 1),
(3, 'Struktur Organisasi', 'struktur.php',       '1', 'Profil', 1);

-- Artikel contoh
INSERT INTO `artikel` (`judul_artikel`, `konten_artikel`, `gambar`, `penulis`, `kategori`, `tanggal_update`, `viewer`, `enable`, `hapus`)
VALUES ('Selamat Datang di Website Yayasan Ihsanul Amal',
        'Ini adalah artikel pertama di website resmi Yayasan Ihsanul Amal Alabio.',
        NULL,
        'ermasmpit@gmail.com',
        'Umum',
        CURRENT_TIMESTAMP(),
        0, 1, 1);

-- Tag contoh
INSERT INTO `artikel_tag` (`id_artikel`, `tag`) VALUES
(1, 'yayasan'),
(1, 'ihsanul amal'),
(1, 'alabio');