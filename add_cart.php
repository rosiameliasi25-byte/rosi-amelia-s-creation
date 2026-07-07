<?php
// add_cart.php — Tambah produk ke keranjang (dipanggil dari products.php & game_store.php)
session_start();
include 'db.php';

// Validasi session — wajib login sebelum bisa menambah ke keranjang
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Hanya proses jika request via POST dan product_id dikirim
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id']) || !ctype_digit((string)$_POST['product_id'])) {
    header("Location: products.php");
    exit;
}

$uid        = (int) $_SESSION['user_id'];
$product_id = (int) $_POST['product_id'];
$qty_add    = isset($_POST['qty']) && ctype_digit((string)$_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;

// Tentukan halaman asal (products / game_store) agar redirect kembali ke halaman yang benar
$referer    = $_SERVER['HTTP_REFERER'] ?? '';
$back_page  = (strpos($referer, 'game_store.php') !== false) ? 'game_store.php' : 'products.php';

// 1. Pastikan produk benar-benar ada
$stmt = mysqli_prepare($conn, "SELECT id, stok FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$product) {
    header("Location: $back_page?error=notfound");
    exit;
}

// 2. Cek apakah produk ini sudah ada di keranjang user → UPDATE qty, jika belum → INSERT baru
$stmt = mysqli_prepare($conn, "SELECT id, qty FROM cart WHERE user_id = ? AND product_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $uid, $product_id);
mysqli_stmt_execute($stmt);
$existing = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

// ... (Kode pengecekan stok sudah ada di atas, tambahkan ini di bawahnya)

if ($existing) {
    // Produk sudah ada di keranjang, tambah qty
    $new_qty = $existing['qty'] + $qty_add;
    
    // 1. Update keranjang
    $stmt = mysqli_prepare($conn, "UPDATE cart SET qty = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $new_qty, $existing['id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // 2. Kurangi stok di tabel products
    $stmtStok = mysqli_prepare($conn, "UPDATE products SET stok = stok - ? WHERE id = ?");
    mysqli_stmt_bind_param($stmtStok, "ii", $qty_add, $product_id);
    mysqli_stmt_execute($stmtStok);
    mysqli_stmt_close($stmtStok);

} else {
    // Produk belum ada di keranjang, insert baru
    
    // 1. Insert ke keranjang
    $stmt = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, qty) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iii", $uid, $product_id, $qty_add);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // 2. Kurangi stok di tabel products
    $stmtStok = mysqli_prepare($conn, "UPDATE products SET stok = stok - ? WHERE id = ?");
    mysqli_stmt_bind_param($stmtStok, "ii", $qty_add, $product_id);
    mysqli_stmt_execute($stmtStok);
    mysqli_stmt_close($stmtStok);
}

header("Location: cart.php"); // Arahkan ke keranjang setelah selesai
exit;
