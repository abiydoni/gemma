<?php
header('Content-Type: application/json');
include '../../api/db.php';
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action) {
  case 'list':
    $stmt = $pdo->query('SELECT * FROM tb_jenjang ORDER BY id ASC');
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success'=>true, 'data'=>$data]);
    break;
  case 'add':
    $nama = $_POST['nama'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';
    $stmt = $pdo->prepare('INSERT INTO tb_jenjang (nama, keterangan) VALUES (?, ?)');
    $ok = $stmt->execute([$nama, $keterangan]);
    echo json_encode(['success'=>$ok]);
    break;
  case 'edit':
    $id = $_POST['id'] ?? 0;
    $nama = $_POST['nama'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';
    $stmt = $pdo->prepare('UPDATE tb_jenjang SET nama=?, keterangan=? WHERE id=?');
    $ok = $stmt->execute([$nama, $keterangan, $id]);
    echo json_encode(['success'=>$ok]);
    break;
  case 'delete':
    $id = $_POST['id'] ?? 0;
    $stmt = $pdo->prepare('DELETE FROM tb_jenjang WHERE id=?');
    $ok = $stmt->execute([$id]);
    echo json_encode(['success'=>$ok]);
    break;
  default:
    echo json_encode(['success'=>false, 'msg'=>'Aksi tidak dikenali']);
} 