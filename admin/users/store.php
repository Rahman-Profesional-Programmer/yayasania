<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(ADMIN_URL . 'users/index.php');
}

$name = trim($_POST['name_show'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$role = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';
$enable = (int) ($_POST['enable'] ?? 1) === 1 ? 1 : 0;
$fotoSource = $_POST['foto_source'] ?? 'link';
$fotoLink = trim((string) ($_POST['foto_link'] ?? ''));
$fotoCroppedData = trim((string) ($_POST['foto_cropped_data'] ?? ''));
$diskripsi = trim($_POST['diskripsi'] ?? '');
$facebook = trim($_POST['facebook'] ?? '');
$instagram = trim($_POST['instagram'] ?? '');

if ($name === '' || $email === '' || $password === '') {
    setSwalFlash('error', 'Data belum lengkap', 'Nama, email, dan password wajib diisi.');
    redirect(ADMIN_URL . 'users/index.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setSwalFlash('error', 'Email tidak valid', 'Gunakan format email yang benar.');
    redirect(ADMIN_URL . 'users/index.php');
}

if (mb_strlen($password) < 6) {
    setSwalFlash('error', 'Password terlalu pendek', 'Password minimal 6 karakter.');
    redirect(ADMIN_URL . 'users/index.php');
}

$check = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$check->bind_param('s', $email);
$check->execute();
$exists = $check->get_result()->fetch_assoc();
$check->close();

if ($exists) {
    setSwalFlash('error', 'Email sudah dipakai', 'Gunakan email lain untuk pengguna baru.');
    redirect(ADMIN_URL . 'users/index.php');
}

$foto = '';

if ($fotoSource === 'file') {
    if ($fotoCroppedData !== '') {
        $savedImage = saveBase64Image($fotoCroppedData);
        if ($savedImage === null) {
            setSwalFlash('error', 'Crop gagal', 'Hasil crop foto profil tidak valid. Silakan coba lagi.');
            redirect(ADMIN_URL . 'users/index.php');
        }
        $foto = $savedImage;
    } else {
        $uploadedPhoto = $_FILES['foto_file'] ?? null;

        if ($uploadedPhoto && $uploadedPhoto['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($uploadedPhoto['error'] !== UPLOAD_ERR_OK) {
                setSwalFlash('error', 'Upload gagal', 'Foto profil gagal diunggah.');
                redirect(ADMIN_URL . 'users/index.php');
            }

            if ($uploadedPhoto['size'] > 5_000_000) {
                setSwalFlash('error', 'Upload gagal', 'Ukuran foto profil terlalu besar (maks 5MB).');
                redirect(ADMIN_URL . 'users/index.php');
            }

            $extension = strtolower(pathinfo($uploadedPhoto['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (!in_array($extension, $allowed, true)) {
                setSwalFlash('error', 'Upload gagal', 'Format foto profil tidak diizinkan.');
                redirect(ADMIN_URL . 'users/index.php');
            }

            if (!is_dir(UPLOAD_PATH)) {
                mkdir(UPLOAD_PATH, 0755, true);
            }

            $fileName = time() . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
            $filePath = UPLOAD_PATH . $fileName;
            if (!move_uploaded_file($uploadedPhoto['tmp_name'], $filePath)) {
                setSwalFlash('error', 'Upload gagal', 'Gagal memindahkan foto profil.');
                redirect(ADMIN_URL . 'users/index.php');
            }

            $foto = 'storage/uploads/foto/' . $fileName;
        }
    }
} elseif ($fotoLink !== '') {
    if (!filter_var($fotoLink, FILTER_VALIDATE_URL)) {
        setSwalFlash('error', 'Link tidak valid', 'Gunakan URL foto profil yang benar.');
        redirect(ADMIN_URL . 'users/index.php');
    }

    if (mb_strlen($fotoLink) > 500) {
        setSwalFlash('error', 'Link terlalu panjang', 'Panjang link foto melebihi batas penyimpanan database.');
        redirect(ADMIN_URL . 'users/index.php');
    }

    $foto = $fotoLink;
}

$passwordHash = hashPassword($password);
$stmt = $conn->prepare('INSERT INTO users (email, pass, name_show, foto, diskripsi, facebook, instagram, enable, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->bind_param('sssssssis', $email, $passwordHash, $name, $foto, $diskripsi, $facebook, $instagram, $enable, $role);

if ($stmt->execute()) {
    $stmt->close();
    setSwalFlash('success', 'Berhasil', 'Pengguna baru berhasil ditambahkan.');
    redirect(ADMIN_URL . 'users/index.php');
}

$stmt->close();
setSwalFlash('error', 'Gagal', 'Pengguna baru gagal ditambahkan.');
redirect(ADMIN_URL . 'users/index.php');
