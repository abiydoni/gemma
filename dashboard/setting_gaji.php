<?php
include 'header.php';
include '../api/db.php';

// Ambil data mapel untuk setting gaji
$mapel = [];
try {
    $stmt = $pdo->query('SELECT id, nama FROM tb_mapel WHERE status = 1 ORDER BY id ASC');
    $mapel = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}
?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Setting Presentase Gaji Tentor</h1>
            <p class="text-gray-600">Atur presentase gaji tentor berdasarkan mata pelajaran</p>
        </div>
        <button id="btnPrint" class="px-3 py-1 rounded bg-green-600 text-white font-bold shadow hover:bg-green-700"><i class="fa fa-print"></i> Print</button>
    </div>

    <!-- Form Setting Gaji -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Tambah/Edit Setting Gaji</h2>
            <button id="btn-init-sample" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 text-sm">
                <i class="fas fa-database mr-2"></i>Init Sample Data
            </button>
        </div>
        
        <form id="form-setting-gaji" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                    <select name="mapel" id="setting-mapel" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Pilih Mapel</option>
                        <?php foreach($mapel as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Presentase Gaji (%)</label>
                    <input type="number" name="presentase" id="setting-presentase" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="25.00" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <input type="text" name="keterangan" id="setting-keterangan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Keterangan setting gaji">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                    <i class="fas fa-save mr-2"></i>Simpan Setting
                </button>
            </div>
        </form>

        <!-- Tabel Setting Gaji -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mapel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Presentase</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-setting-gaji" class="bg-white divide-y divide-gray-200">
                    <!-- Data akan di-load via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Load setting gaji
function loadSettingGaji() {
    fetch('api/gaji_proses.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=list_setting'
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('tbody-setting-gaji');
        tbody.innerHTML = '';
        
        if (data.status === 'ok') {
            data.data.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.nama_mapel}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.presentase_gaji}%</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.keterangan || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="editSetting(${item.id})" class="text-blue-600 hover:text-blue-900 mr-3" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteSetting(${item.id})" class="text-red-600 hover:text-red-900" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Form submit setting gaji
document.getElementById('form-setting-gaji').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'save_setting');
    
    fetch('api/gaji_proses.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Setting gaji berhasil disimpan!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            this.reset();
            loadSettingGaji();
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

// Edit setting
function editSetting(id) {
    fetch('api/gaji_proses.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=detail_setting&id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            document.getElementById('setting-mapel').value = data.data.mapel;
            document.getElementById('setting-presentase').value = data.data.presentase_gaji;
            document.getElementById('setting-keterangan').value = data.data.keterangan || '';
            
            // Tambahkan hidden input untuk ID
            let hiddenInput = document.getElementById('setting-id');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.id = 'setting-id';
                hiddenInput.name = 'id';
                document.getElementById('form-setting-gaji').appendChild(hiddenInput);
            }
            hiddenInput.value = data.data.id;
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.msg || 'Gagal mengambil data setting!',
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

// Delete setting
function deleteSetting(id) {
    Swal.fire({
        title: 'Hapus Setting?',
        text: 'Data yang dihapus tidak bisa dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('api/gaji_proses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete_setting&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    Swal.fire({
                        title: 'Terhapus!',
                        text: 'Setting gaji berhasil dihapus!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    loadSettingGaji();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.msg || 'Gagal menghapus setting!',
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

// Load data saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    loadSettingGaji();
    
    // Print button
    document.getElementById('btnPrint').addEventListener('click', function() {
        window.open('print/print_setting_gaji.php', '_blank');
    });
});

// Init sample data
document.getElementById('btn-init-sample').addEventListener('click', function() {
    Swal.fire({
        title: 'Inisialisasi Sample Data?',
        text: 'Sistem akan menambahkan sample setting gaji untuk testing!',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Tambahkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('api/gaji_proses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=init_sample_data'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.msg,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    loadSettingGaji();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.msg || 'Gagal menambahkan sample data!',
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
</script>

<?php include 'footer.php'; ?> 