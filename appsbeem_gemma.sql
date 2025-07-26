-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Jul 2025 pada 16.19
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `appsbeem_gemma`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_fasilitas`
--

CREATE TABLE `tb_fasilitas` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `ikon` varchar(50) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_fasilitas`
--

INSERT INTO `tb_fasilitas` (`id`, `nama`, `keterangan`, `ikon`, `tanggal`) VALUES
(1, 'Pengajar Terbaik', 'Kami memiliki pengajar berpengalaman dan profesional.', 'fa-chalkboard-teacher', '2025-07-15 03:03:48'),
(2, 'Free Konsultasi', 'Konsultasi akademik & non-akademik gratis untuk siswa.', 'fa-comments', '2025-07-15 03:05:06'),
(3, 'Laporan Bulanan', 'Laporan perkembangan dan evaluasi bulanan untuk orang tua.', 'fa-clipboard-list', '2025-07-15 03:05:34'),
(4, 'Terpercaya', 'Lembaga bimbel terpercaya dengan berbagai program unggulan.', 'fa-certificate', '2025-07-15 03:05:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_jadwal`
--

CREATE TABLE `tb_jadwal` (
  `id` int(11) NOT NULL,
  `hari` varchar(20) NOT NULL,
  `buka` time NOT NULL,
  `tutup` time NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_jadwal`
--

INSERT INTO `tb_jadwal` (`id`, `hari`, `buka`, `tutup`, `tanggal`) VALUES
(1, 'Senin', '09:00:00', '20:00:00', '2025-07-14 05:50:11'),
(2, 'Selasa', '09:00:00', '20:00:00', '2025-07-14 05:50:11'),
(3, 'Rabu', '09:00:00', '20:00:00', '2025-07-14 05:50:11'),
(4, 'Kamis', '09:00:00', '20:00:00', '2025-07-14 05:50:11'),
(5, 'Jumat', '09:00:00', '20:00:00', '2025-07-14 05:50:11'),
(6, 'Sabtu', '09:00:00', '20:00:00', '2025-07-14 05:50:11'),
(7, 'Minggu', '00:00:00', '00:00:00', '2025-07-14 05:50:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_jenis_penilaian`
--

CREATE TABLE `tb_jenis_penilaian` (
  `id` int(11) NOT NULL,
  `nama_penilaian` varchar(100) NOT NULL,
  `urutan` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_jenis_penilaian`
--

INSERT INTO `tb_jenis_penilaian` (`id`, `nama_penilaian`, `urutan`, `created_at`) VALUES
(1, 'Disiplin', 1, '2025-07-26 11:24:07'),
(2, 'Pemahaman Materi', 2, '2025-07-26 11:24:07'),
(3, 'Kerjasama', 3, '2025-07-26 11:24:07'),
(4, 'Kreativitas', 4, '2025-07-26 11:24:07'),
(5, 'Tanggung Jawab', 5, '2025-07-26 11:24:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_jenjang`
--

CREATE TABLE `tb_jenjang` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_jenjang`
--

INSERT INTO `tb_jenjang` (`id`, `nama`, `keterangan`, `tanggal`) VALUES
(1, 'SD', 'Sekolah Dasa', '2025-07-14 05:27:13'),
(2, 'SMP', 'Sekolah Menengah Pertama', '2025-07-14 05:27:13'),
(3, 'SMA', 'Sekolah Menengah Atas', '2025-07-14 05:27:13'),
(4, 'UMUM', 'Umum', '2025-07-14 05:27:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_keuangan`
--

CREATE TABLE `tb_keuangan` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `debet` decimal(20,2) NOT NULL DEFAULT 0.00,
  `kredit` decimal(20,2) NOT NULL DEFAULT 0.00,
  `waktu_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_keuangan`
--

INSERT INTO `tb_keuangan` (`id`, `tanggal`, `keterangan`, `debet`, `kredit`, `waktu_update`) VALUES
(1, '2025-07-16', 'Pengeluaran', 0.00, 120000.00, '2025-07-16 15:28:11'),
(2, '2025-07-16', '[AUTO] Pembayaran Siswa ID: 7', 140000.00, 0.00, '2025-07-16 16:40:13'),
(3, '2025-07-17', '[AUTO] Pembayaran Siswa ID: 6', 70000.00, 0.00, '2025-07-17 02:55:06'),
(4, '2025-07-17', '[AUTO] Pembayaran Siswa: Doni Abiyantoro', 70000.00, 0.00, '2025-07-17 02:58:38'),
(5, '2025-07-17', '[AUTO] Pembayaran Siswa: Doni Abiyantoro', 100000.00, 0.00, '2025-07-17 03:18:27'),
(6, '2025-07-19', '[AUTO] Bayar - Siswa: Doni Abiyantoro', 60000.00, 0.00, '2025-07-19 11:30:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_kondisi`
--

CREATE TABLE `tb_kondisi` (
  `id` int(11) NOT NULL,
  `kode` varchar(10) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_kondisi`
--

INSERT INTO `tb_kondisi` (`id`, `kode`, `nama`, `tanggal`) VALUES
(1, 'KO001', 'Durasi les 1 jam/pertemuan', '2025-07-14 05:45:46'),
(2, 'KO002', 'Jadwal fleksibel sesuai keinginan siswa', '2025-07-14 05:45:46'),
(3, 'KO003', 'Ruang belajar nyaman & kondusif', '2025-07-14 05:45:46'),
(4, 'KO004', 'Konsultasi PR & tugas sekolah', '2025-07-14 05:45:46'),
(5, 'KO005', 'Kelompok SMP/SMA maksimal 4 orang', '2025-07-14 05:45:46'),
(6, 'KO006', 'Pembayaran setiap awal bulan (Cash/TF)', '2025-07-14 05:45:46'),
(7, 'KO007', 'Pertemuan hanya di bimbel, tidak ke rumah siswa', '2025-07-14 05:45:46'),
(8, 'KO008', 'Harga di atas untuk 1 siswa/mapel', '2025-07-14 05:45:46'),
(9, 'KO009', 'Izin maksimal 1 jam sebelum les, kurang dari 1 jam tidak dapat diganti', '2025-07-14 05:45:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_mapel`
--

CREATE TABLE `tb_mapel` (
  `id` int(11) NOT NULL,
  `kode` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_mapel`
--

INSERT INTO `tb_mapel` (`id`, `kode`, `nama`, `keterangan`, `status`, `tanggal`) VALUES
(1, 'GE001', 'CALISTUNG/TEMATIK', '-', 1, '2025-07-16 06:48:33'),
(2, 'GE002', 'Matematika SD', 'Matematika Sekolah Dasar', 1, '2025-07-16 06:48:30'),
(3, 'GE003', 'Matematika SMP', 'Matematika SMP', 1, '2025-07-16 06:48:28'),
(4, 'GE004', 'Matematika SMA', 'Matematika SMA', 1, '2025-07-16 06:48:26'),
(6, 'GE003', 'Lainnya', '-', 0, '2025-07-16 06:50:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_paket`
--

CREATE TABLE `tb_paket` (
  `id` int(11) NOT NULL,
  `Kode` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `jenjang` varchar(20) NOT NULL,
  `harga` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_paket`
--

INSERT INTO `tb_paket` (`id`, `Kode`, `nama`, `keterangan`, `jenjang`, `harga`, `status`, `tanggal`) VALUES
(1, 'PR001', 'Privat', 'Harian', 'SD', 45000, 1, '2025-07-16 05:59:09'),
(2, 'KL001', 'Kelompok', 'Harian', 'SD', 30000, 1, '2025-07-16 05:59:16'),
(3, 'PR002', 'Privat', 'Bulanan', 'SD', 120000, 1, '2025-07-16 05:59:21'),
(4, 'KL002', 'Kelompok', 'Bulanan', 'SD', 90000, 1, '2025-07-16 05:59:24'),
(5, 'PR003', 'Privat', 'Tahunan', 'SD', 140000, 0, '2025-07-17 02:22:22'),
(6, 'KL003', 'Kelompok', 'Tahunan', 'SD', 110000, 0, '2025-07-17 02:22:18'),
(9, 'PR999', 'Promo Privat', 'Persiapan PAS (Harian)', 'SD', 20000, 1, '2025-07-16 06:27:48'),
(10, 'KL999', 'Promo Kelompok', 'Persiapan PAS (Harian)', 'SD', 15000, 1, '2025-07-16 06:03:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_perkembangan_siswa`
--

CREATE TABLE `tb_perkembangan_siswa` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mapel` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `tentor` varchar(100) NOT NULL,
  `id_jenis_penilaian` int(11) NOT NULL,
  `nilai` int(1) NOT NULL CHECK (`nilai` >= 1 and `nilai` <= 5),
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_perkembangan_siswa`
--

INSERT INTO `tb_perkembangan_siswa` (`id`, `email`, `mapel`, `tanggal`, `tentor`, `id_jenis_penilaian`, `nilai`, `keterangan`, `created_at`) VALUES
(7, 'abiydoni@gmail.com', 1, '2025-07-26', 'Septi Fira', 1, 4, '', '2025-07-26 12:25:31'),
(8, 'abiydoni@gmail.com', 1, '2025-07-26', 'Septi Fira', 2, 4, '', '2025-07-26 12:25:31'),
(9, 'abiydoni@gmail.com', 1, '2025-07-26', 'Septi Fira', 3, 4, '', '2025-07-26 12:25:31'),
(10, 'abiydoni@gmail.com', 1, '2025-07-26', 'Septi Fira', 4, 3, '', '2025-07-26 12:25:31'),
(11, 'abiydoni@gmail.com', 1, '2025-07-26', 'Septi Fira', 5, 4, '', '2025-07-26 12:25:31'),
(12, 'abiydoni@gmail.com', 2, '2025-07-26', 'Budi Kurniawan', 1, 3, 'Selalu disiplin', '2025-07-26 12:33:26'),
(13, 'abiydoni@gmail.com', 2, '2025-07-26', 'Budi Kurniawan', 2, 3, 'Pahan dengan perhitungan', '2025-07-26 12:33:26'),
(14, 'abiydoni@gmail.com', 2, '2025-07-26', 'Budi Kurniawan', 3, 4, 'Cukup', '2025-07-26 12:33:26'),
(15, 'abiydoni@gmail.com', 2, '2025-07-26', 'Budi Kurniawan', 4, 2, 'Kreatif', '2025-07-26 12:33:26'),
(16, 'abiydoni@gmail.com', 2, '2025-07-26', 'Budi Kurniawan', 5, 5, 'Selalu bertanggung jawab', '2025-07-26 12:33:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_profile`
--

CREATE TABLE `tb_profile` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `keterangan` varchar(225) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `wa` varchar(20) NOT NULL,
  `ig` varchar(50) NOT NULL,
  `logo1` varchar(100) NOT NULL,
  `logo2` varchar(100) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_profile`
--

INSERT INTO `tb_profile` (`id`, `nama`, `keterangan`, `alamat`, `email`, `wa`, `ig`, `logo1`, `logo2`, `tanggal`) VALUES
(1, 'Bimbel Gemma', 'Bimbingan belajar modern, seru, dan penuh semangat!', 'JL. Dworowati No.5 Randuares RT.07/RW.01 Kumpulreo, Argomulyo, Salatiga, 50734', 'gemma@appsbee.com', '0895-2974-9003', 'bimbelgemma', 'logo4.png', 'logo2.png', '2025-07-16 07:00:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_siswa`
--

CREATE TABLE `tb_siswa` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `ortu` varchar(100) DEFAULT NULL,
  `hp_ortu` varchar(30) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_siswa`
--

INSERT INTO `tb_siswa` (`id`, `nama`, `gender`, `tgl_lahir`, `ortu`, `hp_ortu`, `alamat`, `email`, `foto`, `created_at`) VALUES
(1, 'Doni Abiyantoro', 'Laki-laki', '2025-06-29', 'Sutini', '085225106200', 'Salatiga', 'abiydoni@gmail.com', 'siswa_1752576209_9413.jpg', '2025-07-14 11:20:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_trx`
--

CREATE TABLE `tb_trx` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `paket` varchar(50) NOT NULL,
  `mapel` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `bayar` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_tentor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_trx`
--

INSERT INTO `tb_trx` (`id`, `email`, `paket`, `mapel`, `harga`, `bayar`, `status`, `tanggal`, `id_tentor`) VALUES
(19, 'abiydoni@gmail.com', 'KL001', 'GE002', 60000, 60000, 1, '2025-07-19 11:30:19', 4),
(22, 'abiydoni@gmail.com', 'PR002', 'GE001', 120000, 0, 0, '2025-07-26 11:55:26', 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_trx_tanggal`
--

CREATE TABLE `tb_trx_tanggal` (
  `id` int(11) NOT NULL,
  `id_trx` int(11) DEFAULT NULL,
  `jam_trx` varchar(10) NOT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_trx_tanggal`
--

INSERT INTO `tb_trx_tanggal` (`id`, `id_trx`, `jam_trx`, `tanggal`) VALUES
(16, 19, '13:00', '2025-07-30'),
(17, 19, '16:00', '2025-07-20'),
(23, 22, '20:00', '2025-07-26'),
(24, 22, '20:00', '2025-08-02'),
(25, 22, '20:00', '2025-08-09'),
(26, 22, '20:00', '2025-08-16'),
(27, 22, '20:00', '2025-08-23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_user`
--

CREATE TABLE `tb_user` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `hp` varchar(20) NOT NULL,
  `role` varchar(50) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_user`
--

INSERT INTO `tb_user` (`id`, `email`, `nama`, `password`, `hp`, `role`, `tanggal`) VALUES
(1, 'abiydoni@gmail.com', 'Doni Abiyantoro', '$2y$10$Xkk4KxJJP/14KuKkl3szgu509UwL.6eCmgqsvTNyxqccDPhnJWaqS', '085225106200', 's_admin', '2025-07-26 11:44:23'),
(3, 'bimbelgemma@gmail.com', 'Aviana AS', '$2y$10$yHOlSZHjfUlYm3GDHv5F2uVDHCikcbDqv1Dkyb5k6MJIpaM2NsIji', '085000000112', 'admin', '2025-07-26 11:48:33'),
(4, 'tentor@gmail.com', 'Budi Kurniawan', '$2y$10$GVXlP4GrPBDrt/GJ6olF0uSiaFAe0o7h7nURTeoy3bgmYfSV4pBee', '08512341234', 'tentor', '2025-07-26 11:46:37'),
(5, 'tentor2@gmail.com', 'Septi Fira', '$2y$10$5nx/QNzKaTfLbwiRFf32wuk9LM4xWvPI3Icwk8gaQ/zjbjdhUwWim', '085198765432', 'tentor', '2025-07-26 11:46:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_gaji_tentor`
--

CREATE TABLE `tb_gaji_tentor` (
  `id` int(11) NOT NULL,
  `id_tentor` int(11) NOT NULL,
  `id_trx` int(11) NOT NULL,
  `email_siswa` varchar(100) NOT NULL,
  `mapel` int(11) NOT NULL,
  `total_pembayaran` decimal(10,2) NOT NULL,
  `presentase_gaji` decimal(5,2) NOT NULL,
  `jumlah_gaji` decimal(10,2) NOT NULL,
  `bulan` varchar(7) NOT NULL,
  `status_pembayaran` enum('pending','dibayar') DEFAULT 'pending',
  `tanggal_pembayaran` date DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_setting_gaji`
--

CREATE TABLE `tb_setting_gaji` (
  `id` int(11) NOT NULL,
  `mapel` int(11) NOT NULL,
  `presentase_gaji` decimal(5,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_setting_gaji`
--

INSERT INTO `tb_setting_gaji` (`id`, `mapel`, `presentase_gaji`, `keterangan`, `created_at`) VALUES
(1, 1, 30.00, 'CALISTUNG/TEMATIK - 30%', '2025-07-26 16:30:00'),
(2, 2, 25.00, 'Matematika SD - 25%', '2025-07-26 16:30:00'),
(3, 3, 25.00, 'Matematika SMP - 25%', '2025-07-26 16:30:00'),
(4, 4, 25.00, 'Matematika SMA - 25%', '2025-07-26 16:30:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_mapel_tentor`
--

CREATE TABLE `tb_mapel_tentor` (
  `id` int(11) NOT NULL,
  `mapel` varchar(100) NOT NULL,
  `id_tentor` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_mapel_tentor`
--

INSERT INTO `tb_mapel_tentor` (`id`, `mapel`, `id_tentor`) VALUES
(1, 'GE001', 4),
(2, 'GE002', 5),
(3, 'GE003', 4),
(4, 'GE004', 5),
(5, 'GE005', 4);

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_fasilitas`
--
ALTER TABLE `tb_fasilitas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_jadwal`
--
ALTER TABLE `tb_jadwal`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_jenis_penilaian`
--
ALTER TABLE `tb_jenis_penilaian`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_jenjang`
--
ALTER TABLE `tb_jenjang`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_keuangan`
--
ALTER TABLE `tb_keuangan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_kondisi`
--
ALTER TABLE `tb_kondisi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_mapel`
--
ALTER TABLE `tb_mapel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kode` (`kode`);

--
-- Indeks untuk tabel `tb_paket`
--
ALTER TABLE `tb_paket`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_perkembangan_siswa`
--
ALTER TABLE `tb_perkembangan_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `mapel` (`mapel`),
  ADD KEY `tanggal` (`tanggal`),
  ADD KEY `id_jenis_penilaian` (`id_jenis_penilaian`);

--
-- Indeks untuk tabel `tb_profile`
--
ALTER TABLE `tb_profile`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_siswa`
--
ALTER TABLE `tb_siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_trx`
--
ALTER TABLE `tb_trx`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indeks untuk tabel `tb_trx_tanggal`
--
ALTER TABLE `tb_trx_tanggal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_trx` (`id_trx`);

--
-- Indeks untuk tabel `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_gaji_tentor`
--
ALTER TABLE `tb_gaji_tentor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tentor` (`id_tentor`),
  ADD KEY `id_trx` (`id_trx`),
  ADD KEY `email_siswa` (`email_siswa`),
  ADD KEY `mapel` (`mapel`);

--
-- Indeks untuk tabel `tb_setting_gaji`
--
ALTER TABLE `tb_setting_gaji`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mapel` (`mapel`);

--
-- Indeks untuk tabel `tb_mapel_tentor`
--
ALTER TABLE `tb_mapel_tentor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mapel` (`mapel`),
  ADD KEY `id_tentor` (`id_tentor`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_fasilitas`
--
ALTER TABLE `tb_fasilitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tb_jadwal`
--
ALTER TABLE `tb_jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `tb_jenis_penilaian`
--
ALTER TABLE `tb_jenis_penilaian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tb_jenjang`
--
ALTER TABLE `tb_jenjang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tb_keuangan`
--
ALTER TABLE `tb_keuangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tb_kondisi`
--
ALTER TABLE `tb_kondisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `tb_mapel`
--
ALTER TABLE `tb_mapel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tb_paket`
--
ALTER TABLE `tb_paket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `tb_perkembangan_siswa`
--
ALTER TABLE `tb_perkembangan_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `tb_profile`
--
ALTER TABLE `tb_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tb_siswa`
--
ALTER TABLE `tb_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tb_trx`
--
ALTER TABLE `tb_trx`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `tb_trx_tanggal`
--
ALTER TABLE `tb_trx_tanggal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tb_gaji_tentor`
--
ALTER TABLE `tb_gaji_tentor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_setting_gaji`
--
ALTER TABLE `tb_setting_gaji`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tb_mapel_tentor`
--
ALTER TABLE `tb_mapel_tentor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tb_perkembangan_siswa`
--
ALTER TABLE `