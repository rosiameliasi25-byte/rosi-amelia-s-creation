<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
session_start(); include 'db.php';
if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}
$uid=(int)$_SESSION['user_id'];
$q=mysqli_query($conn,"SELECT * FROM transactions WHERE user_id=$uid ORDER BY id DESC");
$page_title='Riwayat Transaksi · RosiMarket Hub';$active_page='history';include '_navbar.php';
?>
<style>
[data-theme="light"] .history-card { background:#fff; }
[data-theme="light"] .trx-row:hover td,
[data-theme="light"] .trx-row:hover { background:#f0f3f6; }

.page-hero  { border-bottom:1px solid var(--border);padding:24px 0 0; }
.page-inner { max-width:900px;margin:0 auto;padding:0 24px; }
.page-head  { display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-bottom:20px; }
.page-title { font-size:20px;font-weight:700;letter-spacing:-0.3px;margin-bottom:4px; }
.page-sub   { font-size:13px;color:var(--txt2); }
.history-wrap { max-width:900px;margin:24px auto;padding:0 24px; }
.history-card { background:var(--bg3);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;transition:background .25s; }

.trx-row {
  display:grid;grid-template-columns:140px 1fr 130px 120px 80px;
  align-items:center;gap:12px;
  padding:14px 20px;border-bottom:1px solid color-mix(in srgb,var(--border) 60%,transparent);
  transition:background .15s;
}
.trx-row:last-child  { border-bottom:none; }
.trx-row:hover       { background:var(--bghover); }
.trx-head            { font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:var(--txt3);border-bottom:1px solid var(--border) !important;font-weight:600;cursor:default; }
.trx-head:hover      { background:transparent !important; }
.trx-nota    { font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:600;color:var(--txtlink); }
.trx-total   { font-size:13px;font-weight:700;color:var(--txt);font-family:'JetBrains Mono',monospace; }

.status-pill { display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600; }
.s-pending { background:var(--amber-dim,rgba(245,158,11,0.12));color:var(--amber);border:1px solid rgba(245,158,11,0.25); }
.s-success { background:var(--green-dim,rgba(34,197,94,0.12));color:var(--green);border:1px solid rgba(34,197,94,0.25); }
.trx-actions { display:flex;gap:6px;justify-content:flex-end; }

.empty-state  { padding:60px;text-align:center;color:var(--txt3); }
.empty-state div { font-size:40px;margin-bottom:12px; }

@media(max-width:700px){
  .trx-row { grid-template-columns:1fr 1fr;gap:6px; }
  .trx-head { display:none; }
}
</style>

<div class="page-hero">
  <div class="page-inner">
    <div class="page-head">
      <div>
        <div class="page-title">Riwayat Transaksi</div>
        <div class="page-sub">Semua catatan transaksi akun <?php echo htmlspecialchars($_SESSION['nama']); ?></div>
      </div>
      <a href="products.php" class="btn btn-secondary">← Lanjut Belanja</a>
    </div>
  </div>
</div>

<div class="history-wrap">
  <div class="history-card">
    <?php if(mysqli_num_rows($q)==0): ?>
      <div class="empty-state">
        <div>📋</div><p style="margin-bottom:16px;">Belum ada transaksi.</p>
        <a href="products.php" class="btn btn-primary">Ke Marketplace →</a>
      </div>
    <?php else: ?>
      <div class="trx-row trx-head">
        <span>No. Transaksi</span><span>Kontak</span><span>Total</span><span>Status</span><span></span>
      </div>
      <?php while($r=mysqli_fetch_assoc($q)):
      $sl=strtolower($r['status']);
      $sc=in_array($sl, ['success','selesai','sukses'], true) ? 's-success' : 's-pending';
      ?>
      <div class="trx-row">
        <div class="trx-nota">#TRX-<?php echo $r['id']; ?></div>
        <div style="font-size:12px;color:var(--txt2);"><?php echo htmlspecialchars($r['kontak']??'-'); ?></div>
        <div class="trx-total">Rp <?php echo number_format($r['total']); ?></div>
        <div><span class="status-pill <?php echo $sc; ?>"><?php echo ucfirst($r['status']); ?></span></div>
        <div class="trx-actions"><a href="struk.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-secondary">Detail</a></div>
      </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</div>

<?php include '_footer.php'; ?>