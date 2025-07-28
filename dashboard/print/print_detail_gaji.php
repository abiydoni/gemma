<?php
include '../../api/db.php';

// Ambil data profil
$stmt = $pdo->query("SELECT * FROM tb_profile LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil data dari parameter
$data = isset($_GET['data']) ? $_GET['data'] : '';
$printData = json_decode(urldecode($data), true);

if (!$printData) {
    echo "Data tidak ditemukan";
    exit;
}

$namaTentor = $printData['namaTentor'];
$items = $printData['items'];

// Hitung total
$totalGaji = 0;
$totalPending = 0;
$totalDibayar = 0;

foreach($items as $item) {
    $totalGaji += intval($item['jumlah_gaji']);
    if ($item['status_pembayaran'] === 'pending') {
        $totalPending += 1;
    } else {
        $totalDibayar += 1;
    }
}

$statusText = $totalPending === 0 ? 'Lunas' : ($totalDibayar === 0 ? 'Belum Dibayar' : 'Sebagian Dibayar');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Gaji Tentor - Print</title>
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
    .header-info {
      background-color: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
    }
    .tentor-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    .summary-cards {
      display: flex;
      gap: 15px;
      margin-bottom: 15px;
    }
    .card {
      background-color: white;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      padding: 10px;
      flex: 1;
      text-align: center;
    }
    .card-title {
      font-size: 10px;
      color: #64748b;
      margin-bottom: 5px;
    }
    .card-value {
      font-size: 14px;
      font-weight: bold;
      color: #1e293b;
    }
    .status-badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 10px;
      font-weight: bold;
      text-align: center;
      margin-bottom: 15px;
    }
    .status-lunas { background-color: #dcfce7; color: #166534; }
    .status-pending { background-color: #fef3c7; color: #92400e; }
    .status-sebagian { background-color: #dbeafe; color: #1e40af; }
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
      font-size: 10px;
    }
    .no {
      width: 40px;
      text-align: center;
    }
    .harga {
      text-align: right;
    }
    .status {
      text-align: center;
    }
    .footer-summary {
      background-color: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      padding: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
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
  
  <h2>DETAIL GAJI TENTOR</h2>
  
  <div class="header-info">
    <div class="tentor-info">
      <div>
        <h3 style="margin: 0 0 5px 0; font-size: 14px; font-weight: bold;"><?= htmlspecialchars($namaTentor) ?></h3>
        <p style="margin: 0; font-size: 10px; color: #64748b;">Detail Gaji</p>
      </div>
      <div style="text-align: right;">
        <p style="margin: 0; font-size: 10px; color: #64748b;">Total</p>
        <p style="margin: 0; font-size: 14px; font-weight: bold; color: #059669;"><?= number_format($totalGaji, 0, ',', '.') ?></p>
      </div>
    </div>
    
    <div class="summary-cards">
      <div class="card">
        <div class="card-title">Total</div>
        <div class="card-value"><?= count($items) ?></div>
      </div>
      <div class="card">
        <div class="card-title">Dibayar</div>
        <div class="card-value" style="color: #059669;"><?= $totalDibayar ?></div>
      </div>
      <div class="card">
        <div class="card-title">Pending</div>
        <div class="card-value" style="color: #d97706;"><?= $totalPending ?></div>
      </div>
    </div>
    
    <div class="status-badge <?= $totalPending === 0 ? 'status-lunas' : ($totalDibayar === 0 ? 'status-pending' : 'status-sebagian') ?>">
      <?= $statusText ?>
    </div>
  </div>
  
  <table>
    <thead>
      <tr>
        <th class="no">No</th>
        <th>Siswa</th>
        <th>Mapel</th>
        <th class="harga">Bayar</th>
        <th class="harga">%</th>
        <th class="harga">Gaji</th>
        <th class="status">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($items as $index => $item): ?>
      <tr>
        <td class="no"><?= $index + 1 ?></td>
        <td><?= htmlspecialchars($item['nama_siswa'] ?: $item['email_siswa']) ?></td>
        <td><?= htmlspecialchars($item['nama_mapel']) ?></td>
        <td class="harga"><?= number_format($item['total_pembayaran'], 0, ',', '.') ?></td>
        <td class="harga"><?= $item['presentase_gaji'] ?>%</td>
        <td class="harga"><?= number_format($item['jumlah_gaji'], 0, ',', '.') ?></td>
        <td class="status">
          <span class="status-badge <?= $item['status_pembayaran'] === 'dibayar' ? 'status-lunas' : 'status-pending' ?>">
            <?= $item['status_pembayaran'] === 'dibayar' ? 'Dibayar' : 'Pending' ?>
          </span>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  
  <div class="footer-summary">
    <div>
      <p style="margin: 0; font-size: 10px; color: #64748b;">Status</p>
      <p style="margin: 0; font-size: 12px; font-weight: bold;"><?= $statusText ?></p>
    </div>
    <div style="text-align: right;">
      <p style="margin: 0; font-size: 10px; color: #64748b;">Total</p>
      <p style="margin: 0; font-size: 14px; font-weight: bold; color: #059669;"><?= number_format($totalGaji, 0, ',', '.') ?></p>
    </div>
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