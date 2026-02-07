<?php
require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../config/auth.php";
require_admin();

$bulan = trim($_GET['bulan'] ?? '');      // 01..12
$tahun = trim($_GET['tahun'] ?? '');      // YYYY
$tanggal = trim($_GET['tanggal'] ?? '');  // YYYY-MM-DD (tanggal spesifik)
$tgl1  = trim($_GET['tgl1'] ?? '');       // YYYY-MM-DD
$tgl2  = trim($_GET['tgl2'] ?? '');       // YYYY-MM-DD
$kategori = (int)($_GET['kategori'] ?? 0);
$status = trim($_GET['status'] ?? '');    // Menunggu/Proses/Selesai

$where = "1=1";
$params = [];
$types = "";

/*
  PRIORITAS FILTER TANGGAL:
  1) tanggal spesifik
  2) range tgl1-tgl2
  3) bulan+tahun
  4) tahun saja
*/
if ($tanggal !== "") {
  $where .= " AND DATE(ia.created_at)=?";
  $types .= "s";
  $params[] = $tanggal;

} elseif ($tgl1 !== "" && $tgl2 !== "") {
  $where .= " AND DATE(ia.created_at) BETWEEN ? AND ?";
  $types .= "ss";
  $params[] = $tgl1;
  $params[] = $tgl2;

} elseif ($bulan !== "" && $tahun !== "") {
  $where .= " AND MONTH(ia.created_at)=? AND YEAR(ia.created_at)=?";
  $types .= "ii";
  $params[] = (int)$bulan;   // "02" -> 2
  $params[] = (int)$tahun;

} elseif ($bulan !== "") {
  // kalau pilih bulan tapi tahun kosong: ambil semua data di bulan itu dari semua tahun
  $where .= " AND MONTH(ia.created_at)=?";
  $types .= "i";
  $params[] = (int)$bulan;

} elseif ($tahun !== "") {
  $where .= " AND YEAR(ia.created_at)=?";
  $types .= "i";
  $params[] = (int)$tahun;
}

if ($kategori > 0) {
  $where .= " AND ia.id_kategori=?";
  $types .= "i";
  $params[] = $kategori;
}

if ($status !== "") {
  $where .= " AND COALESCE(a.status,'Menunggu')=?";
  $types .= "s";
  $params[] = $status;
}
$kelas = trim($_GET['kelas'] ?? ''); // contoh: "X-1" / "XI IPA 2" (sesuai isi kolom s.kelas)


/* Ambil list data */
$sql = "
  SELECT ia.id_pelaporan, ia.created_at, ia.nis, s.kelas,
         k.ket_kategori, ia.lokasi, ia.ket,
         COALESCE(a.status,'Menunggu') status, a.feedback
  FROM input_aspirasi ia
  JOIN siswa s ON s.nis=ia.nis
  JOIN kategori k ON k.id_kategori=ia.id_kategori
  LEFT JOIN aspirasi a ON a.id_pelaporan=ia.id_pelaporan
  WHERE $where
  ORDER BY ia.created_at DESC
";
$stmt = mysqli_prepare($conn, $sql);
if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

/* Ringkasan */
$sql2 = "
  SELECT
    COUNT(*) total,
    SUM(CASE WHEN COALESCE(a.status,'Menunggu')='Menunggu' THEN 1 ELSE 0 END) menunggu,
    SUM(CASE WHEN COALESCE(a.status,'Menunggu')='Proses' THEN 1 ELSE 0 END) proses,
    SUM(CASE WHEN COALESCE(a.status,'Menunggu')='Selesai' THEN 1 ELSE 0 END) selesai
  FROM input_aspirasi ia
  LEFT JOIN aspirasi a ON a.id_pelaporan=ia.id_pelaporan
  WHERE $where
";
$stmt2 = mysqli_prepare($conn, $sql2);
if ($types) mysqli_stmt_bind_param($stmt2, $types, ...$params);
mysqli_stmt_execute($stmt2);
$stat = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));

$kat = mysqli_query($conn, "SELECT id_kategori, ket_kategori FROM kategori ORDER BY ket_kategori");

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Laporan Aspirasi</title>
  <style>
    body{font-family:Arial;background:#f6f7fb;margin:0}
    .bar{background:#fff;padding:14px 18px;display:flex;justify-content:space-between;align-items:center}
    .card{max-width:1200px;margin:18px auto;background:#fff;border-radius:14px;padding:18px}
    input,select{padding:8px;border:1px solid #ddd;border-radius:10px}
    button,a.btn{padding:8px 10px;border:0;border-radius:10px;background:#2d6cdf;color:#fff;text-decoration:none;cursor:pointer}
    a.link{color:#2d6cdf;text-decoration:none}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{padding:10px;border-bottom:1px solid #eee;text-align:left;font-size:13px;vertical-align:top}
    .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:12px}
    .box{background:#f3f5ff;border-radius:12px;padding:12px}
    .badge{display:inline-block;padding:4px 8px;border-radius:999px;background:#f1f1f1}
    @media print {
      .no-print{display:none !important}
      body{background:#fff}
      .card{box-shadow:none;margin:0;max-width:none}
    }
    .action-btn{display:inline-block;padding:8px 10px;border-radius:10px;background:#2d6cdf;color:#fff;text-decoration:none;font-size:12px;
    }
.action-btn:hover{opacity:.9}

  </style>
</head>
<body>
  <div class="bar">
    <div>Admin: <b><?= htmlspecialchars($_SESSION['admin_user']) ?></b></div>
    <div>
      <a class="btn" href="admin_siswa.php">Data Siswa</a>
      <a href="logout.php" style="margin-left:12px">Logout</a> 
    </div>
  </div>

  <div class="card">
    <div class="no-print">
      <h2 style="margin-top:0">Filter</h2>
      <form method="get" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end">
        <div>
          <div style="font-size:12px">Tanggal (Spesifik)</div>
          <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
        </div>

        <div>
          <div style="font-size:12px">Bulan</div>
          <select name="bulan">
            <option value="">Semua</option>
            <?php for($m=1;$m<=12;$m++): 
              $mm = str_pad($m,2,'0',STR_PAD_LEFT); ?>
              <option value="<?= $mm ?>" <?= $bulan===$mm?'selected':'' ?>>
                <?= $mm ?>
              </option>
            <?php endfor; ?>
          </select>
        </div>

        <div>
          <div style="font-size:12px">Tahun</div>
          <select name="tahun">
            <option value="">Semua</option>
            <?php 
              $yNow = (int)date('Y');
              for($y=$yNow;$y>=$yNow-5;$y--): ?>
              <option value="<?= $y ?>" <?= $tahun==(string)$y?'selected':'' ?>>
                <?= $y ?>
              </option>
            <?php endfor; ?>
          </select>
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
        <div>
          <div style="font-size:12px">Status</div>
          <select name="status">
            <option value="">Semua</option>
            <?php foreach(['Menunggu','Proses','Selesai'] as $st): ?>
              <option value="<?= $st ?>" <?= $status===$st?'selected':'' ?>><?= $st ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit">Terapkan</button>
      </form>
    </div>

    <h2 style="margin:14px 0 6px">Ringkasan</h2>
    <div class="grid">
      <div class="box"><b>Total</b><div><?= (int)$stat['total'] ?></div></div>
      <div class="box"><b>Menunggu</b><div><?= (int)$stat['menunggu'] ?></div></div>
      <div class="box"><b>Proses</b><div><?= (int)$stat['proses'] ?></div></div>
      <div class="box"><b>Selesai</b><div><?= (int)$stat['selesai'] ?></div></div>
    </div>

    <h2 style="margin:14px 0 6px">Detail Data</h2>
    <table>
<thead>
  <tr>
    <th>No</th>
    <th>Tanggal</th><th>NIS</th><th>Kelas</th><th>Kategori</th><th>Lokasi</th><th>KeteranganS</th><th>Status</th><th>Aksi</th>
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
    <td><a class="action-btn" href="admin_aspirasi_detail.php?id=<?= (int)$r['id_pelaporan'] ?>">Feedback</a></td>
  </tr>
  <?php endwhile; ?>
</tbody>
    </table>
  </div>
</body>
</html>
