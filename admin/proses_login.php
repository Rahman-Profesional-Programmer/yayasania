<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

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

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (verifyPassword($password, $row['pass'])) {
        if ((int) $row['enable'] === 1) {
            if (needsPasswordRehash($row['pass'])) {
                $newHash = hashPassword($password);
                $updatePassword = $conn->prepare("UPDATE users SET pass = ? WHERE id = ?");
                $updatePassword->bind_param("si", $newHash, $row['id']);
                $updatePassword->execute();
                $updatePassword->close();
            }

            $_SESSION['user_id'] = (int) $row['id'];
            $_SESSION['name'] = $row['name_show'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'] ?? 'user';
            $_SESSION['foto'] = $row['foto'] ?? '';
            $stmt->close();
            header("Location: crud-menu.php");
            exit();
        }

        $_SESSION['error'] = "Akun Anda belum aktif";
        $stmt->close();
        header("Location: authentication-signin.php");
        exit();
    }
}

$stmt->close();
$_SESSION['error'] = "Email atau password salah";
header("Location: authentication-signin.php");
exit();
