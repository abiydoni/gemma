<?php
include 'header.php';
?>
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 flex items-center gap-2">
    <i class="fa fa-flag text-blue-600"></i> Setting Syarat & Kondisi
  </h1>
  <div class="flex gap-2">
    <button id="btnPrint" class="px-3 py-1 rounded bg-green-600 text-white font-bold shadow hover:bg-green-700"><i class="fa fa-print"></i> Print</button>
    <button id="btn-tambah" class="px-3 py-1 rounded bg-blue-600 text-white font-bold shadow hover:bg-blue-700"><i class="fa fa-plus"></i> Tambah Kondisi</button>
  </div>
</div>
<div class="max-w-full mx-auto bg-white rounded-3xl shadow-2xl p-4 md:p-8 border border-blue-100 mt-6">
  <div class="overflow-x-auto">
    <table class="min-w-full text-xs md:text-sm border border-blue-300 rounded-xl shadow overflow-hidden" id="tabel-kondisi">
      <thead>
        <tr class="bg-blue-100 text-blue-800">
          <th class="py-3 px-3 border-b border-blue-200 rounded-tl-xl">No</th>
          <th class="py-3 px-3 border-b border-blue-200">Kode</th>
          <th class="py-3 px-3 border-b border-blue-200">Nama</th>
          <th class="py-3 px-3 border-b border-blue-200">Tanggal</th>
          <th class="py-3 px-3 border-b border-blue-200 rounded-tr-xl text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>
<!-- Modal Kondisi -->
<div id="modal-kondisi" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md relative">
    <button id="close-modal-kondisi" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-xl"><i class="fa fa-xmark"></i></button>
    <h2 class="text-lg font-bold text-blue-700 mb-4" id="modal-title">Tambah Kondisi</h2>
    <form id="form-kondisi" autocomplete="off">
      <input type="hidden" name="id" id="kondisi-id">
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Kode</label>
        <input type="text" name="kode" id="kondisi-kode" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Nama</label>
        <input type="text" name="nama" id="kondisi-nama" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="mb-2" id="tanggal-group" style="display:none;">
        <label class="block text-sm font-bold mb-1">Tanggal</label>
        <input type="text" id="kondisi-tanggal" class="w-full border rounded px-2 py-1 bg-gray-100 text-gray-500" readonly>
      </div>
      <div class="flex justify-end mt-4">
        <button type="submit" class="px-4 py-1 rounded bg-blue-600 text-white font-bold shadow hover:bg-blue-700"><i class="fa fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function loadKondisi() {
  $.post('api/kondisi_proses.php', {action:'list'}, function(res) {
    if(res.success) {
      let html = '';
      res.data.forEach(function(row, i) {
        html += `<tr class=\"hover:bg-blue-50 border-b border-blue-100 transition-all\">`
          + `<td class='py-2 px-3 border-blue-100 text-center'>${i+1}</td>`
          + `<td class='py-2 px-3 border-blue-100'>${row.kode}</td>`
          + `<td class='py-2 px-3 border-blue-100'>${row.nama}</td>`
          + `<td class='py-2 px-3 border-blue-100'>${row.tanggal||''}</td>`
          + `<td class=\"text-center py-2 px-3 border-blue-100\">`
          + `<button class='btn-edit text-blue-600 hover:text-blue-900 mr-2' data-id='${row.id}'><i class='fa fa-pen'></i></button>`
          + `<button class='btn-hapus text-red-600 hover:text-red-900' data-id='${row.id}'><i class='fa fa-trash'></i></button>`
          + `</td>`
        + `</tr>`;
      });
      $('#tabel-kondisi tbody').html(html);
    }
  },'json');
}

$(document).ready(function(){
  loadKondisi();

  // Print button
  $('#btnPrint').click(function(){
    window.open('print/print_kondisi.php', '_blank');
  });

  $('#btn-tambah').click(function(){
    $('#form-kondisi')[0].reset();
    $('#kondisi-id').val('');
    $('#modal-title').text('Tambah Kondisi');
    $('#tanggal-group').hide();
    $('#modal-kondisi').removeClass('hidden');
  });
  $('#close-modal-kondisi').click(function(){
    $('#modal-kondisi').addClass('hidden');
  });

  // Simpan kondisi (tambah/edit)
  $('#form-kondisi').submit(function(e){
    e.preventDefault();
    let data = $(this).serializeArray();
    let id = $('#kondisi-id').val();
    data.push({name:'action', value: id ? 'edit' : 'add'});
    $.post('api/kondisi_proses.php', data, function(res){
      if(res.success){
        Swal.fire('Sukses', 'Data kondisi berhasil disimpan', 'success');
        $('#modal-kondisi').addClass('hidden');
        loadKondisi();
      }else{
        Swal.fire('Gagal', res.msg||'Gagal menyimpan data', 'error');
      }
    },'json');
  });

  // Edit kondisi
  $('#tabel-kondisi').on('click','.btn-edit',function(){
    let id = $(this).data('id');
    $.post('api/kondisi_proses.php', {action:'list'}, function(res){
      if(res.success){
        let row = res.data.find(r=>r.id==id);
        if(row){
          $('#kondisi-id').val(row.id);
          $('#kondisi-kode').val(row.kode);
          $('#kondisi-nama').val(row.nama);
          $('#kondisi-tanggal').val(row.tanggal);
          $('#tanggal-group').show();
          $('#modal-title').text('Edit Kondisi');
          $('#modal-kondisi').removeClass('hidden');
        }
      }
    },'json');
  });

  // Hapus kondisi
  $('#tabel-kondisi').on('click','.btn-hapus',function(){
    let id = $(this).data('id');
    Swal.fire({
      title:'Hapus Kondisi?',
      text:'Data yang dihapus tidak bisa dikembalikan!',
      icon:'warning',
      showCancelButton:true,
      confirmButtonText:'Hapus',
      cancelButtonText:'Batal'
    }).then((result)=>{
      if(result.isConfirmed){
        $.post('api/kondisi_proses.php', {action:'delete', id:id}, function(res){
          if(res.success){
            Swal.fire('Terhapus','Data kondisi berhasil dihapus','success');
            loadKondisi();
          }else{
            Swal.fire('Gagal',res.msg||'Gagal menghapus data','error');
          }
        },'json');
      }
    });
  });
});
</script>
<?php include 'footer.php'; ?> 