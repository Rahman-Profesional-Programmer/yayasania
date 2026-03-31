<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/functions.php';
session_start();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(ADMIN_URL . 'menu/index.php');

$u          = $_POST['urutan_sub_menu'] ?? '';
$n          = $_POST['nama_sub_menu']   ?? '';
$l          = $_POST['link_sub_menu']   ?? '';
$menu_utama = $_POST['menu_utama']      ?? '';

$stmt = $conn->prepare("INSERT INTO sub_menu (urutan, nama_menu, link_menu, link_menu_active, menu_utama, enable) VALUES (?, ?, ?, '1', ?, 1)");
$stmt->bind_param("isss", $u, $n, $l, $menu_utama);
$stmt->execute() ? redirect(ADMIN_URL . 'menu/index.php') : die("Gagal menambah sub menu.");
$stmt->close();
