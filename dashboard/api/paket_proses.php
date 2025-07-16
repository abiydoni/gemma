<?php
include '../../api/db.php';
header('Content-Type: application/json');
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action == 'list') {
  $data = [];
  $stmt = $pdo->query('SELECT * FROM tb_paket ORDER BY kode ASC');
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
  }
  echo json_encode(['success'=>true, 'data'=>$data]);
  exit;
}

if ($action == 'add') {
  $kode = $_POST['Kode'] ?? '';
  $nama = $_POST['nama'] ?? '';
  $keterangan = $_POST['keterangan'] ?? '';
  $jenjang = $_POST['jenjang'] ?? '';
  $harga = $_POST['harga'] ?? 0;
  $status = $_POST['status'] ?? 1;
  if ($kode && $nama) {
    $stmt = $pdo->prepare('INSERT INTO tb_paket (Kode, nama, keterangan, jenjang, harga, status) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$kode, $nama, $keterangan, $jenjang, $harga, $status]);
    echo json_encode(['success'=>true]);
  } else {
    echo json_encode(['success'=>false, 'msg'=>'Data tidak lengkap']);
  }
  exit;
}

if ($action == 'edit') {
  $id = $_POST['id'] ?? 0;
  // Jika hanya update status
  if (isset($_POST['status']) && !isset($_POST['Kode'])) {
    $status = $_POST['status'];
    if ($id) {
      $stmt = $pdo->prepare('UPDATE tb_paket SET status=? WHERE id=?');
      $stmt->execute([$status, $id]);
      echo json_encode(['success'=>true]);
    } else {
      echo json_encode(['success'=>false, 'msg'=>'ID tidak ditemukan']);
    }
    exit;
  }
  $kode = $_POST['Kode'] ?? '';
  $nama = $_POST['nama'] ?? '';
  $keterangan = $_POST['keterangan'] ?? '';
  $jenjang = $_POST['jenjang'] ?? '';
  $harga = $_POST['harga'] ?? 0;
  $status = $_POST['status'] ?? 1;
  if ($id && $kode && $nama) {
    $stmt = $pdo->prepare('UPDATE tb_paket SET Kode=?, nama=?, keterangan=?, jenjang=?, harga=?, status=? WHERE id=?');
    $stmt->execute([$kode, $nama, $keterangan, $jenjang, $harga, $status, $id]);
    echo json_encode(['success'=>true]);
  } else {
    echo json_encode(['success'=>false, 'msg'=>'Data tidak lengkap']);
  }
  exit;
}

if ($action == 'delete') {
  $id = $_POST['id'] ?? 0;
  if ($id) {
    $stmt = $pdo->prepare('DELETE FROM tb_paket WHERE id=?');
    $stmt->execute([$id]);
    echo json_encode(['success'=>true]);
  } else {
    echo json_encode(['success'=>false, 'msg'=>'ID tidak ditemukan']);
  }
  exit;
}

echo json_encode(['success'=>false, 'msg'=>'Aksi tidak valid']); 