<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(ADMIN_URL . 'users/index.php');
}

$id = (int) ($_POST['id'] ?? 0);
if (!$id) {
    setSwalFlash('error', 'Data tidak valid', 'ID pengguna tidak ditemukan.');
    redirect(ADMIN_URL . 'users/index.php');
}

$stmt = $conn->prepare('SELECT id, email, role, enable FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    setSwalFlash('error', 'Data tidak ditemukan', 'Pengguna yang dipilih tidak tersedia.');
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

if ($name === '' || $email === '') {
    setSwalFlash('error', 'Data belum lengkap', 'Nama dan email wajib diisi.');
    redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setSwalFlash('error', 'Email tidak valid', 'Gunakan format email yang benar.');
    redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
}

if ($password !== '' && mb_strlen($password) < 6) {
    setSwalFlash('error', 'Password terlalu pendek', 'Password minimal 6 karakter.');
    redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
}

$check = $conn->prepare('SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1');
$check->bind_param('si', $email, $id);
$check->execute();
$exists = $check->get_result()->fetch_assoc();
$check->close();

if ($exists) {
    setSwalFlash('error', 'Email sudah dipakai', 'Gunakan email lain untuk pengguna ini.');
    redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
}

if ($id === currentUserId()) {
    if ($role !== ($user['role'] ?? 'user')) {
        setSwalFlash('error', 'Aksi ditolak', 'Anda tidak dapat mengubah role akun sendiri.');
        redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
    }

    if ($enable !== (int) $user['enable']) {
        setSwalFlash('error', 'Aksi ditolak', 'Anda tidak dapat menonaktifkan akun sendiri.');
        redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
    }
}

if (($user['role'] ?? 'user') === 'admin' && $role !== 'admin') {
    $adminCountResult = $conn->query("SELECT COUNT(*) AS total_admins FROM users WHERE role = 'admin'");
    $adminCountRow = $adminCountResult ? $adminCountResult->fetch_assoc() : ['total_admins' => 0];
    if ((int) ($adminCountRow['total_admins'] ?? 0) <= 1) {
        setSwalFlash('error', 'Admin terakhir', 'Minimal harus ada satu akun admin di sistem.');
        redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
    }
}

$foto = $user['foto'] ?? '';

if ($fotoSource === 'file') {
    if ($fotoCroppedData !== '') {
        $savedImage = saveBase64Image($fotoCroppedData);
        if ($savedImage === null) {
            setSwalFlash('error', 'Crop gagal', 'Hasil crop foto profil tidak valid. Silakan coba lagi.');
            redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
        }
        $foto = $savedImage;
    } else {
        $uploadedPhoto = $_FILES['foto_file'] ?? null;

        if ($uploadedPhoto && $uploadedPhoto['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($uploadedPhoto['error'] !== UPLOAD_ERR_OK) {
                setSwalFlash('error', 'Upload gagal', 'Foto profil gagal diunggah.');
                redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
            }

            if ($uploadedPhoto['size'] > 5_000_000) {
                setSwalFlash('error', 'Upload gagal', 'Ukuran foto profil terlalu besar (maks 5MB).');
                redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
            }

            $extension = strtolower(pathinfo($uploadedPhoto['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (!in_array($extension, $allowed, true)) {
                setSwalFlash('error', 'Upload gagal', 'Format foto profil tidak diizinkan.');
                redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
            }

            if (!is_dir(UPLOAD_PATH)) {
                mkdir(UPLOAD_PATH, 0755, true);
            }

            $fileName = time() . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
            $filePath = UPLOAD_PATH . $fileName;
            if (!move_uploaded_file($uploadedPhoto['tmp_name'], $filePath)) {
                setSwalFlash('error', 'Upload gagal', 'Gagal memindahkan foto profil.');
                redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
            }

            $foto = 'storage/uploads/foto/' . $fileName;
        }
    }
} elseif ($fotoLink !== '') {
    if (!filter_var($fotoLink, FILTER_VALIDATE_URL)) {
        setSwalFlash('error', 'Link tidak valid', 'Gunakan URL foto profil yang benar.');
        redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
    }

    if (mb_strlen($fotoLink) > 500) {
        setSwalFlash('error', 'Link terlalu panjang', 'Panjang link foto melebihi batas penyimpanan database.');
        redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
    }

    $foto = $fotoLink;
} elseif (array_key_exists('foto_link', $_POST) && $fotoLink === '') {
    $foto = '';
}

mysqli_begin_transaction($conn);

try {
    if ($password !== '') {
        $passwordHash = hashPassword($password);
        $update = $conn->prepare('UPDATE users SET email = ?, pass = ?, name_show = ?, foto = ?, diskripsi = ?, facebook = ?, instagram = ?, enable = ?, role = ? WHERE id = ?');
        $update->bind_param('sssssssisi', $email, $passwordHash, $name, $foto, $diskripsi, $facebook, $instagram, $enable, $role, $id);
    } else {
        $update = $conn->prepare('UPDATE users SET email = ?, name_show = ?, foto = ?, diskripsi = ?, facebook = ?, instagram = ?, enable = ?, role = ? WHERE id = ?');
        $update->bind_param('ssssssisi', $email, $name, $foto, $diskripsi, $facebook, $instagram, $enable, $role, $id);
    }

    if (!$update->execute()) {
        throw new RuntimeException('Gagal memperbarui data pengguna.');
    }
    $update->close();

    if ($user['email'] !== $email) {
        $updateArtikel = $conn->prepare('UPDATE artikel SET penulis = ? WHERE penulis = ?');
        $updateArtikel->bind_param('ss', $email, $user['email']);
        if (!$updateArtikel->execute()) {
            throw new RuntimeException('Gagal menyelaraskan email penulis pada artikel.');
        }
        $updateArtikel->close();

        if ($id === currentUserId()) {
            $_SESSION['email'] = $email;
        }
    }

    if ($id === currentUserId()) {
        $_SESSION['name'] = $name;
        $_SESSION['role'] = $role;
        $_SESSION['foto'] = $foto;
    }

    mysqli_commit($conn);
    setSwalFlash('success', 'Berhasil', 'Data pengguna berhasil diperbarui.');
    redirect(ADMIN_URL . 'users/index.php');
} catch (Throwable $exception) {
    mysqli_rollback($conn);
    setSwalFlash('error', 'Gagal', $exception->getMessage());
    redirect(ADMIN_URL . 'users/edit.php?id=' . $id);
}
