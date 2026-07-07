<?php

ob_start(); // Tambahkan ini di baris pertama
session_start();
include 'db.php';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$allowed = ['user', 'admin'];

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($type && !in_array($type, $allowed, true)) {
    $type = '';
}

?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>
    (function () {
      var saved = localStorage.getItem('rosimarket-theme') || 'dark';
      document.documentElement.setAttribute('data-theme', saved);
    })();
  </script>
  <title>Register · RosiMarket Hub</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    :root,[data-theme="dark"]{
      --bg:#0d1117;--bg2:#0f172a;--bg3:#131c30;--bg4:#1a2744;
      --border:rgba(99,130,190,0.18);--borderac:rgba(99,130,190,0.38);
      --accent:#3b82f6;--accdim:rgba(59,130,246,0.14);--acchov:#2563eb;
      --txt:#e2e8f0;--txt2:#94a3b8;--txt3:#4f6282;--txtlink:#7ab4fa;
      --red:#ef4444;--red-dim:rgba(239,68,68,0.12);
      --green:#22c55e;--green-dim:rgba(34,197,94,0.12);
      --input-bg:#0f172a;--nav-bg:rgba(13,17,23,0.97);
      --shadowlg:0 8px 48px rgba(0,0,0,0.65);
      --r-sm:6px;--r-md:10px;--r-lg:16px;
    }
    [data-theme="light"]{
      --bg:#f6f8fa;--bg2:#ffffff;--bg3:#ffffff;--bg4:#eaeef2;
      --border:rgba(27,31,36,0.12);--borderac:rgba(27,31,36,0.3);
      --accent:#1d6fce;--accdim:rgba(29,111,206,0.1);--acchov:#154fa3;
      --txt:#1b1f24;--txt2:#57606a;--txt3:#8c959f;--txtlink:#0969da;
      --red:#cf222e;--red-dim:rgba(207,34,46,0.08);
      --green:#1a7f37;--green-dim:rgba(26,127,55,0.08);
      --input-bg:#ffffff;--nav-bg:rgba(246,248,250,0.98);
      --shadowlg:0 8px 32px rgba(0,0,0,0.12);
    }
    body{
      font-family:'Inter',sans-serif;background:var(--bg);color:var(--txt);
      min-height:100vh;display:flex;align-items:center;justify-content:center;
      padding:20px;transition:background 0.25s,color 0.25s;
    }
    body::before{
      content:'';position:fixed;inset:0;pointer-events:none;
      background:radial-gradient(ellipse 60% 50% at 20% 30%,rgba(59,130,246,0.06) 0%,transparent 70%),
                  radial-gradient(ellipse 50% 60% at 80% 70%,rgba(139,92,246,0.04) 0%,transparent 70%);
    }
    .wrap{width:100%;max-width:420px;animation:fadeUp 0.45s ease;position:relative;}
    .auth-topbar{
      display:flex;align-items:center;justify-content:space-between;
      margin-bottom:24px;
    }
    .auth-brand{display:flex;align-items:center;gap:8px;font-size:16px;font-weight:700;color:var(--txt);}
    .auth-brand svg{color:var(--accent);}
    .theme-pill{
      display:flex;align-items:center;gap:8px;
      background:var(--bg3);border:1px solid var(--border);
      border-radius:20px;padding:5px 10px;cursor:pointer;
      font-size:12px;color:var(--txt2);transition:all 0.2s;
    }
    .theme-pill:hover{border-color:var(--borderac);color:var(--txt);}
    .theme-pill .toggle-track{
      width:30px;height:16px;border-radius:8px;background:var(--bg4);
      border:1px solid var(--border);position:relative;flex-shrink:0;
    }
    .theme-pill .toggle-knob{
      width:10px;height:10px;border-radius:50%;background:var(--txt3);
      position:absolute;top:2px;left:2px;
      transition:transform 0.25s,background 0.25s;
    }
    [data-theme="light"] .theme-pill .toggle-knob{transform:translateX(14px);background:var(--accent);}
    .card{
      background:var(--bg3);border:1px solid var(--border);
      border-radius:var(--r-lg);padding:28px;box-shadow:var(--shadowlg);
      transition:background 0.25s,border-color 0.25s;
    }
    h1{font-size:16px;font-weight:600;text-align:center;margin-bottom:6px;}
    .sub{font-size:13px;color:var(--txt2);text-align:center;margin-bottom:22px;}
    .choice-row{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:14px;}
    .choice{
      display:block;text-align:center;padding:12px 10px;border-radius:10px;
      background:var(--input-bg);border:1px solid var(--border);color:var(--txt);
      text-decoration:none;font-size:13px;font-weight:600;transition:0.2s;
    }
    .choice:hover{border-color:var(--borderac);transform:translateY(-1px);}
    .choice.admin{background:rgba(91,108,255,0.08);}
    .choice.user{background:rgba(34,197,94,0.08);}
    .mini{font-size:12px;color:var(--txt3);text-align:center;margin-top:14px;}
    .link{color:var(--txtlink);text-decoration:none;}
    .link:hover{text-decoration:underline;}
    .panel{margin-top:16px;}
    .badge{
      display:inline-block;padding:5px 10px;border-radius:999px;
      font-size:12px;font-weight:700;margin-bottom:14px;
      background:var(--accdim);color:var(--accent);
    }
    .fg{margin-bottom:12px;}
    .fl{display:block;font-size:12px;font-weight:600;color:var(--txt2);margin-bottom:5px;}
    .fi{
      width:100%;padding:9px 12px;background:var(--input-bg);
      border:1px solid var(--border);border-radius:var(--r-sm);
      color:var(--txt);font-family:inherit;font-size:13px;outline:none;
      transition:border-color 0.2s,background 0.25s;
    }
    .fi:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accdim);}
    .pw-wrap{position:relative;}
    .pw-wrap .fi{padding-right:72px;}
    
    .pw-btn{
      position:absolute;right:8px;top:50%;transform:translateY(-50%);
      background:var(--bg4);border:1px solid var(--border);border-radius:4px;
      color:var(--txt2);font-size:11px;font-weight:600;padding:3px 8px;
      cursor:pointer;font-family:inherit;transition:all 0.15s;
    }
    .pw-btn:hover{color:var(--txt);border-color:var(--borderac);}

    .kode-hint{
    font-size: 10px;
    color: var(--txt3);
    margin-top: 2px;
    line-height: 1.25;
    opacity: 0.7;
  }
    .alert-err{
      padding:10px 14px;border-radius:var(--r-sm);font-size:12px;
      background:var(--red-dim);border:1px solid rgba(239,68,68,0.25);
      color:var(--red);margin-bottom:14px;
    }
    .btn{
      width:100%;padding:10px;border:none;background:var(--accent);color:white;
      border-radius:var(--r-sm);font-size:14px;font-weight:600;
      cursor:pointer;font-family:inherit;margin-top:4px;transition:background 0.2s;
    }
    .btn:hover{background:var(--acchov);}
    .divider{display:flex;align-items:center;gap:10px;margin:18px 0;font-size:12px;color:var(--txt3);}
    .divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border);}
    .foot{display:block;text-align:center;font-size:13px;color:var(--txt2);margin-top:16px;}
    .foot a{color:var(--txtlink);}
    .foot a:hover{text-decoration:underline;}
    .copy{text-align:center;font-size:12px;color:var(--txt3);margin-top:20px;}
    @keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
  </style>
</head>
<body>
<div class="wrap">
  <div class="auth-topbar">
    <div class="auth-brand">
      <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      </svg>
      RosiMarket Hub
    </div>
    <div class="theme-pill" onclick="toggleTheme()" title="Ganti tema">
      <span id="themeLabel">🌙 Gelap</span>
      <div class="toggle-track"><div class="toggle-knob"></div></div>
    </div>
  </div>

  <div class="card">
    <?php if ($type === ''): ?>
      <h1>Pilih jenis registrasi</h1>
      <p class="sub">Silakan pilih apakah Anda ingin mendaftar sebagai user atau admin.</p>
      <div class="choice-row">
        <a class="choice user" href="?type=user">Daftar User</a>
        <a class="choice admin" href="?type=admin">Daftar Admin</a>
      </div>
      <div class="mini">Sudah punya akun? <a class="link" href="login.php">Masuk sekarang</a></div>
    <?php elseif ($type === 'user'): ?>
      <span class="badge">Registrasi User</span>
      <?php include 'register_user_form.php'; ?>
    <?php elseif ($type === 'admin'): ?>
      <span class="badge">Registrasi Admin</span>
      <?php include 'register_admin_form.php'; ?>
    <?php endif; ?>
  </div>

  <p class="copy">&copy; <?php echo date('Y'); ?> RosiMarket Hub</p>
</div>

<script>
(function(){
  var s=localStorage.getItem('rosimarket-theme')||'dark';
  document.documentElement.setAttribute('data-theme',s);
  updateLabel(s);
})();
function toggleTheme(){
  var h=document.documentElement;
  var n=h.getAttribute('data-theme')==='dark'?'light':'dark';
  h.setAttribute('data-theme',n);
  localStorage.setItem('rosimarket-theme',n);
  updateLabel(n);
}
function updateLabel(t){
  var el=document.getElementById('themeLabel');
  if(el) el.textContent=t==='light'?'☀️ Terang':'🌙 Gelap';
}
function togglePw(btn,id){
  var inp=document.getElementById(id);
  if(inp.type==='password'){inp.type='text';btn.textContent='Sembunyikan';}
  else{inp.type='password';btn.textContent='Lihat';}
}
</script>
</body>
</html>
<?php 
ob_end_flush(); // Tambahkan ini di paling akhir file
?>