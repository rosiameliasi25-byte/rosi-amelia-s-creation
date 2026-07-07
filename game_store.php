<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
include 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'all';

if ($kategori == 'all') {
    $data = mysqli_query($conn, "SELECT * FROM products WHERE kategori != 'marketplace' ORDER BY id DESC");
} else {
    $kategori_safe = mysqli_real_escape_string($conn, $kategori);
    $data = mysqli_query($conn, "SELECT * FROM products WHERE kategori = '$kategori_safe' ORDER BY id DESC");
}

// Ulasan (semua kategori game digabung jadi satu, tampil di widget samping).
// Sumbernya sama seperti di produk_detail.php: tabel `digital_reviews`
// (join ke `products` untuk nama produk & kategori, join ke `users` untuk nama pengulas).
$ulasan_list = [];
$stmt = mysqli_prepare($conn, "
    SELECT r.rating, r.review_text, r.created_at, u.nama AS nama_user,
           p.nama_produk, p.kategori
    FROM digital_reviews r
    JOIN products p ON p.id = r.product_id
    LEFT JOIN users u ON u.id = r.user_id
    WHERE p.kategori != 'marketplace'
    ORDER BY r.created_at DESC
    LIMIT 20
");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $ulasan_list[] = $row;
}
mysqli_stmt_close($stmt);

$page_title  = 'Game Store · RosiMarket Hub';
$active_page = 'gamestore';
include '_navbar.php';
?>

<style>
  .page-hero { border-bottom: 1px solid var(--border); padding: 24px 0 0; }
  .page-inner { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
  .page-head { display: flex; align-items: flex-end; justify-content: space-between; padding-bottom: 0; gap: 16px; flex-wrap: wrap; }
  .page-title { font-size: 20px; font-weight: 700; letter-spacing: -0.3px; margin-bottom: 4px; }
  .page-sub { font-size: 13px; color: var(--txt2); }
  .page-tabs { display: flex; gap: 0; margin-top: 18px; border-bottom: 1px solid var(--border); }
  .tab-link { display: flex; align-items: center; gap: 6px; padding: 10px 16px; font-size: 13px; font-weight: 500; color: var(--txt2); border-bottom: 2px solid transparent; margin-bottom: -1px; transition: color 0.15s; text-decoration: none; white-space: nowrap; }
  .tab-link:hover { color: var(--txt); }
  .tab-link.active { color: var(--txt); border-bottom-color: var(--accent); }
  .tab-count { background: var(--bghover); color: var(--txt2); font-size: 11px; font-weight: 600; padding: 1px 6px; border-radius: 20px; }

  .content-wrap { max-width: 1200px; margin: 24px auto; padding: 0 24px; }
  .category-row { display: flex; gap: 10px; flex-wrap: wrap; margin: 22px 0 26px; }
  .category-pill { padding: 10px 16px; border: 1px solid var(--border); border-radius: 999px; text-decoration: none; color: var(--txt2); background: var(--bg3); font-size: 14px; }
  .category-pill.active { border-color: var(--accent); color: var(--accent); background: rgba(0,123,255,.08); }

  .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 22px; }
  .product-card { background: var(--bg3); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; transition: .25s ease; }
  .product-card:hover { transform: translateY(-6px); border-color: var(--accent); box-shadow: 0 10px 24px rgba(0,0,0,.14); }
  .product-img { width: 100%; height: 160px; object-fit: cover; object-position: center; display: block; }
  .product-card-header { padding: 16px 18px 12px; flex: 1; }
  .product-name { font-size: 15px; font-weight: 700; color: var(--txt); margin-bottom: 6px; }
  .product-desc { font-size: 12px; color: var(--txt2); line-height: 1.5; min-height: 3em; }
  .product-card-footer { padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border); gap: 12px; }
  .product-price { font-size: 15px; font-weight: 700; color: var(--accent); font-family: 'JetBrains Mono', monospace; }
  .product-stock { font-size: 10px; color: var(--txt3); }
  .btn-add-cart { padding: 8px 16px; background: var(--accent); color: white; border: none; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; }
  .empty-state { text-align: center; padding: 90px 20px; color: var(--txt2); }

  /* ===== Widget Ulasan (mengambang, tidak menggeser grid produk) ===== */
  .review-fab {
    position: fixed;
    top: 50%;
    right: 0;
    transform: translateY(-50%);
    background: var(--accent);
    color: #fff;
    border: none;
    padding: 16px 10px;
    border-radius: 12px 0 0 12px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    box-shadow: -4px 0 14px rgba(0,0,0,.18);
    z-index: 1000;
    writing-mode: vertical-rl;
    text-orientation: mixed;
    transition: filter .15s ease;
  }
  .review-fab:hover { filter: brightness(1.08); }
  .review-fab-icon { writing-mode: horizontal-tb; font-size: 16px; }

  .review-panel {
    position: fixed;
    top: 0;
    right: 0;
    width: 320px;
    max-width: 88vw;
    height: 100vh;
    background: var(--bg3);
    border-left: 1px solid var(--border);
    box-shadow: -10px 0 32px rgba(0,0,0,.22);
    transform: translateX(100%);
    transition: transform .3s ease;
    z-index: 1001;
    display: flex;
    flex-direction: column;
  }
  .review-panel.open { transform: translateX(0); }

  .review-panel-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 20px; border-bottom: 1px solid var(--border); flex-shrink: 0; }
  .review-panel-title { font-size: 15px; font-weight: 700; color: var(--txt); }
  .review-panel-close { background: none; border: none; font-size: 16px; color: var(--txt2); cursor: pointer; line-height: 1; padding: 4px; }
  .review-panel-close:hover { color: var(--txt); }

  .review-panel-body { flex: 1; overflow-y: auto; padding: 16px 20px; display: flex; flex-direction: column; gap: 12px; }
  .review-item { padding: 12px 14px; background: var(--bghover); border-radius: 10px; border: 1px solid var(--border); }
  .review-item-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; gap: 8px; }
  .review-name { font-size: 13px; font-weight: 600; color: var(--txt); }
  .review-stars { font-size: 12px; color: #f5a623; letter-spacing: 1px; white-space: nowrap; }
  .review-cat { font-size: 10px; color: var(--txt3); text-transform: uppercase; letter-spacing: .4px; }
  .review-meta { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 4px; }
  .review-date { font-size: 10px; color: var(--txt3); white-space: nowrap; }
  .review-text { font-size: 12.5px; color: var(--txt2); line-height: 1.5; }
  .review-empty { text-align: center; padding: 50px 10px; color: var(--txt2); font-size: 13px; }

  .review-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,.35); z-index: 999; opacity: 0; pointer-events: none; transition: opacity .3s ease; }
  .review-backdrop.open { opacity: 1; pointer-events: auto; }

  @media (max-width: 640px) {
    .review-fab-text { display: none; }
    .review-panel { width: 100vw; max-width: 100vw; }
  }
</style>

<div class="page-hero">
  <div class="page-inner">
    <div class="page-head">
      <div>
        <div class="page-title">Game Store</div>
        <div class="page-sub">Top up saldo game & voucher hiburan digital secara instan</div>
      </div>
      <a href="cart.php" class="btn btn-secondary">Keranjang</a>
    </div>

    <div class="page-tabs">
      <a href="game_store.php" class="tab-link active">🎮 Game Store</a>
      <a href="products.php" class="tab-link">🛍 Marketplace</a>
      <a href="history.php" class="tab-link">📋 Riwayat</a>
    </div>
  </div>
</div>

<div class="content-wrap">
  <div class="category-row">
    <a class="category-pill <?php echo $kategori=='all'?'active':''; ?>" href="game_store.php?kategori=all">Semua</a>
    <a class="category-pill <?php echo $kategori=='ml_legends'?'active':''; ?>" href="game_store.php?kategori=ml_legends">ML Legends</a>
    <a class="category-pill <?php echo $kategori=='free_fire'?'active':''; ?>" href="game_store.php?kategori=free_fire">Free Fire</a>
    <a class="category-pill <?php echo $kategori=='genshin'?'active':''; ?>" href="game_store.php?kategori=genshin">Genshin</a>
    <a class="category-pill <?php echo $kategori=='steam'?'active':''; ?>" href="game_store.php?kategori=steam">Steam</a>
    <a class="category-pill <?php echo $kategori=='valorant'?'active':''; ?>" href="game_store.php?kategori=valorant">Valorant</a>
    <a class="category-pill <?php echo $kategori=='robux'?'active':''; ?>" href="game_store.php?kategori=robux">Robux</a>
  </div>

  <?php if (mysqli_num_rows($data) == 0): ?>
    <div class="empty-state">
      <p>Belum ada produk game tersedia.</p>
    </div>
  <?php else: ?>
    <div class="product-grid">
      <?php while ($row = mysqli_fetch_assoc($data)):
        $img = !empty($row['gambar']) ? 'assets/' . $row['gambar'] : 'assets/default-game.jpg';
      ?>
      <div class="product-card">
        <img class="product-img" src="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
        <div class="product-card-header">
          <div class="product-name"><?php echo htmlspecialchars($row['nama_produk']); ?></div>
          <div class="product-desc"><?php echo htmlspecialchars($row['deskripsi']); ?></div>
        </div>
        <div class="product-card-footer">
          <div>
            <div class="product-price">Rp <?php echo number_format($row['harga']); ?></div>
            <div class="product-stock">Stok: <?php echo $row['stok']; ?></div>
          </div>
          <form action="add_cart.php" method="POST">
            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
            <button type="submit" class="btn-add-cart">+ Keranjang</button>
          </form>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</div>

<!-- ===== Widget Ulasan: mengambang di sisi kanan, tidak memengaruhi layout grid produk ===== -->
<div class="review-backdrop" id="ulasanBackdrop" onclick="toggleUlasan()"></div>

<button type="button" class="review-fab" onclick="toggleUlasan()">
  <span class="review-fab-icon">⭐</span>
  <span class="review-fab-text">Ulasan</span>
</button>

<div class="review-panel" id="ulasanPanel">
  <div class="review-panel-head">
    <div class="review-panel-title">⭐ Ulasan Pembeli</div>
    <button type="button" class="review-panel-close" onclick="toggleUlasan()">✕</button>
  </div>
  <div class="review-panel-body">
    <?php if (count($ulasan_list) > 0): ?>
      <?php foreach ($ulasan_list as $u):
        $rating = max(0, min(5, (int)($u['rating'] ?? 0)));
      ?>
        <div class="review-item">
          <div class="review-item-top">
            <span class="review-name"><?php echo htmlspecialchars($u['nama_user'] ?? 'Pengguna'); ?></span>
            <span class="review-stars"><?php echo str_repeat('★', $rating) . str_repeat('☆', 5 - $rating); ?></span>
          </div>
          <div class="review-meta">
            <?php if (!empty($u['nama_produk'])): ?>
              <span class="review-cat"><?php echo htmlspecialchars($u['nama_produk']); ?></span>
            <?php endif; ?>
            <?php if (!empty($u['created_at'])): ?>
              <span class="review-date"><?php echo date('d M Y', strtotime($u['created_at'])); ?></span>
            <?php endif; ?>
          </div>
          <div class="review-text"><?php echo nl2br(htmlspecialchars($u['review_text'] ?? '')); ?></div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="review-empty">Belum ada ulasan.</div>
    <?php endif; ?>
  </div>
</div>

<script>
function toggleUlasan() {
  document.getElementById('ulasanPanel').classList.toggle('open');
  document.getElementById('ulasanBackdrop').classList.toggle('open');
}
</script>

<?php include '_footer.php'; ?>