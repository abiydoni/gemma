<?php
include '../../api/db.php';
header('Content-Type: application/json');
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action == 'get') {
  $stmt = $pdo->prepare('SELECT * FROM tb_profile WHERE id=1');
  $stmt->execute();
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
  echo json_encode(['success'=>true, 'data'=>$data]);
  exit;
}

if ($action == 'update') {
  $id = 1;
  $nama = $_POST['nama'] ?? '';
  $keterangan = $_POST['keterangan'] ?? '';
  $alamat = $_POST['alamat'] ?? '';
  $email = $_POST['email'] ?? '';
  $wa = $_POST['wa'] ?? '';
  $ig = $_POST['ig'] ?? '';
  $logo1 = '';
  $logo2 = '';
  // Handle upload logo1
  if(isset($_FILES['logo1']) && $_FILES['logo1']['tmp_name']) {
    $ext = pathinfo($_FILES['logo1']['name'], PATHINFO_EXTENSION);
    $logo1 = 'logo1_'.time().'.'.$ext;
    move_uploaded_file($_FILES['logo1']['tmp_name'], '../../assets/img/'.$logo1);
  }
  // Handle upload logo2
  if(isset($_FILES['logo2']) && $_FILES['logo2']['tmp_name']) {
    $ext = pathinfo($_FILES['logo2']['name'], PATHINFO_EXTENSION);
    $logo2 = 'logo2_'.time().'.'.$ext;
    move_uploaded_file($_FILES['logo2']['tmp_name'], '../../assets/img/'.$logo2);
  }
  // Ambil logo lama jika tidak upload baru
  $stmt = $pdo->prepare('SELECT logo1, logo2 FROM tb_profile WHERE id=1');
  $stmt->execute();
  $old = $stmt->fetch(PDO::FETCH_ASSOC);
  if(!$logo1) $logo1 = $old['logo1'];
  if(!$logo2) $logo2 = $old['logo2'];
  $stmt = $pdo->prepare('UPDATE tb_profile SET nama=?, keterangan=?, alamat=?, email=?, wa=?, ig=?, logo1=?, logo2=? WHERE id=?');
  $stmt->execute([$nama, $keterangan, $alamat, $email, $wa, $ig, $logo1, $logo2, $id]);
  echo json_encode(['success'=>true]);
  exit;
}

echo json_encode(['success'=>false, 'msg'=>'Aksi tidak valid']); 