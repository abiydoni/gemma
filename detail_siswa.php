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
    // Ambil semua transaksi siswa (tanpa join tb_trx_tanggal)
    $stmt = $pdo->prepare('SELECT t.id, t.paket, t.harga, t.bayar, t.status, t.tanggal, p.nama as nama_paket, t.mapel, m.nama as nama_mapel FROM tb_trx t LEFT JOIN tb_paket p ON t.paket = p.kode LEFT JOIN tb_mapel m ON t.mapel = m.kode WHERE t.email = ? ORDER BY t.tanggal DESC');
    $stmt->execute([$siswa['email']]);
    $trx = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($trx as $i => $t) {
      $stmt2 = $pdo->prepare('SELECT id, tanggal, jam_trx FROM tb_trx_tanggal WHERE id_trx = ? ORDER BY tanggal, jam_trx');
      $stmt2->execute([$t['id']]);
      $trx[$i]['jadwal'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
  } catch (Exception $e) {}
}

// Ambil data paket untuk dropdown modal
$list_paket = [];
try {
  $stmt_paket = $pdo->query("SELECT kode, nama, keterangan, jenjang, harga FROM tb_paket WHERE status=1 ORDER BY nama ASC");
  $list_paket = $stmt_paket->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Ambil data mapel untuk dropdown modal
$list_mapel = [];
try {
  $stmt_mapel = $pdo->query('SELECT kode, nama FROM tb_mapel WHERE status=1 ORDER BY kode ASC');
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
                  <span class="text-xs text-gray-400 mt-1 truncate whitespace-nowrap overflow-hidden block max-w-[160px] md:max-w-[200px]" title="<?= htmlspecialchars($t['mulai'] ?? $t['tanggal']) ?>">Mulai: <?= htmlspecialchars($t['mulai'] ?? $t['tanggal']) ?></span>
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
  <div class="bg-gradient-to-br from-blue-100 via-white to-blue-200/80 rounded-3xl shadow-2xl p-0 sm:p-1 w-full max-w-2xl relative overflow-hidden">
    <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 w-full relative">
      <button id="close-modal-trx" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-2xl"><i class="fa-solid fa-xmark"></i></button>
      <div class="text-2xl font-extrabold text-blue-700 mb-6 flex items-center gap-2"><i class="fa-solid fa-plus-circle text-blue-400"></i> Tambah Transaksi</div>
      <form id="form-tambah-trx" class="space-y-1">
        <div class="flex flex-col md:flex-row gap-6">
          <!-- Kolom Kiri: Form Utama -->
          <div class="flex-1">
            <input type="hidden" name="email" value="<?= htmlspecialchars($siswa['email']) ?>">
            <div>
              <label class="block text-sm font-bold text-blue-700 mb-1">Paket</label>
              <select name="paket" required class="input-form-modal" onchange="setHargaPaket()">
                <option value="">Pilih Paket</option>
                <?php foreach($list_paket as $p): ?>
                  <option value="<?= htmlspecialchars($p['kode']) ?>" data-harga="<?= htmlspecialchars($p['harga']) ?>" data-keterangan="<?= htmlspecialchars($p['keterangan']) ?>">
                    <?= htmlspecialchars($p['nama']) ?><?= $p['keterangan'] ? ' - '.htmlspecialchars($p['keterangan']) : '' ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-sm font-bold text-blue-700 mb-1">Mapel</label>
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
              <label class="block text-sm font-bold text-blue-700 mb-1">Harga</label>
              <input type="hidden" name="harga" id="harga-paket-hidden">
              <input type="text" id="harga-paket-view" class="input-form-modal" placeholder="Harga" readonly>
            </div>
          </div>
          <!-- Kolom Kanan: Model Jadwal Les -->
          <div class="flex-1">
            <div class="mb-4">
              <label class="block font-bold mb-1">Mode Jadwal Les:</label>
              <div class="flex gap-4">
                <label><input type="radio" name="mode_jadwal" value="otomatis" checked> Otomatis</label>
                <label><input type="radio" name="mode_jadwal" value="custom"> Custom</label>
              </div>
            </div>
            <div id="form-otomatis">
              <div class="flex gap-4">
                <div class="flex-1">
                  <label class="block text-sm font-bold text-blue-700 mb-1">Hari</label>
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
                  <label class="block text-sm font-bold text-blue-700 mb-1">Jam</label>
                  <select name="jam" class="input-form-modal" required>
                    <option value="">Pilih Jam</option>
                    <option value="09:00">09:00</option>
                    <option value="10:00">10:00</option>
                    <option value="11:00">11:00</option>
                    <option value="12:00">12:00</option>
                    <option value="13:00">13:00</option>
                    <option value="14:00">14:00</option>
                    <option value="15:00">15:00</option>
                    <option value="16:00">16:00</option>
                    <option value="17:00">17:00</option>
                    <option value="18:00">18:00</option>
                    <option value="19:00">19:00</option>
                    <option value="20:00">20:00</option>
                  </select>
                </div>
              </div>
              <div>
                <label class="block text-sm font-bold text-blue-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="input-form-modal" required min="<?= date('Y-m-d') ?>">
              </div>
            </div>
            <div id="form-custom" style="display:none;">
              <div class="mb-4">
                <label class="block font-bold mb-1">Jadwal Les (boleh lebih dari satu):</label>
                <table class="min-w-full text-sm mb-2" id="tabel-tanggal-jam">
                  <thead>
                    <tr>
                      <th class="py-1 px-2">Tanggal</th>
                      <th class="py-1 px-2">Jam</th>
                      <th class="py-1 px-2">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Baris dinamis di sini -->
                  </tbody>
                </table>
                <button type="button" id="btn-tambah-baris" class="px-3 py-1 bg-blue-500 text-white rounded flex items-center justify-center" title="Tambah Baris"><i class="fa fa-plus"></i></button>
              </div>
            </div>
          </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
          <button type="button" id="batal-modal-trx" class="px-5 py-2 rounded-full bg-gray-200 text-gray-700 font-bold shadow hover:bg-gray-300 transition">Batal</button>
          <button type="submit" class="px-5 py-2 rounded-full bg-gradient-to-r from-blue-600 to-blue-400 text-white font-bold shadow-lg hover:scale-105 hover:shadow-xl transition flex items-center gap-2"><i class="fa-solid fa-paper-plane"></i> Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Script dan style dari dashboard untuk modal tambah transaksi -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function setHargaPaket() {
  var select = document.querySelector('select[name=paket]');
  var harga = select.options[select.selectedIndex].getAttribute('data-harga');
  var inputView = document.getElementById('harga-paket-view');
  var inputHidden = document.getElementById('harga-paket-hidden');
  var keterangan = select.options[select.selectedIndex].getAttribute('data-keterangan') || '';
  if(harga) {
    // Jika Harian dan mode custom, kalikan harga dengan jumlah tanggal
    if (keterangan === 'Harian' && document.querySelector('input[name=mode_jadwal]:checked').value === 'custom') {
      var rows = document.querySelectorAll('#tabel-tanggal-jam tbody tr');
      var total = parseInt(harga) * rows.length;
      inputView.value = Number(total).toLocaleString('id-ID');
      inputHidden.value = total;
    } else {
      inputView.value = Number(harga).toLocaleString('id-ID');
      inputHidden.value = harga;
    }
  } else {
    inputView.value = '';
    inputHidden.value = '';
  }
}
// Update harga jika jumlah tanggal bertambah/berkurang pada mode custom dan paket Harian
function updateHargaHarian() {
  var select = document.querySelector('select[name=paket]');
  var harga = select.options[select.selectedIndex].getAttribute('data-harga');
  var keterangan = select.options[select.selectedIndex].getAttribute('data-keterangan') || '';
  var inputView = document.getElementById('harga-paket-view');
  var inputHidden = document.getElementById('harga-paket-hidden');
  if (keterangan === 'Harian' && document.querySelector('input[name=mode_jadwal]:checked').value === 'custom') {
    var rows = document.querySelectorAll('#tabel-tanggal-jam tbody tr');
    var total = parseInt(harga) * rows.length;
    inputView.value = Number(total).toLocaleString('id-ID');
    inputHidden.value = total;
  }
}
// Event listener untuk perubahan baris tanggal pada mode custom
if(document.getElementById('btn-tambah-baris')){
  document.getElementById('btn-tambah-baris').addEventListener('click', function(){
    addBarisTanggalJam();
    updateHargaHarian();
  });
}
document.querySelector('#tabel-tanggal-jam').addEventListener('click', function(e){
  const btn = e.target.closest('.btn-hapus-baris');
  if(btn){
    btn.closest('tr').remove();
    updateHargaHarian();
  }
});
document.querySelectorAll('input[name=mode_jadwal]').forEach(radio => {
  radio.addEventListener('change', function() {
    setHargaPaket();
    updateHargaHarian();
    document.getElementById('form-otomatis').style.display = this.value === 'otomatis' ? '' : 'none';
    document.getElementById('form-custom').style.display = this.value === 'custom' ? '' : 'none';
  });
});
document.querySelector('select[name=paket]').addEventListener('change', function() {
  setHargaPaket();
  updateHargaHarian();
});
function addBarisTanggalJam() {
  var tbody = document.querySelector('#tabel-tanggal-jam tbody');
  var tr = document.createElement('tr');
  tr.innerHTML = `<td><input type="date" name="tanggal_les[]" class="input-form-modal" required></td><td><select name="jam_les[]" class="input-form-modal" required><option value="">Pilih Jam</option><option value="09:00">09:00</option><option value="10:00">10:00</option><option value="11:00">11:00</option><option value="12:00">12:00</option><option value="13:00">13:00</option><option value="14:00">14:00</option><option value="15:00">15:00</option><option value="16:00">16:00</option><option value="17:00">17:00</option><option value="18:00">18:00</option><option value="19:00">19:00</option><option value="20:00">20:00</option></select></td><td class="text-center"><button type="button" class="btn-hapus-baris bg-red-500 text-white rounded-full px-2 py-1"><i class="fa fa-trash"></i></button></td>`;
  tbody.appendChild(tr);
}
document.getElementById('btn-tambah-trx').onclick = function() {
  document.getElementById('modal-tambah-trx').classList.remove('hidden');
};
document.getElementById('close-modal-trx').onclick = function() {
  document.getElementById('modal-tambah-trx').classList.add('hidden');
};
document.getElementById('batal-modal-trx').onclick = function() {
  document.getElementById('modal-tambah-trx').classList.add('hidden');
};
document.getElementById('form-tambah-trx').addEventListener('submit', async function(e) {
  const mode = this.querySelector('input[name="mode_jadwal"]:checked').value;
  if (mode === 'otomatis') {
    const tanggalMulai = this.querySelector('input[name="tanggal_mulai"]').value;
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
  } else if (mode === 'custom') {
    // Validasi minimal satu baris tanggal-jam
    const rows = this.querySelectorAll('#tabel-tanggal-jam tbody tr');
    if (rows.length === 0) {
      e.preventDefault();
      await Swal.fire({icon:'error',title:'Jadwal Kosong',text:'Minimal satu jadwal (tanggal & jam) harus diisi!'});
      return false;
    }
    let valid = true;
    rows.forEach(row => {
      const tgl = row.querySelector('input[name="tanggal_les[]"]').value;
      const jam = row.querySelector('select[name="jam_les[]"]').value;
      if (!tgl || !jam) valid = false;
    });
    if (!valid) {
      e.preventDefault();
      await Swal.fire({icon:'error',title:'Jadwal Tidak Lengkap',text:'Semua baris jadwal harus diisi tanggal dan jam!'});
      return false;
    }
  }
  e.preventDefault();
  const form = this;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type=submit]');
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
    const res = await fetch('api/trx_proses.php', { method: 'POST', body: formData });
    const data = await res.json();
    if(data.status === 'ok') {
      Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Transaksi berhasil disimpan!' }).then(() => window.location.reload());
    } else {
      let msg = data.msg || 'Gagal menyimpan transaksi.';
      if (data.detail) msg += '\n' + data.detail;
      Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
    }
  } catch(err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
  }
  btn.disabled = false;
  btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Simpan';
});
// Script hapus transaksi (seperti dashboard)
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
      const res = await fetch('api/trx_proses.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=delete&id=' + encodeURIComponent(id)
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
<style>
.input-form-modal {
  border: 2px solid #60a5fa;
  border-radius: 0.75rem;
  padding: 0.35rem 0.6rem;
  outline: none;
  background: #fff;
  width: 100%;
  font-size: 0.875rem; /* text-sm */
  font-weight: 400;
  color: #2563eb;
  transition: border 0.2s;
  margin-top: 2px;
}
.input-form-modal:focus {
  border-color: #2563eb;
  background: #f0f6ff;
}
.btn-hapus-baris {
  min-width: 28px;
  min-height: 28px;
  width: 28px;
  height: 28px;
  padding: 0 !important;
  margin-left: 6px;
  margin-right: 6px;
  cursor: pointer;
  z-index: 2;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: #ef4444;
  transition: background 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 8px 0 rgba(0,0,0,0.04);
}
.btn-hapus-baris:hover, .btn-hapus-baris:focus {
  background: #b91c1c;
  box-shadow: 0 4px 16px 0 rgba(239,68,68,0.15);
}
</style>
<?php include 'includes/footer.php'; ?> 