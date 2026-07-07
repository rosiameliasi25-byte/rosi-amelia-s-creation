<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_rosi_consultan";

$conn = mysqli_connect($host, $user, $pass, $db);

// Hapus/Hapus komentar (//) pada baris di bawah jika Anda ingin melihat error
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>