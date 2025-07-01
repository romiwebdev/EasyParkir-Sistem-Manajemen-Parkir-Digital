-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: sql12.freesqldatabase.com
-- Generation Time: 01 Jul 2025 pada 01.17
-- Versi Server: 5.5.62-0ubuntu0.14.04.1
-- PHP Version: 7.0.33-0ubuntu0.16.04.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sql12787385`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenisKendaraan`
--

CREATE TABLE `jenisKendaraan` (
  `id_jenisKendaraan` int(11) NOT NULL,
  `jenis_kendaraan` varchar(20) DEFAULT NULL,
  `harga` varchar(10) DEFAULT NULL,
  `kapasitas_slot` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `jenisKendaraan`
--

INSERT INTO `jenisKendaraan` (`id_jenisKendaraan`, `jenis_kendaraan`, `harga`, `kapasitas_slot`) VALUES
(1, 'Motor', '2000', 50),
(2, 'Mobil', '5000', 20),
(3, 'Listrik', '2000', 20);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kendaraan_masuk`
--

CREATE TABLE `kendaraan_masuk` (
  `id` int(11) NOT NULL,
  `kode_unik` varchar(20) NOT NULL,
  `nama_kendaraan` varchar(50) DEFAULT NULL,
  `id_jenisKendaraan` int(11) DEFAULT NULL,
  `waktu_masuk` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `kendaraan_masuk`
--

INSERT INTO `kendaraan_masuk` (`id`, `kode_unik`, `nama_kendaraan`, `id_jenisKendaraan`, `waktu_masuk`, `id_user`) VALUES
(41, '3A299B13', 'Beat Hitam', 1, '2025-07-01 13:16:33', 0),
(42, '69962081', 'Beat Hitam', 1, '2025-07-01 13:22:50', 0),
(44, '61933D24', 'Scoopy Hitam', 1, '2025-07-01 13:37:38', 1),
(46, '191C8A1C', 'Scoopy Hitam', 1, '2025-07-01 14:00:41', 2),
(47, '9F09BE70', 'vario 125 merah', 1, '2025-07-01 14:03:39', 1),
(48, '61A3178E', 'vario 125 Hitam', 1, '2025-07-01 14:04:19', 1),
(49, '992B3347', 'Scoopy Abu', 1, '2025-07-01 14:11:56', 1),
(50, 'CFA02D02', 'Scoopy Putih', 1, '2025-07-01 14:12:30', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_keluar`
--

CREATE TABLE `riwayat_keluar` (
  `id` int(11) NOT NULL,
  `kode_unik` varchar(20) DEFAULT NULL,
  `nama_kendaraan` varchar(50) DEFAULT NULL,
  `id_jenisKendaraan` int(11) DEFAULT NULL,
  `waktu_masuk` datetime DEFAULT NULL,
  `waktu_keluar` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `durasi_hari` int(11) DEFAULT NULL,
  `biaya` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `riwayat_keluar`
--

INSERT INTO `riwayat_keluar` (`id`, `kode_unik`, `nama_kendaraan`, `id_jenisKendaraan`, `waktu_masuk`, `waktu_keluar`, `durasi_hari`, `biaya`, `id_user`) VALUES
(10, 'FBA8FD28', 'vario 125 merah Putih', 1, '2025-07-01 05:30:47', '2025-07-01 13:31:44', 1, 2000, 1),
(11, '62B10080', 'Scoopy hitam', 1, '2025-06-30 20:56:17', '2025-07-01 13:50:01', 1, 2000, 1),
(12, 'DA47A2EC', 'Beat biru putih', 1, '2025-06-30 20:56:09', '2025-07-01 13:51:55', 1, 2000, 1),
(13, '6676B022', 'Revo Merah', 1, '2025-06-30 20:56:23', '2025-07-01 13:53:56', 1, 2000, 1),
(14, '062BFE23', 'vario 125 Putih', 1, '2025-06-30 20:54:10', '2025-07-01 13:55:35', 1, 2000, 1),
(15, 'A588B54F', 'vario 125 merah doff', 1, '2025-06-30 20:56:34', '2025-07-01 13:58:34', 1, 2000, 1),
(16, '39D67CA1', 'Beat biru putih', 1, '2025-07-01 06:00:22', '2025-07-01 14:01:24', 1, 2000, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','petugas') NOT NULL DEFAULT 'petugas'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$vX0Z5vFYkP691V0T2bjx9uo1nja5tQfZi/CWBMkXRtxKQvMwlmsRy', 'admin'),
(2, 'roy', '$2y$10$VGdBl6g.ucyc0UtiDA/jy.1.XbAiJBsIKpC25sh9xxRJ0agVRWijO', 'petugas'),
(3, 'kafa', '$2y$10$zPyqT/6uN4c2H89/54kx0uuSR1UnMeRxg1pQE2JjwC4F8fwc8RoJ2', 'petugas'),
(5, 'pangdi', '$2y$10$skhDG/esyGMGZt7l3pO4Xe0sMSxIkrk.hiPQO3NxUGAeT9eEzlsgq', 'petugas');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jenisKendaraan`
--
ALTER TABLE `jenisKendaraan`
  ADD PRIMARY KEY (`id_jenisKendaraan`);

--
-- Indexes for table `kendaraan_masuk`
--
ALTER TABLE `kendaraan_masuk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_unik` (`kode_unik`),
  ADD KEY `id_jenisKendaraan` (`id_jenisKendaraan`);

--
-- Indexes for table `riwayat_keluar`
--
ALTER TABLE `riwayat_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jenisKendaraan`
--
ALTER TABLE `jenisKendaraan`
  MODIFY `id_jenisKendaraan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `kendaraan_masuk`
--
ALTER TABLE `kendaraan_masuk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
--
-- AUTO_INCREMENT for table `riwayat_keluar`
--
ALTER TABLE `riwayat_keluar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kendaraan_masuk`
--
ALTER TABLE `kendaraan_masuk`
  ADD CONSTRAINT `kendaraan_masuk_ibfk_1` FOREIGN KEY (`id_jenisKendaraan`) REFERENCES `jenisKendaraan` (`id_jenisKendaraan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
