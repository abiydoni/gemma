<?php
include 'db.php';
header('Content-Type: application/json');

function clean($str) {
    return htmlspecialchars(trim($str));
}

$email = $_POST['email'] ?? '';
$paket = $_POST['paket'] ?? '';
$mapel = $_POST['mapel'] ?? '';
$harga = $_POST['harga'] ?? 0;
$hari = $_POST['hari'] ?? '';
$jam = $_POST['jam'] ?? '';
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';

if(!$email || !$paket || !$mapel || !$harga || !$hari || !$jam || !$tanggal_mulai) {
    echo json_encode(['status'=>'fail','msg'=>'Data wajib diisi lengkap!']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO tb_trx (email, paket, mapel, harga, bayar, hari, jam, mulai, status, tanggal) VALUES (?,?,?,?,?,?,?, ?,0,NOW())");
    $stmt->execute([$email, $paket, $mapel, $harga, 0, $hari, $jam, $tanggal_mulai]);
    echo json_encode(['status'=>'ok']);
} catch(Exception $e) {
    echo json_encode(['status'=>'error','msg'=>'Gagal menyimpan: '.$e->getMessage()]);
} 