<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(ADMIN_URL . 'menu/index.php');

$u = $_POST['urutan_menu'] ?? '';
$n = $_POST['nama_menu']   ?? '';
$l = $_POST['link_menu']   ?? '';

$stmt = $conn->prepare("INSERT INTO menu_utama (urutan, nama_menu, link_menu, link_menu_active, enable) VALUES (?, ?, ?, '1', 1)");
$stmt->bind_param("iss", $u, $n, $l);
$stmt->execute() ? redirect(ADMIN_URL . 'menu/index.php') : die("Gagal menambah menu.");
$stmt->close();
