<?php
require_once __DIR__ . "/../config/koneksi.php";
session_start();

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $role = $_POST['role'] ?? '';
  $user = trim($_POST['user'] ?? '');
  $pass = $_POST['pass'] ?? '';

  if ($role === 'admin') {
    $stmt = mysqli_prepare($conn, "SELECT id_admin, username, password_hash FROM admin WHERE username=?");
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);

    if ($row && password_verify($pass, $row['password_hash'])) {
      $_SESSION['admin_id'] = (int)$row['id_admin'];
      $_SESSION['admin_user'] = $row['username'];
      header("Location: admin_dashboard.php"); exit;
    } else $error = "Login admin gagal.";
  }

  if ($role === 'siswa') {
    $nis = (int)$user;
    $stmt = mysqli_prepare($conn, "SELECT nis, kelas, password_hash FROM siswa WHERE nis=?");
    mysqli_stmt_bind_param($stmt, "i", $nis);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);

    if ($row && password_verify($pass, $row['password_hash'])) {
      $_SESSION['siswa_nis'] = (int)$row['nis'];
      $_SESSION['siswa_kelas'] = $row['kelas'];
      header("Location: siswa_dashboard.php"); exit;
    } else $error = "Login siswa gagal.";
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Pengaduan Sarana Sekolah - Login</title>
  <style>
    body{font-family:Arial;background:#f6f7fb;margin:0}
    .wrap{max-width:420px;margin:60px auto;background:#fff;padding:22px;border-radius:14px;box-shadow:0 10px 30px rgba(0,0,0,.08)}
    h1{margin:0 0 14px;font-size:20px}
    label{display:block;margin:10px 0 6px;font-size:13px;color:#333}
    input,select{width:100%;padding:10px;border:1px solid #ddd;border-radius:10px}
    button{width:100%;padding:10px;border:0;border-radius:10px;background:#2d6cdf;color:#fff;margin-top:14px;cursor:pointer}
    .err{background:#ffe8e8;color:#9b1c1c;padding:10px;border-radius:10px;margin:10px 0}
    .hint{font-size:12px;color:#666;margin-top:10px}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Login Aplikasi Pengaduan Sarana Sekolah</h1>
    <?php if($error): ?><div class="err"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="post">
      <label>Masuk sebagai</label>  
      <select name="role" required>
        <option value="siswa">Siswa</option>
        <option value="admin">Admin</option>
      </select>

      <label>Username / NIS</label>
      <input name="user" required placeholder="admin atau NIS">

      <label>Password</label>
      <input name="pass" type="password" required placeholder="••••••••">

      <button type="submit">Masuk</button>
    </form>
  </div>
</body>
</html>
