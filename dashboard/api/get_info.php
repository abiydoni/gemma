<?php
include '../../api/db.php';
header('Content-Type: application/json');
$total_siswa = 0;
try {
  $stmt = $pdo->query('SELECT COUNT(email) as jumlah FROM tb_siswa');
  $row = $stmt->fetch();
  $total_siswa = $row ? $row['jumlah'] : 0;
} catch(Exception $e) {}
echo json_encode(['total_siswa' => $total_siswa]); 