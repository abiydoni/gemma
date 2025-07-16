<?php
include 'header.php';
?>
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 flex items-center gap-2">
    <i class="fa fa-book text-blue-600"></i> Data Mapel
  </h1>
  <button id="btn-tambah" class="px-3 py-1 rounded bg-blue-600 text-white font-bold shadow hover:bg-blue-700"><i class="fa fa-plus"></i> Tambah Mapel</button>
</div>
<div class="max-w-full mx-auto bg-white rounded-3xl shadow-2xl p-4 md:p-8 border border-blue-100 mt-6">
  <div class="overflow-x-auto">
    <table class="min-w-full text-xs md:text-sm border border-blue-300 rounded-xl shadow overflow-hidden" id="tabel-mapel">
      <thead>
        <tr class="bg-blue-100 text-blue-800">
          <th class="py-3 px-3 border-b border-blue-200 rounded-tl-xl">No</th>
          <th class="py-3 px-3 border-b border-blue-200">Kode</th>
          <th class="py-3 px-3 border-b border-blue-200">Nama</th>
          <th class="py-3 px-3 border-b border-blue-200">Keterangan</th>
          <th class="py-3 px-3 border-b border-blue-200 text-center">Status</th>
          <th class="py-3 px-3 border-b border-blue-200 rounded-tr-xl text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>
<!-- Modal Mapel -->
<div id="modal-mapel" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md relative">
    <button id="close-modal-mapel" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-xl"><i class="fa fa-xmark"></i></button>
    <h2 class="text-lg font-bold text-blue-700 mb-4" id="modal-title">Tambah Mapel</h2>
    <form id="form-mapel" autocomplete="off">
      <input type="hidden" name="id" id="mapel-id">
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Kode</label>
        <input type="text" name="kode" id="mapel-kode" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Nama</label>
        <input type="text" name="nama" id="mapel-nama" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Keterangan</label>
        <input type="text" name="keterangan" id="mapel-keterangan" class="w-full border rounded px-2 py-1">
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Status</label>
        <select name="status" id="mapel-status" class="w-full border rounded px-2 py-1">
          <option value="1">Aktif</option>
          <option value="0">Nonaktif</option>
        </select>
      </div>
      <div class="flex justify-end mt-4">
        <button type="submit" class="px-4 py-1 rounded bg-blue-600 text-white font-bold shadow hover:bg-blue-700"><i class="fa fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function loadMapel() {
  $.post('api/mapel_proses.php', {action:'list'}, function(res) {
    if(res.success) {
      let html = '';
      res.data.forEach(function(row, i) {
        html += `<tr class="hover:bg-blue-50 border-b border-blue-100 transition-all">
          <td class='py-2 px-3 border-blue-100 text-center'>${i+1}</td>
          <td class='py-2 px-3 border-blue-100'>${row.kode}</td>
          <td class='py-2 px-3 border-blue-100'>${row.nama}</td>
          <td class='py-2 px-3 border-blue-100'>${row.keterangan||''}</td>
          <td class="text-center py-2 px-3 border-blue-100">
            <input type="checkbox" class="cb-status" data-id="${row.id}" ${row.status==1?'checked':''} />
          </td>
          <td class="text-center py-2 px-3 border-blue-100">
            <button class='btn-edit text-blue-600 hover:text-blue-900 mr-2' data-id='${row.id}'><i class='fa fa-pen'></i></button>
            <button class='btn-hapus text-red-600 hover:text-red-900' data-id='${row.id}'><i class='fa fa-trash'></i></button>
          </td>
        </tr>`;
      });
      $('#tabel-mapel tbody').html(html);
    }
  },'json');
}

$(document).ready(function(){
  loadMapel();

  $('#btn-tambah').click(function(){
    $('#form-mapel')[0].reset();
    $('#mapel-id').val('');
    $('#modal-title').text('Tambah Mapel');
    $('#modal-mapel').removeClass('hidden');
  });
  $('#close-modal-mapel').click(function(){
    $('#modal-mapel').addClass('hidden');
  });

  // Simpan mapel (tambah/edit)
  $('#form-mapel').submit(function(e){
    e.preventDefault();
    let data = $(this).serializeArray();
    let id = $('#mapel-id').val();
    data.push({name:'action', value: id ? 'edit' : 'add'});
    $.post('api/mapel_proses.php', data, function(res){
      if(res.success){
        Swal.fire('Sukses', 'Data mapel berhasil disimpan', 'success');
        $('#modal-mapel').addClass('hidden');
        loadMapel();
      }else{
        Swal.fire('Gagal', res.msg||'Gagal menyimpan data', 'error');
      }
    },'json');
  });

  // Edit mapel
  $('#tabel-mapel').on('click','.btn-edit',function(){
    let id = $(this).data('id');
    $.post('api/mapel_proses.php', {action:'list'}, function(res){
      if(res.success){
        let row = res.data.find(r=>r.id==id);
        if(row){
          $('#mapel-id').val(row.id);
          $('#mapel-kode').val(row.kode);
          $('#mapel-nama').val(row.nama);
          $('#mapel-keterangan').val(row.keterangan);
          $('#mapel-status').val(row.status);
          $('#modal-title').text('Edit Mapel');
          $('#modal-mapel').removeClass('hidden');
        }
      }
    },'json');
  });

  // Hapus mapel
  $('#tabel-mapel').on('click','.btn-hapus',function(){
    let id = $(this).data('id');
    Swal.fire({
      title:'Hapus Mapel?',
      text:'Data yang dihapus tidak bisa dikembalikan!',
      icon:'warning',
      showCancelButton:true,
      confirmButtonText:'Hapus',
      cancelButtonText:'Batal'
    }).then((result)=>{
      if(result.isConfirmed){
        $.post('api/mapel_proses.php', {action:'delete', id:id}, function(res){
          if(res.success){
            Swal.fire('Terhapus','Data mapel berhasil dihapus','success');
            loadMapel();
          }else{
            Swal.fire('Gagal',res.msg||'Gagal menghapus data','error');
          }
        },'json');
      }
    });
  });

  // Toggle status
  $('#tabel-mapel').on('change','.cb-status',function(){
    let id = $(this).data('id');
    let status = $(this).is(':checked') ? 1 : 0;
    $.post('api/mapel_proses.php', {action:'update_status', id:id, status:status}, function(res){
      if(res.success){
        Swal.fire('Sukses', 'Status mapel berhasil diubah', 'success');
        loadMapel();
      }else{
        Swal.fire('Gagal', res.msg||'Gagal mengubah status', 'error');
        loadMapel();
      }
    },'json');
  });
});
</script>
<?php include 'footer.php'; ?> 