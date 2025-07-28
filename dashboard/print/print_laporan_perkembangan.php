<?php
include '../../api/db.php';

// Ambil data profil
$stmt = $pdo->query("SELECT * FROM tb_profile LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil data laporan perkembangan
$stmt = $pdo->query("
  SELECT l.*, s.nama as nama_siswa, m.nama as nama_mapel, u.nama as nama_tentor
  FROM tb_laporan l
  LEFT JOIN tb_siswa s ON l.id_siswa = s.id
  LEFT JOIN tb_mapel m ON l.id_mapel = m.id
  LEFT JOIN tb_user u ON l.id_tentor = u.id
  ORDER BY l.tanggal DESC
");
$laporan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Perkembangan - Print</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      font-size: 12px;
    }
    .kop {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      border-bottom: 2px solid #333;
      padding-bottom: 10px;
    }
    .kop img {
      width: 60px;
      height: 60px;
      margin-right: 15px;
    }
    .kop .info {
      flex: 1;
    }
    .kop .nama {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 5px;
    }
    .kop .alamat {
      font-size: 12px;
      margin-bottom: 3px;
    }
    .kop .kontak {
      font-size: 11px;
    }
    h2 {
      text-align: center;
      margin: 0 0 20px 0;
      font-size: 16px;
    }
    .summary {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
      font-weight: bold;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
      font-weight: bold;
    }
    .no {
      width: 50px;
      text-align: center;
    }
    .tanggal {
      width: 100px;
    }
    .nilai {
      text-align: center;
    }
    @media print {
      body {
        margin: 0;
        padding: 15px;
      }
      @page {
        size: A4 portrait;
        margin: 1cm;
      }
    }
  </style>
</head>
<body>
  <div class="kop">
    <?php include 'kop_surat.php'; ?>
  </div>
  <h2>LAPORAN PERKEMBANGAN SISWA</h2>
  
  <div class="summary">
    <div>Total Laporan: <?= count($laporan_list) ?></div>
    <div>Periode: <?= date('d/m/Y') ?></div>
  </div>
  
  <table>
    <thead>
      <tr>
        <th class="no">No</th>
        <th class="tanggal">Tanggal</th>
        <th>Siswa</th>
        <th>Mapel</th>
        <th>Tentor</th>
        <th>Materi</th>
        <th class="nilai">Nilai</th>
        <th>Catatan</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($laporan_list as $index => $laporan): ?>
      <tr>
        <td class="no"><?= $index + 1 ?></td>
        <td class="tanggal"><?= date('d/m/Y', strtotime($laporan['tanggal'])) ?></td>
        <td><?= htmlspecialchars($laporan['nama_siswa']) ?></td>
        <td><?= htmlspecialchars($laporan['nama_mapel']) ?></td>
        <td><?= htmlspecialchars($laporan['nama_tentor']) ?></td>
        <td><?= htmlspecialchars($laporan['materi']) ?></td>
        <td class="nilai"><?= $laporan['nilai'] ?></td>
        <td><?= htmlspecialchars($laporan['catatan'] ?? '-') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  
  <script>
    window.onload = function() {
      window.print();
      window.onafterprint = function() {
        window.close();
      }
    }
  </script>
</body>
</html> 