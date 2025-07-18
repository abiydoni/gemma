-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 18, 2025 at 05:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
-- Table structure for table `tb_fasilitas`
--

CREATE TABLE `tb_fasilitas` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `ikon` varchar(50) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_fasilitas`
--

INSERT INTO `tb_fasilitas` (`id`, `nama`, `keterangan`, `ikon`, `tanggal`) VALUES
(1, 'Pengajar Terbaik', 'Kami memiliki pengajar berpengalaman dan profesional.', 'fa-chalkboard-teacher', '2025-07-15 03:03:48'),
(2, 'Free Konsultasi', 'Konsultasi akademik & non-akademik gratis untuk siswa.', 'fa-comments', '2025-07-15 03:05:06'),
(3, 'Laporan Bulanan', 'Laporan perkembangan dan evaluasi bulanan untuk orang tua.', 'fa-clipboard-list', '2025-07-15 03:05:34'),
(4, 'Terpercaya', 'Lembaga bimbel terpercaya dengan berbagai program unggulan.', 'fa-certificate', '2025-07-15 03:05:58');

-- --------------------------------------------------------

--
-- Table structure for table `tb_jadwal`
--

CREATE TABLE `tb_jadwal` (
  `id` int(11) NOT NULL,
  `hari` varchar(20) NOT NULL,
  `buka` time NOT NULL,
  `tutup` time NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_jadwal`
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
-- Table structure for table `tb_jenjang`
--

CREATE TABLE `tb_jenjang` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_jenjang`
--

INSERT INTO `tb_jenjang` (`id`, `nama`, `keterangan`, `tanggal`) VALUES
(1, 'SD', 'Sekolah Dasa', '2025-07-14 05:27:13'),
(2, 'SMP', 'Sekolah Menengah Pertama', '2025-07-14 05:27:13'),
(3, 'SMA', 'Sekolah Menengah Atas', '2025-07-14 05:27:13'),
(4, 'UMUM', 'Umum', '2025-07-14 05:27:13');

-- --------------------------------------------------------

--
-- Table structure for table `tb_keuangan`
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
-- Dumping data for table `tb_keuangan`
--

INSERT INTO `tb_keuangan` (`id`, `tanggal`, `keterangan`, `debet`, `kredit`, `waktu_update`) VALUES
(1, '2025-07-16', 'Pengeluaran', 0.00, 120000.00, '2025-07-16 15:28:11'),
(2, '2025-07-16', '[AUTO] Pembayaran Siswa ID: 7', 140000.00, 0.00, '2025-07-16 16:40:13'),
(3, '2025-07-17', '[AUTO] Pembayaran Siswa ID: 6', 70000.00, 0.00, '2025-07-17 02:55:06'),
(4, '2025-07-17', '[AUTO] Pembayaran Siswa: Doni Abiyantoro', 70000.00, 0.00, '2025-07-17 02:58:38'),
(5, '2025-07-17', '[AUTO] Pembayaran Siswa: Doni Abiyantoro', 100000.00, 0.00, '2025-07-17 03:18:27'),
(6, '2025-07-18', '[PEMBAYARAN] Siswa: Aviana Ariestasya Sari (pipin@gmail.com)', 30000.00, 0.00, '2025-07-18 12:51:28'),
(7, '2025-07-18', '[AUTO] Bayar - Siswa: Aviana Ariestasya Sari', 30000.00, 0.00, '2025-07-18 12:55:55');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kondisi`
--

CREATE TABLE `tb_kondisi` (
  `id` int(11) NOT NULL,
  `kode` varchar(10) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kondisi`
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
-- Table structure for table `tb_mapel`
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
-- Dumping data for table `tb_mapel`
--

INSERT INTO `tb_mapel` (`id`, `kode`, `nama`, `keterangan`, `status`, `tanggal`) VALUES
(1, 'GE001', 'CALISTUNG/TEMATIK', '-', 1, '2025-07-16 06:48:33'),
(2, 'GE002', 'Matematika SD', 'Matematika Sekolah Dasar', 1, '2025-07-16 06:48:30'),
(3, 'GE003', 'Matematika SMP', 'Matematika SMP', 1, '2025-07-16 06:48:28'),
(4, 'GE004', 'Matematika SMA', 'Matematika SMA', 1, '2025-07-16 06:48:26'),
(6, 'GE003', 'Lainnya', '-', 0, '2025-07-16 06:50:20');

-- --------------------------------------------------------

--
-- Table structure for table `tb_paket`
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
-- Dumping data for table `tb_paket`
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
-- Table structure for table `tb_profile`
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
-- Dumping data for table `tb_profile`
--

INSERT INTO `tb_profile` (`id`, `nama`, `keterangan`, `alamat`, `email`, `wa`, `ig`, `logo1`, `logo2`, `tanggal`) VALUES
(1, 'Bimbel Gemma', 'Bimbingan belajar modern, seru, dan penuh semangat!', 'JL. Dworowati No.5 Randuares RT.07/RW.01 Kumpulreo, Argomulyo, Salatiga, 50734', 'gemma@appsbee.com', '0895-2974-9003', 'bimbelgemma', 'logo4.png', 'logo2.png', '2025-07-16 07:00:28');

-- --------------------------------------------------------

--
-- Table structure for table `tb_siswa`
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
-- Dumping data for table `tb_siswa`
--

INSERT INTO `tb_siswa` (`id`, `nama`, `gender`, `tgl_lahir`, `ortu`, `hp_ortu`, `alamat`, `email`, `foto`, `created_at`) VALUES
(1, 'Doni Abiyantoro', 'Laki-laki', '2025-06-29', 'Sutini', '085225106200', 'Salatiga', 'abiydoni@gmail.com', 'siswa_1752576209_9413.jpg', '2025-07-14 11:20:21'),
(2, 'Aviana Ariestasya Sari', 'Perempuan', '2025-06-30', 'Yuli Astuti', '+62 856-4330-6224', 'Salatiga', 'pipin@gmail.com', 'siswa_1752576234_3208.jpeg', '2025-07-15 07:33:18');

-- --------------------------------------------------------

--
-- Table structure for table `tb_trx`
--

CREATE TABLE `tb_trx` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `paket` varchar(50) NOT NULL,
  `mapel` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `bayar` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_trx`
--

INSERT INTO `tb_trx` (`id`, `email`, `paket`, `mapel`, `harga`, `bayar`, `status`, `tanggal`) VALUES
(9, 'pipin@gmail.com', 'KL001', 'GE001', 30000, 30000, 1, '2025-07-18 12:55:55'),
(11, 'abiydoni@gmail.com', 'PR002', 'GE004', 120000, 120000, 1, '2025-07-18 03:03:48');

-- --------------------------------------------------------

--
-- Table structure for table `tb_trx_tanggal`
--

CREATE TABLE `tb_trx_tanggal` (
  `id` int(11) NOT NULL,
  `id_trx` int(11) DEFAULT NULL,
  `jam_trx` varchar(10) NOT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_trx_tanggal`
--

INSERT INTO `tb_trx_tanggal` (`id`, `id_trx`, `jam_trx`, `tanggal`) VALUES
(1, 9, '19:00', '2025-07-17'),
(2, 9, '17:00', '2025-07-26'),
(8, 11, '14:00', '2025-07-22'),
(9, 11, '14:00', '2025-07-29'),
(10, 11, '14:00', '2025-08-05'),
(11, 11, '14:00', '2025-08-12'),
(12, 11, '14:00', '2025-08-19');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id`, `email`, `nama`, `password`, `role`, `tanggal`) VALUES
(1, 'abiydoni@gmail.com', 'Doni Abiyantoro', '$2y$10$Xkk4KxJJP/14KuKkl3szgu509UwL.6eCmgqsvTNyxqccDPhnJWaqS', 's_admin', '2025-07-14 07:33:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_fasilitas`
--
ALTER TABLE `tb_fasilitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_jadwal`
--
ALTER TABLE `tb_jadwal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_jenjang`
--
ALTER TABLE `tb_jenjang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_keuangan`
--
ALTER TABLE `tb_keuangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_kondisi`
--
ALTER TABLE `tb_kondisi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_mapel`
--
ALTER TABLE `tb_mapel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kode` (`kode`);

--
-- Indexes for table `tb_paket`
--
ALTER TABLE `tb_paket`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_profile`
--
ALTER TABLE `tb_profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_siswa`
--
ALTER TABLE `tb_siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_trx`
--
ALTER TABLE `tb_trx`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `tb_trx_tanggal`
--
ALTER TABLE `tb_trx_tanggal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_trx` (`id_trx`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_fasilitas`
--
ALTER TABLE `tb_fasilitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_jadwal`
--
ALTER TABLE `tb_jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_jenjang`
--
ALTER TABLE `tb_jenjang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_keuangan`
--
ALTER TABLE `tb_keuangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_kondisi`
--
ALTER TABLE `tb_kondisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tb_mapel`
--
ALTER TABLE `tb_mapel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_paket`
--
ALTER TABLE `tb_paket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_profile`
--
ALTER TABLE `tb_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_siswa`
--
ALTER TABLE `tb_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_trx`
--
ALTER TABLE `tb_trx`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_trx_tanggal`
--
ALTER TABLE `tb_trx_tanggal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_trx_tanggal`
--
ALTER TABLE `tb_trx_tanggal`
  ADD CONSTRAINT `tb_trx_tanggal_ibfk_1` FOREIGN KEY (`id_trx`) REFERENCES `tb_trx` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
