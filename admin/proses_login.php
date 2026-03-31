<?php
// Memulai sesi PHP
session_start();

// Membuat koneksi ke database
// $conn = mysqli_connect("localhost", "root", "", "ihsanul-web");
require_once 'koneksi_db.php';

// Mengambil data dari form login
$email = $_POST['email'];
$password = $_POST['password'];

// Mengecek apakah email dan password sudah diisi
if (empty($email) || empty($password)) {
    // Jika tidak, kembali ke halaman login dengan pesan error
    $_SESSION['error'] = "Email dan password harus diisi";
    header("Location: authentication-signin.php");
    exit();
}

// Mengecek apakah email dan password cocok dengan data di database
$query = "SELECT * FROM users WHERE email = '$email' AND pass = '$password'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 1) {
    // Jika cocok, cek status aktif user
    $row = mysqli_fetch_assoc($result);
    if ($row['enable'] == 1) {
        // Jika user aktif, simpan data user ke dalam variabel session
        $_SESSION['name'] = $row['name_show'];
        $_SESSION['email'] = $row['email'];
        // Redirect ke halaman dashboard
        header("Location: crud-menu.php");
        exit();
    } else {
        // Jika user tidak aktif, kembali ke halaman login dengan pesan error
        $_SESSION['error'] = "Akun Anda belum aktif";
        header("Location: authentication-signin.php");
        exit();
    }
} else {
    // Jika email atau password salah, kembali ke halaman login dengan pesan error
    $_SESSION['error'] = "Email atau password salah";
    header("Location: authentication-signin.php");
    exit();
}
