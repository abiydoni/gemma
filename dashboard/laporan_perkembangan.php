<?php include "header.php"; ?>
<h1 class="text-2xl font-bold text-blue-800 mb-6 flex items-center gap-3">
  <i class="fa-solid fa-file-lines"></i>
  Laporan Perkembangan Siswa
</h1>

<!-- Data Siswa -->
<div class="mb-8">
  <h2 class="text-lg font-semibold mb-2">Data Siswa</h2>
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white border rounded shadow">
      <thead>
        <tr class="bg-blue-100">
          <th class="py-2 px-4 border">No</th>
          <th class="py-2 px-4 border">Nama</th>
          <th class="py-2 px-4 border">Nomor HP</th>
          <th class="py-2 px-4 border">Alamat</th>
        </tr>
      </thead>
      <tbody>
        <?php
        include '../api/db.php';
        $no = 1;
        $siswa = $pdo->query("SELECT * FROM tb_siswa")->fetchAll(PDO::FETCH_ASSOC);
        foreach($siswa as $row): ?>
        <tr>
          <td class="py-2 px-4 border"><?= $no++; ?></td>
          <td class="py-2 px-4 border"><?= htmlspecialchars($row['nama']); ?></td>
          <td class="py-2 px-4 border"><?= htmlspecialchars($row['hp_ortu']); ?></td>
          <td class="py-2 px-4 border"><?= htmlspecialchars($row['alamat']); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Data Tentor -->
<div class="mb-8">
  <h2 class="text-lg font-semibold mb-2">Data Tentor</h2>
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white border rounded shadow">
      <thead>
        <tr class="bg-green-100">
          <th class="py-2 px-4 border">No</th>
          <th class="py-2 px-4 border">Nama</th>
          <th class="py-2 px-4 border">No HP</th>
        </tr>
      </thead>
      <?php
        // Ambil data user yang sedang login
        $user_id = $_SESSION['user_id'] ?? null;
        $no = 1;
        $tentor = [];
        if ($user_id) {
        $stmt = $pdo->prepare("SELECT * FROM tb_user WHERE id = ?");
        $stmt->execute([$user_id]);
        $tentor = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        ?>
      <tbody>
        <?php foreach($tentor as $row): ?>
        <tr>
          <td class="py-2 px-4 border"><?= $no++; ?></td>
          <td class="py-2 px-4 border"><?= htmlspecialchars($row['nama']); ?></td>
          <td class="py-2 px-4 border"><?= htmlspecialchars($row['hp']); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Tabel Nilai Mapel -->
<div class="mb-8">
  <h2 class="text-lg font-semibold mb-2">Tabel Nilai Mapel</h2>
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white border rounded shadow">
      <thead>
        <tr class="bg-yellow-100">
          <th class="py-2 px-4 border">No</th>
          <th class="py-2 px-4 border">Nama Siswa</th>
          <th class="py-2 px-4 border">Mapel</th>
          <th class="py-2 px-4 border">Nilai</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        // Ganti 'n.id_siswa' menjadi 'n.siswa_id' jika itu nama kolom yang benar
        $nilai = $pdo->query("SELECT n.*, s.nama as nama_siswa FROM tb_nilai_mapel n JOIN tb_siswa s ON n.siswa_id=s.id")->fetchAll(PDO::FETCH_ASSOC);
        foreach($nilai as $row): ?>
        <tr>
          <td class="py-2 px-4 border"><?= $no++; ?></td>
          <td class="py-2 px-4 border"><?= htmlspecialchars($row['nama_siswa']); ?></td>
          <td class="py-2 px-4 border"><?= htmlspecialchars($row['mapel']); ?></td>
          <td class="py-2 px-4 border"><?= htmlspecialchars($row['nilai']); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Tabel Catatan Tentor -->
<div class="mb-8">
  <h2 class="text-lg font-semibold mb-2">Catatan Tentor</h2>
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white border rounded shadow">
      <thead>
        <tr class="bg-red-100">
          <th class="py-2 px-4 border">No</th>
          <th class="py-2 px-4 border">Nama Siswa</th>
          <th class="py-2 px-4 border">Tentor</th>
          <th class="py-2 px-4 border">Catatan</th>
          <th class="py-2 px-4 border">Tanggal</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $catatan = $pdo->query("SELECT c.*, s.nama as nama_siswa, u.nama as nama_tentor FROM tb_catatan c JOIN tb_siswa s ON c.id_siswa=s.id JOIN tb_user u ON c.id_tentor=u.id")->fetchAll(PDO::FETCH_ASSOC);
        foreach($catatan as $row): ?>
        <tr>
          <td class="py-2 px-4 border"><?= $no++; ?></td>
          <td class="py-2 px-4 border"><?= htmlspecialchars($row['nama_siswa']); ?></td>
          <td class="py-2 px-4 border"><?= htmlspecialchars($row['nama_tentor']); ?></td>
          <td class="py-2 px-4 border"><?= htmlspecialchars($row['catatan']); ?></td>
          <td class="py-2 px-4 border"><?= htmlspecialchars($row['tanggal']); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include "footer.php"; ?>