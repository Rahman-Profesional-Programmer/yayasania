<?php
require_once __DIR__ . '/../config/database.php';

// Cek apakah tabel top_news sudah ada
$r = $conn->query("SHOW TABLES LIKE 'top_news'");
if ($r && $r->num_rows > 0) {
    echo "TABEL top_news: SUDAH ADA\n";
} else {
    echo "TABEL top_news: BELUM ADA — menjalankan migrasi...\n";
    $sql = file_get_contents(__DIR__ . '/../database/top_news_migration.sql');
    // Hapus baris komentar SQL
    $sql = preg_replace('/^--.*$/m', '', $sql);
    $sql = trim($sql);
    if ($conn->query($sql)) {
        echo "Migrasi berhasil. Tabel top_news dibuat.\n";
    } else {
        echo "Migrasi GAGAL: " . $conn->error . "\n";
    }
}
$conn->close();
