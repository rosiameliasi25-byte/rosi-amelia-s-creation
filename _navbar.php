<?php
// _navbar.php — Shared header/navbar + Dark/Light mode system
$active       = $active_page ?? '';
$user_nama    = $_SESSION['nama'] ?? 'User';
$user_initial = strtoupper(substr($user_nama, 0, 1));
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>
    // Terapkan tema tersimpan SEBELUM CSS/HTML lain dirender, agar tidak ada flash tema salah (flicker)
    (function () {
      var saved = localStorage.getItem('rosimarket-theme') || 'dark';
      document.documentElement.setAttribute('data-theme', saved);
    })();
  </script>
  <title><?php echo $page_title ?? 'RosiMarket Hub'; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <style>
    /* ============================================================
       DESIGN TOKEN SYSTEM — DARK (default) + LIGHT
    ============================================================ */
    :root,
    [data-theme="dark"] {
      --bg:           #0d1117;
      --bg2:          #0f172a;
      --bg3:          #131c30;
      --bg4:          #1a2744;
      --bghover:      #1e2d4a;
      --border:       rgba(99,130,190,0.18);
      --borderac:     rgba(99,130,190,0.38);
      --shadow:       0 4px 24px rgba(0,0,0,0.45);
      --shadowlg:     0 8px 48px rgba(0,0,0,0.65);
      --shadowcard:   0 2px 12px rgba(0,0,0,0.4);
      --txt:          #e2e8f0;
      --txt2:         #94a3b8;
      --txt3:         #4f6282;
      --txtlink:      #7ab4fa;
      --nav-bg:       rgba(13,17,23,0.97);
      --input-bg:     #0f172a;
      --scrollbar:    #1a2744;
      /* Accent colours stay same in both themes */
      --accent:       #3b82f6;
      --accdim:       rgba(59,130,246,0.14);
      --acchov:       #2563eb;
      --green:        #22c55e;
      --green-dim:    rgba(34,197,94,0.14);
      --purple:       #8b5cf6;
      --purple-dim:   rgba(139,92,246,0.14);
      --amber:        #f59e0b;
      --amber-dim:    rgba(245,158,11,0.14);
      --red:          #ef4444;
      --red-dim:      rgba(239,68,68,0.12);
      --r-sm:         6px;
      --r-md:         10px;
      --r-lg:         16px;
      /* theme-toggle icon */
      --toggle-icon: '☀️';
    }

    [data-theme="light"] {
      --bg:           #f6f8fa;
      --bg2:          #ffffff;
      --bg3:          #ffffff;
      --bg4:          #eaeef2;
      --bghover:      #f0f3f6;
      --border:       rgba(27,31,36,0.12);
      --borderac:     rgba(27,31,36,0.3);
      --shadow:       0 4px 16px rgba(0,0,0,0.08);
      --shadowlg:     0 8px 32px rgba(0,0,0,0.12);
      --shadowcard:   0 1px 6px rgba(0,0,0,0.08);
      --txt:          #1b1f24;
      --txt2:         #57606a;
      --txt3:         #8c959f;
      --txtlink:      #0969da;
      --nav-bg:       rgba(246,248,250,0.98);
      --input-bg:     #ffffff;
      --scrollbar:    #d0d7de;
      /* accent overrides for light — slightly deeper for contrast */
      --accent:       #1d6fce;
      --accdim:       rgba(29,111,206,0.1);
      --acchov:       #154fa3;
      --green:        #1a7f37;
      --green-dim:    rgba(26,127,55,0.1);
      --purple:       #7c3aed;
      --purple-dim:   rgba(124,58,237,0.1);
      --amber:        #d97706;
      --amber-dim:    rgba(217,119,6,0.1);
      --red:          #cf222e;
      --red-dim:      rgba(207,34,46,0.08);
      --toggle-icon: '🌙';
    }

    /* ============================================================
       GLOBAL RESET
    ============================================================ */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--txt);
      min-height: 100vh;
      font-size: 14px;
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
      transition: background 0.25s ease, color 0.25s ease;
    }

    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: var(--bg); }
    ::-webkit-scrollbar-thumb { background: var(--scrollbar); border-radius: 3px; }

    a { color: inherit; text-decoration: none; }

    /* ============================================================
       GITHUB-STYLE NAVBAR
    ============================================================ */
    .gh-nav {
      height: 56px;
      background: var(--nav-bg);
      border-bottom: 1px solid var(--border);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 200;
      display: flex;
      align-items: center;
      padding: 0 20px;
      gap: 12px;
      transition: background 0.25s ease, border-color 0.25s ease;
    }

    .gh-brand {
      display: flex; align-items: center; gap: 8px;
      font-size: 15px; font-weight: 700;
      color: var(--txt); flex-shrink: 0; letter-spacing: -0.3px;
    }
    .gh-brand svg { color: var(--accent); flex-shrink: 0; }

    .gh-search-wrap { position: relative; flex: 1; max-width: 280px; }
    .gh-search-wrap svg {
      position: absolute; left: 10px; top: 50%;
      transform: translateY(-50%); color: var(--txt3); pointer-events: none;
    }
    .gh-search-wrap input {
      width: 100%; padding: 6px 12px 6px 32px;
      background: var(--input-bg); border: 1px solid var(--border);
      border-radius: var(--r-sm); color: var(--txt);
      font-family: inherit; font-size: 13px; outline: none;
      transition: all 0.2s;
    }
    .gh-search-wrap input:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px var(--accdim);
      background: var(--bg3);
    }
    .gh-search-wrap input::placeholder { color: var(--txt3); }

    .gh-links { display: flex; gap: 2px; flex: 1; }
    .gh-links a {
      padding: 6px 10px; border-radius: var(--r-sm);
      color: var(--txt2); font-size: 13px; font-weight: 500;
      transition: all 0.15s; white-space: nowrap;
    }
    .gh-links a:hover { background: var(--bghover); color: var(--txt); }
    .gh-links a.active { color: var(--txt); background: var(--bghover); }

    .gh-right { display: flex; align-items: center; gap: 8px; margin-left: auto; }

    /* Icon button */
    .gh-icon-btn {
      width: 32px; height: 32px;
      background: transparent; border: 1px solid var(--border);
      border-radius: var(--r-sm); color: var(--txt2);
      cursor: pointer; display: flex; align-items: center; justify-content: center;
      transition: all 0.15s; text-decoration: none;
    }
    .gh-icon-btn:hover { background: var(--bghover); color: var(--txt); border-color: var(--borderac); }

    /* ── THEME TOGGLE BUTTON ── */
    .theme-toggle {
      width: 36px; height: 20px;
      border-radius: 10px;
      background: var(--bg4);
      border: 1px solid var(--border);
      position: relative; cursor: pointer;
      transition: background 0.25s, border-color 0.25s;
      flex-shrink: 0;
      display: flex; align-items: center;
      padding: 2px;
    }
    .theme-toggle:hover { border-color: var(--borderac); }

    .theme-toggle-knob {
      width: 14px; height: 14px; border-radius: 50%;
      background: var(--txt3);
      transition: transform 0.25s cubic-bezier(.4,0,.2,1), background 0.25s;
      pointer-events: none;
    }
    [data-theme="light"] .theme-toggle-knob {
      transform: translateX(16px);
      background: var(--accent);
    }

    /* Sun / moon icons flanking toggle */
    .theme-icons {
      display: flex; align-items: center; gap: 6px;
    }
    .theme-icons span { font-size: 12px; line-height: 1; }

    /* Avatar / dropdown */
    .gh-avatar {
      width: 32px; height: 32px; border-radius: 50%;
      background: linear-gradient(135deg, var(--accent), var(--purple));
      border: 2px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      font-size: 12px; font-weight: 700; color: white;
      cursor: pointer; transition: border-color 0.15s;
      position: relative;
    }
    .gh-avatar:hover { border-color: var(--borderac); }

    .gh-dropdown {
      position: absolute; top: calc(100% + 8px); right: 0;
      width: 200px;
      background: var(--bg3); border: 1px solid var(--border);
      border-radius: var(--r-md); box-shadow: var(--shadowlg);
      padding: 6px; display: none; z-index: 300;
      transition: background 0.25s;
    }
    .gh-dropdown.open { display: block; animation: fadeDown 0.15s ease; }

    .gh-dd-header {
      padding: 8px 10px 10px;
      border-bottom: 1px solid var(--border); margin-bottom: 6px;
    }
    .gh-dd-header .name  { font-weight: 600; font-size: 13px; color: var(--txt); }
    .gh-dd-header .email { color: var(--txt3); font-size: 11px; margin-top: 1px; }

    .gh-dropdown a, .gh-dropdown button {
      display: block; width: 100%; padding: 7px 10px;
      color: var(--txt2); font-size: 13px;
      border-radius: var(--r-sm);
      background: transparent; border: none;
      text-align: left; cursor: pointer;
      transition: all 0.1s; font-family: inherit; text-decoration: none;
    }
    .gh-dropdown a:hover, .gh-dropdown button:hover { background: var(--bghover); color: var(--txt); }
    .gh-dropdown .dd-divider { height: 1px; background: var(--border); margin: 6px 0; }
    .gh-dropdown .dd-danger  { color: var(--red) !important; }
    .gh-dropdown .dd-danger:hover { background: var(--red-dim) !important; color: var(--red) !important; }

    /* ============================================================
       PAGE WRAPPER
    ============================================================ */
    .page-wrap { padding-top: 56px; min-height: 100vh; }

    /* ============================================================
       SHARED COMPONENTS
    ============================================================ */

    /* Badges */
    .badge {
      display: inline-flex; align-items: center; gap: 4px;
      padding: 3px 9px; border-radius: 20px;
      font-size: 11px; font-weight: 600;
    }
    .badge-blue   { background: var(--accdim);    color: var(--txtlink);  border: 1px solid color-mix(in srgb, var(--accent) 30%, transparent); }
    .badge-green  { background: var(--green-dim);  color: var(--green);    border: 1px solid color-mix(in srgb, var(--green) 30%, transparent); }
    .badge-purple { background: var(--purple-dim); color: var(--purple);   border: 1px solid color-mix(in srgb, var(--purple) 30%, transparent); }
    .badge-amber  { background: var(--amber-dim);  color: var(--amber);    border: 1px solid color-mix(in srgb, var(--amber) 30%, transparent); }
    .badge-red    { background: var(--red-dim);    color: var(--red);      border: 1px solid color-mix(in srgb, var(--red) 30%, transparent); }
    .badge-gray   { background: var(--bghover);    color: var(--txt2);     border: 1px solid var(--border); }

    /* Cards */
    .card {
      background: var(--bg3); border: 1px solid var(--border);
      border-radius: var(--r-lg); padding: 24px;
      transition: border-color 0.2s, background 0.25s;
    }
    .card:hover { border-color: var(--borderac); }

    /* Buttons */
    .btn {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 8px 16px; border-radius: var(--r-sm);
      font-size: 13px; font-weight: 600;
      cursor: pointer; border: 1px solid transparent;
      transition: all 0.15s; font-family: inherit; text-decoration: none;
    }
    .btn-primary   { background: var(--accent); color: white; }
    .btn-primary:hover { background: var(--acchov); }
    .btn-secondary { background: transparent; color: var(--txt2); border-color: var(--border); }
    .btn-secondary:hover { background: var(--bghover); color: var(--txt); border-color: var(--borderac); }
    .btn-danger    { background: var(--red-dim); color: var(--red); border-color: color-mix(in srgb, var(--red) 30%, transparent); }
    .btn-danger:hover { background: color-mix(in srgb, var(--red) 18%, transparent); }
    .btn-green     { background: var(--green-dim); color: var(--green); border-color: color-mix(in srgb, var(--green) 30%, transparent); }
    .btn-green:hover { background: color-mix(in srgb, var(--green) 22%, transparent); }
    .btn-sm   { padding: 5px 12px; font-size: 12px; }
    .btn-block { width: 100%; justify-content: center; padding: 11px; }

    /* Form */
    .form-group { margin-bottom: 14px; }
    .form-label {
      display: block; font-size: 12px; font-weight: 600;
      color: var(--txt2); margin-bottom: 5px; letter-spacing: 0.2px;
    }
    .form-input {
      width: 100%; padding: 9px 12px;
      background: var(--input-bg); border: 1px solid var(--border);
      border-radius: var(--r-sm); color: var(--txt);
      font-family: inherit; font-size: 13px; outline: none;
      transition: border-color 0.2s, background 0.25s;
    }
    .form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accdim); }
    .form-input::placeholder { color: var(--txt3); }
    .form-input[readonly] { opacity: 0.6; cursor: not-allowed; }

    /* Alerts */
    .alert { padding: 12px 16px; border-radius: var(--r-sm); font-size: 13px; margin-bottom: 16px; display: flex; align-items: flex-start; gap: 8px; }
    .alert-error   { background: var(--red-dim);    border: 1px solid color-mix(in srgb,var(--red) 25%,transparent);    color: var(--red); }
    .alert-success { background: var(--green-dim);  border: 1px solid color-mix(in srgb,var(--green) 25%,transparent);  color: var(--green); }
    .alert-info    { background: var(--accdim);     border: 1px solid color-mix(in srgb,var(--accent) 25%,transparent); color: var(--txtlink); }

    /* Table */
    .gh-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .gh-table th { padding: 10px 14px; text-align: left; color: var(--txt3); font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border); }
    .gh-table td { padding: 11px 14px; border-bottom: 1px solid color-mix(in srgb,var(--border) 60%,transparent); color: var(--txt2); }
    .gh-table tr:last-child td { border-bottom: none; }
    .gh-table tr:hover td { background: var(--bghover); }

    /* Footer */
    .gh-footer { border-top: 1px solid var(--border); padding: 20px; margin-top: 48px; transition: border-color 0.25s; }
    .gh-footer-inner { max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .gh-footer-brand { font-size: 12px; color: var(--txt3); display: flex; align-items: center; gap: 6px; }
    .gh-footer-links { display: flex; gap: 16px; flex-wrap: wrap; }
    .gh-footer-links a { font-size: 12px; color: var(--txt3); transition: color 0.15s; }
    .gh-footer-links a:hover { color: var(--txtlink); }
    .gh-footer-status { font-size: 11px; color: var(--green); display: flex; align-items: center; gap: 5px; }
    .gh-footer-status::before { content:''; width:6px; height:6px; border-radius:50%; background: var(--green); }

    /* Toast */
    .toast {
      position: fixed; bottom: 24px; right: 24px;
      background: var(--bg3); border: 1px solid var(--border);
      border-radius: var(--r-md); box-shadow: var(--shadowlg);
      padding: 12px 18px; font-size: 13px; color: var(--txt);
      z-index: 9999; animation: toastIn 0.3s ease;
      display: flex; align-items: center; gap: 8px;
      max-width: 320px;
    }

    /* Confetti */
    .confetti-bit { position:fixed; width:8px; height:8px; top:-10px; border-radius:2px; animation: confettiFall 2.2s linear forwards; z-index:9999; }

    /* Animations */
    @keyframes fadeDown   { from{opacity:0;transform:translateY(-8px);}  to{opacity:1;transform:translateY(0);} }
    @keyframes fadeUp     { from{opacity:0;transform:translateY(16px);}  to{opacity:1;transform:translateY(0);} }
    @keyframes toastIn    { from{opacity:0;transform:translateY(8px);}   to{opacity:1;transform:translateY(0);} }
    @keyframes confettiFall { to{transform:translateY(110vh) rotate(720deg);opacity:0;} }

    /* Responsive */
    @media (max-width: 768px) {
      .gh-links { display: none; }
      .gh-search-wrap { max-width: 160px; }
      .hide-mobile { display: none !important; }
    }
  </style>
</head>
<body>

<nav class="gh-nav">
  <!-- Brand -->
  <a href="dashboard.php" class="gh-brand">
    <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
      <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    RosiMarket
  </a>

  <!-- Search -->
  <div class="gh-search-wrap">
    <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
      <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
      <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>
    <input type="text" placeholder="Cari produk, game..." id="globalSearch"
           onkeydown="if(event.key==='Enter') window.location='products.php'">
  </div>

  <!-- Nav links -->
  <div class="gh-links">
    <a href="dashboard.php"  class="<?php echo $active==='dashboard'?'active':''; ?>">Dashboard</a>
    <a href="products.php"   class="<?php echo $active==='products' ?'active':''; ?>">Marketplace</a>
    <a href="game_store.php" class="<?php echo $active==='games'    ?'active':''; ?>">Game Store</a>
    <a href="index.php"      class="<?php echo $active==='creative' ?'active':''; ?>">Creative</a>
    <a href="history.php"    class="<?php echo $active==='history'  ?'active':''; ?>">History</a>
  </div>

  <!-- Right actions -->
  <div class="gh-right">

    <!-- Dark / Light mode toggle -->
    <div class="theme-icons" title="Ganti tema">
      <span>🌙</span>
      <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()" aria-label="Toggle theme">
        <div class="theme-toggle-knob" id="themeKnob"></div>
      </button>
      <span>☀️</span>
    </div>

    <!-- Cart -->
    <a href="cart.php" class="gh-icon-btn" title="Keranjang">
      <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <line x1="3" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="1.8"/>
        <path d="M16 10a4 4 0 0 1-8 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      </svg>
    </a>

    <!-- Avatar + dropdown -->
    <div class="gh-avatar" id="ghAvatarBtn" onclick="toggleGhDropdown()">
      <?php echo $user_initial; ?>
      <div class="gh-dropdown" id="ghDropdown">
        <div class="gh-dd-header">
          <div class="name"><?php echo htmlspecialchars($user_nama); ?></div>
          <div class="email">@rosimarket · Pengguna</div>
        </div>
        <a href="dashboard.php">Dashboard</a>
        <a href="history.php">Riwayat Transaksi</a>
        <a href="cart.php">Keranjang Saya</a>
        <div class="dd-divider"></div>
        <a href="logout.php" class="dd-danger">Sign out</a>
      </div>
    </div>
  </div>
</nav>

<div class="page-wrap">

<script>
/* ===== THEME SYSTEM ===== */
(function () {
  // Apply saved theme immediately (before page renders) to avoid flash
  var saved = localStorage.getItem('rosimarket-theme') || 'dark';
  document.documentElement.setAttribute('data-theme', saved);
})();

function toggleTheme() {
  var html    = document.documentElement;
  var current = html.getAttribute('data-theme') || 'dark';
  var next    = current === 'dark' ? 'light' : 'dark';
  html.setAttribute('data-theme', next);
  localStorage.setItem('rosimarket-theme', next);
  showToast(next === 'light' ? '☀️ Mode terang aktif' : '🌙 Mode gelap aktif');
}

/* ===== DROPDOWN ===== */
function toggleGhDropdown() {
  document.getElementById('ghDropdown').classList.toggle('open');
}
document.addEventListener('click', function (e) {
  if (!e.target.closest('#ghAvatarBtn')) {
    var dd = document.getElementById('ghDropdown');
    if (dd) dd.classList.remove('open');
  }
});

/* ===== TOAST ===== */
function showToast(msg) {
  var t = document.createElement('div');
  t.className = 'toast'; t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(function () {
    t.style.opacity = '0'; t.style.transition = 'opacity 0.3s';
    setTimeout(function () { t.remove(); }, 300);
  }, 2600);
}

/* ===== CONFETTI ===== */
function celebrate() {
  var colors = ['#3b82f6','#8b5cf6','#22c55e','#f59e0b','#ec4899'];
  for (var i = 0; i < 30; i++) {
    var c = document.createElement('div');
    c.className = 'confetti-bit';
    c.style.left = Math.random() * 100 + 'vw';
    c.style.animationDelay = Math.random() * 0.5 + 's';
    c.style.background = colors[Math.floor(Math.random() * colors.length)];
    c.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
    document.body.appendChild(c);
    setTimeout(function () { c.remove(); }, 2400);
  }
}
</script>