<?php include "header.php"; ?>

<?php
$tentor_id = $_SESSION['user_id'] ?? 0;
$debug_info = [];

// Ambil jadwal untuk tentor ini
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

// Jika tidak ada parameter bulan dan tidak ada jadwal di bulan ini, cari bulan terakhir yang ada jadwal
if (!isset($_GET['bulan'])) {
    try {
        $cek_bulan = $pdo->prepare("
            SELECT DATE_FORMAT(tt.tanggal, '%Y-%m') as bulan
            FROM tb_trx_tanggal tt
            INNER JOIN tb_trx t ON tt.id_trx = t.id
            WHERE t.id_tentor = ?
            ORDER BY tt.tanggal DESC
            LIMIT 1
        ");
        $cek_bulan->execute([$tentor_id]);
        $bulan_terakhir = $cek_bulan->fetch();
        if ($bulan_terakhir && !empty($bulan_terakhir['bulan'])) {
            $bulan = $bulan_terakhir['bulan'];
        }
    } catch(Exception $e) {}
}

$startBulan = DateTime::createFromFormat('Y-m', $bulan);
if (!$startBulan) {
    $bulan = date('Y-m');
    $startBulan = DateTime::createFromFormat('Y-m', $bulan);
}
$start = strtotime($startBulan->format('Y-m-01'));
$end = strtotime(date('Y-m-t', $start));

// Ambil jadwal les untuk bulan ini
$events = [];
$tanggal_awal = date('Y-m-01', $start);
$tanggal_akhir = date('Y-m-t', $start);

// Debug: Cek apakah ada transaksi dengan id_tentor ini
try {
    $debug_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tb_trx WHERE id_tentor = ?");
    $debug_stmt->execute([$tentor_id]);
    $debug_info['total_trx'] = $debug_stmt->fetch()['total'] ?? 0;
    
    // Cek apakah ada jadwal untuk periode ini (tanpa filter tentor)
    $debug_stmt2 = $pdo->prepare("SELECT COUNT(*) as total FROM tb_trx_tanggal WHERE tanggal BETWEEN ? AND ?");
    $debug_stmt2->execute([$tanggal_awal, $tanggal_akhir]);
    $debug_info['total_jadwal_periode'] = $debug_stmt2->fetch()['total'] ?? 0;
    
    // Cek apakah ada jadwal dengan id_tentor di periode ini
    $debug_stmt3 = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM tb_trx_tanggal tt
        INNER JOIN tb_trx t ON tt.id_trx = t.id
        WHERE t.id_tentor = ? AND tt.tanggal BETWEEN ? AND ?
    ");
    $debug_stmt3->execute([$tentor_id, $tanggal_awal, $tanggal_akhir]);
    $debug_info['total_jadwal_tentor'] = $debug_stmt3->fetch()['total'] ?? 0;
    
    // Cek jadwal untuk tentor ini di SEMUA bulan (untuk membantu mencari bulan yang benar)
    $debug_stmt4 = $pdo->prepare("
        SELECT 
            DATE_FORMAT(tt.tanggal, '%Y-%m') as bulan,
            COUNT(*) as total
        FROM tb_trx_tanggal tt
        INNER JOIN tb_trx t ON tt.id_trx = t.id
        WHERE t.id_tentor = ?
        GROUP BY DATE_FORMAT(tt.tanggal, '%Y-%m')
        ORDER BY bulan DESC
        LIMIT 6
    ");
    $debug_stmt4->execute([$tentor_id]);
    $debug_info['bulan_jadwal'] = $debug_stmt4->fetchAll();
    
    // Cek tanggal jadwal terdekat untuk tentor ini
    $debug_stmt5 = $pdo->prepare("
        SELECT tt.tanggal, tt.jam_trx, t.id as id_trx
        FROM tb_trx_tanggal tt
        INNER JOIN tb_trx t ON tt.id_trx = t.id
        WHERE t.id_tentor = ?
        ORDER BY tt.tanggal ASC
        LIMIT 5
    ");
    $debug_stmt5->execute([$tentor_id]);
    $debug_info['jadwal_terdekat'] = $debug_stmt5->fetchAll();
    
} catch(Exception $e) {
    $debug_info['error'] = $e->getMessage();
}

try {
    // Query untuk mengambil jadwal berdasarkan id_tentor
    // tb_trx.mapel menyimpan kode mapel (bukan ID), jadi join dengan m.kode
    // Pastikan format konsisten dengan tentor/index.php
    $stmt = $pdo->prepare("
        SELECT 
            tt.id as jadwal_id,
            DATE(tt.tanggal) as tanggal, 
            tt.jam_trx, 
            t.email,
            COALESCE(s.nama, t.email) AS nama_siswa, 
            COALESCE(m.nama, t.mapel, 'Tidak Diketahui') AS nama_mapel, 
            COALESCE(p.nama, t.paket, '-') as nama_paket,
            t.paket as paket_kode,
            COALESCE(s.email, t.email) AS email,
            t.id_tentor,
            t.mapel as mapel_kode,
            t.id as trx_id
        FROM tb_trx_tanggal tt
        INNER JOIN tb_trx t ON tt.id_trx = t.id AND t.id_tentor = ?
        LEFT JOIN tb_siswa s ON t.email = s.email
        LEFT JOIN tb_mapel m ON t.mapel = m.kode
        LEFT JOIN tb_paket p ON t.paket = p.Kode
        WHERE DATE(tt.tanggal) BETWEEN ? AND ?
        ORDER BY tt.tanggal ASC, tt.jam_trx ASC
    ");
    $stmt->execute([$tentor_id, $tanggal_awal, $tanggal_akhir]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $debug_info['rows_found'] = count($rows);
    $debug_info['raw_rows'] = $rows; // Untuk debugging
    
    if (!empty($rows)) {
        foreach($rows as $row) {
            // Normalisasi tanggal untuk konsistensi
            $tanggal = $row['tanggal'];
            if (is_string($tanggal) && strpos($tanggal, ' ') !== false) {
                $tanggal = explode(' ', $tanggal)[0];
            }
            $tanggal = date('Y-m-d', strtotime($tanggal));
            
            $events[] = [
                'date' => $tanggal,
                'jam' => $row['jam_trx'] ?? '',
                'nama' => $row['nama_siswa'] ?? $row['email'] ?? 'Tidak Diketahui',
                'email' => $row['email'] ?? '',
                'mapel' => $row['nama_mapel'] ?? $row['mapel_kode'] ?? '-',
                'paket' => $row['nama_paket'] ?? $row['paket_kode'] ?? '-'
            ];
        }
    }
    
    $debug_info['events_count'] = count($events);
} catch(Exception $e) {
    // Debug: log error untuk troubleshooting
    error_log("Error loading jadwal tentor ID $tentor_id: " . $e->getMessage());
    // Tampilkan pesan error sederhana (optional, bisa dihapus di production)
    $error_message = $e->getMessage();
    $debug_info['query_error'] = $e->getMessage();
}

// Hitung bulan sebelumnya dan berikutnya
$bulanDate = DateTime::createFromFormat('Y-m', $bulan);
$prevBulan = $bulanDate->modify('-1 month')->format('Y-m');
$bulanDate = DateTime::createFromFormat('Y-m', $bulan);
$nextBulan = $bulanDate->modify('+1 month')->format('Y-m');
$today = date('Y-m-d');

// Siapkan tanggal untuk ditampilkan
$tanggalList = [];
for($d=$start; $d<=strtotime(date('Y-m-t', $start)); $d+=86400) {
    $tanggalList[] = date('Y-m-d', $d);
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 flex items-center gap-3">
            <i class="fa-solid fa-calendar-days text-blue-600"></i> 
            Jadwal Les Bulan <?= date('F Y', $start) ?>
        </h1>
    </div>

    <div class="bg-white rounded-3xl shadow-2xl p-4 md:p-8 border border-blue-100">
        <div class="flex items-center justify-between gap-2 mb-4">
            <form method="get" class="inline">
                <input type="hidden" name="bulan" value="<?= htmlspecialchars($prevBulan) ?>">
                <button type="submit" class="px-3 py-1 rounded bg-blue-100 text-blue-700 font-bold shadow hover:bg-blue-200">
                    <i class="fa fa-chevron-left"></i> Bulan Sebelumnya
                </button>
            </form>
            <form method="get" class="inline">
                <input type="hidden" name="bulan" value="<?= htmlspecialchars($nextBulan) ?>">
                <button type="submit" class="px-3 py-1 rounded bg-blue-100 text-blue-700 font-bold shadow hover:bg-blue-200">
                    Bulan Berikutnya <i class="fa fa-chevron-right"></i>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-blue-100 text-blue-800">
                        <th class="px-4 py-3 text-left border border-blue-300">Tanggal</th>
                        <th class="px-4 py-3 text-left border border-blue-300">Jam</th>
                        <th class="px-4 py-3 text-left border border-blue-300">Siswa</th>
                        <th class="px-4 py-3 text-left border border-blue-300">Mata Pelajaran</th>
                        <th class="px-4 py-3 text-left border border-blue-300">Paket</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($error_message)): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-red-500 border border-blue-300">
                                <i class="fas fa-exclamation-triangle"></i> Error: <?= htmlspecialchars($error_message) ?>
                                <br><small class="text-gray-500">Tentor ID: <?= $tentor_id ?>, Periode: <?= $tanggal_awal ?> - <?= $tanggal_akhir ?></small>
                                <?php if (!empty($debug_info)): ?>
                                <br><br>
                                <details class="text-left mt-4 bg-gray-100 p-4 rounded">
                                    <summary class="cursor-pointer font-semibold text-red-600">Info Debug (Klik untuk melihat)</summary>
                                    <div class="mt-2 text-xs text-gray-600 space-y-1">
                                        <div><strong>Tentor ID:</strong> <?= $tentor_id ?></div>
                                        <div><strong>Total Transaksi dengan ID Tentor ini:</strong> <?= $debug_info['total_trx'] ?? 0 ?></div>
                                        <div><strong>Total Jadwal di Periode ini (semua tentor):</strong> <?= $debug_info['total_jadwal_periode'] ?? 0 ?></div>
                                        <div><strong>Total Jadwal untuk Tentor ini di Periode ini:</strong> <?= $debug_info['total_jadwal_tentor'] ?? 0 ?></div>
                                        <div><strong>Rows Found:</strong> <?= $debug_info['rows_found'] ?? 0 ?></div>
                                        
                                        <?php if (!empty($debug_info['bulan_jadwal'])): ?>
                                        <div class="mt-2 pt-2 border-t">
                                            <strong>Jadwal Tersedia di Bulan:</strong>
                                            <ul class="list-disc list-inside mt-1">
                                                <?php foreach($debug_info['bulan_jadwal'] as $bulan_data): ?>
                                                    <li><?= date('F Y', strtotime($bulan_data['bulan'] . '-01')) ?> (<?= $bulan_data['total'] ?> jadwal)</li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($debug_info['jadwal_terdekat'])): ?>
                                        <div class="mt-2 pt-2 border-t">
                                            <strong>Jadwal Terdekat:</strong>
                                            <ul class="list-disc list-inside mt-1">
                                                <?php foreach($debug_info['jadwal_terdekat'] as $jdwl): ?>
                                                    <li><?= date('d/m/Y', strtotime($jdwl['tanggal'])) ?> - <?= $jdwl['jam_trx'] ?> (Trx ID: <?= $jdwl['id_trx'] ?>)</li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($debug_info['error'])): ?>
                                            <div class="text-red-600 mt-2 pt-2 border-t"><strong>Error:</strong> <?= htmlspecialchars($debug_info['error']) ?></div>
                                        <?php endif; ?>
                                        <?php if (isset($debug_info['query_error'])): ?>
                                            <div class="text-red-600 mt-2 pt-2 border-t"><strong>Query Error:</strong> <?= htmlspecialchars($debug_info['query_error']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </details>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php elseif (empty($events)): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 border border-blue-300">
                                <i class="fas fa-calendar-times"></i> Tidak ada jadwal untuk bulan ini
                                <br><small>Periode: <?= date('d/m/Y', strtotime($tanggal_awal)) ?> - <?= date('d/m/Y', strtotime($tanggal_akhir)) ?></small>
                                <?php if (!empty($debug_info)): ?>
                                <br><br>
                                <details class="text-left mt-4 bg-gray-100 p-4 rounded" open>
                                    <summary class="cursor-pointer font-semibold text-blue-600">Info Debug (Klik untuk melihat)</summary>
                                    <div class="mt-2 text-xs text-gray-600 space-y-1">
                                        <div><strong>Tentor ID:</strong> <?= $tentor_id ?></div>
                                        <div><strong>Total Transaksi dengan ID Tentor ini:</strong> <?= $debug_info['total_trx'] ?? 0 ?></div>
                                        <div><strong>Total Jadwal di Periode ini (semua tentor):</strong> <?= $debug_info['total_jadwal_periode'] ?? 0 ?></div>
                                        <div><strong>Total Jadwal untuk Tentor ini di Periode ini:</strong> <?= $debug_info['total_jadwal_tentor'] ?? 0 ?></div>
                                        <div><strong>Rows Found:</strong> <?= $debug_info['rows_found'] ?? 0 ?></div>
                                        <div><strong>Events Count:</strong> <?= $debug_info['events_count'] ?? 0 ?></div>
                                        
                                        <?php if (!empty($debug_info['bulan_jadwal'])): ?>
                                        <div class="mt-2 pt-2 border-t">
                                            <strong>Jadwal Tersedia di Bulan (Klik untuk melihat):</strong>
                                            <ul class="list-disc list-inside mt-1 space-y-1">
                                                <?php foreach($debug_info['bulan_jadwal'] as $bulan_data): ?>
                                                    <li>
                                                        <a href="?bulan=<?= htmlspecialchars($bulan_data['bulan']) ?>" class="text-blue-600 hover:text-blue-800 hover:underline font-semibold">
                                                            <?= date('F Y', strtotime($bulan_data['bulan'] . '-01')) ?> (<?= $bulan_data['total'] ?> jadwal) 
                                                            <i class="fa fa-arrow-right text-xs"></i>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($debug_info['jadwal_terdekat'])): ?>
                                        <div class="mt-2 pt-2 border-t">
                                            <strong>Jadwal Terdekat:</strong>
                                            <ul class="list-disc list-inside mt-1">
                                                <?php foreach($debug_info['jadwal_terdekat'] as $jdwl): ?>
                                                    <li><?= date('d/m/Y', strtotime($jdwl['tanggal'])) ?> - <?= $jdwl['jam_trx'] ?> (Trx ID: <?= $jdwl['id_trx'] ?>)</li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($debug_info['error'])): ?>
                                            <div class="text-red-600 mt-2 pt-2 border-t"><strong>Error:</strong> <?= htmlspecialchars($debug_info['error']) ?></div>
                                        <?php endif; ?>
                                        <?php if (isset($debug_info['query_error'])): ?>
                                            <div class="text-red-600 mt-2 pt-2 border-t"><strong>Query Error:</strong> <?= htmlspecialchars($debug_info['query_error']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </details>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <?php 
                            $isToday = $event['date'] == $today;
                            $isPast = $event['date'] < $today;
                            ?>
                            <tr class="<?= $isToday ? 'bg-blue-50 font-semibold' : ($isPast ? 'bg-gray-50 opacity-75' : 'hover:bg-gray-50') ?>">
                                <td class="px-4 py-3 border border-blue-300 <?= $isToday ? 'text-blue-700' : '' ?>">
                                    <?= date('d/m/Y', strtotime($event['date'])) ?>
                                    <?php if ($isToday): ?>
                                        <span class="ml-2 text-xs bg-blue-600 text-white px-2 py-1 rounded">Hari Ini</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 border border-blue-300 font-semibold text-blue-600">
                                    <?= $event['jam'] ?>
                                </td>
                                <td class="px-4 py-3 border border-blue-300">
                                    <?= htmlspecialchars($event['nama'] ?? $event['email']) ?>
                                </td>
                                <td class="px-4 py-3 border border-blue-300">
                                    <?= htmlspecialchars($event['mapel'] ?? '-') ?>
                                </td>
                                <td class="px-4 py-3 border border-blue-300">
                                    <?= htmlspecialchars($event['paket'] ?? '-') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>

