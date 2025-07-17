<?php
header('Content-Type: application/json');
include '../../api/db.php';
$hari = isset($_GET['hari']) ? $_GET['hari'] : '';
if ($hari) {
    $stmt = $pdo->prepare('SELECT buka, tutup FROM tb_jadwal WHERE LOWER(hari) = LOWER(?) LIMIT 1');
    $stmt->execute([$hari]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($data) {
        echo json_encode($data);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
} 