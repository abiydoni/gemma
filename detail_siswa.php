<?php
include 'includes/header.php';
include 'api/db.php';

// Ambil email dari query string jika ada
$email = isset($_GET['email']) ? $_GET['email'] : '';
$siswa = null;
if ($email) {
  $stmt = $pdo->prepare('SELECT * FROM tb_siswa WHERE email = ? LIMIT 1');
  $stmt->execute([$email]);
  $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$siswa) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>Swal.fire({icon:'error',title:'Data Tidak Ditemukan',text:'Email tidak terdaftar! Silakan daftar dulu.'}).then(()=>window.location='daftar.php');</script>";
    exit;
  }
} else {
  // fallback: id
  $id = isset($_GET['id']) ? $_GET['id'] : '';
  if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM tb_siswa WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
  }
}
if (!$siswa) {
  echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
  echo "<script>Swal.fire({icon:'error',title:'Data Tidak Ditemukan',text:'Data siswa tidak ditemukan!'}).then(()=>window.location='daftar.php');</script>";
  exit;
}
$foto = isset($siswa['foto']) && $siswa['foto'] ? 'assets/img/profile/' . $siswa['foto'] : 'assets/img/profile/default.png';

// Ambil data transaksi siswa dari tabel tb_trx berdasarkan email, join tb_paket dan tb_mapel
$trx = [];
if (!empty($siswa['email'])) {
  try {
    $stmt = $pdo->prepare('SELECT t.id, t.paket, t.harga, t.bayar, t.status, t.tanggal, p.nama as nama_paket, t.mapel, m.nama as nama_mapel FROM tb_trx t LEFT JOIN tb_paket p ON t.paket = p.kode OR t.paket = p.nama LEFT JOIN tb_mapel m ON t.mapel = m.kode WHERE t.email = ? ORDER BY t.tanggal DESC');
    $stmt->execute([$siswa['email']]);
    $trx_all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Pisahkan yang belum lunas dan sudah lunas
    $trx_belum_lunas = array_filter($trx_all, function($t) { return ($t['harga'] - $t['bayar']) > 0; });
    $trx_lunas = array_filter($trx_all, function($t) { return ($t['harga'] - $t['bayar']) <= 0; });
    $trx_lunas = array_slice($trx_lunas, 0, 2);
    $trx = array_merge($trx_belum_lunas, $trx_lunas);
  } catch (Exception $e) {}
}

// Ambil data paket untuk dropdown modal
$list_paket = [];
try {
  $stmt_paket = $pdo->query('SELECT kode, nama, jenjang, harga FROM tb_paket ORDER BY kode ASC');
  $list_paket = $stmt_paket->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Ambil data mapel untuk dropdown modal
$list_mapel = [];
try {
  $stmt_mapel = $pdo->query('SELECT kode, nama FROM tb_mapel ORDER BY kode ASC');
  $list_mapel = $stmt_mapel->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}
?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-blue-100 py-10 px-2">
  <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl p-8 md:p-12 border border-blue-100 relative mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
      <!-- Card Data Siswa & Orang Tua -->
      <div class="bg-white rounded-2xl shadow p-6 border border-blue-100 flex flex-col items-start">
        <img src="<?= htmlspecialchars($foto) ?>" alt="Foto Siswa" class="w-36 h-36 rounded-full object-cover border-4 border-blue-200 shadow-lg mb-2">
        <div class="text-2xl md:text-3xl font-extrabold text-blue-700 flex items-center gap-2 mt-2 mb-4 text-left">
          <i class="fa-solid fa-user-graduate"></i> <?= htmlspecialchars($siswa['nama']) ?>
        </div>
        <div class="text-left text-lg font-bold text-blue-700 mb-2">Data Siswa</div>
        <table class="w-full text-base mb-4">
          <tr><td class="py-1 pr-3"><i class="fa-solid fa-venus-mars text-pink-400 mr-2"></i>Gender:</td><td class="py-1"> <?= htmlspecialchars($siswa['gender']) ?></td></tr>
          <tr><td class="py-1 pr-3"><i class="fa-solid fa-calendar-days text-blue-400 mr-2"></i>Tanggal Lahir:</td><td class="py-1"> <?= htmlspecialchars($siswa['tgl_lahir']) ?></td></tr>
          <tr><td class="py-1 pr-3"><i class="fa-solid fa-location-dot text-blue-400 mr-2"></i>Alamat:</td><td class="py-1"> <?= htmlspecialchars($siswa['alamat']) ?></td></tr>
          <tr><td class="py-1 pr-3"><i class="fa-solid fa-envelope text-indigo-400 mr-2"></i>Email:</td><td class="py-1"> <?= htmlspecialchars($siswa['email']) ?></td></tr>
        </table>
        <div class="text-lg font-bold text-blue-700 mb-2 mt-4">Data Orang Tua</div>
        <table class="w-full text-base">
          <tr><td class="py-1 pr-3"><i class="fa-solid fa-user-group text-green-500 mr-2"></i>Orang Tua:</td><td class="py-1"> <?= htmlspecialchars($siswa['ortu']) ?></td></tr>
          <tr><td class="py-1 pr-3"><i class="fa-solid fa-phone text-green-600 mr-2"></i>HP Ortu:</td><td class="py-1"> <?= htmlspecialchars($siswa['hp_ortu']) ?></td></tr>
        </table>
      </div>
      <!-- Card Transaksi -->
      <div class="bg-white rounded-2xl shadow p-6 border border-blue-100">
        <!-- Tombol Tambah dengan id unik -->
        <div class="flex items-center justify-between mb-4">
          <div class="text-base font-bold text-blue-700">Informasi Transaksi</div>
          <button id="btn-tambah-trx" class="px-3 py-1 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-full shadow hover:scale-105 hover:shadow-lg transition flex items-center gap-1 text-sm whitespace-nowrap">
            <i class="fa-solid fa-plus"></i> Tambah Jadwal Les
          </button>
        </div>
        <?php if (empty($trx)): ?>
          <div class="text-gray-500 italic">Belum ada transaksi untuk siswa ini.</div>
        <?php else: ?>
          <div class="space-y-4">
            <?php foreach($trx as $t): 
              $sisa = $t['harga'] - $t['bayar'];
              $lunas = $sisa <= 0;
              $cardBg = $lunas ? 'from-green-50 to-green-100 border-green-200' : 'from-orange-50 to-pink-50 border-orange-200';
              $badgeBg = $lunas ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-orange-100 text-orange-700 border border-orange-300';
              $icon = $lunas ? 'fa-circle-check text-green-500' : 'fa-circle-exclamation text-orange-500';
              $statusText = $lunas ? 'Lunas' : 'Belum Lunas';
              $sisaColor = $lunas ? 'text-green-600' : 'text-red-600';
              $subjudul = htmlspecialchars(($t['nama_paket'] ?? $t['paket']) . ($t['nama_mapel'] ? ' - ' . $t['nama_mapel'] : ''));
            ?>
            <div class="flex flex-col md:flex-row md:items-center justify-between bg-gradient-to-r <?= $cardBg ?> rounded-xl p-4 shadow border max-w-full overflow-visible relative pt-8 pb-8">
              <?php if(isset($t['id'])): ?>
                <button class="absolute -top-4 -right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gradient-to-r from-red-500 to-pink-600 text-white border-2 border-white shadow-lg hover:scale-110 transition z-50 btn-hapus-trx<?php if($lunas) echo ' opacity-50 cursor-not-allowed'; ?>" data-id="<?= htmlspecialchars($t['id']) ?>" title="Hapus Transaksi" <?php if($lunas) echo 'disabled'; ?>>
                  <i class="fa-solid fa-xmark text-base"></i>
                </button>
              <?php endif; ?>
              <div class="min-w-0 w-full md:w-auto">
                <div class="flex items-center gap-2 min-w-0 flex-col items-start text-left w-full">
                  <span class="font-bold text-blue-800 text-base truncate whitespace-nowrap overflow-hidden max-w-full" title="<?= $subjudul ?>">
                    <i class="fa-solid <?= $icon ?>"></i> <?= $subjudul ?>
                  </span>
                </div>
                <div class="text-sm text-gray-600">Harga: <span class="font-semibold text-blue-700">Rp<?= number_format($t['harga'],0,',','.') ?></span></div>
                <div class="text-sm text-gray-600">Bayar: <span class="font-semibold text-green-700">Rp<?= number_format($t['bayar'],0,',','.') ?></span></div>
                <div class="text-sm text-gray-600">Sisa: <span class="font-semibold <?= $sisaColor ?>">Rp<?= number_format(max($sisa,0),0,',','.') ?></span></div>
              </div>
              <div class="flex flex-col items-end mt-2 md:mt-0 min-w-0 w-full md:w-auto">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold <?= $badgeBg ?> max-w-full overflow-hidden mt-6">
                  <i class="fa-solid <?= $icon ?>"></i> <?= $statusText ?>
                </span>
                <?php if (isset($t['tanggal'])): ?>
                  <span class="text-xs text-gray-400 mt-1 truncate whitespace-nowrap overflow-hidden block max-w-[160px] md:max-w-[200px]" title="<?= htmlspecialchars($t['tanggal']) ?>">Transaksi: <?= htmlspecialchars($t['tanggal']) ?></span>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php
            $total_harga = 0;
            $total_bayar = 0;
            foreach($trx as $t) {
              $total_harga += $t['harga'];
              $total_bayar += $t['bayar'];
            }
            $total_sisa = $total_harga - $total_bayar;
          ?>
          <div class="mt-6 flex items-center justify-end">
            <span class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-blue-50 border border-blue-200 shadow text-lg font-bold text-blue-800">
              <i class="fa-solid fa-money-bill-wave text-green-500 text-xl"></i>
              Total yang harus dibayar:
              <span class="ml-2 text-green-700">Rp<?= number_format(max($total_sisa,0),0,',','.') ?></span>
            </span>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="flex gap-4 mt-10 justify-end">
      <a href="javascript:history.back()" class="px-6 py-2 bg-gradient-to-r from-gray-300 to-gray-400 text-gray-700 font-bold rounded-full shadow hover:scale-105 hover:shadow-xl transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Kembali
      </a>
    </div>
  </div>
</div>
<!-- Modal Tambah Transaksi -->
<div id="modal-tambah-trx" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div class="bg-gradient-to-br from-blue-100 via-white to-blue-200/80 rounded-3xl shadow-2xl p-0 sm:p-1 w-full max-w-md relative overflow-hidden">
    <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-10 w-full relative">
      <button id="close-modal-trx" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-2xl"><i class="fa-solid fa-xmark"></i></button>
      <div class="text-2xl font-extrabold text-blue-700 mb-6 flex items-center gap-2"><i class="fa-solid fa-plus-circle text-blue-400"></i> Tambah Transaksi</div>
      <form id="form-tambah-trx" class="space-y-1">
        <input type="hidden" name="email" value="<?= htmlspecialchars($siswa['email']) ?>">
        <div>
          <label class="block text-base font-bold text-blue-700 mb-1">Paket</label>
          <select name="paket" required class="input-form-modal" onchange="setHargaPaket()">
            <option value="">Pilih Paket</option>
            <?php foreach($list_paket as $p): ?>
              <option value="<?= htmlspecialchars($p['kode']) ?>" data-harga="<?= htmlspecialchars($p['harga']) ?>">
                <?= htmlspecialchars($p['nama']) ?> (<?= htmlspecialchars($p['jenjang']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-base font-bold text-blue-700 mb-1">Mapel</label>
          <select name="mapel" required class="input-form-modal">
            <option value="">Pilih Mapel</option>
            <?php foreach($list_mapel as $m): ?>
              <option value="<?= htmlspecialchars($m['kode']) ?>">
                <?= htmlspecialchars($m['nama']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-base font-bold text-blue-700 mb-1">Harga</label>
          <input type="hidden" name="harga" id="harga-paket-hidden">
          <input type="text" id="harga-paket-view" class="input-form-modal" placeholder="Harga" readonly>
        </div>
        <div class="flex gap-4">
          <div class="flex-1">
            <label class="block text-base font-bold text-blue-700 mb-1">Hari</label>
            <select name="hari" class="input-form-modal" required>
              <option value="">Pilih Hari</option>
              <option value="Senin">Senin</option>
              <option value="Selasa">Selasa</option>
              <option value="Rabu">Rabu</option>
              <option value="Kamis">Kamis</option>
              <option value="Jumat">Jumat</option>
              <option value="Sabtu">Sabtu</option>
            </select>
          </div>
          <div class="flex-1">
            <label class="block text-base font-bold text-blue-700 mb-1">Jam</label>
            <select name="jam" class="input-form-modal" required>
              <option value="">Pilih Jam</option>
              <?php for($h=9; $h<=20; $h++): $jam = sprintf('%02d:00', $h); ?>
                <option value="<?= $jam ?>"><?= $jam ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-base font-bold text-blue-700 mb-1">Tanggal Mulai</label>
          <input type="date" name="mulai" class="input-form-modal" required min="<?= date('Y-m-d') ?>">
        </div>
        <div class="flex justify-end gap-3 mt-6">
          <button type="button" id="batal-modal-trx" class="px-5 py-2 rounded-full bg-gray-200 text-gray-700 font-bold shadow hover:bg-gray-300 transition">Batal</button>
          <button type="submit" class="px-5 py-2 rounded-full bg-gradient-to-r from-blue-600 to-blue-400 text-white font-bold shadow-lg hover:scale-105 hover:shadow-xl transition flex items-center gap-2"><i class="fa-solid fa-paper-plane"></i> Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<style>
.input-form { @apply border border-blue-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-400 bg-white w-full text-base font-medium transition-all; }
.input-form-modal {
  border: 2px solid #60a5fa;
  border-radius: 0.75rem;
  padding: 0.35rem 0.6rem;
  outline: none;
  background: #fff;
  width: 100%;
  font-size: 0.95rem;
  font-weight: 500;
  color: #2563eb;
  transition: border 0.2s;
  margin-top: 2px;
}
.input-form-modal:focus {
  border-color: #2563eb;
  background: #f0f6ff;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('btn-tambah-trx').onclick = function() {
  document.getElementById('modal-tambah-trx').classList.remove('hidden');
};
document.getElementById('close-modal-trx').onclick = function() {
  document.getElementById('modal-tambah-trx').classList.add('hidden');
};
document.getElementById('batal-modal-trx').onclick = function() {
  document.getElementById('modal-tambah-trx').classList.add('hidden');
};

function setHargaPaket() {
  var select = document.querySelector('select[name=paket]');
  var harga = select.options[select.selectedIndex].getAttribute('data-harga');
  var inputView = document.getElementById('harga-paket-view');
  var inputHidden = document.getElementById('harga-paket-hidden');
  if(harga) {
    inputView.value = Number(harga).toLocaleString('id-ID');
    inputHidden.value = harga;
  } else {
    inputView.value = '';
    inputHidden.value = '';
  }
}

function setHargaMapel() {
  var select = document.querySelector('select[name=mapel]');
  var harga = select.options[select.selectedIndex].getAttribute('data-harga');
  document.getElementById('harga-mapel').value = harga ? harga : '';
}

document.getElementById('form-tambah-trx').addEventListener('submit', async function(e) {
  const tanggalMulai = this.querySelector('input[name="mulai"]').value;
  const hariInput = this.querySelector('select[name="hari"]').value;
  if (tanggalMulai && hariInput) {
    const hariIndo = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const d = new Date(tanggalMulai);
    const hariTanggal = hariIndo[d.getDay()];
    if (hariTanggal !== hariInput) {
      e.preventDefault();
      await Swal.fire({icon:'error',title:'Hari Tidak Cocok',text:`Hari pada tanggal mulai (${hariTanggal}) tidak sama dengan input hari (${hariInput}). Silakan pilih yang sesuai.`});
      return false;
    }
  }
  e.preventDefault();
  const form = this;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type=submit]');
  // Konfirmasi sebelum simpan
  const konfirmasi = await Swal.fire({
    title: 'Konfirmasi Simpan',
    text: 'Apakah data transaksi sudah benar dan ingin disimpan?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, Simpan',
    cancelButtonText: 'Batal'
  });
  if (!konfirmasi.isConfirmed) return;
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
  try {
    const res = await fetch('api/proses_trx.php', { method: 'POST', body: formData });
    const data = await res.json();
    if(data.status === 'ok') {
      Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Transaksi berhasil disimpan!' }).then(() => window.location.reload());
    } else {
      Swal.fire({ icon: 'error', title: 'Gagal', text: data.msg || 'Gagal menyimpan transaksi.' });
    }
  } catch(err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
  }
  btn.disabled = false;
  btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Simpan';
});

document.querySelectorAll('.btn-hapus-trx').forEach(function(btn) {
  btn.addEventListener('click', async function(e) {
    e.preventDefault();
    const id = this.getAttribute('data-id');
    const konfirmasi = await Swal.fire({
      title: 'Konfirmasi Hapus',
      text: 'Yakin ingin menghapus transaksi ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal'
    });
    if (!konfirmasi.isConfirmed) return;
    try {
      const res = await fetch('api/hapus_trx.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(id)
      });
      const data = await res.json();
      if(data.status === 'ok') {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Transaksi berhasil dihapus!' }).then(() => window.location.reload());
      } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: data.msg || 'Gagal menghapus transaksi.' });
      }
    } catch(err) {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
    }
  });
});
</script>
<?php include 'includes/footer.php'; ?> 