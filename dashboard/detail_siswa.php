<?php
include 'header.php';
include '../api/db.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';
$siswa = null;
if ($id) {
  $stmt = $pdo->prepare('SELECT * FROM tb_siswa WHERE id = ? LIMIT 1');
  $stmt->execute([$id]);
  $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (!$siswa) {
  echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
  echo "<script>Swal.fire({icon:'error',title:'Data Tidak Ditemukan',text:'Data siswa tidak ditemukan!'}).then(()=>window.location='siswa.php');</script>";
  exit;
}
$foto = isset($siswa['foto']) && $siswa['foto'] ? '../assets/img/profile/' . $siswa['foto'] : '../assets/img/profile/default.png';

// Ambil data transaksi siswa dari tabel tb_trx dan tb_trx_tanggal berdasarkan email
$trx = [];
if (!empty($siswa['email'])) {
  try {
    // Ambil semua transaksi siswa (tanpa join tb_trx_tanggal)
    $stmt = $pdo->prepare('SELECT t.id, t.paket, t.harga, t.bayar, t.status, t.tanggal, p.nama as nama_paket, t.mapel, m.nama as nama_mapel FROM tb_trx t LEFT JOIN tb_paket p ON t.paket = p.kode LEFT JOIN tb_mapel m ON t.mapel = m.kode WHERE t.email = ? ORDER BY t.tanggal DESC');
    $stmt->execute([$siswa['email']]);
    $trx = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($trx as $i => $t) {
      $stmt2 = $pdo->prepare('SELECT tanggal, jam_trx FROM tb_trx_tanggal WHERE id_trx = ? ORDER BY tanggal, jam_trx');
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
  <div class="max-w-full overflow-x-auto bg-white rounded-3xl shadow-2xl p-4 md:p-8 border border-blue-100 relative">
    <div class="flex items-center justify-between gap-2 mb-2">
      <img src="<?= htmlspecialchars($foto) ?>" alt="Foto Siswa" class="w-20 h-20 md:w-24 md:h-24 rounded-full object-cover border-4 border-blue-300 shadow-lg">
      <div class="flex-1 flex flex-col items-center md:items-start">
        <div class="flex items-center gap-3 mb-2">
          <span class="text-2xl md:text-3xl font-extrabold text-blue-800 flex items-center gap-2">
            <i class="fa-solid fa-user-graduate"></i> <?= htmlspecialchars($siswa['nama']) ?>
          </span>
          <span class="inline-block px-3 py-1 rounded-full bg-blue-200 text-blue-800 text-xs font-bold border border-blue-300">Siswa Aktif</span>
        </div>
        <div class="flex flex-wrap gap-4 text-base md:text-lg text-blue-700 font-semibold mb-2">
          <span><i class="fa-solid fa-venus-mars text-pink-400 mr-1"></i><?= htmlspecialchars($siswa['gender']) ?></span>
          <span><i class="fa-solid fa-calendar-days text-blue-400 mr-1"></i><?= htmlspecialchars($siswa['tgl_lahir']) ?></span>
          <span><i class="fa-solid fa-envelope text-indigo-400 mr-1"></i><?= htmlspecialchars($siswa['email']) ?></span>
        </div>
        <div class="flex flex-wrap gap-4 text-base text-blue-600 mb-1">
          <span><i class="fa-solid fa-user-group text-green-500 mr-1"></i><?= htmlspecialchars($siswa['ortu']) ?></span>
          <span><i class="fa-solid fa-phone text-green-600 mr-1"></i><?= htmlspecialchars($siswa['hp_ortu']) ?></span>
          <span><i class="fa-solid fa-phone text-green-600 mr-1"></i><?= htmlspecialchars($siswa['alamat']) ?></span>
        </div>
      </div>
    </div>
    <!-- Card Transaksi -->
      <div class="bg-white rounded-2xl shadow p-6 border border-blue-100">
        <div class="flex items-center justify-between mb-4">
          <div class="text-base font-bold text-blue-700">Informasi Transaksi</div>
          <button id="btn-tambah-trx" class="px-3 py-1 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-full shadow hover:scale-105 hover:shadow-lg transition flex items-center gap-1 text-sm whitespace-nowrap">
            <i class="fa-solid fa-plus"></i> Tambah Transaksi
          </button>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-gray-700 border rounded-xl shadow">
            <thead class="bg-blue-100 text-blue-800">
              <tr>
                <th class="py-2 px-3">No</th>
                <th class="py-2 px-3">Paket</th>
                <th class="py-2 px-3">Mapel</th>
                <th class="py-2 px-3">Harga</th>
                <th class="py-2 px-3">Bayar</th>
                <th class="py-2 px-3">Sisa</th>
                <th class="py-2 px-3">Jadwal Les</th>
                <th class="py-2 px-3">Status</th>
                <th class="py-2 px-3">Aksi</th>
              </tr>
            </thead>
            <tbody id="tbodyTrx">
            <?php if (empty($trx)): ?>
                <tr><td colspan="9" class="text-center py-6 text-gray-400">Belum ada transaksi untuk siswa ini.</td></tr>
              <?php else: ?>
                <?php foreach($trx as $i => $t): 
                  $sisa = $t['harga'] - $t['bayar'];
                  $lunas = ($t['status'] == 1);
                  $badgeBg = $lunas ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-orange-100 text-orange-700 border border-orange-300';
                  $statusText = $lunas ? 'Lunas' : 'Aktif';
                ?>
                <tr class="border-b hover:bg-blue-50 group">
                  <td class="py-2 px-3 text-center"><?= $i+1 ?></td>
                  <td class="py-2 px-3"><?= htmlspecialchars($t['nama_paket'] ?? $t['paket']) ?></td>
                  <td class="py-2 px-3"><?= htmlspecialchars($t['nama_mapel'] ?? $t['mapel']) ?></td>
                  <td class="py-2 px-3 text-right"><?= number_format($t['harga'],0,',','.') ?></td>
                  <td class="py-2 px-3 text-right text-green-700"><?= number_format($t['bayar'],0,',','.') ?></td>
                  <td class="py-2 px-3 text-right <?= $lunas ? 'text-green-600' : 'text-red-600' ?>"><?= number_format(max($sisa,0),0,',','.') ?></td>
                  <td class="py-2 px-3">
                    <?php if (!empty($t['jadwal'])): ?>
                      <button type="button" class="btn-detail-jadwal px-3 py-1 bg-blue-500 text-white rounded text-xs font-bold" data-jadwal='<?= json_encode($t['jadwal']) ?>' data-mapel="<?= htmlspecialchars($t['nama_mapel'] ?? $t['mapel']) ?>">Detail</button>
                    <?php else: ?>
                      <span class="text-gray-400">-</span>
                    <?php endif; ?>
                  </td>
                  <td class="py-2 px-3"><span class="inline-block px-3 py-1 rounded-full text-xs font-bold <?= $badgeBg ?>"><?= $statusText ?></span></td>
                  <td class="py-2 px-3 flex gap-1 justify-center">
                    <button class="edit-trx px-2 py-1 rounded bg-yellow-400 text-white hover:bg-yellow-500 text-xs font-bold" data-id="<?= $t['id'] ?>" title="Edit" <?= $lunas ? 'disabled style="opacity:.5;cursor:not-allowed"' : '' ?>><i class="fa fa-edit"></i></button>
                    <button class="hapus-trx px-2 py-1 rounded bg-red-500 text-white hover:bg-red-600 text-xs font-bold" data-id="<?= $t['id'] ?>" title="Hapus" <?= $lunas ? 'disabled style="opacity:.5;cursor:not-allowed"' : '' ?>><i class="fa fa-trash"></i></button>
                    <?php if(!$lunas): ?>
                    <button class="bayar-trx px-2 py-1 rounded bg-green-500 text-white hover:bg-green-600 text-xs font-bold" data-id="<?= $t['id'] ?>" data-sisa="<?= $sisa ?>" title="Bayar"><i class="fa fa-money-bill"></i></button>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
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
      </div>
    </div>
    <div class="flex gap-4 mt-10 justify-end">
      <a href="siswa.php" class="px-6 py-2 bg-gradient-to-r from-gray-300 to-gray-400 text-gray-700 font-bold rounded-full shadow hover:scale-105 hover:shadow-xl transition flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Kembali
      </a>
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
                  <option value="<?= htmlspecialchars($p['kode']) ?>" data-harga="<?= htmlspecialchars($p['harga']) ?>">
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
<!-- Modal Edit Transaksi -->
<div id="modal-edit-trx" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-xs relative">
    <button id="close-modal-edit-trx" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-2xl"><i class="fa fa-xmark"></i></button>
    <div class="text-2xl font-extrabold text-blue-700 mb-6 flex items-center gap-2"><i class="fa-solid fa-edit text-yellow-400"></i> Edit Transaksi</div>
    <form id="form-edit-trx" class="space-y-1">
      <input type="hidden" name="id" id="edit-id">
      <div>
        <label class="block text-base font-bold text-blue-700 mb-1">Paket</label>
        <select name="paket" id="edit-paket" required class="input-form-modal">
          <option value="">Pilih Paket</option>
          <?php foreach($list_paket as $p): ?>
            <option value="<?= htmlspecialchars($p['kode']) ?>"> <?= htmlspecialchars($p['nama']) ?> (<?= htmlspecialchars($p['keterangan']) ?>) </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-base font-bold text-blue-700 mb-1">Mapel</label>
        <select name="mapel" id="edit-mapel" required class="input-form-modal">
          <option value="">Pilih Mapel</option>
          <?php foreach($list_mapel as $m): ?>
            <option value="<?= htmlspecialchars($m['kode']) ?>"> <?= htmlspecialchars($m['nama']) ?> </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-base font-bold text-blue-700 mb-1">Harga</label>
        <input type="number" name="harga" id="edit-harga" class="input-form-modal" required>
      </div>
      <div class="flex gap-4">
        <div class="flex-1">
          <label class="block text-base font-bold text-blue-700 mb-1">Hari</label>
          <select name="hari" id="edit-hari" class="input-form-modal" required>
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
          <select name="jam" id="edit-jam" class="input-form-modal" required>
            <option value="">Pilih Jam</option>
            <?php for($h=9; $h<=20; $h++): $jam = sprintf('%02d:00', $h); ?>
              <option value="<?= $jam ?>"><?= $jam ?></option>
            <?php endfor; ?>
          </select>
        </div>
      </div>
      <div>
        <label class="block text-base font-bold text-blue-700 mb-1">Tanggal Mulai</label>
        <input type="date" name="tanggal_mulai" id="edit-tanggal-mulai" class="input-form-modal" required min="<?= date('Y-m-d') ?>">
      </div>
      <div class="flex justify-end gap-3 mt-6">
        <button type="button" id="batal-modal-edit-trx" class="px-5 py-2 rounded-full bg-gray-200 text-gray-700 font-bold shadow hover:bg-gray-300 transition">Batal</button>
        <button type="submit" class="px-5 py-2 rounded-full bg-gradient-to-r from-yellow-500 to-yellow-400 text-white font-bold shadow-lg hover:scale-105 hover:shadow-xl transition flex items-center gap-2"><i class="fa-solid fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>
<!-- Modal Bayar Transaksi -->
<div id="modal-bayar-trx" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md relative">
    <button id="close-modal-bayar-trx" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-2xl"><i class="fa fa-xmark"></i></button>
    <div class="text-2xl font-extrabold text-blue-700 mb-6 flex items-center gap-2"><i class="fa-solid fa-money-bill text-green-500"></i> Bayar Transaksi</div>
    <form id="form-bayar-trx" class="space-y-5">
      <input type="hidden" name="id" id="bayar-id">
      <div>
        <label class="block text-base font-bold text-blue-700 mb-1">Nominal Bayar</label>
        <input type="number" name="nominal" id="bayar-nominal" class="input-form-modal" min="1" required>
        <div class="text-xs text-gray-500 mt-1">Masukkan nominal pembayaran (maksimal sisa tagihan).</div>
      </div>
      <div class="flex justify-end gap-3 mt-6">
        <button type="button" id="batal-modal-bayar-trx" class="px-5 py-2 rounded-full bg-gray-200 text-gray-700 font-bold shadow hover:bg-gray-300 transition">Batal</button>
        <button type="submit" class="px-5 py-2 rounded-full bg-gradient-to-r from-green-600 to-green-400 text-white font-bold shadow-lg hover:scale-105 hover:shadow-xl transition flex items-center gap-2"><i class="fa-solid fa-money-bill"></i> Bayar</button>
      </div>
    </form>
  </div>
</div>
<!-- Tambahkan modal detail jadwal di bawah tabel -->
<div id="modal-detail-jadwal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-xs relative">
    <button id="close-modal-detail-jadwal" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-2xl"><i class="fa fa-xmark"></i></button>
    <div class="text-xl font-extrabold text-blue-700 mb-4 flex items-center gap-2"><i class="fa-solid fa-calendar-days text-blue-400"></i> Detail Jadwal Les</div>
    <div id="isi-modal-detail-jadwal"></div>
  </div>
</div>
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
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script>
flatpickr("#input-tanggal-les", {
  mode: "multiple",
  dateFormat: "Y-m-d",
  minDate: "today"
});
</script>
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
      const res = await fetch('../api/hapus_trx.php', {
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
<script>
// === Fungsi Tambah Baris Jadwal ===
function addBarisTanggalJam(tgl='', jam='') {
  const tbody = document.querySelector('#tabel-tanggal-jam tbody');
  const idx = Date.now() + Math.floor(Math.random()*1000);
  tbody.insertAdjacentHTML('beforeend', `
    <tr data-idx="${idx}">
      <td><input type="date" name="tanggal_les[]" class="input-form-modal" value="${tgl}" required></td>
      <td>
        <select name="jam_les[]" class="input-form-modal" required>
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
      </td>
      <td><button type="button" class="btn-hapus-baris px-2 py-1 bg-red-500 text-white rounded flex items-center justify-center" title="Hapus"><i class="fa fa-trash"></i></button></td>
    </tr>
  `);
}

// === Fungsi Toggle Required ===
function toggleRequired(mode) {
  // Otomatis
  const hari = document.querySelector('select[name="hari"]');
  const jam = document.querySelector('select[name="jam"]');
  const tglMulai = document.querySelector('input[name="tanggal_mulai"]');
  if(hari) hari.required = (mode === 'otomatis');
  if(jam) jam.required = (mode === 'otomatis');
  if(tglMulai) tglMulai.required = (mode === 'otomatis');
  // Custom
  document.querySelectorAll('input[name="tanggal_les[]"]').forEach(el => el.required = (mode === 'custom'));
  document.querySelectorAll('select[name="jam_les[]"]').forEach(el => el.required = (mode === 'custom'));
}

// === Event Tambah Baris Jadwal ===
document.getElementById('btn-tambah-baris').onclick = () => addBarisTanggalJam();
document.querySelector('#tabel-tanggal-jam').addEventListener('click', function(e){
  if(e.target.classList.contains('btn-hapus-baris')){
    e.target.closest('tr').remove();
  }
});

// === Toggle Mode Otomatis/Custom ===
const radios = document.querySelectorAll('input[name=mode_jadwal]');
radios.forEach(radio => {
  radio.addEventListener('change', function() {
    if (this.value === 'otomatis') {
      document.getElementById('form-otomatis').style.display = '';
      document.getElementById('form-custom').style.display = 'none';
    } else {
      document.getElementById('form-otomatis').style.display = 'none';
      document.getElementById('form-custom').style.display = '';
    }
    toggleRequired(this.value);
  });
});

// === Reset Modal Tambah Transaksi ===
if(document.getElementById('btn-tambah-trx')){
  document.getElementById('btn-tambah-trx').addEventListener('click', function(){
    const tbody = document.querySelector('#tabel-tanggal-jam tbody');
    if(tbody){
      tbody.innerHTML = '';
      addBarisTanggalJam();
    }
    // Reset ke mode otomatis
    document.querySelector('input[name=mode_jadwal][value=otomatis]').checked = true;
    document.getElementById('form-otomatis').style.display = '';
    document.getElementById('form-custom').style.display = 'none';
    toggleRequired('otomatis');
  });
}
</script>
<script>
document.querySelectorAll('.btn-detail-jadwal').forEach(btn => {
  btn.onclick = function() {
    const jadwal = JSON.parse(this.getAttribute('data-jadwal'));
    const mapel = this.getAttribute('data-mapel');
    let html = `<table class='min-w-full text-xs border'><thead><tr><th class='px-2 py-1 border'>Mapel</th><th class='px-2 py-1 border'>Tanggal</th><th class='px-2 py-1 border'>Jam</th></tr></thead><tbody>`;
    jadwal.forEach(j => {
      html += `<tr><td class='px-2 py-1 border'>${mapel}</td><td class='px-2 py-1 border'>${j.tanggal}</td><td class='px-2 py-1 border'>${j.jam_trx}</td></tr>`;
    });
    html += '</tbody></table>';
    document.getElementById('isi-modal-detail-jadwal').innerHTML = html;
    document.getElementById('modal-detail-jadwal').classList.remove('hidden');
  };
});
document.getElementById('close-modal-detail-jadwal').onclick = function() {
  document.getElementById('modal-detail-jadwal').classList.add('hidden');
};
</script>
<?php include 'footer.php'; ?> 