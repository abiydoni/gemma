<?php
header('Content-Type: application/json');
include '../../api/db.php';
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action) {
  case 'list':
    $stmt = $pdo->query('SELECT * FROM tb_kondisi ORDER BY id ASC');
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success'=>true, 'data'=>$data]);
    break;
  case 'add':
    $kode = $_POST['kode'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $stmt = $pdo->prepare('INSERT INTO tb_kondisi (kode, nama) VALUES (?, ?)');
    $ok = $stmt->execute([$kode, $nama]);
    echo json_encode(['success'=>$ok]);
    break;
  case 'edit':
    $id = $_POST['id'] ?? 0;
    $kode = $_POST['kode'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $stmt = $pdo->prepare('UPDATE tb_kondisi SET kode=?, nama=? WHERE id=?');
    $ok = $stmt->execute([$kode, $nama, $id]);
    echo json_encode(['success'=>$ok]);
    break;
  case 'delete':
    $id = $_POST['id'] ?? 0;
    $stmt = $pdo->prepare('DELETE FROM tb_kondisi WHERE id=?');
    $ok = $stmt->execute([$id]);
    echo json_encode(['success'=>$ok]);
    break;
  default:
    echo json_encode(['success'=>false, 'msg'=>'Aksi tidak dikenali']);
} 