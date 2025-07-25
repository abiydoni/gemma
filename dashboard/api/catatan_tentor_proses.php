<?php
header('Content-Type: application/json');
include '../../api/db.php';
$action = $_POST['action'] ?? '';

if ($action == 'list') {
    $id_siswa = $_POST['id_siswa'] ?? 0;
    $kode_mapel = $_POST['kode_mapel'] ?? '';
    if ($id_siswa && $kode_mapel) {
        $stmt = $pdo->prepare('SELECT * FROM tb_catatan_tentor WHERE id_siswa = ? AND kode_mapel = ? ORDER BY tanggal ASC, id ASC');
        $stmt->execute([$id_siswa, $kode_mapel]);
    } else {
        // fallback lama jika filter tidak dikirim
        $id_trx = $_POST['id_trx'] ?? 0;
        $stmt = $pdo->prepare('SELECT * FROM tb_catatan_tentor WHERE id_trx = ? ORDER BY tanggal ASC, id ASC');
        $stmt->execute([$id_trx]);
    }
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success'=>true, 'data'=>$data]);
    exit;
}

if ($action == 'add') {
    $id_trx = $_POST['id_trx'] ?? 0;
    $id_siswa = $_POST['id_siswa'] ?? 0;
    $kode_mapel = $_POST['kode_mapel'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $catatan = $_POST['catatan'] ?? '';
    if (!$id_trx || !$id_siswa || !$kode_mapel || !$tanggal || !$catatan) {
        echo json_encode(['success'=>false, 'msg'=>'Field wajib diisi']); exit;
    }
    $stmt = $pdo->prepare('INSERT INTO tb_catatan_tentor (id_trx, id_siswa, kode_mapel, catatan, tanggal) VALUES (?, ?, ?, ?, ?)');
    $sukses = $stmt->execute([$id_trx, $id_siswa, $kode_mapel, $catatan, $tanggal]);
    echo json_encode(['success'=>$sukses]);
    exit;
}

if ($action == 'edit') {
    $id = $_POST['id'] ?? 0;
    $tanggal = $_POST['tanggal'] ?? '';
    $catatan = $_POST['catatan'] ?? '';
    if (!$id || !$tanggal || !$catatan) {
        echo json_encode(['success'=>false, 'msg'=>'Field wajib diisi']); exit;
    }
    $stmt = $pdo->prepare('UPDATE tb_catatan_tentor SET tanggal=?, catatan=? WHERE id=?');
    $sukses = $stmt->execute([$tanggal, $catatan, $id]);
    echo json_encode(['success'=>$sukses]);
    exit;
}

if ($action == 'delete') {
    $id = $_POST['id'] ?? 0;
    if (!$id) { echo json_encode(['success'=>false, 'msg'=>'ID tidak ditemukan']); exit; }
    $stmt = $pdo->prepare('DELETE FROM tb_catatan_tentor WHERE id=?');
    $sukses = $stmt->execute([$id]);
    echo json_encode(['success'=>$sukses]);
    exit;
}

echo json_encode(['success'=>false, 'msg'=>'Aksi tidak dikenali']);