<?php
include '../../api/db.php';

// Ambil data profil
$stmt = $pdo->query("SELECT * FROM tb_profile LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil ID siswa dari parameter
$id_siswa = $_GET['id'] ?? 0;

// Ambil data siswa
$stmt = $pdo->prepare("SELECT * FROM tb_siswa WHERE id = ?");
$stmt->execute([$id_siswa]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    echo "Siswa tidak ditemukan";
    exit;
}

// Ambil data transaksi siswa
$stmt = $pdo->prepare("
  SELECT t.*, p.nama as nama_paket, m.nama as nama_mapel
  FROM tb_trx t
  LEFT JOIN tb_paket p ON t.paket = p.kode
  LEFT JOIN tb_mapel m ON t.mapel = m.kode
  WHERE t.email = ?
  ORDER BY t.tanggal DESC
");
$stmt->execute([$siswa['email']]);
$transaksi_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data jadwal siswa
$stmt = $pdo->prepare("
  SELECT tt.*, t.bayar as total_les, p.nama as nama_paket, m.nama as nama_mapel
  FROM tb_trx_tanggal tt
  LEFT JOIN tb_trx t ON tt.id_trx = t.id
  LEFT JOIN tb_paket p ON t.paket = p.kode
  LEFT JOIN tb_mapel m ON t.mapel = m.kode
  WHERE t.email = ?
  ORDER BY tt.tanggal DESC, tt.jam_trx ASC
");
$stmt->execute([$siswa['email']]);
$jadwal_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Siswa - Print</title>
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
  <h2>DETAIL SISWA</h2>
  
  <div class="siswa-info">
    <div class="siswa-item">
      <div class="siswa-label">Nama:</div>
      <div class="siswa-value"><?= htmlspecialchars($siswa['nama']) ?></div>
    </div>
    <div class="siswa-item">
      <div class="siswa-label">Email:</div>
      <div class="siswa-value"><?= htmlspecialchars($siswa['email']) ?></div>
    </div>
    <div class="siswa-item">
      <div class="siswa-label">Telepon:</div>
      <div class="siswa-value"><?= htmlspecialchars($siswa['telepon'] ?? '-') ?></div>
    </div>
    <div class="siswa-item">
      <div class="siswa-label">Alamat:</div>
      <div class="siswa-value"><?= htmlspecialchars($siswa['alamat'] ?? '-') ?></div>
    </div>
    <div class="siswa-item">
      <div class="siswa-label">Tanggal Daftar:</div>
      <div class="siswa-value"><?= date('d/m/Y', strtotime($siswa['created_at'])) ?></div>
    </div>
  </div>
  
  <h3 style="margin: 20px 0 10px 0; font-size: 14px;">Data Transaksi</h3>
  <table>
    <thead>
      <tr>
        <th class="no">No</th>
        <th class="tanggal">Tanggal</th>
        <th>Paket</th>
        <th>Mapel</th>
        <th class="harga">Total</th>
        <th class="status">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($transaksi_list as $index => $trx): ?>
      <tr>
        <td class="no"><?= $index + 1 ?></td>
        <td class="tanggal"><?= date('d/m/Y', strtotime($trx['tanggal'])) ?></td>
        <td><?= htmlspecialchars($trx['nama_paket']) ?></td>
        <td><?= htmlspecialchars($trx['nama_mapel']) ?></td>
        <td class="harga">Rp <?= number_format($trx['bayar'], 0, ',', '.') ?></td>
        <td class="status"><?= $trx['status'] == 1 ? 'Lunas' : 'Belum Lunas' ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  
  <h3 style="margin: 20px 0 10px 0; font-size: 14px;">Data Jadwal</h3>
  <table>
    <thead>
      <tr>
        <th class="no">No</th>
        <th class="tanggal">Tanggal</th>
        <th>Jam</th>
        <th>Paket</th>
        <th>Mapel</th>
        <th>Total Les</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($jadwal_list as $index => $jadwal): ?>
      <tr>
        <td class="no"><?= $index + 1 ?></td>
        <td class="tanggal"><?= date('d/m/Y', strtotime($jadwal['tanggal'])) ?></td>
        <td><?= htmlspecialchars($jadwal['jam_trx']) ?></td>
        <td><?= htmlspecialchars($jadwal['nama_paket']) ?></td>
        <td><?= htmlspecialchars($jadwal['nama_mapel']) ?></td>
        <td><?= $jadwal['total_les'] ?></td>
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