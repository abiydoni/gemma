<?php
include '../../api/db.php';

// Ambil data profil
$stmt = $pdo->query("SELECT * FROM tb_profile LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil parameter filter
$filter_tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$filter_tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';

// Debug: tampilkan parameter yang diterima
// echo "Filter awal: " . $filter_tanggal_awal . "<br>";
// echo "Filter akhir: " . $filter_tanggal_akhir . "<br>";

// Buat query dengan filter
$where_conditions = [];
$params = [];

if (!empty($filter_tanggal_awal)) {
    $where_conditions[] = "tanggal >= ?";
    $params[] = $filter_tanggal_awal;
}

if (!empty($filter_tanggal_akhir)) {
    $where_conditions[] = "tanggal <= ?";
    $params[] = $filter_tanggal_akhir;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Ambil data keuangan dengan filter
$stmt = $pdo->prepare("
  SELECT *
  FROM tb_keuangan
  $where_clause
  ORDER BY tanggal, id
");
$stmt->execute($params);
$keuangan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug: tampilkan jumlah data yang ditemukan
// echo "Jumlah data: " . count($keuangan_list) . "<br>";
// echo "Query: SELECT * FROM tb_keuangan $where_clause ORDER BY tanggal DESC, id DESC<br>";
// echo "Params: " . implode(', ', $params) . "<br>";

// Hitung total
$total_debet = 0;
$total_kredit = 0;
foreach($keuangan_list as $keuangan) {
  $total_debet += $keuangan['debet'];
  $total_kredit += $keuangan['kredit'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Keuangan - Print</title>
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
  <h2>LAPORAN KEUANGAN</h2>
  <?php if (!empty($filter_tanggal_awal) || !empty($filter_tanggal_akhir)): ?>
    <p style="text-align:center;margin:0 0 12px 0;font-size:10px;color:#666;">
      Filter: 
      <?php 
      $filter_info = [];
      if (!empty($filter_tanggal_awal)) {
        $filter_info[] = "Dari: " . date('d/m/Y', strtotime($filter_tanggal_awal));
      }
      if (!empty($filter_tanggal_akhir)) {
        $filter_info[] = "Sampai: " . date('d/m/Y', strtotime($filter_tanggal_akhir));
      }
      echo implode(' | ', $filter_info);
      ?>
    </p>
  <?php endif; ?>
  
  <div class="summary">
    <div>Total Transaksi: <?= count($keuangan_list) ?></div>
    <div>Total Debet: <?= number_format($total_debet, 0, ',', '.') ?></div>
    <div>Total Kredit: <?= number_format($total_kredit, 0, ',', '.') ?></div>
    <div>Saldo: <?= number_format($total_debet - $total_kredit, 0, ',', '.') ?></div>
  </div>
  
  <table>
    <thead>
      <tr>
        <th class="no">No</th>
        <th class="tanggal">Tanggal</th>
        <th>Keterangan</th>
        <th class="harga">Debet</th>
        <th class="harga">Kredit</th>
        <th class="harga">Saldo</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $saldo = 0;
      foreach($keuangan_list as $index => $keuangan): 
        $saldo += $keuangan['debet'] - $keuangan['kredit'];
      ?>
      <tr>
        <td class="no"><?= $index + 1 ?></td>
        <td class="tanggal"><?= date('d/m/Y', strtotime($keuangan['tanggal'])) ?></td>
        <td><?= htmlspecialchars($keuangan['keterangan']) ?></td>
        <td class="harga"><?= $keuangan['debet'] > 0 ? number_format($keuangan['debet'], 0, ',', '.') : '-' ?></td>
        <td class="harga"><?= $keuangan['kredit'] > 0 ? number_format($keuangan['kredit'], 0, ',', '.') : '-' ?></td>
        <td class="harga"><?= number_format($saldo, 0, ',', '.') ?></td>
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