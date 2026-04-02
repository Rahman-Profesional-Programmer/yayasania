<?php
// ============================================================
// Helper functions — digunakan admin/ maupun public/
// ============================================================

/**
 * Redirect ke URL tertentu lalu stop eksekusi
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit();
}

/**
 * Cek apakah admin sudah login, jika belum redirect ke login
 * Panggil di awal setiap halaman admin yang perlu proteksi
 */
function requireLogin(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['email'])) {
        redirect(BASE_URL . 'admin/auth/login.php');
    }

    hydrateSessionUser();
}

/**
 * Lengkapi session user dari database jika ada field baru yang belum tersimpan
 */
function hydrateSessionUser(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['email']) || (!empty($_SESSION['role']) && !empty($_SESSION['user_id']))) {
        return;
    }

    global $conn;

    if (!$conn instanceof mysqli) {
        return;
    }

    $stmt = $conn->prepare('SELECT id, name_show, role FROM users WHERE email = ? LIMIT 1');
    if (!$stmt) {
        return;
    }

    $stmt->bind_param('s', $_SESSION['email']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        return;
    }

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['name'] = $user['name_show'];
    $_SESSION['role'] = $user['role'] ?? 'user';
}

/**
 * Cek apakah user saat ini adalah admin
 */
function isAdmin(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    hydrateSessionUser();

    return ($_SESSION['role'] ?? 'user') === 'admin';
}

/**
 * Ambil ID user yang sedang login
 */
function currentUserId(): int
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    hydrateSessionUser();

    return (int) ($_SESSION['user_id'] ?? 0);
}

/**
 * Batasi halaman hanya untuk admin
 */
function requireAdmin(): void
{
    requireLogin();

    if (!isAdmin()) {
        setSwalFlash('error', 'Akses ditolak', 'Hanya admin yang dapat mengakses halaman ini.');
        redirect(ADMIN_URL . 'menu/index.php');
    }
}

/**
 * Simpan notifikasi SweetAlert ke session
 */
function setSwalFlash(string $icon, string $title, string $text): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['swal_flash'] = [
        'icon' => $icon,
        'title' => $title,
        'text' => $text,
    ];
}

/**
 * Render notifikasi SweetAlert dari session jika ada
 */
function renderSwalFlash(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['swal_flash'])) {
        return;
    }

    $swal = $_SESSION['swal_flash'];
    unset($_SESSION['swal_flash']);

    echo '<script>';
    echo 'Swal.fire({';
    echo 'icon:' . json_encode($swal['icon'] ?? 'info') . ',';
    echo 'title:' . json_encode($swal['title'] ?? '') . ',';
    echo 'text:' . json_encode($swal['text'] ?? '') . ',';
    echo 'confirmButtonColor:"#0d6efd"';
    echo '});';
    echo '</script>';
}

/**
 * Hash password untuk penyimpanan yang aman
 */
function hashPassword(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verifikasi password hash dengan fallback legacy plain text
 */
function verifyPassword(string $plainPassword, string $storedPassword): bool
{
    if ($storedPassword === '') {
        return false;
    }

    if (password_get_info($storedPassword)['algo'] !== null) {
        return password_verify($plainPassword, $storedPassword);
    }

    return hash_equals($storedPassword, $plainPassword);
}

/**
 * Cek apakah password lama perlu di-upgrade ke hash
 */
function needsPasswordRehash(string $storedPassword): bool
{
    if (password_get_info($storedPassword)['algo'] === null) {
        return true;
    }

    return password_needs_rehash($storedPassword, PASSWORD_DEFAULT);
}

/**
 * Sanitasi input agar aman dicetak di HTML
 */
function e($value): string
{
    if ($value === null) {
        return '';
    }

    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Potong teks panjang untuk preview
 */
function excerpt($text, int $length = 200): string
{
    $text = strip_tags((string) ($text ?? ''));
    return (mb_strlen($text) > $length) ? mb_substr($text, 0, $length) . '...' : $text;
}
