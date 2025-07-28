<?php
include '../../api/db.php';

// Ambil data profil
$stmt = $pdo->query("SELECT * FROM tb_profile LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil parameter filter
$filter_email = isset($_GET['email']) ? $_GET['email'] : '';
$filter_mapel = isset($_GET['mapel']) ? $_GET['mapel'] : '';
$filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

// Buat query dengan filter
$where_conditions = [];
$params = [];

if (!empty($filter_email)) {
    $where_conditions[] = "ps.email = ?";
    $params[] = $filter_email;
}

if (!empty($filter_mapel)) {
    $where_conditions[] = "ps.mapel = ?";
    $params[] = $filter_mapel;
}

if (!empty($filter_tanggal)) {
    $where_conditions[] = "ps.tanggal = ?";
    $params[] = $filter_tanggal;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Ambil data laporan perkembangan dengan filter
$stmt = $pdo->prepare("
  SELECT ps.*, s.nama as nama_siswa, m.nama as nama_mapel, jp.nama_penilaian as jenis_penilaian
  FROM tb_perkembangan_siswa ps
  LEFT JOIN tb_siswa s ON ps.email = s.email
  LEFT JOIN tb_mapel m ON ps.mapel = m.id
  LEFT JOIN tb_jenis_penilaian jp ON ps.id_jenis_penilaian = jp.id
  $where_clause
  ORDER BY ps.tanggal DESC
");
$stmt->execute($params);
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
        size: A4 landscape;
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
  <h2>LAPORAN PERKEMBANGAN SISWA</h2>
  <?php if (!empty($filter_email) || !empty($filter_mapel) || !empty($filter_tanggal)): ?>
    <p style="text-align:center;margin:0 0 12px 0;font-size:10px;color:#666;">
      Filter: 
      <?php 
      $filter_info = [];
      if (!empty($filter_email)) {
        $stmt = $pdo->prepare("SELECT nama FROM tb_siswa WHERE email = ?");
        $stmt->execute([$filter_email]);
        $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
        $filter_info[] = "Siswa: " . ($siswa['nama'] ?: $filter_email);
      }
      if (!empty($filter_mapel)) {
        $stmt = $pdo->prepare("SELECT nama FROM tb_mapel WHERE id = ?");
        $stmt->execute([$filter_mapel]);
        $mapel = $stmt->fetch(PDO::FETCH_ASSOC);
        $filter_info[] = "Mapel: " . $mapel['nama'];
      }
      if (!empty($filter_tanggal)) {
        $filter_info[] = "Tanggal: " . date('d/m/Y', strtotime($filter_tanggal));
      }
      echo implode(' | ', $filter_info);
      ?>
    </p>
  <?php endif; ?>
  
  <div class="summary">
    <div>Total Laporan: <?= count($laporan_list) ?></div>
    <div>Periode: <?= date('d/m/Y') ?></div>
  </div>
  
  <table>
    <thead>
      <tr>
        <th class="no">No</th>
        <th>Siswa</th>
        <th>Mapel</th>
        <th class="tanggal">Tanggal</th>
        <th>Tentor</th>
        <th class="nilai">Nilai Rata-rata</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      // Kelompokkan data berdasarkan email, mapel, tanggal, dan tentor
      $grouped_data = [];
      foreach($laporan_list as $laporan) {
        $key = $laporan['email'] . '_' . $laporan['mapel'] . '_' . $laporan['tanggal'] . '_' . $laporan['tentor'];
        if (!isset($grouped_data[$key])) {
          $grouped_data[$key] = [
            'nama_siswa' => $laporan['nama_siswa'],
            'email' => $laporan['email'],
            'nama_mapel' => $laporan['nama_mapel'],
            'tanggal' => $laporan['tanggal'],
            'tentor' => $laporan['tentor'],
            'nilai_total' => 0,
            'nilai_count' => 0
          ];
        }
        $grouped_data[$key]['nilai_total'] += $laporan['nilai'];
        $grouped_data[$key]['nilai_count']++;
      }
      
      $no = 1;
      foreach($grouped_data as $data): 
        $rata_nilai = $data['nilai_count'] > 0 ? round($data['nilai_total'] / $data['nilai_count'], 1) : 0;
      ?>
      <tr>
        <td class="no"><?= $no++ ?></td>
        <td>
          <div style="font-weight: bold;"><?= htmlspecialchars($data['nama_siswa']) ?></div>
          <div style="font-size: 10px; color: #666;"><?= htmlspecialchars($data['email']) ?></div>
        </td>
        <td><?= htmlspecialchars($data['nama_mapel']) ?></td>
        <td class="tanggal"><?= date('d/m/Y', strtotime($data['tanggal'])) ?></td>
        <td><?= htmlspecialchars($data['tentor']) ?></td>
        <td class="nilai">
          <span style="
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            <?= $rata_nilai >= 4 ? 'background-color: #dcfce7; color: #166534;' : ($rata_nilai >= 3 ? 'background-color: #fef3c7; color: #92400e;' : 'background-color: #fee2e2; color: #991b1b;') ?>
          "><?= $rata_nilai ?></span>
        </td>
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