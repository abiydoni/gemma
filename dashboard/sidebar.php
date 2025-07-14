<aside class="fixed top-0 left-0 h-full w-64 bg-blue-800 text-white shadow-lg flex flex-col z-40 transition-transform duration-300 md:translate-x-0 -translate-x-full md:relative md:top-auto md:left-auto md:h-auto md:w-64" id="sidebar">
  <div class="flex items-center gap-3 px-6 py-8 border-b border-blue-700">
    <img src="../assets/img/logo4.png" alt="Logo" class="w-12 h-12 object-contain rounded-full bg-white p-1">
    <span class="font-extrabold text-xl tracking-wide">Admin Panel</span>
  </div>
  <nav class="flex-1 flex flex-col gap-1 mt-4">
    <a href="index.php" class="flex items-center gap-3 px-6 py-3 hover:bg-blue-700 transition rounded-r-full font-semibold">
      <i class="fa-solid fa-gauge-high"></i> Dashboard
    </a>
    <a href="#" class="flex items-center gap-3 px-6 py-3 hover:bg-blue-700 transition rounded-r-full font-semibold">
      <i class="fa-solid fa-users"></i> Data Siswa
    </a>
    <a href="#" class="flex items-center gap-3 px-6 py-3 hover:bg-blue-700 transition rounded-r-full font-semibold">
      <i class="fa-solid fa-user-shield"></i> Data User
    </a>
    <div class="flex-1"></div>
    <a href="logout.php" class="flex items-center gap-3 px-6 py-3 hover:bg-red-600 transition rounded-r-full font-semibold mb-6">
      <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
  </nav>
</aside>
<style>
@media (max-width: 768px) {
  #sidebar { position: fixed; left: 0; top: 0; height: 100vh; z-index: 50; }
}
</style> 