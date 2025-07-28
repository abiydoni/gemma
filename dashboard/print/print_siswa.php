<?php
include '../../api/db.php';
// Ambil data profil
$stmt = $pdo->query("SELECT * FROM tb_profile LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);
// Ambil data siswa
$siswa = $pdo->query("SELECT * FROM tb_siswa ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Print Data Siswa</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #222; max-width: 210mm; margin: 0 auto; background: #fff; padding: 12px; }
    .kop { display: flex; align-items: center; gap: 16px; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 16px; max-width: 100%; }
    .kop img { height: 60px; }
    .kop .info { line-height: 1.2; }
    .kop .info .nama { font-size: 20px; font-weight: bold; }
    .kop .info .alamat { font-size: 14px; }
    .kop .info .kontak { font-size: 13px; }
    table { border-collapse: collapse; width: 100%; margin-top: 16px; max-width: 100%; }
    th, td { border: 1px solid #888; padding: 6px 8px; text-align: left; }
    th { background: #e0e7ef; }
    @media print {
      @page { size: A4 portrait; margin: 18mm 12mm 18mm 12mm; }
      body { width: 210mm; margin: 0 auto; background: #fff !important; }
      .kop, table { max-width: 100%; }
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
  <h2 style="text-align:center;margin:0 0 12px 0;">DAFTAR SISWA</h2>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Gender</th>
        <th>Tgl Lahir</th>
        <th>Ortu</th>
        <th>HP Ortu</th>
        <th>Alamat</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($siswa as $i => $s): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><?= htmlspecialchars($s['nama']) ?></td>
        <td><?= htmlspecialchars($s['gender']) ?></td>
        <td><?= htmlspecialchars($s['tgl_lahir']) ?></td>
        <td><?= htmlspecialchars($s['ortu']) ?></td>
        <td><?= htmlspecialchars($s['hp_ortu']) ?></td>
        <td><?= htmlspecialchars($s['alamat']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <!-- Hapus tombol print manual -->
  <script>
    window.onload = function() {
      window.print();
      window.onafterprint = function() { window.close(); }
    }
  </script>
</body>
</html> 