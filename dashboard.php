<?php
session_start(); // INI WAJIB ADA DI PALING ATAS
// Jika tidak ada user_id atau dia adalah admin, tendang keluar!
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
    header("Location: login.php");
    exit;
}
include 'auth.php';
include 'db.php';
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
$page_title  = 'Dashboard · RosiMarket Hub';
$active_page = 'dashboard';
include '_navbar.php';
?>
<style>
/* ── Light-mode overrides for page-specific colours ── */
[data-theme="light"] .dash-hero {
  background: linear-gradient(135deg,rgba(29,111,206,0.06) 0%,rgba(124,58,237,0.03) 100%);
}
[data-theme="light"] .stat-card,
[data-theme="light"] .nav-card,
[data-theme="light"] .about-card,
[data-theme="light"] .info-box,
[data-theme="light"] .sidebar-widget { background: #fff; }
[data-theme="light"] .about-card-header { background: linear-gradient(90deg,rgba(29,111,206,0.04),transparent); }
[data-theme="light"] .quick-link:hover { background: #f0f3f6; }
[data-theme="light"] .tag { background:rgba(29,111,206,0.08); color:#1d6fce; border-color:rgba(29,111,206,0.2); }

.dash-hero {
  background: linear-gradient(135deg,rgba(59,130,246,0.07) 0%,rgba(139,92,246,0.04) 100%);
  border-bottom: 1px solid var(--border);
  padding: 32px 0 28px;
  transition: background 0.25s;
}
.dash-inner { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
.dash-greeting { font-size: 22px; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px; }
.dash-sub { font-size: 14px; color: var(--txt2); }

.dash-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; margin-top: 20px; }
.stat-card {
  background: var(--bg3); border: 1px solid var(--border);
  border-radius: var(--r-md); padding: 16px 18px;
  transition: background 0.25s, border-color 0.25s;
}
.stat-label { font-size: 11px; color: var(--txt3); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.stat-num   { font-size: 24px; font-weight: 700; font-family: 'JetBrains Mono',monospace; }
.stat-delta { font-size: 12px; color: var(--green); margin-top: 2px; }

.dash-layout {
  max-width: 1200px; margin: 28px auto; padding: 0 24px;
  display: grid; grid-template-columns: 1fr 280px; gap: 20px; align-items: start;
}
.section-label {
  font-size: 11px; font-weight: 600; color: var(--txt3);
  text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 12px;
}
.nav-cards { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 24px; }
.nav-card {
  display: block; background: var(--bg3); border: 1px solid var(--border);
  border-radius: var(--r-lg); padding: 22px; text-decoration: none;
  transition: all 0.2s;
}
.nav-card:hover { border-color: var(--accent); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(59,130,246,0.12); }
.nav-card-icon  { font-size: 28px; margin-bottom: 10px; }
.nav-card-title { font-size: 14px; font-weight: 600; color: var(--txt); margin-bottom: 5px; }
.nav-card-desc  { font-size: 12px; color: var(--txt2); line-height: 1.55; }

.about-card { background: var(--bg3); border: 1px solid var(--border); border-radius: var(--r-lg); overflow: hidden; margin-bottom: 20px; transition: background 0.25s; }
.about-card-header {
  padding: 18px 22px; border-bottom: 1px solid var(--border);
  display: flex; align-items: center; gap: 10px;
  background: linear-gradient(90deg,rgba(59,130,246,0.06),transparent);
  transition: background 0.25s;
}
.about-card-body { padding: 20px 22px; }
.about-card-body p { font-size: 13px; color: var(--txt2); line-height: 1.7; }

.info-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; margin-top: 16px; }
.info-box {
  background: var(--bg2); border: 1px solid var(--border);
  border-radius: var(--r-md); padding: 16px;
  transition: background 0.25s;
}
.info-box-top { height: 3px; border-radius: 2px; margin-bottom: 10px; }
.info-box h3 { font-size: 13px; font-weight: 600; color: var(--txt); margin-bottom: 6px; }
.info-box p  { font-size: 12px; color: var(--txt2); line-height: 1.6; }

.sidebar-widget {
  background: var(--bg3); border: 1px solid var(--border);
  border-radius: var(--r-lg); padding: 18px; margin-bottom: 16px;
  transition: background 0.25s;
}
.widget-title {
  font-size: 12px; font-weight: 600; color: var(--txt2);
  text-transform: uppercase; letter-spacing: 0.5px;
  margin-bottom: 14px; display: flex; align-items: center; gap: 6px;
}
.quick-link {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 10px; border-radius: var(--r-sm);
  color: var(--txt2); font-size: 13px; transition: all 0.15s;
  cursor: pointer; text-decoration: none; margin: 0 -10px;
}
.quick-link:hover { background: var(--bghover); color: var(--txt); }
.quick-link-icon { width:28px;height:28px;border-radius:var(--r-sm);display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0; }

.profile-mini { display:flex;align-items:center;gap:12px;margin-bottom:14px; }
.profile-ava  { width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--purple));display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:16px;border:2px solid var(--border); }
.profile-name { font-size:14px;font-weight:600;color:var(--txt); }
.profile-role { font-size:12px;color:var(--txt3); }
.tag { display:inline-block;padding:3px 8px;border-radius:20px;font-size:11px;font-weight:500;margin:3px 2px 0;background:var(--accdim);color:var(--txtlink);border:1px solid color-mix(in srgb,var(--accent) 25%,transparent); }

@media(max-width:900px){.dash-layout{grid-template-columns:1fr;}.nav-cards{grid-template-columns:1fr 1fr;}.info-grid{grid-template-columns:1fr;}.dash-stats{grid-template-columns:1fr 1fr;}}
@media(max-width:600px){.nav-cards{grid-template-columns:1fr;}.dash-stats{grid-template-columns:1fr;}}
</style>

<div class="dash-hero">
  <div class="dash-inner">
    <div class="dash-greeting">Selamat datang, <?php echo htmlspecialchars($_SESSION['nama']); ?></div>
    <div class="dash-sub">Ini adalah panel kendali utama RosiMarket Hub Anda.</div>
    <div class="dash-stats">
      <div class="stat-card">
        <div class="stat-label">Platform</div>
        <div class="stat-num">3</div>
        <div class="stat-delta">Modul aktif</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Status</div>
        <div class="stat-num" style="color:var(--green);font-size:16px;margin-top:4px;">● Online</div>
        <div class="stat-delta">Sistem berjalan normal</div>
      </div>
     <div class="stat-card">
        <div class="stat-label">Sesi Masuk</div>
        <?php date_default_timezone_set('Asia/Jakarta'); ?>
        <div class="stat-num" style="font-size:14px;margin-top:4px;"><?php echo date('d M Y'); ?></div>
        <div class="stat-delta"><?php echo date('H:i'); ?> WIB</div>
      </div>
    </div>
  </div>
</div>

<div class="dash-layout">
  <div>
    <div class="section-label">Menu Utama</div>
    <div class="nav-cards">
      <a href="index.php" class="nav-card">
        <div class="nav-card-icon">🎨</div>
        <div class="nav-card-title">Daily Creative Check-In</div>
        <div class="nav-card-desc">Cek apakah kamu sudah melakukan aktivitas kreatif hari ini, pilih kategori hobi, dan selesaikan tantangan singkat.</div>
      </a>
      <a href="products.php" class="nav-card">
        <div class="nav-card-icon">🛍️</div>
        <div class="nav-card-title">Marketplace</div>
        <div class="nav-card-desc">Jelajahi dan beli produk digital, aset desain, serta lisensi.</div>
      </a>
      <a href="game_store.php" class="nav-card">
        <div class="nav-card-icon">🎮</div>
        <div class="nav-card-title">Game Store</div>
        <div class="nav-card-desc">Top up saldo game, voucher, dan kode hiburan digital instan.</div>
      </a>
    </div>

    <div class="about-card">
      <div class="about-card-header">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/><path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        <span style="font-size:14px;font-weight:600;">Tentang RosiMarket Hub</span>
      </div>
      <div class="about-card-body">
        <p>RosiMarket Hub adalah platform terintegrasi yang menggabungkan manajemen e-commerce produk digital, ekosistem voucher permainan, serta ruang produktivitas aktivitas kreatif dalam satu panel kendali.</p>
        <div class="info-grid">
          <div class="info-box"><div class="info-box-top" style="background:var(--accent);"></div><h3>Daily Creative Check-In</h3><p>Cek harian sederhana untuk memastikan kamu tetap konsisten berkarya, lengkap dengan pilihan kategori hobi dan tantangan singkat.</p></div>
          <div class="info-box"><div class="info-box-top" style="background:var(--green);"></div><h3>Marketplace Umum</h3><p>Akses berbagai produk digital berkualitas dengan standar harga terbaik.</p></div>
          <div class="info-box"><div class="info-box-top" style="background:var(--purple);"></div><h3>Game Store</h3><p>Pengisian saldo virtual game secara instan dan aman untuk berbagai platform.</p></div>
        </div>
      </div>
    </div>
  </div>

  <aside>
    <div class="sidebar-widget">
      <div class="widget-title">
        <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        Profil Saya
      </div>
      <div class="profile-mini">
        <div class="profile-ava"><?php echo strtoupper(substr($_SESSION['nama'],0,1)); ?></div>
        <div>
          <div class="profile-name"><?php echo htmlspecialchars($_SESSION['nama']); ?></div>
          <div class="profile-role">Pengguna Aktif</div>
        </div>
      </div>
      <div><span class="tag">Marketplace</span><span class="tag">Games</span><span class="tag">Creative</span></div>
    </div>

    <div class="sidebar-widget">
      <div class="widget-title">
        <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Akses Cepat
      </div>
      <a class="quick-link" href="cart.php"><div class="quick-link-icon" style="background:var(--accdim);">🛒</div>Keranjang Belanja</a>
      <a class="quick-link" href="history.php"><div class="quick-link-icon" style="background:var(--purple-dim,rgba(139,92,246,0.12));">📋</div>Riwayat Transaksi</a>
      <a class="quick-link" href="index.php"><div class="quick-link-icon" style="background:var(--green-dim,rgba(34,197,94,0.12));">🎯</div>Creative Check-In</a>
      <a class="quick-link" href="logout.php"><div class="quick-link-icon" style="background:var(--red-dim);">⏻</div><span style="color:var(--red);">Keluar Sistem</span></a>
    </div>
  </aside>
</div>

<?php include '_footer.php'; ?>