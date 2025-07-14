<?php
include 'includes/header.php';
include 'api/db.php';

// Ambil id siswa dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$siswa = null;
if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM tb_siswa WHERE id = ?');
    $stmt->execute([$id]);
    $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (!$siswa) {
    echo '<div class="min-h-screen flex items-center justify-center"><div class="text-red-600 font-bold text-xl">Data siswa tidak ditemukan.</div></div>';
    include 'includes/footer.php';
    exit;
}
// Path foto
$foto = !empty($siswa['foto']) ? $siswa['foto'] : 'assets/img/profile/default.png';
if (strpos($foto, '/') === false) $foto = 'assets/img/profile/' . $foto;
?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-blue-100 py-10 px-2">
  <div class="w-full max-w-2xl bg-white rounded-3xl shadow-2xl p-8 md:p-12 border border-blue-100 relative">
    <div class="flex flex-col md:flex-row gap-8 items-center">
      <div class="flex-shrink-0">
        <img src="<?= htmlspecialchars($foto) ?>" alt="Foto Siswa" class="w-40 h-40 rounded-full object-cover border-4 border-blue-200 shadow-lg mb-4 md:mb-0">
      </div>
      <div class="flex-1 space-y-3">
        <div class="flex items-center gap-3 mb-2">
          <i class="fa-solid fa-user-graduate text-blue-500 text-2xl"></i>
          <span class="text-2xl font-extrabold text-blue-700 tracking-tight"><?= htmlspecialchars($siswa['nama']) ?></span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-base">
          <div><i class="fa-solid fa-venus-mars text-pink-400 mr-2"></i> <?= htmlspecialchars($siswa['gender']) ?></div>
          <div><i class="fa-solid fa-calendar-days text-blue-400 mr-2"></i> <?= htmlspecialchars($siswa['tgl_lahir']) ?></div>
          <div><i class="fa-solid fa-user-group text-green-500 mr-2"></i> <?= htmlspecialchars($siswa['ortu']) ?></div>
          <div><i class="fa-solid fa-phone text-green-600 mr-2"></i> <?= htmlspecialchars($siswa['hp_ortu']) ?></div>
          <div class="md:col-span-2"><i class="fa-solid fa-location-dot text-blue-400 mr-2"></i> <?= htmlspecialchars($siswa['alamat']) ?></div>
          <div class="md:col-span-2"><i class="fa-solid fa-envelope text-indigo-400 mr-2"></i> <?= htmlspecialchars($siswa['email']) ?></div>
        </div>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
          <div><i class="fa-solid fa-layer-group text-blue-500 mr-2"></i> Jenjang: <b><?= htmlspecialchars($siswa['jenjang']) ?></b></div>
          <div><i class="fa-solid fa-users text-blue-500 mr-2"></i> Tipe: <b><?= htmlspecialchars($siswa['tipe']) ?></b></div>
          <div><i class="fa-solid fa-book text-yellow-500 mr-2"></i> Mapel: <b><?= htmlspecialchars($siswa['mapel']) ?></b></div>
          <div><i class="fa-solid fa-money-bill-wave text-green-500 mr-2"></i> Harga: <b><?= htmlspecialchars($siswa['harga']) ?></b></div>
        </div>
        <div class="mt-4 text-sm text-gray-500">Waktu Daftar: <?= htmlspecialchars($siswa['created_at'] ?? '-') ?></div>
      </div>
    </div>
    <div class="flex gap-4 mt-10 justify-end">
      <button id="btn-hapus" class="px-6 py-2 bg-gradient-to-r from-red-500 to-pink-500 text-white font-bold rounded-full shadow hover:scale-105 hover:shadow-xl transition flex items-center gap-2">
        <i class="fa-solid fa-trash"></i> Hapus
      </button>
      <a href="javascript:history.back()" class="px-6 py-2 bg-gradient-to-r from-gray-300 to-gray-400 text-gray-700 font-bold rounded-full shadow hover:scale-105 hover:shadow-xl transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Kembali
      </a>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('btn-hapus').addEventListener('click', function() {
  Swal.fire({
    title: 'Hapus Data?',
    text: 'Data siswa akan dihapus permanen. Lanjutkan?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal',
    reverseButtons: true
  }).then(function(result) {
    if (result.isConfirmed) {
      window.location.href = 'hapus_siswa.php?id=<?= $id ?>';
    }
  });
});
</script>
<?php include 'includes/footer.php'; ?> 