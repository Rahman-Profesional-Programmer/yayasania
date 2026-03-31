<?php
// ============================================================
// Konfigurasi Database & Aplikasi
// Satu file untuk semua koneksi — admin/ dan public/
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'rootplt');
define('DB_PASS', 'PLT,./7788()__db');
define('DB_NAME', 'ihsanul-web');

// URL dasar aplikasi (sesuaikan dengan konfigurasi nginx)
define('BASE_URL', 'http://localhost/sania/');

// URL untuk assets admin dan public
define('ADMIN_URL',    BASE_URL . 'admin/');
define('ADMIN_ASSETS', BASE_URL . 'admin/assets/');
define('PUBLIC_URL',   BASE_URL . 'public/');
define('PUBLIC_ASSETS', BASE_URL . 'public/assets/');

// Email admin default (penulis artikel)
define('DEFAULT_USER_EMAIL', 'ermasmpit@gmail.com');

// Path fisik folder upload (pakai DIRECTORY_SEPARATOR agar cross-OS)
define('UPLOAD_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'foto' . DIRECTORY_SEPARATOR);

// URL publik folder upload
define('UPLOAD_URL', BASE_URL . 'storage/uploads/foto/');

// ============================================================
// Koneksi database
// ============================================================
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
