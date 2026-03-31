<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int) ($_POST['id'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM menu_utama WHERE id = ?");
    $stmt->bind_param("i", $id);
    echo $stmt->execute() ? "success" : "gagal";
    $stmt->close();
}
