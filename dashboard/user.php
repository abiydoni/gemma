<?php
include 'header.php';
?>
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 flex items-center gap-2">
    <i class="fa fa-users-cog text-blue-600"></i> Data User
  </h1>
  <button id="btn-tambah" class="px-3 py-1 rounded bg-blue-600 text-white font-bold shadow hover:bg-blue-700"><i class="fa fa-plus"></i> Tambah User</button>
</div>
<div class="max-w-full mx-auto bg-white rounded-3xl shadow-2xl p-4 md:p-8 border border-blue-100 mt-6">
  <div class="overflow-x-auto">
    <table class="min-w-full text-xs md:text-sm border border-blue-300 rounded-xl shadow overflow-hidden" id="tabel-user">
      <thead>
        <tr class="bg-blue-100 text-blue-800">
          <th class="py-3 px-3 border-b border-blue-200 rounded-tl-xl">No</th>
          <th class="py-3 px-3 border-b border-blue-200">Email</th>
          <th class="py-3 px-3 border-b border-blue-200">Nama</th>
          <th class="py-3 px-3 border-b border-blue-200">HP</th>
          <th class="py-3 px-3 border-b border-blue-200">Role</th>
          <th class="py-3 px-3 border-b border-blue-200 rounded-tr-xl text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>
<!-- Modal User -->
<div id="modal-user" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md relative">
    <button id="close-modal-user" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-xl"><i class="fa fa-xmark"></i></button>
    <h2 class="text-lg font-bold text-blue-700 mb-4" id="modal-title">Tambah User</h2>
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
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-xs relative">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function loadUser() {
  $.post('api/user_proses.php', {action:'list'}, function(res) {
    if(res.success) {
      let html = '';
      const myId = '<?= $_SESSION['user_id'] ?? 0 ?>';
      res.data.forEach(function(row, i) {
        let disableDelete = (row.id == myId) ? 'disabled style="opacity:.5;cursor:not-allowed"' : '';
        html += `<tr class="hover:bg-blue-50 border-b border-blue-100 transition-all">
          <td class='py-2 px-3 border-blue-100 text-center'>${i+1}</td>
          <td class='py-2 px-3 border-blue-100'>${row.email}</td>
          <td class='py-2 px-3 border-blue-100'>${row.nama}</td>
          <td class='py-2 px-3 border-blue-100'>${row.hp||''}</td>
          <td class='py-2 px-3 border-blue-100 capitalize'>${row.role.replace('_',' ')}</td>
          <td class="text-center py-2 px-3 border-blue-100">
            <button class='btn-edit text-blue-600 hover:text-blue-900 mr-2' data-id='${row.id}'><i class='fa fa-pen'></i></button>
            <button class='btn-password text-yellow-600 hover:text-yellow-900 mr-2' data-id='${row.id}'><i class='fa fa-key'></i></button>
            <button class='btn-hapus text-red-600 hover:text-red-900' data-id='${row.id}' ${disableDelete}><i class='fa fa-trash'></i></button>
          </td>
        </tr>`;
      });
      $('#tabel-user tbody').html(html);
    }
  },'json');
}

function getAllowedRoles() {
  const myRole = '<?= $_SESSION['user_role'] ?? 'user' ?>';
  if (myRole === 's_admin') {
    return [
      {value:'s_admin', label:'Super Admin', level:4},
      {value:'admin', label:'Admin', level:3},
      {value:'tentor', label:'Tentor', level:2},
      {value:'user', label:'User', level:1}
    ];
  } else if (myRole === 'admin') {
    return [
      {value:'admin', label:'Admin', level:3},
      {value:'tentor', label:'Tentor', level:2},
      {value:'user', label:'User', level:1}
    ];
  } else if (myRole === 'tentor') {
    return [
      {value:'tentor', label:'Tentor', level:2},
      {value:'user', label:'User', level:1}
    ];
  } else {
    return [
      {value:'user', label:'User', level:1}
    ];
  }
}

function setRoleDropdown() {
  const allowed = getAllowedRoles();
  const $role = $('#user-role');
  $role.empty();
  allowed.forEach(r => {
    $role.append(`<option value="${r.value}">${r.label}</option>`);
  });
}

$(document).ready(function(){
  loadUser();

  $('#btn-tambah').click(function(){
    $('#form-user')[0].reset();
    $('#user-id').val('');
    $('#modal-title').text('Tambah User');
    $('#user-email').prop('readonly', false);
    setRoleDropdown();
    $('#modal-user').removeClass('hidden');
  });
  $('#close-modal-user').click(function(){
    $('#modal-user').addClass('hidden');
  });
  $('#close-modal-password').click(function(){
    $('#modal-password').addClass('hidden');
  });

  // Simpan user (tambah/edit)
  $('#form-user').submit(function(e){
    e.preventDefault();
    let data = $(this).serializeArray();
    let id = $('#user-id').val();
    if(!id) {
      // Tambah user, wajib password
      let pass = prompt('Masukkan password awal user:');
      if(!pass) { Swal.fire('Gagal','Password wajib diisi!','error'); return; }
      data.push({name:'password', value:pass});
      data.push({name:'action', value:'add'});
    } else {
      data.push({name:'action', value:'edit'});
    }
    $.post('api/user_proses.php', data, function(res){
      if(res.success){
        Swal.fire('Sukses', 'Data user berhasil disimpan', 'success');
        $('#modal-user').addClass('hidden');
        loadUser();
      }else{
        Swal.fire('Gagal', res.msg||'Gagal menyimpan data', 'error');
      }
    },'json');
  });

  // Edit user
  $('#tabel-user').on('click','.btn-edit',function(){
    let id = $(this).data('id');
    $.post('api/user_proses.php', {action:'list'}, function(res){
      if(res.success){
        let row = res.data.find(r=>r.id==id);
        if(row){
          $('#user-id').val(row.id);
          $('#user-email').val(row.email).prop('readonly', true);
          $('#user-nama').val(row.nama);
          $('#user-hp').val(row.hp||'');
          setRoleDropdown();
          $('#user-role').val(row.role);
          $('#modal-title').text('Edit User');
          $('#modal-user').removeClass('hidden');
        }
      }
    },'json');
  });

  // Hapus user
  $('#tabel-user').on('click','.btn-hapus',function(){
    let id = $(this).data('id');
    Swal.fire({
      title:'Hapus User?',
      text:'Data yang dihapus tidak bisa dikembalikan!',
      icon:'warning',
      showCancelButton:true,
      confirmButtonText:'Hapus',
      cancelButtonText:'Batal'
    }).then((result)=>{
      if(result.isConfirmed){
        $.post('api/user_proses.php', {action:'delete', id:id}, function(res){
          if(res.success){
            Swal.fire('Terhapus','Data user berhasil dihapus','success');
            loadUser();
          }else{
            Swal.fire('Gagal',res.msg||'Gagal menghapus data','error');
          }
        },'json');
      }
    });
  });

  // Ganti password
  $('#tabel-user').on('click','.btn-password',function(){
    let id = $(this).data('id');
    $('#password-id').val(id);
    $('#password-baru').val('');
    $('#modal-password').removeClass('hidden');
  });
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
});
</script>
<?php include 'footer.php'; ?> 