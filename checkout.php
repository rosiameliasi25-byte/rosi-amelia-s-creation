<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
session_start(); include 'db.php';
if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}
$uid=(int)$_SESSION['user_id'];
$tq=mysqli_query($conn,"SELECT SUM(p.harga*c.qty) as total FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$uid");
$total=mysqli_fetch_assoc($tq)['total']??0;
if($total==0){header("Location: cart.php");exit;}
$page_title='Checkout · RosiMarket Hub';$active_page='cart';include '_navbar.php';
?>
<style>
[data-theme="light"] .checkout-card,
[data-theme="light"] .total-pill  { background:#fff; }
[data-theme="light"] .total-pill  { background:#f6f8fa; }
[data-theme="light"] .file-zone   { background:#f6f8fa; }

.checkout-wrap { max-width:520px;margin:36px auto;padding:0 20px;animation:fadeUp .4s ease; }
.back-link { display:inline-flex;align-items:center;gap:4px;font-size:13px;color:var(--txt3);margin-bottom:16px;transition:color .15s; }
.back-link:hover { color:var(--txtlink); }
.checkout-card { background:var(--bg3);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;transition:background .25s; }
.checkout-header {
  padding:20px 22px;border-bottom:1px solid var(--border);
  background:linear-gradient(90deg,var(--accdim),transparent);
  display:flex;align-items:center;gap:10px;transition:background .25s;
}
.checkout-header-title { font-size:14px;font-weight:600; }
.checkout-header-sub   { font-size:12px;color:var(--txt3); }
.checkout-body { padding:22px; }

.total-pill { display:flex;align-items:center;justify-content:space-between;background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-md);padding:14px 18px;margin-bottom:20px;transition:background .25s; }
.total-pill .lbl { font-size:12px;color:var(--txt3); }
.total-pill .num { font-size:20px;font-weight:700;color:var(--accent);font-family:'JetBrains Mono',monospace; }

.fg{margin-bottom:14px;}
.fl{display:block;font-size:12px;font-weight:600;color:var(--txt2);margin-bottom:5px;}
.fi{width:100%;padding:9px 12px;background:var(--input-bg);border:1px solid var(--border);border-radius:var(--r-sm);color:var(--txt);font-family:inherit;font-size:13px;outline:none;transition:border-color .2s,background .25s;}
.fi:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accdim);}
.fi[readonly]{opacity:.55;cursor:not-allowed;}
.fi::placeholder{color:var(--txt3);}

.file-zone {
  border:2px dashed var(--border);border-radius:var(--r-md);padding:24px;text-align:center;
  cursor:pointer;position:relative;overflow:hidden;transition:border-color .2s,background .25s;background:var(--bg2);
}
.file-zone:hover { border-color:var(--accent); }
.file-zone input[type=file] { position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%; }
.file-zone-icon { font-size:28px;margin-bottom:6px; }
.file-zone-text { font-size:13px;color:var(--txt2); }
.file-zone-hint { font-size:11px;color:var(--txt3);margin-top:3px; }
.checkout-footer { padding:16px 22px;border-top:1px solid var(--border);display:flex;gap:10px; }

@keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
</style>

<div class="checkout-wrap">
  <a class="back-link" href="cart.php">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M19 12H5M5 12l7 7M5 12l7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    Kembali ke Keranjang
  </a>

  <div class="checkout-card">
    <div class="checkout-header">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2" stroke="currentColor" stroke-width="1.8"/><line x1="1" y1="10" x2="23" y2="10" stroke="currentColor" stroke-width="1.8"/></svg>
      <div>
        <div class="checkout-header-title">Informasi Pembayaran</div>
        <div class="checkout-header-sub">Langkah terakhir — unggah bukti transfer</div>
      </div>
    </div>

    <div class="checkout-body">
      <?php
        $checkout_errors = [
          'kontak'   => 'Nomor WhatsApp wajib diisi.',
          'upload'   => 'Bukti transfer wajib diunggah.',
          'filetype' => 'File harus berupa gambar JPG/PNG.',
          'filesize' => 'Ukuran file maksimal 5MB.',
          'movefail' => 'Gagal menyimpan file, coba lagi.',
          'dberror'  => 'Terjadi kesalahan sistem, silakan coba lagi.',
        ];
        $err_key = $_GET['error'] ?? '';
        if (isset($checkout_errors[$err_key])):
      ?>
        <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($checkout_errors[$err_key]); ?></div>
      <?php endif; ?>

      <div class="total-pill">
        <div class="lbl">Total Tagihan</div>
        <div class="num">Rp <?php echo number_format($total); ?></div>
      </div>

      <form action="proses_checkout.php" method="POST" enctype="multipart/form-data" id="checkoutForm">
        <div class="fg"><label class="fl">Nama Lengkap</label>
          <input class="fi" type="text" value="<?php echo htmlspecialchars($_SESSION['nama']); ?>" readonly></div>
        <div class="fg"><label class="fl">Nomor WhatsApp (untuk konfirmasi)</label>
          <input class="fi" type="tel" name="kontak" placeholder="Contoh: 08123456789" required></div>
        <div class="fg"><label class="fl">Bukti Transfer</label>
          <div class="file-zone" id="fz">
            <input type="file" name="bukti" accept="image/*" required onchange="previewFile(this)">
            <div class="file-zone-icon" id="fzIcon">📎</div>
            <div class="file-zone-text" id="fzText">Klik atau seret file di sini</div>
            <div class="file-zone-hint">Format: JPG, PNG · Maks 5MB</div>
          </div>
        </div>
      </form>
    </div>

    <div class="checkout-footer">
      <a href="cart.php" class="btn btn-secondary" style="flex:1;justify-content:center;">Batal</a>
      <button type="submit" form="checkoutForm" class="btn btn-primary" style="flex:2;justify-content:center;">✅ Konfirmasi Pembayaran</button>
    </div>
  </div>

  <p style="text-align:center;font-size:12px;color:var(--txt3);margin-top:14px;">🔒 Data pembayaran diproses secara aman</p>
</div>

<script>
function previewFile(inp){
  if(inp.files&&inp.files[0]){
    document.getElementById('fzIcon').textContent='✅';
    document.getElementById('fzText').textContent=inp.files[0].name;
    document.getElementById('fz').style.borderColor='var(--green)';
  }
}
</script>

<?php include '_footer.php'; ?>