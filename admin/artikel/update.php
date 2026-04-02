<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(ADMIN_URL . 'artikel/index.php');
}

$id       = (int) ($_POST['id_artikel'] ?? 0);
$judul    = $_POST['judul_artikel'] ?? '';
$isi      = $_POST['isi_artikel']   ?? '';
$kategori = $_POST['kategori']      ?? '';
$tag_raw  = $_POST['tag']           ?? '';

if ($kategori === 'lain') {
    $kategori = trim($_POST['kategori_baru'] ?? '');
}

// Cek apakah ada foto baru
$foto = $_FILES['foto'] ?? null;
$link_foto = null;

if ($foto && $foto['error'] === UPLOAD_ERR_OK && $foto['size'] > 0) {
    if ($foto['size'] > 5_000_000) die("Ukuran file terlalu besar.");
    $ekstensi = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
    $allowed  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ekstensi, $allowed)) die("Format file tidak diizinkan.");
    $nama_baru  = time() . '.' . $ekstensi;
    if (!is_dir(UPLOAD_PATH)) mkdir(UPLOAD_PATH, 0755, true);
    if (move_uploaded_file($foto['tmp_name'], UPLOAD_PATH . $nama_baru)) {
        $link_foto = 'storage/uploads/foto/' . $nama_baru;
    }
}

// Update artikel
if ($link_foto) {
    $stmt = $conn->prepare(
        "UPDATE artikel SET judul_artikel=?, konten_artikel=?, gambar=?, kategori=?, tanggal_update=CURRENT_TIMESTAMP() WHERE id_artikel=?"
    );
    $stmt->bind_param("ssssi", $judul, $isi, $link_foto, $kategori, $id);
} else {
    $stmt = $conn->prepare(
        "UPDATE artikel SET judul_artikel=?, konten_artikel=?, kategori=?, tanggal_update=CURRENT_TIMESTAMP() WHERE id_artikel=?"
    );
    $stmt->bind_param("sssi", $judul, $isi, $kategori, $id);
}

if ($stmt->execute()) {
    $stmt->close();

    // Hapus tag lama, insert tag baru
    $conn->query("DELETE FROM artikel_tag WHERE id_artikel = $id");
    $tags = array_filter(array_map('trim', explode(',', $tag_raw)));
    if (!empty($tags)) {
        $stmt_tag = $conn->prepare("INSERT INTO artikel_tag (id_artikel, tag) VALUES (?, ?)");
        foreach ($tags as $tag) {
            $stmt_tag->bind_param("is", $id, $tag);
            $stmt_tag->execute();
        }
        $stmt_tag->close();
    }
    $_SESSION['swal_flash'] = [
        'icon' => 'success',
        'title' => 'Berhasil',
        'text' => 'Artikel berhasil diupdate',
    ];
    redirect(ADMIN_URL . 'artikel/index.php');
} else {
    $stmt->close();
    $_SESSION['swal_flash'] = [
        'icon' => 'error',
        'title' => 'Gagal',
        'text' => 'Artikel gagal diupdate',
    ];
    redirect(ADMIN_URL . 'artikel/edit.php?id=' . $id);
}
