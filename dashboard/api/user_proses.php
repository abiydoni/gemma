<?php
include '../../api/db.php';
header('Content-Type: application/json');
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action == 'list') {
  session_start();
  $my_role = $_SESSION['user_role'] ?? 'user';
  $my_id = $_SESSION['user_id'] ?? 0;
  $role_level = [
    'user' => 1,
    'tentor' => 2,
    'admin' => 3,
    's_admin' => 4
  ];
  $my_level = $role_level[$my_role] ?? 1;
  $data = [];
  $stmt = $pdo->query('SELECT id, email, nama, role FROM tb_user ORDER BY nama ASC');
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ((($role_level[$row['role']] ?? 0) <= $my_level) || $row['id'] == $my_id) {
      $data[] = $row;
    }
  }
  echo json_encode(['success'=>true, 'data'=>$data]);
  exit;
}

if ($action == 'add') {
  $email = $_POST['email'] ?? '';
  $nama = $_POST['nama'] ?? '';
  $role = $_POST['role'] ?? 'user';
  $password = $_POST['password'] ?? '';
  if ($email && $nama && $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO tb_user (email, nama, password, role) VALUES (?, ?, ?, ?)');
    $stmt->execute([$email, $nama, $hash, $role]);
    echo json_encode(['success'=>true]);
  } else {
    echo json_encode(['success'=>false, 'msg'=>'Data tidak lengkap']);
  }
  exit;
}

if ($action == 'edit') {
  $id = $_POST['id'] ?? 0;
  $email = $_POST['email'] ?? '';
  $nama = $_POST['nama'] ?? '';
  $role = $_POST['role'] ?? 'user';
  if ($id && $email && $nama) {
    $stmt = $pdo->prepare('UPDATE tb_user SET email=?, nama=?, role=? WHERE id=?');
    $stmt->execute([$email, $nama, $role, $id]);
    echo json_encode(['success'=>true]);
  } else {
    echo json_encode(['success'=>false, 'msg'=>'Data tidak lengkap']);
  }
  exit;
}

if ($action == 'delete') {
  $id = $_POST['id'] ?? 0;
  if ($id) {
    $stmt = $pdo->prepare('DELETE FROM tb_user WHERE id=?');
    $stmt->execute([$id]);
    echo json_encode(['success'=>true]);
  } else {
    echo json_encode(['success'=>false, 'msg'=>'ID tidak ditemukan']);
  }
  exit;
}

if ($action == 'change_password') {
  $id = $_POST['id'] ?? 0;
  $password = $_POST['password'] ?? '';
  if ($id && $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE tb_user SET password=? WHERE id=?');
    $stmt->execute([$hash, $id]);
    echo json_encode(['success'=>true]);
  } else {
    echo json_encode(['success'=>false, 'msg'=>'Data tidak lengkap']);
  }
  exit;
}

echo json_encode(['success'=>false, 'msg'=>'Aksi tidak valid']); 