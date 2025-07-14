-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2025 at 10:09 AM
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
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_fasilitas`
--

INSERT INTO `tb_fasilitas` (`id`, `nama`, `keterangan`, `tanggal`) VALUES
(1, 'Pengajar Terbaik', 'Kami memiliki pengajar berpengalaman dan profesional.', '2025-07-14 06:00:35'),
(2, 'Free Konsultasi', 'Konsultasi akademik & non-akademik gratis untuk siswa.', '2025-07-14 06:00:35'),
(3, 'Laporan Bulanan', 'Laporan perkembangan dan evaluasi bulanan untuk orang tua.', '2025-07-14 06:00:35'),
(4, 'Terpercaya', 'Lembaga bimbel terpercaya dengan berbagai program unggulan.', '2025-07-14 06:00:35');

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
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_mapel`
--

INSERT INTO `tb_mapel` (`id`, `kode`, `nama`, `keterangan`, `tanggal`) VALUES
(1, 'GE001', 'CALISTUNG/TEMATIK', '-', '2025-07-14 05:09:02'),
(2, 'GE002', 'Matematika SD', 'Matematika Sekolah Dasar', '2025-07-14 05:09:02'),
(3, 'GE003', 'Matematika SMP', 'Matematika SMP', '2025-07-14 05:11:52'),
(4, 'GE004', 'Matematika SMA', 'Matematika SMA', '2025-07-14 05:11:52'),
(5, 'GE999', 'Lainnya', 'Sesuai Request', '2025-07-14 05:22:04');

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
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_paket`
--

INSERT INTO `tb_paket` (`id`, `Kode`, `nama`, `keterangan`, `jenjang`, `harga`, `tanggal`) VALUES
(1, 'PR001', 'Privat SD', '-', 'SD', 100000, '2025-07-14 05:38:43'),
(2, 'KL001', 'Kelompok SD', '-', 'SD', 70000, '2025-07-14 05:38:43'),
(3, 'PR002', 'Privat SD', '-', 'SD', 120000, '2025-07-14 05:38:43'),
(4, 'KL002', 'Kelompok SD', '-', 'SD', 90000, '2025-07-14 05:38:43'),
(5, 'PR003', 'Privat SD', '-', 'SD', 140000, '2025-07-14 05:38:43'),
(6, 'KL003', 'Kelompok SD', '-', 'SD', 110000, '2025-07-14 05:38:43'),
(7, 'PR004', 'Privat SD', '-', 'SD', 160000, '2025-07-14 05:38:43'),
(8, 'KL004', 'Kelompok SD', '-', 'SD', 130000, '2025-07-14 05:38:43');

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
(1, 'Bimbel Gemma', 'Bimbingan belajar modern, seru, dan penuh semangat!', 'JL. Dworowati No.5 Randuares RT.07/RW.01 Kumpulreo, Argomulyo, Salatiga, 50734', '-', '0895-2974-9003', 'bimbelgemma', 'logo4.png', 'logo2.png', '2025-07-14 06:15:38');

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
-- AUTO_INCREMENT for table `tb_kondisi`
--
ALTER TABLE `tb_kondisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tb_mapel`
--
ALTER TABLE `tb_mapel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_paket`
--
ALTER TABLE `tb_paket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_profile`
--
ALTER TABLE `tb_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
