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
    <nav class="fixed top-0 left-0 w-full bg-[#1976D2] shadow-lg z-50">
        <div class="max-w-6xl mx-auto flex items-center justify-between px-6 py-3">
            <a href="#" class="flex items-center font-extrabold text-2xl text-white tracking-wide hover:opacity-80 transition">
                <img src="assets/img/logo4.png" alt="Logo" class="w-auto h-12 -ml-4 -mt-2 mr-2 object-contain" style="filter: hue-rotate(200deg) saturate(2);">
            </a>
            <div class="space-x-6 text-base font-semibold">
                <a href="#paket" class="hover:text-yellow-300 text-white transition">Paket</a>
                <a href="#jadwal" class="hover:text-yellow-300 text-white transition">Jadwal</a>
                <a href="#galeri" class="hover:text-yellow-300 text-white transition">Galeri</a>
                <a href="#artikel" class="hover:text-yellow-300 text-white transition">Artikel</a>
                <a href="#promo" class="hover:text-yellow-300 text-white transition">Promo</a>
                <a href="#daftar" class="px-5 py-2 bg-gradient-to-b from-yellow-400 to-yellow-600 text-blue-900 font-extrabold rounded-full border-2 border-yellow-200 shadow-xl transition inline-flex items-center gap-2 hover:scale-105 focus:scale-105">
                    <span>Pendaftaran</span>
                    <i class="fa-solid fa-chalkboard-user text-base"></i>
                </a>
                <a href="#login" class="ml-2 px-5 py-2 bg-gradient-to-b from-pink-400 to-pink-600 text-white font-extrabold rounded-full border-2 border-pink-200 shadow-xl transition inline-flex items-center gap-2 hover:scale-105 focus:scale-105">
                    <span>Login</span>
                    <i class="fa-solid fa-right-to-bracket text-base"></i>
                </a>
            </div>
        </div>
    </nav>
    <div class="h-16"></div>

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
                <span class="absolute inset-0 z-0 select-none" aria-hidden="true" style="color:#2563eb; filter: blur(2px); transform: translate(6px,10px); text-shadow:none;">Bimbel Gemma</span>
                <span class="relative z-10">Bimbel Gemma</span>
                <span class="absolute -top-4 -left-8 text-blue-300 text-2xl rotate-[-20deg]">★</span>
                <span class="absolute -top-6 left-1/2 text-blue-400 text-xl rotate-12">★</span>
                <span class="absolute -bottom-4 left-1/3 text-blue-200 text-lg rotate-6">★</span>
            </h1>
            <p class="text-xl md:text-2xl">Bimbingan belajar modern, seru, dan penuh semangat!</p>
            <p class="text-xl md:text-2xl">Daftar sekarang, raih prestasi bersama kami.</p>
            <a href="#daftar" class="inline-flex items-center px-8 py-3 bg-gradient-to-b from-yellow-400 to-yellow-600 text-blue-900 font-extrabold rounded-full border-2 border-yellow-200 shadow-xl shadow-yellow-900/20 text-base gap-3 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 active:translate-y-1 focus:outline-none focus:ring-4 focus:ring-yellow-300 animate-pulse">
                <span>Daftar Sekarang</span>
                <i class="fa-solid fa-chalkboard-user text-xl"></i>
            </a>
        </div>
        <div class="relative flex-1 flex justify-center z-10">
            <img src="assets/img/ilustrasi.svg" alt="Ilustrasi Bimbel" class="w-96 h-96 object-contain rounded-3xl">
        </div>
    </section>

    <!-- Grid Card Fitur Unggulan Overlap -->
    <div class="relative z-30 max-w-6xl mx-auto -mt-24">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            <div class="bg-gradient-to-br from-blue-50 via-white to-blue-100 rounded-2xl shadow-xl p-8 flex flex-col items-center transition transform hover:scale-105 hover:shadow-2xl hover:border-blue-400 border-2 border-transparent group">
                <div class="w-20 h-20 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-blue-600 mb-4 shadow-lg animate-bounce-slow">
                    <i class="fa-solid fa-user-graduate text-white text-4xl"></i>
                </div>
                <h3 class="font-extrabold text-xl mb-2 text-blue-700 group-hover:text-blue-900 transition">Pengajar Terbaik</h3>
                <p class="text-gray-600 text-center">Kami memiliki pengajar berpengalaman dan profesional.</p>
            </div>
            <div class="bg-gradient-to-br from-pink-50 via-white to-pink-100 rounded-2xl shadow-xl p-8 flex flex-col items-center transition transform hover:scale-105 hover:shadow-2xl hover:border-pink-400 border-2 border-transparent group">
                <div class="w-20 h-20 flex items-center justify-center rounded-full bg-gradient-to-br from-pink-400 to-pink-600 mb-4 shadow-lg animate-bounce-slow">
                    <i class="fa-solid fa-comments text-white text-4xl"></i>
                </div>
                <h3 class="font-extrabold text-xl mb-2 text-pink-700 group-hover:text-pink-900 transition">Free Konsultasi</h3>
                <p class="text-gray-600 text-center">Konsultasi akademik & non-akademik gratis untuk siswa.</p>
            </div>
            <div class="bg-gradient-to-br from-yellow-50 via-white to-yellow-100 rounded-2xl shadow-xl p-8 flex flex-col items-center transition transform hover:scale-105 hover:shadow-2xl hover:border-yellow-400 border-2 border-transparent group">
                <div class="w-20 h-20 flex items-center justify-center rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 mb-4 shadow-lg animate-bounce-slow">
                    <i class="fa-solid fa-book-open text-white text-4xl"></i>
                </div>
                <h3 class="font-extrabold text-xl mb-2 text-yellow-700 group-hover:text-yellow-900 transition">Laporan Bulanan</h3>
                <p class="text-gray-600 text-center">Laporan perkembangan dan evaluasi bulanan untuk orang tua.</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 via-white to-purple-100 rounded-2xl shadow-xl p-8 flex flex-col items-center transition transform hover:scale-105 hover:shadow-2xl hover:border-purple-400 border-2 border-transparent group">
                <div class="w-20 h-20 flex items-center justify-center rounded-full bg-gradient-to-br from-purple-400 to-purple-600 mb-4 shadow-lg animate-bounce-slow">
                    <i class="fa-solid fa-award text-white text-4xl"></i>
                </div>
                <h3 class="font-extrabold text-xl mb-2 text-purple-700 group-hover:text-purple-900 transition">Terpercaya</h3>
                <p class="text-gray-600 text-center">Lembaga bimbel terpercaya dengan berbagai program unggulan.</p>
            </div>
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
        <a href="#" target="_blank" class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-tr from-pink-500 to-yellow-500 text-white text-2xl shadow-lg transition social-anim social-ig" aria-label="Instagram">
            <i class="fa-brands fa-instagram"></i>
        </a>
        <a href="https://wa.me/6289529749003" target="_blank" class="w-12 h-12 flex items-center justify-center rounded-full bg-green-500 text-white text-2xl shadow-lg transition social-anim social-wa" aria-label="WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    </div>

    <!-- Floating Back to Top Button -->
    <button onclick="window.scrollTo({top:0,behavior:'smooth'})" class="fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full bg-blue-600 text-white text-2xl shadow-lg flex items-center justify-center hover:bg-blue-700 transition" aria-label="Kembali ke atas">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <!-- Jam Buka Bimbel - Design Elegan -->
    <section id="jadwal" class="relative py-20 px-6 overflow-hidden">
        <!-- Background dengan gradient elegan -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50"></div>
        
        <!-- Decorative elements -->
        <div class="absolute top-10 left-10 w-20 h-20 bg-gradient-to-br from-blue-200 to-purple-200 rounded-full opacity-20 animate-pulse"></div>
        <div class="absolute bottom-10 right-10 w-32 h-32 bg-gradient-to-br from-pink-200 to-yellow-200 rounded-full opacity-20 animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-gradient-to-br from-green-200 to-blue-200 rounded-full opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
        
        <div class="relative z-10 max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-16">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mb-6 shadow-lg">
                    <i class="fa-solid fa-clock text-white text-2xl"></i>
                </div>
                <h2 class="text-4xl md:text-5xl font-extrabold mb-4 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Jam Buka Bimbel
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Kami siap melayani Anda dengan sepenuh hati untuk membantu meraih prestasi akademik yang gemilang
                </p>
            </div>

            <!-- Main Schedule Card -->
            <div class="relative">
                <!-- Glow effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 rounded-3xl blur-xl opacity-20 animate-pulse"></div>
                
                <!-- Main card -->
                <div class="relative bg-white rounded-3xl shadow-2xl p-8 md:p-12 border border-gray-100">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
                        
                        <!-- Left: Icon & Title -->
                        <div class="text-center lg:text-left">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl mb-6 shadow-lg transform hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-calendar-days text-white text-3xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">Senin - Sabtu</h3>
                            <p class="text-gray-600">Setiap hari kerja</p>
                        </div>
                        
                        <!-- Center: Time Display -->
                        <div class="text-center">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
                                <div class="text-4xl md:text-5xl font-bold mb-2">09:00</div>
                                <div class="text-lg opacity-90">sampai</div>
                                <div class="text-4xl md:text-5xl font-bold mt-2">20:00</div>
                            </div>
                        </div>
                        
                        <!-- Right: Status & CTA -->
                        <div class="text-center lg:text-right">
                            <div class="mb-4">
                                <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                    <i class="fa-solid fa-circle text-green-500 mr-2 animate-pulse"></i>
                                    Buka Sekarang
                                </span>
                            </div>
                            <p class="text-gray-600 mb-4">Booking sekarang sebelum penuh!</p>
                            <button class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                                <i class="fa-solid fa-phone mr-2"></i>
                                Hubungi Kami
                            </button>
                        </div>
                    </div>
                    
                    <!-- Bottom: Additional Info -->
                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-users text-blue-500 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-600">Kapasitas Terbatas</p>
                            </div>
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-star text-yellow-500 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-600">Kualitas Terjamin</p>
                            </div>
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-heart text-pink-500 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-600">Pelayanan Ramah</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Floating Elements -->
            <div class="absolute -top-4 -right-4 w-8 h-8 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full animate-bounce"></div>
            <div class="absolute -bottom-4 -left-4 w-6 h-6 bg-gradient-to-br from-pink-400 to-purple-500 rounded-full animate-bounce" style="animation-delay: 0.5s;"></div>
        </div>
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
                    Pilih paket yang sesuai dengan kebutuhan belajar Anda. Kami menyediakan berbagai program bimbingan yang dirancang khusus untuk meraih prestasi akademik terbaik
                </p>
            </div>

            <!-- Paket Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Paket SD -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-blue-500 rounded-3xl blur-xl opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    <div class="relative bg-white rounded-3xl shadow-xl p-8 border border-gray-100 transform group-hover:scale-105 transition-all duration-300">
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl mb-4 shadow-lg">
                                <i class="fa-solid fa-book text-white text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">SD</h3>
                        </div>
                        <div class="text-center mb-6">
                            <div class="text-green-500 font-semibold">Privat</div>
                            <div class="text-3xl font-bold text-green-600 mb-2">100K/bulan</div>
                            <button class="w-full py-2 mt-2 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold rounded-xl shadow hover:from-green-600 hover:to-green-700 transition">Pilih Paket</button>
                        </div>
                        <div class="text-center">
                            <div class="text-blue-500 font-semibold">Kelompok</div>
                            <div class="text-3xl font-bold text-blue-600 mb-2">70K/bulan</div>
                            <button class="w-full py-2 mt-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold rounded-xl shadow hover:from-blue-600 hover:to-blue-700 transition">Pilih Paket</button>
                        </div>
                    </div>
                </div>
                <!-- Paket SMP -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-3xl blur-xl opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    <div class="relative bg-white rounded-3xl shadow-xl p-8 border border-gray-100 transform group-hover:scale-105 transition-all duration-300">
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl mb-4 shadow-lg">
                                <i class="fa-solid fa-user-graduate text-white text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">SMP</h3>
                        </div>
                        <div class="text-center mb-6">
                            <div class="text-cyan-500 font-semibold">Privat</div>
                            <div class="text-3xl font-bold text-cyan-600 mb-2">120K/bulan</div>
                            <button class="w-full py-2 mt-2 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white font-bold rounded-xl shadow hover:from-cyan-600 hover:to-cyan-700 transition">Pilih Paket</button>
                        </div>
                        <div class="text-center">
                            <div class="text-blue-500 font-semibold">Kelompok</div>
                            <div class="text-3xl font-bold text-blue-600 mb-2">90K/bulan</div>
                            <button class="w-full py-2 mt-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold rounded-xl shadow hover:from-blue-600 hover:to-blue-700 transition">Pilih Paket</button>
                        </div>
                    </div>
                </div>
                <!-- Paket SMA -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-400 to-pink-500 rounded-3xl blur-xl opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    <div class="relative bg-white rounded-3xl shadow-xl p-8 border border-gray-100 transform group-hover:scale-105 transition-all duration-300">
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl mb-4 shadow-lg">
                                <i class="fa-solid fa-flask text-white text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">SMA</h3>
                        </div>
                        <div class="text-center mb-6">
                            <div class="text-purple-500 font-semibold">Privat</div>
                            <div class="text-3xl font-bold text-purple-600 mb-2">140K/bulan</div>
                            <button class="w-full py-2 mt-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white font-bold rounded-xl shadow hover:from-purple-600 hover:to-purple-700 transition">Pilih Paket</button>
                        </div>
                        <div class="text-center">
                            <div class="text-pink-500 font-semibold">Kelompok</div>
                            <div class="text-3xl font-bold text-pink-600 mb-2">110K/bulan</div>
                            <button class="w-full py-2 mt-2 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-bold rounded-xl shadow hover:from-pink-600 hover:to-pink-700 transition">Pilih Paket</button>
                        </div>
                    </div>
                </div>
                <!-- Paket Umum -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-3xl blur-xl opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    <div class="relative bg-white rounded-3xl shadow-xl p-8 border border-gray-100 transform group-hover:scale-105 transition-all duration-300">
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl mb-4 shadow-lg">
                                <i class="fa-solid fa-users text-white text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">Umum</h3>
                        </div>
                        <div class="text-center mb-6">
                            <div class="text-yellow-500 font-semibold">Privat</div>
                            <div class="text-3xl font-bold text-yellow-600 mb-2">160K/bulan</div>
                            <button class="w-full py-2 mt-2 bg-gradient-to-r from-yellow-500 to-yellow-600 text-yellow-900 font-bold rounded-xl shadow hover:from-yellow-600 hover:to-yellow-700 transition">Pilih Paket</button>
                        </div>
                        <div class="text-center">
                            <div class="text-orange-500 font-semibold">Kelompok</div>
                            <div class="text-3xl font-bold text-orange-600 mb-2">130K/bulan</div>
                            <button class="w-full py-2 mt-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold rounded-xl shadow hover:from-orange-600 hover:to-orange-700 transition">Pilih Paket</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Box Ketentuan Umum -->
            <div class="mt-20 max-w-4xl mx-auto">
                <div class="relative bg-gradient-to-br from-purple-100 via-blue-50 to-green-50 rounded-3xl shadow-lg border-2 border-blue-100 p-1">
                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 z-10">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 via-pink-400 to-blue-500 rounded-full shadow-xl border-4 border-white">
                            <i class="fa-solid fa-gem text-white text-3xl drop-shadow-lg"></i>
                        </div>
                    </div>
                    <div class="relative bg-white rounded-3xl p-10 pt-20">
                        <h3 class="text-4xl font-extrabold text-center text-transparent bg-clip-text bg-gradient-to-r from-purple-600 via-blue-600 to-green-600 mb-10 tracking-wide drop-shadow-lg leading-tight">Fasilitas & Ketentuan Umum</h3>
                        <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6 text-gray-700 text-lg max-w-2xl mx-auto">
                            <li class="flex items-start gap-3 hover:bg-purple-50 rounded-lg px-4 py-2 transition font-medium text-left">
                                <i class="fa-solid fa-clock text-purple-500 mt-1 text-xl"></i>
                                <span>Durasi les 1 jam/pertemuan</span>
                            </li>
                            <li class="flex items-start gap-3 hover:bg-blue-50 rounded-lg px-4 py-2 transition font-medium text-left">
                                <i class="fa-solid fa-calendar-check text-blue-500 mt-1 text-xl"></i>
                                <span>Jadwal fleksibel sesuai keinginan siswa</span>
                            </li>
                            <li class="flex items-start gap-3 hover:bg-green-50 rounded-lg px-4 py-2 transition font-medium text-left">
                                <i class="fa-solid fa-house-chimney text-green-500 mt-1 text-xl"></i>
                                <span>Ruang belajar nyaman & kondusif</span>
                            </li>
                            <li class="flex items-start gap-3 hover:bg-pink-50 rounded-lg px-4 py-2 transition font-medium text-left">
                                <i class="fa-solid fa-question-circle text-pink-500 mt-1 text-xl"></i>
                                <span>Konsultasi PR & tugas sekolah</span>
                            </li>
                            <li class="flex items-start gap-3 hover:bg-cyan-50 rounded-lg px-4 py-2 transition font-medium text-left">
                                <i class="fa-solid fa-users text-yellow-500 mt-1 text-xl"></i>
                                <span>Kelompok SMP/SMA maksimal 4 orang</span>
                            </li>
                            <li class="flex items-start gap-3 hover:bg-indigo-50 rounded-lg px-4 py-2 transition font-medium text-left">
                                <i class="fa-solid fa-money-bill-wave text-indigo-500 mt-1 text-xl"></i>
                                <span>Pembayaran setiap awal bulan (Cash/TF)</span>
                            </li>
                            <li class="flex items-start gap-3 hover:bg-red-50 rounded-lg px-4 py-2 transition font-medium text-left">
                                <i class="fa-solid fa-school text-red-500 mt-1 text-xl"></i>
                                <span>Pertemuan hanya di bimbel, tidak ke rumah siswa</span>
                            </li>
                            <li class="flex items-start gap-3 hover:bg-teal-50 rounded-lg px-4 py-2 transition font-medium text-left">
                                <i class="fa-solid fa-user-check text-teal-500 mt-1 text-xl"></i>
                                <span>Harga di atas untuk 1 siswa/mapel</span>
                            </li>
                            <li class="flex items-start gap-3 hover:bg-orange-50 rounded-lg px-4 py-2 transition font-medium text-left md:col-span-2">
                                <i class="fa-solid fa-bell text-orange-500 mt-1 text-xl"></i>
                                <span>Izin maksimal 1 jam sebelum les, kurang dari 1 jam tidak dapat diganti</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
            <a href="/daftar-hero" class="inline-flex items-center px-8 py-3 bg-gradient-to-b from-yellow-400 to-yellow-600 text-blue-900 font-extrabold rounded-full border-2 border-yellow-200 shadow-xl shadow-yellow-900/20 text-base gap-3 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 active:translate-y-1 focus:outline-none focus:ring-4 focus:ring-yellow-300 animate-pulse">
                <span>Daftar Sekarang</span>
                <i class="fa-solid fa-chalkboard-user text-xl"></i>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <section class="relative bg-gradient-to-b from-[#1976D2] via-[#1565C0] to-[#0D47A1] text-white pt-8 pb-4 overflow-hidden">
        <div class="max-w-6xl mx-auto flex flex-col items-center gap-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full">
                <!-- Alamat -->
                <div class="flex flex-col items-center">
                    <div class="font-bold text-xl mb-2">Alamat Bimbel</div>
                    <div class="w-12 h-1 bg-blue-200 rounded-full mb-2"></div>
                    <div class="text-center text-blue-100">JL. Dworowati No.5 Randuares RT.07/RW.01 Kumpulreo, Argomulyo, Salatiga, 50734</div>
                </div>
                <!-- Media Sosial -->
                <div class="flex flex-col items-center">
                    <div class="flex flex-col items-center mb-8">
                        <img src="assets/img/logo4.png" alt="Logo Bimbel Gemma" class="w-auto h-12 mb-2">
                    </div>
                    <div class="font-bold text-xl mb-2">Media Sosial</div>
                    <div class="flex gap-4 mt-2">
                        <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-blue-600 text-xl hover:bg-blue-200 transition">
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
                                <a href="https://wa.me/6289529749003" target="_blank" rel="noopener">
                                    0895-2974-9003
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