<?php
include 'header.php';
include '../api/db.php';

// Ambil data tentor
$tentor = [];
try {
    $stmt = $pdo->query("SELECT id, nama FROM tb_user WHERE role='tentor' ORDER BY nama ASC");
    $tentor = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}
?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Daftar Gaji Tentor</h1>
        <p class="text-gray-600">Kelola dan lihat daftar gaji tentor berdasarkan pembayaran siswa</p>
    </div>

    <!-- Tombol Hitung Gaji -->
    <div class="mb-6">
        <button id="btn-hitung-gaji" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
            <i class="fas fa-calculator mr-2"></i>
            Hitung Gaji Tentor
        </button>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Data</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tentor</label>
                <select id="filter-tentor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Tentor</option>
                    <?php foreach($tentor as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <input type="month" id="filter-bulan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="filter-status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="dibayar">Dibayar</option>
                </select>
            </div>
            <div class="flex items-end">
                <button id="btn-refresh" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    <i class="fas fa-refresh mr-2"></i>Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel Gaji Tentor -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tentor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mapel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pembayaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Presentase</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Gaji</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-gaji-tentor" class="bg-white divide-y divide-gray-200">
                    <!-- Data akan di-load via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Bayar Gaji -->
<div id="modal-bayar-gaji" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md relative">
        <button id="close-modal-bayar" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 text-xl">
            <i class="fa fa-xmark"></i>
        </button>
        
        <h2 class="text-xl font-bold text-gray-800 mb-6">Bayar Gaji Tentor</h2>
        
        <form id="form-bayar-gaji">
            <input type="hidden" name="id" id="bayar-id">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tentor</label>
                <div id="bayar-tentor" class="text-lg font-semibold text-gray-800"></div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Siswa</label>
                <div id="bayar-siswa" class="text-sm text-gray-600"></div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Mapel</label>
                <div id="bayar-mapel" class="text-sm text-gray-600"></div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Gaji</label>
                <div id="bayar-jumlah" class="text-lg font-semibold text-green-600"></div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pembayaran</label>
                <input type="date" name="tanggal_pembayaran" id="bayar-tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <textarea name="keterangan" id="bayar-keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Keterangan pembayaran..."></textarea>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" id="btn-cancel-bayar" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                    Bayar Gaji
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Load gaji tentor
function loadGajiTentor() {
    const filterTentor = document.getElementById('filter-tentor').value;
    const filterBulan = document.getElementById('filter-bulan').value;
    const filterStatus = document.getElementById('filter-status').value;
    
    fetch('api/gaji_proses.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=list_gaji&tentor=${filterTentor}&bulan=${filterBulan}&status=${filterStatus}`
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('tbody-gaji-tentor');
        tbody.innerHTML = '';
        
        if (data.status === 'ok') {
            data.data.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${item.nama_tentor}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.email_siswa}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.nama_mapel}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp ${parseInt(item.total_pembayaran).toLocaleString()}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.presentase_gaji}%</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">Rp ${parseInt(item.jumlah_gaji).toLocaleString()}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.bulan}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${item.status_pembayaran === 'dibayar' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                            ${item.status_pembayaran === 'dibayar' ? 'Dibayar' : 'Pending'}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        ${item.status_pembayaran === 'pending' ? `
                            <button onclick="bayarGaji(${item.id})" class="text-green-600 hover:text-green-900" title="Bayar Gaji">
                                <i class="fas fa-money-bill-wave"></i>
                            </button>
                        ` : ''}
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Hitung gaji
document.getElementById('btn-hitung-gaji').addEventListener('click', function() {
    Swal.fire({
        title: 'Hitung Gaji?',
        text: 'Sistem akan menghitung gaji tentor berdasarkan pembayaran siswa!',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hitung!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('api/gaji_proses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=hitung_gaji'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Gaji tentor berhasil dihitung!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    loadGajiTentor();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.msg || 'Gagal menghitung gaji!',
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
});

// Bayar gaji
function bayarGaji(id) {
    fetch('api/gaji_proses.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=detail_gaji&id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            document.getElementById('bayar-id').value = data.data.id;
            document.getElementById('bayar-tentor').textContent = data.data.nama_tentor;
            document.getElementById('bayar-siswa').textContent = data.data.email_siswa;
            document.getElementById('bayar-mapel').textContent = data.data.nama_mapel;
            document.getElementById('bayar-jumlah').textContent = `Rp ${parseInt(data.data.jumlah_gaji).toLocaleString()}`;
            document.getElementById('bayar-tanggal').value = new Date().toISOString().split('T')[0];
            document.getElementById('modal-bayar-gaji').classList.remove('hidden');
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.msg || 'Gagal mengambil data gaji!',
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

// Form submit bayar gaji
document.getElementById('form-bayar-gaji').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'bayar_gaji');
    
    fetch('api/gaji_proses.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Gaji berhasil dibayar!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            document.getElementById('modal-bayar-gaji').classList.add('hidden');
            loadGajiTentor();
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.msg || 'Gagal membayar gaji!',
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

// Modal handlers
document.getElementById('close-modal-bayar').addEventListener('click', function() {
    document.getElementById('modal-bayar-gaji').classList.add('hidden');
});

document.getElementById('btn-cancel-bayar').addEventListener('click', function() {
    document.getElementById('modal-bayar-gaji').classList.add('hidden');
});

// Filter handlers
document.getElementById('filter-tentor').addEventListener('change', loadGajiTentor);
document.getElementById('filter-bulan').addEventListener('change', loadGajiTentor);
document.getElementById('filter-status').addEventListener('change', loadGajiTentor);
document.getElementById('btn-refresh').addEventListener('click', loadGajiTentor);

// Load data saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    loadGajiTentor();
});
</script>

<?php include 'footer.php'; ?> 