<?php
header('Content-Type: application/json');

function clean($str) {
    return htmlspecialchars(trim($str));
}

// Ambil data
$nama = clean($_POST['nama'] ?? '');
$gender = clean($_POST['gender'] ?? '');
$tgl_lahir = clean($_POST['tgl_lahir'] ?? '');
$ortu = clean($_POST['ortu'] ?? '');
$hp_ortu = clean($_POST['hp_ortu'] ?? '');
$alamat = clean($_POST['alamat'] ?? '');
$email = clean($_POST['email'] ?? '');
$jenjang = clean($_POST['jenjang'] ?? '');
$tipe = clean($_POST['tipe'] ?? '');
$mapel = clean($_POST['mapel'] ?? '');
$harga = clean($_POST['harga'] ?? '');
// $metode = clean($_POST['metode'] ?? ''); // dihapus

// Validasi sederhana
if(!$nama || !$gender || !$tgl_lahir || !$ortu || !$hp_ortu || !$alamat || !$jenjang || !$tipe || !$harga) {
    echo json_encode(['status'=>'fail','msg'=>'Data wajib diisi lengkap!']);
    exit;
}

// Nomor invoice unik
$inv = 'INV-' . date('Ymd') . '-' . rand(100,999);

// Format invoice HTML modern, elegan, 1 kolom horizontal label: value
$invoice = '
<div style="max-width:440px;margin:0 auto;background:#fff;border-radius:22px;box-shadow:0 8px 36px #2563eb22;padding:0 0 28px 0;border:1.5px solid #e0e7ef;font-family:inherit;overflow:hidden;">
  <div style="background:linear-gradient(90deg,#1e3a8a 60%,#2563eb 100%);padding:26px 32px 20px 32px;display:flex;align-items:center;gap:18px;">
    <img src="assets/img/logo4.png" alt="Logo" style="height:54px;width:54px;border-radius:14px;background:#fff;padding:4px;box-shadow:0 2px 8px #2563eb33;">
    <div style="flex:1;">
      <div style="font-size:1.32rem;font-weight:900;color:#fff;letter-spacing:0.5px;display:flex;align-items:center;gap:8px;">
        <i class="fa-solid fa-file-invoice-dollar" style="color:#facc15;"></i> Bimbel Gemma
      </div>
      <div style="font-size:1.01rem;color:#e0e7ef;">Invoice Pendaftaran Siswa</div>
    </div>
    <span style="background:#fff;color:#2563eb;font-weight:700;padding:5px 18px;border-radius:16px;font-size:1.01rem;box-shadow:0 2px 8px #2563eb22;display:flex;align-items:center;gap:7px;"><i class="fa-solid fa-circle-check text-green-500"></i>PAID</span>
  </div>
  <div style="padding:24px 32px 0 32px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
      <span style="color:#888;font-size:1.01rem;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-hashtag text-blue-400"></i>No. Invoice</span>
      <span style="color:#2563eb;font-weight:700;">$inv</span>
    </div>
    <div style="display:flex;flex-direction:column;gap:4px;margin-bottom:18px;">
      <div style="display:flex;align-items:center;gap:8px;"><span style="color:#888;font-size:1.01rem;min-width:120px;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-user text-blue-400"></i>Nama</span><span style="color:#222;font-size:1.09em;font-weight:600;">$nama</span></div>
      <div style="display:flex;align-items:center;gap:8px;"><span style="color:#888;font-size:1.01rem;min-width:120px;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-venus-mars text-pink-400"></i>Jenis Kelamin</span><span style="color:#222;font-size:1.09em;font-weight:600;">$gender</span></div>
      <div style="display:flex;align-items:center;gap:8px;"><span style="color:#888;font-size:1.01rem;min-width:120px;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-calendar-days text-blue-400"></i>Tgl Lahir</span><span style="color:#222;font-size:1.09em;font-weight:600;">$tgl_lahir</span></div>
      <div style="display:flex;align-items:center;gap:8px;"><span style="color:#888;font-size:1.01rem;min-width:120px;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-user-group text-blue-400"></i>Orang Tua/Wali</span><span style="color:#222;font-size:1.09em;font-weight:600;">$ortu</span></div>
      <div style="display:flex;align-items:center;gap:8px;"><span style="color:#888;font-size:1.01rem;min-width:120px;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-phone text-green-500"></i>No. HP/WA</span><span style="color:#222;font-size:1.09em;font-weight:600;">$hp_ortu</span></div>
      <div style="display:flex;align-items:center;gap:8px;"><span style="color:#888;font-size:1.01rem;min-width:120px;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-envelope text-blue-400"></i>Email</span><span style="color:#222;font-size:1.09em;font-weight:600;">$email</span></div>
      <div style="display:flex;align-items:center;gap:8px;"><span style="color:#888;font-size:1.01rem;min-width:120px;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-location-dot text-red-400"></i>Alamat</span><span style="color:#222;font-size:1.09em;font-weight:600;">$alamat</span></div>
      <div style="display:flex;align-items:center;gap:8px;"><span style="color:#888;font-size:1.01rem;min-width:120px;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-layer-group text-blue-400"></i>Paket</span><span style="color:#2563eb;font-size:1.09em;font-weight:600;">$jenjang - $tipe</span></div>
      <div style="display:flex;align-items:center;gap:8px;"><span style="color:#888;font-size:1.01rem;min-width:120px;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-book text-blue-400"></i>Mapel</span><span style="color:#2563eb;font-size:1.09em;font-weight:600;">$mapel</span></div>
    </div>
    <div style="margin:22px 0 12px 0;text-align:center;">
      <span style="display:inline-block;background:#e0f7e9;color:#16a34a;font-weight:900;font-size:1.32em;padding:10px 38px;border-radius:18px;box-shadow:0 2px 8px #16a34a22;letter-spacing:1px;display:flex;align-items:center;gap:12px;"><i class="fa-solid fa-money-bill-wave text-green-500"></i> Total: $harga</span>
    </div>
    <div style="margin-top:22px;font-size:1.01em;color:#888;text-align:center;display:flex;flex-direction:column;align-items:center;gap:8px;">
      <span><i class="fa-solid fa-circle-info text-blue-400"></i> Simpan invoice ini sebagai bukti pendaftaran.</span>
      <span><i class="fa-solid fa-user-shield text-blue-400"></i> Tunjukkan invoice ini ke admin saat pembayaran.</span>
      <span style="color:#2563eb;font-weight:700;"><i class="fa-solid fa-face-smile-beam text-yellow-400"></i> Terima kasih telah mendaftar di Bimbel Gemma!</span>
    </div>
  </div>
</div>';

// Balas JSON
$res = [
    'status' => 'ok',
    'invoice' => $invoice
];
echo json_encode($res); 