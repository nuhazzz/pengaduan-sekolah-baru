<?php
require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../config/auth.php";
require_admin();

$msg = "";

if (isset($_POST['tambah'])) {
  $nis  = (int)($_POST['nis'] ?? 0);
  $nama = trim($_POST['nama'] ?? '');
  $kelas = trim($_POST['kelas'] ?? '');
  $pass = $_POST['pass'] ?? '';

  // validasi sederhana
  if ($nis <= 0 || $nama === '' || $kelas === '' || $pass === '') {
    $msg = "Semua field wajib diisi.";
  } else {
    // cek NIS sudah ada atau belum
    $cek = mysqli_prepare($conn, "SELECT 1 FROM siswa WHERE nis=? LIMIT 1");
    mysqli_stmt_bind_param($cek, "i", $nis);
    mysqli_stmt_execute($cek);
    $ada = mysqli_stmt_get_result($cek);

    if (mysqli_num_rows($ada) > 0) {
      $msg = "Gagal: NIS $nis sudah terdaftar.";
    } else {
      $hash = password_hash($pass, PASSWORD_BCRYPT);

      $stmt = mysqli_prepare($conn, "INSERT INTO siswa(nis,nama,kelas,password_hash) VALUES (?,?,?,?)");
      mysqli_stmt_bind_param($stmt, "isss", $nis, $nama, $kelas, $hash);

      if (mysqli_stmt_execute($stmt)) {
        $msg = "Siswa ditambahkan.";
      } else {
        $msg = "Gagal tambah: " . mysqli_stmt_error($stmt);
      }
    }
  }
}

if (isset($_POST['hapus'])) {
  $nis = (int)($_POST['nis'] ?? 0);

  // INI YANG PENTING: cukup hapus siswa, data aspirasi ikut terhapus karena FK CASCADE
  $stmt = mysqli_prepare($conn, "DELETE FROM siswa WHERE nis=?");
  mysqli_stmt_bind_param($stmt, "i", $nis);
  $msg = mysqli_stmt_execute($stmt) ? "Siswa dihapus (data terkait ikut terhapus)." : "Gagal hapus.";
}

$list = mysqli_query($conn, "SELECT nis, nama, kelas FROM siswa ORDER BY nis DESC");
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Data Siswa</title>
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
</style></head>
<body>
  <div class="card">
    <h2 style="margin-top:0">Data Siswa</h2>
        <button class="pri" type="button" onclick="window.location.href='admin_dashboard.php'">Kembali</button>

    <?php if($msg): ?><div class="msg"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <h3>Tambah Siswa</h3>
    <form method="post" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end">
      <div>
        <div style="font-size:12px">NIS</div>
        <input name="nis" required placeholder="Contoh : 123456789">
      </div>
      <div>
        <div style="font-size:12px">Nama</div>
        <input name="nama" required placeholder="Contoh : Budi Santoso">
      </div>
      <div>
        <div style="font-size:12px">Kelas</div>
        <input name="kelas" required placeholder="Contoh : XII RPL 1">
      </div>
      <div>
        <div style="font-size:12px">Password</div>
        <input name="pass" required placeholder="Wajib isi" type="password">
      </div>
      <button class="pri" name="tambah" value="1">Tambah</button>
    </form>

    <h3 style="margin-top:18px">List Siswa</h3>
    <table>
        <thead><tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php while($s=mysqli_fetch_assoc($list)): ?>
        <tr>
          <td><?= (int)$s['nis'] ?></td>
          <td><?= htmlspecialchars($s['nama']) ?></td>
          <td><?= htmlspecialchars($s['kelas']) ?></td>
          <td>
            <form method="post" onsubmit="return confirm('Hapus siswa ini? Semua aspirasi terkait ikut terhapus!')">
              <input type="hidden" name="nis" value="<?= (int)$s['nis'] ?>">
              <button class="danger" name="hapus" value="1">Hapus</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body></html>
