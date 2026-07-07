<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit;
}

$uid = (int) $_SESSION['user_id'];
$kontak = trim($_POST['kontak'] ?? '');

if ($kontak === '') {
    header("Location: checkout.php?error=kontak");
    exit;
}

$stmt = mysqli_prepare($conn,
    "SELECT c.product_id, c.qty, p.harga
     FROM cart c JOIN products p ON c.product_id = p.id
     WHERE c.user_id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];
$total = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
    $total += (float)$row['harga'] * (int)$row['qty'];
}
mysqli_stmt_close($stmt);

if (empty($items) || $total <= 0) {
    header("Location: cart.php");
    exit;
}

if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] !== UPLOAD_ERR_OK) {
    header("Location: checkout.php?error=upload");
    exit;
}

$allowed_ext = ['jpg', 'jpeg', 'png'];
$ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));
$is_image = getimagesize($_FILES['bukti']['tmp_name']) !== false;
$max_size = 5 * 1024 * 1024;

if (!$is_image || !in_array($ext, $allowed_ext, true)) {
    header("Location: checkout.php?error=filetype");
    exit;
}
if ($_FILES['bukti']['size'] > $max_size) {
    header("Location: checkout.php?error=filesize");
    exit;
}

$upload_dir = __DIR__ . '/uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$file_name = uniqid('bukti_', true) . '.' . $ext;
$target_file = $upload_dir . $file_name;

if (!move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
    header("Location: checkout.php?error=movefail");
    exit;
}

mysqli_begin_transaction($conn);

try {
    $stmtTrx = mysqli_prepare($conn,
        "INSERT INTO transactions (user_id, total, kontak, bukti_transfer, status, created_at)
         VALUES (?, ?, ?, ?, 'pending', NOW())"
    );
    $total_db = (float)$total;
    mysqli_stmt_bind_param($stmtTrx, "idss", $uid, $total_db, $kontak, $file_name);
    if (!mysqli_stmt_execute($stmtTrx)) {
        throw new Exception('Gagal menyimpan transaksi.');
    }
    $transaction_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmtTrx);

    $stmtDetail = mysqli_prepare($conn,
        "INSERT INTO transaction_detail (transaction_id, product_id, qty) VALUES (?, ?, ?)"
    );

    $stmtUpdateStok = mysqli_prepare($conn, "UPDATE products SET stok = stok - ? WHERE id = ? AND stok >= ?");

    foreach ($items as $item) {
        mysqli_stmt_bind_param($stmtDetail, "iii", $transaction_id, $item['product_id'], $item['qty']);
        if (!mysqli_stmt_execute($stmtDetail)) {
            throw new Exception('Gagal menyimpan detail transaksi.');
        }

        mysqli_stmt_bind_param($stmtUpdateStok, "iii", $item['qty'], $item['product_id'], $item['qty']);
        if (!mysqli_stmt_execute($stmtUpdateStok)) {
            throw new Exception('Gagal memperbarui stok.');
        }

        if (mysqli_stmt_affected_rows($stmtUpdateStok) == 0) {
            throw new Exception('Stok tidak mencukupi.');
        }
    }

    mysqli_stmt_close($stmtUpdateStok);
    mysqli_stmt_close($stmtDetail);

    $stmtDelCart = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id = ?");
    mysqli_stmt_bind_param($stmtDelCart, "i", $uid);
    if (!mysqli_stmt_execute($stmtDelCart)) {
        throw new Exception('Gagal mengosongkan keranjang.');
    }
    mysqli_stmt_close($stmtDelCart);

    mysqli_commit($conn);
} catch (Exception $e) {
    mysqli_rollback($conn);
    if (file_exists($target_file)) {
        unlink($target_file);
    }
    header("Location: checkout.php?error=dberror");
    exit;
}

header("Location: struk.php?id=" . $transaction_id);
exit;