<?php
include 'header.php';
?>
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 flex items-center gap-2">
    <i class="fa fa-cash-register text-blue-600"></i> Data Keuangan
  </h1>
  <button id="btn-tambah" class="px-3 py-1 rounded bg-blue-600 text-white font-bold shadow hover:bg-blue-700"><i class="fa fa-plus"></i> Tambah Transaksi</button>
</div>
<div class="max-w-full mx-auto bg-white rounded-3xl shadow-2xl p-4 md:p-8 border border-blue-100 mt-6">
  <div class="mb-4 flex flex-wrap gap-4 justify-between items-center" id="rekap-keuangan">
    <div class="flex items-center gap-2">
      <label class="font-bold text-sm">Tanggal Awal</label>
      <input type="date" id="filter-awal" class="border rounded px-2 py-1" />
      <label class="font-bold text-sm">Tanggal Akhir</label>
      <input type="date" id="filter-akhir" class="border rounded px-2 py-1" />
      <button id="btn-filter" class="ml-2 px-2 py-1 rounded bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold">Filter</button>
      <button id="btn-reset-filter" class="ml-2 px-2 py-1 rounded bg-gray-200 hover:bg-gray-300 text-xs">Reset</button>
    </div>
  </div>
  <div class="mb-4 flex flex-wrap gap-4 justify-between items-center" id="rekap-total">
    <div class="font-bold text-green-700">Total Debet: <span id="total-debet">0,00</span></div>
    <div class="font-bold text-red-700">Total Kredit: <span id="total-kredit">0,00</span></div>
    <div class="font-bold text-blue-700">Saldo Akhir: <span id="saldo-akhir">0,00</span></div>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full text-xs md:text-sm border border-blue-300 rounded-xl shadow overflow-hidden" id="tabel-keuangan">
      <thead>
        <tr class="bg-blue-100 text-blue-800">
          <th class="py-3 px-3 border-b border-blue-200 rounded-tl-xl">No</th>
          <th class="py-3 px-3 border-b border-blue-200">Tanggal</th>
          <th class="py-3 px-3 border-b border-blue-200">Keterangan</th>
          <th class="py-3 px-3 border-b border-blue-200 text-right">Debet</th>
          <th class="py-3 px-3 border-b border-blue-200 text-right">Kredit</th>
          <th class="py-3 px-3 border-b border-blue-200 text-right">Saldo</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>
<!-- Modal Keuangan -->
<div id="modal-keuangan" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md relative">
    <button id="close-modal-keuangan" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-xl"><i class="fa fa-xmark"></i></button>
    <h2 class="text-lg font-bold text-blue-700 mb-4" id="modal-title">Tambah Transaksi</h2>
    <form id="form-keuangan" autocomplete="off">
      <input type="hidden" name="id" id="keuangan-id">
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Tanggal</label>
        <input type="date" name="tanggal" id="keuangan-tanggal" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Keterangan</label>
        <input type="text" name="keterangan" id="keuangan-keterangan" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Debet (Masuk)</label>
        <input type="number" name="debet" id="keuangan-debet" class="w-full border rounded px-2 py-1" min="0" step="1">
      </div>
      <div class="mb-2">
        <label class="block text-sm font-bold mb-1">Kredit (Keluar)</label>
        <input type="number" name="kredit" id="keuangan-kredit" class="w-full border rounded px-2 py-1" min="0" step="1">
      </div>
      <div class="flex justify-end mt-4">
        <button type="submit" class="px-4 py-1 rounded bg-blue-600 text-white font-bold shadow hover:bg-blue-700"><i class="fa fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function formatRupiah(angka) {
  return 'Rp ' + (angka||0).toLocaleString('id-ID');
}
function formatAngka(angka) {
  angka = parseFloat(angka) || 0;
  return angka.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}
function loadKeuangan() {
  let tgl_awal = $('#filter-awal').val();
  let tgl_akhir = $('#filter-akhir').val();
  $.post('api/keuangan_proses.php', {action:'list', tgl_awal, tgl_akhir}, function(res) {
    if(res.success) {
      let html = '';
      let totalDebet = 0, totalKredit = 0, saldoJalan = 0;
      res.data.forEach(function(row, i) {
        totalDebet += parseFloat(row.debet||0);
        totalKredit += parseFloat(row.kredit||0);
        saldoJalan += parseFloat(row.debet||0) - parseFloat(row.kredit||0);
        let saldoClass = saldoJalan < 0 ? 'text-red-600 font-bold' : 'text-blue-700 font-bold';
        let autoClass = row.keterangan && row.keterangan.includes('[AUTO]') ? 'bg-yellow-50' : '';
        html += `<tr class=\"hover:bg-blue-50 border-b border-blue-100 transition-all ${autoClass}\">`
          + `<td class='py-2 px-3 border-blue-100 text-center'>${i+1}</td>`
          + `<td class='py-2 px-3 border-blue-100'>${row.tanggal}</td>`
          + `<td class='py-2 px-3 border-blue-100'>`
          + (row.keterangan && row.keterangan.includes('[AUTO]') ? `<span class='inline-block px-1.5 py-0 rounded-full bg-green-50 text-green-700 border border-green-300 text-[10px] font-bold mr-1 align-middle'>AUTO</span>` : '')
          + `${(row.keterangan||'').replace('[AUTO]', '').trim()}`
          + `</td>`
          + `<td class='py-2 px-3 border-blue-100 text-right'>${row.debet>0?formatAngka(row.debet):''}</td>`
          + `<td class='py-2 px-3 border-blue-100 text-right'>${row.kredit>0?formatAngka(row.kredit):''}</td>`
          + `<td class='py-2 px-3 border-blue-100 text-right ${saldoClass}'>${formatAngka(saldoJalan)}</td>`
        + `</tr>`;
      });
      $('#tabel-keuangan tbody').html(html);
      $('#total-debet').text(formatAngka(totalDebet));
      $('#total-kredit').text(formatAngka(totalKredit));
      $('#saldo-akhir').text(formatAngka(saldoJalan));
    }
  },'json');
}

$(document).ready(function(){
  // Set default tanggal awal & akhir ke bulan ini
  function setDefaultTanggal() {
    let today = new Date();
    let year = today.getFullYear();
    let month = today.getMonth() + 1; // 1-12
    if (month < 10) month = '0' + month;
    let firstDay = `${year}-${month}-01`;
    let lastDayDate = new Date(year, today.getMonth() + 1, 0);
    let lastDay = `${year}-${month}-${lastDayDate.getDate().toString().padStart(2, '0')}`;
    $('#filter-awal').val(firstDay);
    $('#filter-akhir').val(lastDay);
  }
  setDefaultTanggal();
  loadKeuangan();
  // Event tombol Filter
  $('#btn-filter').on('click', function(){
    loadKeuangan();
  });
  // Reset ke default dan langsung load
  $('#btn-reset-filter').on('click', function(){
    setDefaultTanggal();
    loadKeuangan();
  });

  $('#btn-tambah').click(function(){
    $('#form-keuangan')[0].reset();
    $('#keuangan-id').val('');
    // Set tanggal otomatis ke hari ini
    let today = new Date();
    let tgl = today.toISOString().slice(0,10);
    $('#keuangan-tanggal').val(tgl);
    $('#modal-title').text('Tambah Transaksi');
    $('#modal-keuangan').removeClass('hidden');
  });
  $('#close-modal-keuangan').click(function(){
    $('#modal-keuangan').addClass('hidden');
  });

  // Simpan keuangan (tambah/edit)
  $('#form-keuangan').submit(function(e){
    e.preventDefault();
    let tanggal = $('#keuangan-tanggal').val();
    let keterangan = $('#keuangan-keterangan').val();
    let debet = parseInt($('#keuangan-debet').val()) || 0;
    let kredit = parseInt($('#keuangan-kredit').val()) || 0;
    if (!tanggal || !keterangan) {
      Swal.fire('Error', 'Tanggal dan keterangan wajib diisi!', 'error');
      console.log('Tanggal/keterangan kosong');
      return;
    }
    if ((debet > 0 && kredit > 0) || (debet === 0 && kredit === 0)) {
      Swal.fire('Error', 'Isi salah satu: Debet (masuk) atau Kredit (keluar), tidak boleh dua-duanya atau kosong!', 'error');
      console.log('Validasi debet/kredit gagal', debet, kredit);
      return;
    }
    let data = $(this).serializeArray();
    let id = $('#keuangan-id').val();
    data.push({name:'action', value: id ? 'edit' : 'add'});
    $.post('api/keuangan_proses.php', data, function(res){
      if(res.success){
        Swal.fire('Sukses', 'Data keuangan berhasil disimpan', 'success');
        $('#modal-keuangan').addClass('hidden');
        loadKeuangan();
      }else{
        Swal.fire('Gagal', res.msg||'Gagal menyimpan data', 'error');
        console.log('API gagal', res);
      }
    },'json').fail(function(xhr, status, error){
      Swal.fire('Error', 'Gagal menghubungi server', 'error');
      console.log('AJAX error', error);
    });
  });

  // Hapus event handler edit dan hapus transaksi
});
</script>
<?php include 'footer.php'; ?> 