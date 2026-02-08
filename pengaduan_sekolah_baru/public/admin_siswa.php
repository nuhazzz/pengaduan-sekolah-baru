<?php
require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../config/auth.php";
require_admin();

$msg = "";

/* =====================
   TAMBAH SISWA
===================== */
if (isset($_POST['tambah'])) {
  $nis   = trim($_POST['nis'] ?? '');
  $nama  = trim($_POST['nama'] ?? '');
  $kelas = trim($_POST['kelas'] ?? '');
  $pass  = $_POST['pass'] ?? '';

  if ($nis === '' || !ctype_digit($nis) || $nama === '' || $kelas === '' || $pass === '') {
    $msg = "Semua field wajib diisi.";
  } else {
    $cek = mysqli_prepare($conn, "SELECT 1 FROM siswa WHERE nis=? LIMIT 1");
    mysqli_stmt_bind_param($cek, "s", $nis);
    mysqli_stmt_execute($cek);

    if (mysqli_num_rows(mysqli_stmt_get_result($cek)) > 0) {
      $msg = "Gagal: NIS $nis sudah terdaftar.";
    } else {
      $hash = password_hash($pass, PASSWORD_BCRYPT);
      $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO siswa (nis,nama,kelas,password_hash)
         VALUES (?,?,?,?)"
      );
      mysqli_stmt_bind_param($stmt, "ssss", $nis, $nama, $kelas, $hash);

      $msg = mysqli_stmt_execute($stmt)
        ? "Siswa ditambahkan."
        : "Gagal tambah siswa.";
    }
  }
}

/* =====================
   HAPUS SISWA
===================== */
if (isset($_POST['hapus'])) {
  $nis = $_POST['nis'] ?? '';
  if ($nis !== '') {
    $stmt = mysqli_prepare($conn, "DELETE FROM siswa WHERE nis=?");
    mysqli_stmt_bind_param($stmt, "s", $nis);
    mysqli_stmt_execute($stmt);
    $msg = "Siswa dihapus.";
  }
}

/* =====================
   LIST SISWA (TERBARU DI ATAS)
===================== */
$list = mysqli_query(
  $conn,
  "SELECT nis, nama, kelas
   FROM siswa
   ORDER BY created_at DESC"
);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Data Siswa</title>
<style>
body{font-family:Arial;background:#f6f7fb;margin:0}
.card{max-width:900px;margin:18px auto;background:#fff;border-radius:14px;padding:18px}
input{padding:10px;border:1px solid #ddd;border-radius:10px}
button{padding:10px 12px;border-radius:10px;border:0;cursor:pointer}
.pri{background:#2d6cdf;color:#fff}
.danger{background:#d12b2b;color:#fff}
table{width:100%;border-collapse:collapse;margin-top:12px}
th,td{padding:10px;border-bottom:1px solid #eee;text-align:left;font-size:13px}
.msg{background:#f1fff5;padding:10px;border-radius:10px;margin:10px 0}
</style>
</head>
<body>

<div class="card">
  <h2>Data Siswa</h2>
  <button class="pri" onclick="location.href='admin_dashboard.php'">Kembali</button>

  <?php if ($msg): ?>
    <div class="msg"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <h3>Tambah Siswa</h3>
  <form method="post" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end">
    <div>
      <div style="font-size:12px">NIS</div>
      <input name="nis" required placeholder="Contoh: 00123456">
    </div>
    <div>
      <div style="font-size:12px">Nama</div>
      <input name="nama" required>
    </div>
    <div>
      <div style="font-size:12px">Kelas</div>
      <input name="kelas" required>
    </div>
    <div>
      <div style="font-size:12px">Password</div>
      <input name="pass" type="password" required>
    </div>
    <button class="pri" name="tambah" value="1">Tambah</button>
  </form>

  <h3 style="margin-top:18px">List Siswa</h3>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>NIS</th>
        <th>Nama</th>
        <th>Kelas</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php while ($s = mysqli_fetch_assoc($list)): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($s['nis']) ?></td>
        <td><?= htmlspecialchars($s['nama']) ?></td>
        <td><?= htmlspecialchars($s['kelas']) ?></td>
        <td>
          <form method="post" onsubmit="return confirm('Hapus siswa ini?')">
            <input type="hidden" name="nis" value="<?= htmlspecialchars($s['nis']) ?>">
            <button class="danger" name="hapus" value="1">Hapus</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

</body>
</html>
