<?php
include '../../api/db.php';

// Ambil data profil
$stmt = $pdo->query("SELECT * FROM tb_profile LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil parameter filter
$filter_tentor = isset($_GET['tentor']) ? $_GET['tentor'] : '';
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Buat query dengan filter
$where_conditions = [];
$params = [];

if (!empty($filter_tentor)) {
    $where_conditions[] = "gt.id_tentor = ?";
    $params[] = $filter_tentor;
}

if (!empty($filter_bulan)) {
    $where_conditions[] = "gt.bulan = ?";
    $params[] = $filter_bulan;
}

if (!empty($filter_status)) {
    $where_conditions[] = "gt.status_pembayaran = ?";
    $params[] = $filter_status;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Ambil data gaji tentor dengan filter
$stmt = $pdo->prepare("
  SELECT gt.*, u.nama as nama_tentor, s.nama as nama_siswa, m.nama as nama_mapel
  FROM tb_gaji_tentor gt
  LEFT JOIN tb_user u ON gt.id_tentor = u.id
  LEFT JOIN tb_siswa s ON gt.email_siswa = s.email
  LEFT JOIN tb_mapel m ON gt.mapel = m.id
  $where_clause
  ORDER BY gt.created_at DESC
");
$stmt->execute($params);
$gaji_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total
$total_gaji = 0;
foreach($gaji_list as $gaji) {
  $total_gaji += $gaji['jumlah_gaji'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Gaji Tentor - Print</title>
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
  <h2>LAPORAN GAJI TENTOR</h2>
  <?php if (!empty($filter_tentor) || !empty($filter_bulan) || !empty($filter_status)): ?>
    <p style="text-align:center;margin:0 0 12px 0;font-size:10px;color:#666;">
      Filter: 
      <?php 
      $filter_info = [];
      if (!empty($filter_tentor)) {
        // Ambil nama tentor
        $stmt_tentor = $pdo->prepare("SELECT nama FROM tb_user WHERE id = ?");
        $stmt_tentor->execute([$filter_tentor]);
        $nama_tentor = $stmt_tentor->fetchColumn();
        $filter_info[] = "Tentor: " . ($nama_tentor ?: 'Tidak diketahui');
      }
      if (!empty($filter_bulan)) {
        $filter_info[] = "Bulan: " . date('F Y', strtotime($filter_bulan . '-01'));
      }
      if (!empty($filter_status)) {
        $filter_info[] = "Status: " . ucfirst($filter_status);
      }
      echo implode(' | ', $filter_info);
      ?>
    </p>
  <?php endif; ?>
  
  <div class="summary">
    <div>Total Gaji: <?= count($gaji_list) ?></div>
    <div>Total Pembayaran: <?= number_format($total_gaji, 0, ',', '.') ?></div>
  </div>
  
  <table>
    <thead>
      <tr>
        <th class="no">No</th>
        <th class="tanggal">Bulan</th>
        <th>Tentor</th>
        <th>Siswa</th>
        <th>Mapel</th>
        <th class="harga">Total Pembayaran</th>
        <th class="harga">Presentase</th>
        <th class="harga">Jumlah Gaji</th>
        <th class="status">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($gaji_list as $index => $gaji): ?>
      <tr>
        <td class="no"><?= $index + 1 ?></td>
        <td class="tanggal"><?= $gaji['bulan'] ?></td>
        <td><?= htmlspecialchars($gaji['nama_tentor'] ?: 'Tidak diketahui') ?></td>
        <td><?= htmlspecialchars($gaji['nama_siswa'] ?: $gaji['email_siswa']) ?></td>
        <td><?= htmlspecialchars($gaji['nama_mapel'] ?: 'Tidak diketahui') ?></td>
        <td class="harga"><?= number_format($gaji['total_pembayaran'], 0, ',', '.') ?></td>
        <td class="harga"><?= $gaji['presentase_gaji'] ?>%</td>
        <td class="harga"><?= number_format($gaji['jumlah_gaji'], 0, ',', '.') ?></td>
        <td class="status"><?= $gaji['status_pembayaran'] == 'dibayar' ? 'Dibayar' : 'Pending' ?></td>
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