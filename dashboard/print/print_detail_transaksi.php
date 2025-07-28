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

$transaksi = $data;
$jadwal = $transaksi['jadwal'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Transaksi - Print</title>
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
    .info-section {
      margin-bottom: 20px;
      border: 1px solid #ddd;
      padding: 15px;
      background-color: #f9f9f9;
    }
    .info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      margin-bottom: 15px;
    }
    .info-item {
      display: flex;
      margin-bottom: 8px;
    }
    .info-label {
      font-weight: bold;
      width: 120px;
      color: #333;
    }
    .info-value {
      flex: 1;
      color: #666;
    }
    .payment-info {
      background-color: #e8f5e8;
      border: 1px solid #4caf50;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
    }
    .payment-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 10px;
      margin-bottom: 10px;
    }
    .payment-item {
      text-align: center;
      background-color: white;
      padding: 8px;
      border-radius: 3px;
    }
    .payment-label {
      font-size: 10px;
      color: #666;
      margin-bottom: 3px;
    }
    .payment-value {
      font-weight: bold;
      font-size: 11px;
    }
    .status-badge {
      text-align: center;
      padding: 5px 10px;
      border-radius: 15px;
      font-size: 10px;
      font-weight: bold;
    }
    .status-lunas {
      background-color: #d4edda;
      color: #155724;
    }
    .status-belum {
      background-color: #f8d7da;
      color: #721c24;
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
      width: 120px;
    }
    .jam {
      width: 80px;
    }
    .status {
      width: 100px;
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
  <h2>DETAIL TRANSAKSI</h2>
  
  <div class="info-section">
    <h3 style="margin: 0 0 10px 0; font-size: 14px;">Informasi Transaksi</h3>
    <div class="info-grid">
      <div class="info-item">
        <div class="info-label">Nama Siswa:</div>
        <div class="info-value"><?= htmlspecialchars($transaksi['nama_siswa'] ?? '-') ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Paket:</div>
        <div class="info-value"><?= htmlspecialchars($transaksi['nama_paket'] ?? '-') ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Mata Pelajaran:</div>
        <div class="info-value"><?= htmlspecialchars($transaksi['nama_mapel'] ?? '-') ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Tentor:</div>
        <div class="info-value"><?= htmlspecialchars($transaksi['nama_tentor'] ?? 'Belum ditentukan') ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Tanggal Transaksi:</div>
        <div class="info-value"><?= date('d/m/Y H:i', strtotime($transaksi['tanggal'])) ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">ID Transaksi:</div>
        <div class="info-value">#<?= htmlspecialchars($transaksi['id']) ?></div>
      </div>
    </div>
  </div>
  
  <div class="payment-info">
    <h3 style="margin: 0 0 10px 0; font-size: 14px;">Informasi Pembayaran</h3>
    <div class="payment-grid">
      <div class="payment-item">
        <div class="payment-label">Total Harga</div>
        <div class="payment-value">Rp <?= number_format($transaksi['harga'], 0, ',', '.') ?></div>
      </div>
      <div class="payment-item">
        <div class="payment-label">Sudah Bayar</div>
        <div class="payment-value" style="color: #28a745;">Rp <?= number_format($transaksi['bayar'], 0, ',', '.') ?></div>
      </div>
      <div class="payment-item">
        <div class="payment-label">Sisa</div>
        <div class="payment-value" style="color: <?= ($transaksi['harga'] - $transaksi['bayar']) > 0 ? '#dc3545' : '#28a745' ?>;">
          Rp <?= number_format($transaksi['harga'] - $transaksi['bayar'], 0, ',', '.') ?>
        </div>
      </div>
    </div>
    <div class="status-badge <?= ($transaksi['harga'] - $transaksi['bayar']) > 0 ? 'status-belum' : 'status-lunas' ?>">
      <?= ($transaksi['harga'] - $transaksi['bayar']) > 0 ? 'BELUM LUNAS' : 'LUNAS' ?>
    </div>
  </div>
  
  <?php if (!empty($jadwal)): ?>
  <div class="info-section">
    <h3 style="margin: 0 0 10px 0; font-size: 14px;">Jadwal Les (<?= count($jadwal) ?> sesi)</h3>
    <table>
      <thead>
        <tr>
          <th class="no">No</th>
          <th class="tanggal">Tanggal</th>
          <th class="jam">Jam</th>
          <th class="status">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($jadwal as $index => $j): ?>
        <?php 
          $jadwalDate = new DateTime($j['tanggal']);
          $today = new DateTime();
          $isPast = $jadwalDate < $today;
          $isToday = $jadwalDate->format('Y-m-d') === $today->format('Y-m-d');
          
          $statusText = 'Belum';
          $statusClass = 'background-color: #f8f9fa; color: #6c757d;';
          
          if ($isPast) {
            $statusText = 'Selesai';
            $statusClass = 'background-color: #d4edda; color: #155724;';
          } elseif ($isToday) {
            $statusText = 'Hari Ini';
            $statusClass = 'background-color: #cce5ff; color: #004085;';
          }
        ?>
        <tr>
          <td class="no"><?= $index + 1 ?></td>
          <td class="tanggal"><?= $jadwalDate->format('d/m/Y') ?> (<?= $jadwalDate->format('l') ?>)</td>
          <td class="jam"><?= htmlspecialchars($j['jam_trx']) ?></td>
          <td class="status" style="<?= $statusClass ?>"><?= $statusText ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="info-section">
    <h3 style="margin: 0 0 10px 0; font-size: 14px;">Jadwal Les</h3>
    <p style="color: #856404; background-color: #fff3cd; padding: 10px; border-radius: 5px; margin: 0;">
      <i class="fa-solid fa-info-circle"></i> Belum ada jadwal les yang diatur untuk transaksi ini.
    </p>
  </div>
  <?php endif; ?>
  
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