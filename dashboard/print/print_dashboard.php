<?php
include '../../api/db.php';

// Ambil data profil
$stmt = $pdo->query("SELECT * FROM tb_profile LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil statistik
$total_siswa = $pdo->query("SELECT COUNT(*) as total FROM tb_siswa")->fetch(PDO::FETCH_ASSOC)['total'];
$total_trx = $pdo->query("SELECT COUNT(*) as total FROM tb_trx")->fetch(PDO::FETCH_ASSOC)['total'];
$total_pendapatan = $pdo->query("SELECT SUM(bayar) as total FROM tb_trx WHERE status = 1")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$total_tentor = $pdo->query("SELECT COUNT(*) as total FROM tb_user WHERE role = 'tentor'")->fetch(PDO::FETCH_ASSOC)['total'];

// Ambil data transaksi terbaru
$stmt = $pdo->query("
  SELECT t.*, s.nama as nama_siswa, p.nama as nama_paket, m.nama as nama_mapel
  FROM tb_trx t
  LEFT JOIN tb_siswa s ON t.email = s.email
  LEFT JOIN tb_paket p ON t.paket = p.kode
  LEFT JOIN tb_mapel m ON t.mapel = m.kode
  ORDER BY t.tanggal DESC
  LIMIT 10
");
$trx_terbaru = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Print</title>
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
    .stats {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 15px;
      margin-bottom: 20px;
    }
    .stat-card {
      border: 1px solid #ddd;
      padding: 15px;
      text-align: center;
      background-color: #f9f9f9;
    }
    .stat-number {
      font-size: 24px;
      font-weight: bold;
      color: #2563eb;
    }
    .stat-label {
      font-size: 12px;
      color: #666;
      margin-top: 5px;
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
    .harga {
      text-align: right;
    }
    .status {
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
    <img src="../../assets/img/<?= htmlspecialchars($profil['logo2']) ?>" alt="Logo">
    <div class="info">
      <div class="nama"><?= htmlspecialchars($profil['nama']) ?></div>
      <div class="alamat"><?= htmlspecialchars($profil['alamat']) ?></div>
      <div class="kontak">Telp: <?= htmlspecialchars($profil['wa']) ?> | Email: <?= htmlspecialchars($profil['email']) ?></div>
    </div>
  </div>
  <h2>LAPORAN DASHBOARD</h2>
  
  <div class="stats">
    <div class="stat-card">
      <div class="stat-number"><?= number_format($total_siswa) ?></div>
      <div class="stat-label">Total Siswa</div>
    </div>
    <div class="stat-card">
      <div class="stat-number"><?= number_format($total_trx) ?></div>
      <div class="stat-label">Total Transaksi</div>
    </div>
    <div class="stat-card">
      <div class="stat-number">Rp <?= number_format($total_pendapatan) ?></div>
      <div class="stat-label">Total Pendapatan</div>
    </div>
    <div class="stat-card">
      <div class="stat-number"><?= number_format($total_tentor) ?></div>
      <div class="stat-label">Total Tentor</div>
    </div>
  </div>
  
  <h3 style="margin: 20px 0 10px 0; font-size: 14px;">Transaksi Terbaru</h3>
  <table>
    <thead>
      <tr>
        <th class="no">No</th>
        <th class="tanggal">Tanggal</th>
        <th>Siswa</th>
        <th>Paket</th>
        <th>Mapel</th>
        <th class="harga">Total</th>
        <th class="status">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($trx_terbaru as $index => $trx): ?>
      <tr>
        <td class="no"><?= $index + 1 ?></td>
        <td class="tanggal"><?= date('d/m/Y', strtotime($trx['tanggal'])) ?></td>
        <td><?= htmlspecialchars($trx['nama_siswa']) ?></td>
        <td><?= htmlspecialchars($trx['nama_paket']) ?></td>
        <td><?= htmlspecialchars($trx['nama_mapel']) ?></td>
        <td class="harga">Rp <?= number_format($trx['bayar'], 0, ',', '.') ?></td>
        <td class="status"><?= $trx['status'] == 1 ? 'Lunas' : 'Belum Lunas' ?></td>
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