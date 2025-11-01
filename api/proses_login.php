<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['status'=>'error','msg'=>'Email dan password wajib diisi.']);
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM tb_user WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode(['status'=>'error','msg'=>'Email atau password salah.']);
    exit;
}

// Set session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['user_nama'] = $user['nama'];

// Sukses
echo json_encode(['status'=>'ok', 'role'=>$user['role']]); 