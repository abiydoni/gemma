<?php
include '../../api/db.php';

// Ambil data profil
$stmt = $pdo->query("SELECT * FROM tb_profile LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil data gaji tentor
$stmt = $pdo->query("
  SELECT g.*, u.nama as nama_tentor, s.nama as nama_siswa, m.nama as nama_mapel
  FROM tb_gaji g
  LEFT JOIN tb_user u ON g.id_tentor = u.id
  LEFT JOIN tb_siswa s ON g.id_siswa = s.id
  LEFT JOIN tb_mapel m ON g.id_mapel = m.id
  ORDER BY g.tanggal DESC
");
$gaji_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total
$total_gaji = 0;
foreach($gaji_list as $gaji) {
  $total_gaji += $gaji['total'];
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
    <?php include 'kop_surat.php'; ?>
  </div>
  <h2>LAPORAN GAJI TENTOR</h2>
  
  <div class="summary">
    <div>Total Gaji: <?= count($gaji_list) ?></div>
    <div>Total Pembayaran: Rp <?= number_format($total_gaji, 0, ',', '.') ?></div>
  </div>
  
  <table>
    <thead>
      <tr>
        <th class="no">No</th>
        <th class="tanggal">Tanggal</th>
        <th>Tentor</th>
        <th>Siswa</th>
        <th>Mapel</th>
        <th class="harga">Jumlah Les</th>
        <th class="harga">Gaji/Les</th>
        <th class="harga">Total</th>
        <th class="status">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($gaji_list as $index => $gaji): ?>
      <tr>
        <td class="no"><?= $index + 1 ?></td>
        <td class="tanggal"><?= date('d/m/Y', strtotime($gaji['tanggal'])) ?></td>
        <td><?= htmlspecialchars($gaji['nama_tentor']) ?></td>
        <td><?= htmlspecialchars($gaji['nama_siswa']) ?></td>
        <td><?= htmlspecialchars($gaji['nama_mapel']) ?></td>
        <td class="harga"><?= $gaji['jumlah_les'] ?></td>
        <td class="harga">Rp <?= number_format($gaji['gaji_per_les'], 0, ',', '.') ?></td>
        <td class="harga">Rp <?= number_format($gaji['total'], 0, ',', '.') ?></td>
        <td class="status"><?= $gaji['status'] == 1 ? 'Dibayar' : 'Belum Dibayar' ?></td>
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