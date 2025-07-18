<?php
header('Content-Type: application/json');
include '../../api/db.php';

if (isset($_GET['action']) && $_GET['action'] == 'get' && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM tb_trx WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($data) {
        echo json_encode($data);
    } else {
        echo json_encode([]);
    }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'delete' && !empty($_POST['id'])) {
    $id = $_POST['id'];
    try {
        $pdo->prepare('DELETE FROM tb_trx_tanggal WHERE id_trx = ?')->execute([$id]);
        $pdo->prepare('DELETE FROM tb_trx WHERE id = ?')->execute([$id]);
        echo json_encode(['status'=>'ok']);
    } catch(Exception $e) {
        echo json_encode(['status'=>'error','msg'=>'Gagal menghapus: '.$e->getMessage()]);
    }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'edit_jadwal' && !empty($_POST['id']) && !empty($_POST['tanggal']) && !empty($_POST['jam'])) {
    $id = $_POST['id'];
    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];
    try {
        $stmt = $pdo->prepare('UPDATE tb_trx_tanggal SET tanggal = ?, jam_trx = ? WHERE id = ?');
        $stmt->execute([$tanggal, $jam, $id]);
        echo json_encode(['status'=>'ok']);
    } catch(Exception $e) {
        echo json_encode(['status'=>'error','msg'=>'Gagal update jadwal: '.$e->getMessage()]);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'get_jadwal' && !empty($_GET['id_trx'])) {
    $id_trx = $_GET['id_trx'];
    $stmt = $pdo->prepare('SELECT id, tanggal, jam_trx FROM tb_trx_tanggal WHERE id_trx = ? ORDER BY tanggal, jam_trx');
    $stmt->execute([$id_trx]);
    $jadwal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($jadwal);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'bayar' && !empty($_POST['id']) && !empty($_POST['nominal'])) {
    $id = $_POST['id'];
    $nominal = intval($_POST['nominal']);
    try {
        // Update field bayar
        $stmt = $pdo->prepare('UPDATE tb_trx SET bayar = bayar + ? WHERE id = ?');
        $stmt->execute([$nominal, $id]);
        // Cek apakah sudah lunas, lalu update status jika perlu
        $stmt = $pdo->prepare('SELECT harga, bayar FROM tb_trx WHERE id = ?');
        $stmt->execute([$id]);
        $trx = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($trx && $trx['bayar'] >= $trx['harga']) {
            $pdo->prepare('UPDATE tb_trx SET status = 1 WHERE id = ?')->execute([$id]);
        }
        echo json_encode(['status'=>'ok']);
    } catch(Exception $e) {
        echo json_encode(['status'=>'error','msg'=>'Gagal update pembayaran: '.$e->getMessage()]);
    }
    exit;
}

$email = $_POST['email'] ?? '';
$paket = $_POST['paket'] ?? '';
$mapel = $_POST['mapel'] ?? '';
$harga = $_POST['harga'] ?? 0;
$mode_jadwal = $_POST['mode_jadwal'] ?? 'otomatis';
$tanggal_les = $_POST['tanggal_les'] ?? [];
$jam_les = $_POST['jam_les'] ?? [];

if ($mode_jadwal === 'custom') {
    if(
      !$email || !$paket || !$mapel || !$harga ||
      !is_array($tanggal_les) || count(array_filter($tanggal_les)) == 0 ||
      !is_array($jam_les) || count(array_filter($jam_les)) == 0
    ) {
        echo json_encode(['status'=>'fail','msg'=>'Data wajib diisi lengkap!']);
        exit;
    }
} else if ($mode_jadwal === 'otomatis') {
    $hari = $_POST['hari'] ?? '';
    $jam = $_POST['jam'] ?? '';
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    if(!$email || !$paket || !$mapel || !$harga || !$hari || !$jam || !$tanggal_mulai) {
        echo json_encode(['status'=>'fail','msg'=>'Data wajib diisi lengkap!']);
        exit;
    }
}

try {
    // Insert ke tb_trx (field minimal, tanpa hari, jam, mulai)
    $stmt = $pdo->prepare("INSERT INTO tb_trx (email, paket, mapel, harga, bayar, status, tanggal) VALUES (?,?,?,?,0,0,NOW())");
    $stmt->execute([$email, $paket, $mapel, $harga]);
    $id_trx = $pdo->lastInsertId();

    if ($mode_jadwal === 'custom') {
        $isPR = (substr($paket,0,2) === 'PR');
        foreach($tanggal_les as $i => $tgl) {
            $jamx = $jam_les[$i] ?? '';
            // CEK BENTROK: Jika paket PR, cek bentrok PR saja
            if ($isPR) {
                $cek = $pdo->prepare('SELECT t.id, t.tanggal, t.jam_trx, tr.mapel, tr.paket FROM tb_trx_tanggal t JOIN tb_trx tr ON t.id_trx = tr.id WHERE t.tanggal = ? AND t.jam_trx = ? AND tr.mapel = ? AND LEFT(tr.paket,2) = ?');
                $cek->execute([$tgl, $jamx, $mapel, 'PR']);
                $bentrok = $cek->fetch(PDO::FETCH_ASSOC);
                if ($bentrok) {
                    $stmtMapel = $pdo->prepare('SELECT nama FROM tb_mapel WHERE kode = ? LIMIT 1');
                    $stmtMapel->execute([$bentrok['mapel']]);
                    $namaMapel = $stmtMapel->fetchColumn() ?: $bentrok['mapel'];
                    echo json_encode(['status'=>'fail','msg'=>'Jadwal Privat bentrok!','detail'=>'Tanggal: '.$bentrok['tanggal'].' Jam: '.$bentrok['jam_trx'].' Mapel: '.$namaMapel.' (sudah ada Privat)']);
                    $pdo->prepare('DELETE FROM tb_trx WHERE id = ?')->execute([$id_trx]);
                    exit;
                }
            } else {
                $cek = $pdo->prepare('SELECT t.id, t.tanggal, t.jam_trx, tr.mapel FROM tb_trx_tanggal t JOIN tb_trx tr ON t.id_trx = tr.id WHERE t.tanggal = ? AND t.jam_trx = ? AND tr.mapel = ?');
                $cek->execute([$tgl, $jamx, $mapel]);
                $bentrok = $cek->fetch(PDO::FETCH_ASSOC);
                if ($bentrok) {
                    $stmtMapel = $pdo->prepare('SELECT nama FROM tb_mapel WHERE kode = ? LIMIT 1');
                    $stmtMapel->execute([$bentrok['mapel']]);
                    $namaMapel = $stmtMapel->fetchColumn() ?: $bentrok['mapel'];
                    echo json_encode(['status'=>'fail','msg'=>'Jadwal bentrok!','detail'=>'Tanggal: '.$bentrok['tanggal'].' Jam: '.$bentrok['jam_trx'].' Mapel: '.$namaMapel]);
                    $pdo->prepare('DELETE FROM tb_trx WHERE id = ?')->execute([$id_trx]);
                    exit;
                }
            }
            $stmtTgl = $pdo->prepare("INSERT INTO tb_trx_tanggal (id_trx, tanggal, jam_trx) VALUES (?, ?, ?)");
            $stmtTgl->execute([$id_trx, $tgl, $jamx]);
        }
    } else if ($mode_jadwal === 'otomatis') {
        $isPR = (substr($paket,0,2) === 'PR');
        // Generate tanggal otomatis selama 1 bulan ke depan sesuai hari
        $start = strtotime($tanggal_mulai);
        $end = strtotime('+1 month', $start);
        $hariMap = [
            'Minggu'=>0, 'Senin'=>1, 'Selasa'=>2, 'Rabu'=>3, 'Kamis'=>4, 'Jumat'=>5, 'Sabtu'=>6
        ];
        $dow = $hariMap[$hari] ?? null;
        if($dow !== null) {
            for($d=$start; $d<$end; $d+=86400) {
                if(date('w',$d)==$dow) {
                    if ($isPR) {
                        $cek = $pdo->prepare('SELECT t.id, t.tanggal, t.jam_trx, tr.mapel, tr.paket FROM tb_trx_tanggal t JOIN tb_trx tr ON t.id_trx = tr.id WHERE t.tanggal = ? AND t.jam_trx = ? AND tr.mapel = ? AND LEFT(tr.paket,2) = ?');
                        $cek->execute([date('Y-m-d',$d), $jam, $mapel, 'PR']);
                        $bentrok = $cek->fetch(PDO::FETCH_ASSOC);
                        if ($bentrok) {
                            $stmtMapel = $pdo->prepare('SELECT nama FROM tb_mapel WHERE kode = ? LIMIT 1');
                            $stmtMapel->execute([$bentrok['mapel']]);
                            $namaMapel = $stmtMapel->fetchColumn() ?: $bentrok['mapel'];
                            echo json_encode(['status'=>'fail','msg'=>'Jadwal Privat bentrok!','detail'=>'Tanggal: '.$bentrok['tanggal'].' Jam: '.$bentrok['jam_trx'].' Mapel: '.$namaMapel.' (sudah ada Privat)']);
                            $pdo->prepare('DELETE FROM tb_trx WHERE id = ?')->execute([$id_trx]);
                            exit;
                        }
                    } else {
                        $cek = $pdo->prepare('SELECT t.id, t.tanggal, t.jam_trx, tr.mapel FROM tb_trx_tanggal t JOIN tb_trx tr ON t.id_trx = tr.id WHERE t.tanggal = ? AND t.jam_trx = ? AND tr.mapel = ?');
                        $cek->execute([date('Y-m-d',$d), $jam, $mapel]);
                        $bentrok = $cek->fetch(PDO::FETCH_ASSOC);
                        if ($bentrok) {
                            $stmtMapel = $pdo->prepare('SELECT nama FROM tb_mapel WHERE kode = ? LIMIT 1');
                            $stmtMapel->execute([$bentrok['mapel']]);
                            $namaMapel = $stmtMapel->fetchColumn() ?: $bentrok['mapel'];
                            echo json_encode(['status'=>'fail','msg'=>'Jadwal bentrok!','detail'=>'Tanggal: '.$bentrok['tanggal'].' Jam: '.$bentrok['jam_trx'].' Mapel: '.$namaMapel]);
                            $pdo->prepare('DELETE FROM tb_trx WHERE id = ?')->execute([$id_trx]);
                            exit;
                        }
                    }
                    $stmtTgl = $pdo->prepare("INSERT INTO tb_trx_tanggal (id_trx, tanggal, jam_trx) VALUES (?, ?, ?)");
                    $stmtTgl->execute([$id_trx, date('Y-m-d',$d), $jam]);
                }
            }
        }
    }
    echo json_encode(['status'=>'ok']);
} catch(Exception $e) {
    echo json_encode(['status'=>'error','msg'=>'Gagal simpan: '.$e->getMessage()]);
} 