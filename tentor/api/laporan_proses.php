<?php
session_start();
header('Content-Type: application/json');
include '../../api/db.php';

// Pastikan user adalah tentor
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tentor') {
    echo json_encode(['status' => 'error', 'msg' => 'Unauthorized']);
    exit;
}

$tentor_id = $_SESSION['user_id'] ?? 0;
$tentor_nama = $_SESSION['user_nama'] ?? '';

$action = $_POST['action'] ?? '';

if ($action === 'list') {
    $email = $_POST['email'] ?? '';
    $mapel = $_POST['mapel'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    
    $where = ["p.tentor = ?"];
    $params = [$tentor_nama];
    
    // Hanya tampilkan siswa yang diajar oleh tentor ini
    $where[] = "EXISTS (SELECT 1 FROM tb_trx t WHERE t.email = p.email AND t.id_tentor = ?)";
    $params[] = $tentor_id;
    
    if ($email) {
        $where[] = "p.email = ?";
        $params[] = $email;
    }
    
    if ($mapel) {
        $where[] = "p.mapel = ?";
        $params[] = $mapel;
    }
    
    if ($tanggal) {
        $where[] = "p.tanggal = ?";
        $params[] = $tanggal;
    }
    
    $whereClause = 'WHERE ' . implode(' AND ', $where);
    
    $sql = "SELECT DISTINCT 
                p.id,
                p.email,
                s.nama as nama_siswa,
                p.mapel,
                m.nama as nama_mapel,
                p.tanggal,
                p.tentor,
                p.tentor as nama_tentor,
                ROUND(AVG(p2.nilai), 2) as rata_nilai
            FROM tb_perkembangan_siswa p
            LEFT JOIN tb_siswa s ON p.email = s.email
            LEFT JOIN tb_mapel m ON p.mapel = m.id
            LEFT JOIN tb_perkembangan_siswa p2 ON p.email = p2.email AND p.mapel = p2.mapel AND p.tanggal = p2.tanggal
            $whereClause
            GROUP BY p.email, p.mapel, p.tanggal
            ORDER BY p.tanggal DESC, s.nama ASC";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'ok', 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}

elseif ($action === 'save') {
    $id = $_POST['id'] ?? '';
    $email = $_POST['email'] ?? '';
    $mapel = $_POST['mapel'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $jenisPenilaian = $_POST['jenis_penilaian'] ?? [];
    $nilai = $_POST['nilai'] ?? [];
    $keterangan = $_POST['keterangan'] ?? [];
    
    // Validasi: Pastikan siswa diajar oleh tentor ini
    try {
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM tb_trx WHERE email = ? AND id_tentor = ?');
        $stmt->execute([$email, $tentor_id]);
        $check = $stmt->fetch();
        if ($check['count'] == 0) {
            echo json_encode(['status' => 'fail', 'msg' => 'Anda tidak mengajar siswa ini!']);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => 'Error validasi: ' . $e->getMessage()]);
        exit;
    }
    
    // Validasi mapel
    if($mapel) {
        try {
            $stmt = $pdo->prepare('SELECT id, nama FROM tb_mapel WHERE id = ?');
            $stmt->execute([$mapel]);
            $mapelData = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$mapelData) {
                echo json_encode(['status' => 'fail', 'msg' => 'Mapel ID tidak valid!']);
                exit;
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'fail', 'msg' => 'Error validasi mapel: ' . $e->getMessage()]);
            exit;
        }
    }
    
    if (!$email) {
        echo json_encode(['status' => 'fail', 'msg' => 'Email siswa wajib diisi!']);
        exit;
    }
    if (!$mapel) {
        echo json_encode(['status' => 'fail', 'msg' => 'Mapel wajib dipilih!']);
        exit;
    }
    if (!$tanggal) {
        echo json_encode(['status' => 'fail', 'msg' => 'Tanggal wajib diisi!']);
        exit;
    }
    if (empty($jenisPenilaian) || empty($nilai)) {
        echo json_encode(['status' => 'fail', 'msg' => 'Data penilaian tidak lengkap!']);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        if ($id) {
            // Update - hapus data lama (hanya jika milik tentor ini)
            $stmt = $pdo->prepare("DELETE FROM tb_perkembangan_siswa WHERE email = ? AND mapel = ? AND tanggal = ? AND tentor = ?");
            $stmt->execute([$email, $mapel, $tanggal, $tentor_nama]);
        }
        
        // Insert data baru
        $stmt = $pdo->prepare("INSERT INTO tb_perkembangan_siswa (email, mapel, tanggal, tentor, id_jenis_penilaian, nilai, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        for ($i = 0; $i < count($jenisPenilaian); $i++) {
            if (!empty($nilai[$i])) {
                $stmt->execute([
                    $email,
                    $mapel,
                    $tanggal,
                    $tentor_nama,
                    $jenisPenilaian[$i],
                    $nilai[$i],
                    $keterangan[$i] ?? ''
                ]);
            }
        }
        
        $pdo->commit();
        echo json_encode(['status' => 'ok', 'msg' => 'Laporan berhasil disimpan!']);
    } catch (Exception $e) {
        $pdo->rollBack();
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
        // Ambil data utama (email, mapel, tanggal) - hanya jika milik tentor ini
        $stmt = $pdo->prepare("SELECT email, mapel, tanggal FROM tb_perkembangan_siswa WHERE id = ? AND tentor = ? LIMIT 1");
        $stmt->execute([$id, $tentor_nama]);
        $main = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$main) {
            echo json_encode(['status' => 'fail', 'msg' => 'Data tidak ditemukan atau bukan milik Anda!']);
            exit;
        }
        
        // Ambil semua penilaian untuk laporan ini
        $stmt = $pdo->prepare("
            SELECT 
                p.id,
                p.email,
                s.nama as nama_siswa,
                p.mapel,
                m.nama as nama_mapel,
                p.tanggal,
                p.tentor,
                jp.nama_penilaian,
                p.nilai,
                p.keterangan
            FROM tb_perkembangan_siswa p
            LEFT JOIN tb_siswa s ON p.email = s.email
            LEFT JOIN tb_mapel m ON p.mapel = m.id
            LEFT JOIN tb_jenis_penilaian jp ON p.id_jenis_penilaian = jp.id
            WHERE p.email = ? AND p.mapel = ? AND p.tanggal = ? AND p.tentor = ?
            ORDER BY jp.urutan ASC
        ");
        $stmt->execute([$main['email'], $main['mapel'], $main['tanggal'], $tentor_nama]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($data) {
            $result = [
                'id' => $data[0]['id'],
                'email' => $data[0]['email'],
                'nama_siswa' => $data[0]['nama_siswa'],
                'mapel' => $data[0]['mapel'],
                'nama_mapel' => $data[0]['nama_mapel'],
                'tanggal' => $data[0]['tanggal'],
                'tentor' => $data[0]['tentor'],
                'penilaian' => [],
                'rata_nilai' => 0
            ];
            $total = 0;
            foreach($data as $row) {
                $result['penilaian'][] = [
                    'nama' => $row['nama_penilaian'],
                    'nilai' => $row['nilai'],
                    'keterangan' => $row['keterangan']
                ];
                $total += floatval($row['nilai']);
            }
            $result['rata_nilai'] = count($data) ? round($total/count($data),2) : 0;
            $result['nilai'] = array_column($data, 'nilai');
            $result['keterangan'] = array_column($data, 'keterangan');
            $result['jenis_penilaian'] = array_column($data, 'nama_penilaian');
            echo json_encode(['status' => 'ok', 'data' => $result]);
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
        // Ambil data untuk hapus semua record dengan email, mapel, tanggal yang sama (hanya jika milik tentor ini)
        $stmt = $pdo->prepare("SELECT email, mapel, tanggal FROM tb_perkembangan_siswa WHERE id = ? AND tentor = ?");
        $stmt->execute([$id, $tentor_nama]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            $stmt = $pdo->prepare("DELETE FROM tb_perkembangan_siswa WHERE email = ? AND mapel = ? AND tanggal = ? AND tentor = ?");
            $stmt->execute([$data['email'], $data['mapel'], $data['tanggal'], $tentor_nama]);
            
            echo json_encode(['status' => 'ok', 'msg' => 'Laporan berhasil dihapus!']);
        } else {
            echo json_encode(['status' => 'fail', 'msg' => 'Data tidak ditemukan atau bukan milik Anda!']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}

else {
    echo json_encode(['status' => 'fail', 'msg' => 'Action tidak valid!']);
}
?>

