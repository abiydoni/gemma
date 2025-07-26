<?php
header('Content-Type: application/json');
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $tgl_lahir = $_POST['tgl_lahir'] ?? '';
    
    if (empty($email) || empty($tgl_lahir)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email dan tanggal lahir harus diisi'
        ]);
        exit;
    }
    
    // Validasi format tanggal lahir (DDMMYYYY)
    if (!preg_match('/^\d{8}$/', $tgl_lahir)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format tanggal lahir harus DDMMYYYY (8 digit angka)'
        ]);
        exit;
    }
    
    // Konversi DDMMYYYY ke format database
    $day = substr($tgl_lahir, 0, 2);
    $month = substr($tgl_lahir, 2, 2);
    $year = substr($tgl_lahir, 4, 4);
    
    // Validasi tanggal yang masuk akal
    if (!checkdate($month, $day, $year)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tanggal lahir tidak valid'
        ]);
        exit;
    }
    
    // Format tanggal untuk database (YYYY-MM-DD)
    $tgl_lahir_db = $year . '-' . $month . '-' . $day;
    
    try {
        // Cek data siswa berdasarkan email dan tanggal lahir
        $stmt = $pdo->prepare("
            SELECT id, nama, email, tgl_lahir, gender, alamat, ortu, hp_ortu 
            FROM tb_siswa 
            WHERE email = ? AND tgl_lahir = ?
            LIMIT 1
        ");
        $stmt->execute([$email, $tgl_lahir_db]);
        $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($siswa) {
            echo json_encode([
                'status' => 'ok',
                'message' => 'Data siswa ditemukan',
                'data' => [
                    'id' => $siswa['id'],
                    'nama' => $siswa['nama'],
                    'email' => $siswa['email'],
                    'tgl_lahir' => $siswa['tgl_lahir'],
                    'gender' => $siswa['gender'],
                    'alamat' => $siswa['alamat'],
                    'ortu' => $siswa['ortu'],
                    'hp_ortu' => $siswa['hp_ortu']
                ]
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Email atau tanggal lahir tidak sesuai dengan data yang terdaftar'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi kesalahan pada sistem: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Method tidak diizinkan'
    ]);
}
?> 