<?php
include 'db.php';
header('Content-Type: application/json');
$email = $_POST['email'] ?? '';
if (!$email) {
  echo json_encode(['status'=>'not_found']);
  exit;
}
$stmt = $pdo->prepare('SELECT id FROM tb_siswa WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
if ($stmt->fetch()) {
  echo json_encode(['status'=>'found']);
} else {
  echo json_encode(['status'=>'not_found']);
} 