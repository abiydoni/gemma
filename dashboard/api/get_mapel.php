<?php
header('Content-Type: application/json');
include '../../api/db.php';

$email = $_GET['email'] ?? '';
$mapel = [];

if ($email) {
    try {
        $stmt = $pdo->prepare('SELECT DISTINCT t.mapel as kode, m.id, m.nama FROM tb_trx t LEFT JOIN tb_mapel m ON m.id = t.mapel WHERE t.email = ?');
        $stmt->execute([$email]);
        $mapel = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Jika nama mapel tidak ditemukan, gunakan kode mapel
        foreach($mapel as &$item) {
            if(empty($item['nama'])) {
                $item['nama'] = $item['kode'];
            }
            // Pastikan ada ID, jika tidak ada gunakan kode sebagai ID
            if(empty($item['id'])) {
                $item['id'] = $item['kode'];
            }
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

echo json_encode($mapel);
?> 