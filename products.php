<?php
// products.php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
include 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// Ganti bagian $data = mysqli_query(...) di products.php dengan ini:
$data = mysqli_query($conn, "SELECT * FROM products WHERE kategori = 'marketplace' ORDER BY id DESC");

$page_title  = 'Marketplace · RosiMarket Hub';
$active_page = 'products';
include '_navbar.php';
?>

<style>
  /* Styling Header & Tabs tetap dipertahankan dari file asli */
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

  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .content-wrap { max-width: 1200px; margin: 24px auto; padding: 0 24px; }
  .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; animation: fadeInUp 0.6s ease-out; }

  .product-card {
    background: var(--bg3); border: 1px solid var(--border); border-radius: 16px;
    display: flex; flex-direction: column; transition: all 0.3s ease; overflow: hidden;
  }
  .product-card:hover { transform: translateY(-8px); border-color: var(--accent); box-shadow: 0 12px 30px rgba(0,0,0,0.2); }

.product-img {
  width: 100%;
  height: 160px;
  object-fit: cover;
  object-position: center;
  display: block;
  border-bottom: 1px solid var(--border);
}

  .product-link { text-decoration: none; color: inherit; display: block; }

  .product-card-header { padding: 18px 20px 14px; flex: 1; }
  .product-name { font-size: 15px; font-weight: 600; color: var(--txt); margin-bottom: 6px; transition: color 0.15s; }
  .product-link:hover .product-name { color: var(--accent); }
  .product-desc { font-size: 12px; color: var(--txt2); line-height: 1.5; height: 3.6em; overflow: hidden; }

  .product-card-footer { padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border); }
  .product-price { font-size: 15px; font-weight: 700; color: var(--accent); font-family: 'JetBrains Mono', monospace; }
  .product-stock { font-size: 10px; color: var(--txt3); }
  .btn-add-cart { padding: 8px 16px; background: var(--accent); color: white; border: none; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: 0.2s; }
  .btn-add-cart:hover { filter: brightness(1.1); }
</style>

<div class="page-hero">
  <div class="page-inner">
    <div class="page-head">
      <div>
        <div class="page-title">Marketplace Umum</div>
        <div class="page-sub">Produk digital, aset desain, dan lisensi untuk proyek Anda</div>
      </div>
      <a href="cart.php" class="btn btn-secondary">Keranjang</a>
    </div>

    <div class="page-tabs">
      <a href="products.php" class="tab-link active">
        Semua Produk
        <?php $count = mysqli_num_rows($data); mysqli_data_seek($data, 0); ?>
        <span class="tab-count"><?php echo $count; ?></span>
      </a>
      <a href="game_store.php" class="tab-link">🎮 Game Store</a>
      <a href="history.php" class="tab-link">📋 Riwayat</a>
    </div>
  </div>
</div>

<div class="content-wrap">
  <?php if ($count == 0): ?>
    <div class="empty-state"><p>Belum ada produk tersedia saat ini.</p></div>
  <?php else: ?>
  <div class="product-grid">
 <?php while ($row = mysqli_fetch_assoc($data)):
  $images = [
    1 => 'CinemaBundel.jpg',
    2 => 'DevDeveloper.jpg',
    3 => 'ProPlayer.jpg',
    4 => 'DeepSleep.jpg',
  ];

  $img = $images[$row['id']] ?? 'CinemaBundel.jpg';
?>
<div class="product-card">
  <a class="product-link" href="produk_detail.php?id=<?php echo $row['id']; ?>">
    <img class="product-img" src="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">

    <div class="product-card-header">
      <div class="product-name"><?php echo htmlspecialchars($row['nama_produk']); ?></div>
      <div class="product-desc"><?php echo htmlspecialchars($row['deskripsi']); ?></div>
    </div>
  </a>

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

<?php include '_footer.php'; ?>