<?php include "header.php"; ?>

<?php
$tentor_id = intval($_SESSION['user_id'] ?? 0);
if ($tentor_id <= 0) {
    header('Location: ../login.php');
    exit;
}

// Ambil statistik untuk tentor
$stats = [];
try {
    // Total siswa yang diajar tentor ini
    $stmt = $pdo->prepare('SELECT COUNT(DISTINCT email) as total FROM tb_trx WHERE id_tentor = ?');
    $stmt->execute([$tentor_id]);
    $stats['total_siswa'] = $stmt->fetch()['total'] ?? 0;
    
    // Total les bulan ini
    $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM tb_trx WHERE id_tentor = ? AND MONTH(tanggal) = MONTH(CURRENT_DATE()) AND YEAR(tanggal) = YEAR(CURRENT_DATE())');
    $stmt->execute([$tentor_id]);
    $stats['les_bulan_ini'] = $stmt->fetch()['total'] ?? 0;
    
    // Total les hari ini
    $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM tb_trx_tanggal tt JOIN tb_trx t ON tt.id_trx = t.id WHERE t.id_tentor = ? AND tt.tanggal = CURRENT_DATE()');
    $stmt->execute([$tentor_id]);
    $stats['les_hari_ini'] = $stmt->fetch()['total'] ?? 0;
    
    // Total gaji bulan ini
    $stmt = $pdo->prepare('SELECT SUM(jumlah_gaji) as total FROM tb_gaji_tentor WHERE id_tentor = ? AND bulan = DATE_FORMAT(CURRENT_DATE(), "%Y-%m")');
    $stmt->execute([$tentor_id]);
    $stats['gaji_bulan_ini'] = $stmt->fetch()['total'] ?? 0;
    
} catch(Exception $e) {
    $stats = [
        'total_siswa' => 0,
        'les_bulan_ini' => 0,
        'les_hari_ini' => 0,
        'gaji_bulan_ini' => 0
    ];
}

// Ambil jadwal mendatang untuk tentor (termasuk hari ini)
$jadwal_mendatang = [];
$debug_jadwal = [];
try {
    $stmt = $pdo->prepare("
        SELECT 
            tt.id as jadwal_id,
            tt.jam_trx,
            DATE(tt.tanggal) as tanggal,
            t.email,
            t.id as trx_id,
            COALESCE(s.nama, t.email) as nama_siswa,
            COALESCE(m.nama, t.mapel, 'Tidak Diketahui') as nama_mapel,
            COALESCE(p.nama, t.paket, '-') as nama_paket,
            t.id_tentor,
            t.mapel as mapel_kode,
            t.paket as paket_kode
        FROM tb_trx_tanggal tt
        INNER JOIN tb_trx t ON tt.id_trx = t.id AND t.id_tentor = ?
        LEFT JOIN tb_siswa s ON t.email = s.email
        LEFT JOIN tb_mapel m ON t.mapel = m.kode
        LEFT JOIN tb_paket p ON t.paket = p.Kode
        WHERE DATE(tt.tanggal) >= CURRENT_DATE()
        ORDER BY tt.tanggal ASC, tt.jam_trx ASC
        LIMIT 10
    ");
    $stmt->execute([$tentor_id]);
    $jadwal_mendatang = array_map(function($row) {
        if (!empty($row['tanggal'])) {
            $row['tanggal'] = date('Y-m-d', strtotime($row['tanggal']));
        }
        return $row;
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    $debug_jadwal = [
        'tentor_id' => $tentor_id,
        'rows_found' => count($jadwal_mendatang),
        'today_date' => date('Y-m-d')
    ];
} catch(Exception $e) {
    error_log("Error loading jadwal mendatang tentor ID $tentor_id: " . $e->getMessage());
    $debug_jadwal['error'] = $e->getMessage();
}

// Ambil jadwal minggu ini
$jadwal_minggu_ini = [];
try {
    // Hitung awal dan akhir minggu ini
    $today = new DateTime();
    $monday = clone $today;
    $monday->modify('monday this week');
    $sunday = clone $today;
    $sunday->modify('sunday this week');
    
    $stmt = $pdo->prepare("
        SELECT 
            tt.id as jadwal_id,
            tt.jam_trx,
            DATE(tt.tanggal) as tanggal,
            t.email,
            COALESCE(s.nama, t.email) as nama_siswa,
            COALESCE(m.nama, t.mapel, 'Tidak Diketahui') as nama_mapel,
            COALESCE(p.nama, t.paket, '-') as nama_paket
        FROM tb_trx_tanggal tt
        INNER JOIN tb_trx t ON tt.id_trx = t.id AND t.id_tentor = ?
        LEFT JOIN tb_siswa s ON t.email = s.email
        LEFT JOIN tb_mapel m ON t.mapel = m.kode
        LEFT JOIN tb_paket p ON t.paket = p.Kode
        WHERE DATE(tt.tanggal) BETWEEN ? AND ?
        ORDER BY tt.tanggal ASC, tt.jam_trx ASC
    ");
    $stmt->execute([$tentor_id, $monday->format('Y-m-d'), $sunday->format('Y-m-d')]);
    $jadwal_minggu_ini = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    error_log("Error loading jadwal minggu ini tentor ID $tentor_id: " . $e->getMessage());
}

// Ambil siswa yang diajar
$siswa_list = [];
try {
    $stmt = $pdo->prepare("
        SELECT DISTINCT
            s.nama,
            s.email,
            COUNT(DISTINCT t.id) as total_les
        FROM tb_siswa s
        INNER JOIN tb_trx t ON s.email = t.email
        WHERE t.id_tentor = ?
        GROUP BY s.email, s.nama
        ORDER BY s.nama ASC
        LIMIT 10
    ");
    $stmt->execute([$tentor_id]);
    $siswa_list = $stmt->fetchAll();
} catch(Exception $e) {}
?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard Tentor</h1>
        <p class="text-gray-600">Selamat datang, <?= htmlspecialchars($_SESSION['user_nama'] ?? 'Tentor') ?>!</p>
    </div>

    <!-- Kartu Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Siswa</p>
                    <p class="text-3xl font-bold text-blue-600"><?= number_format($stats['total_siswa']) ?></p>
                </div>
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-blue-700 shadow-lg">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Les Hari Ini</p>
                    <p class="text-3xl font-bold text-green-600"><?= number_format($stats['les_hari_ini']) ?></p>
                </div>
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-green-500 to-green-700 shadow-lg">
                    <i class="fas fa-calendar-day text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Les Bulan Ini</p>
                    <p class="text-3xl font-bold text-yellow-600"><?= number_format($stats['les_bulan_ini']) ?></p>
                </div>
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-yellow-500 to-yellow-700 shadow-lg">
                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Gaji Bulan Ini</p>
                    <p class="text-3xl font-bold text-purple-600">Rp <?= number_format($stats['gaji_bulan_ini']) ?></p>
                </div>
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-purple-700 shadow-lg">
                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Hari Ini dan Data Lainnya -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Jadwal Mendatang -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-calendar-day text-blue-600"></i>
                Jadwal Mendatang
            </h3>
            <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                <?php if (empty($jadwal_mendatang)): ?>
                    <p class="text-gray-500 text-center py-4">Belum ada jadwal mendatang</p>
                    <?php if (!empty($debug_jadwal)): ?>
                    <details class="mt-2 text-xs text-gray-400">
                        <summary class="cursor-pointer">Debug Info</summary>
                        <div class="mt-2 p-2 bg-gray-100 rounded text-left">
                            <div>Tentor ID: <?= $debug_jadwal['tentor_id'] ?? 'N/A' ?></div>
                            <div>Today Date: <?= $debug_jadwal['today_date'] ?? date('Y-m-d') ?></div>
                            <div>Total Found: <?= $debug_jadwal['rows_found'] ?? 0 ?></div>
                            <?php if (isset($debug_jadwal['error'])): ?>
                                <div class="text-red-600">Error: <?= htmlspecialchars($debug_jadwal['error']) ?></div>
                            <?php endif; ?>
                        </div>
                    </details>
                    <?php endif; ?>
                <?php else: ?>
                    <?php foreach ($jadwal_mendatang as $jadwal): ?>
                        <?php 
                        // Normalisasi tanggal untuk perbandingan
                        $jadwal_tanggal = $jadwal['tanggal'];
                        $jadwal_tanggal = date('Y-m-d', strtotime($jadwal_tanggal));
                        $is_today = $jadwal_tanggal == date('Y-m-d');
                        ?>
                        <div class="flex items-center justify-between p-3 <?= $is_today ? 'bg-blue-50' : 'bg-green-50' ?> rounded-lg hover:bg-opacity-80 transition">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($jadwal['nama_siswa'] ?? $jadwal['email'] ?? 'Tidak Diketahui') ?></p>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($jadwal['nama_mapel'] ?? '-') ?></p>
                                <p class="text-xs text-gray-500"><?= htmlspecialchars($jadwal['nama_paket'] ?? '-') ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-blue-600"><?= htmlspecialchars($jadwal['jam_trx'] ?? '-') ?></p>
                                <p class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($jadwal_tanggal)) ?></p>
                                <?php if ($is_today): ?>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs bg-blue-600 text-white">Hari ini</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Siswa yang Diajar -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user-graduate text-green-600"></i>
                Siswa yang Diajar
            </h3>
            <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                <?php if (empty($siswa_list)): ?>
                    <p class="text-gray-500 text-center py-4">Belum ada siswa</p>
                <?php else: ?>
                    <?php foreach ($siswa_list as $siswa): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($siswa['nama']) ?></p>
                                <p class="text-xs text-gray-500"><?= htmlspecialchars($siswa['email']) ?></p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    <?= $siswa['total_les'] ?> les
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Jadwal Minggu Ini -->
    <div class="mt-8 bg-white p-6 rounded-2xl shadow-lg">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-calendar-week text-purple-600"></i>
            Jadwal Minggu Ini
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Tanggal</th>
                        <th class="px-4 py-2 text-left">Jam</th>
                        <th class="px-4 py-2 text-left">Siswa</th>
                        <th class="px-4 py-2 text-left">Mata Pelajaran</th>
                        <th class="px-4 py-2 text-left">Paket</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jadwal_minggu_ini)): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">Tidak ada jadwal minggu ini</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($jadwal_minggu_ini as $jadwal): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2"><?= date('d/m/Y', strtotime($jadwal['tanggal'])) ?></td>
                                <td class="px-4 py-2 font-semibold text-blue-600"><?= $jadwal['jam_trx'] ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($jadwal['nama_siswa'] ?? $jadwal['email']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($jadwal['nama_mapel'] ?? '-') ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($jadwal['nama_paket'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>

