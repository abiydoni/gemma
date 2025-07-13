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
$metode = clean($_POST['metode'] ?? '');

// Validasi sederhana
if(!$nama || !$gender || !$tgl_lahir || !$ortu || !$hp_ortu || !$alamat || !$jenjang || !$tipe || !$harga || !$metode) {
    echo json_encode(['status'=>'fail','msg'=>'Data wajib diisi lengkap!']);
    exit;
}

// Nomor invoice unik
$inv = 'INV-' . date('Ymd') . '-' . rand(100,999);

// Jika transfer, cek upload
$bukti = '';
if($metode === 'Transfer' && isset($_FILES['bukti']) && $_FILES['bukti']['tmp_name']) {
    $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
    $fname = 'bukti_' . time() . '_' . rand(100,999) . '.' . $ext;
    $dir = '../uploads/';
    if(!is_dir($dir)) mkdir($dir,0777,true);
    move_uploaded_file($_FILES['bukti']['tmp_name'], $dir.$fname);
    $bukti = $fname;
}

// Format invoice HTML
$invoice = '<div style="text-align:left;max-width:420px;margin:0 auto;font-size:1rem;">'
    .'<h3 style="font-size:1.3rem;font-weight:bold;color:#2563eb;margin-bottom:8px;">INVOICE PENDAFTARAN</h3>'
    .'<div><b>No. Invoice:</b> '.$inv.'</div>'
    .'<div><b>Nama:</b> '.$nama.'</div>'
    .'<div><b>Jenis Kelamin:</b> '.$gender.'</div>'
    .'<div><b>Tgl Lahir:</b> '.$tgl_lahir.'</div>'
    .'<div><b>Orang Tua/Wali:</b> '.$ortu.'</div>'
    .'<div><b>No. HP/WA:</b> '.$hp_ortu.'</div>'
    .'<div><b>Email:</b> '.$email.'</div>'
    .'<div><b>Alamat:</b> '.$alamat.'</div>'
    .'<div><b>Paket:</b> '.$jenjang.' - '.$tipe.'</div>'
    .'<div><b>Mapel:</b> '.$mapel.'</div>'
    .'<div><b>Harga:</b> <span style="color:#16a34a;font-weight:bold;">'.$harga.'</span></div>'
    .'<div><b>Pembayaran:</b> '.$metode.'</div>'
    .($bukti ? '<div><b>Bukti Transfer:</b> <a href="../uploads/'.$bukti.'" target="_blank">Lihat</a></div>' : '')
    .'<div style="margin-top:12px;font-size:0.95em;color:#888;">Simpan invoice ini sebagai bukti pendaftaran.</div>'
    .'</div>';

// Balas JSON
$res = [
    'status' => 'ok',
    'invoice' => $invoice
];
echo json_encode($res); 