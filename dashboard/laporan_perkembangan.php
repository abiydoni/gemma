<?php
include 'header.php';
include '../api/db.php';

// Ambil email siswa unik dari tb_trx dan join nama dari tb_user
$siswa = [];
try {
    $stmt = $pdo->query('SELECT t.email, u.nama FROM (SELECT DISTINCT email FROM tb_trx) t LEFT JOIN tb_user u ON t.email = u.email ORDER BY u.nama ASC, t.email ASC');
    $siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Ambil data mapel
$mapel = [];
try {
    $stmt = $pdo->query('SELECT id, nama FROM tb_mapel ORDER BY nama ASC');
    $mapel = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Ambil jenis penilaian
$jenisPenilaian = [];
try {
    $stmt = $pdo->query('SELECT id, nama_penilaian FROM tb_jenis_penilaian ORDER BY urutan ASC');
    $jenisPenilaian = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Ambil data tentor dari tb_user (role tentor)
$tentor = [];
try {
    $stmt = $pdo->query("SELECT id, nama FROM tb_user WHERE role='tentor' ORDER BY nama ASC");
    $tentor = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Debug: cek data tentor
    if(empty($tentor)) {
        echo "<!-- Debug: Tidak ada data tentor di tb_user dengan role='tentor' -->";
    }
} catch (Exception $e) {
    echo "<!-- Debug: Error loading tentor: " . $e->getMessage() . " -->";
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Laporan Perkembangan Siswa</h1>
        <p class="text-gray-600">Kelola dan lihat perkembangan siswa untuk semua mata pelajaran</p>
    </div>

    <!-- Filter dan Pencarian -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Siswa</label>
                <select id="filter-siswa" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Siswa</option>
                    <?php foreach($siswa as $s): ?>
                    <option value="<?= $s['email'] ?>"><?= htmlspecialchars($s['nama'] ? $s['nama'] : $s['email']) ?> (<?= htmlspecialchars($s['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Mapel</label>
                <select id="filter-mapel" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Mapel</option>
                    <?php foreach($mapel as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" id="filter-tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <!-- Tombol Tambah Laporan -->
    <div class="mb-6">
        <button id="btn-tambah-laporan" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Laporan Perkembangan
        </button>
    </div>

    <!-- Tabel Laporan -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mapel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tentor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Rata-rata</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-laporan" class="bg-white divide-y divide-gray-200">
                    <!-- Data akan di-load via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Laporan -->
<div id="modal-laporan" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto relative">
        <button id="close-modal-laporan" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 text-xl">
            <i class="fa fa-xmark"></i>
        </button>
        
        <h2 class="text-xl font-bold text-gray-800 mb-6" id="modal-title-laporan">Tambah Laporan Perkembangan</h2>
        
        <form id="form-laporan" autocomplete="off">
            <input type="hidden" name="id" id="laporan-id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Siswa</label>
                    <select name="email" id="laporan-email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Pilih Siswa</option>
                        <?php foreach($siswa as $s): ?>
                        <option value="<?= $s['email'] ?>"><?= htmlspecialchars($s['nama'] ? $s['nama'] : $s['email']) ?> (<?= htmlspecialchars($s['email']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                    <select name="mapel" id="laporan-mapel" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Pilih Mapel</option>
                        <!-- Opsi mapel akan diisi via JS -->
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="tanggal" id="laporan-tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tentor</label>
                    <?php if(!empty($tentor)): ?>
                    <select name="tentor" id="laporan-tentor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Pilih Tentor</option>
                        <?php foreach($tentor as $t): ?>
                        <option value="<?= htmlspecialchars($t['nama']) ?>"><?= htmlspecialchars($t['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php else: ?>
                    <input type="text" name="tentor" id="laporan-tentor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nama tentor" required>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Tabel Penilaian -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Penilaian</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Jenis Penilaian</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nilai (1-5)</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-penilaian">
                            <?php foreach($jenisPenilaian as $jp): ?>
                            <tr>
                                <td class="px-4 py-3 border-b border-gray-200">
                                    <input type="hidden" name="jenis_penilaian[]" value="<?= $jp['id'] ?>">
                                    <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($jp['nama_penilaian']) ?></span>
                                </td>
                                <td class="px-4 py-3 border-b border-gray-200">
                                    <select name="nilai[]" class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                                        <option value="">Pilih</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3 border-b border-gray-200">
                                    <textarea name="keterangan[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Keterangan..."></textarea>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" id="btn-cancel-laporan" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail Laporan -->
<div id="modal-detail-laporan" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-2xl relative">
        <button id="close-modal-detail" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 text-xl">
            <i class="fa fa-xmark"></i>
        </button>
        
        <div id="detail-content">
            <!-- Content akan di-load via AJAX -->
        </div>
    </div>
</div>

<script>
// Load data laporan
function loadLaporan() {
    const filterSiswa = document.getElementById('filter-siswa').value;
    const filterMapel = document.getElementById('filter-mapel').value;
    const filterTanggal = document.getElementById('filter-tanggal').value;
    
    fetch('api/laporan_proses.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=list&email=${filterSiswa}&mapel=${filterMapel}&tanggal=${filterTanggal}`
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('tbody-laporan');
        tbody.innerHTML = '';
        
        if (data.status === 'ok') {
            data.data.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${item.nama_siswa}</div>
                        <div class="text-sm text-gray-500">${item.email}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.nama_mapel}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.tanggal}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.nama_tentor}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${item.rata_nilai >= 4 ? 'bg-green-100 text-green-800' : item.rata_nilai >= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}">
                            ${item.rata_nilai}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="viewDetail(${item.id})" class="text-blue-600 hover:text-blue-900 mr-3" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="editLaporan(${item.id})" class="text-green-600 hover:text-green-900 mr-3" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteLaporan(${item.id})" class="text-red-600 hover:text-red-900" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Modal handlers
document.getElementById('btn-tambah-laporan').addEventListener('click', function() {
    document.getElementById('modal-title-laporan').textContent = 'Tambah Laporan Perkembangan';
    document.getElementById('form-laporan').reset();
    document.getElementById('laporan-id').value = '';
    document.getElementById('modal-laporan').classList.remove('hidden');
});

document.getElementById('close-modal-laporan').addEventListener('click', function() {
    document.getElementById('modal-laporan').classList.add('hidden');
});

document.getElementById('btn-cancel-laporan').addEventListener('click', function() {
    document.getElementById('modal-laporan').classList.add('hidden');
});

// Form submit
document.getElementById('form-laporan').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'save');
    
    fetch('api/laporan_proses.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Laporan berhasil disimpan!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            document.getElementById('modal-laporan').classList.add('hidden');
            loadLaporan();
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.msg || 'Terjadi kesalahan!',
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
});

// Fungsi untuk hapus laporan
function deleteLaporan(id) {
    Swal.fire({
        title: 'Hapus Laporan?',
        text: 'Data yang dihapus tidak bisa dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('api/laporan_proses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    Swal.fire({
                        title: 'Terhapus!',
                        text: 'Laporan berhasil dihapus!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    loadLaporan();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.msg || 'Gagal menghapus laporan!',
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
    });
}

// Fungsi untuk edit laporan
function editLaporan(id) {
    // Ambil data laporan berdasarkan ID
    fetch('api/laporan_proses.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=detail&id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            // Isi form dengan data yang ada
            document.getElementById('laporan-id').value = data.data.id;
            document.getElementById('laporan-email').value = data.data.email;
            document.getElementById('laporan-tanggal').value = data.data.tanggal;
            document.getElementById('laporan-tentor').value = data.data.tentor;
            
            // Set mapel (akan di-load via AJAX)
            $('#laporan-email').trigger('change');
            setTimeout(() => {
                document.getElementById('laporan-mapel').value = data.data.mapel;
            }, 500);
            
            // Set nilai penilaian
            if (data.data.nilai) {
                const nilaiInputs = document.querySelectorAll('select[name="nilai[]"]');
                nilaiInputs.forEach((input, index) => {
                    if (data.data.nilai[index]) {
                        input.value = data.data.nilai[index];
                    }
                });
            }
            
            // Set keterangan
            if (data.data.keterangan) {
                const keteranganInputs = document.querySelectorAll('textarea[name="keterangan[]"]');
                keteranganInputs.forEach((input, index) => {
                    if (data.data.keterangan[index]) {
                        input.value = data.data.keterangan[index];
                    }
                });
            }
            
            document.getElementById('modal-title-laporan').textContent = 'Edit Laporan Perkembangan';
            document.getElementById('modal-laporan').classList.remove('hidden');
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.msg || 'Gagal mengambil data laporan!',
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

// Fungsi untuk view detail laporan
function viewDetail(id) {
    // Ambil data laporan berdasarkan ID
    fetch('api/laporan_proses.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=detail&id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            // Tampilkan detail dalam modal dengan desain modern dan ikon
            const detailContent = document.getElementById('detail-content');
            detailContent.innerHTML = `
                <h2 class="text-2xl font-bold text-blue-700 mb-6 flex items-center gap-2">
                  <i class='fas fa-clipboard-list text-blue-500'></i> Detail Laporan Perkembangan
                </h2>
                <div class="space-y-6">
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
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2"><i class="fa-solid fa-star text-yellow-500"></i> Penilaian</label>
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
                              <td class="py-1 px-3 font-bold text-blue-700 flex items-center gap-1"><i class="fa-solid fa-star text-yellow-400"></i> ${n}</td>
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
                <div class="mt-8 flex justify-end">
                  <button onclick="document.getElementById('modal-detail-laporan').classList.add('hidden')" class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 flex items-center gap-2">
                    <i class="fa fa-xmark"></i> Tutup
                  </button>
                </div>
            `;
            document.getElementById('modal-detail-laporan').classList.remove('hidden');
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

// Close modal detail
document.getElementById('close-modal-detail').addEventListener('click', function() {
    document.getElementById('modal-detail-laporan').classList.add('hidden');
});

// Filter handlers
document.getElementById('filter-siswa').addEventListener('change', loadLaporan);
document.getElementById('filter-mapel').addEventListener('change', loadLaporan);
document.getElementById('filter-tanggal').addEventListener('change', loadLaporan);

// Update mapel saat siswa dipilih
$('#laporan-email').on('change', function() {
    var email = $(this).val();
    var $mapel = $('#laporan-mapel');
    $mapel.html('<option value="">Memuat...</option>');
    if(email) {
        $.get('api/get_mapel.php?email='+encodeURIComponent(email), function(data) {
            if(data.length > 0) {
                $mapel.html('<option value="">Pilih Mapel</option>');
                data.forEach(function(m) {
                    $mapel.append('<option value="'+m.id+'">'+m.nama+'</option>');
                });
            } else {
                $mapel.html('<option value="">Tidak ada mapel</option>');
            }
        },'json').fail(function(xhr, status, error) {
            $mapel.html('<option value="">Error loading mapel</option>');
        });
    } else {
        $mapel.html('<option value="">Pilih Mapel</option>');
    }
});

// Set mapel kosong saat modal tambah dibuka
$('#btn-tambah-laporan').on('click', function() {
    $('#laporan-mapel').html('<option value="">Pilih Mapel</option>');
});

// Load data saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    loadLaporan();
});

// Set tanggal hari ini
document.getElementById('laporan-tanggal').value = new Date().toISOString().split('T')[0];
</script>

<?php include 'footer.php'; ?> 