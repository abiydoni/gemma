<?php
include '../../api/db.php';
// Ambil data profil
$stmt = $pdo->query("SELECT * FROM tb_profile LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);
// Ambil data jadwal
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$startBulan = DateTime::createFromFormat('Y-m', $bulan);
$start = strtotime($startBulan->format('Y-m-01'));
$end = strtotime(date('Y-m-t', $start));
$stmt = $pdo->prepare("
  SELECT tgl.tanggal, tgl.jam_trx, s.nama AS nama_siswa, m.nama AS nama_mapel, tr.paket
  FROM tb_trx_tanggal tgl
  JOIN tb_trx tr ON tgl.id_trx = tr.id
  LEFT JOIN tb_siswa s ON tr.email = s.email
  LEFT JOIN tb_mapel m ON tr.mapel = m.id
  WHERE tgl.tanggal BETWEEN ? AND ?
");
$stmt->execute([date('Y-m-01', $start), date('Y-m-t', $start)]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$tanggalList = [];
for($d=$start; $d<=strtotime(date('Y-m-t', $start)); $d+=86400) {
  $tanggalList[] = date('Y-m-d', $d);
}
$jamList = [];
for($h=9; $h<=20; $h++) {
  $jamList[] = sprintf('%02d:00', $h);
}
// Susun data jadwal per tanggal dan jam
$jadwalMap = [];
foreach($rows as $row) {
  $jadwalMap[$row['tanggal']][$row['jam_trx']][] = $row;
}

// Debug: Tampilkan struktur jadwalMap
$debugMap = [];
foreach($jadwalMap as $tanggal => $jamData) {
  foreach($jamData as $jam => $items) {
    $debugMap[] = "Map[$tanggal][$jam] = " . count($items) . " items";
  }
}

$totalJadwal = count($rows);

// Jika tidak ada data jadwal, tambahkan data dummy untuk testing
if ($totalJadwal == 0) {
  // Tambahkan beberapa jadwal dummy untuk testing
  $dummyDates = [date('Y-m-01'), date('Y-m-05'), date('Y-m-10'), date('Y-m-15'), date('Y-m-20')];
  $dummyTimes = ['09:00', '10:00', '14:00', '16:00'];
  
  foreach ($dummyDates as $date) {
    foreach ($dummyTimes as $time) {
      $jadwalMap[$date][$time][] = ['nama_siswa' => 'Test', 'nama_mapel' => 'Test'];
    }
  }
  $totalJadwal = count($dummyDates) * count($dummyTimes);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Print Jadwal Les</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 11px; color: #222; max-width: 297mm; margin: 0 auto; background: #fff; padding: 10px; }
    .kop { display: flex; align-items: center; gap: 16px; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 16px; max-width: 100%; }
    .kop img { height: 60px; }
    .kop .info { line-height: 1.2; }
    .kop .info .nama { font-size: 20px; font-weight: bold; }
    .kop .info .alamat { font-size: 14px; }
    .kop .info .kontak { font-size: 13px; }
    table { border-collapse: collapse; width: 100%; margin-top: 16px; max-width: 100%; }
    th, td { border: 1px solid #888; padding: 4px 6px; text-align: left; }
    th { background: #e0e7ef; font-size: 11px; }
    td { font-size: 10px; min-height: 20px; }
    .rotate { writing-mode: vertical-lr; transform: rotate(180deg); }
    .jadwal-block { 
      background-color: #000 !important; 
      height: 10px !important; 
      margin: 2px 0 !important; 
      border-radius: 2px !important;
      display: block !important;
      width: 100% !important;
      min-width: 8px !important;
      border: 1px solid #000 !important;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
      color-adjust: exact !important;
    }
    @media print {
      @page { size: A4 landscape; margin: 8mm 6mm 8mm 6mm; }
      body { width: 297mm; margin: 0 auto; background: #fff !important; }
      .kop, table { max-width: 100%; }
      table { font-size: 8px; }
      th, td { padding: 2px 3px; }
      * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }
      .jadwal-block { 
        height: 8px !important; 
        background-color: #000 !important;
        margin: 1px 0 !important;
        border-radius: 1px !important;
        display: block !important;
        width: 100% !important;
        min-width: 6px !important;
        border: 1px solid #000 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
      }
    }
  </style>
</head>
<body>
  <div class="kop">
    <?php include 'kop_surat.php'; ?>
  </div>
  <h2 style="text-align:center;margin:0 0 12px 0;">JADWAL LES BULAN <?= strtoupper(date('F Y', $start)) ?></h2>
  <div style="overflow-x:auto;">
  <table>
    <thead>
      <tr>
        <th>Jam</th>
        <?php foreach($tanggalList as $tgl): ?>
          <th class="rotate" style="min-width:12px;max-width:25px;padding:2px;"><?= date('j', strtotime($tgl)) ?><br><?= ['Min','Sen','Sel','Rab','Kam','Jum','Sab'][date('w', strtotime($tgl))] ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach($jamList as $jam): ?>
      <tr>
        <td style="font-weight:bold; text-align:center;"><?= $jam ?></td>
        <?php foreach($tanggalList as $tgl): ?>
          <td style="min-width:12px;max-width:25px;vertical-align:top;padding:2px;">
            <?php 
            $hasJadwal = isset($jadwalMap[$tgl][$jam]) && count($jadwalMap[$tgl][$jam]) > 0;
            if($hasJadwal): ?>
              <?php foreach($jadwalMap[$tgl][$jam] as $item): ?>
                <div class="jadwal-block"></div>
              <?php endforeach; ?>
            <?php endif; ?>
          </td>
        <?php endforeach; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  
  <!-- Detail Jadwal -->
  <div style="margin-top: 20px;">
    <h3 style="text-align:center;margin:0 0 12px 0;font-size:14px;font-weight:bold;">DETAIL JADWAL LES BULAN <?= strtoupper(date('F Y', $start)) ?></h3>
    
    <?php 
    $no = 1;
    $allJadwal = [];
    
    // Kumpulkan semua jadwal dari jadwalMap
    foreach($jadwalMap as $tanggal => $jamData) {
      foreach($jamData as $jam => $items) {
        foreach($items as $item) {
          $allJadwal[] = [
            'tanggal' => $tanggal,
            'jam' => $jam,
            'nama' => $item['nama_siswa'] ?? 'Test',
            'mapel' => $item['nama_mapel'] ?? 'Test'
          ];
        }
      }
    }
    
    // Urutkan berdasarkan tanggal dan jam
    usort($allJadwal, function($a, $b) {
      $dateCompare = strcmp($a['tanggal'], $b['tanggal']);
      if ($dateCompare === 0) {
        return strcmp($a['jam'], $b['jam']);
      }
      return $dateCompare;
    });
    
    // Bagi jadwal menjadi 2 kolom
    $totalJadwal = count($allJadwal);
    $kolom1 = array_slice($allJadwal, 0, ceil($totalJadwal / 2));
    $kolom2 = array_slice($allJadwal, ceil($totalJadwal / 2));
    ?>
    
    <div style="display: flex; gap: 20px;">
      <!-- Kolom 1 -->
      <div style="flex: 1;">
        <table style="width:100%; border-collapse: collapse; margin-top: 10px;">
          <thead>
            <tr style="background-color: #e0e7ef;">
              <th style="border: 1px solid #888; padding: 4px; text-align: center; font-size: 9px;">No</th>
              <th style="border: 1px solid #888; padding: 4px; text-align: center; font-size: 9px;">Tanggal</th>
              <th style="border: 1px solid #888; padding: 4px; text-align: center; font-size: 9px;">Jam</th>
              <th style="border: 1px solid #888; padding: 4px; text-align: center; font-size: 9px;">Nama Siswa</th>
              <th style="border: 1px solid #888; padding: 4px; text-align: center; font-size: 9px;">Mapel</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($kolom1 as $jadwal): ?>
              <tr>
                <td style="border: 1px solid #888; padding: 3px; text-align: center; font-size: 8px;"><?= $no++ ?></td>
                <td style="border: 1px solid #888; padding: 3px; text-align: center; font-size: 8px;">
                  <?php 
                    $tanggalObj = DateTime::createFromFormat('Y-m-d', $jadwal['tanggal']);
                    echo $tanggalObj ? $tanggalObj->format('d/m/Y') : $jadwal['tanggal'];
                  ?>
                </td>
                <td style="border: 1px solid #888; padding: 3px; text-align: center; font-size: 8px;"><?= $jadwal['jam'] ?></td>
                <td style="border: 1px solid #888; padding: 3px; text-align: left; font-size: 8px;"><?= htmlspecialchars($jadwal['nama']) ?></td>
                <td style="border: 1px solid #888; padding: 3px; text-align: left; font-size: 8px;"><?= htmlspecialchars($jadwal['mapel']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      
      <!-- Kolom 2 -->
      <div style="flex: 1;">
        <table style="width:100%; border-collapse: collapse; margin-top: 10px;">
          <thead>
            <tr style="background-color: #e0e7ef;">
              <th style="border: 1px solid #888; padding: 4px; text-align: center; font-size: 9px;">No</th>
              <th style="border: 1px solid #888; padding: 4px; text-align: center; font-size: 9px;">Tanggal</th>
              <th style="border: 1px solid #888; padding: 4px; text-align: center; font-size: 9px;">Jam</th>
              <th style="border: 1px solid #888; padding: 4px; text-align: center; font-size: 9px;">Nama Siswa</th>
              <th style="border: 1px solid #888; padding: 4px; text-align: center; font-size: 9px;">Mapel</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($kolom2 as $jadwal): ?>
              <tr>
                <td style="border: 1px solid #888; padding: 3px; text-align: center; font-size: 8px;"><?= $no++ ?></td>
                <td style="border: 1px solid #888; padding: 3px; text-align: center; font-size: 8px;">
                  <?php 
                    $tanggalObj = DateTime::createFromFormat('Y-m-d', $jadwal['tanggal']);
                    echo $tanggalObj ? $tanggalObj->format('d/m/Y') : $jadwal['tanggal'];
                  ?>
                </td>
                <td style="border: 1px solid #888; padding: 3px; text-align: center; font-size: 8px;"><?= $jadwal['jam'] ?></td>
                <td style="border: 1px solid #888; padding: 3px; text-align: left; font-size: 8px;"><?= htmlspecialchars($jadwal['nama']) ?></td>
                <td style="border: 1px solid #888; padding: 3px; text-align: left; font-size: 8px;"><?= htmlspecialchars($jadwal['mapel']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <script>
    window.onload = function() {
      window.print();
      window.onafterprint = function() { window.close(); }
    }
  </script>
</body>
</html> 