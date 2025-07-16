<?php
header('Content-Type: application/json');
include '../../api/db.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
$keterangan = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : '';
$ikon = isset($_POST['ikon']) ? trim($_POST['ikon']) : '';

if ($id && $nama && $keterangan && $ikon) {
    $stmt = $conn->prepare("UPDATE tb_fasilitas SET nama=?, keterangan=?, ikon=? WHERE id=?");
    $stmt->bind_param('sssi', $nama, $keterangan, $ikon, $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update data.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
}
$conn->close(); 