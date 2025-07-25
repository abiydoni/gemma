<?php include "header.php"; ?>

        <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 mb-6 flex items-center gap-3 drop-shadow animate-fadeInUp">
          <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 shadow-xl animate-pulse">
            <i class="fa-solid fa-gauge-high text-white text-xl"></i>
          </span>
          Dashboard
        </h1>
        <!-- Tombol Laporan Perkembangan Siswa -->
        <div class="mb-6 flex justify-end">
          <a href="laporan_perkembangan.php" class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow transition">
            <i class="fa-solid fa-file-lines"></i>
            Laporan Perkembangan Siswa
          </a>
        </div>
        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div class="bg-white p-4 rounded-2xl shadow hover:shadow-lg transition transform hover:-translate-y-1 animate-fadeInUp flex flex-col items-center group">
            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-blue-700 mb-3 shadow group-hover:scale-105 transition animate-pulse">
              <i class="fas fa-users text-white text-2xl"></i>
            </div>
            <div class="text-3xl font-extrabold text-blue-700 mb-1 drop-shadow" id="totalSiswa">0</div>
            <div class="text-base text-gray-600 font-semibold tracking-wide">Total Siswa</div>
          </div>
          <div class="bg-white p-4 rounded-2xl shadow hover:shadow-lg transition transform hover:-translate-y-1 animate-fadeInUp flex flex-col items-center group" style="animation-delay:0.1s;">
            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-green-600 mb-3 shadow group-hover:scale-105 transition animate-pulse">
              <i class="fas fa-chart-pie text-white text-2xl"></i>
            </div>
            <div class="text-3xl font-extrabold text-green-600 mb-1 drop-shadow">36</div>
            <div class="text-base text-gray-600 font-semibold tracking-wide">Laporan Bulanan</div>
          </div>
          <div class="bg-white p-4 rounded-2xl shadow hover:shadow-lg transition transform hover:-translate-y-1 animate-fadeInUp flex flex-col items-center group" style="animation-delay:0.2s;">
            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 mb-3 shadow group-hover:scale-105 transition animate-pulse">
              <i class="fas fa-comments text-white text-2xl"></i>
            </div>
            <div class="text-3xl font-extrabold text-yellow-600 mb-1 drop-shadow">18</div>
            <div class="text-base text-gray-600 font-semibold tracking-wide">Pesan Masuk</div>
          </div>
        </div>

<?php include "footer.php"; ?>