<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Metode tidak valid.');
}

$id = (int) ($_POST['id'] ?? 0);
if (!$id) {
    exit('ID pengguna tidak valid.');
}

if ($id === currentUserId()) {
    exit('Anda tidak dapat menonaktifkan akun sendiri.');
}

$stmt = $conn->prepare('SELECT role FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    exit('Pengguna tidak ditemukan.');
}

if (($user['role'] ?? 'user') === 'admin') {
    $adminCountResult = $conn->query("SELECT COUNT(*) AS total_admins FROM users WHERE role = 'admin' AND enable = 1");
    $adminCountRow = $adminCountResult ? $adminCountResult->fetch_assoc() : ['total_admins' => 0];
    if ((int) ($adminCountRow['total_admins'] ?? 0) <= 1) {
        exit('Admin terakhir tidak dapat dinonaktifkan.');
    }
}

$update = $conn->prepare('UPDATE users SET enable = 0 WHERE id = ?');
$update->bind_param('i', $id);
echo $update->execute() ? 'success' : 'Gagal menonaktifkan pengguna.';
$update->close();
