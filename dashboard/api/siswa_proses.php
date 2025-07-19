<?php
include '../../api/db.php';
header('Content-Type: application/json');
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'list') {
  try {
    $stmt = $pdo->query('SELECT id, nama, gender, tgl_lahir, ortu, hp_ortu, alamat, email, foto FROM tb_siswa ORDER BY id DESC');
    $data = $stmt->fetchAll();
    echo json_encode($data);
  } catch(Exception $e) {
    echo json_encode([]);
  }
  exit;
}

if ($action === 'add' || $action === 'edit') {
  $id = $_POST['id'] ?? '';
  $nama = $_POST['nama'] ?? '';
  $gender = $_POST['gender'] ?? '';
  $tgl_lahir = $_POST['tgl_lahir'] ?? '';
  $ortu = $_POST['ortu'] ?? '';
  $hp_ortu = $_POST['hp_ortu'] ?? '';
  $alamat = $_POST['alamat'] ?? '';
  $email = $_POST['email'] ?? '';
  $foto = '';
  if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nama_file = 'siswa_' . time() . '_' . rand(1000,9999) . '.' . $ext;
    $tujuan = '../../assets/img/profile/' . $nama_file;
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $tujuan)) {
      $foto = $nama_file;
    }
  }
  if(!$nama || !$gender || !$tgl_lahir || !$ortu || !$hp_ortu || !$alamat || !$email) {
    echo json_encode(['status'=>'fail','msg'=>'Data wajib diisi lengkap!']);
    exit;
  }
  try {
    if($action === 'edit' && $id) {
      $sql = 'UPDATE tb_siswa SET nama=?, gender=?, tgl_lahir=?, ortu=?, hp_ortu=?, alamat=?, email=?'.($foto?', foto=?':'').' WHERE id=?';
      $params = [$nama, $gender, $tgl_lahir, $ortu, $hp_ortu, $alamat, $email];
      if($foto) $params[] = $foto;
      $params[] = $id;
      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);
    } else {
      $sql = 'INSERT INTO tb_siswa (nama, gender, tgl_lahir, ortu, hp_ortu, alamat, email, foto) VALUES (?,?,?,?,?,?,?,?)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$nama, $gender, $tgl_lahir, $ortu, $hp_ortu, $alamat, $email, $foto]);
    }
    echo json_encode(['status'=>'ok']);
  } catch(Exception $e) {
    echo json_encode(['status'=>'error','msg'=>'Gagal simpan: '.$e->getMessage()]);
  }
  exit;
}

if ($action === 'delete') {
  $id = $_POST['id'] ?? '';
  if(!$id) {
    echo json_encode(['status'=>'fail','msg'=>'ID tidak ditemukan!']);
    exit;
  }
  try {
    // Cek email siswa
    $stmt = $pdo->prepare('SELECT email FROM tb_siswa WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $email = $row['email'] ?? '';
    if($email) {
      // Cek apakah email ada di tb_trx
      $cek = $pdo->prepare('SELECT COUNT(*) FROM tb_trx WHERE email = ?');
      $cek->execute([$email]);
      $adaTrx = $cek->fetchColumn();
      if($adaTrx > 0) {
        echo json_encode(['status'=>'fail','msg'=>'Data tidak dapat dihapus karena masih ada transaksi siswa ini!']);
        exit;
      }
    }
    $stmt = $pdo->prepare('DELETE FROM tb_siswa WHERE id = ?');
    $stmt->execute([$id]);
    echo json_encode(['status'=>'ok']);
  } catch(Exception $e) {
    echo json_encode(['status'=>'error','msg'=>'Gagal menghapus: '.$e->getMessage()]);
  }
  exit;
}

echo json_encode(['status'=>'fail','msg'=>'Aksi tidak valid!']); 