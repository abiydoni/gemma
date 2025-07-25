<?php
include 'header.php';
?>
<div class="flex items-center justify-between mb-8">
  <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 flex items-center gap-3">
    <i class="fa fa-clipboard-list text-blue-600"></i> Input & Daftar Catatan Tentor
  </h1>
</div>
<div class="bg-white rounded-2xl shadow-xl p-8 mb-8 max-w-full mx-auto">
  <?php
  $id_tentor = $_SESSION['user_id'];
  $user_role = $_SESSION['user_role'] ?? '';
  if ($user_role === 'admin' || $user_role === 's_admin') {
    $sql = "SELECT t.id, s.nama AS nama_siswa, m.nama AS nama_mapel FROM tb_trx t JOIN tb_siswa s ON t.email = s.email JOIN tb_mapel m ON t.mapel = m.kode ORDER BY s.nama, m.nama";
    $stmt = $pdo->query($sql);
  } else {
    $sql = "SELECT t.id, s.nama AS nama_siswa, m.nama AS nama_mapel FROM tb_trx t JOIN tb_siswa s ON t.email = s.email JOIN tb_mapel m ON t.mapel = m.kode WHERE t.id_tentor = ? ORDER BY s.nama, m.nama";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_tentor]);
  }
  $trx = $stmt->fetchAll();
  ?>
  <form id="form-catatan" class="space-y-6 mb-8">
    <div>
      <label class="block text-sm font-bold mb-1" for="id_trx">Pilih Siswa & Mapel</label>
      <select name="id_trx" id="id_trx" class="w-full border rounded px-3 py-2" required>
        <option value="">-- Pilih --</option>
        <?php foreach($trx as $row): ?>
          <option value="<?= $row['id'] ?>">
            <?= htmlspecialchars($row['nama_siswa']) ?> - <?= htmlspecialchars($row['nama_mapel']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label class="block text-sm font-bold mb-1" for="tanggal">Tanggal</label>
        <input type="date" name="tanggal" id="tanggal" class="w-full border rounded px-3 py-2" required value="<?= date('Y-m-d') ?>">
      </div>
      <div class="md:col-span-3">
        <label class="block text-sm font-bold mb-1" for="catatan">Catatan Tentor</label>
        <input type="text" name="catatan" id="catatan" class="w-full border rounded px-3 py-2" required>
      </div>
    </div>
    <div class="flex justify-end">
      <button type="submit" class="px-6 py-2 rounded bg-blue-600 text-white font-bold shadow hover:bg-blue-700 flex items-center gap-2"><i class="fa fa-save"></i> Simpan Catatan</button>
    </div>
    <input type="hidden" name="id" id="catatan-id">
    <input type="hidden" name="action" id="form-action" value="add">
  </form>
  <div id="tabel-catatan-area" class="overflow-x-auto">
    <table id="tabel-catatan" class="min-w-full text-xs md:text-sm border border-blue-300 rounded-xl shadow overflow-hidden" id="tabel-keuangan">
      <thead>
        <tr class="bg-blue-100 text-blue-800">
          <th class="py-2 px-3">No</th>
          <th class="py-2 px-3">Tanggal</th>
          <th class="py-2 px-3">Catatan</th>
          <th class="py-2 px-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="4" class="text-center py-6 text-gray-400">Pilih siswa & mapel untuk melihat data catatan...</td></tr>
      </tbody>
    </table>
  </div>
</div>
<script>
function loadCatatan() {
  let id_trx = $('#id_trx').val();
  if(!id_trx) {
    $('#tabel-catatan tbody').html('<tr><td colspan="4" class="text-center py-6 text-gray-400">Pilih siswa & mapel untuk melihat data catatan...</td></tr>');
    return;
  }
  $.post('api/catatan_tentor_proses.php', {action:'list', id_trx:id_trx}, function(res){
    if(res.success) {
      let html = '';
      if(res.data.length === 0) {
        html = '<tr><td colspan="4" class="text-center py-6 text-gray-400">Belum ada data catatan.</td></tr>';
      } else {
        res.data.forEach(function(row, i){
          html += `<tr>
            <td class='py-2 px-3 text-center'>${i+1}</td>
            <td class='py-2 px-3'>${row.tanggal}</td>
            <td class='py-2 px-3'>${row.catatan}</td>
            <td class='py-2 px-3 text-center'>
              <button class='btn-edit-catatan text-blue-600 hover:text-blue-900 mr-2' data-id='${row.id}' data-tanggal='${row.tanggal}' data-catatan='${row.catatan}'><i class='fa fa-pen'></i></button>
              <button class='btn-hapus-catatan text-red-600 hover:text-red-900' data-id='${row.id}'><i class='fa fa-trash'></i></button>
            </td>
          </tr>`;
        });
      }
      $('#tabel-catatan tbody').html(html);
    }
  },'json');
}
$('#id_trx').change(function(){
  // Simpan value dropdown sebelum reset
  var val = $(this).val();
  // Reset hanya field input lain, bukan dropdown
  $('#form-catatan input[type=\"date\"], #form-catatan input[type=\"text\"], #form-catatan textarea').val('');
  $('#form-action').val('add');
  $('#catatan-id').val('');
  $(this).val(val); // Kembalikan value dropdown
  loadCatatan();
});
$('#form-catatan').submit(function(e){
  e.preventDefault();
  let data = $(this).serializeArray();
  let action = $('#form-action').val();
  data.push({name:'action', value:action});
  $.post('api/catatan_tentor_proses.php', data, function(res){
    if(res.success){
      Swal.fire('Sukses', 'Catatan berhasil disimpan', 'success');
      $('#form-catatan')[0].reset();
      $('#form-action').val('add');
      $('#catatan-id').val('');
      loadCatatan();
    }else{
      Swal.fire('Gagal', res.msg||'Gagal menyimpan data', 'error');
    }
  },'json');
});
$('#tabel-catatan').on('click','.btn-edit-catatan',function(){
  $('#form-action').val('edit');
  $('#catatan-id').val($(this).data('id'));
  $('#tanggal').val($(this).data('tanggal'));
  $('#catatan').val($(this).data('catatan'));
});
$('#tabel-catatan').on('click','.btn-hapus-catatan',function(){
  let id = $(this).data('id');
  Swal.fire({
    title:'Hapus Catatan?',
    text:'Data yang dihapus tidak bisa dikembalikan!',
    icon:'warning',
    showCancelButton:true,
    confirmButtonText:'Hapus',
    cancelButtonText:'Batal'
  }).then((result)=>{
    if(result.isConfirmed){
      $.post('api/catatan_tentor_proses.php', {action:'delete', id:id}, function(res){
        if(res.success){
          Swal.fire('Terhapus','Catatan berhasil dihapus','success');
          loadCatatan();
        }else{
          Swal.fire('Gagal',res.msg||'Gagal menghapus data','error');
        }
      },'json');
    }
  });
});
</script>
<?php include 'footer.php'; ?> 