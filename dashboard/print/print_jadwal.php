<?php
include '../../api/db.php';
// Ambil data profil
$stmt = $pdo->query("SELECT * FROM tb_profile LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);
// Ambil data jadwal
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$startBulan = DateTime::createFromFormat('Y-m', $bulan);
$start = strtotime($startBulan->format('Y-m-01'));
$end = strtotime(date('Y-m-t', $start));
$stmt = $pdo->prepare("
  SELECT tgl.tanggal, tgl.jam_trx, s.nama AS nama_siswa, m.nama AS nama_mapel, tr.paket
  FROM tb_trx_tanggal tgl
  JOIN tb_trx tr ON tgl.id_trx = tr.id
  LEFT JOIN tb_siswa s ON tr.email = s.email
  LEFT JOIN tb_mapel m ON tr.mapel = m.id
  WHERE tgl.tanggal BETWEEN ? AND ?
");
$stmt->execute([date('Y-m-01', $start), date('Y-m-t', $start)]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$tanggalList = [];
for($d=$start; $d<=strtotime(date('Y-m-t', $start)); $d+=86400) {
  $tanggalList[] = date('Y-m-d', $d);
}
$jamList = [];
for($h=9; $h<=20; $h++) {
  $jamList[] = sprintf('%02d:00', $h);
}
// Susun data jadwal per tanggal dan jam
$jadwalMap = [];
foreach($rows as $row) {
  $jadwalMap[$row['tanggal']][$row['jam_trx']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Print Jadwal Les</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 11px; color: #222; max-width: 297mm; margin: 0 auto; background: #fff; padding: 10px; }
    .kop { display: flex; align-items: center; gap: 16px; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 16px; max-width: 100%; }
    .kop img { height: 60px; }
    .kop .info { line-height: 1.2; }
    .kop .info .nama { font-size: 20px; font-weight: bold; }
    .kop .info .alamat { font-size: 14px; }
    .kop .info .kontak { font-size: 13px; }
    table { border-collapse: collapse; width: 100%; margin-top: 16px; max-width: 100%; }
    th, td { border: 1px solid #888; padding: 4px 6px; text-align: left; }
    th { background: #e0e7ef; font-size: 11px; }
    td { font-size: 10px; }
    .rotate { writing-mode: vertical-lr; transform: rotate(180deg); }
    @media print {
      @page { size: A4 landscape; margin: 12mm 10mm 12mm 10mm; }
      body { width: 297mm; margin: 0 auto; background: #fff !important; }
      .kop, table { max-width: 100%; }
    }
  </style>
</head>
<body>
  <div class="kop">
    <?php include 'kop_surat.php'; ?>
  </div>
  <h2 style="text-align:center;margin:0 0 12px 0;">JADWAL LES BULAN <?= strtoupper(date('F Y', $start)) ?></h2>
  <div style="overflow-x:auto;">
  <table>
    <thead>
      <tr>
        <th>Jam</th>
        <?php foreach($tanggalList as $tgl): ?>
          <th class="rotate"><?= date('j', strtotime($tgl)) ?><br><?= ['Min','Sen','Sel','Rab','Kam','Jum','Sab'][date('w', strtotime($tgl))] ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach($jamList as $jam): ?>
      <tr>
        <td style="font-weight:bold; text-align:center;"><?= $jam ?></td>
        <?php foreach($tanggalList as $tgl): ?>
          <td style="min-width:18px;max-width:40px;vertical-align:top;">
            <?php if(isset($jadwalMap[$tgl][$jam])): ?>
              <?php foreach($jadwalMap[$tgl][$jam] as $item): ?>
                <div><b><?= htmlspecialchars($item['nama_siswa']) ?></b><br><span style="font-size:9px;">(<?= htmlspecialchars($item['nama_mapel']) ?>)</span></div>
              <?php endforeach; ?>
            <?php endif; ?>
          </td>
        <?php endforeach; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <script>
    window.onload = function() {
      window.print();
      window.onafterprint = function() { window.close(); }
    }
  </script>
</body>
</html> 