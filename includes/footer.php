<?php
include_once __DIR__ . '/../api/db.php';
$logo1 = 'assets/img/logo4.png';
try {
  $stmt = $pdo->query('SELECT logo1 FROM tb_profile LIMIT 1');
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row && !empty($row['logo1'])) {
    $logo1 = (strpos($row['logo1'], '/') === false) ? 'assets/img/' . $row['logo1'] : $row['logo1'];
  }
} catch (Exception $e) {}

if (!isset($wa)) {
  $wa = isset($profile['wa']) ? $profile['wa'] : '';
  $wa_link = $wa;
  if (strpos($wa, '08') === 0) {
      $wa_link = '62' . substr($wa, 1);
  }
  $wa_link = preg_replace('/[^0-9]/', '', $wa_link);
}

if (!isset($alamat)) {
  $alamat = isset($profile['alamat']) ? $profile['alamat'] : '';
}

$ig = isset($profile['ig']) ? $profile['ig'] : '';
$ig_link = $ig;
if ($ig && strpos($ig, 'http') !== 0) {
    $ig_link = 'https://instagram.com/' . ltrim($ig, '@');
}
?>
    <!-- Section Ajakan Menjadi Siswa -->
    <section id="hero" class="relative py-8 px-6 flex flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-blue-700 via-blue-500 to-blue-300 text-white" style="min-height: 200px;">
        <!-- Slideshow Background (sama seperti hero) -->
        <div id="hero-bg1" class="absolute inset-0 bg-center bg-cover z-0" style="background-image: url('assets/img/bg1.jpg'); opacity: 1; transition: opacity 1.5s;"></div>
        <div id="hero-bg2" class="absolute inset-0 bg-center bg-cover z-0" style="background-image: url('assets/img/bg2.jpg'); opacity: 0; transition: opacity 1.5s;"></div>
        <div id="hero-bg3" class="absolute inset-0 bg-center bg-cover z-0" style="background-image: url('assets/img/bg3.jpg'); opacity: 0; transition: opacity 1.5s;"></div>
        <div id="hero-bg4" class="absolute inset-0 bg-center bg-cover z-0" style="background-image: url('assets/img/bg4.jpg'); opacity: 0; transition: opacity 1.5s;"></div>
        <div id="hero-bg5" class="absolute inset-0 bg-center bg-cover z-0" style="background-image: url('assets/img/bg5.jpg'); opacity: 0; transition: opacity 1.5s;"></div>
        
        <!-- Overlay Gradasi Biru Metalik -->
        <div class="absolute inset-0 z-0 pointer-events-none" style="background: linear-gradient(135deg,rgba(25,118,210,0.85),rgba(59,130,246,0.7));"></div>
        
        <div class="relative z-10 flex flex-col items-center text-center space-y-4">
            <h2 class="text-2xl md:text-3xl font-extrabold mb-1 drop-shadow-lg">Anda Berminat Menjadi Siswa di Bimbel Gemma?</h2>
            <p class="text-base md:text-lg mb-4 font-medium">Bergabunglah bersama Bimbel Gemma untuk mewujudkan prestasi lebih baik!</p>
            <a href="daftar.php" class="inline-flex items-center px-8 py-3 bg-gradient-to-b from-yellow-400 to-yellow-600 text-blue-900 font-extrabold rounded-full border-2 border-yellow-200 shadow-xl shadow-yellow-900/20 text-base gap-3 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 active:translate-y-1 focus:outline-none focus:ring-4 focus:ring-yellow-300 animate-pulse">
                <span>Daftar Sekarang</span>
                <i class="fa-solid fa-chalkboard-user text-xl"></i>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <section id="footer" class="relative bg-gradient-to-b from-[#1976D2] via-[#1565C0] to-[#0D47A1] text-white pt-8 pb-4 overflow-hidden">
        <div class="max-w-6xl mx-auto flex flex-col items-center gap-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full">
                <!-- Alamat -->
                <div class="flex flex-col items-center">
                    <div class="font-bold text-xl mb-2">Alamat Bimbel</div>
                    <div class="w-12 h-1 bg-blue-200 rounded-full mb-2"></div>
                    <div class="text-center text-blue-100"><?= htmlspecialchars($alamat) ?></div>
                </div>
                <!-- Media Sosial -->
                <div class="flex flex-col items-center">
                    <div class="flex flex-col items-center mb-8">
                        <img src="<?= htmlspecialchars($logo1) ?>" alt="Logo" class="w-auto h-16 object-contain mx-auto mb-2">
                    </div>
                    <div class="font-bold text-xl mb-2">Media Sosial</div>
                    <div class="flex gap-4 mt-2">
                        <a href="<?= htmlspecialchars($ig_link) ?>" class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-blue-600 text-xl hover:bg-blue-200 transition">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    </div>
                </div>
                <!-- Kontak -->
                <div class="flex flex-col items-center">
                    <div class="font-bold text-xl mb-2">Kontak</div>
                    <div class="flex gap-4 mt-2">
                        <div class="bg-white text-blue-700 rounded-xl px-4 py-2 font-bold text-center shadow-md">
                            <div>Whatsapp :</div>
                            <div>
                                <a href="https://wa.me/<?= htmlspecialchars($wa_link) ?>" target="_blank" rel="noopener">
                                <?= htmlspecialchars($wa) ?>
                                </a>            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full border-t border-blue-300 mt-8 pt-4 text-center text-blue-100 text-sm">
                &copy; 2025 Bimbel Gemma. All Rights Reserved by: appsBee.
            </div>
        </div>
    </section>

    <!-- JavaScript -->
    <script>
        // Slideshow background hero
        const heroBgEls = [
            document.getElementById('hero-bg1'),
            document.getElementById('hero-bg2'),
            document.getElementById('hero-bg3'),
            document.getElementById('hero-bg4'),
            document.getElementById('hero-bg5')
        ];
        let heroIdx = 0;
        setInterval(() => {
            heroBgEls.forEach((el, i) => el.style.opacity = (i === heroIdx ? '1' : '0'));
            heroIdx = (heroIdx + 1) % heroBgEls.length;
        }, 4000);
    </script>
</body>
</html> 