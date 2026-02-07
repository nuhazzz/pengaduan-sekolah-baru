<?php
require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../config/auth.php";
require_admin();

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $status = $_POST['status'] ?? 'Menunggu';
  $feedback = trim($_POST['feedback'] ?? '');

  $stmt = mysqli_prepare($conn, "UPDATE aspirasi SET status=?, feedback=? WHERE id_pelaporan=?");
mysqli_stmt_bind_param($stmt, "ssi", $status, $feedback, $id);
mysqli_stmt_execute($stmt);

header("Location: admin_aspirasi_detail.php?id=".$id."&updated=1");
exit;
}

$stmt = mysqli_prepare($conn, "
  SELECT ia.id_pelaporan, ia.created_at, ia.nis, s.kelas, k.ket_kategori, ia.lokasi, ia.ket,
         a.status, a.feedback
  FROM input_aspirasi ia
  JOIN siswa s ON s.nis=ia.nis
  JOIN kategori k ON k.id_kategori=ia.id_kategori
  LEFT JOIN aspirasi a ON a.id_pelaporan=ia.id_pelaporan
  WHERE ia.id_pelaporan=?
");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if(!$r){ die("Data tidak ditemukan"); }
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Detail Aspirasi</title>
<style>
  body{font-family:Arial;background:#f6f7fb;margin:0}
  .card{max-width:800px;margin:18px auto;background:#fff;border-radius:14px;padding:18px}
  label{display:block;margin:10px 0 6px;font-size:13px}
  select,textarea{width:100%;padding:10px;border:1px solid #ddd;border-radius:10px}
  button,a{padding:10px 12px;border-radius:10px;border:0}
  button{background:#2d6cdf;color:#fff;cursor:pointer}
</style></head>
<body>
  <div class="card">
    <h2 style="margin-top:0">Detail & Umpan Balik</h2>

    <?php if (isset($_GET['updated']) && $_GET['updated'] == '1'): ?>
      <div style="margin:12px 0;padding:10px 12px;border-radius:12px;background:#e8fff0;border:1px solid #b7f0c4;color:#0f7b0f;">
        ✅ Feedback berhasil diupdate.
      </div>
    <?php endif; ?>

    <div style="margin-top:12px;font-size:14px;line-height:1.6">
      <b>Tanggal:</b> <?= htmlspecialchars($r['created_at']) ?><br>
      <b>NIS/Kelas:</b> <?= (int)$r['nis'] ?> / <?= htmlspecialchars($r['kelas']) ?><br>
      <b>Kategori:</b> <?= htmlspecialchars($r['ket_kategori']) ?><br>
      <b>Lokasi:</b> <?= htmlspecialchars($r['lokasi']) ?><br>
      <b>Keterangan:</b> <?= htmlspecialchars($r['ket']) ?><br>
    </div>

    <form method="post" style="margin-top:10px">
      <label>Status</label>
      <select name="status">
        <?php foreach(['Menunggu','Proses','Selesai'] as $st): ?>
          <option value="<?= $st ?>" <?= ($r['status']===$st?'selected':'') ?>><?= $st ?></option>
        <?php endforeach; ?>
      </select>

      <label>Feedback Admin</label>
      <textarea name="feedback" rows="5" placeholder="Tulis umpan balik..."><?= htmlspecialchars($r['feedback'] ?? '') ?></textarea>

      <div style="margin-top:12px">
        <button type="submit">Simpan</button>
        <button type="button" onclick="window.location.href='admin_dashboard.php'">Kembali</button>
      </div>
    </form>
  </div>
</body></html>
