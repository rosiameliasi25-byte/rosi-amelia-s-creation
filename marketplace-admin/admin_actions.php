<?php
/* ============================================================
   ADMIN ACTIONS — endpoint AJAX untuk tombol di dashboard
   Selalu kembalikan JSON: { success: bool, message, ...data }
   ============================================================ */
require 'admin_auth.php';
include '../db.php';
header('Content-Type: application/json');

$CFG = [
    'tx_success_status'  => 'sukses',
    'tx_rejected_status' => 'ditolak',
];

$action = $_POST['action'] ?? '';
$id     = $_POST['id'] ?? '';

if (!$action || !ctype_digit((string)$id)) {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid.']);
    exit;
}

try {
    switch ($action) {

       case 'verify_transaction': {
        $stmt = mysqli_prepare($conn, "UPDATE transactions SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $CFG['tx_success_status'], $id);
        $ok = mysqli_stmt_execute($stmt);
        $affected = mysqli_stmt_affected_rows($stmt);
        echo json_encode([
        'success' => $ok,
        'affected_rows' => $affected,
        'debug_id' => $id,
        'debug_status' => $CFG['tx_success_status'],
        'new_label' => 'sukses',
        'new_class' => 'ok'
        ]);
        break;
        }
        case 'reject_transaction': {
            $stmt = mysqli_prepare($conn, "UPDATE transactions SET status = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $CFG['tx_rejected_status'], $id);
            $ok = mysqli_stmt_execute($stmt);
            echo json_encode(['success' => $ok, 'new_label' => 'ditolak', 'new_class' => 'danger']);
            break;
        }

        case 'toggle_active_product': {
            $current = (int)($_POST['active'] ?? 0);
            $new = $current ? 0 : 1;
            $stmt = mysqli_prepare($conn, "UPDATE digital_products SET is_active = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $new, $id);
            $ok = mysqli_stmt_execute($stmt);
            echo json_encode([
                'success' => $ok,
                'new_active' => $new,
                'new_label' => $new ? 'Aktif' : 'Nonaktif',
                'new_class' => $new ? 'ok' : 'warn',
                'btn_label' => $new ? 'Nonaktifkan' : 'Aktifkan',
                'btn_class' => $new ? 'reject' : 'approve',
            ]);
            break;
        }

        case 'delete_review': {
            $stmt = mysqli_prepare($conn, "DELETE FROM digital_reviews WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            $ok = mysqli_stmt_execute($stmt);
            echo json_encode(['success' => $ok, 'message' => $ok ? '' : 'Gagal menghapus ulasan.']);
            break;
        }

        default:
            echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenali.']);
    }
} catch (\mysqli_sql_exception $e) {
    error_log('[admin_actions] ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan database.']);
}