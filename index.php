<?php include "includes/header.php"; ?>
<?php
include 'api/db.php';

// Validasi profile agar tidak error jika belum ada
if (!isset($profile) || !is_array($profile)) {
  $profile = [
    'nama' => 'Bimbel Modern',
    'keterangan' => 'Deskripsi singkat bimbel.',
    'ig' => '',
    'wa' => ''
  ];
}

$fasilitas = [];
$fa_icons = [
  'fa-user-graduate', 'fa-comments', 'fa-book-open', 'fa-award', 'fa-star', 'fa-lightbulb', 'fa-heart', 'fa-gem', 'fa-chalkboard-teacher', 'fa-certificate', 'fa-rocket', 'fa-handshake', 'fa-trophy', 'fa-briefcase', 'fa-graduation-cap'
];
$icon_bg = [
  'from-blue-400 to-blue-600 text-white',
  'from-pink-400 to-pink-600 text-white',
  'from-yellow-400 to-yellow-600 text-white',
  'from-purple-400 to-purple-600 text-white',
  'from-green-400 to-green-600 text-white',
  'from-cyan-400 to-cyan-600 text-white',
  'from-orange-400 to-orange-600 text-white',
  'from-red-400 to-red-600 text-white',
];
$text_color = [
  'text-blue-700 group-hover:text-blue-900',
  'text-pink-700 group-hover:text-pink-900',
  'text-yellow-700 group-hover:text-yellow-900',
  'text-purple-700 group-hover:text-purple-900',
  'text-green-700 group-hover:text-green-900',
  'text-cyan-700 group-hover:text-cyan-900',
  'text-orange-700 group-hover:text-orange-900',
  'text-red-700 group-hover:text-red-900',
];
$border_hover = [
  'hover:border-blue-400',
  'hover:border-pink-400',
  'hover:border-yellow-400',
  'hover:border-purple-400',
  'hover:border-green-400',
  'hover:border-cyan-400',
  'hover:border-orange-400',
  'hover:border-red-400',
];
try {
  $stmt = $pdo->query('SELECT nama, keterangan, ikon FROM tb_fasilitas ORDER BY id ASC');
  $fasilitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  error_log($e->getMessage());
}

$jadwal = [];
try {
  $stmt = $pdo->query('SELECT hari, buka, tutup FROM tb_jadwal ORDER BY FIELD(hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu")');
  $jadwal = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}
$icon_hari = [
  'Senin' => 'fa-calendar-day',
  'Selasa' => 'fa-calendar-day',
  'Rabu' => 'fa-calendar-day',
  'Kamis' => 'fa-calendar-day',
  'Jumat' => 'fa-calendar-check',
  'Sabtu' => 'fa-calendar-week',
  'Minggu' => 'fa-calendar-xmark',
];
$card_bg = [
  'from-blue-100 via-white to-blue-50',
  'from-pink-100 via-white to-pink-50',
  'from-yellow-100 via-white to-yellow-50',
  'from-purple-100 via-white to-purple-50',
  'from-green-100 via-white to-green-50',
  'from-cyan-100 via-white to-cyan-50',
  'from-orange-100 via-white to-orange-50',
];
$badge_bg = [
  'bg-blue-100 text-blue-700',
  'bg-pink-100 text-pink-700',
  'bg-yellow-100 text-yellow-700',
  'bg-purple-100 text-purple-700',
  'bg-green-100 text-green-700',
  'bg-cyan-100 text-cyan-700',
  'bg-orange-100 text-orange-700',
];
// Warna gradasi untuk setiap card (acak/berbeda tiap card)
$card_gradients = [
  ['from-green-400 to-blue-500', 'from-green-500 to-green-600'],
  ['from-cyan-400 to-blue-500', 'from-cyan-500 to-blue-600'],
  ['from-purple-400 to-pink-500', 'from-purple-500 to-pink-600'],
  ['from-yellow-400 to-orange-500', 'from-yellow-500 to-orange-600'],
  ['from-pink-400 to-red-500', 'from-pink-500 to-red-600'],
  ['from-blue-400 to-indigo-500', 'from-blue-500 to-indigo-600'],
  ['from-orange-400 to-yellow-500', 'from-orange-500 to-yellow-600'],
  ['from-teal-400 to-cyan-500', 'from-teal-500 to-cyan-600'],
];

// --- Ambil data paket dari tb_paket ---
$paket = [];
try {
  $stmt = $pdo->query('SELECT jenjang, nama, harga FROM tb_paket ORDER BY FIELD(jenjang, "SD", "SMP", "SMA", "UMUM"), nama ASC');
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $paket[$row['jenjang']][] = $row;
  }
} catch (Exception $e) {}

// --- Ambil data jenjang dari tb_jenjang ---
$jenjangs = [];
try {
  $stmt = $pdo->query('SELECT nama, keterangan FROM tb_jenjang ORDER BY id ASC');
  $jenjangs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}
?>
    <title><?= htmlspecialchars($profile['nama']) ?> - Bimbingan Belajar Modern</title>
    <!-- Hero Section -->
    <section id="hero" class="relative py-20 px-6 flex flex-col md:flex-row items-center gap-10 overflow-hidden bg-gradient-to-br from-blue-700 via-blue-500 to-blue-300 text-white" style="min-height: 480px;">
        <!-- Slideshow Background -->
        <div id="hero-bg1" class="absolute inset-0 bg-center bg-cover z-0" style="background-image: url('assets/img/bg1.jpg'); opacity: 1; transition: opacity 1.5s;"></div>
        <div id="hero-bg2" class="absolute inset-0 bg-center bg-cover z-0" style="background-image: url('assets/img/bg2.jpg'); opacity: 0; transition: opacity 1.5s;"></div>
        <div id="hero-bg3" class="absolute inset-0 bg-center bg-cover z-0" style="background-image: url('assets/img/bg3.jpg'); opacity: 0; transition: opacity 1.5s;"></div>
        <div id="hero-bg4" class="absolute inset-0 bg-center bg-cover z-0" style="background-image: url('assets/img/bg4.jpg'); opacity: 0; transition: opacity 1.5s;"></div>
        <div id="hero-bg5" class="absolute inset-0 bg-center bg-cover z-0" style="background-image: url('assets/img/bg5.jpg'); opacity: 0; transition: opacity 1.5s;"></div>
        
        <!-- Overlay Gradasi Biru Metalik -->
        <div class="absolute inset-0 z-0 pointer-events-none" style="background: linear-gradient(135deg,rgba(25,118,210,0.85),rgba(59,130,246,0.7));"></div>
        
        <div class="relative flex-1 space-y-6 z-10 pl-24">
            <h1 class="relative text-5xl md:text-7xl font-extrabold text-white tracking-tight mb-2" style="text-shadow: 0 6px 0 #2563eb, 0 12px 24px #2563eb99;">
                <span class="absolute inset-0 z-0 select-none" aria-hidden="true" style="color:#2563eb; filter: blur(2px); transform: translate(6px,10px); text-shadow:none;"><?= htmlspecialchars($profile['nama']) ?></span>
                <span class="relative z-10"><?= htmlspecialchars($profile['nama']) ?></span>
                <span class="absolute -top-4 -left-8 text-blue-300 text-2xl rotate-[-20deg]">★</span>
                <span class="absolute -top-6 left-1/2 text-blue-400 text-xl rotate-12">★</span>
                <span class="absolute -bottom-4 left-1/3 text-blue-200 text-lg rotate-6">★</span>
            </h1>
            <p class="text-xl md:text-2xl"><?= htmlspecialchars($profile['keterangan']) ?></p>
            <p class="text-xl md:text-2xl">Daftar sekarang, raih prestasi bersama kami.</p>
            <a href="daftar.php" class="px-5 py-2 bg-gradient-to-b from-yellow-400 to-yellow-600 text-blue-900 font-extrabold rounded-full border-2 border-yellow-200 shadow-xl transition inline-flex items-center gap-2 hover:scale-105 focus:scale-105">
              <span>Pendaftaran</span>
              <i class="fa-solid fa-chalkboard-user text-base"></i>
            </a>
        </div>
        <div class="relative flex-1 flex justify-center z-10">
            <img src="assets/img/ilustrasi.svg" alt="Ilustrasi Bimbel" class="w-96 h-96 object-contain rounded-3xl">
        </div>
    </section>

    <!-- Grid Card Fitur Unggulan Overlap -->
    <div class="relative z-30 max-w-6xl mx-auto -mt-24">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            <?php foreach($fasilitas as $i => $f): ?>
            <?php $bg = $icon_bg[$i % count($icon_bg)]; $txt = $text_color[$i % count($text_color)]; $bord = $border_hover[$i % count($border_hover)]; ?>
            <div class="bg-gradient-to-br from-blue-50 via-white to-blue-100 rounded-2xl shadow-xl p-8 flex flex-col items-center transition transform hover:scale-105 hover:shadow-2xl <?= $bord ?> border-2 border-transparent group">
                <div class="w-20 h-20 flex items-center justify-center rounded-full bg-gradient-to-br <?= $bg ?> mb-4 shadow-lg animate-bounce-slow">
                    <i class="fa-solid <?= htmlspecialchars($f['ikon']) ?> text-4xl"></i>
                </div>
                <h3 class="font-extrabold text-xl mb-2 <?= $txt ?> transition"><?= htmlspecialchars($f['nama']) ?></h3>
                <p class="text-gray-600 text-center"><?= htmlspecialchars($f['keterangan']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <style>
    @keyframes bounce-slow {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-12px); }
    }
    .animate-bounce-slow {
      animation: bounce-slow 2.2s infinite;
    }
    </style>

    <!-- Floating Social Media Icons -->
    <div class="fixed left-4 top-1/3 z-50 flex flex-col gap-4">
        <a href="<?= $profile['ig'] ? 'https://instagram.com/' . htmlspecialchars($profile['ig']) : '#' ?>" target="_blank" class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-tr from-pink-500 to-yellow-500 text-white text-2xl shadow-lg transition social-anim social-ig" aria-label="Instagram">
            <i class="fa-brands fa-instagram"></i>
        </a>
        <?php
        $wa = isset($profile['wa']) ? $profile['wa'] : '';
        $wa_link = $wa;
        if (strpos($wa, '08') === 0) {
            $wa_link = '62' . substr($wa, 1);
        }
        $wa_link = preg_replace('/[^0-9]/', '', $wa_link);
        ?>
        <a href="<?= $wa_link ? 'https://wa.me/' . htmlspecialchars($wa_link) : '#' ?>" target="_blank" class="w-12 h-12 flex items-center justify-center rounded-full bg-green-500 text-white text-2xl shadow-lg transition social-anim social-wa" aria-label="WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    </div>

    <!-- Floating Back to Top Button -->
    <button onclick="window.scrollTo({top:0,behavior:'smooth'})" class="fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full bg-blue-600 text-white text-2xl shadow-lg flex items-center justify-center hover:bg-blue-700 transition" aria-label="Kembali ke atas">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

<?php
// --- Gabungkan hari kerja (Senin-Sabtu) dan ambil jam buka utama ---
$hari_kerja = [];
$jam_buka = '';
$jam_tutup = '';
$libur = false;
foreach ($jadwal as $j) {
    if ($j['hari'] !== 'Minggu' && $j['buka'] != '00:00:00' && $j['tutup'] != '00:00:00') {
        $hari_kerja[] = $j['hari'];
        $jam_buka = $j['buka'];
        $jam_tutup = $j['tutup'];
    }
    if ($j['hari'] === 'Minggu' && $j['buka'] == '00:00:00' && $j['tutup'] == '00:00:00') {
        $libur = true;
    }
}
$hari_kerja_str = count($hari_kerja) === 6 ? 'Senin - Sabtu' : implode(', ', $hari_kerja);

// --- Ambil 3 fitur utama dari tb_fasilitas ---
$fitur_utama = array_slice($fasilitas, 0, 3);

// --- WhatsApp link ---
$wa = isset($profile['wa']) ? $profile['wa'] : '';
$wa_link = $wa;
if (strpos($wa, '08') === 0) {
    $wa_link = '62' . substr($wa, 1);
}
$wa_link = preg_replace('/[^0-9]/', '', $wa_link);
?>
<!-- SECTION JAM BUKA & FITUR UNGGULAN BARU -->
<section id="jadwal" class="relative py-12 px-2 md:px-0 flex flex-col items-center justify-center min-h-[340px] bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50">
  <!-- Judul dan Subjudul -->
  <div class="text-center mb-8">
    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mb-4 shadow-lg">
      <i class="fa-solid fa-clock text-white text-2xl"></i>
    </div>
    <h2 class="text-4xl md:text-5xl font-extrabold mb-2 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
      Jam Buka Bimbel
    </h2>
    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
      Kami siap melayani Anda dengan sepenuh hati untuk membantu meraih prestasi akademik yang gemilang
    </p>
  </div>
  <div class="relative w-full max-w-5xl mx-auto rounded-3xl shadow-2xl bg-white/90 p-6 md:p-12 flex flex-col gap-8" style="backdrop-filter: blur(2px);">
    <div class="flex flex-col md:flex-row items-center gap-8 md:gap-0">
      <!-- Kiri: Ikon & Hari -->
      <div class="flex flex-col items-center justify-center w-full md:w-1/4 mb-6 md:mb-0">
        <div class="w-20 h-20 rounded-2xl flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-500 shadow-lg mb-4">
          <i class="fa-solid fa-calendar-days text-white text-4xl"></i>
        </div>
        <div class="text-2xl font-extrabold text-gray-800 mb-1"><?php echo $hari_kerja_str; ?></div>
        <div class="text-gray-500 text-base">Setiap hari kerja</div>
      </div>
      <!-- Tengah: Jam Buka -->
      <div class="flex-1 flex flex-col items-center justify-center">
        <div class="bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl shadow-lg px-10 py-6 flex flex-col items-center justify-center">
          <div class="text-5xl md:text-6xl font-extrabold text-white mb-1 tracking-widest"><?php echo date('H:i', strtotime($jam_buka)); ?></div>
          <div class="text-white text-lg font-semibold mb-1">sampai</div>
          <div class="text-5xl md:text-6xl font-extrabold text-white"><?php echo date('H:i', strtotime($jam_tutup)); ?></div>
        </div>
      </div>
      <!-- Kanan: Status & Kontak -->
      <div class="flex flex-col items-center justify-center w-full md:w-1/4 gap-3">
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-100 text-green-700 font-bold text-base mb-2">
          <span class="w-3 h-3 rounded-full bg-green-400 animate-pulse"></span> Buka Sekarang
        </span>
        <div class="text-gray-600 text-base mb-2 text-center">Booking sekarang sebelum penuh!</div>
        <a href="<?php echo $wa_link ? 'https://wa.me/' . htmlspecialchars($wa_link) : '#'; ?>" target="_blank" class="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 text-white font-bold shadow-lg hover:scale-105 transition text-lg">
          <i class="fa-solid fa-phone-volume"></i> Hubungi Kami
        </a>
      </div>
    </div>
    <!-- Divider -->
    <div class="w-full h-px bg-gradient-to-r from-blue-100 via-purple-100 to-pink-100 my-2"></div>
    <!-- Info Libur Minggu di bawah card utama -->
    <?php if ($libur): ?>
    <div class="flex flex-col items-center justify-center mt-2">
      <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-100 text-red-700 font-bold text-base">
        <i class="fa-solid fa-circle-xmark"></i> Libur hari Minggu
      </span>
      <div class="text-red-500 text-sm mt-1 text-center">Tidak menerima les di hari Minggu</div>
    </div>
    <?php endif; ?>
  </div>
  <!-- Dekorasi sudut -->
  <div class="absolute -bottom-4 -left-4 w-8 h-8 bg-gradient-to-br from-pink-400 to-purple-400 rounded-full opacity-30"></div>
  <div class="absolute -top-4 -right-4 w-8 h-8 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full opacity-30"></div>
</section>

    <!-- Paket Bimbel - Design Premium -->
    <section id="paket" class="relative py-20 px-6 overflow-hidden">
        <!-- Background dengan gradient elegan -->
        <div class="absolute inset-0 bg-gradient-to-br from-green-50 via-blue-50 to-purple-50"></div>
        
        <!-- Decorative elements -->
        <div class="absolute top-20 right-20 w-24 h-24 bg-gradient-to-br from-green-200 to-blue-200 rounded-full opacity-20 animate-pulse"></div>
        <div class="absolute bottom-20 left-20 w-20 h-20 bg-gradient-to-br from-purple-200 to-pink-200 rounded-full opacity-20 animate-pulse" style="animation-delay: 1.5s;"></div>
        <div class="absolute top-1/3 right-1/4 w-16 h-16 bg-gradient-to-br from-yellow-200 to-orange-200 rounded-full opacity-20 animate-pulse" style="animation-delay: 2.5s;"></div>
        
        <div class="relative z-10 max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-16">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-blue-600 rounded-full mb-6 shadow-lg">
                    <i class="fa-solid fa-graduation-cap text-white text-2xl"></i>
                </div>
                <h2 class="text-4xl md:text-5xl font-extrabold mb-4 bg-gradient-to-r from-green-600 via-blue-600 to-purple-600 bg-clip-text text-transparent leading-normal pb-2">
                    Paket Bimbel Unggulan
                </h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Pilih jenjang yang sesuai dengan kebutuhan belajar Anda. Kami menyediakan berbagai program bimbingan yang dirancang khusus untuk meraih prestasi akademik terbaik
                </p>
            </div>
            <!-- Jenjang Cards Dinamis -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
              <?php $card_idx = 0; foreach($jenjangs as $j): $grad = $card_gradients[$card_idx % count($card_gradients)]; $bg_card = $grad[0]; $bg_icon = $grad[1]; $card_idx++; ?>
                <div class="relative group">
                  <div class="absolute inset-0 bg-gradient-to-r <?= $bg_card ?> rounded-3xl blur-xl opacity-20"></div>
                  <div class="relative bg-white rounded-3xl shadow-xl p-8 border border-gray-100 transform group-hover:scale-105 transition-all duration-300">
                    <div class="text-center mb-8">
                      <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br <?= $bg_icon ?> rounded-2xl mb-4 shadow-lg transition-all duration-300 group-hover:scale-110 animate-bounce-slow">
                        <i class="fa-solid fa-layer-group text-white text-2xl"></i>
                      </div>
                      <h3 class="text-2xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($j['nama']); ?></h3>
                    </div>
                    <div class="text-center mb-4">
                      <div class="text-gray-600 text-base"><?php echo htmlspecialchars($j['keterangan']); ?></div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
        </div>
    </section>

<?php include "includes/footer.php"; ?>