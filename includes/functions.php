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
