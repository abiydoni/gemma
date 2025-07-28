<?php
include '../../api/db.php';
header('Content-Type: application/json');

$response = [];

try {
    // Total siswa
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM tb_siswa');
    $response['total_siswa'] = $stmt->fetch()['total'];
    
    // Total transaksi
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM tb_trx');
    $response['total_transaksi'] = $stmt->fetch()['total'];
    
    // Total pendapatan
    $stmt = $pdo->query('SELECT SUM(bayar) as total FROM tb_trx WHERE status = 1');
    $response['total_pendapatan'] = $stmt->fetch()['total'] ?? 0;
    
    // Total tentor
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tb_user WHERE role = 'tentor'");
    $response['total_tentor'] = $stmt->fetch()['total'];
    
    // Transaksi bulan ini
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tb_trx WHERE MONTH(tanggal) = MONTH(CURRENT_DATE()) AND YEAR(tanggal) = YEAR(CURRENT_DATE())");
    $response['transaksi_bulan_ini'] = $stmt->fetch()['total'];
    
    // Pendapatan bulan ini
    $stmt = $pdo->query("SELECT SUM(bayar) as total FROM tb_trx WHERE status = 1 AND MONTH(tanggal) = MONTH(CURRENT_DATE()) AND YEAR(tanggal) = YEAR(CURRENT_DATE())");
    $response['pendapatan_bulan_ini'] = $stmt->fetch()['total'] ?? 0;
    
    // Siswa baru bulan ini
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tb_siswa WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
    $response['siswa_baru_bulan_ini'] = $stmt->fetch()['total'];
    
    // Pendapatan 6 bulan terakhir untuk grafik
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(tanggal, '%Y-%m') as bulan,
            SUM(bayar) as total
        FROM tb_trx 
        WHERE status = 1 
        AND tanggal >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
        ORDER BY bulan
    ");
    $response['pendapatan_bulanan'] = $stmt->fetchAll();
    
    // Transaksi per mapel
    $stmt = $pdo->query("
        SELECT 
            m.nama as mapel,
            COUNT(t.id) as total
        FROM tb_trx t
        LEFT JOIN tb_mapel m ON t.mapel = m.kode
        WHERE t.status = 1
        GROUP BY t.mapel
        ORDER BY total DESC
        LIMIT 10
    ");
    $response['transaksi_mapel'] = $stmt->fetchAll();
    
    // Siswa per jenjang
    $stmt = $pdo->query("
        SELECT 
            p.jenjang,
            COUNT(DISTINCT t.email) as total
        FROM tb_trx t
        LEFT JOIN tb_paket p ON t.paket = p.Kode
        WHERE t.status = 1
        GROUP BY p.jenjang
        ORDER BY total DESC
    ");
    $response['siswa_jenjang'] = $stmt->fetchAll();
    
    // Transaksi terbaru (5 terakhir)
    $stmt = $pdo->query("
        SELECT 
            t.id,
            t.email,
            s.nama as nama_siswa,
            p.nama as nama_paket,
            m.nama as nama_mapel,
            t.harga,
            t.bayar,
            t.status,
            t.tanggal
        FROM tb_trx t
        LEFT JOIN tb_siswa s ON t.email = s.email
        LEFT JOIN tb_paket p ON t.paket = p.Kode
        LEFT JOIN tb_mapel m ON t.mapel = m.kode
        ORDER BY t.tanggal DESC
        LIMIT 5
    ");
    $response['transaksi_terbaru'] = $stmt->fetchAll();
    
    // Jadwal hari ini
    $stmt = $pdo->query("
        SELECT 
            tt.id,
            tt.jam_trx,
            tt.tanggal,
            t.email,
            s.nama as nama_siswa,
            m.nama as nama_mapel,
            u.nama as nama_tentor
        FROM tb_trx_tanggal tt
        LEFT JOIN tb_trx t ON tt.id_trx = t.id
        LEFT JOIN tb_siswa s ON t.email = s.email
        LEFT JOIN tb_mapel m ON t.mapel = m.kode
        LEFT JOIN tb_user u ON t.id_tentor = u.id
        WHERE tt.tanggal = CURRENT_DATE()
        ORDER BY tt.jam_trx ASC
        LIMIT 5
    ");
    $response['jadwal_hari_ini'] = $stmt->fetchAll();
    
    $response['success'] = true;
    
} catch(Exception $e) {
    $response = [
        'success' => false,
        'error' => $e->getMessage(),
        'total_siswa' => 0,
        'total_transaksi' => 0,
        'total_pendapatan' => 0,
        'total_tentor' => 0,
        'transaksi_bulan_ini' => 0,
        'pendapatan_bulan_ini' => 0,
        'siswa_baru_bulan_ini' => 0,
        'pendapatan_bulanan' => [],
        'transaksi_mapel' => [],
        'siswa_jenjang' => [],
        'transaksi_terbaru' => [],
        'jadwal_hari_ini' => []
    ];
}

echo json_encode($response);
?> 