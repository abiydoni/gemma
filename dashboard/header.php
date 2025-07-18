<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit;
}
$user_nama = $_SESSION['user_nama'] ?? ($_SESSION['user_email'] ?? '');
$user_role = $_SESSION['user_role'] ?? '';
include '../api/db.php';
$total_siswa = 0;
try {
  $stmt = $pdo->query('SELECT COUNT(email) as jumlah FROM tb_siswa');
  $row = $stmt->fetch();
  $total_siswa = $row ? $row['jumlah'] : 0;
} catch(Exception $e) {}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    @keyframes fadeInUp { from { opacity:0; transform: translateY(40px);} to { opacity:1; transform: none; } }
    .animate-fadeInUp { animation: fadeInUp 0.8s cubic-bezier(.4,2,.3,1) both; }
    .sidebar-active { background: linear-gradient(90deg, #2563eb22 60%, #fff0 100%); color: #fff; font-weight: bold; }
  </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 font-sans min-h-screen relative overflow-x-hidden">
  <!-- Dekorasi background -->
  <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-gradient-to-br from-blue-200 via-white to-pink-100 rounded-full blur-3xl opacity-40 -z-10"></div>
  <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-br from-yellow-100 via-white to-blue-100 rounded-full blur-2xl opacity-30 -z-10"></div>

  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 h-screen w-64 bg-blue-800 text-white flex flex-col shadow-2xl z-30 overflow-y-auto">
      <div class="p-6 text-center font-extrabold text-2xl tracking-wide flex items-center justify-center gap-2">
        <img src="../assets/img/logo4.png" alt="Logo" class="w-auto h-20 object-contain p-1">
      </div>
      <nav class="flex-grow p-4 space-y-2">
        <div class="uppercase text-xs font-bold text-blue-200 mt-2 mb-2 tracking-widest">Menu Utama</div>
        <a href="/dashboard/" class="flex items-center space-x-3 rounded-lg px-4 py-1 text-sm transition hover:bg-blue-700/80 sidebar-active">
          <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
        </a>
        <a href="siswa.php" class="flex items-center space-x-3 rounded-lg px-4 py-1 text-sm transition hover:bg-blue-700/80">
          <i class="fa-solid fa-user-graduate"></i><span>Data Siswa</span>
        </a>
        <a href="jadwal.php" class="flex items-center space-x-3 rounded-lg px-4 py-1 text-sm transition hover:bg-blue-700/80">
          <i class="fa-solid fa-calendar-days"></i><span>Jadwal Les</span>
        </a>
        <div class="uppercase text-xs font-bold text-blue-200 mt-6 mb-2 tracking-widest">Master Data</div>
        <a href="paket.php" class="flex items-center space-x-3 rounded-lg px-4 py-1 text-sm transition hover:bg-blue-700/80">
          <i class="fa-solid fa-box-open"></i><span>Data Paket</span>
        </a>
        <a href="mapel.php" class="flex items-center space-x-3 rounded-lg px-4 py-1 text-sm transition hover:bg-blue-700/80">
          <i class="fa-solid fa-book-open-reader"></i><span>Data Mata Pelajaran</span>
        </a>
        <a href="user.php" class="flex items-center space-x-3 rounded-lg px-4 py-1 text-sm transition hover:bg-blue-700/80">
          <i class="fa-solid fa-user-gear"></i><span>Data User</span>
        </a>
        <div class="uppercase text-xs font-bold text-blue-200 mt-6 mb-2 tracking-widest">Laporan</div>
        <a href="keuangan.php" class="flex items-center space-x-3 rounded-lg px-4 py-1 text-sm transition hover:bg-blue-700/80">
          <i class="fa-solid fa-cash-register"></i><span>Keuangan</span>
        </a>
        <div class="uppercase text-xs font-bold text-blue-200 mt-6 mb-2 tracking-widest">Setting</div>
        <a href="setting_fasilitas.php" class="flex items-center space-x-3 rounded-lg px-4 py-1 text-sm transition hover:bg-blue-700/80">
          <i class="fa-solid fa-building-wheat"></i><span>Setting Fasilitas</span>
        </a>
        <a href="setting_jadwal.php" class="flex items-center space-x-3 rounded-lg px-4 py-1 text-sm transition hover:bg-blue-700/80">
          <i class="fa-solid fa-clock-rotate-left"></i><span>Setting Jadwal</span>
        </a>
        <a href="jenjang.php" class="flex items-center space-x-3 rounded-lg px-4 py-1 text-sm transition hover:bg-blue-700/80">
          <i class="fa-solid fa-layer-group"></i><span>Setting Jenjang</span>
        </a>
        <a href="kondisi.php" class="flex items-center space-x-3 rounded-lg px-4 py-1 text-sm transition hover:bg-blue-700/80">
          <i class="fa-solid fa-flag"></i><span>Setting Kondisi</span>
        </a>
        <a href="profile.php" class="flex items-center space-x-3 rounded-lg px-4 py-1 text-sm transition hover:bg-blue-700/80">
          <i class="fa-solid fa-user-cog"></i><span>Profile</span>
        </a>
      </nav>
    </aside>

    <div class="flex-1 flex flex-col min-h-0 ml-0 md:ml-64">
      <!-- Navbar User Info -->
      <nav class="w-full flex items-center justify-end px-8 py-4 bg-white/80 shadow-sm relative z-10">
        <!-- Dropdown User di Navbar -->
        <div class="relative inline-block text-left float-right mr-4 mt-2">
          <button id="dropdownUserBtn" class="flex items-center gap-2 px-3 py-2 rounded-full bg-blue-100 hover:bg-blue-200 text-blue-800 font-bold focus:outline-none">
            <img src="../assets/img/profile/default.png" class="w-8 h-8 rounded-full border-2 border-blue-300" alt="User">
            <span><?= htmlspecialchars($_SESSION['user_nama'] ?? 'User') ?></span>
            <i class="fa fa-chevron-down"></i>
          </button>
          <div id="dropdownUserMenu" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg z-50 border border-blue-100 overflow-hidden">
            <a href="#" id="menu-edit-profil" class="flex items-center gap-2 px-5 py-3 text-blue-700 hover:bg-blue-50 font-semibold border-b"><i class="fa fa-user-edit"></i> Edit Profil</a>
            <a href="#" id="menu-ubah-password" class="flex items-center gap-2 px-5 py-3 text-blue-700 hover:bg-blue-50 font-semibold border-b"><i class="fa fa-key"></i> Ubah Password</a>
            <button onclick="logout()" class="w-full text-left flex items-center gap-2 px-5 py-3 text-red-600 hover:bg-red-50 font-semibold"><i class="fa fa-sign-out-alt"></i> Logout</button>
          </div>
        </div>
      </nav>
      <!-- Konten Utama -->
      <main class="flex-grow p-8 md:p-12 overflow-y-auto pt-32">