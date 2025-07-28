<?php include "header.php"; ?>

<?php
// Ambil data statistik
$stats = [];
try {
    // Total siswa
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM tb_siswa');
    $stats['total_siswa'] = $stmt->fetch()['total'];
    
    // Total transaksi
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM tb_trx');
    $stats['total_transaksi'] = $stmt->fetch()['total'];
    
    // Total pendapatan
    $stmt = $pdo->query('SELECT SUM(bayar) as total FROM tb_trx WHERE status = 1');
    $stats['total_pendapatan'] = $stmt->fetch()['total'] ?? 0;
    
    // Total tentor
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tb_user WHERE role = 'tentor'");
    $stats['total_tentor'] = $stmt->fetch()['total'];
    
    // Transaksi bulan ini
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tb_trx WHERE MONTH(tanggal) = MONTH(CURRENT_DATE()) AND YEAR(tanggal) = YEAR(CURRENT_DATE())");
    $stats['transaksi_bulan_ini'] = $stmt->fetch()['total'];
    
    // Pendapatan bulan ini
    $stmt = $pdo->query("SELECT SUM(bayar) as total FROM tb_trx WHERE status = 1 AND MONTH(tanggal) = MONTH(CURRENT_DATE()) AND YEAR(tanggal) = YEAR(CURRENT_DATE())");
    $stats['pendapatan_bulan_ini'] = $stmt->fetch()['total'] ?? 0;
    
    // Siswa baru bulan ini
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tb_siswa WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
    $stats['siswa_baru_bulan_ini'] = $stmt->fetch()['total'];
    
} catch(Exception $e) {
    $stats = [
        'total_siswa' => 0,
        'total_transaksi' => 0,
        'total_pendapatan' => 0,
        'total_tentor' => 0,
        'transaksi_bulan_ini' => 0,
        'pendapatan_bulan_ini' => 0,
        'siswa_baru_bulan_ini' => 0
    ];
}

// Ambil data untuk grafik
$chart_data = [];
try {
    // Pendapatan 6 bulan terakhir
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
    $chart_data['pendapatan_bulanan'] = $stmt->fetchAll();
    
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
    $chart_data['transaksi_mapel'] = $stmt->fetchAll();
    
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
    $chart_data['siswa_jenjang'] = $stmt->fetchAll();
    
} catch(Exception $e) {
    $chart_data = [
        'pendapatan_bulanan' => [],
        'transaksi_mapel' => [],
        'siswa_jenjang' => []
    ];
}

// Ambil transaksi terbaru
$transaksi_terbaru = [];
try {
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
        LIMIT 10
    ");
    $transaksi_terbaru = $stmt->fetchAll();
} catch(Exception $e) {}

// Ambil jadwal hari ini
$jadwal_hari_ini = [];
try {
    $hari_ini = date('l'); // Nama hari dalam bahasa Inggris
    $hari_indonesia = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa', 
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu'
    ];
    $hari = $hari_indonesia[$hari_ini] ?? 'Senin';
    
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
        LIMIT 10
    ");
    $jadwal_hari_ini = $stmt->fetchAll();
} catch(Exception $e) {}

// Ambil notifikasi
$notifications = [];
try {
    // Transaksi pending
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tb_trx WHERE status = 0");
    $pending_transaksi = $stmt->fetch()['total'];
    if ($pending_transaksi > 0) {
        $notifications[] = [
            'type' => 'warning',
            'icon' => 'fa-exclamation-triangle',
            'title' => 'Transaksi Pending',
            'message' => "Ada $pending_transaksi transaksi yang belum diselesaikan",
            'link' => 'keuangan.php'
        ];
    }
    
    // Jadwal hari ini
    if (count($jadwal_hari_ini) > 0) {
        $notifications[] = [
            'type' => 'info',
            'icon' => 'fa-calendar-day',
            'title' => 'Jadwal Hari Ini',
            'message' => 'Ada ' . count($jadwal_hari_ini) . ' jadwal les hari ini',
            'link' => 'jadwal.php'
        ];
    }
    
    // Siswa baru bulan ini
    if ($stats['siswa_baru_bulan_ini'] > 0) {
        $notifications[] = [
            'type' => 'success',
            'icon' => 'fa-user-plus',
            'title' => 'Siswa Baru',
            'message' => $stats['siswa_baru_bulan_ini'] . ' siswa baru mendaftar bulan ini',
            'link' => 'siswa.php'
        ];
    }
    
} catch(Exception $e) {}
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard</h1>
            <p class="text-gray-600">Selamat datang di sistem manajemen bimbingan belajar</p>
        </div>
        <button id="btnPrint" class="px-3 py-1 rounded bg-green-600 text-white font-bold shadow hover:bg-green-700"><i class="fa fa-print"></i> Print</button>
    </div>
    <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 mb-6 flex items-center gap-3 drop-shadow animate-fadeInUp">
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 shadow-xl animate-pulse">
            <i class="fa-solid fa-gauge-high text-white text-xl"></i>
        </span>
        Dashboard
    </h1>

    <!-- Notifikasi -->
    <?php if (!empty($notifications)): ?>
    <div class="mb-6 space-y-3">
        <?php foreach ($notifications as $notif): ?>
        <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-<?= $notif['type'] == 'warning' ? 'yellow' : ($notif['type'] == 'success' ? 'green' : 'blue') ?>-500">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex items-center justify-center rounded-full bg-<?= $notif['type'] == 'warning' ? 'yellow' : ($notif['type'] == 'success' ? 'green' : 'blue') ?>-100">
                    <i class="fas <?= $notif['icon'] ?> text-<?= $notif['type'] == 'warning' ? 'yellow' : ($notif['type'] == 'success' ? 'green' : 'blue') ?>-600"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-800"><?= $notif['title'] ?></h4>
                    <p class="text-sm text-gray-600"><?= $notif['message'] ?></p>
                </div>
                <a href="<?= $notif['link'] ?>" class="text-<?= $notif['type'] == 'warning' ? 'yellow' : ($notif['type'] == 'success' ? 'green' : 'blue') ?>-600 hover:text-<?= $notif['type'] == 'warning' ? 'yellow' : ($notif['type'] == 'success' ? 'green' : 'blue') ?>-800">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div class="mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-bolt text-yellow-600"></i>
            Quick Actions
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="siswa.php" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1 text-center group">
                <div class="w-12 h-12 mx-auto mb-3 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-blue-700 shadow-lg group-hover:scale-110 transition">
                    <i class="fas fa-user-plus text-white text-xl"></i>
                </div>
                <p class="font-semibold text-gray-800">Tambah Siswa</p>
            </a>
            
            <a href="keuangan.php" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1 text-center group">
                <div class="w-12 h-12 mx-auto mb-3 flex items-center justify-center rounded-full bg-gradient-to-br from-green-500 to-green-700 shadow-lg group-hover:scale-110 transition">
                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
                <p class="font-semibold text-gray-800">Keuangan</p>
            </a>
            
            <a href="jadwal.php" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1 text-center group">
                <div class="w-12 h-12 mx-auto mb-3 flex items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-purple-700 shadow-lg group-hover:scale-110 transition">
                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                </div>
                <p class="font-semibold text-gray-800">Jadwal</p>
            </a>
            
            <a href="laporan_perkembangan.php" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1 text-center group">
                <div class="w-12 h-12 mx-auto mb-3 flex items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-orange-700 shadow-lg group-hover:scale-110 transition">
                    <i class="fas fa-chart-bar text-white text-xl"></i>
                </div>
                <p class="font-semibold text-gray-800">Laporan</p>
            </a>
        </div>
    </div>

    <!-- Tombol Laporan Perkembangan Siswa -->
    <div class="mb-6 flex justify-end">
        <a href="laporan_perkembangan.php" class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow transition">
            <i class="fa-solid fa-file-lines"></i>
            Laporan Perkembangan Siswa
        </a>
    </div>

    <!-- Kartu Statistik Utama -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 animate-fadeInUp card-hover glow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Siswa</p>
                    <p class="text-3xl font-bold text-blue-600" id="totalSiswa" data-stat="total-siswa"><?= number_format($stats['total_siswa']) ?></p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-arrow-up"></i>
                        +<?= $stats['siswa_baru_bulan_ini'] ?> bulan ini
                    </p>
                </div>
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-blue-700 shadow-lg">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 animate-fadeInUp card-hover glow" style="animation-delay:0.1s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                    <p class="text-3xl font-bold text-green-600" data-stat="total-transaksi"><?= number_format($stats['total_transaksi']) ?></p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-arrow-up"></i>
                        +<?= $stats['transaksi_bulan_ini'] ?> bulan ini
                    </p>
                </div>
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-green-500 to-green-700 shadow-lg">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 animate-fadeInUp card-hover glow" style="animation-delay:0.2s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                    <p class="text-3xl font-bold text-yellow-600" data-stat="total-pendapatan">Rp <?= number_format($stats['total_pendapatan']) ?></p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-arrow-up"></i>
                        Rp <?= number_format($stats['pendapatan_bulan_ini']) ?> bulan ini
                    </p>
                </div>
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-yellow-500 to-yellow-700 shadow-lg">
                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 animate-fadeInUp card-hover glow" style="animation-delay:0.3s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tentor</p>
                    <p class="text-3xl font-bold text-purple-600" data-stat="total-tentor"><?= number_format($stats['total_tentor']) ?></p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-users"></i>
                        Aktif
                    </p>
                </div>
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-purple-700 shadow-lg">
                    <i class="fas fa-chalkboard-teacher text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik dan Data -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Grafik Pendapatan Bulanan -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-area text-blue-600"></i>
                Pendapatan Bulanan
            </h3>
            <canvas id="chartPendapatan" width="400" height="200"></canvas>
        </div>

        <!-- Grafik Transaksi per Mapel -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-green-600"></i>
                Transaksi per Mata Pelajaran
            </h3>
            <canvas id="chartMapel" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Data Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Transaksi Terbaru -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-clock text-orange-600"></i>
                Transaksi Terbaru
            </h3>
            <div class="space-y-3 max-h-80 overflow-y-auto custom-scrollbar">
                <?php if (empty($transaksi_terbaru)): ?>
                    <p class="text-gray-500 text-center py-4">Belum ada transaksi</p>
                <?php else: ?>
                    <?php foreach ($transaksi_terbaru as $trx): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($trx['nama_siswa'] ?? $trx['email']) ?></p>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($trx['nama_paket']) ?> - <?= htmlspecialchars($trx['nama_mapel']) ?></p>
                                <p class="text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($trx['tanggal'])) ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-green-600">Rp <?= number_format($trx['bayar']) ?></p>
                                <span class="inline-flex px-2 py-1 text-xs rounded-full <?= $trx['status'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                    <?= $trx['status'] ? 'Lunas' : 'Pending' ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-calendar-day text-blue-600"></i>
                Jadwal Hari Ini
            </h3>
            <div class="space-y-3 max-h-80 overflow-y-auto custom-scrollbar">
                <?php if (empty($jadwal_hari_ini)): ?>
                    <p class="text-gray-500 text-center py-4">Tidak ada jadwal hari ini</p>
                <?php else: ?>
                    <?php foreach ($jadwal_hari_ini as $jadwal): ?>
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($jadwal['nama_siswa'] ?? $jadwal['email']) ?></p>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($jadwal['nama_mapel']) ?></p>
                                <p class="text-xs text-gray-500">Tentor: <?= htmlspecialchars($jadwal['nama_tentor'] ?? 'Belum ditentukan') ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-blue-600"><?= $jadwal['jam_trx'] ?></p>
                                <p class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($jadwal['tanggal'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Ringkasan Keuangan dan Aktivitas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
        <!-- Ringkasan Keuangan -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-wallet text-green-600"></i>
                Ringkasan Keuangan
            </h3>
            <div class="space-y-4">
                <?php
                // Hitung statistik keuangan
                $keuangan_stats = [];
                try {
                    // Total pendapatan hari ini
                    $stmt = $pdo->query("SELECT SUM(bayar) as total FROM tb_trx WHERE status = 1 AND DATE(tanggal) = CURRENT_DATE()");
                    $keuangan_stats['hari_ini'] = $stmt->fetch()['total'] ?? 0;
                    
                    // Total pendapatan minggu ini
                    $stmt = $pdo->query("SELECT SUM(bayar) as total FROM tb_trx WHERE status = 1 AND YEARWEEK(tanggal) = YEARWEEK(CURRENT_DATE())");
                    $keuangan_stats['minggu_ini'] = $stmt->fetch()['total'] ?? 0;
                    
                    // Total pendapatan bulan ini
                    $keuangan_stats['bulan_ini'] = $stats['pendapatan_bulan_ini'];
                    
                    // Rata-rata per transaksi
                    $stmt = $pdo->query("SELECT AVG(bayar) as rata FROM tb_trx WHERE status = 1");
                    $keuangan_stats['rata_transaksi'] = $stmt->fetch()['rata'] ?? 0;
                    
                } catch(Exception $e) {
                    $keuangan_stats = [
                        'hari_ini' => 0,
                        'minggu_ini' => 0,
                        'bulan_ini' => 0,
                        'rata_transaksi' => 0
                    ];
                }
                ?>
                
                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Hari Ini</p>
                        <p class="font-bold text-green-600">Rp <?= number_format($keuangan_stats['hari_ini']) ?></p>
                    </div>
                    <i class="fas fa-calendar-day text-green-600"></i>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Minggu Ini</p>
                        <p class="font-bold text-blue-600">Rp <?= number_format($keuangan_stats['minggu_ini']) ?></p>
                    </div>
                    <i class="fas fa-calendar-week text-blue-600"></i>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Bulan Ini</p>
                        <p class="font-bold text-purple-600">Rp <?= number_format($keuangan_stats['bulan_ini']) ?></p>
                    </div>
                    <i class="fas fa-calendar-alt text-purple-600"></i>
                </div>
                
                <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Rata-rata/Transaksi</p>
                        <p class="font-bold text-yellow-600">Rp <?= number_format($keuangan_stats['rata_transaksi']) ?></p>
                    </div>
                    <i class="fas fa-chart-line text-yellow-600"></i>
                </div>
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-activity text-orange-600"></i>
                Aktivitas Terbaru
            </h3>
            <div class="space-y-3 max-h-80 overflow-y-auto custom-scrollbar">
                <?php
                // Ambil aktivitas terbaru
                $aktivitas = [];
                try {
                    // Siswa baru
                    $stmt = $pdo->query("SELECT nama, created_at FROM tb_siswa ORDER BY created_at DESC LIMIT 3");
                    while ($row = $stmt->fetch()) {
                        $aktivitas[] = [
                            'type' => 'siswa',
                            'icon' => 'fa-user-plus',
                            'color' => 'blue',
                            'text' => 'Siswa baru: ' . $row['nama'],
                            'time' => $row['created_at']
                        ];
                    }
                    
                    // Transaksi baru
                    $stmt = $pdo->query("SELECT t.id, s.nama, t.tanggal FROM tb_trx t LEFT JOIN tb_siswa s ON t.email = s.email ORDER BY t.tanggal DESC LIMIT 3");
                    while ($row = $stmt->fetch()) {
                        $aktivitas[] = [
                            'type' => 'transaksi',
                            'icon' => 'fa-money-bill-wave',
                            'color' => 'green',
                            'text' => 'Transaksi: ' . ($row['nama'] ?? 'Siswa'),
                            'time' => $row['tanggal']
                        ];
                    }
                    
                    // Urutkan berdasarkan waktu
                    usort($aktivitas, function($a, $b) {
                        return strtotime($b['time']) - strtotime($a['time']);
                    });
                    
                    // Ambil 5 aktivitas terbaru
                    $aktivitas = array_slice($aktivitas, 0, 5);
                    
                } catch(Exception $e) {}
                ?>
                
                <?php if (empty($aktivitas)): ?>
                    <p class="text-gray-500 text-center py-4">Belum ada aktivitas</p>
                <?php else: ?>
                    <?php foreach ($aktivitas as $act): ?>
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-<?= $act['color'] ?>-100">
                                <i class="fas <?= $act['icon'] ?> text-<?= $act['color'] ?>-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($act['text']) ?></p>
                                <p class="text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($act['time'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Statistik Mapel -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-book text-purple-600"></i>
                Mata Pelajaran Terpopuler
            </h3>
            <div class="space-y-3">
                <?php if (empty($chart_data['transaksi_mapel'])): ?>
                    <p class="text-gray-500 text-center py-4">Belum ada data</p>
                <?php else: ?>
                    <?php 
                    $colors = ['blue', 'green', 'yellow', 'red', 'purple', 'pink', 'indigo', 'teal'];
                    $i = 0;
                    foreach (array_slice($chart_data['transaksi_mapel'], 0, 5) as $mapel): 
                    ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-<?= $colors[$i % count($colors)] ?>-100">
                                    <i class="fas fa-book text-<?= $colors[$i % count($colors)] ?>-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($mapel['mapel'] ?? 'Lainnya') ?></p>
                                    <p class="text-xs text-gray-500"><?= $mapel['total'] ?> transaksi</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <?php 
                                    $max = max(array_column($chart_data['transaksi_mapel'], 'total'));
                                    $percentage = $max > 0 ? ($mapel['total'] / $max) * 100 : 0;
                                    ?>
                                    <div class="h-full bg-<?= $colors[$i % count($colors)] ?>-500 rounded-full" style="width: <?= $percentage ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php 
                    $i++;
                    endforeach; 
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Loading state
function showLoading() {
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'loading-overlay';
    loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    loadingDiv.innerHTML = `
        <div class="bg-white p-6 rounded-lg shadow-xl">
            <div class="flex items-center gap-3">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="text-gray-700">Memuat data...</p>
            </div>
        </div>
    `;
    document.body.appendChild(loadingDiv);
}

function hideLoading() {
    const loadingDiv = document.getElementById('loading-overlay');
    if (loadingDiv) {
        loadingDiv.remove();
    }
}

// Data untuk grafik
const pendapatanData = <?= json_encode($chart_data['pendapatan_bulanan']) ?>;
const mapelData = <?= json_encode($chart_data['transaksi_mapel']) ?>;
const jenjangData = <?= json_encode($chart_data['siswa_jenjang']) ?>;

// Grafik Pendapatan Bulanan
const ctxPendapatan = document.getElementById('chartPendapatan').getContext('2d');
new Chart(ctxPendapatan, {
    type: 'line',
    data: {
        labels: pendapatanData.map(item => {
            const date = new Date(item.bulan + '-01');
            return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
        }),
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: pendapatanData.map(item => item.total),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            }
        }
    }
});

// Grafik Transaksi per Mapel
const ctxMapel = document.getElementById('chartMapel').getContext('2d');
new Chart(ctxMapel, {
    type: 'doughnut',
    data: {
        labels: mapelData.map(item => item.mapel || 'Lainnya'),
        datasets: [{
            data: mapelData.map(item => item.total),
            backgroundColor: [
                '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6B7280'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Update statistik real-time
function updateStats() {
    fetch('api/get_info.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalSiswa').textContent = new Intl.NumberFormat('id-ID').format(data.total_siswa);
                
                // Update statistik lainnya jika ada
                const totalTransaksiEl = document.querySelector('[data-stat="total-transaksi"]');
                const totalPendapatanEl = document.querySelector('[data-stat="total-pendapatan"]');
                const totalTentorEl = document.querySelector('[data-stat="total-tentor"]');
                
                if (totalTransaksiEl) totalTransaksiEl.textContent = new Intl.NumberFormat('id-ID').format(data.total_transaksi);
                if (totalPendapatanEl) totalPendapatanEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.total_pendapatan);
                if (totalTentorEl) totalTentorEl.textContent = new Intl.NumberFormat('id-ID').format(data.total_tentor);
            }
        })
        .catch(error => {
            console.error('Error updating stats:', error);
            // Tampilkan notifikasi error jika diperlukan
            showNotification('Gagal memperbarui data statistik', 'error');
        });
}

// Fungsi untuk menampilkan notifikasi
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'error' ? 'bg-red-500 text-white' : 
        type === 'success' ? 'bg-green-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 
                           type === 'success' ? 'fa-check-circle' : 
                           'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Hapus notifikasi setelah 3 detik
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Animasi untuk kartu statistik
function animateValue(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = new Intl.NumberFormat('id-ID').format(Math.floor(current));
    }, 16);
}

// Animate statistik cards on load
document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('[data-stat]');
    statCards.forEach(card => {
        const valueEl = card.querySelector('.text-3xl');
        if (valueEl) {
            const finalValue = parseInt(valueEl.textContent.replace(/[^\d]/g, ''));
            animateValue(valueEl, 0, finalValue, 1000);
        }
    });
});

// Update setiap 30 detik
setInterval(updateStats, 30000);

// Auto refresh data setiap 5 menit
setInterval(() => {
    showLoading();
    setTimeout(() => {
        location.reload();
    }, 1000);
}, 300000);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + R untuk refresh
    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
        e.preventDefault();
        showLoading();
        location.reload();
    }
    
    // Ctrl/Cmd + K untuk focus search (jika ada)
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.querySelector('input[type="search"]');
        if (searchInput) {
            searchInput.focus();
        }
    }
});

// Smooth scroll untuk anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Tooltip untuk statistik cards
document.querySelectorAll('.bg-white.p-6.rounded-2xl').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px) scale(1.02)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
});

// Hide loading after page load
window.addEventListener('load', function() {
    hideLoading();
});

// Print button
document.getElementById('btnPrint').addEventListener('click', function() {
    window.open('print/print_dashboard.php', '_blank');
});
</script>

<?php include "footer.php"; ?>