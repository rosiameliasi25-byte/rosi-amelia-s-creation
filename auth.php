<?php
// Memulai sesi jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login atau belum
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit;
}
?>