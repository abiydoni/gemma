<?php
include '../../api/db.php';

// Ambil data profil
$stmt = $pdo->query("SELECT * FROM tb_profile LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile Lembaga - Print</title>
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
    .profile-info {
      margin-bottom: 20px;
    }
    .profile-item {
      margin-bottom: 10px;
      display: flex;
      border-bottom: 1px solid #eee;
      padding-bottom: 5px;
    }
    .profile-label {
      font-weight: bold;
      width: 150px;
      color: #333;
    }
    .profile-value {
      flex: 1;
      color: #666;
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
  <h2>PROFILE LEMBAGA</h2>
  
  <div class="profile-info">
    <div class="profile-item">
      <div class="profile-label">Nama Lembaga:</div>
      <div class="profile-value"><?= htmlspecialchars($profil['nama']) ?></div>
    </div>
    <div class="profile-item">
      <div class="profile-label">Alamat:</div>
      <div class="profile-value"><?= htmlspecialchars($profil['alamat']) ?></div>
    </div>
    <div class="profile-item">
      <div class="profile-label">Telepon:</div>
      <div class="profile-value"><?= htmlspecialchars($profil['wa']) ?></div>
    </div>
    <div class="profile-item">
      <div class="profile-label">Email:</div>
      <div class="profile-value"><?= htmlspecialchars($profil['email']) ?></div>
    </div>
    <div class="profile-item">
      <div class="profile-label">Website:</div>
      <div class="profile-value"><?= htmlspecialchars($profil['website'] ?? '-') ?></div>
    </div>
    <div class="profile-item">
      <div class="profile-label">Deskripsi:</div>
      <div class="profile-value"><?= htmlspecialchars($profil['deskripsi'] ?? '-') ?></div>
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