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

$stmt = $conn->prepare('UPDATE users SET enable = 1 WHERE id = ?');
$stmt->bind_param('i', $id);
echo $stmt->execute() ? 'success' : 'Gagal mengaktifkan pengguna.';
$stmt->close();
