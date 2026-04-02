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
$kategori = trim((string) ($_POST['kategori'] ?? ''));
$tag_raw  = $_POST['tag']           ?? [];
$penulis  = $_SESSION['email']      ?? DEFAULT_USER_EMAIL;
$gambar_source = $_POST['gambar_source'] ?? 'file';
$gambar_link = trim((string) ($_POST['gambar_link'] ?? ''));
$gambar_cropped_data = trim((string) ($_POST['gambar_cropped_data'] ?? ''));

if ($judul === '' || $isi === '' || $kategori === '') {
    setSwalFlash('error', 'Data belum lengkap', 'Judul, kategori, dan isi artikel wajib diisi.');
    redirect(ADMIN_URL . 'artikel/create.php');
}

// Sumber gambar: link atau file
$foto = $_FILES['foto'] ?? null;
$link_foto = '';

if ($gambar_source === 'link') {
    if ($gambar_link === '' || !filter_var($gambar_link, FILTER_VALIDATE_URL)) {
        setSwalFlash('error', 'Link tidak valid', 'Masukkan URL gambar yang valid.');
        redirect(ADMIN_URL . 'artikel/create.php');
    }

    if (mb_strlen($gambar_link) > 500) {
        setSwalFlash('error', 'Link terlalu panjang', 'Panjang link gambar melebihi batas penyimpanan database.');
        redirect(ADMIN_URL . 'artikel/create.php');
    }

    $link_foto = $gambar_link;
} else {
    if ($gambar_cropped_data !== '') {
        $savedImage = saveBase64Image($gambar_cropped_data);
        if ($savedImage === null) {
            setSwalFlash('error', 'Crop gagal', 'Hasil crop gambar tidak valid. Silakan coba lagi.');
            redirect(ADMIN_URL . 'artikel/create.php');
        }
        $link_foto = $savedImage;
    } else {
        if (!$foto || $foto['error'] !== UPLOAD_ERR_OK) {
            setSwalFlash('error', 'Upload gagal', 'Gagal mengunggah foto. Silakan coba lagi.');
            redirect(ADMIN_URL . 'artikel/create.php');
        }

        if ($foto['size'] > 5_000_000) {
            setSwalFlash('error', 'Upload gagal', 'Ukuran file terlalu besar (maks 5MB).');
            redirect(ADMIN_URL . 'artikel/create.php');
        }

        $ekstensi  = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($ekstensi, $allowed, true)) {
            setSwalFlash('error', 'Upload gagal', 'Format file tidak diizinkan.');
            redirect(ADMIN_URL . 'artikel/create.php');
        }

        $nama_baru  = time() . '.' . $ekstensi;
        $path_fisik = UPLOAD_PATH . $nama_baru;
        $link_foto  = 'storage/uploads/foto/' . $nama_baru;

        if (!is_dir(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0755, true);
        }

        if (!move_uploaded_file($foto['tmp_name'], $path_fisik)) {
            setSwalFlash('error', 'Upload gagal', 'Gagal memindahkan file foto.');
            redirect(ADMIN_URL . 'artikel/create.php');
        }
    }
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
    if (!is_array($tag_raw)) {
        $tag_raw = array_map('trim', explode(',', (string) $tag_raw));
    }
    $tags = array_values(array_unique(array_filter(array_map('trim', $tag_raw))));
    if (!empty($tags)) {
        $stmt_tag = $conn->prepare("INSERT INTO artikel_tag (id_artikel, tag) VALUES (?, ?)");
        foreach ($tags as $tag) {
            $stmt_tag->bind_param("is", $id_artikel, $tag);
            $stmt_tag->execute();
        }
        $stmt_tag->close();
    }
    setSwalFlash('success', 'Berhasil', 'Artikel berhasil ditambahkan.');
    redirect(ADMIN_URL . 'artikel/index.php');
} else {
    $stmt->close();
    setSwalFlash('error', 'Gagal', 'Artikel gagal ditambahkan.');
    redirect(ADMIN_URL . 'artikel/create.php');
}
