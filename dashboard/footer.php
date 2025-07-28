      </main>
    </div>
  </div>

  <!-- Script SweetAlert -->
  <script>
    function logout() {
      Swal.fire({
        title: 'Logout?',
        text: 'Apakah Anda yakin ingin keluar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = '../api/logout.php';
        }
      });
    }
  </script>
  <script>
    // Dropdown user (untuk id dropdownUserBtn & dropdownUserMenu)
    const dropdownUserBtn = document.getElementById('dropdownUserBtn');
    const dropdownUserMenu = document.getElementById('dropdownUserMenu');
    document.addEventListener('click', function(e) {
      if (dropdownUserBtn && dropdownUserMenu) {
        if (dropdownUserBtn.contains(e.target)) {
          dropdownUserMenu.classList.toggle('hidden');
        } else if (!dropdownUserMenu.contains(e.target)) {
          dropdownUserMenu.classList.add('hidden');
        }
      }
    });
    // Handler menu edit profil
    const menuEditProfil = document.getElementById('menu-edit-profil');
    if (menuEditProfil) {
      menuEditProfil.onclick = function(e) {
        e.preventDefault();
        $('#form-user')[0].reset();
        $('#user-id').val('<?= $_SESSION['user_id'] ?? '' ?>');
        $('#user-email').val('<?= $_SESSION['user_email'] ?? '' ?>').prop('readonly', true);
        $('#user-nama').val('<?= $_SESSION['user_nama'] ?? '' ?>');
        $('#user-hp').val('<?= $_SESSION['user_hp'] ?? '' ?>');
        if (typeof setRoleDropdown === 'function') setRoleDropdown();
        $('#user-role').val('<?= $_SESSION['user_role'] ?? 'user' ?>');
        $('#modal-title').text('Edit Profil');
        $('#modal-user').removeClass('hidden');
        dropdownUserMenu.classList.add('hidden');
      };
    }
    // Handler menu ubah password
    const menuUbahPassword = document.getElementById('menu-ubah-password');
    if (menuUbahPassword) {
      menuUbahPassword.onclick = function(e) {
        e.preventDefault();
        $('#password-id').val('<?= $_SESSION['user_id'] ?? '' ?>');
        $('#password-baru').val('');
        $('#modal-password').removeClass('hidden');
        dropdownUserMenu.classList.add('hidden');
      };
    }
    // Fetch total siswa
    fetch('api/get_info.php')
      .then(res => res.json())
      .then(data => {
        var el = document.getElementById('totalSiswa');
        if(el && data.total_siswa !== undefined) {
          el.textContent = data.total_siswa;
        }
      });
  </script>
  <!-- Modal User (Edit Profil) -->
<div id="modal-user" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-96 max-w-sm mx-4 relative">
    <button id="close-modal-user" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-xl"><i class="fa fa-xmark"></i></button>
    <h2 class="text-lg font-bold text-blue-700 mb-4" id="modal-title">Edit Profil User</h2>
    <form id="form-user" autocomplete="off">
      <input type="hidden" name="id" id="user-id">
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Email</label>
        <input type="email" name="email" id="user-email" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Nama</label>
        <input type="text" name="nama" id="user-nama" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">HP</label>
        <input type="text" name="hp" id="user-hp" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Role</label>
        <select name="role" id="user-role" class="w-full border rounded px-2 py-1">
          <option value="s_admin">Super Admin</option>
          <option value="admin">Admin</option>
          <option value="tentor">Tentor</option>
          <option value="user">User</option>
        </select>
      </div>
      <div class="flex justify-end mt-4">
        <button type="submit" class="px-4 py-1 rounded bg-blue-600 text-white font-bold shadow hover:bg-blue-700"><i class="fa fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>
<!-- Modal Ganti Password -->
<div id="modal-password" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-80 max-w-xs mx-4 relative">
    <button id="close-modal-password" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-xl"><i class="fa fa-xmark"></i></button>
    <h2 class="text-lg font-bold text-blue-700 mb-4">Ganti Password</h2>
    <form id="form-password" autocomplete="off">
      <input type="hidden" name="id" id="password-id">
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Password Baru</label>
        <input type="password" name="password" id="password-baru" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="flex justify-end mt-4">
        <button type="submit" class="px-4 py-1 rounded bg-blue-600 text-white font-bold shadow hover:bg-blue-700"><i class="fa fa-key"></i> Ganti</button>
      </div>
    </form>
  </div>
</div>
<script>
// Modal User (Edit Profil)
$('#close-modal-user').click(function(){
  $('#modal-user').addClass('hidden');
});
// Modal Password
$('#close-modal-password').click(function(){
  $('#modal-password').addClass('hidden');
});
// Simpan user (edit profil)
$('#form-user').submit(function(e){
  e.preventDefault();
  let data = $(this).serializeArray();
  data.push({name:'action', value:'edit'});
  $.post('api/user_proses.php', data, function(res){
    if(res.success){
      Swal.fire('Sukses', 'Profil berhasil diupdate', 'success');
      $('#modal-user').addClass('hidden');
      location.reload();
    }else{
      Swal.fire('Gagal', res.msg||'Gagal menyimpan data', 'error');
    }
  },'json');
});
// Ganti password
$('#form-password').submit(function(e){
  e.preventDefault();
  let id = $('#password-id').val();
  let password = $('#password-baru').val();
  if(!password) { Swal.fire('Gagal','Password wajib diisi!','error'); return; }
  $.post('api/user_proses.php', {action:'change_password', id:id, password:password}, function(res){
    if(res.success){
      Swal.fire('Sukses', 'Password berhasil diganti', 'success');
      $('#modal-password').addClass('hidden');
    }else{
      Swal.fire('Gagal', res.msg||'Gagal mengganti password', 'error');
    }
  },'json');
});
</script>
</body>
</html>
