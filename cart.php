<?php
session_start();
include 'db.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}
$uid=(int)$_SESSION['user_id'];
$q=mysqli_query($conn,"SELECT c.*,p.nama_produk,p.harga,p.deskripsi FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$uid ORDER BY c.id DESC");
$items=[];$total=0;
while($r=mysqli_fetch_assoc($q)){$r['subtotal']=$r['harga']*$r['qty'];$total+=$r['subtotal'];$items[]=$r;}
$page_title='Keranjang · RosiMarket Hub';$active_page='cart';include '_navbar.php';
?>
<style>
[data-theme="light"] .cart-card,
[data-theme="light"] .summary-card,
[data-theme="light"] .pay-info { background:#fff; }
[data-theme="light"] .pay-info { background:#f6f8fa; }
[data-theme="light"] .cart-item:hover { background:#f0f3f6; }

.page-hero { border-bottom:1px solid var(--border);padding:24px 0 0; }
.page-inner { max-width:1000px;margin:0 auto;padding:0 24px; }
.page-head { display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-bottom:20px; }
.page-title { font-size:20px;font-weight:700;letter-spacing:-0.3px;margin-bottom:4px; }
.page-sub { font-size:13px;color:var(--txt2); }

.cart-wrap { max-width:1000px;margin:24px auto;padding:0 24px;display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start; }
.cart-card { background:var(--bg3);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;transition:background .25s; }
.cart-item { display:flex;align-items:center;gap:14px;padding:16px 20px;border-bottom:1px solid color-mix(in srgb,var(--border) 60%,transparent);transition:background .15s; }
.cart-item:last-child { border-bottom:none; }
.cart-item:hover { background:var(--bghover); }
.cart-item-icon { width:40px;height:40px;border-radius:var(--r-sm);background:var(--accdim);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.cart-item-info { flex:1;min-width:0; }
.cart-item-name { font-size:13px;font-weight:600;color:var(--txt);margin-bottom:2px; }
.cart-item-desc { font-size:11px;color:var(--txt3);display:flex;align-items:center;gap:10px;flex-wrap:wrap; }
.cart-item-price { font-size:14px;font-weight:700;color:var(--accent);font-family:'JetBrains Mono',monospace;white-space:nowrap; }
.cart-empty { padding:48px;text-align:center;color:var(--txt3); }
.cart-empty div { font-size:40px;margin-bottom:12px; }

.qty-box { display:inline-flex;align-items:center;gap:6px; }
.qty-btn { width:24px;height:24px;border-radius:50%;border:1px solid var(--border);background:var(--bg2);color:var(--txt);display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:14px;line-height:1; }
.qty-num { min-width:18px;text-align:center;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--txt2); }

.summary-card { background:var(--bg3);border:1px solid var(--border);border-radius:var(--r-lg);padding:20px;position:sticky;top:76px;transition:background .25s; }
.summary-title { font-size:14px;font-weight:600;color:var(--txt);margin-bottom:16px; }
.summary-row { display:flex;justify-content:space-between;align-items:center;font-size:13px;margin-bottom:10px; }
.summary-row .label { color:var(--txt2); }
.summary-row .value { font-weight:500;color:var(--txt); }
.summary-divider { height:1px;background:var(--border);margin:14px 0; }
.summary-total { display:flex;justify-content:space-between;align-items:center;font-size:15px;font-weight:700; }
.summary-total .num { color:var(--accent);font-family:'JetBrains Mono',monospace; }

.pay-info { background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-md);padding:14px;margin:14px 0;transition:background .25s; }
.pay-info-title { font-size:12px;font-weight:600;color:var(--txt2);margin-bottom:10px; }
.pay-method { display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid color-mix(in srgb,var(--border) 50%,transparent);font-size:12px; }
.pay-method:last-child { border-bottom:none; }
.pay-method .bank { color:var(--txt2);font-weight:500; }
.pay-method .norek { color:var(--accent);font-weight:600;font-family:'JetBrains Mono',monospace;font-size:11px; }

@media(max-width:768px){.cart-wrap{grid-template-columns:1fr;}}
</style>

<div class="page-hero">
  <div class="page-inner">
    <div class="page-head">
      <div>
        <div class="page-title">Keranjang Belanja</div>
        <div class="page-sub"><?php echo count($items); ?> item di keranjang Anda</div>
      </div>
      <a href="products.php" class="btn btn-secondary">← Lanjut Belanja</a>
    </div>
  </div>
</div>

<div class="cart-wrap">
  <div class="cart-card">
    <?php if(empty($items)): ?>
      <div class="cart-empty">
        <div>🛒</div><p style="margin-bottom:16px;">Keranjang Anda masih kosong.</p>
        <a href="products.php" class="btn btn-primary">Mulai Belanja →</a>
      </div>
    <?php else: ?>
      <?php foreach($items as $item): ?>
      <div class="cart-item">
        <div class="cart-item-icon">📦</div>
        <div class="cart-item-info">
          <div class="cart-item-name"><?php echo htmlspecialchars($item['nama_produk']); ?></div>
          <div class="cart-item-desc">
            <span>Qty:</span>
            <span class="qty-box">
              <a class="qty-btn" href="cart_action.php?action=decrease&id=<?php echo (int)$item['id']; ?>">-</a>
              <span class="qty-num"><?php echo (int)$item['qty']; ?></span>
              <a class="qty-btn" href="cart_action.php?action=increase&id=<?php echo (int)$item['id']; ?>">+</a>
            </span>
            <span>× Rp <?php echo number_format($item['harga']); ?></span>
          </div>
        </div>
        <div class="cart-item-price">Rp <?php echo number_format($item['subtotal']); ?></div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <?php if(!empty($items)): ?>
  <div class="summary-card">
    <div class="summary-title">Ringkasan Pesanan</div>
    <div class="summary-row"><span class="label">Subtotal (<?php echo count($items); ?> item)</span><span class="value">Rp <?php echo number_format($total); ?></span></div>
    <div class="summary-row"><span class="label">Biaya layanan</span><span class="value" style="color:var(--green);">Gratis</span></div>
    <div class="summary-divider"></div>
    <div class="summary-total"><span>Total</span><span class="num">Rp <?php echo number_format($total); ?></span></div>

    <div class="pay-info">
      <div class="pay-info-title">Rekening Tujuan Transfer:</div>
      <div class="pay-method"><span class="bank">DANA</span><span class="norek">0857-9879-8329</span></div>
      <div class="pay-method"><span class="bank">SeaBank</span><span class="norek">901-8890-76542</span></div>
      <div class="pay-method"><span class="bank">BRI</span><span class="norek">0123-01-567209-53-8</span></div>
      <div style="font-size:11px;color:var(--txt3);margin-top:8px;">a.n. RosiMarket Hub</div>
    </div>

    <a href="checkout.php" class="btn btn-primary btn-block" style="margin-top:14px;">Lanjut ke Pembayaran →</a>
  </div>
  <?php endif; ?>
</div>

<?php include '_footer.php'; ?>