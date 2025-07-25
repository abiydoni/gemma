<?php
header('Content-Type: application/json');
include '../../api/db.php';

$action = $_POST['action'] ?? '';

if ($action == 'list') {
    $stmt = $pdo->query('SELECT id, email, nama, hp, role FROM tb_user ORDER BY id DESC');
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success'=>true, 'data'=>$data]);
    exit;
}

if ($action == 'add') {
    $email = $_POST['email'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $hp = $_POST['hp'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';
    if (!$email || !$nama || !$password || !$hp) {
        echo json_encode(['success'=>false, 'msg'=>'Semua field wajib diisi']); exit;
    }
    $cek = $pdo->prepare('SELECT id FROM tb_user WHERE email=?');
    $cek->execute([$email]);
    if ($cek->fetch()) {
        echo json_encode(['success'=>false, 'msg'=>'Email sudah terdaftar']); exit;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO tb_user (email, nama, hp, role, password) VALUES (?, ?, ?, ?, ?)');
    $sukses = $stmt->execute([$email, $nama, $hp, $role, $hash]);
    echo json_encode(['success'=>$sukses]);
    exit;
}

if ($action == 'edit') {
    $id = $_POST['id'] ?? '';
    $email = $_POST['email'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $hp = $_POST['hp'] ?? '';
    $role = $_POST['role'] ?? 'user';
    if (!$id || !$email || !$nama || !$hp) {
        echo json_encode(['success'=>false, 'msg'=>'Semua field wajib diisi']); exit;
    }
    $stmt = $pdo->prepare('UPDATE tb_user SET nama=?, hp=?, role=? WHERE id=?');
    $sukses = $stmt->execute([$nama, $hp, $role, $id]);
    echo json_encode(['success'=>$sukses]);
    exit;
}

if ($action == 'delete') {
    $id = $_POST['id'] ?? '';
    if (!$id) { echo json_encode(['success'=>false, 'msg'=>'ID tidak ditemukan']); exit; }
    $stmt = $pdo->prepare('DELETE FROM tb_user WHERE id=?');
    $sukses = $stmt->execute([$id]);
    echo json_encode(['success'=>$sukses]);
    exit;
}

if ($action == 'change_password') {
    $id = $_POST['id'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!$id || !$password) { echo json_encode(['success'=>false, 'msg'=>'ID dan password wajib diisi']); exit; }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE tb_user SET password=? WHERE id=?');
    $sukses = $stmt->execute([$hash, $id]);
    echo json_encode(['success'=>$sukses]);
    exit;
}

echo json_encode(['success'=>false, 'msg'=>'Aksi tidak dikenali']); 