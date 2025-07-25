<?php
require_once '../../api/db.php';
header('Content-Type: application/json');
$id_siswa = $_POST['id_siswa'] ?? '';
if(!$id_siswa) {
  echo json_encode(['success'=>false, 'data'=>[]]);
  exit;
}
// Ambil email siswa
$stmt = $pdo->prepare("SELECT email FROM tb_siswa WHERE id = ?");
$stmt->execute([$id_siswa]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$row) {
  echo json_encode(['success'=>false, 'data'=>[]]);
  exit;
}
$email = $row['email'];
// Ambil mapel yang diambil siswa dari tb_trx dan tb_mapel
$stmt = $pdo->prepare("SELECT m.kode, m.nama FROM tb_trx t JOIN tb_mapel m ON t.mapel = m.kode WHERE t.email = ? GROUP BY m.kode, m.nama");
$stmt->execute([$email]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['success'=>true, 'data'=>$data]);