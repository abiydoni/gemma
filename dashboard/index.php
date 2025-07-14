<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit;
}
$user_nama = $_SESSION['user_nama'] ?? ($_SESSION['user_email'] ?? '');
$user_role = $_SESSION['user_role'] ?? '';
?>
<body class="flex min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100">
  <?php include 'sidebar.php'; ?>
  <div class="flex-1 flex flex-col min-h-screen ml-0 md:ml-64">
    <?php include 'header.php'; ?>
    <main class="flex-1 p-4 md:p-10">
      <section class="max-w-7xl mx-auto py-4 md:py-10 px-2 md:px-4">
        <div class="mb-10 text-left">
          <h1 class="text-3xl md:text-4xl font-extrabold text-blue-800 mb-2 flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 shadow-lg">
              <i class="fa-solid fa-gauge-high text-white text-2xl"></i>
            </span>
            Dashboard
          </h1>
          <div class="text-xl text-gray-700 font-medium flex items-center gap-3 flex-wrap">
            Selamat datang, <span class="font-bold text-blue-700 text-2xl drop-shadow"> <?= htmlspecialchars($user_nama) ?> </span>
            <span class="inline-block px-3 py-1 rounded-full bg-gradient-to-r from-pink-500 to-pink-300 text-white text-xs font-bold shadow ml-2"> <?= htmlspecialchars($user_role) ?> </span>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
          <div class="bg-gradient-to-br from-blue-100 via-white to-blue-200 rounded-3xl shadow-xl p-8 flex flex-col items-center transition transform hover:scale-105 hover:shadow-2xl cursor-pointer">
            <div class="w-16 h-16 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-blue-700 mb-4 shadow-lg">
              <i class="fa-solid fa-users text-white text-3xl"></i>
            </div>
            <div class="text-4xl font-extrabold text-blue-700 mb-1">120</div>
            <div class="text-gray-600 font-semibold">Total Siswa</div>
          </div>
          <div class="bg-gradient-to-br from-pink-100 via-white to-pink-200 rounded-3xl shadow-xl p-8 flex flex-col items-center transition transform hover:scale-105 hover:shadow-2xl cursor-pointer">
            <div class="w-16 h-16 flex items-center justify-center rounded-full bg-gradient-to-br from-pink-500 to-pink-600 mb-4 shadow-lg">
              <i class="fa-solid fa-user-shield text-white text-3xl"></i>
            </div>
            <div class="text-4xl font-extrabold text-pink-700 mb-1">8</div>
            <div class="text-gray-600 font-semibold">Total User</div>
          </div>
          <div class="bg-gradient-to-br from-yellow-100 via-white to-yellow-200 rounded-3xl shadow-xl p-8 flex flex-col items-center transition transform hover:scale-105 hover:shadow-2xl cursor-pointer">
            <div class="w-16 h-16 flex items-center justify-center rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 mb-4 shadow-lg">
              <i class="fa-solid fa-book text-white text-3xl"></i>
            </div>
            <div class="text-4xl font-extrabold text-yellow-700 mb-1">15</div>
            <div class="text-gray-600 font-semibold">Total Mapel</div>
          </div>
        </div>
        <!-- Area konten dinamis lain bisa ditambahkan di sini -->
      </section>
    </main>
    <?php include 'footer.php'; ?>
  </div>
</body>
<style>
body {
  background: linear-gradient(135deg, #f0f6ff 0%, #e0e7ff 100%);
}
</style> 