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
$foto = trim($_POST['foto'] ?? '');
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
