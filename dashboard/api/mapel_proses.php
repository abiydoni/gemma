<?php
include '../../api/db.php';
header('Content-Type: application/json');
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action == 'list') {
  $data = [];
  $stmt = $pdo->query('SELECT id, kode, nama, keterangan, status FROM tb_mapel ORDER BY nama ASC');
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
  }
  echo json_encode(['success'=>true, 'data'=>$data]);
  exit;
}

if ($action == 'add') {
  $kode = $_POST['kode'] ?? '';
  $nama = $_POST['nama'] ?? '';
  $keterangan = $_POST['keterangan'] ?? '';
  $status = $_POST['status'] ?? 1;
  if ($kode && $nama) {
    $stmt = $pdo->prepare('INSERT INTO tb_mapel (kode, nama, keterangan, status) VALUES (?, ?, ?, ?)');
    $stmt->execute([$kode, $nama, $keterangan, $status]);
    echo json_encode(['success'=>true]);
  } else {
    echo json_encode(['success'=>false, 'msg'=>'Data tidak lengkap']);
  }
  exit;
}

if ($action == 'edit') {
  $id = $_POST['id'] ?? 0;
  $kode = $_POST['kode'] ?? '';
  $nama = $_POST['nama'] ?? '';
  $keterangan = $_POST['keterangan'] ?? '';
  $status = $_POST['status'] ?? 1;
  if ($id && $kode && $nama) {
    $stmt = $pdo->prepare('UPDATE tb_mapel SET kode=?, nama=?, keterangan=?, status=? WHERE id=?');
    $stmt->execute([$kode, $nama, $keterangan, $status, $id]);
    echo json_encode(['success'=>true]);
  } else {
    echo json_encode(['success'=>false, 'msg'=>'Data tidak lengkap']);
  }
  exit;
}

if ($action == 'delete') {
  $id = $_POST['id'] ?? 0;
  if ($id) {
    $stmt = $pdo->prepare('DELETE FROM tb_mapel WHERE id=?');
    $stmt->execute([$id]);
    echo json_encode(['success'=>true]);
  } else {
    echo json_encode(['success'=>false, 'msg'=>'ID tidak ditemukan']);
  }
  exit;
}

if ($action == 'update_status') {
  $id = $_POST['id'] ?? 0;
  $status = $_POST['status'] ?? 1;
  if ($id) {
    $stmt = $pdo->prepare('UPDATE tb_mapel SET status=? WHERE id=?');
    $stmt->execute([$status, $id]);
    echo json_encode(['success'=>true]);
  } else {
    echo json_encode(['success'=>false, 'msg'=>'ID tidak ditemukan']);
  }
  exit;
}

echo json_encode(['success'=>false, 'msg'=>'Aksi tidak valid']); 