<?php include "header.php"; ?>

<?php
$tentor_id = $_SESSION['user_id'] ?? 0;

// Ambil data tentor
$tentor_data = [];
try {
    $stmt = $pdo->prepare('SELECT * FROM tb_user WHERE id = ?');
    $stmt->execute([$tentor_id]);
    $tentor_data = $stmt->fetch();
} catch(Exception $e) {
    $tentor_data = [];
}

// Ambil mata pelajaran yang diajar
$mapel_list = [];
try {
    $stmt = $pdo->prepare("
        SELECT m.nama 
        FROM tb_mapel_tentor mt
        LEFT JOIN tb_mapel m ON mt.mapel = m.id
        WHERE mt.id_tentor = ?
        ORDER BY m.nama ASC
    ");
    $stmt->execute([$tentor_id]);
    $mapel_list = $stmt->fetchAll();
} catch(Exception $e) {}
?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Profil Saya</h1>
        <p class="text-gray-600">Kelola informasi profil Anda</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Informasi Profil -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user text-blue-600"></i>
                Informasi Profil
            </h3>
            <form id="form-profile" class="space-y-4">
                <input type="hidden" name="id" id="user-id" value="<?= htmlspecialchars($tentor_data['id'] ?? '') ?>">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="user-email" value="<?= htmlspecialchars($tentor_data['email'] ?? '') ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           readonly>
                    <p class="text-xs text-gray-500 mt-1">Email tidak dapat diubah</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama</label>
                    <input type="text" name="nama" id="user-nama" value="<?= htmlspecialchars($tentor_data['nama'] ?? '') ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">No. HP</label>
                    <input type="text" name="hp" id="user-hp" value="<?= htmlspecialchars($tentor_data['hp'] ?? '') ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           required>
                </div>
                
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="location.reload()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                        <i class="fa fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Informasi Tambahan -->
        <div class="space-y-6">
            <!-- Mata Pelajaran yang Diajar -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-book text-green-600"></i>
                    Mata Pelajaran
                </h3>
                <?php if (empty($mapel_list)): ?>
                    <p class="text-gray-500 text-sm">Belum ada mata pelajaran yang ditugaskan</p>
                <?php else: ?>
                    <div class="space-y-2">
                        <?php foreach ($mapel_list as $mapel): ?>
                            <div class="flex items-center gap-2 p-2 bg-green-50 rounded-lg">
                                <i class="fas fa-check-circle text-green-600"></i>
                                <span class="text-sm font-medium text-gray-800"><?= htmlspecialchars($mapel['nama'] ?? '-') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Ganti Password -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-key text-purple-600"></i>
                    Ganti Password
                </h3>
                <form id="form-password" class="space-y-4">
                    <input type="hidden" name="id" id="password-id" value="<?= htmlspecialchars($tentor_data['id'] ?? '') ?>">
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="password" id="password-baru" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" 
                               required>
                    </div>
                    
                    <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">
                        <i class="fa fa-key"></i> Ganti Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Simpan profil
$('#form-profile').submit(function(e){
    e.preventDefault();
    let data = $(this).serializeArray();
    data.push({name:'action', value:'edit'});
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.post('../dashboard/api/user_proses.php', data, function(res){
        Swal.close();
        if(res.success){
            Swal.fire('Sukses', 'Profil berhasil diupdate', 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Gagal', res.msg || 'Gagal menyimpan data', 'error');
        }
    },'json').fail(function(){
        Swal.close();
        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
    });
});

// Ganti password
$('#form-password').submit(function(e){
    e.preventDefault();
    let id = $('#password-id').val();
    let password = $('#password-baru').val();
    
    if(!password || password.length < 6) {
        Swal.fire('Gagal', 'Password minimal 6 karakter!', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Mengganti password...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.post('../dashboard/api/user_proses.php', {
        action:'change_password', 
        id:id, 
        password:password
    }, function(res){
        Swal.close();
        if(res.success){
            Swal.fire('Sukses', 'Password berhasil diganti', 'success').then(() => {
                $('#password-baru').val('');
            });
        } else {
            Swal.fire('Gagal', res.msg || 'Gagal mengganti password', 'error');
        }
    },'json').fail(function(){
        Swal.close();
        Swal.fire('Error', 'Terjadi kesalahan saat mengganti password', 'error');
    });
});
</script>

<?php include "footer.php"; ?>

