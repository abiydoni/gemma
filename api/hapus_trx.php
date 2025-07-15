<?php
include 'db.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? '';
if(!$id) {
  echo json_encode(['status'=>'fail','msg'=>'ID tidak ditemukan!']);
  exit;
}
try {
  $stmt = $pdo->prepare('DELETE FROM tb_trx WHERE id = ?');
  $stmt->execute([$id]);
  echo json_encode(['status'=>'ok']);
} catch(Exception $e) {
  echo json_encode(['status'=>'error','msg'=>'Gagal menghapus: '.$e->getMessage()]);
} 