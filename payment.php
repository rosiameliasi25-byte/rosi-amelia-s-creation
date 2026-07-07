<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
// payment.php
include 'auth.php';
include 'db.php';
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) { header("Location: dashboard.php"); exit; }
$transaction_id = (int) $_GET['id'];
$user_id        = (int) $_SESSION['user_id'];

// Prepared statement — memastikan transaksi memang milik user yang sedang login
$stmtTrx = mysqli_prepare($conn, "SELECT * FROM transactions WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmtTrx, "ii", $transaction_id, $user_id);
mysqli_stmt_execute($stmtTrx);
$trx = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtTrx));
mysqli_stmt_close($stmtTrx);
if (!$trx) { header("Location: dashboard.php"); exit; }

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bukti_transfer'])) {
    $target_dir = __DIR__ . "/uploads/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0755, true);

    $ext   = strtolower(pathinfo($_FILES["bukti_transfer"]["name"], PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["bukti_transfer"]["tmp_name"]);

    if ($_FILES['bukti_transfer']['error'] !== UPLOAD_ERR_OK) {
        $error = "Gagal mengunggah file. Silakan coba lagi.";
    } elseif ($_FILES['bukti_transfer']['size'] > 5 * 1024 * 1024) {
        $error = "Ukuran file maksimal 5MB.";
    } elseif ($check === false || !in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        $error = "Hanya menerima format JPG, JPEG, & PNG.";
    } else {
        $file_name   = uniqid('bukti_', true) . '.' . $ext;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["bukti_transfer"]["tmp_name"], $target_file)) {
            $stmtUpd = mysqli_prepare($conn, "UPDATE transactions SET bukti_transfer = ? WHERE id = ? AND user_id = ?");
            mysqli_stmt_bind_param($stmtUpd, "sii", $file_name, $transaction_id, $user_id);
            mysqli_stmt_execute($stmtUpd);
            mysqli_stmt_close($stmtUpd);
            $success = "Bukti transfer berhasil diunggah! Admin akan memverifikasi dalam 1×24 jam.";
        } else {
            $error = "Terjadi kesalahan saat mengunggah gambar.";
        }
    }
}

$page_title  = 'Upload Bukti Bayar · RosiMarket Hub';
$active_page = 'history';
include '_navbar.php';
?>

<style>
  .pay-wrap { max-width:520px;margin:32px auto;padding:0 20px;animation:fadeUp 0.4s ease; }
  .back-link { display:inline-flex;align-items:center;gap:4px;font-size:13px;color:var(--txt3);margin-bottom:16px;transition:color 0.15s; }
  .back-link:hover { color:var(--txtlink); }

  .pay-card { background:var(--bg3);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden; }

  .pay-head {
    padding:20px 22px;border-bottom:1px solid var(--border);
    background:linear-gradient(90deg,rgba(59,130,246,0.06),transparent);
    display:flex;align-items:center;gap:10px;
  }
  .pay-head-title { font-size:14px;font-weight:600; }
  .pay-head-sub   { font-size:12px;color:var(--txt3); }

  .pay-body { padding:22px; }

  .total-box {
    background:var(--bg2);border:1px solid var(--border);
    border-radius:var(--r-md);padding:16px 18px;margin-bottom:20px;
    display:flex;align-items:center;justify-content:space-between;
  }
  .total-box .lbl { font-size:12px;color:var(--txt3); }
  .total-box .num { font-size:22px;font-weight:700;color:var(--accent);font-family:'JetBrains Mono',monospace; }

  .pay-methods {
    display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px;
  }
  .pay-method {
    background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-md);padding:12px;
  }
  .pay-method .bank { font-size:11px;font-weight:700;color:var(--txt2);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px; }
  .pay-method .num  { font-size:12px;font-weight:700;color:var(--accent);font-family:'JetBrains Mono',monospace; }
  .pay-method .name { font-size:11px;color:var(--txt3);margin-top:2px; }

  .file-zone {
    border:2px dashed var(--border);border-radius:var(--r-md);
    padding:24px;text-align:center;cursor:pointer;position:relative;
    overflow:hidden;transition:border-color 0.2s;
  }
  .file-zone:hover { border-color:var(--accent); }
  .file-zone input { position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%; }
  .file-zone-icon { font-size:30px;margin-bottom:8px; }
  .file-zone-text { font-size:13px;color:var(--txt2); }
  .file-zone-hint { font-size:11px;color:var(--txt3);margin-top:3px; }

  .pay-foot { padding:16px 22px;border-top:1px solid var(--border);display:flex;gap:10px; }

  @keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
</style>

<div class="pay-wrap">
  <a class="back-link" href="history.php">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M19 12H5M5 12l7 7M5 12l7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    Kembali ke Riwayat
  </a>

  <div class="pay-card">
    <div class="pay-head">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" stroke="currentColor" stroke-width="1.8"/></svg>
      <div>
        <div class="pay-head-title">Invoice #<?php echo $transaction_id; ?></div>
        <div class="pay-head-sub">Unggah bukti transfer untuk konfirmasi pembayaran</div>
      </div>
    </div>

    <div class="pay-body">
      <?php if ($error):   ?><div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?></div><?php endif; ?>

      <div class="total-box">
        <div><div class="lbl">Total Tagihan</div></div>
        <div class="num">Rp <?php echo number_format($trx['total']); ?></div>
      </div>

      <?php if (!$success): ?>
        <div style="font-size:12px;font-weight:600;color:var(--txt2);margin-bottom:10px;">Pilihan Transfer Resmi:</div>
        <div class="pay-methods">
          <div class="pay-method">
            <div class="bank">Bank BCA</div>
            <div class="num">8410-2344-11</div>
            <div class="name">a.n. Market Hub Admin</div>
          </div>
          <div class="pay-method">
            <div class="bank">DANA</div>
            <div class="num">0812-3456-7890</div>
            <div class="name">a.n. Market Hub Digital</div>
          </div>
        </div>

        <div style="font-size:12px;font-weight:600;color:var(--txt2);margin-bottom:8px;">Bukti Transfer:</div>
        <form action="payment.php?id=<?php echo $transaction_id; ?>" method="POST" enctype="multipart/form-data" id="payForm">
          <div class="file-zone" id="payFileZone">
            <input type="file" name="bukti_transfer" accept="image/*" required onchange="previewFile(this)">
            <div class="file-zone-icon" id="pfIcon">📎</div>
            <div class="file-zone-text" id="pfText">Klik atau seret file di sini</div>
            <div class="file-zone-hint">JPG, JPEG, PNG · Maks 5MB</div>
          </div>
        </form>
      <?php endif; ?>
    </div>

    <div class="pay-foot">
      <a href="history.php" class="btn btn-secondary" style="flex:1;justify-content:center;">Riwayat</a>
      <?php if (!$success): ?>
      <button type="submit" form="payForm" class="btn btn-primary" style="flex:2;justify-content:center;">
        Kirim Bukti Pembayaran
      </button>
      <?php else: ?>
      <a href="struk.php?id=<?php echo $transaction_id; ?>" class="btn btn-primary" style="flex:2;justify-content:center;">
        Lihat Struk →
      </a>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
function previewFile(input) {
  if (input.files && input.files[0]) {
    document.getElementById('pfIcon').textContent = '✅';
    document.getElementById('pfText').textContent = input.files[0].name;
    document.getElementById('payFileZone').style.borderColor = 'var(--green)';
  }
}
</script>

<?php include '_footer.php'; ?>