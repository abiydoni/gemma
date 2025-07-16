<?php
header('Content-Type: application/json');
include '../../api/db.php';
$action = isset($_POST['action']) ? $_POST['action'] : '';

function updateAllSaldo($pdo) {
  $saldo = 0;
  $stmt = $pdo->query('SELECT * FROM tb_keuangan ORDER BY tanggal, id');
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach($rows as $row) {
    $saldo = $saldo + (int)$row['debet'] - (int)$row['kredit'];
    $pdo->prepare('UPDATE tb_keuangan SET saldo=? WHERE id=?')->execute([$saldo, $row['id']]);
  }
}

try {
  switch($action) {
    case 'list':
      $where = [];
      $params = [];
      if (!empty($_POST['tgl_awal']) && !empty($_POST['tgl_akhir'])) {
        $where[] = 'tanggal BETWEEN ? AND ?';
        $params[] = $_POST['tgl_awal'];
        $params[] = $_POST['tgl_akhir'];
      }
      $sql = 'SELECT id, tanggal, keterangan, debet, kredit FROM tb_keuangan';
      if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
      $sql .= ' ORDER BY tanggal, id';
      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);
      $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode(['success'=>true, 'data'=>$data]);
      break;
    case 'add':
      $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
      $keterangan = $_POST['keterangan'] ?? '';
      $debet = $_POST['debet'] ?? 0;
      $kredit = $_POST['kredit'] ?? 0;
      $stmt = $pdo->prepare('INSERT INTO tb_keuangan (tanggal, keterangan, debet, kredit) VALUES (?, ?, ?, ?)');
      $ok = $stmt->execute([$tanggal, $keterangan, $debet, $kredit]);
      echo json_encode(['success'=>$ok]);
      break;
    case 'edit':
      $id = $_POST['id'] ?? 0;
      $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
      $keterangan = $_POST['keterangan'] ?? '';
      $debet = $_POST['debet'] ?? 0;
      $kredit = $_POST['kredit'] ?? 0;
      $stmt = $pdo->prepare('UPDATE tb_keuangan SET tanggal=?, keterangan=?, debet=?, kredit=? WHERE id=?');
      $ok = $stmt->execute([$tanggal, $keterangan, $debet, $kredit, $id]);
      echo json_encode(['success'=>$ok]);
      break;
    case 'delete':
      $id = $_POST['id'] ?? 0;
      $stmt = $pdo->prepare('DELETE FROM tb_keuangan WHERE id=?');
      $ok = $stmt->execute([$id]);
      echo json_encode(['success'=>$ok]);
      break;
    default:
      echo json_encode(['success'=>false, 'msg'=>'Aksi tidak dikenali']);
  }
} catch(Exception $e) {
  echo json_encode(['success'=>false, 'msg'=>'Error: '.$e->getMessage()]);
} 