<?php
header('Content-Type: application/json');
include '../../api/db.php';

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
        foreach($tanggal_les as $i => $tgl) {
            $jamx = $jam_les[$i] ?? '';
            $stmtTgl = $pdo->prepare("INSERT INTO tb_trx_tanggal (id_trx, tanggal, jam_trx) VALUES (?, ?, ?)");
            $stmtTgl->execute([$id_trx, $tgl, $jamx]);
        }
    } else if ($mode_jadwal === 'otomatis') {
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