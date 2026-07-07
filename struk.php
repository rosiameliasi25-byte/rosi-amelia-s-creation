<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
// struk.php — Receipt / Invoice
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
if (!isset($_GET['id'])) { header("Location: history.php"); exit; }

$id  = intval($_GET['id']);
$uid = (int) $_SESSION['user_id'];

// Security: hanya boleh lihat struk milik sendiri (atau admin)
$tx = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transactions WHERE id = $id AND user_id = $uid"));
if (!$tx) { header("Location: history.php"); exit; }

$details = mysqli_query($conn, "SELECT td.*, p.nama_produk, p.harga FROM transaction_detail td JOIN products p ON td.product_id = p.id WHERE td.transaction_id = $id");

$status_low = strtolower($tx['status']);
$is_success = ($status_low === 'success' || $status_low === 'selesai');

$page_title  = 'Invoice #TRX-'.$id.' · RosiMarket Hub';
$active_page = 'history';
include '_navbar.php';
?>

<style>
  .struk-wrap {
    max-width: 540px;
    margin: 32px auto;
    padding: 0 20px;
    animation: fadeUp 0.4s ease;
  }

  .back-link {
    display:inline-flex;align-items:center;gap:4px;
    font-size:13px;color:var(--txt3);margin-bottom:16px;
    transition:color 0.15s;
  }
  .back-link:hover { color:var(--txtlink); }

  .struk-card {
    background:var(--bg3);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;
  }

  .struk-header {
    padding:22px;
    background:linear-gradient(135deg,rgba(59,130,246,0.07),rgba(139,92,246,0.04));
    border-bottom:1px solid var(--border);
    text-align:center;
  }
  .struk-brand { font-size:18px;font-weight:700;letter-spacing:-0.3px;margin-bottom:2px; }
  .struk-sub   { font-size:12px;color:var(--txt3); }

  .struk-status {
    display:inline-flex;align-items:center;gap:6px;
    padding:6px 14px;border-radius:20px;font-size:12px;font-weight:600;margin-top:10px;
  }

  .struk-meta {
    padding:18px 22px;
    border-bottom:1px solid var(--border);
    display:grid;grid-template-columns:1fr 1fr;gap:12px;
  }
  .meta-item .label { font-size:11px;color:var(--txt3);margin-bottom:3px; }
  .meta-item .value { font-size:13px;font-weight:500;color:var(--txt); }
  .meta-mono { font-family:'JetBrains Mono',monospace;color:var(--txtlink) !important; }

  .struk-items { padding:18px 22px;border-bottom:1px solid var(--border); }
  .items-label { font-size:11px;font-weight:600;color:var(--txt3);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px; }

  .order-item {
    display:flex;align-items:center;justify-content:space-between;
    padding:9px 0;border-bottom:1px solid rgba(99,130,190,0.07);font-size:13px;
  }
  .order-item:last-child { border-bottom:none; }
  .order-item .name { color:var(--txt2);flex:1; }
  .order-item .qty  { color:var(--txt3);font-size:12px;margin:0 12px; }
  .order-item .sub  { font-weight:600;color:var(--txt);font-family:'JetBrains Mono',monospace;font-size:12px; }

  .struk-total {
    padding:18px 22px;border-bottom:1px solid var(--border);
    display:flex;align-items:center;justify-content:space-between;
  }
  .total-label { font-size:14px;font-weight:600;color:var(--txt2); }
  .total-num   { font-size:22px;font-weight:700;color:var(--accent);font-family:'JetBrains Mono',monospace; }

  .struk-footer { padding:18px 22px;display:flex;gap:10px;flex-wrap:wrap; }

  .struk-notice {
    background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-md);
    padding:12px 16px;margin:0 22px 18px;font-size:12px;color:var(--txt2);
    display:flex;align-items:flex-start;gap:8px;
  }

  @keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
  @media print {
    .gh-nav,.gh-footer,.back-link,.struk-footer { display:none !important; }
    body { background:#fff; color:#000; }
    .struk-card { border:1px solid #ddd; }
  }
</style>

<div class="struk-wrap">
  <a class="back-link" href="history.php">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M19 12H5M5 12l7 7M5 12l7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    Kembali ke Riwayat
  </a>

  <div class="struk-card">
    <!-- Header -->
    <div class="struk-header">
      <div class="struk-brand">RosiMarket Hub</div>
      <div class="struk-sub">Bukti Pembayaran Resmi</div>
      <div class="struk-status <?php echo $is_success ? 'badge-green' : 'badge-amber'; ?> badge">
        <?php echo $is_success ? '✅ ' : '⏳ '; echo ucfirst($tx['status']); ?>
      </div>
    </div>

    <!-- Meta info -->
    <div class="struk-meta">
      <div class="meta-item">
        <div class="label">No. Transaksi</div>
        <div class="value meta-mono">#TRX-<?php echo $id; ?></div>
      </div>
      <div class="meta-item">
        <div class="label">WhatsApp</div>
        <div class="value"><?php echo htmlspecialchars($tx['kontak'] ?? '-'); ?></div>
      </div>
      <div class="meta-item">
        <div class="label">Pelanggan</div>
        <div class="value"><?php echo htmlspecialchars($_SESSION['nama']); ?></div>
      </div>
      <div class="meta-item">
        <div class="label">Tanggal</div>
        <div class="value"><?php echo isset($tx['created_at']) ? date('d M Y', strtotime($tx['created_at'])) : date('d M Y'); ?></div>
      </div>
    </div>

    <!-- Order items -->
    <div class="struk-items">
      <div class="items-label">Detail Pesanan</div>
      <?php while ($row = mysqli_fetch_assoc($details)): ?>
      <div class="order-item">
        <span class="name"><?php echo htmlspecialchars($row['nama_produk']); ?></span>
        <span class="qty">×<?php echo $row['qty']; ?></span>
        <span class="sub">Rp <?php echo number_format($row['harga'] * $row['qty']); ?></span>
      </div>
      <?php endwhile; ?>
    </div>

    <!-- Total -->
    <div class="struk-total">
      <span class="total-label">Total Pembayaran</span>
      <span class="total-num">Rp <?php echo number_format($tx['total']); ?></span>
    </div>

    <!-- Notice -->
    <?php if (!$is_success): ?>
    <div class="struk-notice">
      <span>⏳</span>
      <span>Pembayaran Anda sedang dalam proses verifikasi oleh admin. Mohon tunggu konfirmasi melalui WhatsApp yang Anda daftarkan.</span>
    </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="struk-footer">
      <a href="products.php" class="btn btn-secondary" style="flex:1;justify-content:center;">Lanjut Belanja</a>
      <button onclick="window.print()" class="btn btn-primary" style="flex:1;justify-content:center;">
        🖨️ Cetak Struk
      </button>
    </div>
  </div>
</div>

<?php include '_footer.php'; ?>