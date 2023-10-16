-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2023 at 05:02 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smart_parking`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_lokasi`
--

CREATE TABLE `detail_lokasi` (
  `id` bigint(20) NOT NULL,
  `parkir_id` bigint(20) NOT NULL,
  `lokasi_detail_parkir` varchar(6) NOT NULL,
  `status` int(11) NOT NULL,
  `harga_tiket` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `detail_lokasi`
--

INSERT INTO `detail_lokasi` (`id`, `parkir_id`, `lokasi_detail_parkir`, `status`, `harga_tiket`, `created_at`, `updated_at`) VALUES
(1, 1, 'a-36', 0, 2000, NULL, NULL),
(2, 1, 'a-37', 0, 2000, NULL, '2023-10-15 14:59:02'),
(3, 2, 'b-34', 1, 3000, NULL, '2023-10-15 14:51:48'),
(4, 1, 'a-39', 0, 2000, NULL, NULL),
(5, 3, 'c-21', 0, 5000, NULL, NULL),
(6, 3, 'c-31', 0, 5000, NULL, NULL),
(7, 2, 'b-21', 0, 3000, NULL, NULL),
(8, 2, 'b-32', 0, 3000, NULL, NULL),
(9, 3, 'c-41', 0, 5000, NULL, NULL),
(10, 3, 'c-51', 0, 5000, NULL, NULL),
(11, 1, 'a-41', 0, 2000, NULL, NULL),
(12, 1, 'a-43', 0, 2000, NULL, NULL),
(13, 4, 'd-43', 0, 2500, NULL, NULL),
(14, 4, 'd-45', 0, 2500, NULL, NULL),
(15, 5, 'e-62', 0, 5000, NULL, NULL),
(16, 5, 'e-36', 0, 5000, NULL, NULL),
(17, 6, 'f-34', 0, 2000, NULL, NULL),
(18, 6, 'f-34', 0, 2000, NULL, NULL),
(19, 7, 'g-21', 0, 3500, NULL, NULL),
(20, 7, 'g-83', 0, 3500, NULL, NULL),
(21, 8, 'h-42', 0, 4000, NULL, NULL),
(22, 8, 'h-54', 0, 4000, NULL, NULL),
(23, 9, 'i-74', 0, 4500, NULL, NULL),
(24, 9, 'i-78', 0, 4500, NULL, NULL),
(25, 10, 'j-86', 0, 5500, NULL, NULL),
(26, 10, 'j-76', 0, 5500, NULL, NULL),
(27, 4, 'd-53', 0, 2500, NULL, NULL),
(28, 4, 'd-54', 0, 2500, NULL, NULL),
(29, 5, 'e-55', 0, 5000, NULL, NULL),
(30, 5, 'e-67', 0, 5000, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id` bigint(20) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `nama_kendaraan` varchar(150) NOT NULL,
  `nomor_plat` varchar(20) NOT NULL,
  `foto_stnk` varchar(150) NOT NULL,
  `foto_kendaraan_tampak_depan` varchar(150) NOT NULL,
  `foto_kendaraan_tampak_belakang` varchar(150) NOT NULL,
  `foto_kendaraan_dengan_pemilik` varchar(150) NOT NULL,
  `image_qr` varchar(150) DEFAULT NULL,
  `is_active` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kendaraan`
--

INSERT INTO `kendaraan` (`id`, `user_id`, `nama_kendaraan`, `nomor_plat`, `foto_stnk`, `foto_kendaraan_tampak_depan`, `foto_kendaraan_tampak_belakang`, `foto_kendaraan_dengan_pemilik`, `image_qr`, `is_active`, `created_at`, `updated_at`) VALUES
(3, '123456765897654', 'Porsche Boxter S 981', 'P 3333 DK', '35y5UWwdsm.jpeg', 'gxw1k5Xmco.jpeg', '5zkneKhQvZ.jpeg', 'pptIzpfP6k.jpeg', 'kendaraaan_652a8b557a555.png', 1, '2023-10-14 05:36:37', '2023-10-15 06:39:57');

-- --------------------------------------------------------

--
-- Table structure for table `parkir`
--

CREATE TABLE `parkir` (
  `id` bigint(20) NOT NULL,
  `lokasi_parkir` varchar(50) NOT NULL,
  `alamat` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `parkir`
--

INSERT INTO `parkir` (`id`, `lokasi_parkir`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 'Lippo Plaza', 'Jl. Gajah Mada No.106, Kb. Kidul, Jember Kidul, Kec. Kaliwates, Kabupaten Jember, Jawa Timur', NULL, NULL),
(2, 'Roxy Square', 'Jl. Hayam Wuruk No.50-58, Gerdu, Sempusari, Kaliwates, Kabupaten Jember, Jawa Timur', NULL, NULL),
(3, 'Matahari Johar Plaza', 'Johar Plaza, Jl. Diponegoro No.3, Tembaan, Kepatihan, Kec. Kaliwates, Kabupaten Jember, Jawa Timur', NULL, NULL),
(4, 'Super Galaxy Tempurejo', 'Galaksi, Tempurejo, Kabupaten Jember, Jawa Timur', NULL, NULL),
(5, 'Golden Market', 'Jl. Trunojoyo No.42, Sawahan Cantian, Kepatihan, Kec. Kaliwates, Kabupaten Jember, Jawa Timur', NULL, NULL),
(6, 'Dira Shopping Centre', 'Jl. Puger, Krajan Lor, Balung Kulon, Balung, Kabupaten Jember, Jawa Timur', NULL, NULL),
(7, 'Transmart Jember', 'Jl. Hayam Wuruk No.71, Gerdu, Sempusari, Kec. Kaliwates, Kabupaten Jember, Jawa Timur', NULL, NULL),
(8, 'Larisso Supermarket', 'Jl. Watu Ulo No.21, Krajan, Ambulu, Kabupaten Jember, Jawa Timur', NULL, NULL),
(9, ' Waterpark Dira Kencong', 'Jl. Krakatau No.93, Pd. Waluh, Kencong, Kabupaten Jember, Jawa Timur', NULL, NULL),
(10, 'Kota Cinema Mall', 'Jl. Gajah Mada No.176, Kb. Kidul, Jember Kidul, Kec. Kaliwates, Kabupaten Jember, Jawa Timur', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `saldo`
--

CREATE TABLE `saldo` (
  `id` bigint(20) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `nominal` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `saldo`
--

INSERT INTO `saldo` (`id`, `user_id`, `nominal`, `tanggal`, `status`, `created_at`, `updated_at`) VALUES
(7, '123456765897654', 100000, '2023-10-14', 1, '2023-10-14 14:02:46', '2023-10-14 14:02:46'),
(13, '123456765897654', 2000, '2023-10-15', 0, '2023-10-15 14:56:58', '2023-10-15 14:56:58');

--
-- Triggers `saldo`
--
DELIMITER $$
CREATE TRIGGER `edit_saldo` AFTER UPDATE ON `saldo` FOR EACH ROW BEGIN
    IF NEW.status = 0 THEN
        UPDATE users
        SET saldo = saldo - OLD.nominal + NEW.nominal
        WHERE nomor_identitas = NEW.user_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `hapus_saldo` AFTER DELETE ON `saldo` FOR EACH ROW BEGIN
    IF OLD.status = 1 THEN
        UPDATE users
        SET saldo = saldo - OLD.nominal
        WHERE nomor_identitas = OLD.user_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tambah_saldo` AFTER INSERT ON `saldo` FOR EACH ROW BEGIN
    IF NEW.status = 1 THEN
        UPDATE users
        SET saldo = saldo + NEW.nominal
        WHERE nomor_identitas = NEW.user_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tambah_transaksi` AFTER INSERT ON `saldo` FOR EACH ROW BEGIN
    IF NEW.status = 0 THEN
        UPDATE users
        SET saldo = saldo - NEW.nominal
        WHERE nomor_identitas = NEW.user_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` bigint(20) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `harga_akhir` varchar(50) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `status` int(11) NOT NULL,
  `status_keluar_masuk` int(11) NOT NULL,
  `detail_lokasi_id` bigint(20) NOT NULL,
  `kendaraan_id` bigint(20) NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_keluar` time DEFAULT NULL,
  `image_qr` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `user_id`, `harga_akhir`, `tanggal`, `status`, `status_keluar_masuk`, `detail_lokasi_id`, `kendaraan_id`, `jam_masuk`, `jam_keluar`, `image_qr`, `created_at`, `updated_at`) VALUES
(8, '123456765897654', '2000', '2023-10-15', 1, 1, 2, 3, '21:49:22', '21:56:58', 'tiket_652bfbf2a6c82.png', '2023-10-15 14:49:22', '2023-10-15 14:59:02'),
(10, '123456765897654', NULL, '2023-10-15', 0, 0, 3, 3, '21:51:48', NULL, 'tiket_652bfc8442113.png', '2023-10-15 14:51:48', '2023-10-15 14:51:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `nama_lengkap` varchar(30) NOT NULL,
  `no_telp` varchar(14) NOT NULL,
  `email` text NOT NULL,
  `password` varchar(150) NOT NULL,
  `nomor_identitas` varchar(20) NOT NULL,
  `foto_identitas` varchar(50) NOT NULL,
  `saldo` int(10) NOT NULL,
  `pin` varchar(6) NOT NULL,
  `qr_code` varchar(30) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`nama_lengkap`, `no_telp`, `email`, `password`, `nomor_identitas`, `foto_identitas`, `saldo`, `pin`, `qr_code`, `created_at`, `updated_at`) VALUES
('Rangga Santoso', '089765123456', 'rangga@gmail.com', '$2y$10$DQf1k6OdCvi14LH0cMjMM.leIGJ09aJXNpVYvVqifmDwlDoePXzGu', '123456765897654', 'R04Skoq1jJ.jpeg', 92000, '123456', 'user_652a84e53fcb1.png', '2023-10-14 05:09:14', '2023-10-14 05:17:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_lokasi`
--
ALTER TABLE `detail_lokasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_parkir` (`parkir_id`);

--
-- Indexes for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `parkir`
--
ALTER TABLE `parkir`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saldo`
--
ALTER TABLE `saldo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`user_id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tempat_parkir` (`detail_lokasi_id`),
  ADD KEY `id_user` (`kendaraan_id`),
  ADD KEY `kendaraan_id` (`kendaraan_id`),
  ADD KEY `tb_user_fk` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`nomor_identitas`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `saldo`
--
ALTER TABLE `saldo`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_lokasi`
--
ALTER TABLE `detail_lokasi`
  ADD CONSTRAINT `detail_lokasi_ibfk_1` FOREIGN KEY (`parkir_id`) REFERENCES `parkir` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD CONSTRAINT `kendaraan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`nomor_identitas`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `saldo`
--
ALTER TABLE `saldo`
  ADD CONSTRAINT `saldo_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`nomor_identitas`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `tb_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`nomor_identitas`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`detail_lokasi_id`) REFERENCES `detail_lokasi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
