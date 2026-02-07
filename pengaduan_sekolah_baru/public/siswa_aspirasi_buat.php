<?php
require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../config/auth.php";
require_siswa();

$nis = (int)$_SESSION['siswa_nis'];

$kat = mysqli_query($conn, "SELECT id_kategori, ket_kategori FROM kategori ORDER BY ket_kategori");

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_kategori = (int)($_POST['id_kategori'] ?? 0);
  $lokasi = trim($_POST['lokasi'] ?? '');
  $ket = trim($_POST['ket'] ?? '');

  $stmt = mysqli_prepare($conn, "INSERT INTO input_aspirasi(nis,id_kategori,lokasi,ket) VALUES (?,?,?,?)");
  mysqli_stmt_bind_param($stmt, "iiss", $nis, $id_kategori, $lokasi, $ket);
  if (mysqli_stmt_execute($stmt)) {
    $id_pelaporan = mysqli_insert_id($conn);

    // buat baris aspirasi default Menunggu (agar admin tinggal update)
    $stmt2 = mysqli_prepare($conn, "INSERT INTO aspirasi(id_pelaporan,id_kategori,status) VALUES (?,?, 'Menunggu')");
    mysqli_stmt_bind_param($stmt2, "ii", $id_pelaporan, $id_kategori);
    mysqli_stmt_execute($stmt2);

    $msg = "Aspirasi berhasil dikirim.";
  } else {
    $msg = "Gagal menyimpan.";
  }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Buat Aspirasi</title>
<style>
  body{font-family:Arial;background:#f6f7fb;margin:0}
  .card{max-width:700px;margin:18px auto;background:#fff;border-radius:14px;padding:18px}
  label{display:block;margin:10px 0 6px;font-size:13px}
  input,select{width:100%;padding:10px;border:1px solid #ddd;border-radius:10px}
  button,a{padding:10px 12px;border-radius:10px;border:0}
  button{background:#2d6cdf;color:#fff;cursor:pointer}
  .ok{background:#e9fff0;padding:10px;border-radius:10px}
</style></head>
<body>
  <div class="card">
    <h2 style="margin-top:0">Form Aspirasi Siswa</h2>
    <?php if($msg): ?><div class="ok"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <form method="post">
      <label>Kategori</label>
      <select name="id_kategori" required>
        <?php while($k=mysqli_fetch_assoc($kat)): ?>
          <option value="<?= (int)$k['id_kategori'] ?>"><?= htmlspecialchars($k['ket_kategori']) ?></option>
        <?php endwhile; ?>
      </select>

      <label>Lokasi</label>
      <input name="lokasi" maxlength="50" required placeholder="Contoh: Kelas XII RPL / Toilet belakang">

      <label>Keterangan</label>
      <input name="ket" maxlength="50" required placeholder="Contoh: Lampu mati / Keran bocor">

      <div style="margin-top:14px">
        <button type="submit">Kirim</button>
        <a href="siswa_dashboard.php" style="text-decoration:none;background:#eee;color:#222">Kembali</a>
      </div>
    </form>
  </div>
</body></html>
