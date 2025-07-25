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
  // Ambil data siswa
  $siswa = $pdo->query("SELECT DISTINCT s.id, s.nama FROM tb_siswa s JOIN tb_trx t ON t.email = s.email")->fetchAll(PDO::FETCH_ASSOC);
  ?>
  <form id="form-catatan" class="space-y-6 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-bold mb-1" for="id_siswa">Pilih Siswa</label>
        <select name="id_siswa" id="id_siswa" class="w-full border rounded px-3 py-2" required>
          <option value="">-- Pilih Siswa --</option>
          <?php foreach($siswa as $row): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-sm font-bold mb-1" for="kode_mapel">Pilih Mapel</label>
        <select name="kode_mapel" id="kode_mapel" class="w-full border rounded px-3 py-2" required>
          <option value="">-- Pilih Mapel --</option>
          <!-- Opsi mapel akan diisi via JS -->
        </select>
      </div>
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
  <div class="flex justify-end mb-4">
    <button type="button" id="btn-print-catatan" class="px-4 py-2 rounded bg-green-600 text-white font-bold shadow hover:bg-green-700 flex items-center gap-2">
      <i class="fa fa-print"></i> Print Catatan
    </button>
  </div>
  <div id="tabel-catatan-area" class="overflow-x-auto">
    <table id="tabel-catatan" class="min-w-full text-xs md:text-sm border border-blue-300 rounded-xl shadow overflow-hidden" id="tabel-keuangan">
      <thead>
        <tr class="bg-blue-100 text-blue-800">
          <th class="py-2 px-3">No</th>
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
// Ambil mapel berdasarkan siswa yang dipilih
$('#id_siswa').change(function(){
  let id_siswa = $(this).val();
  $('#kode_mapel').html('<option value="">-- Pilih Mapel --</option>');
  if(!id_siswa) {
    // Kosongkan mapel jika siswa belum dipilih
    return;
  }
  $.post('api/get_mapel_by_siswa.php', {id_siswa: id_siswa}, function(res){
    if(res.success && res.data.length > 0){
      res.data.forEach(function(row){
        $('#kode_mapel').append(`<option value="${row.kode}">${row.nama}</option>`);
      });
    }
  }, 'json');
  // Reset input lain, kecuali dropdown
  $('#form-catatan input[type="date"], #form-catatan input[type="text"], #form-catatan textarea').val('');
  $('#form-action').val('add');
  $('#catatan-id').val('');
  $('#tabel-catatan tbody').html('<tr><td colspan="4" class="text-center py-6 text-gray-400">Pilih siswa & mapel untuk melihat data catatan...</td></tr>');
});

// Mapel berubah, load catatan
$('#kode_mapel').change(function(){
  // Reset input lain, kecuali dropdown
  $('#form-catatan input[type="date"], #form-catatan input[type="text"], #form-catatan textarea').val('');
  $('#form-action').val('add');
  $('#catatan-id').val('');
  loadCatatan();
});

function loadCatatan() {
  let id_siswa = $('#id_siswa').val();
  let kode_mapel = $('#kode_mapel').val();
  if(!id_siswa || !kode_mapel) {
    $('#tabel-catatan tbody').html('<tr><td colspan="4" class="text-center py-6 text-gray-400">Pilih siswa & mapel untuk melihat data catatan...</td></tr>');
    return;
  }
  $.post('api/catatan_tentor_proses.php', {
    action: 'list',
    id_siswa: id_siswa,
    kode_mapel: kode_mapel
  }, function(res){
    if(res.success) {
      let html = '';
      if(res.data.length === 0) {
        html = '<tr><td colspan="4" class="text-center py-6 text-gray-400">Belum ada data catatan.</td></tr>';
      } else {
        res.data.forEach(function(row, i){
          html += `<tr>
            <td class='py-2 px-3 text-center'>${i+1}</td>
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
$('#btn-print-catatan').click(function(){
  // Ambil nama siswa dan mapel dari dropdown
  let namaSiswa = $('#id_siswa option:selected').text();
  let namaMapel = $('#kode_mapel option:selected').text();
  let printTable = $('#tabel-catatan').clone();
  printTable.find('th:last-child, td:last-child').remove();
  let printWindow = window.open('', '', 'height=700,width=1000');
  printWindow.document.write(`
    <html>
      <head>
        <title>Daftar Catatan Tentor</title>
        <style>
          body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f7fa;
            color: #222;
            padding: 0;
            margin: 0;
          }
          .print-container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 40px 40px 40px;
          }
          .print-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 12px;
          }
          .print-header-icon {
            font-size: 32px;
            color: #2563eb;
          }
          .print-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2563eb;
            margin: 0;
          }
          .print-info {
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: #374151;
          }
          .print-info-mapel {
            font-size: 1.1rem;
            margin-bottom: 24px;
            color: #374151;
          }
          table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            background: #f9fafb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
          }
          th, td {
            padding: 12px 14px;
            border-bottom: 1px solid #e5e7eb;
          }
          th {
            background: #e0e7ff;
            color: #1e293b;
            font-weight: 600;
            font-size: 1rem;
            border-top: 1px solid #e5e7eb;
          }
          tr:last-child td {
            border-bottom: none;
          }
          @media print {
            body { background: #fff; }
            .print-container { box-shadow: none; margin: 0; padding: 0; border-radius: 0; }
          }
        </style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
      </head>
      <body>
        <div class="print-container">
          <div class="print-header">
            <span class="print-header-icon"><i class="fa-solid fa-clipboard-list"></i></span>
            <span class="print-title">Daftar Catatan Tentor</span>
          </div>
          <div class="print-info"><strong>Nama Siswa:</strong> ${namaSiswa}</div>
          <div class="print-info-mapel"><strong>Nama Mapel:</strong> ${namaMapel}</div>
          ${printTable.prop('outerHTML')}
        </div>
      </body>
    </html>
  `);
  printWindow.document.close();
  printWindow.focus();
  printWindow.print();
  printWindow.close();
});
</script>
<?php include 'footer.php'; ?>