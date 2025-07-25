<?php
header('Content-Type: application/json');
include '../../api/db.php';
$action = $_POST['action'] ?? '';

if ($action == 'list') {
    $id_trx = $_POST['id_trx'] ?? 0;
    $stmt = $pdo->prepare('SELECT * FROM tb_nilai_mapel WHERE id_trx = ? ORDER BY tanggal ASC, id ASC');
    $stmt->execute([$id_trx]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success'=>true, 'data'=>$data]);
    exit;
}

if ($action == 'add') {
    $id_trx = $_POST['id_trx'] ?? 0;
    $tanggal = $_POST['tanggal'] ?? '';
    $jenis = $_POST['jenis'] ?? '';
    $nilai = $_POST['nilai'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';
    if (!$id_trx || !$tanggal || $nilai === '') {
        echo json_encode(['success'=>false, 'msg'=>'Field wajib diisi']); exit;
    }
    $stmt = $pdo->prepare('INSERT INTO tb_nilai_mapel (id_trx, tanggal, jenis, nilai, keterangan) VALUES (?, ?, ?, ?, ?)');
    $sukses = $stmt->execute([$id_trx, $tanggal, $jenis, $nilai, $keterangan]);
    echo json_encode(['success'=>$sukses]);
    exit;
}

if ($action == 'edit') {
    $id = $_POST['id'] ?? 0;
    $tanggal = $_POST['tanggal'] ?? '';
    $jenis = $_POST['jenis'] ?? '';
    $nilai = $_POST['nilai'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';
    if (!$id || !$tanggal || $nilai === '') {
        echo json_encode(['success'=>false, 'msg'=>'Field wajib diisi']); exit;
    }
    $stmt = $pdo->prepare('UPDATE tb_nilai_mapel SET tanggal=?, jenis=?, nilai=?, keterangan=? WHERE id=?');
    $sukses = $stmt->execute([$tanggal, $jenis, $nilai, $keterangan, $id]);
    echo json_encode(['success'=>$sukses]);
    exit;
}

if ($action == 'delete') {
    $id = $_POST['id'] ?? 0;
    if (!$id) { echo json_encode(['success'=>false, 'msg'=>'ID tidak ditemukan']); exit; }
    $stmt = $pdo->prepare('DELETE FROM tb_nilai_mapel WHERE id=?');
    $sukses = $stmt->execute([$id]);
    echo json_encode(['success'=>$sukses]);
    exit;
}

echo json_encode(['success'=>false, 'msg'=>'Aksi tidak dikenali']); 