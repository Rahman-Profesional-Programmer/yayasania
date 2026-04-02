<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(ADMIN_URL . 'auth/login.php');
}

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Email dan password harus diisi";
    redirect(ADMIN_URL . 'auth/login.php');
}

// Gunakan prepared statement untuk mencegah SQL injection
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (verifyPassword($password, $row['pass'])) {
        if ($row['enable'] == 1) {
            if (needsPasswordRehash($row['pass'])) {
                $newHash = hashPassword($password);
                $updatePassword = $conn->prepare("UPDATE users SET pass = ? WHERE id = ?");
                $updatePassword->bind_param("si", $newHash, $row['id']);
                $updatePassword->execute();
                $updatePassword->close();
            }

            $_SESSION['user_id'] = (int) $row['id'];
            $_SESSION['name']  = $row['name_show'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role']  = $row['role'] ?? 'user';
            $stmt->close();
            redirect(ADMIN_URL . 'menu/index.php');
        } else {
            $_SESSION['error'] = "Akun Anda belum aktif";
        }
    } else {
        $_SESSION['error'] = "Email atau password salah";
    }
} else {
    $_SESSION['error'] = "Email atau password salah";
}

$stmt->close();
redirect(ADMIN_URL . 'auth/login.php');
