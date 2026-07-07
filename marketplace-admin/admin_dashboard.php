<?php
/* ============================================================
   ADMIN DASHBOARD — RosiMarket Hub
   Tema: Minimalist Operational Dashboard
   Disesuaikan dengan skema asli:
     - products(id, nama_produk, deskripsi, harga, stok, kategori, gambar)
     - digital_products(id, product_name, ..., is_active, created_at, ...)
     - digital_reviews(id, product_id, user_id, rating, review_text, created_at)
     - transactions(id, user_id, total, kontak, bukti_transfer, status, created_at)
     - transaction_detail(id, transaction_id, product_id, qty)

   ── CONFIG: ubah di sini kalau ada penyesuaian lanjutan ──
   ============================================================ */

require 'admin_auth.php';
include '../db.php';

$CFG = [
    'tx_pending_status'   => 'pending',
    'tx_success_status'   => 'sukses',
    'tx_rejected_status'  => 'ditolak',
    'stok_minim_batas'    => 10,                       // ambang batas "stok menipis"
    'bukti_transfer_path' => '../uploads/',// folder penyimpanan bukti transfer (new6/bukti/)
];

function safe_query($conn, $sql) {
    try {
        $res = mysqli_query($conn, $sql);
        return $res ?: false;
    } catch (\mysqli_sql_exception $e) {
        error_log('[admin_dashboard] Query gagal: ' . $e->getMessage() . ' | SQL: ' . $sql);
        return false;
    }
}

// ---------- 1. STAT RINGKAS (Overview) ----------
$totalPendapatan = 0;
$q = safe_query($conn, "SELECT SUM(total) as total FROM transactions WHERE status = '{$CFG['tx_success_status']}'");
if ($q && $row = mysqli_fetch_assoc($q)) $totalPendapatan = (float)($row['total'] ?? 0);

$totalTransaksi = 0;
$q = safe_query($conn, "SELECT COUNT(*) as c FROM transactions");
if ($q && $row = mysqli_fetch_assoc($q)) $totalTransaksi = (int)$row['c'];

$totalUser = 0;
$q = safe_query($conn, "SELECT COUNT(*) as c FROM users WHERE role = 'user'");
if ($q && $row = mysqli_fetch_assoc($q)) $totalUser = (int)$row['c'];

$produkDigitalAktif = 0;
$q = safe_query($conn, "SELECT COUNT(*) as c FROM digital_products WHERE is_active = 1");
if ($q && $row = mysqli_fetch_assoc($q)) $produkDigitalAktif = (int)$row['c'];

// ---------- 2. TREN PENJUALAN 14 HARI TERAKHIR (line chart) ----------
$trendLabels = [];
$trendValues = [];
$q = safe_query($conn, "
    SELECT DATE(created_at) as tgl, SUM(total) as total
    FROM transactions
    WHERE status = '{$CFG['tx_success_status']}'
      AND created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
    GROUP BY DATE(created_at)
    ORDER BY tgl ASC
");
if ($q) {
    while ($row = mysqli_fetch_assoc($q)) {
        $trendLabels[] = date('d M', strtotime($row['tgl']));
        $trendValues[] = (float)$row['total'];
    }
}

// ---------- 3. PRODUK TERLARIS (bar chart, top 5) ----------
// Catatan: transaction_detail.product_id mengacu ke tabel `products` (top-up game / katalog admin)
$topLabels = [];
$topValues = [];
$q = safe_query($conn, "
    SELECT p.nama_produk as nama, SUM(td.qty) as terjual
    FROM transaction_detail td
    JOIN products p ON p.id = td.product_id
    GROUP BY td.product_id
    ORDER BY terjual DESC
    LIMIT 5
");
if ($q) {
    while ($row = mysqli_fetch_assoc($q)) {
        $topLabels[] = $row['nama'];
        $topValues[] = (int)$row['terjual'];
    }
}

// ---------- 4. STOK MENIPIS ----------
$produkStokMenipis = [];
$q = safe_query($conn, "
    SELECT id, nama_produk, stok, kategori
    FROM products
    WHERE stok <= {$CFG['stok_minim_batas']}
    ORDER BY stok ASC
    LIMIT 20
");
if ($q) { while ($row = mysqli_fetch_assoc($q)) $produkStokMenipis[] = $row; }
$jumlahStokMenipis = count($produkStokMenipis);

// ---------- 5. MODERASI: produk digital (toggle aktif/nonaktif) ----------
$produkDigital = [];
$q = safe_query($conn, "SELECT id, product_name, price_idr, is_active, created_at FROM digital_products ORDER BY created_at DESC LIMIT 50");
if ($q) { while ($row = mysqli_fetch_assoc($q)) $produkDigital[] = $row; }

// ---------- 6. MODERASI: ulasan pengguna ----------
$ulasanList = [];
$q = safe_query($conn, "
    SELECT r.id, r.rating, r.review_text, r.created_at,
           u.nama as nama_user, dp.nama_produk as product_name
    FROM digital_reviews r
    LEFT JOIN users u ON u.id = r.user_id
    LEFT JOIN products dp ON dp.id = r.product_id
    ORDER BY r.created_at DESC
    LIMIT 30
");
if ($q) { while ($row = mysqli_fetch_assoc($q)) $ulasanList[] = $row; }

// ---------- 7. TRANSAKSI: semua transaksi terbaru ----------
$transaksiList = [];
$q = safe_query($conn, "
    SELECT t.id, t.total, t.kontak, t.bukti_transfer, t.status, t.created_at, u.nama as nama_user
    FROM transactions t
    LEFT JOIN users u ON u.id = t.user_id
    ORDER BY t.created_at DESC
    LIMIT 50
");
if ($q) { while ($row = mysqli_fetch_assoc($q)) $transaksiList[] = $row; }

$jumlahTxPending = 0;
foreach ($transaksiList as $t) {
    if ($t['status'] === $CFG['tx_pending_status']) $jumlahTxPending++;
}

$namaAdmin = $_SESSION['nama'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Console · RosiMarket Hub</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="shell">

  <!-- ===================== SIDEBAR ===================== -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
      <button class="sidebar-toggle" id="sidebarToggle" title="Lipat/Buka sidebar">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
      </button>
      <span class="sidebar-brand">RosiMarket <em>Admin</em></span>
    </div>

    <nav class="sidebar-nav">
      <button class="nav-item active" data-tab="overview">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 12l4-4 4 4 6-8 4 4"/><path d="M3 19h18"/></svg>
        <span>Overview</span>
      </button>
      <button class="nav-item" data-tab="moderasi">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
        <span>Moderasi</span>
      </button>
      <button class="nav-item" data-tab="transaksi">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/></svg>
        <span>Transaksi</span>
        <?php if ($jumlahTxPending > 0): ?><span class="nav-badge alert"><?php echo $jumlahTxPending; ?></span><?php endif; ?>
      </button>
    </nav>

    <div class="sidebar-bottom">
      <a href="admin_logout.php" class="nav-item logout">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
        <span>Keluar</span>
      </a>
    </div>
  </aside>

  <!-- ===================== MAIN ===================== -->
  <main class="main">

    <!-- Top bar -->
    <header class="topbar">
      <div class="topbar-greet">
        <h1>Halo, <?php echo htmlspecialchars($namaAdmin); ?></h1>
        <span>Berikut ringkasan operasional hari ini</span>
      </div>

      <div class="topbar-actions">
        <?php if ($jumlahTxPending > 0): ?>
        <button class="action-pill" data-goto="transaksi">
          <span class="dot danger"></span>
          <?php echo $jumlahTxPending; ?> Pembayaran Menunggu Verifikasi
        </button>
        <?php endif; ?>

        <?php if ($jumlahStokMenipis > 0): ?>
        <button class="action-pill" data-goto="overview">
          <span class="dot warn"></span>
          <?php echo $jumlahStokMenipis; ?> Produk Stok Menipis
        </button>
        <?php endif; ?>

        <?php if ($jumlahTxPending === 0 && $jumlahStokMenipis === 0): ?>
        <span class="action-pill calm">
          <span class="dot ok"></span> Semua beres, tidak ada antrean
        </span>
        <?php endif; ?>
      </div>
    </header>

    <!-- ============ TAB: OVERVIEW ============ -->
    <section class="tab-panel active" id="tab-overview">

      <div class="stat-row">
        <div class="stat-card">
          <span class="stat-label">Total Pendapatan</span>
          <span class="stat-num">Rp <?php echo number_format($totalPendapatan, 0, ',', '.'); ?></span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Total Transaksi</span>
          <span class="stat-num"><?php echo number_format($totalTransaksi, 0, ',', '.'); ?></span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Pengguna Terdaftar</span>
          <span class="stat-num"><?php echo number_format($totalUser, 0, ',', '.'); ?></span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Produk Digital Aktif</span>
          <span class="stat-num"><?php echo number_format($produkDigitalAktif, 0, ',', '.'); ?></span>
        </div>
      </div>

      <div class="chart-row">
        <div class="chart-card">
          <div class="chart-card-head">
            <h3>Tren Penjualan</h3>
            <span>14 hari terakhir</span>
          </div>
          <canvas id="chartTrend" height="110"></canvas>
        </div>
        <div class="chart-card">
          <div class="chart-card-head">
            <h3>Produk Terlaris</h3>
            <span>Top 5</span>
          </div>
          <canvas id="chartTop" height="110"></canvas>
        </div>
      </div>

      <?php if ($jumlahStokMenipis > 0): ?>
      <div class="panel-card" style="margin-top:14px;">
        <div class="panel-card-head">
          <h3>Stok Menipis</h3>
          <span><?php echo $jumlahStokMenipis; ?> item ≤ <?php echo $CFG['stok_minim_batas']; ?> pcs</span>
        </div>
        <table class="data-table">
          <thead><tr><th>Produk</th><th>Kategori</th><th>Sisa Stok</th></tr></thead>
          <tbody>
            <?php foreach ($produkStokMenipis as $p): ?>
            <tr>
              <td><?php echo htmlspecialchars($p['nama_produk']); ?></td>
              <td><?php echo htmlspecialchars($p['kategori']); ?></td>
              <td><span class="badge <?php echo $p['stok'] == 0 ? 'danger' : 'warn'; ?>"><?php echo $p['stok']; ?> pcs</span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </section>

    <!-- ============ TAB: MODERASI ============ -->
    <section class="tab-panel" id="tab-moderasi">

      <div class="panel-card">
        <div class="panel-card-head">
          <h3>Produk Digital</h3>
          <span><?php echo count($produkDigital); ?> item</span>
        </div>
        <table class="data-table">
          <thead><tr><th>Nama Produk</th><th>Harga</th><th>Status</th><th class="col-action">Aksi</th></tr></thead>
          <tbody>
            <?php foreach ($produkDigital as $p): ?>
            <tr data-row-id="<?php echo $p['id']; ?>">
              <td><?php echo htmlspecialchars($p['product_name']); ?></td>
              <td>Rp <?php echo number_format($p['price_idr'], 0, ',', '.'); ?></td>
              <td><span class="badge js-status-badge <?php echo $p['is_active'] ? 'ok' : 'warn'; ?>"><?php echo $p['is_active'] ? 'Aktif' : 'Nonaktif'; ?></span></td>
              <td class="col-action">
                <button class="btn-mini js-toggle-btn <?php echo $p['is_active'] ? 'reject' : 'approve'; ?>"
                        data-action="toggle_active_product" data-id="<?php echo $p['id']; ?>"
                        data-active="<?php echo $p['is_active']; ?>">
                  <?php echo $p['is_active'] ? 'Nonaktifkan' : 'Aktifkan'; ?>
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="panel-card" style="margin-top:14px;">
        <div class="panel-card-head">
          <h3>Ulasan Pengguna</h3>
          <span><?php echo count($ulasanList); ?> ulasan terbaru</span>
        </div>

        <?php if (empty($ulasanList)): ?>
          <div class="empty-block">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
            <p>Belum ada ulasan masuk.</p>
          </div>
        <?php else: ?>
        <table class="data-table">
          <thead><tr><th>Produk</th><th>Pengguna</th><th>Rating</th><th>Ulasan</th><th class="col-action">Aksi</th></tr></thead>
          <tbody>
            <?php foreach ($ulasanList as $r): ?>
            <tr data-row-id="<?php echo $r['id']; ?>">
              <td><?php echo htmlspecialchars($r['product_name'] ?? '—'); ?></td>
              <td><?php echo htmlspecialchars($r['nama_user'] ?? '—'); ?></td>
              <td><span class="mono">★ <?php echo (int)$r['rating']; ?></span></td>
              <td style="white-space:normal;max-width:280px;"><?php echo htmlspecialchars(mb_strimwidth($r['review_text'], 0, 90, '…')); ?></td>
              <td class="col-action">
                <button class="btn-mini reject" data-action="delete_review" data-id="<?php echo $r['id']; ?>">Hapus</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
    </section>

    <!-- ============ TAB: TRANSAKSI ============ -->
    <section class="tab-panel" id="tab-transaksi">
      <div class="panel-card">
        <div class="panel-card-head">
          <h3>Verifikasi Pembayaran</h3>
          <span><?php echo count($transaksiList); ?> transaksi terbaru</span>
        </div>

        <table class="data-table">
          <thead>
            <tr><th>ID</th><th>Pengguna</th><th>Kontak</th><th>Tanggal</th><th>Jumlah</th><th>Bukti</th><th>Status</th><th class="col-action">Aksi</th></tr>
          </thead>
          <tbody>
            <?php foreach ($transaksiList as $t): ?>
            <tr data-row-id="<?php echo $t['id']; ?>">
              <td class="mono">#<?php echo $t['id']; ?></td>
              <td><?php echo htmlspecialchars($t['nama_user'] ?? '—'); ?></td>
              <td class="mono"><?php echo htmlspecialchars($t['kontak']); ?></td>
              <td><?php echo date('d M Y', strtotime($t['created_at'])); ?></td>
              <td class="mono">Rp <?php echo number_format($t['total'], 0, ',', '.'); ?></td>
              <td>
                <?php if ($t['bukti_transfer']): ?>
                  <a href="<?php echo $CFG['bukti_transfer_path'] . htmlspecialchars($t['bukti_transfer']); ?>" target="_blank" class="link-bukti">Lihat</a>
                <?php else: ?>
                  <span class="muted">—</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge js-status-badge <?php echo $t['status'] === $CFG['tx_success_status'] ? 'ok' : ($t['status'] === $CFG['tx_rejected_status'] ? 'danger' : 'warn'); ?>">
                  <?php echo htmlspecialchars($t['status']); ?>
                </span>
              </td>
              <td class="col-action">
                <?php if ($t['status'] === $CFG['tx_pending_status']): ?>
                  <button class="btn-mini approve" data-action="verify_transaction" data-id="<?php echo $t['id']; ?>">Verifikasi</button>
                  <button class="btn-mini reject" data-action="reject_transaction" data-id="<?php echo $t['id']; ?>">Tolak</button>
                <?php else: ?>
                  <span class="muted">—</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

  </main>
</div>

<script>
  window.__chartData = {
    trendLabels: <?php echo json_encode($trendLabels); ?>,
    trendValues: <?php echo json_encode($trendValues); ?>,
    topLabels:   <?php echo json_encode($topLabels); ?>,
    topValues:   <?php echo json_encode($topValues); ?>
  };
</script>
<script src="admin.js"></script>
</body>
</html>