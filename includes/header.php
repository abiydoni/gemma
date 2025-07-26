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
        
        /* Custom SweetAlert2 Styles */
        .swal2-custom-popup {
            border-radius: 20px !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }
        
        .swal2-custom-title {
            padding: 0 !important;
            margin-bottom: 0 !important;
        }
        
        .swal2-custom-html {
            padding: 0 !important;
        }
        
        .swal2-custom-confirm {
            border-radius: 12px !important;
            font-weight: 600 !important;
            padding: 12px 24px !important;
            transition: all 0.3s ease !important;
        }
        
        .swal2-custom-confirm:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3) !important;
        }
        
        .swal2-custom-cancel {
            border-radius: 12px !important;
            font-weight: 600 !important;
            padding: 12px 24px !important;
            transition: all 0.3s ease !important;
        }
        
        .swal2-custom-cancel:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 10px 25px rgba(107, 114, 128, 0.3) !important;
        }
        
        /* Input focus effects */
        .swal2-custom-html input:focus {
            transform: scale(1.02) !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
        
        /* Loading animation */
        .swal2-loading {
            border-radius: 50% !important;
            border: 3px solid #f3f4f6 !important;
            border-top: 3px solid #3b82f6 !important;
            animation: spin 1s linear infinite !important;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
        title: '<div class="flex items-center justify-center gap-3 mb-4"><div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg"><i class="fa-solid fa-user-graduate text-white text-xl"></i></div><span class="text-2xl font-bold text-gray-800">Cek Data Siswa</span></div>',
        html: `
          <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-6 border border-blue-100 shadow-lg">
            <div class="space-y-6">
              <!-- Email Input -->
              <div class="relative group">
                <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                  <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-envelope text-white text-sm"></i>
                  </div>
                  Email Siswa
                </label>
                <div class="relative">
                  <input type="email" id="swal-email" 
                         class="w-full px-4 py-4 pl-12 border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300 bg-white shadow-sm group-hover:shadow-md"
                         placeholder="Masukkan email yang terdaftar">
                  <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400">
                    <i class="fa-solid fa-at"></i>
                  </div>
                </div>
              </div>
              
              <!-- Tanggal Lahir Input -->
              <div class="relative group">
                <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                  <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-calendar-days text-white text-sm"></i>
                  </div>
                  Tanggal Lahir
                </label>
                <div class="relative">
                  <input type="text" id="swal-tgl-lahir" 
                         class="w-full px-4 py-4 pl-12 border-2 border-purple-200 rounded-xl focus:border-purple-500 focus:outline-none transition-all duration-300 bg-white shadow-sm group-hover:shadow-md"
                         placeholder="DDMMYYYY (contoh: 15012010)"
                         maxlength="8">
                  <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-400">
                    <i class="fa-solid fa-calendar"></i>
                  </div>
                </div>
                <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                  <i class="fa-solid fa-info-circle text-blue-500"></i>
                  Format: DDMMYYYY (tanpa spasi atau tanda hubung)
                </p>
              </div>
              
              <!-- Info Card -->
              <div class="bg-gradient-to-r from-blue-100 to-purple-100 rounded-xl p-4 border border-blue-200">
                <div class="flex items-start gap-3">
                  <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-lightbulb text-white text-sm"></i>
                  </div>
                  <div class="text-sm text-gray-700">
                    <p class="font-semibold text-gray-800 mb-1">Tips Pencarian:</p>
                    <ul class="space-y-1 text-xs">
                      <li>• Pastikan email yang digunakan sudah terdaftar</li>
                      <li>• Tanggal lahir harus sesuai dengan data pendaftaran</li>
                      <li>• Format tanggal: DDMMYYYY (contoh: 15012010)</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-search mr-2"></i>Cek Data',
        cancelButtonText: '<i class="fa-solid fa-times mr-2"></i>Batal',
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        customClass: {
          popup: 'swal2-custom-popup',
          title: 'swal2-custom-title',
          htmlContainer: 'swal2-custom-html',
          confirmButton: 'swal2-custom-confirm',
          cancelButton: 'swal2-custom-cancel'
        },
        preConfirm: () => {
          const email = document.getElementById('swal-email').value.trim();
          const tglLahir = document.getElementById('swal-tgl-lahir').value.trim();
          
          if (!email) {
            Swal.showValidationMessage('Email wajib diisi!');
            return false;
          }
          if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
            Swal.showValidationMessage('Format email tidak valid!');
            return false;
          }
          if (!tglLahir) {
            Swal.showValidationMessage('Tanggal lahir wajib diisi!');
            return false;
          }
          if (!/^\d{8}$/.test(tglLahir)) {
            Swal.showValidationMessage('Format tanggal lahir harus DDMMYYYY (8 digit angka)!');
            return false;
          }
          
          // Validasi tanggal yang masuk akal
          const day = parseInt(tglLahir.substring(0, 2));
          const month = parseInt(tglLahir.substring(2, 4));
          const year = parseInt(tglLahir.substring(4, 8));
          
          if (day < 1 || day > 31 || month < 1 || month > 12 || year < 1900 || year > new Date().getFullYear()) {
            Swal.showValidationMessage('Tanggal lahir tidak valid!');
            return false;
          }
          
          return { email, tglLahir };
        },
        didOpen: () => {
          // Format input tanggal lahir
          document.getElementById('swal-tgl-lahir').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Hanya angka
            if (value.length > 8) {
              value = value.substring(0, 8);
            }
            e.target.value = value;
          });
        }
      }).then((result) => {
        if(result.isConfirmed && result.value) {
          const { email, tglLahir } = result.value;
          
          // Tampilkan loading
          Swal.fire({
            title: '<div class="flex items-center justify-center gap-3"><div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center animate-spin"><i class="fa-solid fa-search text-white text-sm"></i></div><span class="text-lg font-semibold text-gray-800">Mencari Data Siswa...</span></div>',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            showConfirmButton: false,
            customClass: {
              popup: 'swal2-custom-popup',
              title: 'swal2-custom-title'
            }
          });
          
          // Kirim request ke API untuk validasi
          fetch('api/cek_siswa.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(email)}&tgl_lahir=${encodeURIComponent(tglLahir)}`
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'ok') {
              Swal.fire({
                title: '<div class="flex items-center justify-center gap-3"><div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-lg"><i class="fa-solid fa-check text-white text-xl"></i></div><span class="text-xl font-bold text-gray-800">Data Ditemukan!</span></div>',
                html: `
                  <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200 shadow-lg">
                    <div class="text-center">
                      <div class="text-2xl font-bold text-green-700 mb-2">Selamat datang!</div>
                      <div class="text-lg font-semibold text-gray-800 mb-4">${data.data.nama}</div>
                      <div class="text-sm text-gray-600">Email: ${data.data.email}</div>
                    </div>
                  </div>
                `,
                confirmButtonText: '<i class="fa-solid fa-eye mr-2"></i>Lihat Detail',
                showCancelButton: true,
                cancelButtonText: '<i class="fa-solid fa-times mr-2"></i>Batal',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                customClass: {
                  popup: 'swal2-custom-popup',
                  title: 'swal2-custom-title',
                  htmlContainer: 'swal2-custom-html',
                  confirmButton: 'swal2-custom-confirm',
                  cancelButton: 'swal2-custom-cancel'
                }
              }).then((result) => {
                if (result.isConfirmed) {
                  // Redirect ke detail siswa
                  window.location.href = `detail_siswa.php?email=${encodeURIComponent(email)}`;
                }
              });
            } else {
              Swal.fire({
                title: '<div class="flex items-center justify-center gap-3"><div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg"><i class="fa-solid fa-exclamation-triangle text-white text-xl"></i></div><span class="text-xl font-bold text-gray-800">Data Tidak Ditemukan!</span></div>',
                html: `
                  <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-6 border border-red-200 shadow-lg">
                    <div class="text-center">
                      <div class="text-lg font-semibold text-red-700 mb-2">Oops!</div>
                      <div class="text-sm text-gray-700 mb-4">${data.message || 'Email atau tanggal lahir tidak sesuai dengan data yang terdaftar'}</div>
                      <div class="text-xs text-gray-500">
                        <p>• Pastikan email yang digunakan sudah terdaftar</p>
                        <p>• Tanggal lahir harus sesuai dengan data pendaftaran</p>
                      </div>
                    </div>
                  </div>
                `,
                confirmButtonText: '<i class="fa-solid fa-check mr-2"></i>OK',
                confirmButtonColor: '#ef4444',
                customClass: {
                  popup: 'swal2-custom-popup',
                  title: 'swal2-custom-title',
                  htmlContainer: 'swal2-custom-html',
                  confirmButton: 'swal2-custom-confirm'
                }
              });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              title: '<div class="flex items-center justify-center gap-3"><div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center shadow-lg"><i class="fa-solid fa-exclamation-circle text-white text-xl"></i></div><span class="text-xl font-bold text-gray-800">Error!</span></div>',
              html: `
                <div class="bg-gradient-to-br from-orange-50 to-yellow-50 rounded-2xl p-6 border border-orange-200 shadow-lg">
                  <div class="text-center">
                    <div class="text-lg font-semibold text-orange-700 mb-2">Terjadi Kesalahan</div>
                    <div class="text-sm text-gray-700 mb-4">Gagal menghubungi server. Silakan coba lagi.</div>
                    <div class="text-xs text-gray-500">
                      <p>• Periksa koneksi internet Anda</p>
                      <p>• Coba refresh halaman</p>
                    </div>
                  </div>
                </div>
              `,
              confirmButtonText: '<i class="fa-solid fa-refresh mr-2"></i>Coba Lagi',
              confirmButtonColor: '#f97316',
              customClass: {
                popup: 'swal2-custom-popup',
                title: 'swal2-custom-title',
                htmlContainer: 'swal2-custom-html',
                confirmButton: 'swal2-custom-confirm'
              }
            });
          });
        }
      });
    });
  }
});
</script>
