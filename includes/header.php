<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bimbel Gemma - Bimbingan Belajar Modern</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        @keyframes social-rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .social-anim {
            animation: social-rotate 2.5s linear infinite;
            transition: background 0.3s, transform 0.3s;
        }
        .social-anim:hover, .social-anim:focus, .social-anim:active {
            transform: scale(1.1) rotate(0deg) !important;
            animation-play-state: paused;
        }
        .social-ig:hover, .social-ig:focus, .social-ig:active {
            background: linear-gradient(135deg, #be185d, #f59e42) !important;
        }
        .social-fb:hover, .social-fb:focus, .social-fb:active {
            background-color: #1e3a8a !important;
        }
        .social-wa:hover, .social-wa:focus, .social-wa:active {
            background-color: #047857 !important;
        }
        @keyframes logo-rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .navbar-logo-anim {
            animation: logo-rotate 6s linear infinite;
            transition: transform 0.3s;
        }
        .navbar-logo-anim:hover, .navbar-logo-anim:focus {
            animation-play-state: paused;
            transform: scale(1.1) rotate(0deg) !important;
        }
        @keyframes fitur-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.18); }
        }
        .fitur-bounce {
            animation: fitur-pulse 1.6s infinite;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 via-pink-50 to-yellow-50 min-h-screen text-gray-800">
    <!-- Navbar -->
    <nav id="navbar" class="fixed top-0 left-0 w-full bg-[#1976D2] shadow-lg z-50">
        <div class="max-w-6xl mx-auto flex items-center justify-between px-6 py-3">
            <a href="/" class="flex items-center font-extrabold text-2xl text-white tracking-wide hover:opacity-80 transition">
                <?php
                include_once __DIR__ . '/../api/db.php';
                $profile = [
                  'nama' => 'Bimbel Gemma',
                  'alamat' => '',
                  'ig' => '',
                  'wa' => '',
                  'keterangan' => 'Bimbingan belajar modern, seru, dan penuh semangat!'
                ];
                try {
                  $stmt = $pdo->query('SELECT nama, alamat, ig, wa, keterangan, logo1 FROM tb_profile LIMIT 1');
                  $row = $stmt->fetch(PDO::FETCH_ASSOC);
                  if ($row) {
                    foreach ($profile as $k => $v) {
                      if (!empty($row[$k])) $profile[$k] = $row[$k];
                    }
                    $logo1 = (isset($row['logo1']) && $row['logo1'] && strpos($row['logo1'], '/') === false) ? 'assets/img/' . $row['logo1'] : $row['logo1'];
                    if (empty($logo1)) $logo1 = 'assets/img/logo4.png';
                  }
                } catch (Exception $e) {}
                ?>
                <img src="<?= htmlspecialchars($logo1) ?>" alt="Logo" class="w-auto h-12 -ml-4 -mt-2 mr-2 object-contain" style="filter: hue-rotate(200deg) saturate(2);">
            </a>
            <div class="space-x-6 text-base font-semibold">
                <a href="#paket" class="hover:text-yellow-300 text-white transition">Paket</a>
                <a href="#jadwal" class="hover:text-yellow-300 text-white transition">Jadwal</a>
                <!-- <a href="#galeri" class="hover:text-yellow-300 text-white transition">Galeri</a>
                <a href="#artikel" class="hover:text-yellow-300 text-white transition">Artikel</a> -->
                <a href="#promo" class="hover:text-yellow-300 text-white transition">Siswa</a>
                <a href="login.php" class="ml-2 px-5 py-2 bg-gradient-to-b from-pink-400 to-pink-600 text-white font-extrabold rounded-full border-2 border-pink-200 shadow-xl transition inline-flex items-center gap-2 hover:scale-105 focus:scale-105">
                    <span>Login</span>
                    <i class="fa-solid fa-right-to-bracket text-base"></i>
                </a>
            </div>
        </div>
    </nav>
    <div class="h-16"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var menuSiswa = document.querySelector('a[href="#promo"], a[href="#siswa"]');
  if(menuSiswa) {
    menuSiswa.addEventListener('click', function(e) {
      e.preventDefault();
      Swal.fire({
        title: 'Cek Data Siswa',
        input: 'email',
        inputLabel: 'Masukkan Email Siswa',
        inputPlaceholder: 'Email aktif siswa',
        showCancelButton: true,
        confirmButtonText: 'Cek',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
          if (!value) return 'Email wajib diisi!';
          if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(value)) return 'Format email tidak valid!';
        }
      }).then((result) => {
        if(result.isConfirmed && result.value) {
          var email = result.value;
          fetch('api/cek_email.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'email=' + encodeURIComponent(email)
          })
          .then(r => r.json())
          .then(data => {
            if(data.status === 'found') {
              window.location.href = 'detail_siswa.php?email=' + encodeURIComponent(email);
            } else {
              Swal.fire({
                icon: 'warning',
                title: 'Email tidak ditemukan',
                text: 'Email belum terdaftar. Ingin mendaftar sebagai siswa baru?',
                showCancelButton: true,
                confirmButtonText: 'Daftar',
                cancelButtonText: 'Batal'
              }).then((r2) => {
                if(r2.isConfirmed) window.location.href = 'daftar.php';
              });
            }
          })
          .catch(() => {
            Swal.fire('Error','Gagal menghubungi server!','error');
          });
        }
      });
    });
  }
});
</script>
