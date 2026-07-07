<?php
define('ADMIN_REGISTER_CODE', 'koderahasia');

include 'db.php';

if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

$error = '';
$success = false;

if (isset($_POST['register_admin'])) {
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
<?php if ($success): ?>
  <h1>Akun admin dibuat ✅</h1>
  <p class="sub">Akun Anda sudah aktif dengan akses administrator.</p>
  <div class="alert-ok">Berhasil! Silakan masuk dengan email dan password yang baru saja Anda buat.</div>
  <a href="admin_login.php" class="btn" style="display:block;text-align:center;text-decoration:none;">Masuk ke Console</a>
<?php else: ?>
  <p class="sub">Hanya untuk staf internal yang memiliki kode registrasi.</p>

  <?php if ($error): ?>
    <div class="alert-err">⚠️ <?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

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
        <input class="fi" type="password" name="password" id="pwAdmin" placeholder="Minimal 8 karakter" required minlength="8">
        <button type="button" class="pw-btn" onclick="togglePw(this,'pwAdmin')">Lihat</button>
      </div>
    </div>

    <div class="fg">
      <label class="fl">Konfirmasi Password</label>
      <div class="pw-wrap">
        <input class="fi" type="password" name="password_confirm" id="pwConfirm" placeholder="Ulangi password" required minlength="8">
        <button type="button" class="pw-btn" onclick="togglePw(this,'pwConfirm')">Lihat</button>
      </div>
    </div>

    <div class="fg">
      <label class="fl">Kode Registrasi</label>
      <input class="fi" type="password" name="kode" placeholder="Kode rahasia dari tim Anda" required>
      <p class="kode-hint">Diberikan oleh admin yang sudah ada atau pemilik sistem.</p>
    </div>

    <button type="submit" name="register_admin" class="btn">Buat Akun Admin</button>
  </form>

  <div class="divider">atau</div>
  <span class="foot">Sudah punya akun? 
    <a href="marketplace-admin/admin_login.php">Masuk di sini</a>
  </span>
<?php endif; ?>