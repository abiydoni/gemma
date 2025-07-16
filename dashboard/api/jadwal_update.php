<?php
header('Content-Type: application/json');
include '../../api/db.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$hari = isset($_POST['hari']) ? trim($_POST['hari']) : '';
$buka = isset($_POST['buka']) ? trim($_POST['buka']) : '';
$tutup = isset($_POST['tutup']) ? trim($_POST['tutup']) : '';

if ($id && $hari && $buka && $tutup) {
    $stmt = $pdo->prepare("UPDATE tb_jadwal SET hari=?, buka=?, tutup=? WHERE id=?");
    $stmt->execute([$hari, $buka, $tutup, $id]);
    if ($stmt->rowCount()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
} 