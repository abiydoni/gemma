<?php
include 'header.php';
?>
<div class="max-w-full mx-auto bg-white rounded-3xl shadow-2xl p-6 md:p-10 border border-blue-100 mt-8">
  <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 mb-6 flex items-center gap-3">
    <i class="fa fa-user-cog text-blue-600"></i> Update Profile
  </h1>
  <form id="form-profile" enctype="multipart/form-data" autocomplete="off">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-bold mb-1">Nama</label>
        <input type="text" name="nama" id="profile-nama" class="w-full border rounded px-2 py-1" required>
      </div>
      <div>
        <label class="block text-sm font-bold mb-1">Email</label>
        <input type="email" name="email" id="profile-email" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-bold mb-1">Keterangan</label>
        <input type="text" name="keterangan" id="profile-keterangan" class="w-full border rounded px-2 py-1">
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-bold mb-1">Alamat</label>
        <input type="text" name="alamat" id="profile-alamat" class="w-full border rounded px-2 py-1">
      </div>
      <div>
        <label class="block text-sm font-bold mb-1">WhatsApp</label>
        <input type="text" name="wa" id="profile-wa" class="w-full border rounded px-2 py-1">
      </div>
      <div>
        <label class="block text-sm font-bold mb-1">Instagram</label>
        <input type="text" name="ig" id="profile-ig" class="w-full border rounded px-2 py-1">
      </div>
      <div>
        <label class="block text-sm font-bold mb-1">Logo 1</label>
        <input type="file" name="logo1" id="profile-logo1" accept="image/*" class="hidden">
        <img id="preview-logo1" src="../assets/img/default.png" alt="Logo 1" class="mt-2 h-24 w-24 object-contain rounded shadow cursor-pointer border border-blue-200 bg-gray-50" />
      </div>
      <div>
        <label class="block text-sm font-bold mb-1">Logo 2</label>
        <input type="file" name="logo2" id="profile-logo2" accept="image/*" class="hidden">
        <img id="preview-logo2" src="../assets/img/default.png" alt="Logo 2" class="mt-2 h-24 w-24 object-contain rounded shadow cursor-pointer border border-blue-200 bg-gray-50" />
      </div>
    </div>
    <div class="flex justify-end mt-8">
      <button type="submit" class="px-6 py-2 rounded bg-blue-600 text-white font-bold shadow hover:bg-blue-700"><i class="fa fa-save"></i> Simpan</button>
    </div>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function loadProfile() {
  $.post('api/profile_proses.php', {action:'get'}, function(res) {
    if(res.success && res.data) {
      $('#profile-nama').val(res.data.nama||'');
      $('#profile-keterangan').val(res.data.keterangan||'');
      $('#profile-alamat').val(res.data.alamat||'');
      $('#profile-email').val(res.data.email||'');
      $('#profile-wa').val(res.data.wa||'');
      $('#profile-ig').val(res.data.ig||'');
      if(res.data.logo1) {
        $('#preview-logo1').attr('src','../assets/img/'+res.data.logo1).removeClass('hidden');
      } else {
        $('#preview-logo1').attr('src','../assets/img/default.png').removeClass('hidden');
      }
      if(res.data.logo2) {
        $('#preview-logo2').attr('src','../assets/img/'+res.data.logo2).removeClass('hidden');
      } else {
        $('#preview-logo2').attr('src','../assets/img/default.png').removeClass('hidden');
      }
    }
  },'json');
}

$(document).ready(function(){
  loadProfile();

  // Klik gambar untuk upload logo1
  $('#preview-logo1').on('click', function(){
    $('#profile-logo1').trigger('click');
  });
  // Klik gambar untuk upload logo2
  $('#preview-logo2').on('click', function(){
    $('#profile-logo2').trigger('click');
  });
  // Preview logo1
  $('#profile-logo1').on('change', function(e){
    const [file] = this.files;
    if(file) {
      $('#preview-logo1').attr('src', URL.createObjectURL(file)).removeClass('hidden');
    }
  });
  // Preview logo2
  $('#profile-logo2').on('change', function(e){
    const [file] = this.files;
    if(file) {
      $('#preview-logo2').attr('src', URL.createObjectURL(file)).removeClass('hidden');
    }
  });

  // Submit form
  $('#form-profile').submit(function(e){
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('action','update');
    $.ajax({
      url: 'api/profile_proses.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(res) {
        if(res.success) {
          Swal.fire('Sukses','Profile berhasil diupdate','success');
          loadProfile();
        } else {
          Swal.fire('Gagal', res.msg||'Gagal update profile', 'error');
        }
      },
      error: function() {
        Swal.fire('Gagal','Terjadi error saat update profile','error');
      }
    });
  });
});
</script>
<?php include 'footer.php'; ?> 