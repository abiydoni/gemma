<?php
include '../../api/db.php';

// Ambil data profil
$stmt = $pdo->query("SELECT * FROM tb_profile LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil data dari parameter
$data = isset($_GET['data']) ? json_decode(urldecode($_GET['data']), true) : null;

if (!$data) {
    echo "Data tidak ditemukan";
    exit;
}

$jadwal = $data['jadwal'] ?? [];
$mapel = $data['mapel'] ?? '';
$siswa = $data['siswa'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Jadwal Les - Print</title>
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
    .siswa-info {
      margin-bottom: 20px;
      border: 1px solid #ddd;
      padding: 15px;
      background-color: #f9f9f9;
    }
    .siswa-item {
      margin-bottom: 8px;
      display: flex;
    }
    .siswa-label {
      font-weight: bold;
      width: 120px;
      color: #333;
    }
    .siswa-value {
      flex: 1;
      color: #666;
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
    .jam {
      width: 80px;
    }
    .mapel {
      width: 150px;
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
    <img src="../../assets/img/<?= htmlspecialchars($profil['logo2']) ?>" alt="Logo">
    <div class="info">
      <div class="nama"><?= htmlspecialchars($profil['nama']) ?></div>
      <div class="alamat"><?= htmlspecialchars($profil['alamat']) ?></div>
      <div class="kontak">Telp: <?= htmlspecialchars($profil['wa']) ?> | Email: <?= htmlspecialchars($profil['email']) ?></div>
    </div>
  </div>
  <h2>DETAIL JADWAL LES</h2>
  
  <div class="siswa-info">
    <div class="siswa-item">
      <div class="siswa-label">Nama Siswa:</div>
      <div class="siswa-value"><?= htmlspecialchars($siswa['nama'] ?? '-') ?></div>
    </div>
    <div class="siswa-item">
      <div class="siswa-label">Email:</div>
      <div class="siswa-value"><?= htmlspecialchars($siswa['email'] ?? '-') ?></div>
    </div>
    <div class="siswa-item">
      <div class="siswa-label">Mata Pelajaran:</div>
      <div class="siswa-value"><?= htmlspecialchars($mapel) ?></div>
    </div>
    <div class="siswa-item">
      <div class="siswa-label">Jumlah Jadwal:</div>
      <div class="siswa-value"><?= count($jadwal) ?> sesi</div>
    </div>
  </div>
  
  <h3 style="margin: 20px 0 10px 0; font-size: 14px;">Jadwal Les</h3>
  <table>
    <thead>
      <tr>
        <th class="no">No</th>
        <th class="tanggal">Tanggal</th>
        <th class="jam">Jam</th>
        <th class="mapel">Mata Pelajaran</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($jadwal as $index => $j): ?>
      <tr>
        <td class="no"><?= $index + 1 ?></td>
        <td class="tanggal"><?= date('d/m/Y', strtotime($j['tanggal'])) ?></td>
        <td class="jam"><?= htmlspecialchars($j['jam_trx']) ?></td>
        <td class="mapel"><?= htmlspecialchars($mapel) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  
  <div style="margin-top: 30px; text-align: center; font-size: 11px; color: #666;">
    <p>Dicetak pada: <?= date('d/m/Y H:i') ?></p>
  </div>
  
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