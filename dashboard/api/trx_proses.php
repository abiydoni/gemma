<?php
include '../../api/db.php';
header('Content-Type: application/json');
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// List transaksi by email
if ($action === 'list') {
  $email = $_GET['email'] ?? '';
  if(!$email) {
    echo json_encode([]); exit;
  }
  try {
    $stmt = $pdo->prepare('SELECT t.*, p.nama as nama_paket, m.nama as nama_mapel FROM tb_trx t LEFT JOIN tb_paket p ON t.paket = p.kode OR t.paket = p.nama LEFT JOIN tb_mapel m ON t.mapel = m.kode WHERE t.email = ? ORDER BY t.tanggal DESC');
    $stmt->execute([$email]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
  } catch(Exception $e) {
    echo json_encode([]);
  }
  exit;
}

// Get transaksi by id
if ($action === 'get') {
  $id = $_GET['id'] ?? '';
  if(!$id) { echo json_encode([]); exit; }
  $stmt = $pdo->prepare('SELECT * FROM tb_trx WHERE id = ? LIMIT 1');
  $stmt->execute([$id]);
  $trx = $stmt->fetch(PDO::FETCH_ASSOC);
  echo json_encode($trx ?: []); exit;
}

// Tambah transaksi
if ($action === 'add') {
  $email = $_POST['email'] ?? '';
  $paket = $_POST['paket'] ?? '';
  $mapel = $_POST['mapel'] ?? '';
  $harga = $_POST['harga'] ?? 0;
  $hari = $_POST['hari'] ?? '';
  $jam = $_POST['jam'] ?? '';
  $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
  if(!$email || !$paket || !$mapel || !$harga || !$hari || !$jam) {
    echo json_encode(['status'=>'fail','msg'=>'Data wajib diisi lengkap!']); exit;
  }
  try {
    $stmt = $pdo->prepare('INSERT INTO tb_trx (email, paket, mapel, harga, bayar, hari, jam, status, mulai) VALUES (?,?,?,?,0,?,?,0,?)');
    $stmt->execute([$email, $paket, $mapel, $harga, $hari, $jam, $tanggal_mulai ? $tanggal_mulai : date('Y-m-d')]);
    echo json_encode(['status'=>'ok']);
  } catch(Exception $e) {
    echo json_encode(['status'=>'error','msg'=>'Gagal simpan: '.$e->getMessage()]);
  }
  exit;
}

// Edit transaksi
if ($action === 'edit') {
  $id = $_POST['id'] ?? '';
  $paket = $_POST['paket'] ?? '';
  $mapel = $_POST['mapel'] ?? '';
  $harga = $_POST['harga'] ?? 0;
  $hari = $_POST['hari'] ?? '';
  $jam = $_POST['jam'] ?? '';
  $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
  if(!$id || !$paket || !$mapel || !$harga || !$hari || !$jam) {
    echo json_encode(['status'=>'fail','msg'=>'Data wajib diisi lengkap!']); exit;
  }
  try {
    $stmt = $pdo->prepare('UPDATE tb_trx SET paket=?, mapel=?, harga=?, hari=?, jam=?, mulai=? WHERE id=?');
    $stmt->execute([$paket, $mapel, $harga, $hari, $jam, $tanggal_mulai ? $tanggal_mulai : date('Y-m-d'), $id]);
    echo json_encode(['status'=>'ok']);
  } catch(Exception $e) {
    echo json_encode(['status'=>'error','msg'=>'Gagal update: '.$e->getMessage()]);
  }
  exit;
}

// Bayar transaksi
if ($action === 'bayar') {
  $id = $_POST['id'] ?? '';
  $nominal = $_POST['nominal'] ?? 0;
  if(!$id || !$nominal) {
    echo json_encode(['status'=>'fail','msg'=>'Data wajib diisi!']); exit;
  }
  try {
    $stmt = $pdo->prepare('UPDATE tb_trx SET bayar = bayar + ? WHERE id = ?');
    $stmt->execute([$nominal, $id]);
    // Jurnal otomatis keuangan
    $tanggal = date('Y-m-d');
    $keterangan = "[AUTO] Pembayaran Siswa ID: $id";
    $debet = $nominal;
    $kredit = 0;
    $stmt2 = $pdo->prepare('INSERT INTO tb_keuangan (tanggal, keterangan, debet, kredit) VALUES (?, ?, ?, ?)');
    $stmt2->execute([$tanggal, $keterangan, $debet, $kredit]);
    // updateAllSaldo jika ada
    if (function_exists('updateAllSaldo')) updateAllSaldo($pdo);
    echo json_encode(['status'=>'ok']);
  } catch(Exception $e) {
    echo json_encode(['status'=>'error','msg'=>'Gagal bayar: '.$e->getMessage()]);
  }
  exit;
}

// Hapus transaksi
if ($action === 'delete') {
  $id = $_POST['id'] ?? '';
  if(!$id) { echo json_encode(['status'=>'fail','msg'=>'ID tidak ditemukan!']); exit; }
  try {
    $stmt = $pdo->prepare('DELETE FROM tb_trx WHERE id = ?');
    $stmt->execute([$id]);
    echo json_encode(['status'=>'ok']);
  } catch(Exception $e) {
    echo json_encode(['status'=>'error','msg'=>'Gagal hapus: '.$e->getMessage()]);
  }
  exit;
}
echo json_encode(['status'=>'fail','msg'=>'Aksi tidak valid!']); 