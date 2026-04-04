<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireLogin();

// Hanya izinkan request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(ADMIN_URL . 'top-news/index.php');
}

$input = $_POST['artikel'] ?? [];

// ── Validasi: harus array dengan kunci 1–5 ───────────────────────
$valid = true;
for ($pos = 1; $pos <= 5; $pos++) {
    // Posisi boleh kosong (admin tidak wajib isi semua)
    // Namun jika diisi, harus berupa integer positif
    if (isset($input[$pos]) && $input[$pos] !== '' && (int)$input[$pos] <= 0) {
        $valid = false;
        break;
    }
}

if (!$valid) {
    redirect(ADMIN_URL . 'top-news/index.php?status=error');
}

// ── Simpan tiap posisi dengan INSERT … ON DUPLICATE KEY UPDATE ───
$stmt = $conn->prepare(
    "INSERT INTO top_news (posisi, id_artikel) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE id_artikel = VALUES(id_artikel)"
);

// Hapus posisi yang dikosongkan admin
$delStmt = $conn->prepare("DELETE FROM top_news WHERE posisi = ?");

$success = true;
for ($pos = 1; $pos <= 5; $pos++) {
    $idArtikel = isset($input[$pos]) && $input[$pos] !== '' ? (int)$input[$pos] : 0;

    if ($idArtikel > 0) {
        // Verifikasi artikel masih aktif sebelum disimpan
        $chk = $conn->prepare(
            "SELECT id_artikel FROM artikel WHERE id_artikel = ? AND hapus = 1 AND enable = 1 LIMIT 1"
        );
        $chk->bind_param('i', $idArtikel);
        $chk->execute();
        $exists = (bool)$chk->get_result()->fetch_row();
        $chk->close();

        if (!$exists) {
            $success = false;
            break;
        }

        $stmt->bind_param('ii', $pos, $idArtikel);
        if (!$stmt->execute()) {
            $success = false;
            break;
        }
    } else {
        // Posisi dikosongkan — hapus dari tabel
        $delStmt->bind_param('i', $pos);
        $delStmt->execute();
    }
}

$stmt->close();
$delStmt->close();
$conn->close();

redirect(ADMIN_URL . 'top-news/index.php?status=' . ($success ? 'ok' : 'error'));
