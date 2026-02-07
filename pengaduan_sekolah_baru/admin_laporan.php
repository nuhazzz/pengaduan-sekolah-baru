<?php
require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../config/auth.php";
require_admin();

$bulan = trim($_GET['bulan'] ?? '');      // format: YYYY-MM
$nis = trim($_GET['nis'] ?? '');
$kategori = (int)($_GET['kategori'] ?? 0);

$where = "1=1";
$params = [];
$types = "";

if ($bulan !== "") { $where .= " AND DATE_FORMAT(ia.created_at, '%Y-%m')=?"; $types.="s"; $params[]=$bulan; }
if ($nis !== "") { $where .= " AND ia.nis=?"; $types.="i"; $params[]=(int)$nis; }
if ($kategori > 0) { $where .= " AND ia.id_kategori=?"; $types.="i"; $params[]=$kategori; }

$sql = "
  SELECT ia.id_pelaporan, ia.created_at, ia.nis, s.kelas, k.ket_kategori, ia.lokasi, ia.ket,
         COALESCE(a.status,'Menunggu') status
  FROM input_aspirasi ia
  JOIN siswa s ON s.nis=ia.nis
  JOIN kategori k ON k.id_kategori=ia.id_kategori
  LEFT JOIN aspirasi a ON a.id_pelaporan=ia.id_pelaporan
  WHERE $where
  ORDER BY ia.created_at DESC
";

$stmt = mysqli_prepare($conn, $sql);
if ($types) { mysqli_stmt_bind_param($stmt, $types, ...$params); }
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$kat = mysqli_query($conn, "SELECT id_kategori, ket_kategori FROM kategori ORDER BY ket_kategori");
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin</title>
<style>
  body{font-family:Arial;background:#f6f7fb;margin:0}
  .bar{background:#fff;padding:14px 18px;display:flex;justify-content:space-between;align-items:center}
  .card{max-width:1100px;margin:18px auto;background:#fff;border-radius:14px;padding:18px}
  table{width:100%;border-collapse:collapse}
  th,td{padding:10px;border-bottom:1px solid #eee;text-align:left;font-size:13px;vertical-align:top}
  input,select{padding:8px;border:1px solid #ddd;border-radius:10px}
  button{padding:8px 10px;border:0;border-radius:10px;background:#2d6cdf;color:#fff;cursor:pointer}
  a.btn{padding:8px 10px;border-radius:10px;background:#111;color:#fff;text-decoration:none}
</style></head>
<body>
  <div class="bar">
    <div>Admin: <b><?= htmlspecialchars($_SESSION['admin_user']) ?></b></div>
    <div>
      <a class="btn" href="admin_siswa.php">Data Siswa</a>
      <a href="logout.php" style="margin-left:12px">Logout</a>
      <a class="btn" href="admin_laporan.php">Laporan</a>
    </div>
  </div>

  <div class="card">
    <h2 style="margin-top:0">List Aspirasi (Filter)</h2>
    <form method="get" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end">
      <div>
        <div style="font-size:12px">Bulan (YYYY-MM)</div>
        <input name="bulan" value="<?= htmlspecialchars($bulan) ?>" placeholder="YYYY-MM">
      </div>
      <div>
        <div style="font-size:12px">NIS</div>
        <input name="nis" value="<?= htmlspecialchars($nis) ?>" placeholder="12345">
      </div>
      <div>
        <div style="font-size:12px">Kategori</div>
        <select name="kategori">
          <option value="0">Semua</option>
          <?php while($k=mysqli_fetch_assoc($kat)): ?>
            <option value="<?= (int)$k['id_kategori'] ?>" <?= $kategori==(int)$k['id_kategori']?'selected':'' ?>>
              <?= htmlspecialchars($k['ket_kategori']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <button type="submit">Terapkan</button>
       <a class="btn" href="admin_aspirasi.php" style="text-align:center;display:inline-block">Reset</a>
    </form>

    <table style="margin-top:12px">
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th><th>NIS</th><th>Kelas</th><th>Kategori</th><th>Lokasi</th><th>Ket</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        <?php while($r=mysqli_fetch_assoc($res)): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($r['created_at']) ?></td>
          <td><?= (int)$r['nis'] ?></td>
          <td><?= htmlspecialchars($r['kelas']) ?></td>
          <td><?= htmlspecialchars($r['ket_kategori']) ?></td>
          <td><?= htmlspecialchars($r['lokasi']) ?></td>
          <td><?= htmlspecialchars($r['ket']) ?></td>
          <td><?= htmlspecialchars($r['status']) ?></td>
          <td><a href="admin_aspirasi_detail.php?id=<?= (int)$r['id_pelaporan'] ?>">Detail / Feedback</a></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body></html>
