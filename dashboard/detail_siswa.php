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
    $stmt = $pdo->prepare('SELECT t.id, t.paket, t.harga, t.bayar, t.status, t.tanggal, p.nama as nama_paket, t.mapel, m.nama as nama_mapel FROM tb_trx t LEFT JOIN tb_paket p ON t.paket = p.kode LEFT JOIN tb_mapel m ON t.mapel = m.id WHERE t.email = ? ORDER BY t.tanggal DESC');
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
  $stmt_mapel = $pdo->query('SELECT id, nama FROM tb_mapel WHERE status=1 ORDER BY id ASC');
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
                  <option value="<?= htmlspecialchars($p['kode']) ?>" data-harga="<?= htmlspecialchars($p['harga']) ?>" data-keterangan="<?= htmlspecialchars($p['keterangan']) ?>">
                    <?= htmlspecialchars($p['nama']) ?><?= $p['keterangan'] ? ' - '.htmlspecialchars($p['keterangan']) : '' ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-sm font-bold text-blue-700 mb-1">Mapel</label>
              <select name="mapel" id="mapel-select" required class="input-form-modal" onchange="setTentorByMapel()">
                <option value="">Pilih Mapel</option>
                <?php foreach($list_mapel as $m): ?>
                  <option value="<?= htmlspecialchars($m['id']) ?>">
                    <?= htmlspecialchars($m['nama']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-sm font-bold text-blue-700 mb-1">Tentor</label>
              <select name="tentor" id="tentor-select" required class="input-form-modal" readonly>
                <option value="">Pilih Mapel terlebih dahulu</option>
                <?php 
                // Ambil data tentor
                $tentor = [];
                try {
                    $stmt = $pdo->query("SELECT id, nama FROM tb_user WHERE role='tentor' ORDER BY nama ASC");
                    $tentor = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (Exception $e) {}
                ?>
                <?php foreach($tentor as $t): ?>
                  <option value="<?= $t['id'] ?>">
                    <?= htmlspecialchars($t['nama']) ?>
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
            <option value="<?= htmlspecialchars($m['id']) ?>"> <?= htmlspecialchars($m['nama']) ?> </option>
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
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-lg relative">
    <button id="close-modal-detail-jadwal" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-2xl"><i class="fa fa-xmark"></i></button>
    <div class="text-xl font-extrabold text-blue-700 mb-4 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-calendar-days text-blue-400"></i> Detail Jadwal Les
      </div>
      <button id="btnPrintDetailJadwal" class="px-3 py-1 rounded bg-green-600 text-white font-bold shadow hover:bg-green-700">
        <i class="fa fa-print"></i> Print
      </button>
    </div>
    <div id="isi-modal-detail-jadwal"></div>
  </div>
</div>
<!-- Modal Edit Jadwal -->
<div id="modalEditJadwal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
    <button id="closeEditJadwalModal" class="absolute top-2 right-2 text-gray-400 hover:text-red-500"><i class="fa fa-times"></i></button>
    <h3 class="text-lg font-bold mb-4 text-blue-700">Edit Tanggal & Jam Jadwal</h3>
    <form id="formEditJadwal">
      <input type="hidden" id="editJadwalId" name="id">
      <div class="mb-3">
        <label class="block text-xs font-bold mb-1">Tanggal</label>
        <input type="date" id="editJadwalTanggal" name="tanggal" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="mb-3">
        <label class="block text-xs font-bold mb-1">Jam</label>
        <select id="editJadwalJam" name="jam" class="w-full border rounded px-2 py-1" required>
          <option value="">Pilih Jam</option>
        </select>
      </div>
      <div class="flex justify-end gap-2 mt-4">
        <button type="button" id="batalEditJadwal" class="px-3 py-1 rounded bg-gray-300 text-gray-700 font-bold">Batal</button>
        <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white font-bold">Simpan</button>
      </div>
    </form>
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

function setTentorByMapel() {
  const mapelSelect = document.getElementById('mapel-select');
  const tentorSelect = document.getElementById('tentor-select');
  const selectedMapel = mapelSelect.value;
  tentorSelect.innerHTML = '<option value="">Pilih Mapel terlebih dahulu</option>'; // Reset pilihan

  if (selectedMapel) {
    fetch('../api/tentor_mapel.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'mapel=' + encodeURIComponent(selectedMapel)
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'ok') {
        data.data.forEach(tentor => {
          const option = document.createElement('option');
          option.value = tentor.id;
          option.textContent = tentor.nama;
          tentorSelect.appendChild(option);
        });
      } else {
        tentorSelect.innerHTML = '<option value="">Tidak ada tentor untuk mapel ini.</option>';
      }
    })
    .catch(err => {
      tentorSelect.innerHTML = '<option value="">Gagal mengambil data tentor.</option>';
    });
  } else {
    tentorSelect.innerHTML = '<option value="">Pilih Mapel terlebih dahulu</option>';
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
function addBarisTanggalJam() {
  var tbody = document.querySelector('#tabel-tanggal-jam tbody');
  var tr = document.createElement('tr');
  tr.innerHTML = `
    <td>
      <input type="date" name="tanggal_les[]" class="input-form-modal" required>
    </td>
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
    <td class="text-center">
      <button type="button" class="btn-hapus-baris bg-red-500 text-white rounded-full px-2 py-1">
        <i class="fa fa-trash"></i>
      </button>
    </td>
  `;
  tbody.appendChild(tr);
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
    // Atur required tanggal_mulai, hari, jam
    const inputTanggalMulai = document.querySelector('input[name="tanggal_mulai"]');
    const inputHari = document.querySelector('select[name="hari"]');
    const inputJam = document.querySelector('select[name="jam"]');
    if (this.value === 'otomatis') {
      inputTanggalMulai.setAttribute('required', 'required');
      inputHari.setAttribute('required', 'required');
      inputJam.setAttribute('required', 'required');
    } else {
      inputTanggalMulai.removeAttribute('required');
      inputHari.removeAttribute('required');
      inputJam.removeAttribute('required');
    }
  });
});
// Inisialisasi required tanggal_mulai, hari, jam saat load
const modeAwal = document.querySelector('input[name=mode_jadwal]:checked');
if (modeAwal) {
  const inputTanggalMulai = document.querySelector('input[name="tanggal_mulai"]');
  const inputHari = document.querySelector('select[name="hari"]');
  const inputJam = document.querySelector('select[name="jam"]');
  if (modeAwal.value === 'otomatis') {
    inputTanggalMulai.setAttribute('required', 'required');
    inputHari.setAttribute('required', 'required');
    inputJam.setAttribute('required', 'required');
  } else {
    inputTanggalMulai.removeAttribute('required');
    inputHari.removeAttribute('required');
    inputJam.removeAttribute('required');
  }
}
document.querySelector('select[name=paket]').addEventListener('change', function() {
  setHargaPaket();
  updateHargaHarian();
});
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
document.querySelectorAll('.hapus-trx').forEach(function(btn) {
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
// Fungsi untuk ambil hari dari tanggal (YYYY-MM-DD -> Senin, Selasa, dst)
function getHariIndo(tgl) {
  const hariIndo = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
  const d = new Date(tgl);
  return hariIndo[d.getDay()];
}
// Fungsi untuk konversi tanggal DD/MM/YYYY ke YYYY-MM-DD
function toYYYYMMDD(tgl) {
  if (/^\d{2}\/\d{2}\/\d{4}$/.test(tgl)) {
    const [d, m, y] = tgl.split('/');
    return `${y}-${m}-${d}`;
  }
  return tgl;
}
// Fungsi untuk generate opsi jam dari jam buka-tutup
function generateJamOptions(buka, tutup) {
  let html = '<option value="">Pilih Jam</option>';
  if (!buka || !tutup) return html;
  const [bukaH, bukaM] = buka.split(':').map(Number);
  const [tutupH, tutupM] = tutup.split(':').map(Number);
  for (let h = bukaH; h <= tutupH; h++) {
    let jam = (h < 10 ? '0' : '') + h + ':00';
    if (h === tutupH && tutupM === 0) break;
    html += `<option value="${jam}">${jam}</option>`;
  }
  return html;
}
// Handler untuk modal edit jadwal
const inputTglEdit = document.getElementById('editJadwalTanggal');
const selectJamEdit = document.getElementById('editJadwalJam');
// Fungsi untuk mengisi jam pada modal edit jadwal
function isiJamEditJadwal(tanggal, jamLama) {
  const hari = getHariIndo(tanggal);
  const selectJamEdit = document.getElementById('editJadwalJam'); // ambil ulang setiap kali fungsi dipanggil
  if (!selectJamEdit) return;
  fetch('api/jadwal_get.php?hari=' + encodeURIComponent(hari))
    .then(res => res.json())
    .then(data => {
      selectJamEdit.innerHTML = generateJamOptions(data.buka, data.tutup);
      if (jamLama) selectJamEdit.value = jamLama;
    });
}
let jadwalTerpilih = null; // Variabel untuk menyimpan data jadwal yang sedang diedit
document.addEventListener('click', function(e) {
  // Handler tombol detail jadwal
  if (e.target.closest('.btn-detail-jadwal')) {
    const btn = e.target.closest('.btn-detail-jadwal');
    const jadwal = JSON.parse(btn.getAttribute('data-jadwal'));
    const mapel = btn.getAttribute('data-mapel');
    let html = `<table class='min-w-full text-xs border'><thead><tr><th class='px-2 py-1 border'>Mapel</th><th class='px-2 py-1 border'>Tanggal</th><th class='px-2 py-1 border'>Jam</th><th class='px-2 py-1 border'>Aksi</th></tr></thead><tbody>`;
    jadwal.forEach((j, idx) => {
      html += `<tr data-idx='${idx}' data-idjadwal='${j.id}'>
        <td class='px-2 py-1 border'>${mapel}</td>
        <td class='px-2 py-1 border'>${j.tanggal}</td>
        <td class='px-2 py-1 border'>${j.jam_trx}</td>
        <td class='px-2 py-1 border text-center'>
          <button type='button' class='btn-edit-jadwal px-2 py-1 bg-yellow-400 text-white rounded text-xs font-bold' data-idx='${idx}' title='Edit'><i class='fa fa-edit'></i></button>
        </td>
      </tr>`;
    });
    html += '</tbody></table>';
    document.getElementById('isi-modal-detail-jadwal').innerHTML = html;
    document.getElementById('modal-detail-jadwal').classList.remove('hidden');
    
    // Simpan data untuk print
    window.currentJadwalData = {
      jadwal: jadwal,
      mapel: mapel,
      siswa: {
        nama: '<?= htmlspecialchars($siswa['nama']) ?>',
        email: '<?= htmlspecialchars($siswa['email']) ?>'
      }
    };
    return;
  }
  // Handler tombol edit jadwal di modal detail
  if (e.target.closest('.btn-edit-jadwal')) {
    const btn = e.target.closest('.btn-edit-jadwal');
    const idx = btn.getAttribute('data-idx');
    const tr = btn.closest('tr');
    // Ambil data dari baris
    const tanggal = tr.children[1].textContent.trim();
    const jam = tr.children[2].textContent.trim();
    const idJadwal = tr.getAttribute('data-idjadwal') || '';
    document.getElementById('editJadwalId').value = idJadwal;
    document.getElementById('editJadwalTanggal').value = toYYYYMMDD(tanggal);
    document.getElementById('editJadwalJam').value = jam;
    document.getElementById('modalEditJadwal').classList.remove('hidden');
    isiJamEditJadwal(toYYYYMMDD(tanggal), jam);
    jadwalTerpilih = { idx, idJadwal, tr };
    return;
  }
  // Handler tombol close modal detail jadwal
  if (e.target.closest('#close-modal-detail-jadwal')) {
    document.getElementById('modal-detail-jadwal').classList.add('hidden');
    return;
  }
  
  // Handler tombol print detail jadwal
  if (e.target.closest('#btnPrintDetailJadwal')) {
    if (window.currentJadwalData) {
      const data = window.currentJadwalData;
      const encodedData = encodeURIComponent(JSON.stringify(data));
      window.open(`print/print_detail_jadwal.php?data=${encodedData}`, '_blank');
    }
    return;
  }
});
document.getElementById('closeEditJadwalModal').onclick = function() {
  document.getElementById('modalEditJadwal').classList.add('hidden');
};
document.getElementById('batalEditJadwal').onclick = function() {
  document.getElementById('modalEditJadwal').classList.add('hidden');
};
document.getElementById('formEditJadwal').onsubmit = function(e) {
  e.preventDefault();
  const id = document.getElementById('editJadwalId').value;
  const tanggal = document.getElementById('editJadwalTanggal').value;
  const jam = document.getElementById('editJadwalJam').value;
  if (!id || !tanggal || !jam) return;
  fetch('api/trx_proses.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=edit_jadwal&id=${encodeURIComponent(id)}&tanggal=${encodeURIComponent(tanggal)}&jam=${encodeURIComponent(jam)}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'ok') {
      // Update tampilan baris jadwal tanpa reload
      if (jadwalTerpilih && jadwalTerpilih.tr) {
        jadwalTerpilih.tr.children[1].textContent = tanggal;
        jadwalTerpilih.tr.children[2].textContent = jam;
      }
      // Ambil ulang data jadwal dari server dan update data-jadwal pada tombol detail
      if (jadwalTerpilih && jadwalTerpilih.tr) {
        // Cari tombol detail jadwal terkait transaksi ini
        const btnDetail = document.querySelector(`button.btn-detail-jadwal[data-mapel]`);
        if (btnDetail) {
          // Ambil id_trx dari parent row transaksi
          const trTransaksi = btnDetail.closest('tr');
          if (trTransaksi) {
            const idTrx = trTransaksi.querySelector('.hapus-trx')?.getAttribute('data-id');
            if (idTrx) {
              fetch(`api/trx_proses.php?action=get_jadwal&id_trx=${encodeURIComponent(idTrx)}`)
                .then(res => res.json())
                .then(jadwalBaru => {
                  btnDetail.setAttribute('data-jadwal', JSON.stringify(jadwalBaru));
                  // Jika modal detail jadwal masih terbuka, update isi modal juga
                  if (!document.getElementById('modal-detail-jadwal').classList.contains('hidden')) {
                    // Render ulang isi modal
                    const mapel = btnDetail.getAttribute('data-mapel');
                    let html = `<table class='min-w-full text-xs border'><thead><tr><th class='px-2 py-1 border'>Mapel</th><th class='px-2 py-1 border'>Tanggal</th><th class='px-2 py-1 border'>Jam</th><th class='px-2 py-1 border'>Aksi</th></tr></thead><tbody>`;
                    jadwalBaru.forEach((j, idx) => {
                      html += `<tr data-idx='${idx}' data-idjadwal='${j.id}'>
                        <td class='px-2 py-1 border'>${mapel}</td>
                        <td class='px-2 py-1 border'>${j.tanggal}</td>
                        <td class='px-2 py-1 border'>${j.jam_trx}</td>
                        <td class='px-2 py-1 border text-center'>
                          <button type='button' class='btn-edit-jadwal px-2 py-1 bg-yellow-400 text-white rounded text-xs font-bold' data-idx='${idx}' title='Edit'><i class='fa fa-edit'></i></button>
                        </td>
                      </tr>`;
                    });
                    html += '</tbody></table>';
                    document.getElementById('isi-modal-detail-jadwal').innerHTML = html;
                  }
                });
            }
          }
        }
      }
      document.getElementById('modalEditJadwal').classList.add('hidden');
      Swal.fire('Berhasil', 'Jadwal berhasil diupdate', 'success');
    } else {
      Swal.fire('Gagal', data.msg || 'Gagal update jadwal', 'error');
    }
  })
  .catch(() => {
    Swal.fire('Gagal', 'Gagal update jadwal', 'error');
  });
};
</script>
<script>
// Script modal bayar transaksi
// Buka modal dan isi data saat tombol bayar diklik
let sisaTagihan = 0;
document.querySelectorAll('.bayar-trx').forEach(function(btn) {
  btn.addEventListener('click', function() {
    const id = this.getAttribute('data-id');
    sisaTagihan = parseInt(this.getAttribute('data-sisa')) || 0;
    document.getElementById('bayar-id').value = id;
    document.getElementById('bayar-nominal').value = sisaTagihan; // Otomatis isi nominal sesuai sisa
    document.getElementById('bayar-nominal').setAttribute('max', sisaTagihan);
    document.getElementById('modal-bayar-trx').classList.remove('hidden');
  });
});
document.getElementById('close-modal-bayar-trx').onclick = function() {
  document.getElementById('modal-bayar-trx').classList.add('hidden');
};
document.getElementById('batal-modal-bayar-trx').onclick = function() {
  document.getElementById('modal-bayar-trx').classList.add('hidden');
};
document.getElementById('form-bayar-trx').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id = document.getElementById('bayar-id').value;
  const nominal = parseInt(document.getElementById('bayar-nominal').value);
  if (!id || !nominal || nominal < 1) {
    await Swal.fire({icon:'error',title:'Input Salah',text:'Nominal pembayaran harus diisi!'});
    return;
  }
  if (nominal > sisaTagihan) {
    await Swal.fire({icon:'error',title:'Input Salah',text:'Nominal tidak boleh melebihi sisa tagihan!'});
    return;
  }
  // Konfirmasi sebelum submit
  const konfirmasi = await Swal.fire({
    title: 'Konfirmasi Pembayaran',
    text: `Yakin ingin membayar sebesar Rp${nominal.toLocaleString('id-ID')}?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, Bayar',
    cancelButtonText: 'Batal'
  });
  if (!konfirmasi.isConfirmed) return;
  const btn = this.querySelector('button[type=submit]');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Membayar...';
  try {
    const res = await fetch('api/trx_proses.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=bayar&id=${encodeURIComponent(id)}&nominal=${encodeURIComponent(nominal)}`
    });
    const data = await res.json();
    if(data.status === 'ok') {
      Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Pembayaran berhasil!' }).then(() => window.location.reload());
    } else {
      Swal.fire({ icon: 'error', title: 'Gagal', text: data.msg || 'Gagal melakukan pembayaran.' });
    }
  } catch(err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
  }
  btn.disabled = false;
  btn.innerHTML = '<i class="fa-solid fa-money-bill"></i> Bayar';
});
</script>

<?php include 'footer.php'; ?> 