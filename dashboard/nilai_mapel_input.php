<?php
include 'header.php';
?>
<div class="flex items-center justify-between mb-8">
  <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 flex items-center gap-3">
    <i class="fa fa-table-list text-blue-600"></i> Input & Daftar Nilai Mapel
  </h1>
</div>
<div class="bg-white rounded-2xl shadow-xl p-8 mb-8 max-w-full mx-auto">
  <?php
  $msg = '';
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
  <form id="form-nilai" class="space-y-6 mb-8">
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
      <div>
        <label class="block text-sm font-bold mb-1" for="jenis">Jenis</label>
        <input type="text" name="jenis" id="jenis" class="w-full border rounded px-3 py-2" placeholder="Ulangan/Latihan/UTS/UAS" required>
      </div>
      <div>
        <label class="block text-sm font-bold mb-1" for="nilai">Nilai</label>
        <input type="number" name="nilai" id="nilai" class="w-full border rounded px-3 py-2" min="0" max="100" step="0.01" required>
      </div>
      <div>
        <label class="block text-sm font-bold mb-1" for="keterangan">Keterangan</label>
        <input type="text" name="keterangan" id="keterangan" class="w-full border rounded px-3 py-2" placeholder="Keterangan singkat">
      </div>
    </div>
    <div class="flex justify-end">
      <button type="submit" class="px-6 py-2 rounded bg-blue-600 text-white font-bold shadow hover:bg-blue-700 flex items-center gap-2"><i class="fa fa-save"></i> Simpan Nilai</button>
    </div>
    <input type="hidden" name="id" id="nilai-id">
    <input type="hidden" name="action" id="form-action" value="add">
  </form>
  <div class="flex justify-end mb-4">
    <button type="button" id="btn-print-nilai" class="px-4 py-2 rounded bg-green-600 text-white font-bold shadow hover:bg-green-700 flex items-center gap-2">
      <i class="fa fa-print"></i> Print Nilai
    </button>
  </div>
  <div id="tabel-nilai-area" class="overflow-x-auto">
    <table id="tabel-nilai" class="min-w-full text-xs md:text-sm border border-blue-300 rounded-xl shadow overflow-hidden" id="tabel-keuangan">
      <thead>
        <tr class="bg-blue-100 text-blue-800">
          <th class="py-2 px-3">No</th>
          <th class="py-2 px-3 text-left">Tanggal</th>
          <th class="py-2 px-3 text-left">Jenis</th>
          <th class="py-2 px-3 text-center">Nilai</th>
          <th class="py-2 px-3 text-left">Keterangan</th>
          <th class="py-2 px-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="6" class="text-center py-6 text-gray-400">Pilih siswa & mapel untuk melihat data nilai...</td></tr>
      </tbody>
    </table>
  </div>
</div>
<script>
let editing = false;
function loadNilai() {
  let id_trx = $('#id_trx').val();
  if(!id_trx) {
    $('#tabel-nilai tbody').html('<tr><td colspan="6" class="text-center py-6 text-gray-400">Pilih siswa & mapel untuk melihat data nilai...</td></tr>');
    return;
  }
  $.post('api/nilai_mapel_proses.php', {action:'list', id_trx:id_trx}, function(res){
    if(res.success) {
      let html = '';
      if(res.data.length === 0) {
        html = '<tr><td colspan="6" class="text-center py-6 text-gray-400">Belum ada data nilai.</td></tr>';
      } else {
        res.data.forEach(function(row, i){
          html += `<tr>
            <td class='py-2 px-3 text-center'>${i+1}</td>
            <td class='py-2 px-3'>${row.tanggal}</td>
            <td class='py-2 px-3'>${row.jenis||''}</td>
            <td class='py-2 px-3 text-center'>${row.nilai}</td>
            <td class='py-2 px-3'>${row.keterangan||''}</td>
            <td class='py-2 px-3 text-center'>
              <button class='btn-edit-nilai text-blue-600 hover:text-blue-900 mr-2' data-id='${row.id}' data-tanggal='${row.tanggal}' data-jenis='${row.jenis||''}' data-nilai='${row.nilai}' data-keterangan='${row.keterangan||''}'><i class='fa fa-pen'></i></button>
              <button class='btn-hapus-nilai text-red-600 hover:text-red-900' data-id='${row.id}'><i class='fa fa-trash'></i></button>
            </td>
          </tr>`;
        });
      }
      $('#tabel-nilai tbody').html(html);
    }
  },'json');
}
$('#id_trx').change(function(){
  $('#form-action').val('add');
  $('#nilai-id').val('');
  loadNilai();
});
$('#form-nilai').submit(function(e){
  e.preventDefault();
  let data = $(this).serializeArray();
  let action = $('#form-action').val();
  data.push({name:'action', value:action});
  $.post('api/nilai_mapel_proses.php', data, function(res){
    if(res.success){
      Swal.fire('Sukses', 'Data nilai berhasil disimpan', 'success');
      $('#form-nilai')[0].reset();
      $('#form-action').val('add');
      $('#nilai-id').val('');
      loadNilai();
    }else{
      Swal.fire('Gagal', res.msg||'Gagal menyimpan data', 'error');
    }
  },'json');
});
$('#tabel-nilai').on('click','.btn-edit-nilai',function(){
  $('#form-action').val('edit');
  $('#nilai-id').val($(this).data('id'));
  $('#tanggal').val($(this).data('tanggal'));
  $('#jenis').val($(this).data('jenis'));
  $('#nilai').val($(this).data('nilai'));
  $('#keterangan').val($(this).data('keterangan'));
});
$('#tabel-nilai').on('click','.btn-hapus-nilai',function(){
  let id = $(this).data('id');
  Swal.fire({
    title:'Hapus Nilai?',
    text:'Data yang dihapus tidak bisa dikembalikan!',
    icon:'warning',
    showCancelButton:true,
    confirmButtonText:'Hapus',
    cancelButtonText:'Batal'
  }).then((result)=>{
    if(result.isConfirmed){
      $.post('api/nilai_mapel_proses.php', {action:'delete', id:id}, function(res){
        if(res.success){
          Swal.fire('Terhapus','Data nilai berhasil dihapus','success');
          loadNilai();
        }else{
          Swal.fire('Gagal',res.msg||'Gagal menghapus data','error');
        }
      },'json');
    }
  });
});
$('#btn-print-nilai').click(function(){
  let selected = $('#id_trx option:selected').text();
  let printTable = $('#tabel-nilai').clone();
  printTable.find('th:last-child, td:last-child').remove();
  let printWindow = window.open('', '', 'height=700,width=1000');
  printWindow.document.write(`
    <html>
      <head>
        <title>Daftar Nilai Mapel</title>
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
            <span class="print-header-icon"><i class="fa-solid fa-file-lines"></i></span>
            <span class="print-title">Daftar Nilai Mapel</span>
          </div>
          <div class="print-info"><strong>Siswa & Mapel:</strong> ${selected}</div>
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