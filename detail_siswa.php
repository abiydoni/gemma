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
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-blue-100 py-10 px-2">
  <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl p-8 md:p-12 border border-blue-100 relative mx-auto">
    
    <!-- Header dengan Foto dan Data Siswa -->
    <div class="text-center mb-8">
      <div class="flex justify-between items-center mb-4">
        <div></div>
        <div>
          <img src="<?= htmlspecialchars($foto) ?>" alt="Foto Siswa" class="w-32 h-32 rounded-full object-cover border-4 border-blue-200 shadow-lg mx-auto mb-4">
          <div class="text-3xl font-extrabold text-blue-700 flex items-center justify-center gap-2">
            <i class="fa-solid fa-user-graduate"></i> <?= htmlspecialchars($siswa['nama']) ?>
          </div>
          <p class="text-gray-600 mt-2"><?= htmlspecialchars($siswa['email']) ?></p>
        </div>
        <button id="btnPrint" class="px-3 py-1 rounded bg-green-600 text-white font-bold shadow hover:bg-green-700"><i class="fa fa-print"></i> Print</button>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg mb-6">
      <button onclick="showTab('transaksi')" class="px-6 py-3 rounded-lg text-sm font-medium bg-blue-600 text-white transition-all">
        <i class="fa-solid fa-receipt mr-2"></i>Informasi Transaksi
      </button>
      <button onclick="showTab('perkembangan')" class="px-6 py-3 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 transition-all">
        <i class="fa-solid fa-chart-line mr-2"></i>Laporan Perkembangan
      </button>
    </div>

    <!-- Tab Content -->
    <div id="tab-content-transaksi" class="block">
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Transaksi</h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Kolom Kiri: Data Siswa & Orang Tua -->
          <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
              <h4 class="font-semibold text-gray-800 mb-3">Data Siswa</h4>
              <table class="w-full text-sm">
                <tr><td class="py-1 pr-3"><i class="fa-solid fa-venus-mars text-pink-400 mr-2"></i>Gender:</td><td class="py-1"> <?= htmlspecialchars($siswa['gender']) ?></td></tr>
                <tr><td class="py-1 pr-3"><i class="fa-solid fa-calendar-days text-blue-400 mr-2"></i>Tanggal Lahir:</td><td class="py-1"> <?= htmlspecialchars($siswa['tgl_lahir']) ?></td></tr>
                <tr><td class="py-1 pr-3"><i class="fa-solid fa-location-dot text-blue-400 mr-2"></i>Alamat:</td><td class="py-1"> <?= htmlspecialchars($siswa['alamat']) ?></td></tr>
                <tr><td class="py-1 pr-3"><i class="fa-solid fa-envelope text-indigo-400 mr-2"></i>Email:</td><td class="py-1"> <?= htmlspecialchars($siswa['email']) ?></td></tr>
              </table>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4">
              <h4 class="font-semibold text-gray-800 mb-3">Data Orang Tua</h4>
              <table class="w-full text-sm">
                <tr><td class="py-1 pr-3"><i class="fa-solid fa-user-group text-green-500 mr-2"></i>Orang Tua:</td><td class="py-1"> <?= htmlspecialchars($siswa['ortu']) ?></td></tr>
                <tr><td class="py-1 pr-3"><i class="fa-solid fa-phone text-green-600 mr-2"></i>HP Ortu:</td><td class="py-1"> <?= htmlspecialchars($siswa['hp_ortu']) ?></td></tr>
              </table>
            </div>
            
            <?php 
            $total_sisa = array_sum(array_map(function($t) { return max(0, $t['harga'] - $t['bayar']); }, $trx));
            ?>
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200 shadow-sm">
              <h4 class="font-semibold text-green-800 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-green-600"></i>Status Pembayaran
              </h4>
              <div class="text-center">
                <?php if ($total_sisa > 0): ?>
                <div class="text-2xl font-bold text-green-700 mb-2">
                  Rp<?= number_format($total_sisa,0,',','.') ?>
                </div>
                <p class="text-sm text-green-600">Yang harus dibayar</p>
                <?php else: ?>
                <div class="text-lg font-bold text-blue-700 mb-2">
                  ✅ Lunas
                </div>
                <p class="text-sm text-blue-600">Semua pembayaran sudah lunas</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
          
          <!-- Kolom Kanan: Data Transaksi -->
          <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
              <h4 class="font-semibold text-gray-800">Data Transaksi</h4>
              <button id="btn-tambah-trx" class="px-3 py-1 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-full shadow hover:scale-105 hover:shadow-lg transition flex items-center gap-1 text-sm whitespace-nowrap">
                <i class="fa-solid fa-plus"></i> Tambah Jadwal Les
              </button>
            </div>
            
            <?php if (empty($trx)): ?>
              <div class="text-gray-500 italic text-center py-8">Belum ada transaksi untuk siswa ini.</div>
            <?php else: ?>
              <?php
              // Pisahkan transaksi lunas dan belum lunas
              $trx_lunas = [];
              $trx_belum_lunas = [];
              
              foreach($trx as $t) {
                $sisa = $t['harga'] - $t['bayar'];
                if ($sisa <= 0) {
                  $trx_lunas[] = $t;
                } else {
                  $trx_belum_lunas[] = $t;
                }
              }
              
              // Tentukan transaksi yang akan ditampilkan
              $trx_to_show = [];
              
              if (empty($trx_belum_lunas)) {
                // Semua lunas - tampilkan maksimal 3
                $trx_to_show = array_slice($trx_lunas, 0, 3);
              } elseif (count($trx_belum_lunas) == 1 && count($trx_lunas) >= 2) {
                // Hanya 1 belum lunas - tampilkan 3 baris (1 belum lunas + 2 lunas)
                $trx_to_show = array_merge($trx_belum_lunas, array_slice($trx_lunas, 0, 2));
              } elseif (count($trx_belum_lunas) == 2 && count($trx_lunas) >= 1) {
                // Ada 2 belum lunas - tampilkan 3 baris (2 belum lunas + 1 lunas)
                $trx_to_show = array_merge($trx_belum_lunas, array_slice($trx_lunas, 0, 1));
              } elseif (count($trx_belum_lunas) == 3) {
                // Ada 3 belum lunas - tampilkan 3 baris (3 belum lunas)
                $trx_to_show = $trx_belum_lunas;
              } else {
                // Ada lebih dari 3 belum lunas - tampilkan semua yang belum lunas saja
                $trx_to_show = $trx_belum_lunas;
              }
              ?>
              
              <div class="space-y-4">
                <?php foreach($trx_to_show as $t): 
                  $sisa = $t['harga'] - $t['bayar'];
                  $lunas = $sisa <= 0;
                ?>
                  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200 shadow-lg hover:shadow-xl transition-all duration-300 relative">
                    <!-- Tombol Hapus di Pojok Kanan Atas -->
                    <button onclick="<?= $lunas ? 'return false;' : 'hapusTransaksi(' . $t['id'] . ')' ?>" 
                            class="absolute -top-2 -right-2 w-6 h-6 <?= $lunas ? 'bg-gray-400 cursor-not-allowed' : 'bg-red-500 hover:bg-red-600' ?> text-white rounded-full flex items-center justify-center text-xs transition-all duration-200 <?= $lunas ? '' : 'hover:scale-110' ?> shadow-lg"
                            <?= $lunas ? 'disabled title="Transaksi lunas tidak dapat dihapus"' : '' ?>>
                      <i class="fa-solid fa-times"></i>
                    </button>
                    
                    <div class="flex justify-between items-start mb-4">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                          <?= strtoupper(substr($t['nama_mapel'], 0, 2)) ?>
                        </div>
                        <div>
                          <h4 class="font-bold text-gray-800 text-lg"><?= htmlspecialchars($t['nama_paket']) ?> - <?= htmlspecialchars($t['nama_mapel']) ?></h4>
                          <p class="text-sm text-gray-600">Mulai: <?= date('d/m/Y H:i', strtotime($t['tanggal'])) ?></p>
                        </div>
                      </div>
                      <div class="text-right">
                        <div class="text-lg font-bold text-gray-800">Rp<?= number_format($t['harga'],0,',','.') ?></div>
                        <div class="text-sm text-gray-600">Bayar: Rp<?= number_format($t['bayar'],0,',','.') ?></div>
                        <div class="text-sm <?= $lunas ? 'text-green-600' : 'text-red-600' ?> font-semibold">
                          Sisa: Rp<?= number_format($sisa,0,',','.') ?>
                        </div>
                      </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                      <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold <?= $lunas ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-orange-100 text-orange-800 border border-orange-300' ?>">
                          <?= $lunas ? '✅ Lunas' : '⚠️ Belum Lunas' ?>
                        </span>
                        <span class="text-xs text-gray-500">ID: <?= $t['id'] ?></span>
                      </div>
                      <div class="flex gap-2">
                        <button onclick="viewTransaksiDetail(<?= $t['id'] ?>)" class="px-3 py-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 flex items-center gap-1">
                          <i class="fa-solid fa-eye"></i> Detail
                        </button>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              
              <?php if (count($trx) > count($trx_to_show)): ?>
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                  <p class="text-blue-800 text-sm text-center">
                    <i class="fa-solid fa-info-circle mr-1"></i>
                    Menampilkan <?= count($trx_to_show) ?> dari <?= count($trx) ?> transaksi
                    <?php if (empty($trx_belum_lunas)): ?>
                      (semua lunas)
                    <?php elseif (count($trx_belum_lunas) == 1): ?>
                      (1 belum lunas + 2 lunas terbaru)
                    <?php elseif (count($trx_belum_lunas) == 2): ?>
                      (2 belum lunas + 1 lunas terbaru)
                    <?php elseif (count($trx_belum_lunas) == 3): ?>
                      (3 belum lunas)
                    <?php else: ?>
                      (semua yang belum lunas)
                    <?php endif; ?>
                  </p>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div id="tab-content-perkembangan" class="hidden">
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center gap-2">
          <i class="fa-solid fa-chart-line text-blue-600"></i>
          Laporan Perkembangan Siswa
        </h3>
        
        <div id="perkembangan-content">
          <div class="text-center py-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
              <i class="fa-solid fa-spinner fa-spin text-blue-600 text-xl"></i>
            </div>
            <p class="text-gray-600 font-medium">Memuat data perkembangan...</p>
            <p class="text-sm text-gray-500 mt-2">Email: <?= htmlspecialchars($siswa['email']) ?></p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Tombol Kembali -->
    <div class="flex justify-center mt-8">
      <a href="javascript:history.back()" class="px-6 py-3 bg-gradient-to-r from-gray-300 to-gray-400 text-gray-700 font-bold rounded-full shadow hover:scale-105 hover:shadow-xl transition flex items-center gap-2">
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
<!-- Modal Detail Jadwal -->
<div id="modal-detail-jadwal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div class="bg-gradient-to-br from-blue-100 via-white to-blue-200/80 rounded-3xl shadow-2xl p-0 sm:p-1 w-full max-w-2xl relative overflow-hidden">
    <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 w-full relative">
      <button id="close-modal-detail-jadwal" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-2xl"><i class="fa-solid fa-xmark"></i></button>
      <div class="text-2xl font-extrabold text-blue-700 mb-6 flex items-center gap-2"><i class="fa-solid fa-calendar-days text-blue-400"></i> Jadwal Les</div>
      <div id="isi-modal-detail-jadwal">
        <!-- Isi jadwal akan dimuat di sini -->
      </div>
    </div>
  </div>
</div>
<!-- Modal Edit Jadwal -->
<div id="modalEditJadwal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div class="bg-gradient-to-br from-blue-100 via-white to-blue-200/80 rounded-3xl shadow-2xl p-0 sm:p-1 w-full max-w-2xl relative overflow-hidden">
    <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 w-full relative">
      <button id="close-modal-edit-jadwal" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-2xl"><i class="fa-solid fa-xmark"></i></button>
      <div class="text-2xl font-extrabold text-blue-700 mb-6 flex items-center gap-2"><i class="fa-solid fa-edit text-blue-400"></i> Edit Jadwal Les</div>
      <form id="formEditJadwal" class="space-y-1">
        <input type="hidden" name="id_jadwal" id="editJadwalId">
        <div class="flex flex-col md:flex-row gap-6">
          <div class="flex-1">
            <label class="block text-sm font-bold text-blue-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal" id="editJadwalTanggal" class="input-form-modal" required>
          </div>
          <div class="flex-1">
            <label class="block text-sm font-bold text-blue-700 mb-1">Jam</label>
            <select name="jam" id="editJadwalJam" class="input-form-modal" required>
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
        <div class="flex justify-end gap-3 mt-6">
          <button type="button" id="batal-modal-edit-jadwal" class="px-5 py-2 rounded-full bg-gray-200 text-gray-700 font-bold shadow hover:bg-gray-300 transition">Batal</button>
          <button type="submit" class="px-5 py-2 rounded-full bg-gradient-to-r from-blue-600 to-blue-400 text-white font-bold shadow-lg hover:scale-105 hover:shadow-xl transition flex items-center gap-2"><i class="fa-solid fa-save"></i> Simpan</button>
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

// Script untuk modal detail jadwal
document.addEventListener('click', function(e) {
  // Handler tombol detail jadwal
  if (e.target.closest('.btn-detail-jadwal')) {
    const btn = e.target.closest('.btn-detail-jadwal');
    const jadwal = JSON.parse(btn.getAttribute('data-jadwal'));
    const mapel = btn.getAttribute('data-mapel');
    let html = `<table class='min-w-full text-xs border'><thead><tr><th class='px-2 py-1 border'>Mapel</th><th class='px-2 py-1 border'>Tanggal</th><th class='px-2 py-1 border'>Jam</th></tr></thead><tbody>`;
    jadwal.forEach((j, idx) => {
      html += `<tr data-idx='${idx}' data-idjadwal='${j.id}'>
        <td class='px-2 py-1 border'>${mapel}</td>
        <td class='px-2 py-1 border'>${j.tanggal}</td>
        <td class='px-2 py-1 border'>${j.jam_trx}</td>
      </tr>`;
    });
    html += '</tbody></table>';
    document.getElementById('isi-modal-detail-jadwal').innerHTML = html;
    document.getElementById('modal-detail-jadwal').classList.remove('hidden');
    return;
  }
  // Handler tombol close modal detail jadwal
  if (e.target.closest('#close-modal-detail-jadwal')) {
    document.getElementById('modal-detail-jadwal').classList.add('hidden');
    return;
  }
});

// Script untuk modal edit jadwal
document.getElementById('formEditJadwal').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = this;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type=submit]');
  const konfirmasi = await Swal.fire({
    title: 'Konfirmasi Edit',
    text: 'Apakah data jadwal sudah benar dan ingin disimpan?',
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
      Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Jadwal berhasil diubah!' }).then(() => {
        document.getElementById('modalEditJadwal').classList.add('hidden');
        window.location.reload(); // Refresh halaman untuk menampilkan jadwal yang sudah diubah
      });
    } else {
      let msg = data.msg || 'Gagal menyimpan jadwal.';
      if (data.detail) msg += '\n' + data.detail;
      Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
    }
  } catch(err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
  }
  btn.disabled = false;
  btn.innerHTML = '<i class="fa-solid fa-save"></i> Simpan';
});
document.getElementById('close-modal-edit-jadwal').onclick = function() {
  document.getElementById('modalEditJadwal').classList.add('hidden');
};
document.getElementById('batal-modal-edit-jadwal').onclick = function() {
  document.getElementById('modalEditJadwal').classList.add('hidden');
};

// Helper function untuk mengubah tanggal ke format YYYY-MM-DD
function toYYYYMMDD(dateString) {
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

// Helper function untuk mengisi jam pada modal edit jadwal
function isiJamEditJadwal(tanggal, jam) {
  const selectJam = document.getElementById('editJadwalJam');
  selectJam.value = jam;
}

function setTentorByMapel() {
  const mapelSelect = document.getElementById('mapel-select');
  const tentorSelect = document.getElementById('tentor-select');
  const selectedMapel = mapelSelect.value;
  tentorSelect.innerHTML = '<option value="">Pilih Mapel terlebih dahulu</option>'; // Reset pilihan

  if (selectedMapel) {
    fetch('api/tentor_mapel.php', {
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

// Tab Management untuk detail_siswa.php root
function showTab(tabName) {
  console.log('Showing tab:', tabName);
  
  // Hide all tab contents
  document.querySelectorAll('[id^="tab-content-"]').forEach(content => {
    content.classList.add('hidden');
  });
  
  // Reset all tab buttons
  document.querySelectorAll('[onclick^="showTab"]').forEach(btn => {
    btn.className = 'px-6 py-3 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 transition-all';
  });
  
  // Show selected tab content
  const selectedContent = document.getElementById(`tab-content-${tabName}`);
  if (selectedContent) {
    selectedContent.classList.remove('hidden');
  }
  
  // Highlight selected tab button
  const selectedBtn = event.target;
  if (selectedBtn) {
    selectedBtn.className = 'px-6 py-3 rounded-lg text-sm font-medium bg-blue-600 text-white transition-all';
  }
  
  // Load data for the selected tab
  if (tabName === 'perkembangan') {
    loadPerkembanganData('<?= htmlspecialchars($siswa['email']) ?>');
  }
}

function loadTabData(tabName) {
  const email = '<?= htmlspecialchars($siswa['email']) ?>';
  console.log('Loading data for tab:', tabName, 'email:', email);
  
  // Sekarang hanya load perkembangan data
  loadPerkembanganData(email);
}

// Load Perkembangan Data
function loadPerkembanganData(email) {
  const content = document.getElementById('perkembangan-content');
  content.innerHTML = `
    <div class="text-center py-8">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
        <i class="fa-solid fa-spinner fa-spin text-blue-600 text-xl"></i>
      </div>
      <p class="text-gray-600 font-medium">Memuat data perkembangan...</p>
      <p class="text-sm text-gray-500 mt-2">Email: ${email}</p>
    </div>
  `;
  
  // Gunakan API dari dashboard yang sudah ada
  fetch('dashboard/api/laporan_proses.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=list&email=${encodeURIComponent(email)}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'ok' && data.data.length > 0) {
      let html = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      `;
      
      data.data.forEach(item => {
        const rataNilai = item.rata_nilai || 'N/A';
        const statusClass = rataNilai !== 'N/A' ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200';
        const statusIcon = rataNilai !== 'N/A' ? 'fa-chart-line text-green-600' : 'fa-clock text-gray-500';
        const nilaiColor = rataNilai !== 'N/A' ? 'text-green-600' : 'text-gray-400';
        
        html += `
          <div class="bg-white rounded-xl border ${statusClass} p-6 shadow-sm hover:shadow-md transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                  ${(item.nama_mapel || 'MP').substring(0, 2).toUpperCase()}
                </div>
                <div>
                  <h4 class="font-bold text-gray-800 text-lg">${item.nama_mapel || 'Mata Pelajaran'}</h4>
                  <p class="text-sm text-gray-600">${item.nama_tentor || 'Tentor'}</p>
                </div>
              </div>
              <div class="text-right">
                <div class="text-2xl font-bold ${nilaiColor}">
                  ${rataNilai}
                </div>
                <p class="text-xs text-gray-500">Nilai Rata-rata</p>
              </div>
            </div>
            
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <i class="fa-solid ${statusIcon}"></i>
                <span class="text-sm text-gray-600">${item.tanggal}</span>
              </div>
              <button onclick="viewPerkembanganDetail('${item.id}')" 
                      class="px-3 py-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 flex items-center gap-1">
                <i class="fa-solid fa-eye"></i> Detail
              </button>
            </div>
          </div>
        `;
      });
      
      html += `</div>`;
      
      // Tambahkan summary card
      const totalMapel = data.data.length;
      const mapelDenganNilai = data.data.filter(item => item.rata_nilai !== 'N/A').length;
      const rataRataUmum = data.data
        .filter(item => item.rata_nilai !== 'N/A')
        .reduce((sum, item) => sum + parseFloat(item.rata_nilai), 0) / mapelDenganNilai || 0;
      
      html += `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center text-white">
                <i class="fa-solid fa-book text-lg"></i>
              </div>
              <div>
                <div class="text-2xl font-bold text-blue-700">${totalMapel}</div>
                <div class="text-sm text-blue-600">Total Mapel</div>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center text-white">
                <i class="fa-solid fa-chart-line text-lg"></i>
              </div>
              <div>
                <div class="text-2xl font-bold text-green-700">${mapelDenganNilai}</div>
                <div class="text-sm text-green-600">Mapel Dinilai</div>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl p-6 border border-purple-200">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center text-white">
                <i class="fa-solid fa-star text-lg"></i>
              </div>
              <div>
                <div class="text-2xl font-bold text-purple-700">${mapelDenganNilai > 0 ? rataRataUmum.toFixed(1) : 'N/A'}</div>
                <div class="text-sm text-purple-600">Rata-rata Umum</div>
              </div>
            </div>
          </div>
        </div>
      `;
      
      content.innerHTML = html;
    } else {
      content.innerHTML = `
        <div class="text-center py-12">
          <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-6">
            <i class="fa-solid fa-chart-line text-gray-400 text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Data Perkembangan</h3>
          <p class="text-gray-500 mb-4">Siswa ini belum memiliki data perkembangan pembelajaran.</p>
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md mx-auto">
            <p class="text-blue-800 text-sm">
              <i class="fa-solid fa-info-circle mr-2"></i>
              Data perkembangan akan muncul setelah tentor memberikan penilaian.
            </p>
          </div>
        </div>
      `;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    content.innerHTML = `
      <div class="text-center py-12">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-6">
          <i class="fa-solid fa-exclamation-triangle text-red-500 text-2xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-red-700 mb-2">Gagal Memuat Data</h3>
        <p class="text-gray-500 mb-4">Terjadi kesalahan saat memuat data perkembangan.</p>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 max-w-md mx-auto">
          <p class="text-red-800 text-sm">
            <i class="fa-solid fa-exclamation-circle mr-2"></i>
            Error: ${error.message}
          </p>
        </div>
      </div>
    `;
  });
}

// View Perkembangan Detail
function viewPerkembanganDetail(id) {
  fetch('dashboard/api/laporan_proses.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=detail&id=${id}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'ok') {
      Swal.fire({
        title: 'Detail Laporan Perkembangan',
        html: `
          <div class="space-y-6 text-left">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="flex items-center gap-3 bg-blue-50 rounded-lg p-4 shadow">
                <i class="fa-solid fa-user-graduate text-blue-600 text-2xl"></i>
                <div>
                  <div class="text-xs text-gray-500 font-semibold">Siswa</div>
                  <div class="text-base font-bold text-gray-800">${data.data.nama_siswa || data.data.email}</div>
                </div>
              </div>
              <div class="flex items-center gap-3 bg-green-50 rounded-lg p-4 shadow">
                <i class="fa-solid fa-book-open-reader text-green-600 text-2xl"></i>
                <div>
                  <div class="text-xs text-gray-500 font-semibold">Mapel</div>
                  <div class="text-base font-bold text-gray-800">${data.data.nama_mapel}</div>
                </div>
              </div>
              <div class="flex items-center gap-3 bg-yellow-50 rounded-lg p-4 shadow">
                <i class="fa-solid fa-calendar-days text-yellow-600 text-2xl"></i>
                <div>
                  <div class="text-xs text-gray-500 font-semibold">Tanggal</div>
                  <div class="text-base font-bold text-gray-800">${data.data.tanggal}</div>
                </div>
              </div>
              <div class="flex items-center gap-3 bg-purple-50 rounded-lg p-4 shadow">
                <i class="fa-solid fa-chalkboard-user text-purple-600 text-2xl"></i>
                <div>
                  <div class="text-xs text-gray-500 font-semibold">Tentor</div>
                  <div class="text-base font-bold text-gray-800">${data.data.tentor}</div>
                </div>
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-star text-yellow-500"></i> Penilaian
              </label>
              <div class="bg-gray-50 p-1 rounded-lg shadow">
                <table class="w-full text-sm">
                  <thead>
                    <tr class="text-gray-600">
                      <th class="py-1 px-3 text-left">No</th>
                      <th class="py-1 px-3 text-left">Jenis Penilaian</th>
                      <th class="py-1 px-3 text-left">Nilai</th>
                      <th class="py-1 px-3 text-left">Keterangan</th>
                    </tr>
                  </thead>
                  <tbody class="text-sm">
                    ${(data.data.nilai || []).map((n, i) => `
                      <tr>
                        <td class="py-1 px-3">${i+1}</td>
                        <td class="py-1 px-3 font-medium text-gray-700">${(data.data.jenis_penilaian && data.data.jenis_penilaian[i]) ? data.data.jenis_penilaian[i] : '-'}</td>
                        <td class="py-1 px-3 font-bold text-blue-700 flex items-center gap-1">
                          <i class="fa-solid fa-star text-yellow-400"></i> ${n}
                        </td>
                        <td class="py-1 px-3">${(data.data.keterangan && data.data.keterangan[i]) ? data.data.keterangan[i] : '-'}</td>
                      </tr>
                    `).join('')}
                    <tr class="bg-gray-100 font-semibold">
                      <td class="py-1 px-3" colspan="2">Rata-rata</td>
                      <td class="py-1 px-3 text-blue-700">${data.data.rata_nilai ?? '-'}</td>
                      <td class="py-1 px-3">-</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        `,
        width: '800px',
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#6b7280',
        showCloseButton: true,
        customClass: {
          container: 'swal2-custom-container',
          popup: 'swal2-custom-popup',
          content: 'swal2-custom-content'
        }
      });
    } else {
      Swal.fire({
        title: 'Error!',
        text: data.msg || 'Gagal mengambil detail laporan!',
        icon: 'error',
        confirmButtonText: 'OK'
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire({
      title: 'Error!',
      text: 'Terjadi kesalahan pada sistem!',
      icon: 'error',
      confirmButtonText: 'OK'
    });
  });
}

// View Transaksi Detail
function viewTransaksiDetail(id) {
  console.log('Viewing transaksi detail for ID:', id);
  
  // Tampilkan loading
  Swal.fire({
    title: 'Memuat Detail Transaksi...',
    text: 'Mohon tunggu sebentar',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });
  
  // Ambil data detail transaksi
  fetch('api/trx_proses.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=detail&id=${id}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'ok') {
      const transaksi = data.data;
      let jadwalHtml = '';
      
      if (transaksi.jadwal && transaksi.jadwal.length > 0) {
        jadwalHtml = `
          <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-sm">
              <thead>
                <tr class="bg-gradient-to-r from-blue-500 to-purple-600 text-white">
                  <th class="px-4 py-3 text-left font-semibold">No</th>
                  <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                  <th class="px-4 py-3 text-left font-semibold">Jam</th>
                  <th class="px-4 py-3 text-left font-semibold">Status</th>
                </tr>
              </thead>
              <tbody>
                ${transaksi.jadwal.map((jadwal, index) => {
                  const jadwalDate = new Date(jadwal.tanggal);
                  const today = new Date();
                  const isPast = jadwalDate < today;
                  const isToday = jadwalDate.toDateString() === today.toDateString();
                  
                  let statusClass = 'bg-gray-100 text-gray-700 border-gray-200';
                  let statusText = 'Belum';
                  let statusIcon = 'fa-solid fa-clock';
                  
                  if (isPast) {
                    statusClass = 'bg-green-100 text-green-700 border-green-200';
                    statusText = 'Selesai';
                    statusIcon = 'fa-solid fa-check-circle';
                  } else if (isToday) {
                    statusClass = 'bg-blue-100 text-blue-700 border-blue-200';
                    statusText = 'Hari Ini';
                    statusIcon = 'fa-solid fa-star';
                  }
                  
                  return `
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                      <td class="px-4 py-3 font-medium text-gray-700">${index + 1}</td>
                      <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                          <i class="fa-solid fa-calendar text-blue-500"></i>
                          <span class="font-medium">${new Date(jadwal.tanggal).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                          <i class="fa-solid fa-clock text-purple-500"></i>
                          <span class="font-medium">${jadwal.jam_trx || 'TBD'}</span>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold border ${statusClass}">
                          <i class="${statusIcon}"></i>
                          ${statusText}
                        </span>
                      </td>
                    </tr>
                  `;
                }).join('')}
              </tbody>
            </table>
          </div>
        `;
      } else {
        jadwalHtml = `
          <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg p-6 border border-yellow-200">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-info-circle text-white text-lg"></i>
              </div>
              <div>
                <p class="text-yellow-800 font-semibold">Belum Ada Jadwal</p>
                <p class="text-yellow-700 text-sm">Jadwal les belum diatur untuk transaksi ini.</p>
              </div>
            </div>
          </div>
        `;
      }
      
      Swal.fire({
        title: '<div class="flex items-center justify-center gap-3 mb-4"><div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg"><i class="fa-solid fa-receipt text-white text-xl"></i></div><span class="text-2xl font-bold text-gray-800">Detail Transaksi</span></div>',
        html: `
          <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-3 border border-blue-100 shadow-lg">
            <!-- Transaction Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-3">
              <div class="bg-white rounded-xl p-2 shadow-sm border border-blue-100">
                <div class="flex items-center gap-1 mb-1">
                  <div class="w-5 h-5 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-gift text-white text-xs"></i>
                  </div>
                  <span class="text-xs font-semibold text-gray-600">Paket</span>
                </div>
                <p class="text-sm font-bold text-gray-800">${transaksi.nama_paket}</p>
              </div>
              
              <div class="bg-white rounded-xl p-2 shadow-sm border border-purple-100">
                <div class="flex items-center gap-1 mb-1">
                  <div class="w-5 h-5 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-book-open text-white text-xs"></i>
                  </div>
                  <span class="text-xs font-semibold text-gray-600">Mapel</span>
                </div>
                <p class="text-sm font-bold text-gray-800">${transaksi.nama_mapel}</p>
              </div>
              
              <div class="bg-white rounded-xl p-2 shadow-sm border border-green-100">
                <div class="flex items-center gap-1 mb-1">
                  <div class="w-5 h-5 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-chalkboard-user text-white text-xs"></i>
                  </div>
                  <span class="text-xs font-semibold text-gray-600">Tentor</span>
                </div>
                <p class="text-sm font-bold text-gray-800">${transaksi.nama_tentor || 'Belum ditentukan'}</p>
              </div>
              
              <div class="bg-white rounded-xl p-2 shadow-sm border border-orange-100">
                <div class="flex items-center gap-1 mb-1">
                  <div class="w-5 h-5 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-user text-white text-xs"></i>
                  </div>
                  <span class="text-xs font-semibold text-gray-600">Nama Siswa</span>
                </div>
                <p class="text-sm font-bold text-gray-800">${transaksi.nama_siswa}</p>
              </div>
            </div>
            
            <!-- Payment Information -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-3 border border-green-200 shadow-sm mb-3">
              <div class="flex items-center gap-2 mb-2">
                <div class="w-6 h-6 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                  <i class="fa-solid fa-money-bill-wave text-white text-xs"></i>
                </div>
                <h4 class="text-sm font-bold text-gray-800">Informasi Pembayaran</h4>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="bg-white rounded-lg p-2 text-center shadow-sm">
                  <div class="text-xs text-gray-600 mb-1">Total Harga</div>
                  <div class="text-sm font-bold text-gray-800">Rp${Number(transaksi.harga).toLocaleString('id-ID')}</div>
                </div>
                <div class="bg-white rounded-lg p-2 text-center shadow-sm">
                  <div class="text-xs text-gray-600 mb-1">Sudah Bayar</div>
                  <div class="text-sm font-bold text-green-600">Rp${Number(transaksi.bayar).toLocaleString('id-ID')}</div>
                </div>
                <div class="bg-white rounded-lg p-2 text-center shadow-sm">
                  <div class="text-xs text-gray-600 mb-1">Sisa</div>
                  <div class="text-sm font-bold ${transaksi.harga - transaksi.bayar > 0 ? 'text-red-600' : 'text-green-600'}">
                    Rp${Number(transaksi.harga - transaksi.bayar).toLocaleString('id-ID')}
                  </div>
                </div>
              </div>
              
              <!-- Payment Status Badge -->
              <div class="mt-2 text-center">
                ${transaksi.harga - transaksi.bayar > 0 ? 
                  '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-red-100 text-red-700 font-semibold text-xs"><i class="fa-solid fa-exclamation-triangle"></i>Belum Lunas</span>' :
                  '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold text-xs"><i class="fa-solid fa-check-circle"></i>Lunas</span>'
                }
              </div>
            </div>
            
            <!-- Schedule Section -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-3 border border-blue-200 shadow-sm">
              <div class="flex items-center gap-2 mb-2">
                <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                  <i class="fa-solid fa-calendar-days text-white text-xs"></i>
                </div>
                <h4 class="text-sm font-bold text-gray-800">Jadwal Les</h4>
              </div>
              
              ${jadwalHtml}
            </div>
          </div>
        `,
        width: '400px',
        confirmButtonText: '<i class="fa-solid fa-times mr-2"></i>Tutup',
        confirmButtonColor: '#6b7280',
        showCloseButton: true,
        customClass: {
          popup: 'swal2-custom-popup',
          title: 'swal2-custom-title',
          htmlContainer: 'swal2-custom-html',
          confirmButton: 'swal2-custom-confirm'
        }
      });
    } else {
      Swal.fire(
        'Error!',
        data.message || 'Gagal memuat detail transaksi.',
        'error'
      );
    }
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire(
      'Error!',
      'Terjadi kesalahan pada sistem.',
      'error'
    );
  });
}

// Hapus Transaksi
function hapusTransaksi(id) {
  console.log('Deleting transaksi with ID:', id);
  
  Swal.fire({
    title: 'Konfirmasi Hapus',
    text: `Apakah Anda yakin ingin menghapus transaksi dengan ID: ${id}?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      // Kirim request hapus ke API
      fetch('api/hapus_trx.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'ok') {
          Swal.fire(
            'Berhasil!',
            'Transaksi berhasil dihapus.',
            'success'
          ).then(() => {
            // Reload halaman untuk refresh data
            location.reload();
          });
        } else {
          Swal.fire(
            'Gagal!',
            data.message || 'Terjadi kesalahan saat menghapus transaksi.',
            'error'
          );
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire(
          'Error!',
          'Terjadi kesalahan pada sistem.',
          'error'
        );
      });
    }
  });
}

// Load initial data when page loads
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM loaded, initializing detail siswa...');
  
  // Ensure transaksi tab is visible initially
  const transaksiTab = document.getElementById('tab-content-transaksi');
  if (transaksiTab) {
    transaksiTab.classList.remove('hidden');
    console.log('Transaksi tab activated');
  }
  
  // Add click handlers for tab buttons
  document.querySelectorAll('[onclick^="showTab"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const tabName = this.getAttribute('onclick').match(/showTab\('([^']+)'\)/)[1];
      showTab(tabName);
    });
  });
});

// Fallback function if onclick doesn't work
function showTabFallback(tabName) {
  console.log('Fallback showTab called:', tabName);
  showTab(tabName);
}

// Initialize everything when page loads
window.addEventListener('load', function() {
  console.log('Page fully loaded');
  
  // Show alert to confirm JavaScript is working
  console.log('JavaScript is working!');
  
  // Ensure transaksi tab is visible initially
  const transaksiTab = document.getElementById('tab-content-transaksi');
  if (transaksiTab) {
    transaksiTab.classList.remove('hidden');
    console.log('Transaksi tab activated');
  }
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

/* Simple Tab Styling */
.tab-btn {
  transition: all 0.3s ease;
  border: none;
  cursor: pointer;
}

.tab-btn:hover {
  opacity: 0.8;
}

/* Rapor Styling */
.rapor-card {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
}

.rapor-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.nilai-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  color: white;
  box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
}

.btn-hapus-baris {
  min-width: 28px;
  height: 28px;
  border-radius: 6px;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  transition: all 0.2s;
  box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
}

.btn-hapus-baris:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(239, 68, 68, 0.4);
}

/* Custom SweetAlert2 Styling */
.swal2-custom-popup {
  border-radius: 1rem !important;
  padding: 2rem !important;
}

.swal2-custom-content {
  text-align: left !important;
}

.swal2-popup {
  max-width: 800px !important;
  width: 90% !important;
}

.swal2-title {
  color: #1e40af !important;
  font-size: 1.5rem !important;
  font-weight: 700 !important;
  margin-bottom: 1.5rem !important;
}

.swal2-html-container {
  margin: 0 !important;
  padding: 0 !important;
}

.swal2-confirm {
  background: #6b7280 !important;
  border-radius: 0.5rem !important;
  padding: 0.5rem 1rem !important;
  font-weight: 600 !important;
  transition: all 0.2s !important;
}

.swal2-confirm:hover {
  background: #4b5563 !important;
  transform: translateY(-1px) !important;
}

// Print button
document.getElementById('btnPrint').addEventListener('click', function() {
    const id = <?= $siswa['id'] ?>;
    window.open(`dashboard/print/print_detail_siswa.php?id=${id}`, '_blank');
});
</style>
<?php include 'includes/footer.php'; ?> 