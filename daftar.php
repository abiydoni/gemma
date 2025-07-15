<?php include 'includes/header.php'; ?>
<?php
include 'api/db.php';
try {
  $stmt = $pdo->query('SELECT kode, nama FROM tb_mapel ORDER BY nama ASC');
  $list_mapel = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $stmt2 = $pdo->query('SELECT nama, keterangan FROM tb_jenjang ORDER BY nama ASC');
  $list_jenjang = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $list_mapel = [];
  $list_jenjang = [];
}
?>
<!-- Overlay untuk disable interaksi di luar form, termasuk navbar dan footer -->
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-blue-100 py-10 relative z-[110] pointer-events-auto">
  <div class="w-full max-w-5xl mx-auto flex flex-col gap-8 relative z-[110] pointer-events-auto px-2 sm:px-4">
    <!-- Kontainer Kiri: Form -->
    <div class="flex-1 w-full">
      <div class="bg-gradient-to-br from-blue-100 via-white to-blue-200/80 rounded-2xl shadow-2xl p-2 sm:p-4 flex flex-col items-center justify-center relative overflow-hidden z-60 w-full">
        <!-- Card Profil Siswa -->
        <form id="form-daftar" action="api/proses_daftar.php" method="POST" enctype="multipart/form-data" class="w-full flex flex-col items-center gap-4 sm:gap-6 py-6 sm:py-8 relative z-60">
          <!-- Foto Siswa -->
          <div class="w-full flex flex-col md:flex-row items-center md:items-start gap-4 sm:gap-6 mb-4 sm:mb-6">
            <div class="relative group flex justify-center w-full md:w-auto">
              <label for="foto" class="block cursor-pointer">
                <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-full bg-white/40 shadow-2xl flex items-center justify-center overflow-hidden transition-all duration-300 group-hover:scale-105 group-hover:shadow-blue-200 backdrop-blur-md mx-auto">
                  <img id="preview-foto-profile" src="assets/img/profile/default.png" alt="Foto Siswa" class="object-cover w-full h-full transition-all duration-300 hidden" />
                  <div id="icon-foto-profile" class="flex flex-col items-center justify-center w-full h-full z-10">
                    <i class="fa-solid fa-user text-blue-200 text-5xl sm:text-6xl drop-shadow"></i>
                    <span class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                      <i class="fa-solid fa-plus-circle text-blue-400 text-2xl sm:text-4xl bg-white/80 rounded-full p-2 shadow-lg"></i>
                    </span>
                  </div>
                  <div id="icon-edit-profile" class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all pointer-events-none">
                    <i class="fa-solid fa-pen-to-square text-blue-500 text-xl sm:text-3xl bg-white/80 rounded-full p-2 shadow-lg"></i>
                  </div>
                </div>
                <input type="file" name="foto" id="foto" accept="image/*" class="hidden" />
              </label>
            </div>
            <div class="flex flex-col justify-center mt-2 md:mt-0 w-full md:w-auto text-center md:text-left">
              <div class="flex items-center gap-2 sm:gap-3 justify-center md:justify-start">
                <i class="fa-solid fa-user-plus text-blue-500 text-xl sm:text-2xl md:text-3xl"></i>
                <span class="text-lg sm:text-xl md:text-3xl font-extrabold text-blue-700 tracking-tight">Registrasi Siswa</span>
              </div>
            </div>
          </div>
          <!-- Data Profil Siswa -->
          <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
            <!-- Sub Judul Data Siswa -->
            <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-2 mb-2">
              <i class="fa-solid fa-address-card text-blue-500"></i>
              <span class="font-bold text-blue-700 text-base">Data Siswa</span>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 bg-white/80 rounded-lg px-3 sm:px-4 py-2 sm:py-3 shadow transition-all w-full">
              <i class="fa-solid fa-user text-blue-400 text-base sm:text-lg"></i>
              <input type="text" name="nama" required placeholder="Nama Lengkap" class="w-full bg-transparent outline-none focus:ring-0 text-base font-semibold text-blue-900 placeholder-gray-400" />
            </div>
            <div class="flex items-center gap-2 sm:gap-3 bg-white/80 rounded-lg px-3 sm:px-4 py-2 sm:py-3 shadow transition-all w-full">
              <i class="fa-solid fa-venus-mars text-blue-400 text-base sm:text-lg"></i>
              <select name="gender" required class="w-full bg-transparent outline-none focus:ring-0 text-base font-semibold text-blue-900 placeholder-gray-400">
                <option value="">Jenis Kelamin</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 bg-white/80 rounded-lg px-3 sm:px-4 py-2 sm:py-3 shadow transition-all w-full">
              <i class="fa-solid fa-calendar-days text-blue-400 text-base sm:text-lg"></i>
              <input type="date" name="tgl_lahir" required class="w-full bg-transparent outline-none focus:ring-0 text-base font-semibold text-blue-900 placeholder-gray-400" />
            </div>
            <!-- Sub Judul Data Orang Tua/Wali -->
            <div class="col-span-1 md:col-span-2 flex items-center gap-2 mt-6 mb-2">
              <i class="fa-solid fa-user-group text-blue-500"></i>
              <span class="font-bold text-blue-700 text-base">Data Orang Tua/Wali</span>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 bg-white/80 rounded-lg px-3 sm:px-4 py-2 sm:py-3 shadow transition-all w-full">
              <i class="fa-solid fa-user text-blue-400 text-base sm:text-lg"></i>
              <input type="text" name="ortu" required placeholder="Nama Orang Tua/Wali" class="w-full bg-transparent outline-none focus:ring-0 text-base font-semibold text-blue-900 placeholder-gray-400" />
            </div>
            <div class="flex items-center gap-2 sm:gap-3 bg-white/80 rounded-lg px-3 sm:px-4 py-2 sm:py-3 shadow transition-all w-full">
              <i class="fa-solid fa-phone text-blue-400 text-base sm:text-lg"></i>
              <input type="text" name="hp_ortu" required placeholder="No. HP/WA Orang Tua" class="w-full bg-transparent outline-none focus:ring-0 text-base font-semibold text-blue-900 placeholder-gray-400" />
            </div>
            <div class="flex items-center gap-2 sm:gap-3 bg-white/80 rounded-lg px-3 sm:px-4 py-2 sm:py-3 shadow transition-all w-full">
              <i class="fa-solid fa-location-dot text-blue-400 text-base sm:text-lg"></i>
              <input type="text" name="alamat" required placeholder="Alamat Lengkap" class="w-full bg-transparent outline-none focus:ring-0 text-base font-semibold text-blue-900 placeholder-gray-400" />
            </div>
            <div class="flex items-center gap-2 sm:gap-3 bg-white/80 rounded-lg px-3 sm:px-4 py-2 sm:py-3 shadow transition-all w-full">
              <i class="fa-solid fa-envelope text-blue-400 text-base sm:text-lg"></i>
              <input type="email" name="email" placeholder="Email (opsional)" class="w-full bg-transparent outline-none focus:ring-0 text-base font-semibold text-blue-900 placeholder-gray-400" />
            </div>
          </div>
          <div class="w-full flex flex-row gap-3 sm:gap-4 mt-4">
            <button type="button" id="btn-batal" class="flex-1 py-3 bg-gradient-to-r from-gray-300 to-gray-400 text-gray-700 font-bold rounded-full shadow hover:scale-105 hover:shadow-xl transition-all text-base sm:text-lg tracking-wide flex items-center justify-center gap-2 relative z-60">
              <i class="fa-solid fa-arrow-left"></i> Batal
            </button>
            <button type="submit" id="btn-daftar" class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-blue-400 text-white font-bold rounded-full shadow-lg hover:scale-105 hover:shadow-xl transition-all text-base sm:text-lg tracking-wide flex items-center justify-center gap-2">
              <i class="fa-solid fa-paper-plane"></i> Daftar & Lihat Invoice
            </button>
          </div>
          <span id="btn-loading" class="hidden flex items-center justify-center mt-2 text-blue-600"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Memproses...</span>
        </form>
      </div>
    </div>
    <!-- Kontainer Kanan: Keranjang & Invoice -->
  </div>
</div>
<style>
.input-form { @apply border-2 border-blue-300 rounded-lg px-4 py-2.5 mt-1 focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200 bg-white w-full text-base font-medium transition-all duration-200; }
.input-form-ktp {
  @apply border-0 border-b border-blue-400 bg-white w-full text-base font-medium px-0 py-1 focus:outline-none focus:border-blue-600 transition-all duration-150 shadow-none rounded-none;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Tombol kamera untuk upload foto
  var btnUploadFoto = document.getElementById('btn-upload-foto');
  if (btnUploadFoto) {
    btnUploadFoto.addEventListener('click', function(e) {
      e.preventDefault();
      document.getElementById('foto').click();
    });
  }
  // Upload & preview foto head dan form
  const fotoInput = document.getElementById('foto');
  const previewHead = document.getElementById('preview-foto-head');
  const previewForm = document.getElementById('preview-foto');
  const iconPlusHead = document.getElementById('icon-plus-head');
  fotoInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if(file) {
      const reader = new FileReader();
      reader.onload = function(ev) {
        previewHead.src = ev.target.result;
        previewForm.src = ev.target.result;
        previewHead.classList.remove('hidden');
        iconPlusHead.classList.add('hidden');
      }
      reader.readAsDataURL(file);
    } else {
      previewHead.src = 'assets/img/profile/default.png';
      previewForm.src = 'assets/img/profile/default.png';
      previewHead.classList.add('hidden');
      iconPlusHead.classList.remove('hidden');
    }
  });
  // Inisialisasi: jika belum ada foto, tampilkan plus, jika sudah ada, tampilkan foto
  window.addEventListener('DOMContentLoaded', function() {
    if (!fotoInput.files.length) {
      previewHead.classList.add('hidden');
      iconPlusHead.classList.remove('hidden');
    }
  });
  // SweetAlert submit
  document.getElementById('form-daftar').addEventListener('submit', async function(e) {
    if (!this.checkValidity()) {
      this.reportValidity();
      return;
    }
    e.preventDefault();
    const konfirmasi = await Swal.fire({
      title: 'Konfirmasi Pendaftaran',
      text: 'Apakah data sudah benar dan ingin mendaftar?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Daftar',
      cancelButtonText: 'Batal'
    });
    if (!konfirmasi.isConfirmed) return;
    const btn = document.getElementById('btn-daftar');
    const btnLoading = document.getElementById('btn-loading');
    btn.disabled = true;
    btnLoading.classList.remove('hidden');
    const form = e.target;
    const formData = new FormData(form);
    const res = await fetch(form.action, { method: 'POST', body: formData });
    const data = await res.json();
    btn.disabled = false;
    btnLoading.classList.add('hidden');
    if(data.status === 'ok') {
      form.reset();
      document.getElementById('preview-foto').src = 'assets/img/profile/default.png';
      Swal.fire({ icon: 'success', title: 'Pendaftaran Berhasil', text: 'Data berhasil dikirim. Anda akan diarahkan ke detail siswa.' }).then(() => {
        window.location.href = 'detail_siswa.php?email=' + encodeURIComponent(form.email.value);
      });
    } else {
      Swal.fire({ icon: 'error', title: 'Gagal', text: data.msg || 'Terjadi kesalahan.' });
    }
  });
  // Tidak perlu update card siswa, karena sekarang input langsung di card
  // Foto siswa profil preview dengan efek icon
  const fotoInputProfile = document.getElementById('foto');
  const previewProfile = document.getElementById('preview-foto-profile');
  const iconFotoProfile = document.getElementById('icon-foto-profile');
  const iconEditProfile = document.getElementById('icon-edit-profile');
  fotoInputProfile.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if(file) {
      const reader = new FileReader();
      reader.onload = function(ev) {
        previewProfile.src = ev.target.result;
        previewProfile.classList.remove('hidden');
        iconFotoProfile.classList.add('hidden');
        if (iconEditProfile) {
          iconEditProfile.classList.remove('pointer-events-none');
        }
      }
      reader.readAsDataURL(file);
    } else {
      previewProfile.src = 'assets/img/profile/default.png';
      previewProfile.classList.add('hidden');
      iconFotoProfile.classList.remove('hidden');
      if (iconEditProfile) {
        iconEditProfile.classList.add('pointer-events-none');
      }
    }
  });
  // Inisialisasi: jika belum ada foto, tampilkan icon user, jika sudah ada, tampilkan foto
  window.addEventListener('DOMContentLoaded', function() {
    if (!fotoInputProfile.files.length) {
      previewProfile.classList.add('hidden');
      iconFotoProfile.classList.remove('hidden');
      if (iconEditProfile) {
        iconEditProfile.classList.add('pointer-events-none');
      }
    }
  });
  // Event listener tombol batal
  const btnBatal = document.getElementById('btn-batal');
  if (btnBatal) {
    btnBatal.addEventListener('click', function() {
      window.location.href = '/';
    });
  }
</script>
<?php include 'includes/footer.php'; ?> 