<?php
include 'header.php';
include '../api/db.php';

// Ambil semua mapel untuk mapping warna
$mapelColors = [
  'blue'=>'bg-blue-500 text-white',
  'green'=>'bg-green-500 text-white',
  'yellow'=>'bg-yellow-400 text-blue-900',
  'red'=>'bg-red-500 text-white',
  'purple'=>'bg-purple-500 text-white',
  'orange'=>'bg-orange-400 text-blue-900',
  'pink'=>'bg-pink-500 text-white',
  'teal'=>'bg-teal-500 text-white',
  'gray'=>'bg-gray-500 text-white',
  'indigo'=>'bg-indigo-500 text-white',
  'lime'=>'bg-lime-400 text-blue-900',
  'amber'=>'bg-amber-400 text-blue-900',
  'cyan'=>'bg-cyan-500 text-white',
  'fuchsia'=>'bg-fuchsia-500 text-white',
  'rose'=>'bg-rose-500 text-white',
  'emerald'=>'bg-emerald-500 text-white',
  'sky'=>'bg-sky-500 text-white',
  'violet'=>'bg-violet-500 text-white',
  'slate'=>'bg-slate-500 text-white',
  'stone'=>'bg-stone-500 text-white',
  'zinc'=>'bg-zinc-500 text-white',
];
$mapelList = [];
try {
  $stmt = $pdo->query("SELECT kode, nama FROM tb_mapel ORDER BY kode");
  $i=0;
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $colorKeys = array_keys($mapelColors);
    $mapelList[$row['nama']] = $mapelColors[$colorKeys[$i % count($colorKeys)]];
    $i++;
  }
} catch(Exception $e) {}

// Ambil jadwal les (tb_trx) untuk bulan aktif dan 1 bulan berikutnya
$events = [];
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$startBulan = DateTime::createFromFormat('Y-m', $bulan);
$start = strtotime($startBulan->format('Y-m-01'));
$endBulan = clone $startBulan;
$endBulan->modify('+1 month');
$end = strtotime($endBulan->format('Y-m-t'));
try {
  $stmt = $pdo->prepare("SELECT t.*, s.nama as nama_siswa, m.nama as nama_mapel FROM tb_trx t LEFT JOIN tb_siswa s ON t.email = s.email LEFT JOIN tb_mapel m ON t.mapel = m.kode WHERE t.tanggal BETWEEN ? AND ?");
  $stmt->execute([date('Y-m-01', $start), date('Y-m-t', $end)]);
  $trx = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach($trx as $t) {
    $hariMap = [
      'Senin'=>1, 'Selasa'=>2, 'Rabu'=>3, 'Kamis'=>4, 'Jumat'=>5, 'Sabtu'=>6
    ];
    $dow = $hariMap[$t['hari']] ?? null;
    if($dow) {
      $startLoop = strtotime(date('Y-m-01', strtotime($t['tanggal'])));
      $endLoop = strtotime(date('Y-m-t', strtotime($t['tanggal'])));
      for($d=$startLoop; $d<=$endLoop; $d+=86400) {
        if(date('N',$d)==$dow) {
          $date = date('Y-m-d',$d);
          $events[] = [
            'date' => $date,
            'jam' => $t['jam'],
            'nama' => $t['nama_siswa'],
            'mapel' => $t['nama_mapel']
          ];
        }
      }
    }
  }
} catch(Exception $e) {}

// Siapkan tanggal dan jam hanya untuk 1 bulan aktif
$tanggalList = [];
for($d=$start; $d<=strtotime(date('Y-m-t', $start)); $d+=86400) {
  $tanggalList[] = date('Y-m-d', $d);
}
$jamList = [];
for($h=9; $h<=20; $h++) {
  $jamList[] = sprintf('%02d:00', $h);
}

// Hilangkan paginasi, tampilkan semua tanggal
$showTanggal = $tanggalList;

// Hitung bulan sebelumnya dan berikutnya
$bulanDate = DateTime::createFromFormat('Y-m', $bulan);
$prevBulan = $bulanDate->modify('-1 month')->format('Y-m');
$bulanDate = DateTime::createFromFormat('Y-m', $bulan); // reset
$nextBulan = $bulanDate->modify('+1 month')->format('Y-m');
$today = date('Y-m-d');

// Ambil mapel yang dipakai di jadwal bulan ini
$usedMapel = [];
foreach($events as $e) {
  $usedMapel[$e['mapel']] = $mapelList[$e['mapel']] ?? 'bg-blue-500 text-white';
}
?>
<main class="flex-1 p-6 md:p-10 overflow-y-auto">
  <div class="max-w-full overflow-x-auto bg-white rounded-3xl shadow-2xl p-4 md:p-8 border border-blue-100 relative">
    <div class="flex items-center justify-between gap-2 mb-2">
      <form method="get" class="inline">
        <input type="hidden" name="bulan" value="<?= htmlspecialchars($prevBulan) ?>">
        <button type="submit" class="px-3 py-1 rounded bg-blue-100 text-blue-700 font-bold shadow hover:bg-blue-200">&laquo; Bulan Sebelumnya</button>
      </form>
      <div class="text-lg md:text-xl font-bold text-blue-700 text-center flex-1"><?= date('F Y', $start) ?></div>
      <form method="get" class="inline">
        <input type="hidden" name="bulan" value="<?= htmlspecialchars($nextBulan) ?>">
        <button type="submit" class="px-3 py-1 rounded bg-blue-100 text-blue-700 font-bold shadow hover:bg-blue-200">Bulan Berikutnya &raquo;</button>
      </form>
    </div>
    <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 mb-6 flex items-center gap-3">
      <i class="fa-solid fa-calendar-days text-blue-600"></i> Jadwal Les Harian Bulan <?= date('F Y', $start) ?>
    </h1>
    <!-- Hapus form paginasi di sini -->
    <table class="min-w-max w-full text-xs border-2 border-blue-400 rounded-xl shadow">
      <thead>
        <tr class="bg-blue-100 text-blue-800">
          <th class="py-1 px-1 sticky left-0 bg-blue-100 z-10 border-blue-300 border-r w-12">Jam</th>
          <?php foreach($showTanggal as $tgl): ?>
            <?php $isPast = $tgl < $today; ?>
            <th class="py-1 px-1 text-center border-blue-300 border-r last:border-r-0 w-8 min-w-[28px] max-w-[32px] <?= $isPast ? 'bg-gray-100 text-gray-400 opacity-60' : '' ?>">
              <?= date('j', strtotime($tgl)) ?><br><span class="text-[10px] text-gray-500 font-normal"><?= date('D', strtotime($tgl)) ?></span>
            </th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach($jamList as $jam): ?>
        <tr>
          <td class="py-1 px-1 font-bold sticky left-0 bg-blue-50 z-10 text-blue-700 text-center border-blue-200 border-r w-12"><?= $jam ?></td>
          <?php foreach($showTanggal as $tgl): ?>
            <?php
              $isPast = $tgl < $today;
              $found = array_filter($events, function($e) use($tgl, $jam) {
                return $e['date'] === $tgl && $e['jam'] === $jam;
              });
              $cellColor = '';
              if($found) {
                $first = reset($found);
                $cellColor = $mapelList[$first['mapel']] ?? 'bg-blue-200';
              }
            ?>
            <td class="py-0.5 px-0.5 text-center align-top border-blue-100 border-r last:border-r-0 border-b min-w-[18px] max-w-[32px] <?= $cellColor ?> <?= $isPast ? 'bg-gray-100 text-gray-400 opacity-60 pointer-events-none select-none' : '' ?>"
              <?php if($found): ?>
                title="<?php
                  $tips = [];
                  foreach($found as $f) {
                    $tips[] = htmlspecialchars($f['nama'].' - '.$f['mapel']);
                  }
                  echo implode(' | ', $tips);
                ?>"
              <?php endif; ?>
            ></td>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="flex flex-wrap gap-2 mt-4">
      <?php foreach($usedMapel as $nama=>$warna): ?>
        <span class="inline-flex items-center gap-2 px-2 py-1 rounded <?= $warna ?> border text-xs font-bold">
          <span class="w-3 h-3 rounded-full inline-block <?= $warna ?> border"></span> <?= htmlspecialchars($nama) ?>
        </span>
      <?php endforeach; ?>
    </div>
    <!-- Hapus form paginasi di bawah tabel di sini -->
  </div>
</main>
<?php include 'footer.php'; ?> 