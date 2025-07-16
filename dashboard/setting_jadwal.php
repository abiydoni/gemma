<?php
include 'header.php';
include '../api/db.php';

// Ambil data jadwal
$data_jadwal = [];
$sql = "SELECT * FROM tb_jadwal ORDER BY id ASC";
$result = $pdo->query($sql);
$data_jadwal = $result->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="flex items-center justify-between mb-8">
  <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 flex items-center gap-2">
    <i class="fa fa-clock text-blue-600"></i> Setting Jadwal
  </h1>
</div>
<div class="max-w-5xl mx-auto">
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8">
    <?php foreach ($data_jadwal as $jadwal): ?>
    <form class="bg-white shadow-xl rounded-2xl p-8 flex flex-col gap-3 jadwal-form border border-blue-100 hover:shadow-2xl transition duration-300 group relative" data-id="<?= $jadwal['id'] ?>">
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-0.5">Hari</label>
        <input type="text" name="hari" value="<?= htmlspecialchars($jadwal['hari']) ?>" class="border border-blue-200 rounded px-3 py-2 w-full bg-gray-100 text-blue-900 font-bold text-lg" readonly />
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-0.5">Jam Buka</label>
        <input type="time" name="buka" value="<?= htmlspecialchars($jadwal['buka']) ?>" class="border border-blue-200 rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400 focus:outline-none text-blue-700" required />
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-0.5">Jam Tutup</label>
        <input type="time" name="tutup" value="<?= htmlspecialchars($jadwal['tutup']) ?>" class="border border-blue-200 rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400 focus:outline-none text-blue-700" required />
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-0.5">Tanggal Update</label>
        <input type="text" value="<?= htmlspecialchars($jadwal['tanggal']) ?>" class="border border-gray-200 rounded px-3 py-2 w-full bg-gray-100 text-gray-500" readonly />
      </div>
      <button type="submit" class="mt-2 bg-gradient-to-r from-blue-600 to-blue-400 text-white rounded-full px-8 py-2 font-bold shadow-lg hover:from-blue-700 hover:to-blue-500 transition-all flex items-center gap-2 justify-center text-lg tracking-wide">
        <i class="fa-solid fa-floppy-disk"></i> Update
      </button>
    </form>
    <?php endforeach; ?>
  </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.jadwal-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = this.getAttribute('data-id');
        const formData = new FormData(this);
        formData.append('id', id);
        fetch('api/jadwal_update.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Jadwal berhasil diupdate!',
                    showConfirmButton: false,
                    timer: 1500,
                    background: '#f0f6ff',
                    color: '#2563eb',
                }).then(() => window.location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Gagal update jadwal',
                    background: '#fff0f0',
                    color: '#b91c1c',
                });
            }
        })
        .catch(() => Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan',
            background: '#fff0f0',
            color: '#b91c1c',
        }));
    });
});
</script>
<?php include 'footer.php'; ?> 