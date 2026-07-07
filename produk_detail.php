<?php
// produk_detail.php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
include 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$uid = (int) $_SESSION['user_id'];
$product_id = (int) ($_GET['id'] ?? 0);

if ($product_id <= 0) { header("Location: products.php"); exit; }

// ---------- Ambil data produk ----------
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$product = mysqli_stmt_get_result($stmt)->fetch_assoc();
mysqli_stmt_close($stmt);

if (!$product) { header("Location: products.php"); exit; }

// ---------- Cek apakah user pernah beli produk ini dengan status sukses ----------
$stmt = mysqli_prepare($conn, "
    SELECT COUNT(*) as c
    FROM transaction_detail td
    JOIN transactions t ON t.id = td.transaction_id
    WHERE t.user_id = ? AND td.product_id = ? AND t.status = 'sukses'
");
mysqli_stmt_bind_param($stmt, "ii", $uid, $product_id);
mysqli_stmt_execute($stmt);
$sudah_beli = (int) mysqli_stmt_get_result($stmt)->fetch_assoc()['c'] > 0;
mysqli_stmt_close($stmt);

// ---------- Cek apakah user sudah pernah menulis ulasan untuk produk ini ----------
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as c FROM digital_reviews WHERE product_id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $product_id, $uid);
mysqli_stmt_execute($stmt);
$sudah_ulas = (int) mysqli_stmt_get_result($stmt)->fetch_assoc()['c'] > 0;
mysqli_stmt_close($stmt);

$boleh_ulas = $sudah_beli && !$sudah_ulas;

// ---------- Proses submit ulasan ----------
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!$boleh_ulas) {
        header("Location: produk_detail.php?id=$product_id&error=notallowed");
        exit;
    }

    $rating = (int) ($_POST['rating'] ?? 0);
    $review_text = trim($_POST['review_text'] ?? '');

    if ($rating < 1 || $rating > 5) {
        header("Location: produk_detail.php?id=$product_id&error=rating");
        exit;
    }
    if ($review_text === '') {
        header("Location: produk_detail.php?id=$product_id&error=empty");
        exit;
    }

    $stmt = mysqli_prepare($conn, "
        INSERT INTO digital_reviews (product_id, user_id, rating, review_text, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    mysqli_stmt_bind_param($stmt, "iiis", $product_id, $uid, $rating, $review_text);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: produk_detail.php?id=$product_id&success=1");
    exit;
}

$err_key = $_GET['error'] ?? '';
$review_errors = [
    'notallowed' => 'Kamu belum bisa menulis ulasan untuk produk ini.',
    'rating'     => 'Rating harus antara 1-5.',
    'empty'      => 'Isi ulasan tidak boleh kosong.',
];

// ---------- Ambil semua ulasan produk ini ----------
$stmt = mysqli_prepare($conn, "
    SELECT r.rating, r.review_text, r.created_at, u.nama as nama_user
    FROM digital_reviews r
    LEFT JOIN users u ON u.id = r.user_id
    WHERE r.product_id = ?
    ORDER BY r.created_at DESC
");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$reviews_result = mysqli_stmt_get_result($stmt);
$reviews = [];
$rating_sum = 0;
while ($r = mysqli_fetch_assoc($reviews_result)) {
    $reviews[] = $r;
    $rating_sum += (int) $r['rating'];
}
mysqli_stmt_close($stmt);

$jumlah_ulasan = count($reviews);
$rata_rating = $jumlah_ulasan > 0 ? round($rating_sum / $jumlah_ulasan, 1) : 0;

$page_title  = htmlspecialchars($product['nama_produk']) . ' · RosiMarket Hub';
$active_page = 'products';
include '_navbar.php';
?>

<style>
  .detail-wrap { max-width: 900px; margin: 32px auto; padding: 0 24px; }
  .back-link { display:inline-flex; align-items:center; gap:4px; font-size:13px; color:var(--txt3); margin-bottom:18px; text-decoration:none; transition:color .15s; }
  .back-link:hover { color:var(--txtlink); }

  .detail-card {
    background:var(--bg3); border:1px solid var(--border); border-radius:16px; overflow:hidden;
    display:flex; flex-wrap:wrap; margin-bottom:28px;
  }

  .detail-img-wrap {
    width:280px; height:220px; flex-shrink:0; background:var(--bg2);
    display:flex; align-items:center; justify-content:center; overflow:hidden;
  }
  .detail-img { width:100%; height:100%; object-fit:contain; object-position:center; display:block; }

  .detail-info { padding:22px 24px; flex:1; min-width:260px; display:flex; flex-direction:column; gap:10px; }
  .detail-name { font-size:20px; font-weight:700; line-height:1.3; }
  .detail-desc { font-size:13px; color:var(--txt2); line-height:1.6; }

  .detail-price-row { display:flex; align-items:baseline; gap:10px; }
  .detail-price { font-size:20px; font-weight:700; color:var(--accent); font-family:'JetBrains Mono',monospace; }
  .detail-stock { font-size:12px; color:var(--txt3); }

  .rating-summary { display:flex; align-items:center; gap:8px; }
  .rating-num { font-size:22px; font-weight:700; color:var(--txt); }
  .rating-stars { color:#f5a623; font-size:15px; letter-spacing:1px; }
  .rating-count { font-size:12px; color:var(--txt3); }

  .detail-info .btn-primary { align-self:flex-start; }

  .section-title { font-size:15px; font-weight:700; margin:28px 0 14px; }

  .review-form { background:var(--bg3); border:1px solid var(--border); border-radius:12px; padding:20px; margin-bottom:24px; }
  .star-select { display:flex; gap:6px; margin-bottom:14px; }
  .star-select label { cursor:pointer; font-size:26px; color:var(--border); transition:color .15s; }
  .star-select input { display:none; }
  .star-select input:checked ~ label,
  .star-select label:hover,
  .star-select label:hover ~ label { color:#f5a623; }
  .review-textarea { width:100%; min-height:90px; padding:10px 12px; background:var(--input-bg); border:1px solid var(--border); border-radius:8px; color:var(--txt); font-family:inherit; font-size:13px; resize:vertical; outline:none; }
  .review-textarea:focus { border-color:var(--accent); }

  .review-locked { background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:16px 20px; font-size:13px; color:var(--txt3); margin-bottom:24px; }

  .review-item { border-bottom:1px solid var(--border); padding:16px 0; }
  .review-item:last-child { border-bottom:none; }
  .review-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; }
  .review-user { font-size:13px; font-weight:600; }
  .review-date { font-size:11px; color:var(--txt3); }
  .review-stars { color:#f5a623; font-size:13px; margin-bottom:6px; }
  .review-text { font-size:13px; color:var(--txt2); line-height:1.6; }

  .empty-reviews { text-align:center; padding:30px; color:var(--txt3); font-size:13px; }
</style>

<div class="detail-wrap">
  <a class="back-link" href="products.php">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M19 12H5M5 12l7 7M5 12l7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    Kembali ke Produk
  </a>

  <?php
    $images = [1 => 'CinemaBundel.jpg', 2 => 'DevDeveloper.jpg', 3 => 'ProPlayer.jpg', 4 => 'DeepSleep.jpg'];
    $img = $images[$product['id']] ?? 'CinemaBundel.jpg';
  ?>
  <div class="detail-card">
    <div class="detail-img-wrap">
      <img class="detail-img" src="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>">
    </div>
    <div class="detail-info">
      <div class="detail-name"><?php echo htmlspecialchars($product['nama_produk']); ?></div>
      <div class="detail-desc"><?php echo htmlspecialchars($product['deskripsi']); ?></div>

      <div class="detail-price-row">
        <span class="detail-price">Rp <?php echo number_format($product['harga']); ?></span>
        <span class="detail-stock">Stok: <?php echo (int) $product['stok']; ?></span>
      </div>

      <div class="rating-summary">
        <span class="rating-num"><?php echo $rata_rating; ?></span>
        <span class="rating-stars"><?php echo str_repeat('★', round($rata_rating)) . str_repeat('☆', 5 - round($rata_rating)); ?></span>
        <span class="rating-count">(<?php echo $jumlah_ulasan; ?> ulasan)</span>
      </div>

      <form action="add_cart.php" method="POST">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <button type="submit" class="btn btn-primary">+ Tambah ke Keranjang</button>
      </form>
    </div>
  </div>

  <div class="section-title">Tulis Ulasan</div>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success" style="margin-bottom:16px;">✅ Ulasan kamu berhasil dikirim, terima kasih!</div>
  <?php endif; ?>
  <?php if ($err_key && isset($review_errors[$err_key])): ?>
    <div class="alert alert-error" style="margin-bottom:16px;">⚠️ <?php echo htmlspecialchars($review_errors[$err_key]); ?></div>
  <?php endif; ?>

  <?php if ($boleh_ulas): ?>
    <form class="review-form" method="POST">
      <div class="fl" style="font-size:12px;font-weight:600;color:var(--txt2);margin-bottom:8px;">Rating kamu</div>
      <div class="star-select">
        <?php for ($i = 5; $i >= 1; $i--): ?>
          <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $i == 5 ? 'checked' : ''; ?>>
          <label for="star<?php echo $i; ?>">★</label>
        <?php endfor; ?>
      </div>
      <textarea class="review-textarea" name="review_text" placeholder="Bagaimana pengalaman kamu dengan produk ini?" required></textarea>
      <div style="margin-top:12px;">
        <button type="submit" name="submit_review" class="btn btn-primary">Kirim Ulasan</button>
      </div>
    </form>
  <?php elseif ($sudah_ulas): ?>
    <div class="review-locked">✓ Kamu sudah pernah menulis ulasan untuk produk ini.</div>
  <?php else: ?>
    <div class="review-locked">🔒 Kamu bisa menulis ulasan setelah membeli produk ini dan pembayarannya terverifikasi.</div>
  <?php endif; ?>

  <div class="section-title">Semua Ulasan (<?php echo $jumlah_ulasan; ?>)</div>

  <?php if (empty($reviews)): ?>
    <div class="empty-reviews">Belum ada ulasan untuk produk ini.</div>
  <?php else: ?>
    <div class="review-list">
      <?php foreach ($reviews as $r): ?>
        <div class="review-item">
          <div class="review-head">
            <span class="review-user"><?php echo htmlspecialchars($r['nama_user'] ?? 'Pengguna'); ?></span>
            <span class="review-date"><?php echo date('d M Y', strtotime($r['created_at'])); ?></span>
          </div>
          <div class="review-stars"><?php echo str_repeat('★', (int) $r['rating']) . str_repeat('☆', 5 - (int) $r['rating']); ?></div>
          <div class="review-text"><?php echo nl2br(htmlspecialchars($r['review_text'])); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include '_footer.php'; ?>