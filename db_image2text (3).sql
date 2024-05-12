-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 12, 2024 at 04:56 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_image2text`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `email`, `fullname`, `created_at`) VALUES
(1, 'admin', '123', 'admin@image2text.com', 'admin', '2024-05-10 15:22:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `email`, `password`, `created_at`) VALUES
(1, 'kimpoi@gmail.com', '$2y$10$O6EJdb.Ryo7GVPBftBxAVehmJXUE97QLOkoumk34OSEYhYfyx8CV6', '2024-05-12 01:54:47'),
(2, 'annonymous@annonymous.com', '0', '2024-05-12 07:18:15');

-- --------------------------------------------------------

--
-- Table structure for table `user_response`
--

CREATE TABLE `user_response` (
  `id_ur` int NOT NULL,
  `id_user` int NOT NULL,
  `ip_address` varchar(55) NOT NULL,
  `file_name` varchar(55) NOT NULL,
  `extracted_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_response`
--

INSERT INTO `user_response` (`id_ur`, `id_user`, `ip_address`, `file_name`, `extracted_text`, `created_at`) VALUES
(45, 2, '::1', 'Screenshot 2024-05-11 171015.png', '11th Gen Intel{R} Core(T!\n\n1) i5-11260H @ 2.60GHz (12 CPUs), ~2.6GHz\n', '2024-05-12 07:19:49'),
(46, 1, '::1', 'Screenshot 2024-05-11 171015.png', '11th Gen Intel{R} Core(T!\n\n1) i5-11260H @ 2.60GHz (12 CPUs), ~2.6GHz\n', '2024-05-12 07:21:19'),
(47, 2, '::1', 'Screenshot 2024-05-07 120425.png', ')) 127.0.0.1:5000\n', '2024-05-12 07:34:25'),
(48, 2, '::1', 'Lembar Pengesahan.pdf', 'LEMBAR PENGESAHAN DUNIA USAHA / INSTANSI\nARTIFICIAL INTELLIGENCE CENTER INDONESIA.\n\nCe\n\ng Laboratorium Risel Vullicisiplin Pecaming FRIPA UILL 4.\nUniverss Incenesia, Kelurahan Pencek Cina. Kecamalar Bel, Kota Depok.\n\nProving) Jawa Bars: 18424\n\nGOnric!\n\nLaporan Prastis Kera Indusin in telah dipenksss, deli, dar ciselu ut pada\n\nTanggal Bulan... Tarun 2024\nOlen:\nPemoimb ng Per-sahaan, Peribimising Seko ah,\nRayfan Bagassetya Kurniawan, S.Pd. Cicih Sri Rahayu, M.Kom,\n\nNIP. Tss0a2520710\" 2002\n\nMengetahut\nOirekur Perusaraan,\n\nDr. Baiq Hana Susanti, M.Sc.\n\nLEMBAR PENGESAHAN SEKOLAH\n\nLaporan Prastis Keca Indusin (PRAKERINIn teah deer ks.\n\ndiset.yu) oad\n\nTanga... Bulan... Tahun 2024\n\neh\n\nPembimt\n\nng Laveran,\n\nSri Rahayu, M.kom.\nNip 198904232017\n\n‘aki Kepa a Sekola” Kepaa Jurusan Pengerbangan\nst Lena dan Gi,\n\ndang Kemilrazn Pern\n\nVengeishu\nKepaa SV Negen 1 Depok,\n\nLusi Triana, S.Pd., MM.\nNIP 19720706 996TZ200\n\n\nLEMBAR PENGESAHAN PENGUJI\n\nLaporen Praktk Ken Lagangan {PKL} Ini tla diaukar\n\nTanga’ 14 Bulan Mer Tahun 2024\n\nOlen\n\nNama Penguji\n\nNama Pembimbing', '2024-05-12 07:56:41'),
(49, 2, '::1', 'images.jpeg', '', '2024-05-12 15:57:58'),
(50, 2, '::1', 'Screenshot 2024-05-12 224230.png', 'Image to Text Converter\n\nDrag and drop file here, or click to\n', '2024-05-12 15:58:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `user_response`
--
ALTER TABLE `user_response`
  ADD PRIMARY KEY (`id_ur`),
  ADD KEY `id_user` (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_response`
--
ALTER TABLE `user_response`
  MODIFY `id_ur` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_response`
--
ALTER TABLE `user_response`
  ADD CONSTRAINT `user_response_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
