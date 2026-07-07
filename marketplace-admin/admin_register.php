<?php
/* ============================================================
   ADMIN REGISTER — RosiMarket Hub
   Dilindungi kode registrasi supaya tidak sembarang orang
   bisa membuat akun admin sendiri.

   ── PENTING ──
   Ganti nilai ADMIN_REGISTER_CODE di bawah ini dengan kode
   rahasia milik Anda sendiri sebelum dipakai di server asli.
   Setelah tim admin Anda selesai dibuat, sebaiknya HAPUS atau
   pindahkan file ini supaya tidak bisa diakses publik lagi.
   ============================================================ */
define('ADMIN_REGISTER_CODE', 'koderahasia');

include '../db.php';
session_start();

if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

$error = '';
$success = false;

if (isset($_POST['register'])) {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $pass     = $_POST['password'] ?? '';
    $passConf = $_POST['password_confirm'] ?? '';
    $kode     = $_POST['kode'] ?? '';

    if ($nama === '' || $email === '' || $pass === '') {
        $error = 'Semua field wajib diisi.';
    } elseif (!hash_equals(ADMIN_REGISTER_CODE, $kode)) {
        $error = 'Kode registrasi salah.';
    } elseif (strlen($pass) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($pass !== $passConf) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $stmtCek = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmtCek, "s", $email);
        mysqli_stmt_execute($stmtCek);
        $resultCek = mysqli_stmt_get_result($stmtCek);

        if (mysqli_fetch_assoc($resultCek)) {
            $error = 'Email sudah terdaftar.';
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $role   = 'admin';
            $stmtIns = mysqli_prepare($conn, "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmtIns, "ssss", $nama, $email, $hashed, $role);

            if (mysqli_stmt_execute($stmtIns)) {
                $success = true;
            } else {
                $error = 'Registrasi gagal, coba lagi.';
            }
            mysqli_stmt_close($stmtIns);
        }
        mysqli_stmt_close($stmtCek);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Admin · RosiMarket Hub</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
  --bg:#0b0d12; --panel:#12151c; --panel2:#171b24;
  --border:rgba(148,163,184,0.12); --borderac:rgba(148,163,184,0.28);
  --accent:#5b6cff; --accdim:rgba(91,108,255,0.14); --acchov:#4756e6;
  --txt:#e7e9ee; --txt2:#8a91a3; --txt3:#565d70;
  --red:#ef4444; --red-dim:rgba(239,68,68,0.12);
  --green:#22c55e; --green-dim:rgba(34,197,94,0.12);
  --r-sm:6px; --r-md:10px; --r-lg:14px;
}
body{
  font-family:'Inter',sans-serif; background:var(--bg); color:var(--txt);
  min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px;
}
body::before{
  content:''; position:fixed; inset:0; pointer-events:none;
  background:
    radial-gradient(ellipse 50% 40% at 85% 15%, rgba(91,108,255,0.07) 0%, transparent 70%),
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
.sub{ font-size:13px; color:var(--txt2); margin-bottom:24px; }

.fg{ margin-bottom:14px; }
.fl{ display:block; font-size:11px; font-weight:600; color:var(--txt3); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px; }
.fi{
  width:100%; padding:10px 12px; background:var(--panel2); border:1px solid var(--border);
  border-radius:var(--r-sm); color:var(--txt); font-family:inherit; font-size:13.5px; outline:none;
  transition:border-color 0.2s;
}
.fi:focus{ border-color:var(--accent); box-shadow:0 0 0 3px var(--accdim); }
.fi::placeholder{ color:var(--txt3); }

.pw-wrap{ position:relative; }
.pw-wrap .fi{ padding-right:70px; }
.pw-btn{
  position:absolute; right:7px; top:50%; transform:translateY(-50%);
  background:var(--panel); border:1px solid var(--border); border-radius:4px;
  color:var(--txt2); font-size:10.5px; font-weight:600; padding:4px 8px; cursor:pointer;
}

.kode-hint{ font-size:11px; color:var(--txt3); margin-top:5px; }

.alert-err{
  padding:10px 13px; border-radius:var(--r-sm); font-size:12.5px;
  background:var(--red-dim); border:1px solid rgba(239,68,68,0.25); color:var(--red); margin-bottom:14px;
}
.alert-ok{
  padding:10px 13px; border-radius:var(--r-sm); font-size:12.5px;
  background:var(--green-dim); border:1px solid rgba(34,197,94,0.25); color:var(--green); margin-bottom:14px;
}
.btn{
  width:100%; padding:11px; border:none; background:var(--accent); color:white;
  border-radius:var(--r-sm); font-size:13.5px; font-weight:600; cursor:pointer; margin-top:6px;
  font-family:inherit; transition:background 0.2s;
}
.btn:hover{ background:var(--acchov); }

.foot{ display:block; text-align:center; font-size:13px; color:var(--txt2); margin-top:18px; }
.foot a{ color:var(--accent); text-decoration:none; }
.foot a:hover{ text-decoration:underline; }
</style>
</head>
<body>
<div class="wrap">
  <div class="brand"><span class="dot"></span> ROSIMARKET ADMIN CONSOLE</div>

  <div class="card">
    <?php if ($success): ?>
      <h1>Akun admin dibuat ✅</h1>
      <p class="sub">Akun Anda sudah aktif dengan akses administrator.</p>
      <div class="alert-ok">Berhasil! Silakan masuk dengan email dan password yang baru saja Anda buat.</div>
      <a href="admin_login.php" class="btn" style="display:block;text-align:center;text-decoration:none;">Masuk ke Console</a>
    <?php else: ?>
      <h1>Daftar Akun Admin</h1>
      <p class="sub">Hanya untuk staf internal yang memiliki kode registrasi.</p>

      <?php if ($error): ?><div class="alert-err">⚠️ <?php echo htmlspecialchars($error); ?></div><?php endif; ?>

      <form method="POST">
        <div class="fg">
          <label class="fl">Nama lengkap</label>
          <input class="fi" type="text" name="nama" placeholder="Nama Anda" required
                 value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>">
        </div>
        <div class="fg">
          <label class="fl">Email</label>
          <input class="fi" type="email" name="email" placeholder="admin@rosimarket.com" required
                 value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div class="fg">
          <label class="fl">Password</label>
          <div class="pw-wrap">
            <input class="fi" type="password" name="password" id="pwInput" placeholder="Minimal 8 karakter" required minlength="8">
            <button type="button" class="pw-btn" onclick="togglePw('pwInput', this)">Lihat</button>
          </div>
        </div>
        <div class="fg">
          <label class="fl">Konfirmasi Password</label>
          <div class="pw-wrap">
            <input class="fi" type="password" name="password_confirm" id="pwConfirm" placeholder="Ulangi password" required minlength="8">
            <button type="button" class="pw-btn" onclick="togglePw('pwConfirm', this)">Lihat</button>
          </div>
        </div>
        <div class="fg">
          <label class="fl">Kode Registrasi</label>
          <input class="fi" type="password" name="kode" placeholder="Kode rahasia dari tim Anda" required>
          <p class="kode-hint">Diberikan oleh admin yang sudah ada atau pemilik sistem.</p>
        </div>
        <button type="submit" name="register" class="btn">Buat Akun Admin</button>
      </form>
    <?php endif; ?>
  </div>

  <span class="foot">Sudah punya akun? <a href="admin_login.php">Masuk di sini</a></span>
</div>
<script>
function togglePw(id, btn){
  var i=document.getElementById(id);
  if(i.type==='password'){ i.type='text'; btn.textContent='Sembunyikan'; }
  else{ i.type='password'; btn.textContent='Lihat'; }
}
</script>
</body>
</html>