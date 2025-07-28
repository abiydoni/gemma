<?php
include 'header.php';
include '../api/db.php';

// Ambil data fasilitas
$data_fasilitas = [];
$sql = "SELECT * FROM tb_fasilitas ORDER BY id ASC";
$result = $pdo->query($sql);
$data_fasilitas = $result->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="flex items-center justify-between mb-8">
  <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 flex items-center gap-2">
    <i class="fa fa-cogs text-blue-600"></i> Setting Fasilitas
  </h1>
  <button id="btnPrint" class="px-3 py-1 rounded bg-green-600 text-white font-bold shadow hover:bg-green-700"><i class="fa fa-print"></i> Print</button>
</div>
<div class="max-w-5xl mx-auto">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <?php foreach ($data_fasilitas as $fasilitas): ?>
    <?php
      $ikon = trim($fasilitas['ikon']);
      // Jika belum ada prefix fa-solid/fa-regular/fa-brands, tambahkan fa-solid
      if (!preg_match('/fa-(solid|regular|brands)/', $ikon)) {
        $ikon = 'fa-solid ' . $ikon;
      }
      // Jika belum ada prefix 'fa ' di depan, tambahkan
      if (strpos($ikon, 'fa ') !== 0) {
        $ikon = 'fa ' . $ikon;
      }
    ?>
    <form class="bg-white shadow-xl rounded-2xl p-8 flex flex-col gap-6 fasilitas-form border border-blue-100 hover:shadow-2xl transition duration-300 group relative" data-id="<?= $fasilitas['id'] ?>">
      <div class="flex flex-col items-center mb-2">
        <div class="rounded-full bg-blue-50 p-6 border-2 border-blue-200 group-hover:scale-105 transition-transform mb-3 shadow-sm">
          <i class="<?= htmlspecialchars($ikon) ?> text-5xl text-blue-600"></i>
        </div>
        <div class="text-xs text-gray-400 mb-2">Class: <?= htmlspecialchars($ikon) ?></div>
        <input type="text" name="ikon" value="<?= htmlspecialchars($fasilitas['ikon']) ?>" class="border border-blue-200 rounded px-3 py-2 w-2/3 text-center focus:ring-2 focus:ring-blue-400 focus:outline-none text-blue-700 font-mono text-sm" placeholder="Icon (Font Awesome)" required />
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Nama Fasilitas</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($fasilitas['nama']) ?>" class="border border-blue-200 rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400 focus:outline-none font-bold text-lg text-blue-900" required />
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Keterangan</label>
        <textarea name="keterangan" class="border border-blue-200 rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400 focus:outline-none min-h-[60px] text-gray-700" required><?= htmlspecialchars($fasilitas['keterangan']) ?></textarea>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Tanggal Update</label>
        <input type="text" value="<?= htmlspecialchars($fasilitas['tanggal']) ?>" class="border border-gray-200 rounded px-3 py-2 w-full bg-gray-100 text-gray-500" readonly />
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
document.querySelectorAll('.fasilitas-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = this.getAttribute('data-id');
        const formData = new FormData(this);
        formData.append('id', id);
        fetch('api/fasilitas_update.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Fasilitas berhasil diupdate!',
                    showConfirmButton: false,
                    timer: 1500,
                    background: '#f0f6ff',
                    color: '#2563eb',
                }).then(() => window.location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Gagal update fasilitas',
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

// Print button
document.getElementById('btnPrint').addEventListener('click', function() {
    window.open('print/print_setting_fasilitas.php', '_blank');
});
</script>
<?php include 'footer.php'; ?> 