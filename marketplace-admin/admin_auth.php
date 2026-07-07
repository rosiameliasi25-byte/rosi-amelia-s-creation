<?php
/* ============================================================
   ADMIN AUTH GUARD
   include file ini di paling atas setiap halaman admin.
   ============================================================ */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: admin_login.php");
    exit;
}
// TAMBAHKAN INI: Memaksa browser untuk tidak menyimpan halaman di cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>