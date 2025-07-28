<?php
header('Content-Type: application/json');
include '../../api/db.php';

$action = $_POST['action'] ?? '';

if ($action === 'list_setting') {
    try {
        $stmt = $pdo->query('SELECT sg.*, m.nama as nama_mapel FROM tb_setting_gaji sg LEFT JOIN tb_mapel m ON sg.mapel = m.id ORDER BY m.id ASC');
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'ok', 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}

elseif ($action === 'save_setting') {
    $mapel = $_POST['mapel'] ?? '';
    $presentase = $_POST['presentase'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';
    
    if (!$mapel || !$presentase) {
        echo json_encode(['status' => 'fail', 'msg' => 'Mapel dan presentase wajib diisi!']);
        exit;
    }
    
    try {
        // Cek apakah setting sudah ada
        $stmt = $pdo->prepare('SELECT id FROM tb_setting_gaji WHERE mapel = ?');
        $stmt->execute([$mapel]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update
            $stmt = $pdo->prepare('UPDATE tb_setting_gaji SET presentase_gaji = ?, keterangan = ? WHERE mapel = ?');
            $stmt->execute([$presentase, $keterangan, $mapel]);
        } else {
            // Insert
            $stmt = $pdo->prepare('INSERT INTO tb_setting_gaji (mapel, presentase_gaji, keterangan) VALUES (?, ?, ?)');
            $stmt->execute([$mapel, $presentase, $keterangan]);
        }
        
        echo json_encode(['status' => 'ok', 'msg' => 'Setting gaji berhasil disimpan!']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}

elseif ($action === 'list_gaji') {
    $tentor = $_POST['tentor'] ?? '';
    $bulan = $_POST['bulan'] ?? '';
    $status = $_POST['status'] ?? '';
    
    $where = [];
    $params = [];
    
    if ($tentor) {
        $where[] = "gt.id_tentor = ?";
        $params[] = $tentor;
    }
    
    if ($bulan) {
        $where[] = "gt.bulan = ?";
        $params[] = $bulan;
    }
    
    if ($status) {
        $where[] = "gt.status_pembayaran = ?";
        $params[] = $status;
    }
    
    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $sql = "SELECT 
                gt.*,
                u.nama as nama_tentor,
                m.nama as nama_mapel,
                s.nama as nama_siswa
            FROM tb_gaji_tentor gt
            LEFT JOIN tb_user u ON gt.id_tentor = u.id
            LEFT JOIN tb_mapel m ON gt.mapel = m.id
            LEFT JOIN tb_siswa s ON gt.email_siswa = s.email
            $whereClause
            ORDER BY gt.created_at DESC";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'ok', 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}

elseif ($action === 'hitung_gaji') {
    try {
        $pdo->beginTransaction();

        // Hanya ambil transaksi yang sudah dibayar dan memiliki tentor
        $stmt = $pdo->query("SELECT t.*, u.nama as nama_tentor FROM tb_trx t LEFT JOIN tb_user u ON t.id_tentor = u.id WHERE t.status = 1 AND t.id_tentor IS NOT NULL");
        $transaksi = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $bulanSekarang = date('Y-m');
        $countProcessed = 0;

        foreach ($transaksi as $trx) {
            // Cek apakah ada setting gaji untuk mapel ini
            $stmt = $pdo->prepare('SELECT presentase_gaji FROM tb_setting_gaji WHERE mapel = ?');
            $stmt->execute([$trx['mapel']]);
            $setting = $stmt->fetch();

            if ($setting) {
                $totalPembayaran = $trx['bayar'];
                $presentase = $setting['presentase_gaji'];
                $jumlahGaji = ($totalPembayaran * $presentase) / 100;

                // Cek apakah sudah ada gaji untuk transaksi ini
                $stmt = $pdo->prepare('SELECT id FROM tb_gaji_tentor WHERE id_trx = ?');
                $stmt->execute([$trx['id']]);
                $existing = $stmt->fetch();

                if (!$existing) {
                    $stmt = $pdo->prepare('INSERT INTO tb_gaji_tentor (id_tentor, id_trx, email_siswa, mapel, total_pembayaran, presentase_gaji, jumlah_gaji, bulan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                    $stmt->execute([
                        $trx['id_tentor'],
                        $trx['id'],
                        $trx['email'],
                        $trx['mapel'],
                        $totalPembayaran,
                        $presentase,
                        $jumlahGaji,
                        $bulanSekarang
                    ]);
                    $countProcessed++;
                }
            }
        }

        $pdo->commit();
        echo json_encode(['status' => 'ok', 'msg' => "Gaji tentor berhasil dihitung! {$countProcessed} gaji diproses."]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}

elseif ($action === 'detail_gaji') {
    $id = $_POST['id'] ?? '';
    
    if (!$id) {
        echo json_encode(['status' => 'fail', 'msg' => 'ID tidak ditemukan!']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT gt.*, u.nama as nama_tentor, m.nama as nama_mapel, s.nama as nama_siswa FROM tb_gaji_tentor gt LEFT JOIN tb_user u ON gt.id_tentor = u.id LEFT JOIN tb_mapel m ON gt.mapel = m.id LEFT JOIN tb_siswa s ON gt.email_siswa = s.email WHERE gt.id = ?");
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

elseif ($action === 'bayar_gaji') {
    $id = $_POST['id'] ?? '';
    $tanggalPembayaran = $_POST['tanggal_pembayaran'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';
    
    if (!$id || !$tanggalPembayaran) {
        echo json_encode(['status' => 'fail', 'msg' => 'ID dan tanggal pembayaran wajib diisi!']);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Ambil detail gaji untuk jurnal
        $stmt = $pdo->prepare("SELECT gt.*, u.nama as nama_tentor, m.nama as nama_mapel, s.nama as nama_siswa FROM tb_gaji_tentor gt LEFT JOIN tb_user u ON gt.id_tentor = u.id LEFT JOIN tb_mapel m ON gt.mapel = m.id LEFT JOIN tb_siswa s ON gt.email_siswa = s.email WHERE gt.id = ?");
        $stmt->execute([$id]);
        $gaji = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$gaji) {
            throw new Exception('Data gaji tidak ditemukan!');
        }
        
        // Update status pembayaran gaji
        $stmt = $pdo->prepare('UPDATE tb_gaji_tentor SET status_pembayaran = ?, tanggal_pembayaran = ?, keterangan = ? WHERE id = ?');
        $stmt->execute(['dibayar', $tanggalPembayaran, $keterangan, $id]);
        
        // Buat jurnal pengeluaran gaji otomatis
        $namaTentor = $gaji['nama_tentor'] ?? 'Tentor';
        $namaMapel = $gaji['nama_mapel'] ?? 'Mapel';
        $jumlahGaji = $gaji['jumlah_gaji'];
        $keteranganJurnal = "[AUTO] Pengeluaran Gaji - {$namaTentor} ({$namaMapel})";
        
        if ($keterangan) {
            $keteranganJurnal .= " - {$keterangan}";
        }
        
        // Insert ke jurnal keuangan sebagai pengeluaran (kredit)
        $stmt = $pdo->prepare('INSERT INTO tb_keuangan (tanggal, keterangan, debet, kredit) VALUES (?, ?, 0, ?)');
        $stmt->execute([$tanggalPembayaran, $keteranganJurnal, $jumlahGaji]);
        
        $pdo->commit();
        echo json_encode(['status' => 'ok', 'msg' => 'Gaji berhasil dibayar dan jurnal pengeluaran dibuat!']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}

elseif ($action === 'detail_setting') {
    $id = $_POST['id'] ?? '';
    
    if (!$id) {
        echo json_encode(['status' => 'fail', 'msg' => 'ID tidak ditemukan!']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM tb_setting_gaji WHERE id = ?");
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

elseif ($action === 'delete_setting') {
    $id = $_POST['id'] ?? '';
    
    if (!$id) {
        echo json_encode(['status' => 'fail', 'msg' => 'ID tidak ditemukan!']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM tb_setting_gaji WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['status' => 'ok', 'msg' => 'Setting gaji berhasil dihapus!']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}

elseif ($action === 'init_sample_data') {
    try {
        // Tambahkan sample setting gaji jika belum ada
        $sampleSettings = [
            ['mapel' => 'GE001', 'presentase' => 25.00, 'keterangan' => 'Matematika - 25%'],
            ['mapel' => 'GE002', 'presentase' => 30.00, 'keterangan' => 'Bahasa Inggris - 30%'],
            ['mapel' => 'GE003', 'presentase' => 25.00, 'keterangan' => 'Fisika - 25%'],
            ['mapel' => 'GE004', 'presentase' => 25.00, 'keterangan' => 'Kimia - 25%'],
            ['mapel' => 'GE005', 'presentase' => 30.00, 'keterangan' => 'Bahasa Indonesia - 30%']
        ];
        
        $countAdded = 0;
        foreach ($sampleSettings as $setting) {
            $stmt = $pdo->prepare('SELECT id FROM tb_setting_gaji WHERE mapel = ?');
            $stmt->execute([$setting['mapel']]);
            $existing = $stmt->fetch();
            
            if (!$existing) {
                $stmt = $pdo->prepare('INSERT INTO tb_setting_gaji (mapel, presentase_gaji, keterangan) VALUES (?, ?, ?)');
                $stmt->execute([$setting['mapel'], $setting['presentase'], $setting['keterangan']]);
                $countAdded++;
            }
        }
        
        echo json_encode(['status' => 'ok', 'msg' => "Sample data berhasil ditambahkan! {$countAdded} setting gaji ditambahkan."]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}

else {
    echo json_encode(['status' => 'fail', 'msg' => 'Action tidak valid!']);
}
?> 