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
    exit('Anda tidak dapat menghapus akun sendiri.');
}

$stmt = $conn->prepare('SELECT email, role FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    exit('Pengguna tidak ditemukan.');
}

if (($user['role'] ?? 'user') === 'admin') {
    $adminCountResult = $conn->query("SELECT COUNT(*) AS total_admins FROM users WHERE role = 'admin'");
    $adminCountRow = $adminCountResult ? $adminCountResult->fetch_assoc() : ['total_admins' => 0];
    if ((int) ($adminCountRow['total_admins'] ?? 0) <= 1) {
        exit('Admin terakhir tidak dapat dihapus.');
    }
}

$artikelCheck = $conn->prepare('SELECT COUNT(*) AS total_artikel FROM artikel WHERE penulis = ?');
$artikelCheck->bind_param('s', $user['email']);
$artikelCheck->execute();
$artikelRow = $artikelCheck->get_result()->fetch_assoc();
$artikelCheck->close();

if ((int) ($artikelRow['total_artikel'] ?? 0) > 0) {
    exit('Pengguna ini masih terhubung dengan artikel. Ubah penulis artikel terlebih dahulu.');
}

$delete = $conn->prepare('DELETE FROM users WHERE id = ?');
$delete->bind_param('i', $id);
echo $delete->execute() ? 'success' : 'Gagal menghapus pengguna.';
$delete->close();
