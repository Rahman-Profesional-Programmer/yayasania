<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(ADMIN_URL . 'artikel/index.php');
}

$judul    = $_POST['judul_artikel'] ?? '';
$isi      = $_POST['isi_artikel']   ?? '';
$kategori = $_POST['kategori']      ?? '';
$tag_raw  = $_POST['tag']           ?? '';
$penulis  = $_SESSION['email']      ?? DEFAULT_USER_EMAIL;

if ($kategori === 'lain') {
    $kategori = trim($_POST['kategori_baru'] ?? '');
}

// Upload foto
$foto     = $_FILES['foto'] ?? null;
if (!$foto || $foto['error'] !== UPLOAD_ERR_OK) {
    die("Gagal mengunggah foto. Silakan kembali dan coba lagi.");
}

if ($foto['size'] > 5_000_000) {
    die("Ukuran file terlalu besar (maks 5MB).");
}

$ekstensi  = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
$allowed   = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
if (!in_array($ekstensi, $allowed)) {
    die("Format file tidak diizinkan.");
}

$nama_baru  = time() . '.' . $ekstensi;
$path_fisik = UPLOAD_PATH . $nama_baru;
$link_foto  = 'storage/uploads/foto/' . $nama_baru;

if (!is_dir(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

if (!move_uploaded_file($foto['tmp_name'], $path_fisik)) {
    die("Gagal memindahkan file foto.");
}

// Insert artikel dengan prepared statement
$stmt = $conn->prepare(
    "INSERT INTO artikel (judul_artikel, konten_artikel, gambar, penulis, kategori, tanggal_update, viewer, enable, hapus)
     VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP(), 0, 1, 1)"
);
$stmt->bind_param("sssss", $judul, $isi, $link_foto, $penulis, $kategori);

if ($stmt->execute()) {
    $id_artikel = $conn->insert_id;
    $stmt->close();

    // Insert tags
    $tags = array_filter(array_map('trim', explode(',', $tag_raw)));
    if (!empty($tags)) {
        $stmt_tag = $conn->prepare("INSERT INTO artikel_tag (id_artikel, tag) VALUES (?, ?)");
        foreach ($tags as $tag) {
            $stmt_tag->bind_param("is", $id_artikel, $tag);
            $stmt_tag->execute();
        }
        $stmt_tag->close();
    }

    echo '<script>alert("Artikel berhasil ditambahkan"); window.location.href="' . ADMIN_URL . 'artikel/index.php";</script>';
} else {
    $stmt->close();
    echo '<script>alert("Artikel gagal ditambahkan"); window.history.back();</script>';
}
