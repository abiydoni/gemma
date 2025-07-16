<?php
include 'header.php';
?>
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 flex items-center gap-2">
    <i class="fa fa-box-open text-blue-600"></i> Data Paket
  </h1>
  <button id="btn-tambah" class="px-3 py-1 rounded bg-blue-600 text-white font-bold shadow hover:bg-blue-700"><i class="fa fa-plus"></i> Tambah Paket</button>
</div>
<div class="max-w-full mx-auto bg-white rounded-3xl shadow-2xl p-4 md:p-8 border border-blue-100 mt-6">
  <div class="overflow-x-auto">
    <table class="min-w-full text-xs md:text-sm border border-blue-300 rounded-xl shadow overflow-hidden" id="tabel-paket">
      <thead>
        <tr class="bg-blue-100 text-blue-800">
          <th class="py-3 px-3 border-b border-blue-200 rounded-tl-xl">No</th>
          <th class="py-3 px-3 border-b border-blue-200">Kode</th>
          <th class="py-3 px-3 border-b border-blue-200">Nama</th>
          <th class="py-3 px-3 border-b border-blue-200">Keterangan</th>
          <th class="py-3 px-3 border-b border-blue-200">Jenjang</th>
          <th class="py-3 px-3 border-b border-blue-200 text-right">Harga</th>
          <th class="py-3 px-3 border-b border-blue-200 text-center">Status</th>
          <th class="py-3 px-3 border-b border-blue-200 rounded-tr-xl text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>
<!-- Modal Paket -->
<div id="modal-paket" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md relative">
    <button id="close-modal-paket" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-xl"><i class="fa fa-xmark"></i></button>
    <h2 class="text-lg font-bold text-blue-700 mb-4" id="modal-title">Tambah Paket</h2>
    <form id="form-paket" autocomplete="off">
      <input type="hidden" name="id" id="paket-id">
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Kode</label>
        <input type="text" name="Kode" id="paket-kode" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Nama</label>
        <input type="text" name="nama" id="paket-nama" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Keterangan</label>
        <input type="text" name="keterangan" id="paket-keterangan" class="w-full border rounded px-2 py-1">
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Jenjang</label>
        <input type="text" name="jenjang" id="paket-jenjang" class="w-full border rounded px-2 py-1">
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Harga</label>
        <input type="number" name="harga" id="paket-harga" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Status</label>
        <select name="status" id="paket-status" class="w-full border rounded px-2 py-1">
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
function loadPaket() {
  $.post('api/paket_proses.php', {action:'list'}, function(res) {
    if(res.success) {
      let html = '';
      res.data.forEach(function(row, i) {
        html += `<tr class="hover:bg-blue-50 border-b border-blue-100 transition-all">
          <td class='py-2 px-3 border-blue-100 text-center'>${i+1}</td>
          <td class='font-mono py-2 px-3 border-blue-100'>${row.Kode}</td>
          <td class='py-2 px-3 border-blue-100'>${row.nama}</td>
          <td class='py-2 px-3 border-blue-100'>${row.keterangan||''}</td>
          <td class='py-2 px-3 border-blue-100'>${row.jenjang||''}</td>
          <td class='text-right py-2 px-3 border-blue-100'>${row.harga.toLocaleString('id-ID')}</td>
          <td class="text-center py-2 px-3 border-blue-100">
            <input type="checkbox" class="cb-status" data-id="${row.id}" ${row.status==1?'checked':''} />
          </td>
          <td class="text-center py-2 px-3 border-blue-100">
            <button class='btn-edit text-blue-600 hover:text-blue-900 mr-2' data-id='${row.id}'><i class='fa fa-pen'></i></button>
            <button class='btn-hapus text-red-600 hover:text-red-900' data-id='${row.id}'><i class='fa fa-trash'></i></button>
          </td>
        </tr>`;
      });
      $('#tabel-paket tbody').html(html);
    }
  },'json');
}

$(document).ready(function(){
  loadPaket();

  $('#btn-tambah').click(function(){
    $('#form-paket')[0].reset();
    $('#paket-id').val('');
    $('#modal-title').text('Tambah Paket');
    $('#modal-paket').removeClass('hidden');
  });
  $('#close-modal-paket').click(function(){
    $('#modal-paket').addClass('hidden');
  });

  // Simpan paket
  $('#form-paket').submit(function(e){
    e.preventDefault();
    let data = $(this).serializeArray();
    let id = $('#paket-id').val();
    data.push({name:'action', value: id ? 'edit' : 'add'});
    $.post('api/paket_proses.php', data, function(res){
      if(res.success){
        Swal.fire('Sukses', 'Data paket berhasil disimpan', 'success');
        $('#modal-paket').addClass('hidden');
        loadPaket();
      }else{
        Swal.fire('Gagal', res.msg||'Gagal menyimpan data', 'error');
      }
    },'json');
  });

  // Edit paket
  $('#tabel-paket').on('click','.btn-edit',function(){
    let id = $(this).data('id');
    $.post('api/paket_proses.php', {action:'list'}, function(res){
      if(res.success){
        let row = res.data.find(r=>r.id==id);
        if(row){
          $('#paket-id').val(row.id);
          $('#paket-kode').val(row.Kode);
          $('#paket-nama').val(row.nama);
          $('#paket-keterangan').val(row.keterangan);
          $('#paket-jenjang').val(row.jenjang);
          $('#paket-harga').val(row.harga);
          $('#paket-status').val(row.status);
          $('#modal-title').text('Edit Paket');
          $('#modal-paket').removeClass('hidden');
        }
      }
    },'json');
  });

  // Hapus paket
  $('#tabel-paket').on('click','.btn-hapus',function(){
    let id = $(this).data('id');
    Swal.fire({
      title:'Hapus Paket?',
      text:'Data yang dihapus tidak bisa dikembalikan!',
      icon:'warning',
      showCancelButton:true,
      confirmButtonText:'Hapus',
      cancelButtonText:'Batal'
    }).then((result)=>{
      if(result.isConfirmed){
        $.post('api/paket_proses.php', {action:'delete', id:id}, function(res){
          if(res.success){
            Swal.fire('Terhapus','Data paket berhasil dihapus','success');
            loadPaket();
          }else{
            Swal.fire('Gagal',res.msg||'Gagal menghapus data','error');
          }
        },'json');
      }
    });
  });

  // Toggle status paket
  $('#tabel-paket').on('change','.cb-status',function(){
    let id = $(this).data('id');
    let status = $(this).is(':checked') ? 1 : 0;
    $.post('api/paket_proses.php', {action:'edit', id:id, status:status}, function(res){
      if(res.success){
        Swal.fire('Sukses', 'Status paket berhasil diubah', 'success');
        loadPaket();
      }else{
        Swal.fire('Gagal', res.msg||'Gagal mengubah status', 'error');
        loadPaket();
      }
    },'json');
  });
});
</script>
<?php include 'footer.php'; ?> 