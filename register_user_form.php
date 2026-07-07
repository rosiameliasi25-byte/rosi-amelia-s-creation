<?php
include 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if (isset($_POST['register_user'])) {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $role  = 'user';

    if ($nama === '' || $email === '' || $pass === '') {
        $error = 'Semua field wajib diisi.';
    } else {
        $stmtCek = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmtCek, "s", $email);
        mysqli_stmt_execute($stmtCek);
        $resultCek = mysqli_stmt_get_result($stmtCek);

        if (mysqli_fetch_assoc($resultCek)) {
            $error = 'Email sudah terdaftar.';
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $stmtIns = mysqli_prepare($conn, "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmtIns, "ssss", $nama, $email, $hashed, $role);

            if (mysqli_stmt_execute($stmtIns)) {
                header("Location: login.php?pesan=berhasil");
                exit;
            } else {
                $error = 'Registrasi gagal.';
            }
            mysqli_stmt_close($stmtIns);
        }
        mysqli_stmt_close($stmtCek);
    }
}
?>
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
    <label class="fl">Email address</label>
    <input class="fi" type="email" name="email" placeholder="nama@email.com" required
           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
  </div>

  <div class="fg">
    <label class="fl">Password</label>
    <div class="pw-wrap">
      <input class="fi" type="password" name="password" id="pwUser" placeholder="••••••••" required>
      <button type="button" class="pw-btn" onclick="togglePw(this,'pwUser')">Lihat</button>
    </div>
  </div>

  <button type="submit" name="register_user" class="btn">Sign up</button>
</form>

<div class="divider">atau</div>
<span class="foot">Sudah punya akun? <a href="login.php">Masuk sekarang</a></span>