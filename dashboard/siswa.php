<?php include "header.php"; ?>

<div class="flex items-center justify-between mb-8">
  <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 flex items-center gap-3">
    <i class="fas fa-users text-blue-600"></i> Data Siswa
  </h1>
  <div class="flex gap-2">
    <button id="btnPrint" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-bold shadow flex items-center gap-2">
      <i class="fa fa-print"></i> Print
    </button>
    <button id="btnTambah" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold shadow flex items-center gap-2">
      <i class="fa fa-plus"></i> Tambah Siswa
    </button>
  </div>
</div>
<div class="bg-white rounded-2xl shadow-xl p-4 overflow-x-auto">
  <table class="min-w-full text-sm text-gray-700" id="tabelSiswa">
          <thead>
            <tr class="bg-blue-100 text-blue-800">
              <th class="py-2 px-3">#</th>
              <th class="py-2 px-3">Nama</th>
              <th class="py-2 px-3">Gender</th>
              <th class="py-2 px-3">Tgl Lahir</th>
              <th class="py-2 px-3">Ortu</th>
              <th class="py-2 px-3">HP Ortu</th>
              <th class="py-2 px-3">Alamat</th>
              <th class="py-2 px-3">Foto</th>
              <th class="py-2 px-3">Aksi</th>
            </tr>
          </thead>
          <tbody id="tbodySiswa">
            <tr><td colspan="9" class="text-center py-6 text-gray-400">Memuat data...</td></tr>
          </tbody>
        </table>
      </div>
<?php include 'footer.php'; ?>
  </div>

  <!-- Modal Tambah/Edit Siswa -->
  <div id="modalSiswa" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-lg relative">
      <button id="closeModal" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-2xl"><i class="fa fa-xmark"></i></button>
      <h2 id="modalTitle" class="text-xl font-bold text-blue-700 mb-6">Tambah Siswa</h2>
      <form id="formSiswa" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="id" id="inputId">
        <div class="flex gap-4">
          <div class="flex-1">
            <label class="block text-sm font-bold mb-1">Nama</label>
            <input type="text" name="nama" id="inputNama" required class="w-full border rounded px-3 py-2">
          </div>
          <div class="flex-1">
            <label class="block text-sm font-bold mb-1">Gender</label>
            <select name="gender" id="inputGender" required class="w-full border rounded px-3 py-2">
              <option value="">Pilih</option>
              <option value="Laki-laki">Laki-laki</option>
              <option value="Perempuan">Perempuan</option>
            </select>
          </div>
        </div>
        <div class="flex gap-4">
          <div class="flex-1">
            <label class="block text-sm font-bold mb-1">Tgl Lahir</label>
            <input type="date" name="tgl_lahir" id="inputTglLahir" required class="w-full border rounded px-3 py-2">
          </div>
          <div class="flex-1">
            <label class="block text-sm font-bold mb-1">Ortu</label>
            <input type="text" name="ortu" id="inputOrtu" required class="w-full border rounded px-3 py-2">
          </div>
        </div>
        <div class="flex gap-4">
          <div class="flex-1">
            <label class="block text-sm font-bold mb-1">HP Ortu</label>
            <input type="text" name="hp_ortu" id="inputHpOrtu" required class="w-full border rounded px-3 py-2">
          </div>
          <div class="flex-1">
            <label class="block text-sm font-bold mb-1">Email</label>
            <input type="email" name="email" id="inputEmail" required class="w-full border rounded px-3 py-2">
          </div>
        </div>
        <div>
          <label class="block text-sm font-bold mb-1">Alamat</label>
          <input type="text" name="alamat" id="inputAlamat" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
          <label class="block text-sm font-bold mb-1">Foto</label>
          <div class="flex flex-col items-center gap-2">
            <button type="button" id="btnFotoArea" class="w-24 h-24 rounded-full border-2 border-dashed border-blue-300 flex items-center justify-center bg-blue-50 overflow-hidden mb-2 focus:outline-none focus:ring-2 focus:ring-blue-400 relative group">
              <img id="fotoPreview" src="../assets/img/profile/default.png" alt="Preview Foto" class="object-cover w-full h-full absolute top-0 left-0 hidden z-0" />
              <span id="iconUpload" class="flex items-center justify-center w-full h-full z-10">
                <i class="fa fa-plus text-blue-500 text-4xl group-hover:scale-110 transition-transform"></i>
              </span>
            </button>
            <input type="file" name="foto" id="inputFoto" accept="image/*" class="hidden" />
            <span class="text-xs text-gray-400">Klik lingkaran untuk pilih foto (JPG, PNG, maks 1MB)</span>
          </div>
        </div>
        <div class="flex justify-end gap-3 mt-4">
          <button type="button" id="batalModal" class="px-5 py-2 rounded bg-gray-200 text-gray-700 font-bold hover:bg-gray-300">Batal</button>
          <button type="submit" class="px-5 py-2 rounded bg-blue-600 text-white font-bold hover:bg-blue-700">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Modal logic
    const modal = document.getElementById('modalSiswa');
    const btnTambah = document.getElementById('btnTambah');
    const closeModal = document.getElementById('closeModal');
    const batalModal = document.getElementById('batalModal');
    btnTambah.onclick = () => { openModal(); };
    closeModal.onclick = batalModal.onclick = () => { modal.classList.add('hidden'); };

    // Foto preview logic
    const inputFoto = document.getElementById('inputFoto');
    const fotoPreview = document.getElementById('fotoPreview');
    const iconUpload = document.getElementById('iconUpload');
    const btnFotoArea = document.getElementById('btnFotoArea');
    btnFotoArea.addEventListener('click', function() {
      inputFoto.click();
    });
    inputFoto.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
          fotoPreview.src = ev.target.result;
          fotoPreview.classList.remove('hidden');
          iconUpload.classList.add('hidden');
        }
        reader.readAsDataURL(file);
      } else {
        fotoPreview.src = '../assets/img/profile/default.png';
        fotoPreview.classList.add('hidden');
        iconUpload.classList.remove('hidden');
      }
    });

    // Saat modal dibuka, reset preview jika tambah, atau tampilkan foto lama jika edit
    function openModal(edit = false, data = {}) {
      document.getElementById('modalTitle').textContent = edit ? 'Edit Siswa' : 'Tambah Siswa';
      document.getElementById('formSiswa').reset();
      document.getElementById('inputId').value = data.id || '';
      document.getElementById('inputNama').value = data.nama || '';
      document.getElementById('inputGender').value = data.gender || '';
      document.getElementById('inputTglLahir').value = data.tgl_lahir || '';
      document.getElementById('inputOrtu').value = data.ortu || '';
      document.getElementById('inputHpOrtu').value = data.hp_ortu || '';
      document.getElementById('inputEmail').value = data.email || '';
      document.getElementById('inputAlamat').value = data.alamat || '';
      if(edit && data.foto) {
        fotoPreview.src = '../assets/img/profile/' + data.foto;
        fotoPreview.classList.remove('hidden');
        iconUpload.classList.add('hidden');
      } else {
        fotoPreview.src = '../assets/img/profile/default.png';
        fotoPreview.classList.add('hidden');
        iconUpload.classList.remove('hidden');
      }
      inputFoto.value = '';
      modal.classList.remove('hidden');
    }

    // Load data siswa
    function loadSiswa() {
      fetch('api/siswa_proses.php?action=list')
        .then(res => res.json())
        .then(data => {
          const tbody = document.getElementById('tbodySiswa');
          if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center py-6 text-gray-400">Belum ada data siswa.</td></tr>';
            return;
          }
          tbody.innerHTML = data.map((s, i) => `
            <tr class="border-b hover:bg-blue-50 group">
              <td class="py-2 px-3 text-center">${i+1}</td>
              <td class="py-2 px-3"><a href="detail_siswa.php?id=${s.id}" class="text-blue-700 font-bold hover:underline hover:text-blue-900 transition cursor-pointer">${s.nama}</a></td>
              <td class="py-2 px-3">${s.gender||'-'}</td>
              <td class="py-2 px-3">${s.tgl_lahir||'-'}</td>
              <td class="py-2 px-3">${s.ortu||'-'}</td>
              <td class="py-2 px-3">${s.hp_ortu||'-'}</td>
              <td class="py-2 px-3">${s.alamat||'-'}</td>
              <td class="py-2 px-3 text-center">${s.foto ? `<img src="../assets/img/profile/${s.foto}" class="w-10 h-10 rounded-full object-cover mx-auto">` : '-'}</td>
              <td class="py-2 px-3 flex gap-2 justify-center">
                <button class="px-3 py-1 rounded bg-yellow-400 text-white hover:bg-yellow-500 text-xs font-bold" onclick='editSiswa(${JSON.stringify(s)})'><i class="fa fa-edit"></i></button>
                <button class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600 text-xs font-bold" onclick='hapusSiswa(${s.id})'><i class="fa fa-trash"></i></button>
              </td>
            </tr>
          `).join('');
        });
    }
    loadSiswa();

    // Edit siswa
    window.editSiswa = function(s) {
      openModal(true, s);
    }

    // Hapus siswa
    window.hapusSiswa = function(id) {
      Swal.fire({
        title: 'Hapus Data?',
        text: 'Yakin ingin menghapus data siswa ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
      }).then((result) => {
        if(result.isConfirmed) {
          fetch('api/siswa_proses.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=delete&id='+encodeURIComponent(id)
          })
          .then(res => res.json())
          .then(data => {
            if(data.status==='ok') {
              Swal.fire('Berhasil','Data siswa dihapus!','success');
              loadSiswa();
            } else {
              Swal.fire('Gagal', data.msg || 'Gagal menghapus data','error');
            }
          });
        }
      });
    }

    // Simpan siswa (tambah/edit)
    document.getElementById('formSiswa').onsubmit = function(e) {
      e.preventDefault();
      const form = e.target;
      const formData = new FormData(form);
      formData.append('action', form.inputId.value ? 'edit' : 'add');
      fetch('api/siswa_proses.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if(data.status==='ok') {
          Swal.fire('Berhasil','Data siswa disimpan!','success');
          modal.classList.add('hidden');
          loadSiswa();
        } else {
          Swal.fire('Gagal', data.msg || 'Gagal menyimpan data','error');
        }
      });
    }

    document.getElementById('btnPrint').onclick = function() {
      window.open('print/print_siswa.php', '_blank');
    }
  </script>
