<?php include "header.php"; ?>

<?php
$tentor_id = $_SESSION['user_id'] ?? 0;
$debug_info = [];

// Ambil jadwal untuk tentor ini
// Default adalah bulan saat ini (tanggal sekarang)
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

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

// Organisasi events berdasarkan tanggal
$eventsByDate = [];
foreach($events as $event) {
    $dateKey = $event['date'];
    if (!isset($eventsByDate[$dateKey])) {
        $eventsByDate[$dateKey] = [];
    }
    $eventsByDate[$dateKey][] = $event;
}

// Hitung hari pertama bulan dan jumlah hari
$firstDay = date('w', $start); // 0 = Minggu, 1 = Senin, dst
$daysInMonth = date('t', $start);
$monthName = date('F Y', $start);

// Nama hari
$dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl md:text-4xl font-extrabold text-blue-800 flex items-center gap-3">
                <i class="fa-solid fa-calendar-days text-blue-600"></i> 
                Jadwal Les
            </h1>
            <div class="flex items-center gap-2">
                <form method="get" class="inline">
                    <input type="hidden" name="bulan" value="<?= htmlspecialchars($prevBulan) ?>">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-100 text-blue-700 font-bold shadow hover:bg-blue-200 transition">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                </form>
                <div class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold shadow-lg">
                    <?= $monthName ?>
                </div>
                <form method="get" class="inline">
                    <input type="hidden" name="bulan" value="<?= htmlspecialchars($nextBulan) ?>">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-100 text-blue-700 font-bold shadow hover:bg-blue-200 transition">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-2 md:p-4 border border-blue-100">
        <?php if (isset($error_message)): ?>
            <div class="text-center py-8 text-red-500">
                <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                <p class="text-lg font-semibold">Error: <?= htmlspecialchars($error_message) ?></p>
                <p class="text-sm text-gray-500 mt-2">Tentor ID: <?= $tentor_id ?>, Periode: <?= $tanggal_awal ?> - <?= $tanggal_akhir ?></p>
            </div>
        <?php else: ?>
            <!-- Header Hari -->
            <div class="grid grid-cols-7 gap-1 mb-1">
                <?php foreach($dayNames as $dayName): ?>
                    <div class="text-center font-bold text-blue-800 py-1 text-xs">
                        <?= $dayName ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Kalender Grid -->
            <div class="grid grid-cols-7 gap-1">
                <!-- Spacer untuk hari pertama bulan -->
                <?php for($i = 0; $i < $firstDay; $i++): ?>
                    <div class="min-h-[80px]"></div>
                <?php endfor; ?>

                <!-- Hari dalam bulan -->
                <?php for($day = 1; $day <= $daysInMonth; $day++): ?>
                    <?php
                    $dayPadded = str_pad($day, 2, '0', STR_PAD_LEFT);
                    $currentDate = $bulan . '-' . $dayPadded;
                    $isToday = $currentDate == $today;
                    $isPast = $currentDate < $today;
                    $dayEvents = $eventsByDate[$currentDate] ?? [];
                    $hasEvents = !empty($dayEvents);
                    ?>
                    <div class="min-h-[80px] border rounded p-1 transition-all hover:shadow-md <?= $isToday ? 'border-blue-600 bg-blue-50 shadow-sm' : ($isPast ? 'border-gray-300 bg-gray-50 opacity-75' : 'border-gray-200 bg-white hover:border-blue-400') ?>">
                        <!-- Nomor Tanggal -->
                        <div class="flex items-center justify-between mb-0.5">
                            <span class="text-xs font-bold <?= $isToday ? 'text-blue-700' : ($isPast ? 'text-gray-500' : 'text-gray-800') ?>">
                                <?= $day ?>
                            </span>
                            <?php if ($isToday): ?>
                                <span class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span>
                            <?php endif; ?>
                        </div>

                        <!-- Jadwal -->
                        <div class="space-y-0.5 overflow-y-auto max-h-[calc(100%-1rem)] custom-scrollbar">
                            <?php if ($hasEvents): ?>
                                <?php foreach(array_slice($dayEvents, 0, 2) as $event): ?>
                                    <div class="text-[10px] p-1 rounded bg-blue-100 border-l-2 border-blue-500 hover:bg-blue-200 transition cursor-pointer group" title="<?= htmlspecialchars($event['nama']) ?> - <?= htmlspecialchars($event['mapel']) ?> (<?= $event['jam'] ?>)">
                                        <div class="font-semibold text-blue-700 truncate text-[9px]">
                                            <?= htmlspecialchars($event['jam']) ?>
                                        </div>
                                        <div class="text-gray-700 truncate text-[8px]">
                                            <?= htmlspecialchars($event['nama']) ?>
                                        </div>
                                        <div class="text-gray-500 truncate text-[7px]">
                                            <?= htmlspecialchars($event['mapel']) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (count($dayEvents) > 2): ?>
                                    <div class="text-[9px] text-center text-blue-600 font-semibold pt-0.5">
                                        +<?= count($dayEvents) - 2 ?> lagi
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-gray-300 text-center text-[8px] py-1">
                                    <i class="fa fa-calendar-day"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <!-- Legend -->
            <div class="mt-3 flex flex-wrap items-center justify-center gap-3 text-xs">
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 border border-blue-600 bg-blue-50 rounded"></div>
                    <span class="text-gray-700">Hari Ini</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 border border-gray-200 bg-white rounded"></div>
                    <span class="text-gray-700">Mendatang</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 border border-gray-300 bg-gray-50 rounded"></div>
                    <span class="text-gray-700">Sudah Lewat</span>
                </div>
            </div>

            <?php if (empty($events)): ?>
                <div class="text-center py-8 text-gray-500 mt-4">
                    <i class="fas fa-calendar-times text-4xl mb-4"></i>
                    <p class="text-lg font-semibold">Tidak ada jadwal untuk bulan ini</p>
                    <p class="text-sm mt-2">Periode: <?= date('d/m/Y', strtotime($tanggal_awal)) ?> - <?= date('d/m/Y', strtotime($tanggal_akhir)) ?></p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 3px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<?php include "footer.php"; ?>

