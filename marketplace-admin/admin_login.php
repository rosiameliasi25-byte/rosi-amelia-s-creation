<?php

/* ============================================================

   ADMIN LOGIN — RosiMarket Hub

   Terpisah dari login.php milik user biasa.

   Hanya akun dengan kolom `role` = 'admin' yang bisa masuk ke sini.

   Ditambah: kolom Kode Rahasia (harus "koderahasia") sebagai lapisan

   verifikasi tambahan sebelum proses cek email/password dijalankan.

   ============================================================ */

include '../db.php';

session_start();

// Kode rahasia yang wajib diisi benar agar proses login diproses
define('ADMIN_SECRET_CODE', 'koderahasia');

// Kalau sudah login sebagai admin, langsung lempar ke dashboard
if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

$error = '';
if (isset($_POST['login'])) {
    $email       = trim($_POST['email'] ?? '');
    $password    = $_POST['password'] ?? '';
    $secret_code = $_POST['secret_code'] ?? '';

    // ---- Gerbang kode rahasia (dicek lebih dulu) ----
    if ($secret_code !== ADMIN_SECRET_CODE) {
        $error = 'Kode rahasia salah.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($data = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $data['password'])) {
                // ---- Gerbang khusus admin ----
                if (($data['role'] ?? '') !== 'admin') {
                    $error = 'Akun ini terdaftar, tapi tidak memiliki akses admin.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $data['id'];
                    $_SESSION['nama']    = $data['nama'];
                    $_SESSION['email']   = $data['email'];
                    $_SESSION['role']    = $data['role'];
                    header("Location: admin_dashboard.php");
                    exit;
                }
            } else {
                $error = 'Password salah.';
            }
        } else {
            $error = 'Email tidak terdaftar di sistem.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Console · RosiMarket Hub</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
  --bg:#0b0d12; --panel:#12151c; --panel2:#171b24;
  --border:rgba(148,163,184,0.12); --borderac:rgba(148,163,184,0.28);
  --accent:#5b6cff; --accdim:rgba(91,108,255,0.14); --acchov:#4756e6;
  --txt:#e7e9ee; --txt2:#8a91a3; --txt3:#565d70;
  --red:#ef4444; --red-dim:rgba(239,68,68,0.12);
  --r-sm:6px; --r-md:10px; --r-lg:14px;
}
body{
  font-family:'Inter',sans-serif; background:var(--bg); color:var(--txt);
  min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px;
}
body::before{
  content:''; position:fixed; inset:0; pointer-events:none;
  background:
    radial-gradient(ellipse 50% 40% at 15% 20%, rgba(91,108,255,0.07) 0%, transparent 70%),
    repeating-linear-gradient(0deg, rgba(255,255,255,0.015) 0px, transparent 1px, transparent 2px);
}
.wrap{ width:100%; max-width:380px; position:relative; }
.brand{
  display:flex; align-items:center; gap:9px; margin-bottom:28px; justify-content:center;
  font-size:13px; font-weight:600; letter-spacing:0.06em; text-transform:uppercase; color:var(--txt2);
}
.brand .dot{ width:6px; height:6px; border-radius:50%; background:var(--accent); box-shadow:0 0 8px var(--accent); }
.card{
  background:var(--panel); border:1px solid var(--border); border-radius:var(--r-lg);
  padding:32px 30px; box-shadow:0 20px 60px rgba(0,0,0,0.5);
}
h1{ font-size:18px; font-weight:700; margin-bottom:4px; }
.sub{ font-size:13px; color:var(--txt2); margin-bottom:26px; }
.fg{ margin-bottom:14px; }
.fl{ display:block; font-size:11px; font-weight:600; color:var(--txt3); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px; }
.fi{
  width:100%; padding:10px 12px; background:var(--panel2); border:1px solid var(--border);
  border-radius:var(--r-sm); color:var(--txt); font-family:inherit; font-size:13.5px; outline:none;
  transition:border-color 0.2s;
}
.fi:focus{ border-color:var(--accent); box-shadow:0 0 0 3px var(--accdim); }
.fi::placeholder{ color:var(--txt3); }
.fi.mono{ font-family:'JetBrains Mono',monospace; letter-spacing:0.04em; }
.pw-wrap{ position:relative; }
.pw-wrap .fi{ padding-right:70px; }
.pw-btn{
  position:absolute; right:7px; top:50%; transform:translateY(-50%);
  background:var(--panel); border:1px solid var(--border); border-radius:4px;
  color:var(--txt2); font-size:10.5px; font-weight:600; padding:4px 8px; cursor:pointer;
}
.alert-err{
  padding:10px 13px; border-radius:var(--r-sm); font-size:12.5px;
  background:var(--red-dim); border:1px solid rgba(239,68,68,0.25); color:var(--red); margin-bottom:14px;
}
.btn{
  width:100%; padding:11px; border:none; background:var(--accent); color:white;
  border-radius:var(--r-sm); font-size:13.5px; font-weight:600; cursor:pointer; margin-top:6px;
  font-family:inherit; transition:background 0.2s;
}
.btn:hover{ background:var(--acchov); }
.foot-note{
  text-align:center; font-size:11.5px; color:var(--txt3); margin-top:22px;
  display:flex; align-items:center; justify-content:center; gap:6px;
}
.foot-note svg{ flex-shrink:0; }
</style>
</head>
<body>
<div class="wrap">
  <div class="brand"><span class="dot"></span> ROSIMARKET ADMIN CONSOLE</div>
  <div class="card">
    <h1>Masuk sebagai Admin</h1>
    <p class="sub">Akses terbatas — khusus akun dengan peran administrator.</p>
    <?php if ($error): ?><div class="alert-err">⚠️ <?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <form method="POST">
      <div class="fg">
        <label class="fl">Email</label>
        <input class="fi" type="email" name="email" placeholder="admin@rosimarket.com" required
               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
      </div>
      <div class="fg">
        <label class="fl">Password</label>
        <div class="pw-wrap">
          <input class="fi" type="password" name="password" id="pwInput" placeholder="••••••••" required>
          <button type="button" class="pw-btn" onclick="togglePw()">Lihat</button>
        </div>
      </div>
      <div class="fg">
        <label class="fl">Kode Rahasia</label>
        <input class="fi mono" type="password" name="secret_code" placeholder="Masukkan kode rahasia" required>
      </div>
      <button type="submit" name="login" class="btn">Masuk ke Console</button>
    </form>
  </div>
  <span class="foot-note" style="margin-bottom:8px;">
    Belum punya akun admin? <a href="admin_register.php" style="color:var(--accent);text-decoration:none;font-weight:600;">Daftar di sini</a>
  </span>
  <p class="foot-note">
    <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M8 11V7a4 4 0 018 0v4" stroke="currentColor" stroke-width="1.5"/></svg>
    Halaman ini hanya untuk staf internal yang berwenang
  </p>
</div>
<script>
function togglePw(){
  var i=document.getElementById('pwInput');
  var b=event.target;
  if(i.type==='password'){ i.type='text'; b.textContent='Sembunyikan'; }
  else{ i.type='password'; b.textContent='Lihat'; }

}

</script>

</body>

</html>