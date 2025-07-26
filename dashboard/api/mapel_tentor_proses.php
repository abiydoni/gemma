<?php
header('Content-Type: application/json');
include '../../api/db.php';

$action = $_POST['action'] ?? '';

if ($action === 'list') {
    try {
        $stmt = $pdo->query('SELECT mt.*, m.nama as nama_mapel, u.nama as nama_tentor FROM tb_mapel_tentor mt LEFT JOIN tb_mapel m ON mt.mapel = m.kode LEFT JOIN tb_user u ON mt.id_tentor = u.id ORDER BY m.nama ASC');
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'ok', 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}

elseif ($action === 'save') {
    $mapel = $_POST['mapel'] ?? '';
    $tentor = $_POST['tentor'] ?? '';
    $id = $_POST['id'] ?? '';

    if (!$mapel || !$tentor) {
        echo json_encode(['status' => 'fail', 'msg' => 'Mapel dan tentor wajib diisi!']);
        exit;
    }

    try {
        if ($id) {
            // Update
            $stmt = $pdo->prepare('UPDATE tb_mapel_tentor SET mapel = ?, id_tentor = ? WHERE id = ?');
            $stmt->execute([$mapel, $tentor, $id]);
        } else {
            // Insert
            $stmt = $pdo->prepare('INSERT INTO tb_mapel_tentor (mapel, id_tentor) VALUES (?, ?)');
            $stmt->execute([$mapel, $tentor]);
        }

        echo json_encode(['status' => 'ok', 'msg' => 'Mapping berhasil disimpan!']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}

elseif ($action === 'detail') {
    $id = $_POST['id'] ?? '';
    
    if (!$id) {
        echo json_encode(['status' => 'fail', 'msg' => 'ID tidak ditemukan!']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM tb_mapel_tentor WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            echo json_encode(['status' => 'ok', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'fail', 'msg' => 'Data tidak ditemukan!']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}

elseif ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    
    if (!$id) {
        echo json_encode(['status' => 'fail', 'msg' => 'ID tidak ditemukan!']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM tb_mapel_tentor WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['status' => 'ok', 'msg' => 'Mapping berhasil dihapus!']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}

else {
    echo json_encode(['status' => 'fail', 'msg' => 'Action tidak valid!']);
}
?> 