<?php
require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../config/auth.php";
require_siswa();

$nis = (int)$_SESSION['siswa_nis'];

$stmt = mysqli_prepare($conn, "
  SELECT
    COUNT(*) total,
    SUM(CASE WHEN COALESCE(a.status,'Menunggu')='Menunggu' THEN 1 ELSE 0 END) menunggu,
    SUM(CASE WHEN COALESCE(a.status,'Menunggu')='Proses' THEN 1 ELSE 0 END) proses,
    SUM(CASE WHEN COALESCE(a.status,'Menunggu')='Selesai' THEN 1 ELSE 0 END) selesai
  FROM input_aspirasi ia
  LEFT JOIN aspirasi a ON a.id_pelaporan = ia.id_pelaporan
  WHERE ia.nis=?
");
mysqli_stmt_bind_param($stmt, "i", $nis);
mysqli_stmt_execute($stmt);
$stat = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Siswa</title>
<style>
  body{font-family:Arial;background:#f6f7fb;margin:0}
  .bar{background:#fff;padding:14px 18px;display:flex;justify-content:space-between;align-items:center}
  .card{max-width:900px;margin:18px auto;background:#fff;border-radius:14px;padding:18px}
  .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
  .box{background:#f3f5ff;border-radius:12px;padding:12px}
  a.btn{display:inline-block;padding:10px 12px;border-radius:10px;background:#2d6cdf;color:#fff;text-decoration:none;margin-right:8px}
</style></head>
<body>
  <div class="bar">
    <div>Login: NIS <b><?= $nis ?></b> (<?= htmlspecialchars($_SESSION['siswa_kelas']) ?>)</div>
    <div><a href="logout.php">Logout</a></div>
  </div>

  <div class="card">
    <h2 style="margin-top:0">Ringkasan Aspirasi</h2>
    <div class="grid">
      <div class="box"><b>Total</b><div><?= (int)$stat['total'] ?></div></div>
      <div class="box"><b>Menunggu</b><div><?= (int)$stat['menunggu'] ?></div></div>
      <div class="box"><b>Proses</b><div><?= (int)$stat['proses'] ?></div></div>
      <div class="box"><b>Selesai</b><div><?= (int)$stat['selesai'] ?></div></div>
    </div>
    <div style="margin-top:14px">
      <a class="btn" href="siswa_aspirasi_buat.php">Buat Aspirasi</a>
      <a class="btn" href="siswa_histori.php">Histori & Umpan Balik</a>
    </div>
  </div>
</body></html>
