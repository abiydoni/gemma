<?php
include 'header.php';
include '../api/db.php';

// Ambil data mapel
$mapel = [];
try {
    $stmt = $pdo->query('SELECT id, kode, nama FROM tb_mapel WHERE status = 1 ORDER BY nama ASC');
    $mapel = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Ambil data tentor
$tentor = [];
try {
    $stmt = $pdo->query("SELECT id, nama FROM tb_user WHERE role='tentor' ORDER BY nama ASC");
    $tentor = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}
?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Mapping Mapel-Tentor</h1>
            <p class="text-gray-600">Atur tentor untuk setiap mata pelajaran</p>
        </div>
        <button id="btnPrint" class="px-3 py-1 rounded bg-green-600 text-white font-bold shadow hover:bg-green-700"><i class="fa fa-print"></i> Print</button>
    </div>

    <!-- Form Mapping -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Tambah/Edit Mapping</h2>
        
        <form id="form-mapping" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                    <select name="mapel" id="mapping-mapel" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Pilih Mapel</option>
                        <?php foreach($mapel as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tentor</label>
                    <select name="tentor" id="mapping-tentor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Pilih Tentor</option>
                        <?php foreach($tentor as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                    <i class="fas fa-save mr-2"></i>Simpan Mapping
                </button>
            </div>
        </form>

        <!-- Tabel Mapping -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mapel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tentor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-mapping" class="bg-white divide-y divide-gray-200">
                    <!-- Data akan di-load via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Load mapping
function loadMapping() {
    fetch('api/mapel_tentor_proses.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=list'
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('tbody-mapping');
        tbody.innerHTML = '';
        
        if (data.status === 'ok') {
            data.data.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.nama_mapel}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.nama_tentor}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="editMapping(${item.id})" class="text-blue-600 hover:text-blue-900 mr-3" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteMapping(${item.id})" class="text-red-600 hover:text-red-900" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Form submit mapping
document.getElementById('form-mapping').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'save');
    
    fetch('api/mapel_tentor_proses.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Mapping berhasil disimpan!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            this.reset();
            loadMapping();
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

// Edit mapping
function editMapping(id) {
    fetch('api/mapel_tentor_proses.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=detail&id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            document.getElementById('mapping-mapel').value = data.data.mapel;
            document.getElementById('mapping-tentor').value = data.data.id_tentor;
            
            // Tambahkan hidden input untuk ID
            let hiddenInput = document.getElementById('mapping-id');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.id = 'mapping-id';
                hiddenInput.name = 'id';
                document.getElementById('form-mapping').appendChild(hiddenInput);
            }
            hiddenInput.value = data.data.id;
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.msg || 'Gagal mengambil data mapping!',
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

// Delete mapping
function deleteMapping(id) {
    Swal.fire({
        title: 'Hapus Mapping?',
        text: 'Data yang dihapus tidak bisa dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('api/mapel_tentor_proses.php', {
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
                        text: 'Mapping berhasil dihapus!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    loadMapping();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.msg || 'Gagal menghapus mapping!',
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
    loadMapping();
    
    // Print button
    document.getElementById('btnPrint').addEventListener('click', function() {
        window.open('print/print_mapel_tentor.php', '_blank');
    });
});
</script>

<?php include 'footer.php'; ?> 