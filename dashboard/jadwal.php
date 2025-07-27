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
  $stmt = $pdo->query("SELECT id, nama FROM tb_mapel ORDER BY id");
  $i=0;
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $colorKeys = array_keys($mapelColors);
    $mapelList[$row['nama']] = $mapelColors[$colorKeys[$i % count($colorKeys)]];
    $i++;
  }
} catch(Exception $e) {}

// Ambil jadwal les dari tb_trx_tanggal untuk bulan aktif
$events = [];
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$startBulan = DateTime::createFromFormat('Y-m', $bulan);
$start = strtotime($startBulan->format('Y-m-01'));
$end = strtotime(date('Y-m-t', $start));
try {
  $stmt = $pdo->prepare("
    SELECT tgl.tanggal, tgl.jam_trx, s.nama AS nama_siswa, m.nama AS nama_mapel, tr.paket
    FROM tb_trx_tanggal tgl
    JOIN tb_trx tr ON tgl.id_trx = tr.id
    LEFT JOIN tb_siswa s ON tr.email = s.email
    LEFT JOIN tb_mapel m ON tr.mapel = m.id
    WHERE tgl.tanggal BETWEEN ? AND ?
  ");
  $stmt->execute([date('Y-m-01', $start), date('Y-m-t', $start)]);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach($rows as $row) {
    $events[] = [
      'date' => $row['tanggal'],
      'jam' => $row['jam_trx'],
      'nama' => $row['nama_siswa'],
      'mapel' => $row['nama_mapel'],
      'paket' => $row['paket']
    ];
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
<div class="flex items-center mb-6">
  <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 flex items-center gap-3">
    <i class="fa-solid fa-calendar-days text-blue-600"></i> Jadwal Les Harian Bulan <?= date('F Y', $start) ?>
  </h1>
</div>
  <div class="max-w-full overflow-x-auto bg-white rounded-3xl shadow-2xl p-4 md:p-8 border border-blue-100 relative">
    <div class="flex items-center justify-between gap-2 mb-2">
      <form method="get" class="inline">
        <input type="hidden" name="bulan" value="<?= htmlspecialchars($prevBulan) ?>">
        <button type="submit" class="px-3 py-1 rounded bg-blue-100 text-blue-700 font-bold shadow hover:bg-blue-200">&laquo; Bulan Sebelumnya</button>
      </form>
      <form method="get" class="inline">
        <input type="hidden" name="bulan" value="<?= htmlspecialchars($nextBulan) ?>">
        <button type="submit" class="px-3 py-1 rounded bg-blue-100 text-blue-700 font-bold shadow hover:bg-blue-200">Bulan Berikutnya &raquo;</button>
      </form>
    </div>
    <table class="min-w-max w-full text-[10px] border-2 border-blue-400 rounded-xl shadow">
      <thead>
        <tr class="bg-blue-100 text-blue-800">
          <th class="py-0.5 px-0.5 sticky left-0 bg-blue-100 z-10 border-blue-300 border-r w-8">Jam</th>
          <?php foreach($showTanggal as $tgl): ?>
            <?php $isPast = $tgl < $today; ?>
            <?php $isMinggu = date('w', strtotime($tgl)) == 0; ?>
            <th class="py-0.5 px-0.5 text-center border-blue-300 border-r last:border-r-0 w-6 min-w-[16px] max-w-[20px] <?= $isPast ? 'bg-gray-100 text-gray-400 opacity-60' : '' ?><?= $isMinggu ? ' text-red-500' : '' ?>">
              <?= date('j', strtotime($tgl)) ?><br><span class="text-[9px] font-normal<?= $isMinggu ? ' text-red-500' : ' text-gray-500' ?>"><?php
                $hariInggris = date('D', strtotime($tgl));
                $hariIndo = [
                  'Sun' => 'Min',
                  'Mon' => 'Sen',
                  'Tue' => 'Sel',
                  'Wed' => 'Rab',
                  'Thu' => 'Kam',
                  'Fri' => 'Jum',
                  'Sat' => 'Sab',
                ];
                echo $hariIndo[$hariInggris] ?? $hariInggris;
              ?></span>
            </th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach($jamList as $jam): ?>
        <tr>
          <td class="py-0.5 px-0.5 font-bold sticky left-0 bg-blue-50 z-10 text-blue-700 text-center border-blue-200 border-r w-8"><?= $jam ?></td>
          <?php foreach($showTanggal as $tgl): ?>
            <?php
              $isPast = $tgl < $today;
              $isMinggu = date('w', strtotime($tgl)) == 0;
              $found = array_filter($events, function($e) use($tgl, $jam) {
                return $e['date'] === $tgl && $e['jam'] === $jam;
              });
              $cellColor = '';
              $hasKelompok = false;
              $hasPrivate = false;
              $colorKL = '';
              $colorPR = '';
              if($found) {
                $first = reset($found);
                $cellColor = $mapelList[$first['mapel']] ?? 'bg-blue-200';
                foreach($found as $f) {
                  if(isset($f['paket']) && substr($f['paket'],0,2)==='KL') {
                    $hasKelompok = true;
                    $colorKL = $mapelList[$f['mapel']] ?? 'bg-pink-400';
                  }
                  if(isset($f['paket']) && substr($f['paket'],0,2)==='PR') {
                    $hasPrivate = true;
                    $colorPR = $mapelList[$f['mapel']] ?? 'bg-blue-400';
                  }
                }
              }
              $dataDetail = '';
              if($found) {
                $details = [];
                foreach($found as $f) {
                  $details[] = htmlspecialchars($f['nama']) . ' - ' . htmlspecialchars($f['mapel']) . ' (' . htmlspecialchars($f['jam']) . ')';
                }
                $dataDetail = implode('<br>', $details);
              }
            ?>
            <td class="py-0.5 px-0.5 text-center align-top border-blue-100 border-r last:border-r-0 border-b min-w-[16px] max-w-[20px]
              <?= ($hasKelompok && $hasPrivate) ? '' : ' ' . $cellColor ?>
              <?= $isPast ? ' bg-gray-100 text-gray-400 opacity-60 pointer-events-none select-none' : '' ?>
              <?= $isMinggu ? ' text-red-500' : '' ?>
              <?= $found ? ' detail-cell' : '' ?>"
              <?php if($found): ?>
                data-detail="<?= $dataDetail ?>"
                data-tanggal="<?= $tgl ?>"
                data-jam="<?= $jam ?>"
                title="<?php
                  $tips = [];
                  foreach($found as $f) {
                    $tips[] = htmlspecialchars($f['nama'].' - '.$f['mapel']);
                  }
                  echo implode(' | ', $tips);
                ?>"
              <?php endif; ?>
            style="height:24px;min-height:24px;max-height:24px;">
              <?php if($hasKelompok && $hasPrivate): ?>
                <div class="w-full h-full flex relative items-center" style="height:100%;min-height:100%;max-height:100%;">
                  <div class="h-full w-1/2 relative flex items-center justify-center <?= $colorKL ?>">
                    <i class='fa fa-users text-[9px] text-pink-700 absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2'></i>
                  </div>
                  <div class="h-full w-1/2 <?= $colorPR ?>"></div>
                </div>
              <?php elseif(isset($hasKelompok) && $hasKelompok): ?>
                <i class="fa fa-users text-xs text-pink-700"></i>
              <?php endif; ?>
            </td>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <!-- Modal Detail Jadwal -->
    <div id="modal-detail-jadwal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
      <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-xs relative">
        <button id="close-modal-detail" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-xl"><i class="fa fa-xmark"></i></button>
        <h2 class="text-lg font-bold text-blue-700 mb-4">Detail Jadwal Les</h2>
        <div id="modal-detail-content" class="text-sm text-gray-700"></div>
      </div>
    </div>
    <script>
      // Modal logic
      const modalDetail = document.getElementById('modal-detail-jadwal');
      const modalContent = document.getElementById('modal-detail-content');
      const closeModalDetail = document.getElementById('close-modal-detail');
      closeModalDetail.onclick = () => { modalDetail.classList.add('hidden'); };
      document.querySelectorAll('.detail-cell').forEach(cell => {
        let clickCount = 0;
        let clickTimer = null;
        cell.addEventListener('click', function() {
          clickCount++;
          if (clickCount === 2) {
            clickCount = 0;
            clearTimeout(clickTimer);
            const detail = this.getAttribute('data-detail');
            const tanggal = this.getAttribute('data-tanggal');
            const jam = this.getAttribute('data-jam');
            // Format tanggal ke Indonesia
            function formatTanggalIndo(tgl) {
              const bulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
              const parts = tgl.split('-');
              if(parts.length === 3) {
                return parts[2] + ' ' + bulanIndo[parseInt(parts[1],10)-1] + ' ' + parts[0];
              }
              return tgl;
            }
            const tanggalIndo = formatTanggalIndo(tanggal);
            if(detail) {
              modalContent.innerHTML = `<b>Tanggal:</b> ${tanggalIndo}<br><b>Jam:</b> ${jam}<br><b>Jadwal:</b><br>${detail}`;
            } else {
              modalContent.innerHTML = `<b>Tanggal:</b> ${tanggalIndo}<br><b>Jam:</b> ${jam}<br><span class='text-gray-400'>Tidak ada jadwal les.</span>`;
            }
            modalDetail.classList.remove('hidden');
          } else {
            clickTimer = setTimeout(() => { clickCount = 0; }, 300);
          }
        });
      });
    </script>
    <div class="flex flex-wrap gap-2 mt-4">
      <?php foreach($usedMapel as $nama=>$warna): ?>
        <span class="inline-flex items-center gap-2 px-2 py-1 rounded <?= $warna ?> border text-xs font-bold">
          <span class="w-3 h-3 rounded-full inline-block <?= $warna ?> border"></span> <?= htmlspecialchars($nama) ?>
        </span>
      <?php endforeach; ?>
    </div>
    <!-- Hapus form paginasi di bawah tabel di sini -->
  </div>
<?php include 'footer.php'; ?> 