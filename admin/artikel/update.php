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
$kategori = trim((string) ($_POST['kategori'] ?? ''));
$tag_raw  = $_POST['tag']           ?? [];
$gambar_source = $_POST['gambar_source'] ?? 'file';
$gambar_link = trim((string) ($_POST['gambar_link'] ?? ''));
$gambar_cropped_data = trim((string) ($_POST['gambar_cropped_data'] ?? ''));

if ($id <= 0 || $judul === '' || $isi === '' || $kategori === '') {
    setSwalFlash('error', 'Data belum lengkap', 'Judul, kategori, dan isi artikel wajib diisi.');
    redirect(ADMIN_URL . 'artikel/edit.php?id=' . $id);
}

// Cek apakah ada foto baru
$foto = $_FILES['foto'] ?? null;
$link_foto = null;

if ($gambar_source === 'link') {
    if ($gambar_link === '' || !filter_var($gambar_link, FILTER_VALIDATE_URL)) {
        setSwalFlash('error', 'Link tidak valid', 'Masukkan URL gambar yang valid.');
        redirect(ADMIN_URL . 'artikel/edit.php?id=' . $id);
    }

    if (mb_strlen($gambar_link) > 500) {
        setSwalFlash('error', 'Link terlalu panjang', 'Panjang link gambar melebihi batas penyimpanan database.');
        redirect(ADMIN_URL . 'artikel/edit.php?id=' . $id);
    }

    $link_foto = $gambar_link;
} elseif ($gambar_cropped_data !== '') {
    $savedImage = saveBase64Image($gambar_cropped_data);
    if ($savedImage === null) {
        setSwalFlash('error', 'Crop gagal', 'Hasil crop gambar tidak valid. Silakan coba lagi.');
        redirect(ADMIN_URL . 'artikel/edit.php?id=' . $id);
    }
    $link_foto = $savedImage;
} elseif ($foto && $foto['error'] === UPLOAD_ERR_OK && $foto['size'] > 0) {
    if ($foto['size'] > 5_000_000) {
        setSwalFlash('error', 'Upload gagal', 'Ukuran file terlalu besar (maks 5MB).');
        redirect(ADMIN_URL . 'artikel/edit.php?id=' . $id);
    }
    $ekstensi = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
    $allowed  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ekstensi, $allowed, true)) {
        setSwalFlash('error', 'Upload gagal', 'Format file tidak diizinkan.');
        redirect(ADMIN_URL . 'artikel/edit.php?id=' . $id);
    }
    $nama_baru  = time() . '.' . $ekstensi;
    if (!is_dir(UPLOAD_PATH)) mkdir(UPLOAD_PATH, 0755, true);
    if (move_uploaded_file($foto['tmp_name'], UPLOAD_PATH . $nama_baru)) {
        $link_foto = 'storage/uploads/foto/' . $nama_baru;
    } else {
        setSwalFlash('error', 'Upload gagal', 'Gagal memindahkan file foto.');
        redirect(ADMIN_URL . 'artikel/edit.php?id=' . $id);
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
    $stmt_delete_tag = $conn->prepare("DELETE FROM artikel_tag WHERE id_artikel = ?");
    $stmt_delete_tag->bind_param("i", $id);
    $stmt_delete_tag->execute();
    $stmt_delete_tag->close();

    if (!is_array($tag_raw)) {
        $tag_raw = array_map('trim', explode(',', (string) $tag_raw));
    }
    $tags = array_values(array_unique(array_filter(array_map('trim', $tag_raw))));
    if (!empty($tags)) {
        $stmt_tag = $conn->prepare("INSERT INTO artikel_tag (id_artikel, tag) VALUES (?, ?)");
        foreach ($tags as $tag) {
            $stmt_tag->bind_param("is", $id, $tag);
            $stmt_tag->execute();
        }
        $stmt_tag->close();
    }
    setSwalFlash('success', 'Berhasil', 'Artikel berhasil diupdate.');
    redirect(ADMIN_URL . 'artikel/index.php');
} else {
    $stmt->close();
    setSwalFlash('error', 'Gagal', 'Artikel gagal diupdate.');
    redirect(ADMIN_URL . 'artikel/edit.php?id=' . $id);
}
