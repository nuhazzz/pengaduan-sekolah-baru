<?php
require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../config/auth.php";
require_siswa();

$nis = (int)$_SESSION['siswa_nis'];

$stmt = mysqli_prepare($conn, "
  SELECT ia.id_pelaporan, ia.created_at, k.ket_kategori, ia.lokasi, ia.ket,
         COALESCE(a.status,'Menunggu') status, a.feedback
  FROM input_aspirasi ia
  JOIN kategori k ON k.id_kategori=ia.id_kategori
  LEFT JOIN aspirasi a ON a.id_pelaporan=ia.id_pelaporan
  WHERE ia.nis=?
  ORDER BY ia.created_at DESC
");
mysqli_stmt_bind_param($stmt, "i", $nis);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Histori</title>
<style>
  body{font-family:Arial;background:#f6f7fb;margin:0}
  .card{max-width:1000px;margin:18px auto;background:#fff;border-radius:14px;padding:18px}
  table{width:100%;border-collapse:collapse}
  th,td{padding:10px;border-bottom:1px solid #eee;text-align:left;font-size:13px;vertical-align:top}
  .badge{display:inline-block;padding:4px 8px;border-radius:999px;background:#f1f1f1}
  th.nocol, td.nocol { width: 55px; white-space: nowrap; }
  button{background:#2d6cdf;color:#fff;cursor:pointer}
  </style></head>
<body>
  <div class="card">
    <h2 style="margin-top:0">Histori Aspirasi & Umpan Balik</h2>
        <button type="button" onclick="window.location.href='siswa_dashboard.php'">Kembali</button>
    <table>
<thead>
  <tr>
    <th class="nocol">No</th>
    <th>Tanggal</th><th>Kategori</th><th>Lokasi</th><th>Ket</th><th>Status</th><th>Feedback</th>
  </tr>
</thead>
<tbody>
  <?php $no = 1; ?>
  <?php while($r=mysqli_fetch_assoc($res)): ?>
  <tr>
    <td class="nocol"><?= $no++ ?></td>
    <td><?= htmlspecialchars($r['created_at']) ?></td>
    <td><?= htmlspecialchars($r['ket_kategori']) ?></td>
    <td><?= htmlspecialchars($r['lokasi']) ?></td>
    <td><?= htmlspecialchars($r['ket']) ?></td>
    <td><span class="badge"><?= htmlspecialchars($r['status']) ?></span></td>
    <td><?= nl2br(htmlspecialchars($r['feedback'] ?? '')) ?></td>
  </tr>
  <?php endwhile; ?>
</tbody>
    </table>
  </div>
</body></html>
