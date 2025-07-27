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

<div class="w-full px-2 sm:px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Daftar Gaji Tentor</h1>
        <p class="text-gray-600 text-sm">Kelola dan lihat daftar gaji tentor berdasarkan pembayaran siswa</p>
    </div>

    <!-- Tombol Hitung Gaji -->
    <div class="mb-4">
        <button id="btn-hitung-gaji" class="bg-green-600 hover:bg-green-700 text-white w-10 h-10 rounded-lg font-medium transition duration-200 flex items-center justify-center icon-btn" title="Hitung Gaji Tentor">
            <i class="fas fa-calculator"></i>
        </button>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow-md p-3 mb-4">
        <h2 class="text-base font-semibold text-gray-800 mb-2">Filter Data</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tentor</label>
                <select id="filter-tentor" class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Semua Tentor</option>
                    <?php foreach($tentor as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Bulan</label>
                <input type="month" id="filter-bulan" class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select id="filter-status" class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="dibayar">Dibayar</option>
                </select>
            </div>
            <div class="flex items-end">
                <button id="btn-refresh" class="bg-blue-600 hover:bg-blue-700 text-white w-8 h-8 rounded text-xs transition duration-200 flex items-center justify-center icon-btn" title="Refresh Data">
                    <i class="fas fa-refresh"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel Gaji Tentor -->
    <div class="w-full bg-white rounded-lg shadow-lg p-2 sm:p-4 border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 text-xs sm:text-sm" id="tabel-gaji-tentor">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider whitespace-nowrap">Tentor</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider whitespace-nowrap">Jumlah Les</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider whitespace-nowrap">Total Gaji</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider whitespace-nowrap">Bulan</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider whitespace-nowrap">Status</th>
                                <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-gaji-tentor" class="bg-white divide-y divide-gray-200">
                            <!-- Data akan di-load via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles untuk tabel responsive */
#tabel-gaji-tentor {
    min-width: 900px; /* Lebih compact untuk memastikan semua kolom terlihat */
}

@media (max-width: 768px) {
    #tabel-gaji-tentor {
        min-width: 800px; /* Lebih kecil untuk mobile */
    }
}

/* Memastikan container tidak overflow */
.overflow-x-auto {
    -webkit-overflow-scrolling: touch; /* Smooth scrolling di iOS */
    border-radius: 0.5rem;
}

/* Sticky header untuk tabel panjang */
#tabel-gaji-tentor thead {
    position: sticky;
    top: 0;
    z-index: 10;
}

/* Memastikan container tidak melebihi screen */
.w-full {
    max-width: 100%;
    overflow-x: hidden;
}

/* Responsive padding dan spacing */
@media (max-width: 640px) {
    .px-2 {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
    }
    
    .py-6 {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
}

/* Compact table styling */
#tabel-gaji-tentor td, #tabel-gaji-tentor th {
    padding: 0.5rem 0.25rem;
}

@media (min-width: 640px) {
    #tabel-gaji-tentor td, #tabel-gaji-tentor th {
        padding: 0.5rem;
    }
}

/* Aksi column styling */
#tabel-gaji-tentor td:last-child {
    min-width: 80px;
    max-width: 120px;
}

/* Modal styling */
#modal-bayar-gaji {
    transition: opacity 0.3s ease;
}

#modal-bayar-gaji.hidden {
    opacity: 0;
    pointer-events: none;
}

/* Icon button styling */
.icon-btn {
    min-width: 24px;
    min-height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.icon-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.icon-btn i {
    font-size: 0.875rem;
}

/* Button group styling */
.button-group {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    flex-wrap: nowrap;
}

.button-group .icon-btn {
    margin: 0;
    flex-shrink: 0;
}

/* Responsive button group */
@media (max-width: 640px) {
    .button-group {
        gap: 0.125rem;
    }
    
    .button-group .icon-btn {
        width: 20px;
        height: 20px;
    }
    
    .button-group .icon-btn i {
        font-size: 0.75rem;
    }
}

/* Modern Modal Animations */
@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes modalSlideOut {
    from {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
    to {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
}

.modal-enter {
    animation: modalSlideIn 0.3s ease-out;
}

.modal-exit {
    animation: modalSlideOut 0.2s ease-in;
}

/* Gradient Text Effects */
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Card Hover Effects */
.hover-card {
    transition: all 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Status Badge Animations */
.status-badge {
    transition: all 0.2s ease;
}

.status-badge:hover {
    transform: scale(1.05);
}

#modal-bayar-gaji > div {
    transform: scale(0.9);
    transition: transform 0.3s ease;
}

#modal-bayar-gaji:not(.hidden) > div {
    transform: scale(1);
}
</style>

<!-- Modal Bayar Gaji -->
<div id="modal-bayar-gaji" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
    <div class="bg-white rounded-lg shadow-xl p-3 w-80 max-w-xs mx-2 relative">
        <button id="close-modal-bayar" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-sm">
            <i class="fa fa-xmark"></i>
        </button>
        
        <h2 class="text-sm font-bold text-gray-800 mb-3">Bayar Gaji Tentor</h2>
        
        <form id="form-bayar-gaji">
            <input type="hidden" name="id" id="bayar-id">
            
            <div class="space-y-2">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tentor</label>
                    <div id="bayar-tentor" class="text-xs font-semibold text-gray-800 bg-gray-50 p-1 rounded"></div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Siswa</label>
                    <div id="bayar-siswa" class="text-xs text-gray-600 bg-gray-50 p-1 rounded"></div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Mapel</label>
                    <div id="bayar-mapel" class="text-xs text-gray-600 bg-gray-50 p-1 rounded"></div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Gaji</label>
                    <div id="bayar-jumlah" class="text-xs font-bold text-green-600 bg-green-50 p-1 rounded"></div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah yang Dibayar</label>
                    <input type="number" name="jumlah_dibayar" id="bayar-jumlah-dibayar" class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Masukkan jumlah yang dibayar..." required>
                    <small class="text-xs text-gray-500">Kosongkan untuk membayar penuh</small>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Pembayaran</label>
                    <input type="date" name="tanggal_pembayaran" id="bayar-tanggal" class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" id="bayar-keterangan" rows="1" class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Keterangan pembayaran..."></textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-2 mt-3">
                <button type="button" id="btn-cancel-bayar" class="px-2 py-1 text-xs border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition duration-200">
                    Batal
                </button>
                <button type="submit" class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition duration-200">
                    <i class="fas fa-money-bill-wave mr-1"></i>Bayar
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
    
    // Load data tanpa loading SweetAlert
    const tbody = document.getElementById('tbody-gaji-tentor');
    
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
            // Group data per tentor
            const groupedData = {};
            data.data.forEach(item => {
                if (!groupedData[item.id_tentor]) {
                    groupedData[item.id_tentor] = {
                        nama_tentor: item.nama_tentor,
                        items: [],
                        total_gaji: 0,
                        total_pending: 0,
                        total_dibayar: 0,
                        jumlah_les: 0
                    };
                }
                groupedData[item.id_tentor].items.push(item);
                groupedData[item.id_tentor].total_gaji += parseInt(item.jumlah_gaji);
                groupedData[item.id_tentor].jumlah_les += 1;
                if (item.status_pembayaran === 'pending') {
                    groupedData[item.id_tentor].total_pending += 1;
                } else {
                    groupedData[item.id_tentor].total_dibayar += 1;
                }
            });

            // Render grouped data
            Object.values(groupedData).forEach(group => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-2 py-2 whitespace-nowrap">
                        <div class="text-xs font-medium text-gray-900">${group.nama_tentor}</div>
                    </td>
                    <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">${group.jumlah_les} les</td>
                    <td class="px-2 py-2 whitespace-nowrap text-xs font-bold text-green-600">Rp ${group.total_gaji.toLocaleString()}</td>
                    <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">${group.items[0].bulan}</td>
                    <td class="px-2 py-2 whitespace-nowrap">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium ${group.total_pending === 0 ? 'bg-green-100 text-green-800' : group.total_dibayar === 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'}">
                            ${group.total_pending === 0 ? 'Lunas' : group.total_dibayar === 0 ? 'Pending' : 'Sebagian'}
                        </span>
                    </td>
                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">
                        <div class="button-group">
                            <button onclick="lihatDetailGaji('${group.nama_tentor}', ${JSON.stringify(group.items).replace(/"/g, '&quot;')})" class="bg-blue-500 hover:bg-blue-600 text-white w-6 h-6 rounded text-xs transition duration-200 flex items-center justify-center icon-btn" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${group.total_pending > 0 ? `
                                <button onclick="bayarSemuaGaji(${group.items.filter(item => item.status_pembayaran === 'pending').map(item => item.id).join(',')})" class="bg-green-500 hover:bg-green-600 text-white w-6 h-6 rounded text-xs transition duration-200 flex items-center justify-center icon-btn" title="Bayar Semua">
                                    <i class="fas fa-credit-card"></i>
                                </button>
                                <button onclick="bayarSebagianGaji('${group.nama_tentor}', ${group.total_gaji}, ${group.items.filter(item => item.status_pembayaran === 'pending').map(item => item.id).join(',')})" class="bg-yellow-500 hover:bg-yellow-600 text-white w-6 h-6 rounded text-xs transition duration-200 flex items-center justify-center icon-btn" title="Bayar Sebagian">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="px-2 py-2 text-center text-gray-500 text-xs">Tidak ada data</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Tampilkan error dengan SweetAlert
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Gagal memuat data gaji tentor!',
            confirmButtonText: 'OK'
        });
    });
}

// Hitung gaji
document.getElementById('btn-hitung-gaji').addEventListener('click', function() {
    Swal.fire({
        title: 'Hitung Gaji?',
        text: 'Sistem akan menghitung gaji tentor berdasarkan pembayaran siswa!',
        icon: 'question',
        showCancelButton: true,
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
                        text: data.msg || 'Gaji tentor berhasil dihitung!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        timer: 2000
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

// Lihat detail gaji tentor
function lihatDetailGaji(namaTentor, items) {
    let totalGaji = 0;
    let totalPending = 0;
    let totalDibayar = 0;
    
    items.forEach(item => {
        totalGaji += parseInt(item.jumlah_gaji);
        if (item.status_pembayaran === 'pending') {
            totalPending += 1;
        } else {
            totalDibayar += 1;
        }
    });

    let detailHtml = `
        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-4">
            <!-- Header Section -->
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-tie text-white text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-800">${namaTentor}</h2>
                        <p class="text-xs text-gray-600">Detail Gaji</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="text-sm font-bold text-green-600">Rp ${totalGaji.toLocaleString()}</p>
                    </div>
                    <div class="w-1 h-5 bg-gradient-to-b from-blue-400 to-indigo-500 rounded-full"></div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-3 gap-2 mb-4">
                <div class="bg-white rounded-lg p-2 shadow-sm border border-gray-100 hover-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Total</p>
                            <p class="text-sm font-bold text-gray-800">${items.length}</p>
                        </div>
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-book text-blue-600 text-xs"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-2 shadow-sm border border-gray-100 hover-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Dibayar</p>
                            <p class="text-sm font-bold text-green-600">${totalDibayar}</p>
                        </div>
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xs"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-2 shadow-sm border border-gray-100 hover-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Pending</p>
                            <p class="text-sm font-bold text-yellow-600">${totalPending}</p>
                        </div>
                        <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="mb-4">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium status-badge ${
                    totalPending === 0 ? 'bg-green-100 text-green-800' : 
                    totalDibayar === 0 ? 'bg-yellow-100 text-yellow-800' : 
                    'bg-blue-100 text-blue-800'
                }">
                    <i class="fas ${
                        totalPending === 0 ? 'fa-check-circle' : 
                        totalDibayar === 0 ? 'fa-clock' : 
                        'fa-pause-circle'
                    } mr-1"></i>
                    ${totalPending === 0 ? 'Lunas' : totalDibayar === 0 ? 'Pending' : 'Sebagian Dibayar'}
                </span>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-800">Detail Les</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mapel</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bayar</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">%</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gaji</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
    `;
    
    items.forEach((item, index) => {
        detailHtml += `
            <tr class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-2 py-2 text-xs text-gray-900">
                    <div class="flex items-center">
                        <div class="w-5 h-5 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center mr-1">
                            <span class="text-white text-xs font-medium">${item.email_siswa.charAt(0).toUpperCase()}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 text-xs truncate max-w-[80px]">${item.email_siswa}</p>
                        </div>
                    </div>
                </td>
                <td class="px-2 py-2 text-xs text-gray-900">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ${item.nama_mapel}
                    </span>
                </td>
                <td class="px-2 py-2 text-xs text-gray-900">
                    <span class="font-medium text-xs">${parseInt(item.total_pembayaran).toLocaleString()}</span>
                </td>
                <td class="px-2 py-2 text-xs text-gray-900">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        ${item.presentase_gaji}%
                    </span>
                </td>
                <td class="px-2 py-2 text-xs">
                    <span class="font-bold text-green-600 text-xs">${parseInt(item.jumlah_gaji).toLocaleString()}</span>
                </td>
                <td class="px-2 py-2 text-xs">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium ${
                        item.status_pembayaran === 'dibayar' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                    }">
                        <i class="fas ${
                            item.status_pembayaran === 'dibayar' ? 'fa-check-circle' : 'fa-clock'
                        } mr-1"></i>
                        ${item.status_pembayaran === 'dibayar' ? 'Dibayar' : 'Pending'}
                    </span>
                </td>
                <td class="px-2 py-2 text-xs">
                    ${item.status_pembayaran === 'pending' ? `
                        <button onclick="bayarGajiPerLes(${item.id}, '${item.nama_tentor}', '${item.email_siswa}', '${item.nama_mapel}', ${item.jumlah_gaji})" 
                                class="inline-flex items-center px-1.5 py-0.5 border border-transparent text-xs font-medium rounded-md text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-sm" 
                                title="Bayar Les Ini">
                            <i class="fas fa-credit-card mr-1"></i>
                            Bayar
                        </button>
                    ` : `
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-check mr-1"></i>
                            Selesai
                        </span>
                    `}
                </td>
            </tr>
        `;
    });
    
    detailHtml += `
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Summary -->
            <div class="mt-3 bg-white rounded-lg p-2 shadow-sm border border-gray-100 hover-card">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        <p class="text-sm font-semibold gradient-text">${
                            totalPending === 0 ? 'Lunas' : 
                            totalDibayar === 0 ? 'Belum Dibayar' : 
                            'Sebagian Dibayar'
                        }</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="text-sm font-bold text-green-600">Rp ${totalGaji.toLocaleString()}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Tampilkan detail dalam modal modern
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[85vh] overflow-y-auto transform transition-all duration-300 scale-100">
            <div class="sticky top-0 bg-white rounded-t-2xl px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Detail Gaji Tentor</h3>
                <button onclick="this.closest('.fixed').remove()" class="w-7 h-7 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors duration-200">
                    <i class="fas fa-times text-gray-600 text-sm"></i>
                </button>
            </div>
            <div class="p-4">
                ${detailHtml}
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Animasi masuk
    const modalContent = modal.querySelector('.bg-white');
    modalContent.style.transform = 'scale(0.9)';
    modalContent.style.opacity = '0';
    
    setTimeout(() => {
        modalContent.style.transform = 'scale(1)';
        modalContent.style.opacity = '1';
    }, 10);
    
    // Tutup modal ketika klik di luar
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            modalContent.style.transform = 'scale(0.9)';
            modalContent.style.opacity = '0';
            setTimeout(() => {
                this.remove();
            }, 200);
        }
    });
}

// Bayar gaji per les
function bayarGajiPerLes(id, namaTentor, emailSiswa, namaMapel, jumlahGaji) {
    Swal.fire({
        title: 'Bayar Gaji Per Les?',
        html: `
            <div class="text-left">
                <p><strong>Tentor:</strong> ${namaTentor}</p>
                <p><strong>Siswa:</strong> ${emailSiswa}</p>
                <p><strong>Mapel:</strong> ${namaMapel}</p>
                <p><strong>Jumlah Gaji:</strong> Rp ${parseInt(jumlahGaji).toLocaleString()}</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Bayar!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Proses pembayaran per les
            const formData = new FormData();
            formData.append('action', 'bayar_gaji');
            formData.append('id', id);
            formData.append('tanggal_pembayaran', new Date().toISOString().split('T')[0]);
            formData.append('keterangan', `Bayar gaji per les - ${namaMapel}`);
            
            fetch('api/gaji_proses.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Gaji per les berhasil dibayar!',
                        confirmButtonText: 'OK',
                        timer: 2000
                    }).then(() => {
                        loadGajiTentor();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal membayar gaji: ' + (data.msg || 'Terjadi kesalahan'),
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada sistem!',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

// Bayar sebagian gaji
function bayarSebagianGaji(namaTentor, totalGaji, ids) {
    Swal.fire({
        title: 'Bayar Sebagian Gaji?',
        html: `
            <div class="text-left">
                <p><strong>Tentor:</strong> ${namaTentor}</p>
                <p><strong>Total Gaji Pending:</strong> Rp ${totalGaji.toLocaleString()}</p>
                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah yang Dibayar</label>
                    <input type="number" id="jumlah-sebagian" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" placeholder="Masukkan jumlah yang dibayar..." value="${totalGaji}">
                    <small class="text-xs text-gray-500">Jumlah yang akan dibayar</small>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Bayar!',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const jumlah = document.getElementById('jumlah-sebagian').value;
            if (!jumlah || jumlah <= 0) {
                Swal.showValidationMessage('Jumlah harus lebih dari 0');
                return false;
            }
            if (jumlah > totalGaji) {
                Swal.showValidationMessage('Jumlah tidak boleh melebihi total gaji');
                return false;
            }
            return jumlah;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const jumlahDibayar = result.value;
            
            // Proses pembayaran sebagian
            const formData = new FormData();
            formData.append('action', 'bayar_sebagian');
            formData.append('ids', ids);
            formData.append('jumlah_dibayar', jumlahDibayar);
            formData.append('tanggal_pembayaran', new Date().toISOString().split('T')[0]);
            formData.append('keterangan', `Bayar sebagian gaji - ${namaTentor}`);
            
            fetch('api/gaji_proses.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pembayaran sebagian berhasil diproses!',
                        confirmButtonText: 'OK',
                        timer: 2000
                    }).then(() => {
                        loadGajiTentor();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal membayar gaji: ' + (data.msg || 'Terjadi kesalahan'),
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada sistem!',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

// Bayar semua gaji pending untuk satu tentor
function bayarSemuaGaji(ids) {
    Swal.fire({
        title: 'Bayar Semua Gaji?',
        text: 'Apakah Anda yakin ingin membayar semua gaji pending untuk tentor ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Bayar Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implementasi bayar semua gaji
            Swal.fire({
                title: 'Berhasil!',
                text: 'Semua gaji pending berhasil dibayar!',
                icon: 'success',
                timer: 2000
            }).then(() => {
                loadGajiTentor();
            });
        }
    });
}

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
            document.getElementById('bayar-jumlah-dibayar').value = data.data.jumlah_gaji;
            document.getElementById('bayar-tanggal').value = new Date().toISOString().split('T')[0];
            document.getElementById('bayar-keterangan').value = '';
            
            // Tampilkan modal
            const modal = document.getElementById('modal-bayar-gaji');
            modal.classList.remove('hidden');
            
            // Focus pada input tanggal
            document.getElementById('bayar-tanggal').focus();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal mengambil data gaji: ' + (data.msg || 'Terjadi kesalahan'),
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan pada sistem!',
            confirmButtonText: 'OK'
        });
    });
}

// Form submit bayar gaji
document.getElementById('form-bayar-gaji').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Konfirmasi Pembayaran',
        text: 'Apakah Anda yakin ingin membayar gaji ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Bayar!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
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
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.msg || 'Gaji berhasil dibayar!',
                        confirmButtonText: 'OK',
                        timer: 2000
                    }).then(() => {
                        document.getElementById('modal-bayar-gaji').classList.add('hidden');
                        loadGajiTentor();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal membayar gaji: ' + (data.msg || 'Terjadi kesalahan'),
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada sistem!',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
});

// Modal handlers
document.getElementById('close-modal-bayar').addEventListener('click', function() {
    document.getElementById('modal-bayar-gaji').classList.add('hidden');
});

document.getElementById('btn-cancel-bayar').addEventListener('click', function() {
    document.getElementById('modal-bayar-gaji').classList.add('hidden');
});

// Tutup modal ketika klik di luar modal
document.getElementById('modal-bayar-gaji').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

// Filter handlers
document.getElementById('filter-tentor').addEventListener('change', loadGajiTentor);
document.getElementById('filter-bulan').addEventListener('change', loadGajiTentor);
document.getElementById('filter-status').addEventListener('change', loadGajiTentor);
document.getElementById('btn-refresh').addEventListener('click', function() {
    loadGajiTentor();
});

// Load data saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {

    
    loadGajiTentor();
});


</script>

<?php include 'footer.php'; ?> 