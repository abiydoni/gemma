<?php
header('Content-Type: application/json');
include 'db.php';

$mapel = $_POST['mapel'] ?? '';

if (!$mapel) {
    echo json_encode(['status' => 'fail', 'msg' => 'Mapel wajib diisi!']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT u.id, u.nama FROM tb_mapel_tentor mt 
                           LEFT JOIN tb_user u ON mt.id_tentor = u.id 
                           WHERE mt.mapel = ? AND u.role = 'tentor'");
    $stmt->execute([$mapel]);
    $tentor = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($tentor) {
        echo json_encode(['status' => 'ok', 'data' => $tentor]);
    } else {
        echo json_encode(['status' => 'fail', 'msg' => 'Tidak ada tentor untuk mapel ini!']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
?> 