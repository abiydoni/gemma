<?php
include '../../api/db.php';

// Ambil data profil
$stmt = $pdo->query("SELECT * FROM tb_profile LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil ID dari parameter
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Ambil data detail laporan berdasarkan ID
$stmt = $pdo->prepare("
  SELECT ps.*, s.nama as nama_siswa, m.nama as nama_mapel, jp.nama_penilaian as jenis_penilaian
  FROM tb_perkembangan_siswa ps
  LEFT JOIN tb_siswa s ON ps.email = s.email
  LEFT JOIN tb_mapel m ON ps.mapel = m.id
  LEFT JOIN tb_jenis_penilaian jp ON ps.id_jenis_penilaian = jp.id
  WHERE ps.id = ?
");
$stmt->execute([$id]);
$detail = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$detail) {
    die("Data tidak ditemukan");
}

// Ambil semua data penilaian untuk siswa, mapel, tanggal, dan tentor yang sama
$stmt = $pdo->prepare("
  SELECT ps.*, jp.nama_penilaian as jenis_penilaian
  FROM tb_perkembangan_siswa ps
  LEFT JOIN tb_jenis_penilaian jp ON ps.id_jenis_penilaian = jp.id
  WHERE ps.email = ? AND ps.mapel = ? AND ps.tanggal = ? AND ps.tentor = ?
  ORDER BY jp.urutan ASC
");
$stmt->execute([$detail['email'], $detail['mapel'], $detail['tanggal'], $detail['tentor']]);
$nilai_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung rata-rata nilai
$total_nilai = 0;
$count_nilai = 0;
foreach ($nilai_list as $nilai) {
    $total_nilai += $nilai['nilai'];
    $count_nilai++;
}
$rata_nilai = $count_nilai > 0 ? round($total_nilai / $count_nilai, 1) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Laporan Perkembangan - Print</title>
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
      color: #1e40af;
    }
    .info-cards {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      margin-bottom: 20px;
    }
    .info-card {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 12px;
      background-color: #f9fafb;
    }
    .info-card.siswa { background-color: #eff6ff; }
    .info-card.mapel { background-color: #f0fdf4; }
    .info-card.tanggal { background-color: #fffbeb; }
    .info-card.tentor { background-color: #faf5ff; }
    .info-card .label {
      font-size: 10px;
      color: #6b7280;
      font-weight: bold;
      margin-bottom: 4px;
    }
    .info-card .value {
      font-size: 14px;
      font-weight: bold;
      color: #1f2937;
    }
    .penilaian-section {
      margin-top: 20px;
    }
    .penilaian-title {
      font-size: 14px;
      font-weight: bold;
      color: #1f2937;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      background-color: #f9fafb;
      border-radius: 8px;
      overflow: hidden;
    }
    th, td {
      border: 1px solid #e5e7eb;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #f3f4f6;
      font-weight: bold;
      font-size: 11px;
    }
    td {
      font-size: 11px;
    }
    .nilai-cell {
      font-weight: bold;
      color: #1d4ed8;
      display: flex;
      align-items: center;
      gap: 3px;
    }
    .rata-rata-row {
      background-color: #f3f4f6;
      font-weight: bold;
    }
    .rata-rata-value {
      color: #1d4ed8;
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
  
  <h2>DETAIL LAPORAN PERKEMBANGAN SISWA</h2>
  
  <div class="info-cards">
    <div class="info-card siswa">
      <div class="label">Siswa</div>
      <div class="value"><?= htmlspecialchars($detail['nama_siswa'] ?: $detail['email']) ?></div>
    </div>
    <div class="info-card mapel">
      <div class="label">Mapel</div>
      <div class="value"><?= htmlspecialchars($detail['nama_mapel']) ?></div>
    </div>
    <div class="info-card tanggal">
      <div class="label">Tanggal</div>
      <div class="value"><?= date('d/m/Y', strtotime($detail['tanggal'])) ?></div>
    </div>
    <div class="info-card tentor">
      <div class="label">Tentor</div>
      <div class="value"><?= htmlspecialchars($detail['tentor']) ?></div>
    </div>
  </div>
  
  <div class="penilaian-section">
    <div class="penilaian-title">
      <span style="color: #f59e0b;">★</span> Penilaian
    </div>
    <table>
      <thead>
        <tr>
          <th style="width: 50px;">No</th>
          <th>Jenis Penilaian</th>
          <th style="width: 80px;">Nilai</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($nilai_list as $index => $nilai): ?>
        <tr>
          <td><?= $index + 1 ?></td>
          <td style="font-weight: bold; color: #374151;"><?= htmlspecialchars($nilai['jenis_penilaian']) ?></td>
          <td class="nilai-cell">
            <span style="color: #f59e0b;">★</span> <?= $nilai['nilai'] ?>
          </td>
          <td><?= htmlspecialchars($nilai['keterangan'] ?: '-') ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="rata-rata-row">
          <td colspan="2" style="font-weight: bold;">Rata-rata</td>
          <td class="rata-rata-value"><?= $rata_nilai ?></td>
          <td>-</td>
        </tr>
      </tbody>
    </table>
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