<?php include "header.php"; ?>

<?php
$tentor_id = $_SESSION['user_id'] ?? 0;
$tentor_nama = $_SESSION['user_nama'] ?? '';

// Ambil siswa yang diajar oleh tentor ini
$siswa = [];
try {
    $stmt = $pdo->prepare('SELECT DISTINCT t.email, s.nama FROM tb_trx t LEFT JOIN tb_siswa s ON t.email = s.email WHERE t.id_tentor = ? ORDER BY s.nama ASC, t.email ASC');
    $stmt->execute([$tentor_id]);
    $siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Ambil mapel yang diajar oleh tentor ini
$mapel = [];
try {
    $stmt = $pdo->prepare('SELECT DISTINCT m.id, m.nama FROM tb_mapel_tentor mt LEFT JOIN tb_mapel m ON mt.mapel = m.id WHERE mt.id_tentor = ? ORDER BY m.nama ASC');
    $stmt->execute([$tentor_id]);
    $mapel = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Ambil jenis penilaian
$jenisPenilaian = [];
try {
    $stmt = $pdo->query('SELECT id, nama_penilaian FROM tb_jenis_penilaian ORDER BY urutan ASC');
    $jenisPenilaian = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}
?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Laporan Perkembangan Siswa</h1>
            <p class="text-gray-600">Kelola dan lihat perkembangan siswa yang Anda ajarkan</p>
        </div>
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
        <div class="mt-4 flex gap-2">
            <button onclick="loadLaporan()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                <i class="fas fa-search mr-2"></i> Filter
            </button>
            <button onclick="resetFilter()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition">
                <i class="fas fa-redo mr-2"></i> Reset
            </button>
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
                        <?php foreach($mapel as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="tanggal" id="laporan-tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            if (data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data laporan</td></tr>';
            } else {
                data.data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${item.nama_siswa || item.email}</div>
                            <div class="text-sm text-gray-500">${item.email}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.nama_mapel}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.tanggal}</td>
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
            }
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error: ' + (data.msg || 'Gagal memuat data') + '</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('tbody-laporan').innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error memuat data</td></tr>';
    });
}

function resetFilter() {
    document.getElementById('filter-siswa').value = '';
    document.getElementById('filter-mapel').value = '';
    document.getElementById('filter-tanggal').value = '';
    loadLaporan();
}

// Modal handlers
document.getElementById('btn-tambah-laporan').addEventListener('click', function() {
    document.getElementById('modal-title-laporan').textContent = 'Tambah Laporan Perkembangan';
    document.getElementById('form-laporan').reset();
    document.getElementById('laporan-id').value = '';
    document.getElementById('laporan-tanggal').value = new Date().toISOString().split('T')[0];
    document.getElementById('modal-laporan').classList.remove('hidden');
});

document.getElementById('close-modal-laporan').addEventListener('click', function() {
    document.getElementById('modal-laporan').classList.add('hidden');
});

document.getElementById('btn-cancel-laporan').addEventListener('click', function() {
    document.getElementById('modal-laporan').classList.add('hidden');
});

document.getElementById('close-modal-detail').addEventListener('click', function() {
    document.getElementById('modal-detail-laporan').classList.add('hidden');
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
            document.getElementById('laporan-id').value = data.data.id;
            document.getElementById('laporan-email').value = data.data.email;
            document.getElementById('laporan-tanggal').value = data.data.tanggal;
            document.getElementById('laporan-mapel').value = data.data.mapel;
            
            // Set nilai penilaian
            const nilaiInputs = document.querySelectorAll('select[name="nilai[]"]');
            const keteranganInputs = document.querySelectorAll('textarea[name="keterangan[]"]');
            
            if (data.data.nilai && data.data.nilai.length > 0) {
                nilaiInputs.forEach((input, index) => {
                    if (data.data.nilai[index]) {
                        input.value = data.data.nilai[index];
                    }
                });
            }
            
            if (data.data.keterangan && data.data.keterangan.length > 0) {
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
            let penilaianHtml = '';
            data.data.penilaian.forEach(p => {
                penilaianHtml += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-2">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">${p.nama}</div>
                            ${p.keterangan ? '<div class="text-sm text-gray-600 mt-1">' + p.keterangan + '</div>' : ''}
                        </div>
                        <div class="ml-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${p.nilai >= 4 ? 'bg-green-100 text-green-800' : p.nilai >= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}">
                                ${p.nilai}
                            </span>
                        </div>
                    </div>
                `;
            });
            
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
                      <i class="fa-solid fa-star text-purple-600 text-2xl"></i>
                      <div>
                        <div class="text-xs text-gray-500 font-semibold">Nilai Rata-rata</div>
                        <div class="text-base font-bold text-gray-800">${data.data.rata_nilai}</div>
                      </div>
                    </div>
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Penilaian</h3>
                    ${penilaianHtml}
                  </div>
                </div>
            `;
            document.getElementById('modal-detail-laporan').classList.remove('hidden');
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

// Load data saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    loadLaporan();
});
</script>

<?php include "footer.php"; ?>

